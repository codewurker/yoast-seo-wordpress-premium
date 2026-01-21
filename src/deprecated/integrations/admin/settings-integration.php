<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Settings_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Settings_Integration.
 *
 * @deprecated 26.8
 * @codeCoverageIgnore
 */
class Settings_Integration implements Integration_Interface {

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	protected $current_page_helper;

	/**
	 * Constructs Settings_Integration.
	 *
	 * @deprecated 26.8
	 * @codeCoverageIgnore
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager       The WPSEO_Admin_Asset_Manager.
	 * @param Current_Page_Helper       $current_page_helper The Current_Page_Helper.
	 */
	public function __construct( WPSEO_Admin_Asset_Manager $asset_manager, Current_Page_Helper $current_page_helper ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.8' );
		$this->asset_manager       = $asset_manager;
		$this->current_page_helper = $current_page_helper;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @deprecated 26.8
	 * @codeCoverageIgnore
	 * @return array
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.8' );
		return [ Settings_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @deprecated 26.8
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function register_hooks() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.8' );
	}

	/**
	 * Enqueues the assets.
	 *
	 * @deprecated 26.8
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function enqueue_assets() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.8' );
	}
}
