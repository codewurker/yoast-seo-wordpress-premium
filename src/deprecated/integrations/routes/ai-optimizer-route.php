<?php

namespace Yoast\WP\SEO\Premium\Integrations\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Premium\Actions\AI_Optimizer_Action;
use Yoast\WP\SEO\Premium\Helpers\AI_Generator_Helper;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers the route for the AI_Optimizer integration.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Optimizer_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * The AI_Optimizer route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'ai';

	/**
	 * The optimize route constant.
	 *
	 * @var string
	 */
	public const AI_OPTIMIZE_ROUTE = self::ROUTE_PREFIX . '/optimize';

	/**
	 * Instance of the AI_Optimizer_Action.
	 *
	 * @var AI_Optimizer_Action
	 */
	protected $ai_optimizer_action;

	/**
	 * Instance of the AI_Generator_Helper.
	 *
	 * @var AI_Generator_Helper
	 */
	protected $ai_generator_helper;

	/**
	 * AI_Generator_Route constructor.
	 *
	 * @param AI_Optimizer_Action $ai_optimizer_action The action to handle the requests to the endpoint.
	 * @param AI_Generator_Helper $ai_generator_helper The AI_Generator helper.
	 */
	public function __construct( AI_Optimizer_Action $ai_optimizer_action, AI_Generator_Helper $ai_generator_helper ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Route::construct' );
		$this->ai_optimizer_action = $ai_optimizer_action;
		$this->ai_generator_helper = $ai_generator_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Route::register_routes' );
	}

	/**
	 * Runs the callback to improve assessment results through AI.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the assess action.
	 */
	public function optimize( WP_REST_Request $request ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Optimizer::optimize' );
	}

	/**
	 * Checks:
	 * - if the user is logged
	 * - if the user can edit posts
	 *
	 * @return bool Whether the user is logged in and can edit posts.
	 */
	public function check_permissions() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Route::check_permissions' );
	}
}
