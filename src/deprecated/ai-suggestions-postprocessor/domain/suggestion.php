<?php

namespace Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Domain;

/**
 * Class implementing the Suggestion_Interface.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class Suggestion implements Suggestion_Interface {

	/**
	 * The suggestion.
	 *
	 * @var string
	 */
	private $content;

	/**
	 * Gets the suggestion content.
	 *
	 * @return string
	 */
	public function get_content(): string {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Domain\Suggestion::get_content' );
		return $this->content;
	}

	/**
	 * Sets the suggestion string.
	 *
	 * @param string $content The suggestion content.
	 *
	 * @return void
	 */
	public function set_content( string $content ): void {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Domain\Suggestion::set_content' );
		$this->content = $content;
	}
}
