<?php

namespace Yoast\WP\SEO\Premium\Actions;

use RuntimeException;
use WP_User;
use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Bad_Request_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Forbidden_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Internal_Server_Error_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Not_Found_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Payment_Required_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Request_Timeout_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Service_Unavailable_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Too_Many_Requests_Exception;
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\Unauthorized_Exception;
use Yoast\WP\SEO\Premium\Helpers\AI_Generator_Helper;

/**
 * Handles the actual requests to our API endpoints.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Base_Action {

	/**
	 * The AI_Generator helper.
	 *
	 * @var AI_Generator_Helper
	 */
	protected $ai_generator_helper;

	/**
	 * The Options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The User helper.
	 *
	 * @var User_Helper
	 */
	protected $user_helper;

	/**
	 * The add-on manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * AI_Generator_Action constructor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param AI_Generator_Helper $ai_generator_helper The AI_Generator helper.
	 * @param Options_Helper      $options_helper      The Options helper.
	 * @param User_Helper         $user_helper         The User helper.
	 * @param WPSEO_Addon_Manager $addon_manager       The add-on manager.
	 */
	public function __construct(
		AI_Generator_Helper $ai_generator_helper,
		Options_Helper $options_helper,
		User_Helper $user_helper,
		WPSEO_Addon_Manager $addon_manager
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6' );
		$this->ai_generator_helper = $ai_generator_helper;
		$this->options_helper      = $options_helper;
		$this->user_helper         = $user_helper;
		$this->addon_manager       = $addon_manager;
	}

	/**
	 * Requests a new set of JWT tokens.
	 *
	 * Requests a new JWT access and refresh token for a user from the Yoast AI Service and stores it in the database
	 * under usermeta. The storing of the token happens in a HTTP callback that is triggered by this request.
	 *
	 * @deprecated 25.6
	 *  @codeCoverageIgnore
	 *
	 * @param WP_User $user The WP user.
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
	 * @return void
	 */
	public function token_request( WP_User $user ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Token_Manager::token_request' );
	}

	/**
	 * Refreshes the JWT access token.
	 *
	 * Refreshes a stored JWT access token for a user with the Yoast AI Service and stores it in the database under
	 * usermeta. The storing of the token happens in a HTTP callback that is triggered by this request.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_User $user The WP user.
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
	 * @throws RuntimeException Unable to retrieve the refresh token.
	 * @return void
	 */
	public function token_refresh( WP_User $user ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Token_Manager::token_refresh' );
	}

	/**
	 * Callback function that will be invoked by our API.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $access_jwt     The access JWT.
	 * @param string $refresh_jwt    The refresh JWT.
	 * @param string $code_challenge The verification code.
	 * @param int    $user_id        The user ID.
	 *
	 * @throws Unauthorized_Exception Unauthorized_Exception.
	 * @return string The code verifier.
	 */
	public function callback(
		string $access_jwt,
		string $refresh_jwt,
		string $code_challenge,
		int $user_id
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\User_Interface\Callback_Route::callback' );
	}

	/**
	 * Stores the consent given or revoked by the user.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param int  $user_id The user ID.
	 * @param bool $consent Whether the consent has been given.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws RuntimeException Unable to retrieve the access token.
	 * @return void
	 */
	public function consent( int $user_id, bool $consent ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6' );
	}

	/**
	 * Busts the subscription cache.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function bust_subscription_cache() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\User_Interface\Bust_Subscription_Cache_Route::bust_subscription_cache' );
	}

	/**
	 * Retrieves the access token.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_User $user The WP user.
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
	 * @throws RuntimeException Unable to retrieve the access or refresh token.
	 * @return string The access token.
	 */
	protected function get_or_request_access_token( WP_User $user ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Token_Manager::get_or_request_access_token' );
	}

	/**
	 * Invalidates the access token.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $user_id The user ID.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws RuntimeException Unable to retrieve the access token.
	 * @return void
	 */
	private function token_invalidate( string $user_id ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Token_Manager::token_invalidate' );
	}

	/**
	 * Action used to retrieve how much usage of the AI API has the current user had so far this month.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_User $user The WP user.
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
	 * @return object<string, object<string>> The AI-generated content.
	 */
	public function get_usage(
		WP_User $user
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\User_Interface\Get_Usage_Route::get_usage' );
	}

	/**
	 * Handles consent revoked.
	 *
	 * By deleting the consent user metadata from the database.
	 * And then throwing a Forbidden_Exception.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param int $user_id     The user ID.
	 * @param int $status_code The status code. Defaults to 403.
	 *
	 * @return Forbidden_Exception The Forbidden_Exception.
	 */
	protected function handle_consent_revoked( int $user_id, int $status_code = 403 ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6' );
	}
}
