<?php

namespace Yoast\WP\SEO\Premium;

use Exception;
use Plugin_Upgrader;
use stdClass;
use WP_Error;
use WP_Upgrader_Skin;
use WPSEO_Capability_Manager_Factory;
use WPSEO_Options;
use WPSEO_Premium_Option;

/**
 * Class responsible for performing the premium as an addon installer.
 */
class Addon_Installer {

	/**
	 * The option key for tracking the status of the installer.
	 */
	public const OPTION_KEY = 'yoast_premium_as_an_addon_installer';

	/**
	 * The transient key used to prevent concurrent installs.
	 */
	private const LOCK_KEY = 'yoast_premium_addon_install_lock';

	/**
	 * The lock time-to-live in seconds.
	 */
	private const LOCK_TTL = 300; // 5 minutes.

	/**
	 * The transient key used to back off after a failed install attempt.
	 */
	private const COOLDOWN_KEY = 'yoast_premium_addon_install_cooldown';

	/**
	 * The cooldown time-to-live in seconds.
	 */
	private const COOLDOWN_TTL = 86_400; // 24 hours.

	/**
	 * The minimum Yoast SEO version required.
	 */
	public const MINIMUM_YOAST_SEO_VERSION = '28.0';

	/**
	 * The trunk download URL for Yoast SEO.
	 */
	public const TRUNK_URL = 'https://downloads.wordpress.org/plugin/wordpress-seo.zip';

	/**
	 * The filter callback for injecting the update transient.
	 *
	 * @var array<int, object|string>|null
	 */
	private $update_transient_filter = null;

	/**
	 * The URL to inject into the update transient filter.
	 *
	 * @var string|null
	 */
	private $update_transient_url = null;

	/**
	 * The base directory for the installer.
	 *
	 * @var string
	 */
	protected $base_dir;

	/**
	 * The detected Yoast SEO version.
	 *
	 * @var string
	 */
	protected $yoast_seo_version = '0';

	/**
	 * The detected Yoast SEO plugin file.
	 *
	 * @var string
	 */
	protected $yoast_seo_file = 'wordpress-seo/wp-seo.php';

	/**
	 * The detected Yoast SEO directory.
	 *
	 * @var string
	 */
	protected $yoast_seo_dir = \WP_PLUGIN_DIR . '/wordpress-seo';

	/**
	 * Addon installer constructor.
	 *
	 * @param string $base_dir The base directory.
	 */
	public function __construct( $base_dir ) {
		$this->base_dir = $base_dir;
	}

	/**
	 * Performs the installer if it hasn't been done yet.
	 *
	 * A notice will be shown in the admin if the installer failed.
	 *
	 * @return void
	 */
	public function install_yoast_seo_from_repository() {
		\add_action( 'admin_notices', [ $this, 'show_install_yoast_seo_notification' ] );
		\add_action( 'network_admin_notices', [ $this, 'show_install_yoast_seo_notification' ] );
		\add_action( 'plugins_loaded', [ $this, 'validate_installation_status' ] );
		if ( ! $this->get_status() ) {
			try {
				$this->install();
			} catch ( Exception $e ) {
				// Auto installation failed, the notice will be displayed.
				return;
			}
		}
		elseif ( $this->get_status() === 'started' ) {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
			$this->detect_yoast_seo();
			if ( \is_plugin_active( $this->yoast_seo_file ) ) {
				// Yoast SEO is active so mark installation as successful.
				\update_option( self::OPTION_KEY, 'completed', true );
				// Enable tracking.
				if ( \class_exists( WPSEO_Options::class ) ) {
					WPSEO_Premium_Option::register_option();
					if ( WPSEO_Options::get( 'toggled_tracking' ) !== true ) {
						WPSEO_Options::set( 'tracking', true );
					}
					WPSEO_Options::set( 'should_redirect_after_install', true );
				}

				if ( \class_exists( WPSEO_Capability_Manager_Factory::class ) ) {
					\do_action( 'wpseo_register_capabilities_premium' );
					WPSEO_Capability_Manager_Factory::get( 'premium' )->add();
				}
			}
		}
	}

	/**
	 * Performs the installer if it hasn't been done yet.
	 * Otherwise attempts to load Yoast SEO from the vendor directory.
	 *
	 * @deprecated 21.9
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function install_or_load_yoast_seo_from_vendor_directory() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 21.9' );
	}

	/**
	 * Displays a notification to install Yoast SEO.
	 *
	 * @return void
	 */
	public function show_install_yoast_seo_notification() {
		if ( ! $this->should_show_notification() ) {
			return;
		}

		require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		$this->detect_yoast_seo();

		$action = $this->get_notification_action();

		if ( ! $action ) {
			return;
		}

		echo (
			'<div class="error yoast-migrated-notice">'
				. '<h4 class="yoast-notice-migrated-header">'
					. \sprintf(
						/* translators: %1$s: Yoast SEO */
						\esc_html__( 'Install latest %1$s', 'wordpress-seo-premium' ),
						'Yoast SEO',
					)
				. '</h4>'
				. '<div class="notice-yoast-content">'
					. '<p>'
						. \sprintf(
							/* translators: %1$s: Yoast SEO, %2$s: The minimum Yoast SEO version required, %3$s: Yoast SEO Premium. */
							\esc_html__( '%1$s %2$s must be installed and activated in order to use %3$s.', 'wordpress-seo-premium' ),
							'Yoast SEO',
							\esc_html( self::MINIMUM_YOAST_SEO_VERSION ),
							'Yoast SEO Premium',
						)
					. '</p>'
					. '<p>'
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped above.
						. $action
					. '</p>'
				. '</div>'
			. '</div>'
		);
	}

	/**
	 * Returns the notification action to display.
	 *
	 * @return string|false The notification action or false if no action should be taken.
	 */
	protected function get_notification_action() {
		$minimum_version_met = \version_compare( $this->yoast_seo_version, self::MINIMUM_YOAST_SEO_VERSION . '-RC0', '>=' );
		$network_active      = \is_plugin_active_for_network( \WPSEO_PREMIUM_BASENAME );
		$yoast_seo_active    = ( $network_active ) ? \is_plugin_active_for_network( $this->yoast_seo_file ) : \is_plugin_active( $this->yoast_seo_file );

		if ( $minimum_version_met && $yoast_seo_active ) {
			return false;
		}

		if ( $minimum_version_met ) {
			$permission = 'activate_plugins';
		}
		elseif ( $this->yoast_seo_version !== '0' ) {
			$permission = 'update_plugins';
		}
		else {
			$permission = 'install_plugins';
		}

		if ( \current_user_can( $permission ) ) {
			switch ( $permission ) {
				case 'activate_plugins':
					if ( $network_active ) {
						$base_url = \network_admin_url( 'plugins.php?action=activate&plugin=' . $this->yoast_seo_file );
						/* translators: %1$s: Yoast SEO, %2$s: Link start tag, %3$s: Link end tag. */
						$button_content = \__( '%2$sNetwork Activate %1$s now%3$s', 'wordpress-seo-premium' );
					}
					else {
						$base_url = \self_admin_url( 'plugins.php?action=activate&plugin=' . $this->yoast_seo_file );
						/* translators: %1$s: Yoast SEO, %2$s: Link start tag, %3$s: Link end tag. */
						$button_content = \__( '%2$sActivate %1$s now%3$s', 'wordpress-seo-premium' );
					}
					$url = \wp_nonce_url( $base_url, 'activate-plugin_' . $this->yoast_seo_file );
					break;
				case 'update_plugins':
					$url = \wp_nonce_url( \self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $this->yoast_seo_file ), 'upgrade-plugin_' . $this->yoast_seo_file );
					/* translators: %1$s: Yoast SEO, %2$s: Link start tag, %3$s: Link end tag. */
					$button_content = \__( '%2$sUpgrade %1$s now%3$s', 'wordpress-seo-premium' );
					break;
				case 'install_plugins':
					$url = \wp_nonce_url( \self_admin_url( 'update.php?action=install-plugin&plugin=wordpress-seo' ), 'install-plugin_wordpress-seo' );
					/* translators: %1$s: Yoast SEO, %2$s: Link start tag, %3$s: Link end tag. */
					$button_content = \__( '%2$sInstall %1$s now%3$s', 'wordpress-seo-premium' );
					break;
			}
			return \sprintf(
				\esc_html( $button_content ),
				'Yoast SEO',
				'<a class="button" href="' . \esc_url( $url ) . '">',
				'</a>',
			);
		}

		if ( \is_multisite() ) {
			/* translators: %1$s: Yoast SEO, %2$s: The minimum Yoast SEO version required. */
			$message = \__( 'Please contact a network administrator to install %1$s %2$s.', 'wordpress-seo-premium' );
		}
		else {
			/* translators: %1$s: Yoast SEO, %2$s: The minimum Yoast SEO version required. */
			$message = \__( 'Please contact an administrator to install %1$s %2$s.', 'wordpress-seo-premium' );
		}
		return \sprintf(
			\esc_html( $message ),
			'Yoast SEO',
			\esc_html( self::MINIMUM_YOAST_SEO_VERSION ),
		);
	}

	/**
	 * Checks if Yoast SEO is at a minimum required version.
	 *
	 * @return bool True if Yoast SEO is at a minimal required version
	 */
	public static function is_yoast_seo_up_to_date() {
		return ( \defined( 'WPSEO_VERSION' ) && \version_compare( \WPSEO_VERSION, self::MINIMUM_YOAST_SEO_VERSION . '-RC0', '>=' ) );
	}

	/**
	 * Resets the installation status if Yoast SEO is not installed or outdated.
	 *
	 * @return void
	 */
	public function validate_installation_status() {
		if ( ! self::is_yoast_seo_up_to_date() ) {
			\delete_option( self::OPTION_KEY );
		}
	}

	/**
	 * Returns the status of the installer.
	 *
	 * This uses a separate option from our options framework as it needs to be available
	 * before Yoast SEO has been loaded.
	 *
	 * @return string|false false if the installer hasn't been started.
	 *                      "started" if it has but hasn't completed.
	 *                      "completed" if it has been completed.
	 */
	protected function get_status() {
		return \get_option( self::OPTION_KEY );
	}

	/**
	 * Installs to premium as an addon.
	 *
	 * @return void
	 *
	 * @throws Exception If the installer failed.
	 */
	protected function install() {
		if ( $this->get_status() ) {
			return;
		}

		// Back off if a recent install attempt failed.
		if ( \get_transient( self::COOLDOWN_KEY ) ) {
			return;
		}

		// Acquire a lock to prevent concurrent installs.
		if ( \get_transient( self::LOCK_KEY ) ) {
			return;
		}
		\set_transient( self::LOCK_KEY, true, self::LOCK_TTL );

		try {
			// Mark the installer as having been started but not completed.
			\update_option( self::OPTION_KEY, 'started', true );

			require_once \ABSPATH . 'wp-admin/includes/plugin.php';

			$this->detect_yoast_seo();
			// Either the plugin is not installed or is installed and too old.
			if ( \version_compare( $this->yoast_seo_version, self::MINIMUM_YOAST_SEO_VERSION . '-RC0', '<' ) ) {
				include_once \ABSPATH . 'wp-includes/pluggable.php';
				include_once \ABSPATH . 'wp-admin/includes/file.php';
				include_once \ABSPATH . 'wp-admin/includes/misc.php';
				require_once \ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

				$free_is_installed = ( $this->yoast_seo_version !== '0' );

				/**
				 * Filters the trunk download URL used when the versioned Yoast SEO zip is not yet available.
				 *
				 * @param string $trunk_url The default trunk URL pointing to the latest Yoast SEO release on wordpress.org.
				 */
				$trunk_url = (string) \apply_filters( 'Yoast\WP\SEO\Premium\trunk_url', self::TRUNK_URL );

				/**
				 * Filters the versioned download URL for the minimum required Yoast SEO release.
				 *
				 * @param string $versioned_url The default versioned URL on wordpress.org.
				 * @param string $version       The minimum required Yoast SEO version.
				 */
				$versioned_url       = (string) \apply_filters(
					'Yoast\WP\SEO\Premium\versioned_url',
					'https://downloads.wordpress.org/plugin/wordpress-seo.' . self::MINIMUM_YOAST_SEO_VERSION . '.zip',
					self::MINIMUM_YOAST_SEO_VERSION,
				);
				$version_is_released = \wp_remote_retrieve_response_code( \wp_remote_head( $versioned_url, [ 'redirection' => 5 ] ) ) === 200;

				if ( $free_is_installed ) {
					// Upgrade via WP's update system; inject trunk URL if the version is not yet released.
					$result = $this->perform_upgrade( ( $version_is_released ) ? null : $trunk_url );
				}
				else {
					// Fresh install: use versioned URL if available, otherwise trunk.
					$result = $this->perform_fresh_install( ( $version_is_released ) ? $versioned_url : $trunk_url );
				}

				if ( \is_wp_error( $result ) || ! $result ) {
					\set_transient( self::COOLDOWN_KEY, true, self::COOLDOWN_TTL );
					throw new Exception( 'Could not automatically install Yoast SEO' );
				}

				// Re-detect after install/upgrade since the plugin file path may have changed.
				$this->detect_yoast_seo();
			}

			$this->ensure_yoast_seo_is_activated();
			$this->transfer_auto_update_settings();
			// Mark the installer as having been completed.
			\update_option( self::OPTION_KEY, 'completed', true );
		} finally {
			\delete_transient( self::LOCK_KEY );
		}
	}

	/**
	 * Creates a silent upgrader skin that suppresses all output.
	 *
	 * The class is defined inline to avoid problems with the autoloader when extending a WP class.
	 *
	 * @return WP_Upgrader_Skin The silent skin instance.
	 */
	protected function create_silent_skin() {
		return new class() extends WP_Upgrader_Skin {

			/**
			 * Suppresses the header.
			 *
			 * @return void
			 */
			public function header() {
			}

			/**
			 * Suppresses the footer.
			 *
			 * @return void
			 */
			public function footer() {
			}

			/**
			 * Suppresses the errors.
			 *
			 * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Flags unused params which are required via the interface. Invalid.
			 *
			 * @param string|WP_Error $errors Errors.
			 *
			 * @return void
			 */
			public function error( $errors ) {
			}

			/**
			 * Suppresses the feedback.
			 *
			 * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Flags unused params which are required via the interface. Invalid.
			 *
			 * @param string        $feedback Message data.
			 * @param array<string> ...$args  Optional text replacements.
			 *
			 * @return void
			 */
			public function feedback( $feedback, ...$args ) {
			}
		};
	}

	/**
	 * Performs a fresh install of Yoast SEO from the given URL.
	 *
	 * @param string $url The download URL.
	 *
	 * @return bool|WP_Error The install result.
	 */
	private function perform_fresh_install( string $url ) {
		$skin     = $this->create_silent_skin();
		$upgrader = new Plugin_Upgrader( $skin ); // nosemgrep audit.php.wp.security.arbitrary-plugin-install -- Used to pull and install Yoast SEO from the WordPress repository.
		return $upgrader->install( $url );
	}

	/**
	 * Performs an upgrade of an existing Yoast SEO installation.
	 *
	 * When a trunk URL is provided, a temporary filter is added to inject it
	 * into the update_plugins transient so the upgrader can find the package.
	 *
	 * @param string|null $trunk_url Optional trunk URL for unreleased versions.
	 *
	 * @return bool|WP_Error The upgrade result.
	 */
	private function perform_upgrade( ?string $trunk_url = null ) {
		if ( $trunk_url !== null ) {
			$this->inject_update_transient( $trunk_url );
		}

		try {
			$skin     = $this->create_silent_skin();
			$upgrader = new Plugin_Upgrader( $skin ); // nosemgrep audit.php.wp.security.arbitrary-plugin-install -- Used to pull and upgrade Yoast SEO from the WordPress repository.
			return $upgrader->upgrade( $this->yoast_seo_file );
		} finally {
			if ( $trunk_url !== null ) {
				$this->remove_update_transient();
			}
		}
	}

	/**
	 * Injects a download URL into the update_plugins transient via a filter.
	 *
	 * @param string $url The download URL to inject.
	 *
	 * @return void
	 */
	private function inject_update_transient( string $url ): void {
		$this->update_transient_url    = $url;
		$this->update_transient_filter = [ $this, 'filter_update_plugins_transient' ];
		\add_filter( 'site_transient_update_plugins', $this->update_transient_filter );
	}

	/**
	 * Filters the update_plugins transient to inject a download URL.
	 *
	 * @param object|false $transient The update_plugins transient value.
	 *
	 * @return object The modified transient.
	 */
	public function filter_update_plugins_transient( $transient ) {
		if ( ! \is_object( $transient ) ) {
			$transient = new stdClass();
		}
		if ( ! isset( $transient->response ) ) {
			$transient->response = [];
		}
		$transient->response[ $this->yoast_seo_file ] = (object) [
			'slug'    => 'wordpress-seo',
			'plugin'  => $this->yoast_seo_file,
			'package' => $this->update_transient_url,
		];
		return $transient;
	}

	/**
	 * Removes the update_plugins transient filter if it was added.
	 *
	 * @return void
	 */
	private function remove_update_transient(): void {
		if ( $this->update_transient_filter !== null ) {
			\remove_filter( 'site_transient_update_plugins', $this->update_transient_filter );
			$this->update_transient_filter = null;
			$this->update_transient_url    = null;
		}
	}

	/**
	 * Detects the Yoast SEO plugin file and version.
	 *
	 * @return void
	 */
	protected function detect_yoast_seo() {
		// Make sure Yoast SEO isn't already installed in another directory.
		foreach ( \get_plugins() as $file => $plugin ) {
			// Use text domain to identify the plugin as it's the closest thing to a slug.
			if (
				isset( $plugin['TextDomain'] ) && $plugin['TextDomain'] === 'wordpress-seo'
				&& isset( $plugin['Name'] ) && $plugin['Name'] === 'Yoast SEO'
			) {
				$this->yoast_seo_file    = $file;
				$this->yoast_seo_version = ( $plugin['Version'] ?? '0' );
				$this->yoast_seo_dir     = \WP_PLUGIN_DIR . '/' . \dirname( $file );
			}
		}
	}

	/**
	 * Activates Yoast SEO.
	 *
	 * @return void
	 *
	 * @throws Exception If Yoast SEO could not be activated.
	 */
	protected function ensure_yoast_seo_is_activated() {
		if ( ! \is_plugin_active( $this->yoast_seo_file ) ) {
			$network_active = \is_plugin_active_for_network( \WPSEO_PREMIUM_BASENAME );
			// If we're not active at all it means we're being activated.
			if ( ! $network_active && ! \is_plugin_active( \WPSEO_PREMIUM_BASENAME ) ) {
				// So set network active to whether or not we're in the network admin.
				$network_active = \is_network_admin();
			}
			// Activate Yoast SEO. If Yoast SEO Premium is network active then make sure Yoast SEO is as well.
			$activation = \activate_plugin( $this->yoast_seo_file, '', $network_active );
			if ( \is_wp_error( $activation ) ) {
				throw new Exception( \esc_html( 'Could not activate Yoast SEO: ' . $activation->get_error_message() ) );
			}
		}
	}

	/**
	 * Transfers the auto update settings for Yoast SEO Premium to Yoast SEO.
	 *
	 * @return void
	 */
	protected function transfer_auto_update_settings() {
		$auto_updates = (array) \get_site_option( 'auto_update_plugins', [] );

		if ( \in_array( \WPSEO_PREMIUM_BASENAME, $auto_updates, true ) ) {
			$auto_updates[] = $this->yoast_seo_file;
			$auto_updates   = \array_unique( $auto_updates );
			\update_site_option( 'auto_update_plugins', $auto_updates );
		}
	}

	/**
	 * Wether or not the notification to install Yoast SEO should be shown.
	 *
	 * This is copied from the Yoast_Admin_And_Dashboard_Conditional which we can't use as Yoast SEO may not be installed.
	 *
	 * @return bool
	 */
	protected function should_show_notification() {
		global $pagenow;

		// Do not output on plugin / theme upgrade pages or when WordPress is upgrading.
		if ( ( \defined( 'IFRAME_REQUEST' ) && \IFRAME_REQUEST ) || \wp_installing() ) {
			return false;
		}

		/*
		 * IFRAME_REQUEST is not defined on these pages,
		 * though these action pages do show when upgrading themes or plugins.
		 */
		$actions = [ 'do-theme-upgrade', 'do-plugin-upgrade', 'do-core-upgrade', 'do-core-reinstall' ];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['action'] ) && \in_array( $_GET['action'], $actions, true ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput -- Reason: We are not processing form information, only a strpos is done in the input.
		if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) && \strpos( $_GET['page'], 'wpseo' ) === 0 ) {
			return true;
		}

		$target_pages = [
			'index.php',
			'plugins.php',
			'update-core.php',
			'options-permalink.php',
		];

		return \in_array( $pagenow, $target_pages, true );
	}
}
