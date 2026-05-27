<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes
 */

/**
 * Class WPSEO_Redirect_Manager.
 */
class WPSEO_Redirect_Upgrade {

	/**
	 * ID of the persistent admin notification raised by the 27.6.1 cleanup.
	 */
	public const CLEANUP_27_6_1_NOTIFICATION_ID = 'wpseo-premium-redirect-cleanup-27-6-1';

	/**
	 * Lookup table for previous redirect format constants to their current counterparts.
	 *
	 * @var array
	 */
	private static $redirect_option_names = [
		WPSEO_Redirect_Option::OLD_OPTION_PLAIN => WPSEO_Redirect_Formats::PLAIN,
		WPSEO_Redirect_Option::OLD_OPTION_REGEX => WPSEO_Redirect_Formats::REGEX,
	];

	/**
	 * Upgrade routine from Yoast SEO premium 1.2.0.
	 *
	 * @return void
	 */
	public static function upgrade_1_2_0() {
		$redirect_option = self::get_redirect_option();
		$redirects       = [];

		foreach ( self::$redirect_option_names as $redirect_option_name => $redirect_format ) {
			$old_redirects = $redirect_option->get_from_option( $redirect_option_name );

			foreach ( $old_redirects as $origin => $redirect ) {
				// Check if the redirect is not an array yet.
				if ( ! is_array( $redirect ) ) {
					$redirects[] = new WPSEO_Redirect( $origin, $redirect['url'], $redirect['type'], $redirect_format );
				}
			}
		}

		self::import_redirects( $redirects );
	}

	/**
	 * Check if redirects should be imported from the free version.
	 *
	 * @since 2.3
	 *
	 * @return void
	 */
	public static function import_redirects_2_3() {
		// phpcs:ignore WordPress.DB.SlowDBQuery -- Upgrade routine, so rarely used, therefore not an issue.
		$wp_query = new WP_Query( 'post_type=any&meta_key=_yoast_wpseo_redirect&order=ASC' );

		if ( ! empty( $wp_query->posts ) ) {
			$redirects = [];

			foreach ( $wp_query->posts as $post ) {

				$old_url = '/' . $post->post_name . '/';
				$new_url = get_post_meta( $post->ID, '_yoast_wpseo_redirect', true );

				// Create redirect.
				$redirects[] = new WPSEO_Redirect( $old_url, $new_url, 301, WPSEO_Redirect_Formats::PLAIN );

				// Remove post meta value.
				delete_post_meta( $post->ID, '_yoast_wpseo_redirect' );
			}

			self::import_redirects( $redirects );
		}
	}

	/**
	 * Upgrade routine to merge plain and regex redirects in a single option.
	 *
	 * @return void
	 */
	public static function upgrade_3_1() {
		$redirects = [];

		foreach ( self::$redirect_option_names as $redirect_option_name => $redirect_format ) {
			$old_redirects = get_option( $redirect_option_name, [] );

			foreach ( $old_redirects as $origin => $redirect ) {
				// Only when URL and type is set.
				if ( array_key_exists( 'url', $redirect ) && array_key_exists( 'type', $redirect ) ) {
					$redirects[] = new WPSEO_Redirect( $origin, $redirect['url'], $redirect['type'], $redirect_format );
				}
			}
		}

		// Saving the redirects to the option.
		self::import_redirects( $redirects, [ new WPSEO_Redirect_Option_Exporter() ] );
	}

	/**
	 * Exports the redirects to htaccess or nginx file if needed.
	 *
	 * @return void
	 */
	public static function upgrade_13_0() {
		$redirect_manager = new WPSEO_Redirect_Manager();
		$redirect_manager->export_redirects();
	}

	/**
	 * Removes any stored redirect rows whose origin or target contains a C0
	 * control character or DEL. Bails on sites using PHP redirects; on file-based
	 * redirect sites it cleans the option but leaves .htaccess alone -- the admin
	 * is asked, via a persistent notice, to inspect the file editor and clean any
	 * leftover poisoned directives manually.
	 *
	 * @return void
	 */
	public static function cleanup_27_6_1() {
		if ( WPSEO_Options::get( 'disable_php_redirect' ) !== 'on' ) {
			return;
		}

		$rows = get_option( WPSEO_Redirect_Option::OPTION, [] );
		if ( ! is_array( $rows ) || $rows === [] ) {
			return;
		}

		$clean   = [];
		$removed = 0;
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				$clean[] = $row;
				continue;
			}
			if ( WPSEO_Redirect::pair_has_control_chars( ( $row['origin'] ?? '' ), ( $row['url'] ?? '' ) ) ) {
				++$removed;
				continue;
			}
			$clean[] = $row;
		}

		if ( $removed === 0 ) {
			return;
		}

		update_option( WPSEO_Redirect_Option::OPTION, array_values( $clean ) );

		Yoast_Notification_Center::get()->add_notification(
			new Yoast_Notification(
				sprintf(
					/* translators: 1: number of removed redirect entries. 2: link opening tag to the .htaccess file editor. 3: link closing tag. */
					_n(
						'Yoast SEO Premium removed %1$d malformed redirect entry during a security update. Please %2$sinspect your .htaccess%3$s for leftover directives.',
						'Yoast SEO Premium removed %1$d malformed redirect entries during a security update. Please %2$sinspect your .htaccess%3$s for leftover directives.',
						$removed,
						'wordpress-seo-premium',
					),
					$removed,
					'<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=file-editor' ) ) . '">',
					'</a>',
				),
				[
					'type' => 'error',
					'id'   => self::CLEANUP_27_6_1_NOTIFICATION_ID,
				],
			),
		);
	}

	/**
	 * Dismisses the 27.6.1 cleanup notification when the admin is viewing the
	 * .htaccess file editor -- visiting that page is taken as acknowledgement
	 * that the admin has the file open in front of them.
	 *
	 * @return void
	 */
	public static function maybe_dismiss_cleanup_27_6_1_notice() {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- 'wpseo_manage_options' is a custom capability added by Yoast SEO.
		if ( ! current_user_can( 'wpseo_manage_options' ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query-param inspection; no state mutation gated on it.
		if ( ! isset( $_GET['page'], $_GET['tool'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Strict compare against known literals; sanitize is unnecessary.
		if ( wp_unslash( $_GET['page'] ) !== 'wpseo_tools' || wp_unslash( $_GET['tool'] ) !== 'file-editor' ) {
			return;
		}

		Yoast_Notification_Center::get()->remove_notification_by_id( self::CLEANUP_27_6_1_NOTIFICATION_ID );
	}

	/**
	 * Imports an array of redirect objects.
	 *
	 * @param WPSEO_Redirect[]               $redirects The redirects.
	 * @param WPSEO_Redirect_Exporter[]|null $exporters The exporters.
	 *
	 * @return void
	 */
	private static function import_redirects( $redirects, $exporters = null ) {
		if ( empty( $redirects ) ) {
			return;
		}

		$redirect_option  = self::get_redirect_option();
		$redirect_manager = new WPSEO_Redirect_Manager( null, $exporters, $redirect_option );

		foreach ( $redirects as $redirect ) {
			$redirect_option->add( $redirect );
		}

		$redirect_option->save( false );
		$redirect_manager->export_redirects();
	}

	/**
	 * Gets and caches the redirect option.
	 *
	 * @return WPSEO_Redirect_Option
	 */
	private static function get_redirect_option() {
		static $redirect_option;

		if ( empty( $redirect_option ) ) {
			$redirect_option = new WPSEO_Redirect_Option( false );
		}

		return $redirect_option;
	}
}
