<?php

namespace Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Application;

/**
 * Sentence_Processor class
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class Sentence_Processor {
	private const DIFF_THRESHOLD = 5;
	public const INS_PLACEHOLDER = 'ins-yst-tag';
	public const DEL_PLACEHOLDER = 'del-yst-tag';

	/**
	 * Open a tag
	 *
	 * @param string $tag The tag to open.
	 *
	 * @return string The opened tag
	 */
	public static function open( string $tag ): string {
		return "[$tag]";
	}

	/**
	 * Close a tag
	 *
	 * @param string $tag The tag to close.
	 *
	 * @return string The closed tag
	 */
	public static function close( string $tag ): string {
		return "[/$tag]";
	}

	/**
	 * Get all the positions of a tag in a text
	 *
	 * @param string $tag  The tag to search for.
	 * @param string $text The text to search in.
	 *
	 * @return array<int> The positions of the tag in the text.
	 */
	public function get_tag_positions( string $tag, string $text ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::get_tag_positions' );
	}

	/**
	 * Ensure a tag is properly open and closed in a text
	 *
	 * @param string $tag         The tag to check.
	 * @param string $text        The text containing the tag.
	 * @param bool   $is_tag_open Whether the ins tag is still open from previous sentence.
	 *
	 * @return bool Whether the tag is still open after the processing
	 */
	public function ensure_well_formedness_for_tag( string $tag, string &$text, bool $is_tag_open = false ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::ensure_well_formedness_for_tag' );
	}

	/**
	 * Check if the suggestion preview should be switched to sentence based
	 *
	 * @param string $sentence The sentence to check.
	 *
	 * @return bool Whether the suggestion should be switched to sentence based
	 */
	public function should_switch_to_sentence_based( string $sentence ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::should_switch_to_sentence_based' );
	}

	/**
	 * Check if the last sentence in the array is a closing diff tag
	 *
	 * @param array<string> $sentences The sentences to check.
	 *
	 * @return void
	 */
	public function check_last_sentence( array &$sentences ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::check_last_sentence' );
	}

	/**
	 * Apply fixes to a sentence
	 *
	 * @param string $sentence The sentence to apply the fixes to.
	 *
	 * @return string The sentence with the fixes applied
	 */
	public function apply_fixes( string $sentence ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::apply_fixes' );
	}

	/**
	 * Dismiss fixes from a sentence
	 *
	 * @param string $sentence The sentence to dismiss the fixes from.
	 *
	 * @return string The sentence with the fixes dismissed
	 */
	public function dismiss_fixes( string $sentence ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Sentence_Processor::dismiss_fixes' );
	}
}
