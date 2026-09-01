<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.

namespace Yoast\WP\SEO\Premium\Bulk_Editor\User_Interface;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Bulk_Editor\User_Interface\Bulk_Editor_Integration;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Loads morphology data into the Free bulk editor's client-side scoring, so its per-field scores match the
 * Premium editor.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Bulk_Editor_Morphology_Integration implements Integration_Interface {

	/**
	 * The script handle, registered in WPSEO_Premium_Assets.
	 */
	public const ASSET_HANDLE = 'wp-seo-premium-bulk-editor-morphology';

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the Url_Helper.
	 *
	 * @var Url_Helper
	 */
	private $url_helper;

	/**
	 * Holds the WPSEO_Addon_Manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Constructs the instance.
	 *
	 * @param Current_Page_Helper $current_page_helper The Current_Page_Helper.
	 * @param Url_Helper          $url_helper          The Url_Helper.
	 * @param WPSEO_Addon_Manager $addon_manager       The WPSEO_Addon_Manager.
	 */
	public function __construct(
		Current_Page_Helper $current_page_helper,
		Url_Helper $url_helper,
		WPSEO_Addon_Manager $addon_manager
	) {
		$this->current_page_helper = $current_page_helper;
		$this->url_helper          = $url_helper;
		$this->addon_manager       = $addon_manager;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Only on the Free bulk editor page, whose client-side scoring exposes the researcher filter.
		if ( $this->current_page_helper->get_current_yoast_seo_page() === Bulk_Editor_Integration::PAGE ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}
	}

	/**
	 * Enqueues the morphology script and localizes the data its MyYoast request needs.
	 *
	 * Skipped without a valid subscription: the MyYoast download would be rejected, so scoring keeps using
	 * base word forms.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ) ) {
			return;
		}

		\wp_enqueue_script( self::ASSET_HANDLE );
		\wp_localize_script(
			self::ASSET_HANDLE,
			'wpseoPremiumBulkEditorMorphology',
			[
				'siteUrl'       => $this->url_helper->network_safe_home_url(),
				'pluginVersion' => \WPSEO_PREMIUM_VERSION,
			],
		);
	}
}
