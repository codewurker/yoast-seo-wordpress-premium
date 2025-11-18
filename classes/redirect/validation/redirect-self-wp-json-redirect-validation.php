<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes\Redirect\Validation
 */

/**
 * Validator for validating that the redirect doesn't point to the WP JSON endpoint or to itself.
 */
class WPSEO_Redirect_Self_Wp_Json_Redirect_Validation extends WPSEO_Redirect_Abstract_Validation {

	/**
	 * Validate the redirect to check if it doesn't point to itself or to WP JSON endpoints.
	 *
	 * @param WPSEO_Redirect             $redirect     The redirect to validate.
	 * @param WPSEO_Redirect|null        $old_redirect The old redirect to compare.
	 * @param array<WPSEO_Redirect>|null $redirects    Array with redirect to validate against.
	 *
	 * @return bool
	 */
	public function run( WPSEO_Redirect $redirect, ?WPSEO_Redirect $old_redirect = null, ?array $redirects = null ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$target = $redirect->get_target();
		$origin = $redirect->get_origin();

		if ( $this->is_wp_json_endpoint( $origin ) ) {
			$error = __( 'You cannot create a redirect from the WordPress REST API endpoint (/wp-json). Please choose a different origin URL.', 'wordpress-seo-premium' );
			$this->set_error( new WPSEO_Validation_Warning( $error, 'origin' ) );

			return false;
		}

		if ( $this->is_wp_json_endpoint( $target ) ) {
			$error = __( 'You cannot create a redirect to the WordPress REST API endpoint (/wp-json). Please choose a different URL to redirect to.', 'wordpress-seo-premium' );
			$this->set_error( new WPSEO_Validation_Warning( $error, 'target' ) );

			return false;
		}

		return true;
	}

	/**
	 * Check if the given URL is a WordPress JSON endpoint.
	 *
	 * @param string $url The URL to check.
	 *
	 * @return bool True if the URL contains /wp-json, false otherwise.
	 */
	private function is_wp_json_endpoint( $url ) {
		$url = trim( $url );

		return ( strpos( $url, 'wp-json/' ) !== false || preg_match( '#wp-json/?$#', $url ) );
	}
}
