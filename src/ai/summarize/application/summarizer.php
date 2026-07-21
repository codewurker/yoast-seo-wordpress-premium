<?php
//phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Summarize\Application;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\AI\Authentication\Application\AI_Request_Sender_Factory;
use Yoast\WP\SEO\AI\Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI\Generator\Domain\Suggestion;
use Yoast\WP\SEO\AI\Generator\Domain\Suggestions_Bucket;
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

/**
 * Class used to summarize content using AI.
 */
class Summarizer {

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
	 * Summarizer constructor.
	 *
	 * @param Consent_Handler           $consent_handler           The consent handler.
	 * @param AI_Request_Sender_Factory $ai_request_sender_factory The AI request sender factory.
	 */
	public function __construct(
		Consent_Handler $consent_handler,
		AI_Request_Sender_Factory $ai_request_sender_factory
	) {
		$this->consent_handler           = $consent_handler;
		$this->ai_request_sender_factory = $ai_request_sender_factory;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Action used to generate a summary through AI.
	 *
	 * @param WP_User $user            The WP user.
	 * @param string  $language        The language of the post.
	 * @param string  $prompt_content  The excerpt taken from the post.
	 * @param string  $focus_keyphrase The focus keyphrase associated to the post.
	 *
	 * @return string[] The summary list.
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
	 * @throws RuntimeException Unable to retrieve the access token.
	 */
	public function summarize(
		WP_User $user,
		string $language,
		string $prompt_content,
		string $focus_keyphrase
	): array {
		$subject = [
			'language' => $language,
			'content'  => $prompt_content,
		];
		// We are not sending the synonyms for now, as these are not used in the current prompts.
		if ( $focus_keyphrase !== '' ) {
			$subject['focus_keyphrase'] = $focus_keyphrase;
		}

		$request_body = [
			'service' => 'openai',
			'subject' => $subject,
		];

		try {
			$sender   = $this->ai_request_sender_factory->create( $user );
			$response = $sender->send( new Request( '/openai/summary', $request_body ), $user );
		} catch ( Insufficient_Scope_Exception $exception ) {
			throw $exception;
		} catch ( Forbidden_Exception $exception ) {
			// Follow the API in the consent being revoked (Use case: user sent an e-mail to revoke?).
			$this->consent_handler->revoke_consent( $user->ID );
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			throw new Forbidden_Exception( 'CONSENT_REVOKED', $exception->getCode() );
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $this->build_summarize_response( $response )->to_array();
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

	/**
	 * Generates the summary to return.
	 *
	 * @param object $response The response from the API.
	 *
	 * @return Suggestions_Bucket The array of suggestions.
	 */
	public function build_summarize_response( object $response ): Suggestions_Bucket {
		$suggestions_bucket = new Suggestions_Bucket();
		$json               = \json_decode( $response->get_body() );
		if ( $json === null || ! isset( $json->choices ) ) {
			return $suggestions_bucket;
		}
		foreach ( $json->choices as $suggestion ) {
			$suggestions_bucket->add_suggestion( new Suggestion( $suggestion->text ) );
		}

		return $suggestions_bucket;
	}
}
