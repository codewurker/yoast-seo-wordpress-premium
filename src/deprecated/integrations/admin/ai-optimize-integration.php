<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\AI_HTTP_Request\Infrastructure\API_Client;
use Yoast\WP\SEO\Conditionals\Admin\Post_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;

/**
 * Ai_Optimize_Integration class.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Ai_Optimize_Integration implements Integration_Interface {

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Represents the add-on manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Holds the API client instance.
	 *
	 * @var API_Client
	 */
	private $api_client;

	/**
	 * Represents the options manager.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Represents the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Represents the introductions seen repository.
	 *
	 * @var Introductions_Seen_Repository
	 */
	private $introductions_seen_repository;

	/**
	 * Represents the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper $current_page_helper The Current_Page_Helper.
	 */
	private $current_page_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::register_routes' );
		return [ Post_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WPSEO_Admin_Asset_Manager     $asset_manager                 The admin asset manager.
	 * @param WPSEO_Addon_Manager           $addon_manager                 The addon manager.
	 * @param API_Client                    $api_client                    The API client.
	 * @param Options_Helper                $options_helper                The options helper.
	 * @param User_Helper                   $user_helper                   The user helper.
	 * @param Introductions_Seen_Repository $introductions_seen_repository The introductions seen repository.
	 * @param Current_Page_Helper           $current_page_helper           The Current_Page_Helper.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		API_Client $api_client,
		Options_Helper $options_helper,
		User_Helper $user_helper,
		Introductions_Seen_Repository $introductions_seen_repository,
		Current_Page_Helper $current_page_helper
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::register_routes' );
		$this->asset_manager                 = $asset_manager;
		$this->addon_manager                 = $addon_manager;
		$this->api_client                    = $api_client;
		$this->options_helper                = $options_helper;
		$this->user_helper                   = $user_helper;
		$this->introductions_seen_repository = $introductions_seen_repository;
		$this->current_page_helper           = $current_page_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::register_hooks' );
	}

	/**
	 * Returns `true` when the page is the Elementor editor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return bool `true` when the page is the Elementor editor.
	 */
	private function is_elementor_editor() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::is_elementor_editor' );
	}

	/**
	 * Renders the React container.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function render_react_container(): void {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::render_react_container' );
	}

	/**
	 * Gets the subscription status for Yoast SEO Premium and Yoast WooCommerce SEO.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return array<string, bool>
	 */
	public function get_product_subscriptions() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::get_product_subscriptions' );
	}

	/**
	 * Enqueues the required assets.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::enqueue_assets' );
	}

	/**
	 * Adds the AI Optimize CSS file to the list of CSS files to be loaded inside the classic editor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $css_files The CSS files that WordPress currently inside the classic editor.
	 *
	 * @return string The CSS files, including our AI Optimize CSS file.
	 */
	public function enqueue_css_mce( $css_files ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface\AI_Optimize_Integration::enqueue_css_mce' );
	}
}
