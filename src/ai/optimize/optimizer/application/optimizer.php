<?php
//phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\AI_Authorization\Application\Token_Manager;
use Yoast\WP\SEO\AI_Consent\Application\Consent_Handler;
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
use Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Domain\Suggestion;
use Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Suggestion_Processor;
use Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Suggestions_Unifier;

/**
 * Class used to optimize content using AI.
 */
class Optimizer {

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
	 * The AI suggestion helper.
	 *
	 * @var Suggestions_Unifier
	 */
	private $ai_suggestions_unifier;

	/**
	 * The suggestion processor.
	 *
	 * @var Suggestion_Processor
	 */
	private $suggestion_processor;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * AI_Optimizer_Helper constructor.
	 *
	 * @param Suggestions_Unifier  $ai_suggestions_unifier The AI suggestion unifier.
	 * @param Consent_Handler      $consent_handler        The consent handler.
	 * @param Request_Handler      $request_handler        The request handler.
	 * @param Suggestion_Processor $suggestion_processor   The suggestion processor.
	 * @param Token_Manager        $token_manager          The token manager.
	 * @param User_Helper          $user_helper            The user helper.
	 */
	public function __construct(
		Suggestions_Unifier $ai_suggestions_unifier,
		Consent_Handler $consent_handler,
		Request_Handler $request_handler,
		Suggestion_Processor $suggestion_processor,
		Token_Manager $token_manager,
		User_Helper $user_helper
	) {
		$this->ai_suggestions_unifier = $ai_suggestions_unifier;
		$this->consent_handler        = $consent_handler;
		$this->request_handler        = $request_handler;
		$this->suggestion_processor   = $suggestion_processor;
		$this->token_manager          = $token_manager;
		$this->user_helper            = $user_helper;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Action used to generate improved copy through AI, that scores better on our content analysis' assessments.
	 *
	 * @param WP_User $user                  The WP user.
	 * @param string  $assessment            The assessment to improve.
	 * @param string  $language              The language of the post.
	 * @param string  $prompt_content        The excerpt taken from the post.
	 * @param string  $focus_keyphrase       The focus keyphrase associated to the post.
	 * @param string  $synonyms              Synonyms for the focus keyphrase.
	 * @param string  $editor                WordPress editor type, either Gutenberg or Classic.
	 * @param bool    $retry_on_unauthorized Whether to retry when unauthorized (mechanism to retry once).
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
	 *
	 * @return string The AI-generated content.
	 */
	public function optimize(
		WP_User $user,
		string $assessment,
		string $language,
		string $prompt_content,
		string $focus_keyphrase,
		string $synonyms,
		string $editor,
		bool $retry_on_unauthorized = true
	): string {
		$token = $this->token_manager->get_or_request_access_token( $user );

		$subject = [
			'language'        => $language,
			'content'         => $prompt_content,
			'editor'          => $editor,
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
			$response = $this->request_handler->handle( new Request( "/fix/assessments/$assessment", $request_body, $request_headers ) );
		} catch ( Unauthorized_Exception $exception ) {
			// Delete the stored JWT tokens, as they appear to be no longer valid.
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_access_jwt' );
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_refresh_jwt' );

			if ( ! $retry_on_unauthorized ) {
				throw $exception;
			}

			// Try again once more by fetching a new set of tokens and trying the endpoint again.
			return $this->optimize( $user, $assessment, $language, $prompt_content, $focus_keyphrase, $synonyms, $editor, false );
		} catch ( Forbidden_Exception $exception ) {
			// Follow the API in the consent being revoked (Use case: user sent an e-mail to revoke?).
			$this->consent_handler->revoke_consent( $user->ID );
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			throw new Forbidden_Exception( 'CONSENT_REVOKED', $exception->getCode() );
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $this->build_optimize_response( $assessment, $prompt_content, $response );
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

	/**
	 * Builds a response for the AI Optimize route by comparing the response to the input.
	 * We output the diff as an HTML string and will parse this string on the JavaScript side.
	 * The differences are marked with `<ins>` and `<del>` tags.
	 *
	 * @param string $assessment The assessment to improve.
	 * @param string $original   The original text.
	 * @param object $response   The response from the API.
	 *
	 * @return string The HTML containing the suggested content.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 */
	public function build_optimize_response( string $assessment, string $original, object $response ): string {
		$raw_fixes = $this->suggestion_processor->get_suggestion_from_ai_response( $response->get_body() );
		if ( $raw_fixes === '' ) {
			return '';
		}

		$diff = $this->suggestion_processor->calculate_diff( $original, $raw_fixes );

		// For the paragraph length assessment, we need to replace any introduced paragraph breaks with a special class.
		if ( $assessment === 'read-paragraph-length' ) {
			$diff = $this->suggestion_processor->mark_new_paragraphs_in_suggestions( $diff );
		}

		$diff = $this->suggestion_processor->remove_html_from_suggestion( $diff );

		$diff = $this->suggestion_processor->keep_nbsp_in_suggestions( $diff );

		// If we end up with no suggestions, we have to show an error to the user.
		if ( \preg_match( '/<(ins|del) class="yst-/', $diff ) === false ) {
			throw new Bad_Request_Exception();
		}
		$suggestion = new Suggestion();
		$suggestion->set_content( $diff );
		return $this->ai_suggestions_unifier->unify_diffs( $suggestion );
	}
}
