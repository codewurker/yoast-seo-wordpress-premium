<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain;

/**
 * Decides whether a post has too little content for AI generation to work well.
 *
 * Mirrors the single-editor AI tip: products carry a lower threshold than regular content, because their
 * descriptions are expected to be shorter.
 */
class Minimal_Content_Policy {

	/**
	 * The character threshold for regular content (posts, pages, and everything that is not irregular).
	 *
	 * @var int
	 */
	public const MIN_CHARACTERS_DEFAULT = 300;

	/**
	 * The character threshold for irregular content (WooCommerce products), whose content is expected to be shorter.
	 *
	 * @var int
	 */
	public const MIN_CHARACTERS_IRREGULAR = 150;

	/**
	 * The content types that use the irregular (lower) threshold.
	 *
	 * @var array<string>
	 */
	private const IRREGULAR_CONTENT_TYPES = [ 'product' ];

	/**
	 * Decides whether the given content length is minimal for its content type.
	 *
	 * @param int    $content_length The plain-text length of the post content, in characters.
	 * @param string $content_type   The post type the content belongs to.
	 *
	 * @return bool Whether the content is minimal, i.e. at or below the threshold for its content type.
	 */
	public function is_minimal_content( int $content_length, string $content_type ): bool {
		if ( \in_array( $content_type, self::IRREGULAR_CONTENT_TYPES, true ) ) {
			return $content_length <= self::MIN_CHARACTERS_IRREGULAR;
		}

		return $content_length <= self::MIN_CHARACTERS_DEFAULT;
	}
}
