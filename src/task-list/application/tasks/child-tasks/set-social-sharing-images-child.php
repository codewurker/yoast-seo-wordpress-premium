<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\Task_List\Application\Tasks\Child_Tasks;

use Yoast\WP\SEO\Premium\Task_List\Domain\Data\Content_Item_OG_Image_Data;
use Yoast\WP\SEO\Task_List\Domain\Components\Call_To_Action_Entry;
use Yoast\WP\SEO\Task_List\Domain\Components\Copy_Set;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Abstract_Child_Task;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Parent_Task_Interface;

/**
 * Represents the child task for setting a social sharing image on a specific content item.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Set_Social_Sharing_Images_Child extends Abstract_Child_Task {

	// @TODO: This can use the Child_Task_Trait in the future.

	/**
	 * Holds the duration.
	 *
	 * @var int
	 */
	protected $duration = 5;

	/**
	 * The content item OG image data.
	 *
	 * @var Content_Item_OG_Image_Data
	 */
	private $content_data;

	/**
	 * Constructs the child task.
	 *
	 * @param Parent_Task_Interface      $parent_task  The parent task.
	 * @param Content_Item_OG_Image_Data $content_data The content item OG image data.
	 */
	public function __construct( Parent_Task_Interface $parent_task, Content_Item_OG_Image_Data $content_data ) {
		$this->parent_task  = $parent_task;
		$this->content_data = $content_data;
	}

	/**
	 * Returns the task's id.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->parent_task->get_id() . '-' . $this->content_data->get_content_id();
	}

	/**
	 * Returns whether this task is completed.
	 *
	 * @return bool Whether this task is completed.
	 */
	public function get_is_completed(): bool {
		return $this->content_data->has_og_image();
	}

	/**
	 * Returns the task's priority.
	 *
	 * @return string
	 */
	public function get_priority(): string {
		return 'medium';
	}

	/**
	 * Returns the task's link.
	 *
	 * @return string|null
	 */
	public function get_link(): ?string {
		return \get_edit_post_link( $this->content_data->get_content_id(), '&' );
	}

	/**
	 * Returns the task's call to action entry.
	 *
	 * @return Call_To_Action_Entry|null
	 */
	public function get_call_to_action(): ?Call_To_Action_Entry {
		return new Call_To_Action_Entry(
			\__( 'Open editor', 'wordpress-seo-premium' ),
			'link',
			$this->get_link(),
		);
	}

	/**
	 * Returns the task's copy set.
	 *
	 * @return Copy_Set
	 */
	public function get_copy_set(): Copy_Set {
		return new Copy_Set(
			\html_entity_decode( $this->content_data->get_title(), \ENT_QUOTES, 'UTF-8' ),
			$this->parent_task->get_copy_set()->get_about(),
		);
	}

	/**
	 * Returns the task's badge.
	 *
	 * @return string|null
	 */
	public function get_badge(): ?string {
		return 'premium';
	}
}
