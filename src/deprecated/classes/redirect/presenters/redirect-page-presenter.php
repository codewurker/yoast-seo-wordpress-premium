<?php
// phpcs:ignoreFile
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes\Redirect\Presenters
 */

/**
 * Class WPSEO_Redirect_Page_Presenter
 *
 * @deprecated 25.7
 * @codeCoverageIgnore
 */
class WPSEO_Redirect_Page_Presenter implements WPSEO_Redirect_Presenter {

	/**
	 * Displays the redirect page.
	 *
	 * @deprecated 25.7
	 * @codeCoverageIgnore
	 *
	 * @param array $display Contextual display data.
	 *
	 * @return void
	 */
	public function display( array $display = [] ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 25.7' );
	}

	/**
	 * Gets the available redirect formats.
	 *
	 * @deprecated 25.7
	 * @codeCoverageIgnore
	 * @return void
	 */
	protected function get_redirect_formats() {
		_deprecated_function( __METHOD__, 'Yoast SEO 25.7' );
	}
}
