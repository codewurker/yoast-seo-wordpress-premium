<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;
use Yoast\WP\SEO\Premium\Conditionals\Ai_Editor_Conditional;
use Yoast\WP\SEO\Premium\Helpers\AI_Generator_Helper;
use Yoast\WP\SEO\Premium\Helpers\Current_Page_Helper;

/**
 * Ai_Generator_Integration class.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class Ai_Generator_Integration implements Integration_Interface {

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
	 * Represents the AI generator helper.
	 *
	 * @var AI_Generator_Helper
	 */
	private $ai_generator_helper;

	/**
	 * Represents the current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

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
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		return [ Ai_Editor_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WPSEO_Admin_Asset_Manager     $asset_manager                 The admin asset manager.
	 * @param WPSEO_Addon_Manager           $addon_manager                 The addon manager.
	 * @param AI_Generator_Helper           $ai_generator_helper           The AI generator helper.
	 * @param Current_Page_Helper           $current_page_helper           The current page helper.
	 * @param Options_Helper                $options_helper                The options helper.
	 * @param User_Helper                   $user_helper                   The user helper.
	 * @param Introductions_Seen_Repository $introductions_seen_repository The introductions seen repository.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		AI_Generator_Helper $ai_generator_helper,
		Current_Page_Helper $current_page_helper,
		Options_Helper $options_helper,
		User_Helper $user_helper,
		Introductions_Seen_Repository $introductions_seen_repository
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		$this->asset_manager                 = $asset_manager;
		$this->addon_manager                 = $addon_manager;
		$this->ai_generator_helper           = $ai_generator_helper;
		$this->current_page_helper           = $current_page_helper;
		$this->options_helper                = $options_helper;
		$this->user_helper                   = $user_helper;
		$this->introductions_seen_repository = $introductions_seen_repository;
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
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Gets the subscription status for Yoast SEO Premium and Yoast WooCommerce SEO.
	 *
	 * @return array<string, bool>
	 */
	public function get_product_subscriptions() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		return [
			'premiumSubscription'     => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ),
			'wooCommerceSubscription' => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ),
		];
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
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );
	}

	/**
	 * Returns the post type of the currently edited object.
	 * In case this object is a term, returns the taxonomy.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return string
	 */
	private function get_post_type() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		// The order of checking is important here: terms have an empty post_type parameter in their GET request.
		$taxonomy = $this->current_page_helper->get_current_taxonomy();
		if ( $taxonomy !== '' ) {
			return $taxonomy;
		}

		$post_type = $this->current_page_helper->get_current_post_type();
		if ( $post_type ) {
			return $post_type;
		}

		return '';
	}

	/**
	 * Returns the content type (i.e., 'post' or 'term') of the currently edited object.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return string
	 */
	private function get_content_type() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		$taxonomy = $this->current_page_helper->get_current_taxonomy();
		if ( $taxonomy !== '' ) {
			return 'term';
		}

		$post_type = $this->current_page_helper->get_current_post_type();
		if ( $post_type ) {
			return 'post';
		}

		return '';
	}
}
