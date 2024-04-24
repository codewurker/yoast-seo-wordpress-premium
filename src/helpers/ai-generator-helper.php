<?php

namespace Yoast\WP\SEO\Premium\Helpers;

use RuntimeException;
use WP_Error;
use WP_User;
use WPSEO_Utils;
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

/**
 * Class AI_Generator_Helper
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
	 * @codeCoverageIgnore It only sets dependencies.
	 *
	 * @param Options_Helper $options     The options helper.
	 * @param User_Helper    $user_helper The User helper.
	 * @param Date_Helper    $date_helper The date helper.
	 */
	public function __construct( Options_Helper $options, User_Helper $user_helper, Date_Helper $date_helper ) {
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
	 * @param WP_User $user The WP user.
	 *
	 * @return string The code verifier.
	 */
	public function generate_code_verifier( WP_User $user ) {
		$random_string = \substr( \str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 1, 10 );

		return \hash( 'sha256', $user->user_email . $random_string );
	}

	/**
	 * Temporarily stores the code verifier. We expect the callback that consumes this verifier to reach us within a couple of seconds.
	 * So, we throw away the code after 5 minutes: when we know the callback isn't coming.
	 *
	 * @param int    $user_id       The user ID.
	 * @param string $code_verifier The code verifier.
	 *
	 * @return void
	 */
	public function set_code_verifier( int $user_id, string $code_verifier ): void {
		$this->user_helper->update_meta(
			$user_id,
			'yoast_wpseo_ai_generator_code_verifier_for_blog_' . \get_current_blog_id(),
			[
				'code'       => $code_verifier,
				'created_at' => $this->date_helper->current_time(),
			]
		);
	}

	/**
	 * Retrieves the code verifier.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The code verifier.
	 *
	 * @throws RuntimeException No valid code verifier could be found.
	 */
	public function get_code_verifier( int $user_id ): string {
		$code_verifier = $this->user_helper->get_meta( $user_id, 'yoast_wpseo_ai_generator_code_verifier_for_blog_' . \get_current_blog_id(), true );
		if ( ! \is_array( $code_verifier ) || ! isset( $code_verifier['code'] ) || $code_verifier['code'] === '' ) {
			throw new RuntimeException( 'Unable to retrieve the code verifier.' );
		}

		if ( ! isset( $code_verifier['created_at'] ) || $code_verifier['created_at'] < ( $this->date_helper->current_time() - self::CODE_VERIFIER_VALIDITY_IN_MINUTES * \MINUTE_IN_SECONDS ) ) {
			$this->delete_code_verifier( $user_id );
			throw new RuntimeException( 'Code verifier has expired.' );
		}

		return (string) $code_verifier['code'];
	}

	/**
	 * Deletes the code verifier.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function delete_code_verifier( int $user_id ): void {
		$this->user_helper->delete_meta( $user_id, 'yoast_wpseo_ai_generator_code_verifier_for_blog_' . \get_current_blog_id() );
	}

	/**
	 * Gets the licence URL.
	 *
	 * @return string The licence URL.
	 */
	public function get_license_url() {
		return WPSEO_Utils::get_home_url();
	}

	/**
	 * Gets the callback URL to be used by the API to send back the access token, refresh token and code challenge.
	 *
	 * @return string The callbacks URL.
	 */
	public function get_callback_url() {
		return \get_rest_url( null, 'yoast/v1/ai_generator/callback' );
	}

	/**
	 * Gets the callback URL to be used by the API to send back the refreshed JWTs once they expire.
	 *
	 * @return string The callbacks URL.
	 */
	public function get_refresh_callback_url() {
		return \get_rest_url( null, 'yoast/v1/ai_generator/refresh_callback' );
	}

	/**
	 * Performs the request using WordPress internals.
	 *
	 * @param string   $action_path     The path to the desired action.
	 * @param array    $request_body    The request body.
	 * @param string[] $request_headers The request headers.
	 *
	 * @return object The response object.
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
	 */
	public function request( $action_path, $request_body = [], $request_headers = [] ) {
		// Our API expects JSON.
		// The request times out after 30 seconds.
		$request_headers   = \array_merge( $request_headers, [ 'Content-Type' => 'application/json' ] );
		$request_arguments = [
			'timeout' => 30,
			// phpcs:ignore Yoast.Yoast.JsonEncodeAlternative.Found -- Reason: We don't want the debug/pretty possibility.
			'body'    => \wp_json_encode( $request_body ),
			'headers' => $request_headers,
		];

		/**
		 * Filter: 'Yoast\WP\SEO\ai_api_url' - Replaces the default URL for the AI API with a custom one.
		 *
		 * Note: This is a Premium plugin-only hook.
		 *
		 * @since 21.0
		 * @internal
		 *
		 * @param string $url The default URL for the AI API.
		 */
		$api_url  = \apply_filters( 'Yoast\WP\SEO\ai_api_url', $this->base_url );
		$response = \wp_remote_post( $api_url . $action_path, $request_arguments );

		if ( \is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			throw new Bad_Request_Exception( $response->get_error_message(), $response->get_error_code() );
		}

		[ $response_code, $response_message, $error_code, $missing_licenses ] = $this->parse_response( $response );

		// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
		switch ( $response_code ) {
			case 200:
				return (object) $response;
			case 401:
				throw new Unauthorized_Exception( $response_message, $response_code, $error_code );
			case 402:
				throw new Payment_Required_Exception( $response_message, $response_code, $error_code, null, $missing_licenses );
			case 403:
				throw new Forbidden_Exception( $response_message, $response_code, $error_code );
			case 404:
				throw new Not_Found_Exception( $response_message, $response_code, $error_code );
			case 408:
				throw new Request_Timeout_Exception( $response_message, $response_code, $error_code );
			case 429:
				throw new Too_Many_Requests_Exception( $response_message, $response_code, $error_code );
			case 500:
				throw new Internal_Server_Error_Exception( $response_message, $response_code, $error_code );
			case 503:
				throw new Service_Unavailable_Exception( $response_message, $response_code, $error_code );
			default:
				throw new Bad_Request_Exception( $response_message, $response_code, $error_code );
		}
		// phpcs:enable
	}

	/**
	 * Generates the list of 5 suggestions to return.
	 *
	 * @param object $response The response from the API.
	 *
	 * @return string[] The array of suggestions.
	 */
	public function build_suggestions_array( $response ): array {
		$suggestions = [];
		$json        = \json_decode( $response->body );
		if ( $json === null || ! isset( $json->choices ) ) {
			return $suggestions;
		}
		foreach ( $json->choices as $suggestion ) {
			$suggestions[] = $suggestion->text;
		}

		return $suggestions;
	}

	/**
	 * Parses the response from the API.
	 *
	 * @param array|WP_Error $response The response from the API.
	 *
	 * @return (string|int)[] The response code and message.
	 */
	public function parse_response( $response ) {
		$response_code    = ( \wp_remote_retrieve_response_code( $response ) !== '' ) ? \wp_remote_retrieve_response_code( $response ) : 0;
		$response_message = \esc_html( \wp_remote_retrieve_response_message( $response ) );
		$error_code       = '';
		$missing_licenses = [];

		if ( $response_code !== 200 && $response_code !== 0 ) {
			$json_body = \json_decode( \wp_remote_retrieve_body( $response ) );
			if ( $json_body !== null ) {
				$response_message = ( $json_body->message ?? $response_message );
				$error_code       = ( $json_body->error_code ?? $this->map_message_to_code( $json_body->message ) );
				if ( $response_code === 402 ) {
					$missing_licenses = isset( $json_body->missing_licenses ) ? (array) $json_body->missing_licenses : [];
				}
			}
		}

		return [ $response_code, $response_message, $error_code, $missing_licenses ];
	}

	/**
	 * Checks whether the token has expired.
	 *
	 * @param string $jwt The JWT.
	 *
	 * @return bool Whether the token has expired.
	 */
	public function has_token_expired( string $jwt ): bool {
		$parts = \explode( '.', $jwt );
		if ( \count( $parts ) !== 3 ) {
			// Headers, payload and signature parts are not detected.
			return true;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Reason: Decoding the payload of the JWT.
		$payload = \base64_decode( $parts[1] );
		$json    = \json_decode( $payload );
		if ( $json === null || ! isset( $json->exp ) ) {
			return true;
		}

		return $json->exp < \time();
	}

	/**
	 * Retrieves the access JWT.
	 *
	 * @param string $user_id The user ID.
	 *
	 * @return string The access JWT.
	 *
	 * @throws RuntimeException Unable to retrieve the access token.
	 */
	public function get_access_token( string $user_id ): string {
		$access_jwt = $this->user_helper->get_meta( $user_id, '_yoast_wpseo_ai_generator_access_jwt', true );
		if ( ! \is_string( $access_jwt ) || $access_jwt === '' ) {
			throw new RuntimeException( 'Unable to retrieve the access token.' );
		}

		return $access_jwt;
	}

	/**
	 * Retrieves the refresh JWT.
	 *
	 * @param string $user_id The user ID.
	 *
	 * @return string The access JWT.
	 *
	 * @throws RuntimeException Unable to retrieve the refresh token.
	 */
	public function get_refresh_token( $user_id ) {
		$refresh_jwt = $this->user_helper->get_meta( $user_id, '_yoast_wpseo_ai_generator_refresh_jwt', true );
		if ( ! \is_string( $refresh_jwt ) || $refresh_jwt === '' ) {
			throw new RuntimeException( 'Unable to retrieve the refresh token.' );
		}

		return $refresh_jwt;
	}

	/**
	 * Checks if the AI Generator feature is active.
	 *
	 * @return bool Whether the feature is active.
	 */
	public function is_ai_generator_enabled() {
		return $this->options_helper->get( 'enable_ai_generator', false );
	}

	/**
	 * Maps the message to a code.
	 *
	 * @param string $message The message.
	 *
	 * @return string The code.
	 */
	private function map_message_to_code( $message ) {
		if ( \strpos( $message, 'must NOT have fewer than 1 characters' ) !== false ) {
			return 'NOT_ENOUGH_CONTENT';
		}
		if ( \strpos( $message, 'Client timeout' ) !== false ) {
			return 'CLIENT_TIMEOUT';
		}
		if ( \strpos( $message, 'Server timeout' ) !== false ) {
			return 'SERVER_TIMEOUT';
		}

		return 'UNKNOWN';
	}
}
