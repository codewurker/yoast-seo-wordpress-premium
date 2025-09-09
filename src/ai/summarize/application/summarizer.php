<?php
//phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Summarize\Application;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\AI_Authorization\Application\Token_Manager;
use Yoast\WP\SEO\AI_Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI_Generator\Domain\Suggestion;
use Yoast\WP\SEO\AI_Generator\Domain\Suggestions_Bucket;
use Yoast\WP\SEO\AI_HTTP_Request\Application\Request_Handler;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Bad_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Forbidden_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Internal_Server_Error_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Not_Found_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Request_Timeout_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Service_Unavailable_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Unauthorized_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Request;
use Yoast\WP\SEO\Helpers\User_Helper;

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
	 * The request handler.
	 *
	 * @var Request_Handler
	 */
	private $request_handler;

	/**
	 * The AI token manager.
	 *
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Summarizer constructor.
	 *
	 * @param Consent_Handler $consent_handler The consent handler.
	 * @param Request_Handler $request_handler The request handler.
	 * @param Token_Manager   $token_manager   The token manager.
	 * @param User_Helper     $user_helper     The user helper.
	 */
	public function __construct(
		Consent_Handler $consent_handler,
		Request_Handler $request_handler,
		Token_Manager $token_manager,
		User_Helper $user_helper
	) {
		$this->consent_handler = $consent_handler;
		$this->request_handler = $request_handler;
		$this->token_manager   = $token_manager;
		$this->user_helper     = $user_helper;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Action used to generate a summary through AI.
	 *
	 * @param WP_User $user                  The WP user.
	 * @param string  $language              The language of the post.
	 * @param string  $prompt_content        The excerpt taken from the post.
	 * @param string  $focus_keyphrase       The focus keyphrase associated to the post.
	 * @param bool    $retry_on_unauthorized Whether to retry when unauthorized (mechanism to retry once).
	 *
	 * @return string[] The summary list.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws Unauthorized_Exception Unauthorized_Exception.
	 * @throws RuntimeException Unable to retrieve the access token.
	 */
	public function summarize(
		WP_User $user,
		string $language,
		string $prompt_content,
		string $focus_keyphrase,
		bool $retry_on_unauthorized = true
	): array {
		$token = $this->token_manager->get_or_request_access_token( $user );

		$subject = [
			'language' => $language,
			'content'  => $prompt_content,
		];
		// We are not sending the synonyms for now, as these are not used in the current prompts.
		if ( $focus_keyphrase !== '' ) {
			$subject['focus_keyphrase'] = $focus_keyphrase;
		}

		$request_body    = [
			'service' => 'openai',
			'user_id' => (string) $user->ID,
			'subject' => $subject,
		];
		$request_headers = [
			'Authorization' => "Bearer $token",
		];

		try {
			$response = $this->request_handler->handle( new Request( '/openai/summary', $request_body, $request_headers ) );
		} catch ( Unauthorized_Exception $exception ) {
			// Delete the stored JWT tokens, as they appear to be no longer valid.
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_access_jwt' );
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_refresh_jwt' );

			if ( ! $retry_on_unauthorized ) {
				throw $exception;
			}

			// Try again once more by fetching a new set of tokens and trying the endpoint again.
			return $this->summarize( $user, $language, $prompt_content, $focus_keyphrase, false );
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
