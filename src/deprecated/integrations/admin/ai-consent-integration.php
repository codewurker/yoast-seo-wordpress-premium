<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\User_Profile_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;

/**
 * Ai_Consent_Integration class.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class Ai_Consent_Integration implements Integration_Interface {

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
	 * Represents the wistia embed permission repository.
	 *
	 * @var Wistia_Embed_Permission_Repository
	 */
	private $wistia_embed_permission_repository;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		return [ User_Profile_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param WPSEO_Admin_Asset_Manager          $asset_manager                      The admin asset manager.
	 * @param WPSEO_Addon_Manager                $addon_manager                      The addon manager.
	 * @param Options_Helper                     $options_helper                     The options helper.
	 * @param User_Helper                        $user_helper                        The user helper.
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The wistia embed permission
	 *                                                                               repository.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		Options_Helper $options_helper,
		User_Helper $user_helper,
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		$this->asset_manager                      = $asset_manager;
		$this->addon_manager                      = $addon_manager;
		$this->options_helper                     = $options_helper;
		$this->user_helper                        = $user_helper;
		$this->wistia_embed_permission_repository = $wistia_embed_permission_repository;
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
	 * Renders the AI consent button for the user profile.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function render_user_profile() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 25.6' );

		echo '<label for="ai-generator-consent-button">',
		\esc_html__( 'AI features', 'wordpress-seo-premium' ),
		'</label>',
		'<div id="ai-generator-consent" style="display:inline-block; margin-top: 28px; padding-left:5px;"></div>';
	}
}
