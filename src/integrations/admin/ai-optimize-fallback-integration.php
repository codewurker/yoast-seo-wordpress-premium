<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WP_HTML_Tag_Processor;
use Yoast\WP\SEO\Conditionals\Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\Ai_Editor_Conditional;

/**
 * Ai_Optimize_Fallback_Integration class.
 *
 * Handles cleanup of AI optimization attributes that may remain in post content
 * when content is saved to the database, preventing these attributes from being
 * displayed in the frontend.
 *
 * phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Ai_Optimize_Fallback_Integration implements Integration_Interface {

	/**
	 * Represents the options manager.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Returns the conditionals that determine when this loadable should be active.
	 *
	 * @return array<Conditional> Array of conditional objects that determine if this integration should load.
	 */
	public static function get_conditionals(): array {
		return [ Ai_Editor_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct(
		Options_Helper $options_helper
	) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Initializes the integration by registering hooks and filters.
	 *
	 * This method only registers hooks if the AI feature is enabled.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! $this->options_helper->get( 'enable_ai_generator', false ) ) {
			return;
		}

		\add_filter( 'wp_insert_post_data', [ $this, 'remove_yst_optimize_attribute' ], 10, 1 );
	}

	/**
	 * Removes `data-yst-optimize*` attributes from post and product on save.
	 *
	 * This method processes both regular and escaped attribute formats to ensure
	 * that no optimization attributes remain in the saved content.
	 *
	 * @param array<string, string> $data The post or product data.
	 *
	 * @return array<string, string> The post or product data with cleaned content.
	 */
	public function remove_yst_optimize_attribute( $data ) {
		if ( ! isset( $data['post_content'], $data['post_type'] ) ) {
			return $data;
		}

		$post_type = $data['post_type'];

		// Apply only to post types that support the editor (content field).
		if ( ! \post_type_supports( $post_type, 'editor' ) ) {
			return $data;
		}

		// We want an early return if there's no attribute to remove.
		if ( \strpos( $data['post_content'], 'data-yst-optimize' ) === false ) {
			return $data;
		}

		// If the class is not there we just leave the data attributes since it does not hurt anyone, it just might look weird in the html source.
		if ( \class_exists( WP_HTML_Tag_Processor::class ) ) {
			$data['post_content'] = $this->strip_attribute_with_tag_processor( $data['post_content'] );
		}

		return $data;
	}

	/**
	 * This strips all data-yst-optimize attributes from the content using WP_HTML_Tag_Processor.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	private function strip_attribute_with_tag_processor( $content ) {

		$tag_processor = new WP_HTML_Tag_Processor( $content );
		$attribute     = 'data-yst-optimize';
		while ( $tag_processor->next_tag() ) {
			$tag_processor->remove_attribute( $attribute );
		}

		return $tag_processor->get_updated_html();
	}
}
