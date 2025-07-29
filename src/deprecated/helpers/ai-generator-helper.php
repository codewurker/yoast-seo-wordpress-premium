<?php

namespace Yoast\WP\SEO\Premium\Helpers;

use RuntimeException;
use WP_Error;
use WP_User;
use Yoast\WP\SEO\Helpers\Date_Helper;
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
use Yoast\WP\SEO\Premium\Exceptions\Remote_Request\WP_Request_Exception;

/**
 * Class AI_Generator_Helper
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 *
 * @package Yoast\WP\SEO\Helpers
 */
class AI_Generator_Helper {

	private const CODE_VERIFIER_VALIDITY_IN_MINUTES = 5;

	/**
	 * The API base URL.
	 *
	 * @var string
	 */
	protected $base_url = 'https://ai.yoa.st/api/v1';

	/**
	 * The options helper.
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
	 * The date helper.
	 *
	 * @var Date_Helper
	 */
	private $date_helper;

	/**
	 * AI_Generator_Helper constructor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param Options_Helper $options     The options helper.
	 * @param User_Helper    $user_helper The User helper.
	 * @param Date_Helper    $date_helper The date helper.
	 */
	public function __construct( Options_Helper $options, User_Helper $user_helper, Date_Helper $date_helper ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6' );
		$this->options_helper = $options;
		$this->user_helper    = $user_helper;
		$this->date_helper    = $date_helper;
	}

	/**
	 * Generates a random code verifier for a user. The code verifier is used in communication with the Yoast AI API
	 * to ensure that the callback that is sent for both the token and refresh request are handled by the same site that requested the tokens.
	 * Each code verifier should only be used once.
	 * This all helps with preventing access tokens from one site to be sent to another and it makes a mitm attack more difficult to execute.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_User $user The WP user.
	 *
	 * @return string The code verifier.
	 */
	public function generate_code_verifier( WP_User $user ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Code_Verifier_Interface::generate' );
	}

	/**
	 * Temporarily stores the code verifier. We expect the callback that consumes this verifier to reach us within a couple of seconds.
	 * So, we throw away the code after 5 minutes: when we know the callback isn't coming.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param int    $user_id       The user ID.
	 * @param string $code_verifier The code verifier.
	 *
	 * @return void
	 */
	public function set_code_verifier( int $user_id, string $code_verifier ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository::store_code_verifier' );
	}

	/**
	 * Retrieves the code verifier.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param int $user_id The user ID.
	 *
	 * @throws RuntimeException No valid code verifier could be found.
	 * @return string The code verifier.
	 */
	public function get_code_verifier( int $user_id ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository::get_code_verifier' );
	}

	/**
	 * Deletes the code verifier.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function delete_code_verifier( int $user_id ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository::delete_code_verifier' );
	}

	/**
	 * Gets the licence URL.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return string The licence URL.
	 */
	public function get_license_url() {
		\_deprecated_function( __METHOD__, 'Yoast\WP\SEO\AI_Generator\Infrastructure\WordPress_URLs::get_license_url' );
	}

	/**
	 * Gets the timeout of the suggestion requests in seconds.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return int The timeout of the suggestion requests in seconds.
	 */
	public function get_request_timeout() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_HTTP_Request\Infrastructure\API_Client::get_request_timeout' );
	}

	/**
	 * Gets the callback URL to be used by the API to send back the access token, refresh token and code challenge.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return string The callbacks URL.
	 */
	public function get_callback_url() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\Infrastructure\WordPress_URLs::get_callback_url' );
	}

	/**
	 * Gets the callback URL to be used by the API to send back the refreshed JWTs once they expire.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return string The callbacks URL.
	 */
	public function get_refresh_callback_url() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\Infrastructure\WordPress_URLs::get_refresh_callback_url' );
	}

	/**
	 * Performs the request using WordPress internals.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string        $action_path     The path to the desired action.
	 * @param array<string> $request_body    The request body.
	 * @param array<string> $request_headers The request headers.
	 * @param bool          $is_post         Whether it's a POST request.
	 *
	 * @throws Bad_Request_Exception When the request fails for any other reason.
	 * @throws Forbidden_Exception When the response code is 403.
	 * @throws Internal_Server_Error_Exception When the response code is 500.
	 * @throws Not_Found_Exception When the response code is 404.
	 * @throws Payment_Required_Exception When the response code is 402.
	 * @throws Request_Timeout_Exception When the response code is 408.
	 * @throws Service_Unavailable_Exception When the response code is 503.
	 * @throws Too_Many_Requests_Exception When the response code is 429.
	 * @throws Unauthorized_Exception When the response code is 401.
	 * @throws WP_Request_Exception When the wp_remote_post() returns an error.
	 * @return object The response object.
	 */
	public function request( $action_path, $request_body = [], $request_headers = [], $is_post = true ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_HTTP_Request\Application\Request_Handler::request' );
	}

	/**
	 * Generates the list of 5 suggestions to return.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param object $response The response from the API.
	 *
	 * @return string[] The array of suggestions.
	 */
	public function build_suggestions_array( $response ): array {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Generator\Application\Suggestions_Provider::build_suggestions_array' );
	}

	/**
	 * Parses the response from the API.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param array<string>|WP_Error $response The response from the API.
	 *
	 * @return (string|int)[] The response code and message.
	 */
	public function parse_response( $response ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_HTTP_Request\Application\Response_Parser::parse' );
	}

	/**
	 * Checks whether the token has expired.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $jwt The JWT.
	 *
	 * @return bool Whether the token has expired.
	 */
	public function has_token_expired( string $jwt ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Application\Token_Manager::has_token_expired' );
	}

	/**
	 * Retrieves the access JWT.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $user_id The user ID.
	 *
	 * @throws RuntimeException Unable to retrieve the access token.
	 * @return string The access JWT.
	 */
	public function get_access_token( string $user_id ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Infrastructure\Access_Token_User_Meta_Repository::get_token' );
	}

	/**
	 * Retrieves the refresh JWT.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $user_id The user ID.
	 *
	 * @throws RuntimeException Unable to retrieve the refresh token.
	 * @return string The access JWT.
	 */
	public function get_refresh_token( $user_id ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\AI_Authorization\Infrastructure\Refresh_Token_User_Meta_Repository::get_token' );
	}

	/**
	 * Checks if the AI Generator feature is active.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return bool Whether the feature is active.
	 */
	public function is_ai_generator_enabled() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Conditionals\AI_Conditional::is_met' );
	}
}
