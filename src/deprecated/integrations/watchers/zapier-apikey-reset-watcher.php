<?php

namespace Yoast\WP\SEO\Premium\Integrations\Watchers;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\Zapier_Enabled_Conditional;

/**
 * Watcher for resetting the Zapier API key.
 *
 * Represents the Zapier API key reset watcher for Premium.
 *
 * @deprecated 20.5
 * @codeCoverageIgnore
 */
class Zapier_APIKey_Reset_Watcher implements Integration_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Watcher constructor.
	 *
	 * @deprecated 20.5
	 * @codeCoverageIgnore
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 20.5' );

		$this->options = $options;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @deprecated 20.5
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 20.5' );

		return [ Zapier_Enabled_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @deprecated 20.5
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 20.5' );
	}

	/**
	 * Checks if the Zapier API key must be reset; if so, deletes the data.
	 *
	 * @deprecated 20.5
	 * @codeCoverageIgnore
	 *
	 * @return bool Whether the Zapier data has been deleted or not.
	 */
	public function zapier_api_key_reset() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 20.5' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- The nonce is already validated.
		if ( \current_user_can( 'manage_options' ) && isset( $_POST['zapier_api_key_reset'] ) && $_POST['zapier_api_key_reset'] === '1' ) {
			$this->options->set( 'zapier_api_key', '' );
			$this->options->set( 'zapier_subscription', [] );

			return true;
		}

		return false;
	}
}
