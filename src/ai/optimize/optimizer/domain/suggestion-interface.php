<?php
//phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Domain;

/**
 * Interface representing a suggestion domain object.
 */
interface Suggestion_Interface {

	/**
	 * Gets the suggestion content.
	 *
	 * @return string
	 */
	public function get_content(): string;

	/**
	 * Sets the suggestion string.
	 *
	 * @param string $content The suggestion content.
	 *
	 * @return void
	 */
	public function set_content( string $content ): void;
}
