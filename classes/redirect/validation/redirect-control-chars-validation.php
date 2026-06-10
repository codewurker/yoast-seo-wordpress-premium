<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes\Redirect\Validation
 */

/**
 * Rejects redirects whose origin or target carries a C0 control character or DEL.
 *
 * Closes the .htaccess directive-injection primitive: an authenticated user could
 * otherwise embed a newline (or other control byte) in origin/target and have it
 * land verbatim in the rewrite file, smuggling an extra Apache directive in.
 *
 * This validation runs at the user-input boundary so the rejection is explicit and
 * visible. WPSEO_Redirect_Option::save() still drops poisoned rows at persistence
 * time as defense-in-depth (legacy data, third-party filter hooks).
 */
class WPSEO_Redirect_Control_Chars_Validation extends WPSEO_Redirect_Abstract_Validation {

	/**
	 * Validates that origin and target are free of control characters.
	 *
	 * @param WPSEO_Redirect             $redirect     The redirect to validate.
	 * @param WPSEO_Redirect|null        $old_redirect Unused.
	 * @param array<string, string>|null $redirects    Unused.
	 *
	 * @return bool
	 */
	public function run( WPSEO_Redirect $redirect, ?WPSEO_Redirect $old_redirect = null, ?array $redirects = null ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( ! WPSEO_Redirect::pair_has_control_chars( $redirect->get_origin(), $redirect->get_target() ) ) {
			return true;
		}

		$this->set_error(
			new WPSEO_Validation_Error(
				__( 'The redirect URLs may not contain line breaks or control characters.', 'wordpress-seo-premium' ),
				[ 'origin', 'target' ],
			),
		);

		return false;
	}
}
