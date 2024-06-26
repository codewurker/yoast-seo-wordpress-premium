<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes
 */

/**
 * Class WPSEO_Redirect_Page.
 */
class WPSEO_Redirect_Page {

	/**
	 * Constructing redirect module.
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->initialize_admin();
		}

		// Only initialize the ajax for all tabs except settings.
		if ( wp_doing_ajax() ) {
			$this->initialize_ajax();
		}
	}

	/**
	 * Display the presenter.
	 *
	 * @return void
	 */
	public function display() {
		$display_args = [ 'current_tab' => $this->get_current_tab() ];

		$redirect_presenter = new WPSEO_Redirect_Page_Presenter();
		$redirect_presenter->display( $display_args );
	}

	/**
	 * Catches possible posted filter values and redirects it to a GET-request.
	 *
	 * It catches:
	 * A search post.
	 * A redirect-type filter.
	 *
	 * @return void
	 */
	public function list_table_search() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Variable is used in a strict comparison and sanitized by wp_safe_redirect anyway.
		$url     = wp_unslash( $_SERVER['REQUEST_URI'] );
		$new_url = $this->extract_redirect_type_from_url( $url );
		$new_url = $this->extract_search_string_from_url( $new_url );

		if ( $url !== $new_url ) {
			// Do the redirect.
			wp_safe_redirect( $new_url );
			exit;
		}
	}

	/**
	 * Extracts the redirect type from the passed URL.
	 *
	 * @param string $url The URL to try and extract the redirect type from.
	 *
	 * @return string The newly formatted URL. Returns original URL if filter is null.
	 */
	protected function extract_redirect_type_from_url( $url ) {
		if ( ( ! isset( $_POST['redirect-type'] ) ) || ( ! is_string( $_POST['redirect-type'] ) )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in wp_verify_none.
			|| ! isset( $_POST['wpseo_redirects_ajax_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wpseo_redirects_ajax_nonce'] ), 'wpseo-redirects-ajax-security' ) ) {
			return $url;
		}

		$filter = sanitize_text_field( wp_unslash( $_POST['redirect-type'] ) );

		$new_url = remove_query_arg( 'redirect-type', $url );

		if ( $filter !== '0' ) {
			$new_url = add_query_arg( 'redirect-type', rawurlencode( $filter ), $new_url );
		}

		return $new_url;
	}

	/**
	 * Extracts the search string from the passed URL.
	 *
	 * @param string $url The URL to try and extract the search string from.
	 *
	 * @return string The newly formatted URL. Returns original URL if search string is null.
	 */
	protected function extract_search_string_from_url( $url ) {
		if ( ( ! isset( $_POST['s'] ) ) || ( ! is_string( $_POST['s'] ) )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in wp_verify_none.
			|| ! isset( $_POST['wpseo_redirects_ajax_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wpseo_redirects_ajax_nonce'] ), 'wpseo-redirects-ajax-security' ) ) {
			return $url;
		}

		$search_string = sanitize_text_field( wp_unslash( $_POST['s'] ) );

		$new_url = remove_query_arg( 's', $url );

		if ( $search_string !== '' ) {
			$new_url = add_query_arg( 's', rawurlencode( $search_string ), $new_url );
		}

		return $new_url;
	}

	/**
	 * Load the admin redirects scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$version       = $asset_manager->flatten_version( WPSEO_PREMIUM_VERSION );

		$dependencies = [
			'jquery',
			'jquery-ui-dialog',
			'wp-util',
			'underscore',
			'wp-api',
			'wp-api-fetch',
		];

		wp_enqueue_script(
			'wp-seo-premium-admin-redirects',
			plugin_dir_url( WPSEO_PREMIUM_FILE )
			. 'assets/js/dist/wp-seo-premium-admin-redirects-' . $version . WPSEO_CSSJS_SUFFIX . '.js',
			$dependencies,
			WPSEO_PREMIUM_VERSION,
			true
		);
		wp_localize_script( 'wp-seo-premium-admin-redirects', 'wpseoPremiumStrings', WPSEO_Premium_Javascript_Strings::strings() );
		wp_localize_script( 'wp-seo-premium-admin-redirects', 'wpseoUserLocale', [ 'code' => substr( get_user_locale(), 0, 2 ) ] );
		wp_localize_script( 'wp-seo-premium-admin-redirects', 'wpseoAdminRedirect', [ 'homeUrl' => home_url( '/' ) ] );
		wp_enqueue_style( 'wpseo-premium-redirects', plugin_dir_url( WPSEO_PREMIUM_FILE ) . 'assets/css/dist/premium-redirects-' . $version . '.css', [], WPSEO_PREMIUM_VERSION );

		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		$screen_option_args = [
			'label'   => __( 'Redirects per page', 'wordpress-seo-premium' ),
			'default' => 25,
			'option'  => 'redirects_per_page',
		];
		add_screen_option( 'per_page', $screen_option_args );
	}

	/**
	 * Catch redirects_per_page.
	 *
	 * @param string|false $status The value to save instead of the option value.
	 *                             Default false (to skip saving the current option).
	 * @param string       $option The option name where the value is set for.
	 * @param string       $value  The new value for the screen option.
	 *
	 * @return string|false
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( $option === 'redirects_per_page' ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Hook that runs after the 'wpseo_redirect' option is updated.
	 *
	 * @param array $old_value Unused.
	 * @param array $value     The new saved values.
	 *
	 * @return void
	 */
	public function save_redirect_files( $old_value, $value ) {

		$is_php = ( empty( $value['disable_php_redirect'] ) || $value['disable_php_redirect'] !== 'on' );

		$was_separate_file = ( ! empty( $old_value['separate_file'] ) && $old_value['separate_file'] === 'on' );
		$is_separate_file  = ( ! empty( $value['separate_file'] ) && $value['separate_file'] === 'on' );

		// Check if the 'disable_php_redirect' option set to true/on.
		if ( ! $is_php ) {
			// The 'disable_php_redirect' option is set to true(on) so we need to generate a file.
			// The Redirect Manager will figure out what file needs to be created.
			$redirect_manager = new WPSEO_Redirect_Manager();
			$redirect_manager->export_redirects();
		}

		// Check if we need to remove the .htaccess redirect entries.
		if ( WPSEO_Utils::is_apache() ) {
			if ( $is_php || ( ! $was_separate_file && $is_separate_file ) ) {
				// Remove the apache redirect entries.
				WPSEO_Redirect_Htaccess_Util::clear_htaccess_entries();
			}

			if ( $is_php || ( $was_separate_file && ! $is_separate_file ) ) {
				// Remove the apache separate file redirect entries.
				WPSEO_Redirect_File_Util::write_file( WPSEO_Redirect_File_Util::get_file_path(), '' );
			}
		}

		if ( WPSEO_Utils::is_nginx() && $is_php ) {
			// Remove the nginx redirect entries.
			$this->clear_nginx_redirects();
		}
	}

	/**
	 * The server should always be apache. And the php redirects have to be enabled or in case of a separate
	 * file it should be disabled.
	 *
	 * @param bool $disable_php_redirect Are the php redirects disabled.
	 * @param bool $separate_file        Value of the separate file.
	 *
	 * @return bool
	 */
	private function remove_htaccess_entries( $disable_php_redirect, $separate_file ) {
		return ( WPSEO_Utils::is_apache() && ( ! $disable_php_redirect || ( $disable_php_redirect && $separate_file ) ) );
	}

	/**
	 * Clears the redirects from the nginx config.
	 *
	 * @return void
	 */
	private function clear_nginx_redirects() {
		$redirect_file = WPSEO_Redirect_File_Util::get_file_path();
		if ( is_writable( $redirect_file ) ) {
			WPSEO_Redirect_File_Util::write_file( $redirect_file, '' );
		}
	}

	/**
	 * Initialize admin hooks.
	 *
	 * @return void
	 */
	private function initialize_admin() {
		$this->fetch_bulk_action();

		// Check if we need to save files after updating options.
		add_action( 'update_option_wpseo_redirect', [ $this, 'save_redirect_files' ], 10, 2 );

		// Convert post into get on search and loading the page scripts.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- We're not manipulating the value.
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) && wp_unslash( $_GET['page'] ) === 'wpseo_redirects' ) {
			$upgrade_manager = new WPSEO_Upgrade_Manager();
			$upgrade_manager->retry_upgrade_31();

			add_action( 'admin_init', [ $this, 'list_table_search' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			add_filter( 'set-screen-option', [ $this, 'set_screen_option' ], 11, 3 );
		}
	}

	/**
	 * Initialize the AJAX redirect files.
	 *
	 * @return void
	 */
	private function initialize_ajax() {
		// Normal Redirect AJAX.
		new WPSEO_Redirect_Ajax( WPSEO_Redirect_Formats::PLAIN );

		// Regex Redirect AJAX.
		new WPSEO_Redirect_Ajax( WPSEO_Redirect_Formats::REGEX );
	}

	/**
	 * Getting the current active tab.
	 *
	 * @return string
	 */
	private function get_current_tab() {
		static $current_tab;

		if ( $current_tab === null ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended --  We're not manipulating the value.
			if ( isset( $_GET['tab'] ) && is_string( $_GET['tab'] )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- value sanitized in the if body, regex filters unwanted values.
			&& in_array( wp_unslash( $_GET['tab'] ), [ WPSEO_Redirect_Formats::PLAIN, WPSEO_Redirect_Formats::REGEX, 'settings' ], true ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the regex takes care of filtering out unwanted values.
				$current_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
			}
			else {
				$current_tab = WPSEO_Redirect_Formats::PLAIN;
			}
		}

		return $current_tab;
	}

	/**
	 * Setting redirect manager, based on the current active tab.
	 *
	 * @return WPSEO_Redirect_Manager
	 */
	private function get_redirect_manager() {
		static $redirect_manager;

		if ( $redirect_manager === null ) {
			$redirects_format = WPSEO_Redirect_Formats::PLAIN;
			if ( $this->get_current_tab() === WPSEO_Redirect_Formats::REGEX ) {
				$redirects_format = WPSEO_Redirect_Formats::REGEX;
			}

			$redirect_manager = new WPSEO_Redirect_Manager( $redirects_format );
		}

		return $redirect_manager;
	}

	/**
	 * Fetches the bulk action for removing redirects.
	 *
	 * @return void
	 */
	private function fetch_bulk_action() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in wp_verify_none.
		if ( ! isset( $_POST['wpseo_redirects_ajax_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wpseo_redirects_ajax_nonce'] ), 'wpseo-redirects-ajax-security' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We're just strictly comparing the value.
		if ( ( ! isset( $_POST['action'] ) || ! is_string( $_POST['action'] ) || ! wp_unslash( $_POST['action'] ) === 'delete' )
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We're just strictly comparing the value.
		&& ( ! isset( $_POST['action2'] ) || ! is_string( $_POST['action2'] ) || ! wp_unslash( $_POST['action2'] ) === 'delete' ) ) {
			return;
		}

		if ( ! isset( $_POST['wpseo_redirects_bulk_delete'] ) || ! is_array( $_POST['wpseo_redirects_bulk_delete'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array elements are sanitized one by one in the foreach loop.
		$bulk_delete = wp_unslash( $_POST['wpseo_redirects_bulk_delete'] );
		$redirects   = [];
		foreach ( $bulk_delete as $origin ) {
			$redirect = $this->get_redirect_manager()->get_redirect( $origin );
			if ( $redirect !== false ) {
				$redirects[] = $redirect;
			}
		}
		$this->get_redirect_manager()->delete_redirects( $redirects );
	}
}
