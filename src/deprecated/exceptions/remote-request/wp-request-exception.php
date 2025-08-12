<?php

namespace Yoast\WP\SEO\Premium\Exceptions\Remote_Request;

/**
 * Class to manage an error response in wp_remote_*() requests.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class WP_Request_Exception extends Remote_Request_Exception {

	/**
	 * WP_Request_Exception constructor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string          $message  The error message.
	 * @param Throwable| null $previous The previously thrown exception.
	 */
	public function __construct( $message = '', $previous = null ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6' );
		parent::__construct( $message, 400, 'WP_HTTP_REQUEST_ERROR', $previous );
	}
}
