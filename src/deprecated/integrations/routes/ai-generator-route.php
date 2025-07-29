<?php

namespace Yoast\WP\SEO\Premium\Integrations\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Premium\Actions\AI_Generator_Action;
use Yoast\WP\SEO\Premium\Helpers\AI_Generator_Helper;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers the route for the AI_Generator integration.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Generator_Route implements Route_Interface {

	/**
	 * The AI_Generator route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'ai_generator';

	/**
	 * The callback route constant (invoked by the API).
	 *
	 * @var string
	 */
	public const CALLBACK_ROUTE = self::ROUTE_PREFIX . '/callback';

	/**
	 * The refresh callback route constant (invoked by the API).
	 *
	 * @var string
	 */
	public const REFRESH_CALLBACK_ROUTE = self::ROUTE_PREFIX . '/refresh_callback';

	/**
	 * The get_suggestions route constant.
	 *
	 * @var string
	 */
	public const GET_SUGGESTIONS_ROUTE = self::ROUTE_PREFIX . '/get_suggestions';

	/**
	 * The get_usage route constant.
	 *
	 * @var string
	 */
	public const GET_USAGE_ROUTE = self::ROUTE_PREFIX . '/get_usage';

	/**
	 * The consent route constant.
	 *
	 * @var string
	 */
	public const CONSENT_ROUTE = self::ROUTE_PREFIX . '/consent';

	/**
	 * The bust_subscription_cache route constant.
	 *
	 * @var string
	 */
	public const BUST_SUBSCRIPTION_CACHE_ROUTE = self::ROUTE_PREFIX . '/bust_subscription_cache';

	/**
	 * Instance of the AI_Generator_Action.
	 *
	 * @var AI_Generator_Action
	 */
	protected $ai_generator_action;

	/**
	 * Instance of the AI_Generator_Helper.
	 *
	 * @var AI_Generator_Helper
	 */
	protected $ai_generator_helper;

	/**
	 * Returns an empty array, meaning no conditionals are required to load whatever uses this trait.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return array<string> The conditionals that must be met to load this.
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		return [];
	}

	/**
	 * AI_Generator_Route constructor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param AI_Generator_Action $ai_generator_action The action to handle the requests to the endpoint.
	 * @param AI_Generator_Helper $ai_generator_helper The AI_Generator helper.
	 */
	public function __construct( AI_Generator_Action $ai_generator_action, AI_Generator_Helper $ai_generator_helper ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		$this->ai_generator_action = $ai_generator_action;
		$this->ai_generator_helper = $ai_generator_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_routes() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Runs the callback to store connection credentials and the tokens locally.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function callback( WP_REST_Request $request ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Runs the callback to get AI-generated suggestions.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the get_suggestions action.
	 */
	public function get_suggestions( WP_REST_Request $request ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Runs the callback to store the consent given by the user to use AI-based services.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function consent( WP_REST_Request $request ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Runs the callback that gets the monthly usage of the user.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function get_usage() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Runs the callback that busts the subscription cache.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function bust_subscription_cache() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Checks:
	 * - if the user is logged
	 * - if the user can edit posts
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return bool Whether the user is logged in, can edit posts and the feature is active.
	 */
	public function check_permissions() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}
}
