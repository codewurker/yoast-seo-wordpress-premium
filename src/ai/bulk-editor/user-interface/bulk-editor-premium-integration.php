<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.

namespace Yoast\WP\SEO\Premium\AI\Bulk_Editor\User_Interface;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\AI\Consent\User_Interface\Consent_Route;
use Yoast\WP\SEO\AI\Generator\User_Interface\Get_Usage_Route;
use Yoast\WP\SEO\Bulk_Editor\User_Interface\Bulk_Editor_Integration;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\User_Interface\AI_Bulk_Suggestions_Route;

/**
 * Adds the Premium AI generate buttons and consent gate to the Free bulk editor.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Bulk_Editor_Premium_Integration implements Integration_Interface {

	/**
	 * The script handle, registered in WPSEO_Premium_Assets.
	 */
	public const ASSET_HANDLE = 'wp-seo-premium-ai-bulk-editor';

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the User_Helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Holds the Short_Link_Helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * Holds the WPSEO_Addon_Manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Constructs the instance.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager       The WPSEO_Admin_Asset_Manager.
	 * @param Current_Page_Helper       $current_page_helper The Current_Page_Helper.
	 * @param User_Helper               $user_helper         The User_Helper.
	 * @param Short_Link_Helper         $short_link_helper   The Short_Link_Helper.
	 * @param WPSEO_Addon_Manager       $addon_manager       The WPSEO_Addon_Manager.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Current_Page_Helper $current_page_helper,
		User_Helper $user_helper,
		Short_Link_Helper $short_link_helper,
		WPSEO_Addon_Manager $addon_manager
	) {
		$this->asset_manager       = $asset_manager;
		$this->current_page_helper = $current_page_helper;
		$this->user_helper         = $user_helper;
		$this->short_link_helper   = $short_link_helper;
		$this->addon_manager       = $addon_manager;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class, AI_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Only on the Free bulk editor page, which exposes the bulk-actions slot.
		if ( $this->current_page_helper->get_current_yoast_seo_page() === Bulk_Editor_Integration::PAGE ) {

			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}
	}

	/**
	 * Enqueues the assets and localizes the consent data.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$user_id = $this->user_helper->get_current_user_id();

		\wp_enqueue_script( self::ASSET_HANDLE );
		// The consent modal reuses the Yoast AI consent styling.
		$this->asset_manager->enqueue_style( 'ai-generator' );
		\wp_localize_script(
			self::ASSET_HANDLE,
			'wpseoPremiumBulkEditor',
			[
				'hasConsent'           => $this->user_helper->get_meta( $user_id, '_yoast_wpseo_ai_consent', true ),
				'endpoints'            => [
					'consent'         => '/' . Consent_Route::ROUTE_NAMESPACE . Consent_Route::ROUTE_PREFIX,
					'bulkSuggestions' => '/' . Main::API_V1_NAMESPACE . '/' . AI_Bulk_Suggestions_Route::AI_BULK_SUGGESTIONS_ROUTE,
					'getUsage'        => '/' . Get_Usage_Route::ROUTE_NAMESPACE . Get_Usage_Route::ROUTE_PREFIX,
				],
				'pluginUrl'            => \plugins_url( '', \WPSEO_PREMIUM_FILE ),
				'adminUrl'             => \admin_url(),
				'linkParams'           => $this->short_link_helper->get_query_params(),
				'productSubscriptions' => [
					'premiumSubscription'     => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ),
					'wooCommerceSubscription' => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ),
					'wooCommerceSeoActive'    => $this->addon_manager->is_installed( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ),
				],
			],
		);
	}
}
