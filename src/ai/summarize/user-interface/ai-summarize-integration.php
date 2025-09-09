<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Summarize\User_Interface;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\AI_Summarize_Support_Conditional;

/**
 * AI_Summarize_Integration class.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class AI_Summarize_Integration implements Integration_Interface {

	/**
	 * Represents the options manager.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals(): array {
		return [ AI_Conditional::class, AI_Summarize_Support_Conditional::class ];
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
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Ensure the AI generator feature is enabled before proceeding.
		if ( ! $this->options_helper->get( 'enable_ai_generator', false ) ) {
			return;
		}

		\add_filter( 'block_categories_all', [ $this, 'add_block_categories' ] );

		// Register the block.
		$base_path = \WPSEO_PREMIUM_PATH . 'assets/blocks/ai-blocks/';
		\register_block_type(
			$base_path . 'summary/block.json',
			[
				'editor_script_handles' => [ 'wp-seo-premium-ai-blocks' ],
				'editor_style_handles'  => [ WPSEO_Admin_Asset_Manager::PREFIX . 'premium-ai-summarize' ],
			]
		);
	}

	/**
	 * Adds a custom block category for AI blocks.
	 *
	 * @param array<array<string, string>> $block_categories Array of categories for block types.
	 *
	 * @return array<array<string, string>> Array of categories for block types with added custom category.
	 */
	public function add_block_categories( array $block_categories ): array {
		return \array_merge(
			$block_categories,
			[
				[
					'slug'  => 'yoast-ai-blocks',
					'title' => \sprintf(
					/* translators: %1$s expands to Yoast. */
						\__( '%1$s AI Blocks', 'wordpress-seo-premium' ),
						'Yoast'
					),
				],
			]
		);
	}
}
