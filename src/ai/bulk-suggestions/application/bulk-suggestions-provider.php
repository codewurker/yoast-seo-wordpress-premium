<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Application;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\AI\Authentication\Application\AI_Request_Sender_Factory;
use Yoast\WP\SEO\AI\Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Bad_Request_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Forbidden_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Insufficient_Scope_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Internal_Server_Error_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Not_Found_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Request_Timeout_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Service_Unavailable_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Request;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject_Build_Exception;

/**
 * Fetches AI suggestions for multiple posts in a single request to the AI service.
 */
class Bulk_Suggestions_Provider {

	/**
	 * The default platform for subjects that do not specify one.
	 */
	private const DEFAULT_PLATFORM = 'Google';

	/**
	 * The consent handler.
	 *
	 * @var Consent_Handler
	 */
	private $consent_handler;

	/**
	 * The AI request sender factory.
	 *
	 * @var AI_Request_Sender_Factory
	 */
	private $ai_request_sender_factory;

	/**
	 * The subject builder.
	 *
	 * @var Subject_Builder_Interface
	 */
	private $subject_builder;

	/**
	 * Bulk_Suggestions_Provider constructor.
	 *
	 * @param Consent_Handler           $consent_handler           The consent handler.
	 * @param AI_Request_Sender_Factory $ai_request_sender_factory The AI request sender factory.
	 * @param Subject_Builder_Interface $subject_builder           The subject builder.
	 */
	public function __construct(
		Consent_Handler $consent_handler,
		AI_Request_Sender_Factory $ai_request_sender_factory,
		Subject_Builder_Interface $subject_builder
	) {
		$this->consent_handler           = $consent_handler;
		$this->ai_request_sender_factory = $ai_request_sender_factory;
		$this->subject_builder           = $subject_builder;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Fetches bulk suggestions for the given subjects.
	 *
	 * Subjects that fail locally (unknown or trashed post, no edit capability, missing focus keyphrase) are
	 * excluded from the AI request and returned as per-item error results; the remaining subjects are sent to
	 * the AI service in a single request.
	 *
	 * @param WP_User                          $user          The WP user.
	 * @param array<array<string, int|string>> $subject_specs The requested subjects: per item a post `id`,
	 *                                                        a suggestion `type` and an optional `platform`.
	 *
	 * @return array<string, array|null> The response envelope: `summary`, `results` and `usage`.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Insufficient_Scope_Exception Insufficient_Scope_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws RuntimeException Unable to retrieve the access token or the AI service response is malformed.
	 */
	public function get_bulk_suggestions( WP_User $user, array $subject_specs ): array {
		$subjects     = [];
		$local_errors = [];

		foreach ( $subject_specs as $index => $spec ) {
			try {
				$subjects[ $index ] = $this->subject_builder->build(
					$user,
					(int) $spec['id'],
					(string) $spec['type'],
					(string) ( $spec['platform'] ?? self::DEFAULT_PLATFORM ),
				);
			} catch ( Subject_Build_Exception $exception ) {
				$local_errors[ $index ] = [
					'id'         => (string) $spec['id'],
					'index'      => $index,
					'status'     => 'error',
					'error_code' => $exception->get_error_code(),
					'message'    => $exception->getMessage(),
				];
			}
		}

		if ( $subjects === [] ) {
			return [
				'summary' => [
					'total'     => \count( $local_errors ),
					'succeeded' => 0,
					'failed'    => \count( $local_errors ),
				],
				'results' => \array_values( $local_errors ),
				'usage'   => null,
			];
		}

		$envelope = $this->request_bulk_suggestions( $user, $subjects );

		return $this->merge_results( $envelope, $subjects, $local_errors );
	}

	/**
	 * Sends the bulk suggestions request to the AI service.
	 *
	 * @param WP_User        $user     The WP user.
	 * @param array<Subject> $subjects The subjects to send.
	 *
	 * @return array<string, array<int|string, int|string|array<int|string, int|string|array<int|string, int|string>>>> The decoded response envelope.
	 *
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Insufficient_Scope_Exception Insufficient_Scope_Exception.
	 * @throws RuntimeException Unable to retrieve the access token or the AI service response is malformed.
	 */
	private function request_bulk_suggestions( WP_User $user, array $subjects ): array {
		$request_body = [ 'subjects' => [] ];
		foreach ( $subjects as $subject ) {
			$request_body['subjects'][] = $subject->to_array();
		}

		try {
			$sender   = $this->ai_request_sender_factory->create( $user );
			$response = $sender->send( new Request( '/openai/suggestions/bulk', $request_body ), $user );
		} catch ( Insufficient_Scope_Exception $exception ) {
			throw $exception;
		} catch ( Forbidden_Exception $exception ) {
			// Follow the API in the consent being revoked (Use case: user sent an e-mail to revoke?).
			$this->consent_handler->revoke_consent( $user->ID );
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			throw new Forbidden_Exception( 'CONSENT_REVOKED', $exception->getCode() );
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$envelope = \json_decode( $response->get_body(), true );
		if ( ! \is_array( $envelope ) || ! isset( $envelope['results'] ) || ! \is_array( $envelope['results'] ) ) {
			throw new RuntimeException( 'Malformed response from the AI service' );
		}
		if ( \count( $envelope['results'] ) !== \count( $subjects ) ) {
			throw new RuntimeException( 'The AI service did not return a result for every subject' );
		}

		return $envelope;
	}

	/**
	 * Merges the locally rejected subjects into the AI service results, in the original request order.
	 *
	 * The remote results are mapped back by their position in the sent subjects array, which is robust against
	 * duplicate post IDs in the request. Each result's `index` is rewritten to the original request index and
	 * the summary is recomputed to also cover the locally rejected subjects.
	 *
	 * @param array<string, array<int|string, int|string|array<int|string, int|string|array<int|string, int|string>>>> $envelope     The decoded response envelope of the AI service.
	 * @param array<int, Subject>                                                                                      $subjects     The sent subjects, keyed by original request index.
	 * @param array<int, array<string, int|string>>                                                                    $local_errors The local error results, keyed by original request index.
	 *
	 * @return array<string, array|null> The merged response envelope.
	 *
	 * @throws RuntimeException When a result entry of the AI service is not an array.
	 */
	private function merge_results( array $envelope, array $subjects, array $local_errors ): array {
		$remote_results  = \array_values( $envelope['results'] );
		$subject_indices = \array_keys( $subjects );

		// The local errors are already keyed by their original request index; slot the remote results back in
		// by the original index of the subject that was sent at the same position.
		$results = $local_errors;
		foreach ( $remote_results as $position => $result ) {
			if ( ! \is_array( $result ) ) {
				throw new RuntimeException( 'Malformed result entry from the AI service' );
			}
			$index             = $subject_indices[ $position ];
			$result['index']   = $index;
			$results[ $index ] = $result;
		}

		\ksort( $results );
		$results = \array_values( $results );

		// Locally rejected subjects carry a non-'success' status too, so a single pass covers both error sources.
		$failed = 0;
		foreach ( $results as $result ) {
			if ( ( $result['status'] ?? '' ) !== 'success' ) {
				++$failed;
			}
		}

		$total = \count( $results );

		return [
			'summary' => [
				'total'     => $total,
				'succeeded' => ( $total - $failed ),
				'failed'    => $failed,
			],
			'results' => $results,
			'usage'   => ( $envelope['usage'] ?? null ),
		];
	}
}
