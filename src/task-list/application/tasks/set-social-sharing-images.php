<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Task_List\Application\Tasks;

use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Premium\Task_List\Application\Tasks\Child_Tasks\Set_Social_Sharing_Images_Child;
use Yoast\WP\SEO\Premium\Task_List\Infrastructure\Indexables\Recent_Content_Indexable_Collector;
use Yoast\WP\SEO\Task_List\Domain\Components\Call_To_Action_Entry;
use Yoast\WP\SEO\Task_List\Domain\Components\Copy_Set;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Abstract_Post_Type_Parent_Task;

/**
 * Represents the task for setting social sharing images on recent content.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Set_Social_Sharing_Images extends Abstract_Post_Type_Parent_Task {

	/**
	 * The default maximum number of content items to retrieve.
	 *
	 * @var int
	 */
	public const DEFAULT_LIMIT = 100;

	/**
	 * Holds the id.
	 *
	 * @var string
	 */
	protected $id = 'set-social-sharing-images';

	/**
	 * Holds the priority.
	 *
	 * @var string
	 */
	protected $priority = 'medium';

	/**
	 * Holds the recent content indexable collector.
	 *
	 * @var Recent_Content_Indexable_Collector
	 */
	private $recent_content_indexable_collector;

	/**
	 * Holds the indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Constructs the task.
	 *
	 * @param Recent_Content_Indexable_Collector $recent_content_indexable_collector The recent content indexable collector.
	 * @param Indexable_Helper                   $indexable_helper                   The indexable helper.
	 */
	public function __construct(
		Recent_Content_Indexable_Collector $recent_content_indexable_collector,
		Indexable_Helper $indexable_helper
	) {
		$this->recent_content_indexable_collector = $recent_content_indexable_collector;
		$this->indexable_helper                   = $indexable_helper;
	}

	/**
	 * Returns the task's badge.
	 *
	 * @return string|null
	 */
	public function get_badge(): ?string {
		return 'premium';
	}

	/**
	 * Returns the task's link.
	 *
	 * @return string|null
	 */
	public function get_link(): ?string {
		return null;
	}

	/**
	 * Returns the task's call to action entry.
	 *
	 * @return Call_To_Action_Entry|null
	 */
	public function get_call_to_action(): ?Call_To_Action_Entry {
		return null;
	}

	/**
	 * Returns the task's copy set.
	 *
	 * @return Copy_Set
	 */
	public function get_copy_set(): Copy_Set {
		$post_type = \get_post_type_object( $this->get_post_type() );

		return new Copy_Set(
			/* translators: %1$s expands to the post type label this task is about. */
			\sprintf( \__( 'Set social sharing images in your recent content: %1$s', 'wordpress-seo-premium' ), $post_type->label ),
			\sprintf(
				/* translators: %1$s and %3$s expands to an opening p tag, %2$s and %4$s expand to a closing p tag */
				\__( '%1$sUnstyled preview images can lower engagement when shared on social media. Keep every post on brand. %2$s%3$sOpen the post, go to Yoast SEO Analysis tab, click on Social, and add a relevant image to your preview.%4$s', 'wordpress-seo-premium' ),
				'<p>',
				'</p>',
				'<p>',
				'</p>',
			),
		);
	}

	/**
	 * Populates the child tasks by querying content modified in the last two months.
	 *
	 * @return Set_Social_Sharing_Images_Child[]
	 */
	public function populate_child_tasks(): array {
		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return [];
		}

		// @TODO: We are going to introduce a Recent_Content_Task_Trait to implement this rather tha repeating the same logic in multiple tasks.
		$two_months_ago = \gmdate( 'Y-m-d H:i:s', \strtotime( '-2 months' ) );

		$content_data = $this->recent_content_indexable_collector->get_recent_content_with_og_image(
			$post_type,
			$two_months_ago,
			self::DEFAULT_LIMIT,
		);

		$child_tasks = [];
		foreach ( $content_data as $content_item ) {
			$child_tasks[] = new Set_Social_Sharing_Images_Child(
				$this,
				$content_item,
			);
		}

		return $child_tasks;
	}

	/**
	 * Returns whether the task is valid.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		if ( ! $this->indexable_helper->should_index_indexables() ) {
			return false;
		}

		return true;
	}
}
