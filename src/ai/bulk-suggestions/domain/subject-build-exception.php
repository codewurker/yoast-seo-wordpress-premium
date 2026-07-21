<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain;

use RuntimeException;

/**
 * Exception thrown when a subject cannot be built for a post.
 */
class Subject_Build_Exception extends RuntimeException {

	public const POST_NOT_FOUND = 'POST_NOT_FOUND';

	public const NOT_ALLOWED_TO_EDIT = 'NOT_ALLOWED_TO_EDIT';

	public const MISSING_FOCUS_KEYPHRASE = 'MISSING_FOCUS_KEYPHRASE';

	/**
	 * The error code identifying why the subject could not be built.
	 *
	 * @var string
	 */
	private $error_code;

	/**
	 * Subject_Build_Exception constructor.
	 *
	 * The arguments are optional so the exception stays autowirable when discovered by the dependency
	 * injection container, mirroring the Free remote-request exceptions.
	 *
	 * @param string $error_code The error code identifying why the subject could not be built.
	 * @param string $message    The human-readable message.
	 */
	public function __construct( string $error_code = '', string $message = '' ) {
		parent::__construct( $message );

		$this->error_code = $error_code;
	}

	/**
	 * Returns the error code identifying why the subject could not be built.
	 *
	 * @return string The error code.
	 */
	public function get_error_code(): string {
		return $this->error_code;
	}
}
