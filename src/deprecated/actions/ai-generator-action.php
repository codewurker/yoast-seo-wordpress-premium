<?php

namespace Yoast\WP\SEO\Premium\Actions;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Bad_Request_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Forbidden_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Internal_Server_Error_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Not_Found_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Payment_Required_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Request_Timeout_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Service_Unavailable_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Too_Many_Requests_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Unauthorized_Exception;

/**
 * Handles the actual requests to our API endpoints.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Generator_Action extends AI_Base_Action {

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Action used to generate suggestions through AI.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_User $user                  The WP user.
	 * @param string  $suggestion_type       The type of the requested suggestion.
	 * @param string  $prompt_content        The excerpt taken from the post.
	 * @param string  $focus_keyphrase       The focus keyphrase associated to the post.
	 * @param string  $language              The language of the post.
	 * @param string  $platform              The platform the post is intended for.
	 * @param string  $editor                The current editor.
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
	 * @return string[] The suggestions.
	 */
	public function get_suggestions(
		WP_User $user,
		string $suggestion_type,
		string $prompt_content,
		string $focus_keyphrase,
		string $language,
		string $platform,
		string $editor,
		bool $retry_on_unauthorized = true
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\User_Interface\Get_Suggestions_Route::get_suggestions' );
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
}
