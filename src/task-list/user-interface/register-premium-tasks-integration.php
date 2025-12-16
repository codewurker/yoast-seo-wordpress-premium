<?php

namespace Yoast\WP\SEO\Premium\Task_List\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Task_List\Application\Tasks_Collector;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Post_Type_Task_Interface;

/**
 * Handles registering and saving additional contactmethods for users.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Register_Premium_Tasks_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * Holds the post type tasks.
	 *
	 * @var Post_Type_Task_Interface
	 */
	private $post_type_tasks;

	/**
	 * The task collector.
	 *
	 * @var Tasks_Collector $task_collector The Tasks Collector.
	 */
	private $task_collector;

	/**
	 * The constructor.
	 *
	 * @param Tasks_Collector          $task_collector     The Tasks Collector.
	 * @param Post_Type_Task_Interface ...$post_type_tasks The post type tasks.
	 */
	public function __construct(
		Tasks_Collector $task_collector,
		Post_Type_Task_Interface ...$post_type_tasks
	) {
		$this->task_collector  = $task_collector;
		$this->post_type_tasks = $post_type_tasks;
	}

	/**
	 * Registers action hook.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_filter( 'wpseo_task_list_tasks', [ $this, 'register_tasks' ] );
		\add_filter( 'wpseo_task_list_post_type_tasks', [ $this, 'register_post_type_tasks' ] );
	}

	/**
	 * Adds the Premium tasks in the task collector.
	 *
	 * @param array<string, array<string, Task_Interface>> $existing_tasks Currently set tasks.
	 *
	 * @return array<string, array<string, Task_Interface>> Tasks with added Premium tasks.
	 */
	public function register_tasks( $existing_tasks ) {
		$premium_tasks = $this->task_collector->get_tasks();

		return \array_merge( $existing_tasks, $premium_tasks );
	}

	/**
	 * Adds the Premium post type tasks.
	 *
	 * @param array<string, array<string, Post_Type_Task_Interface>> $existing_post_type_tasks Currently set post type tasks.
	 *
	 * @return array<string, array<string, Post_Type_Task_Interface>> Tasks with added Premium post type tasks.
	 */
	public function register_post_type_tasks( $existing_post_type_tasks ): array {
		return \array_merge( $this->post_type_tasks, $existing_post_type_tasks );
	}
}
