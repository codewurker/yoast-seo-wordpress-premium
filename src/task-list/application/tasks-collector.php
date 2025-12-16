<?php

namespace Yoast\WP\SEO\Premium\Task_List\Application;

use Yoast\WP\SEO\Task_List\Domain\Tasks\Post_Type_Task_Interface;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Task_Interface;

/**
 * Manages the collection of tasks.
 */
class Tasks_Collector {

	/**
	 * Holds all the tasks.
	 *
	 * @var Task_Interface[]
	 */
	private $tasks;

	/**
	 * Constructs the collector.
	 *
	 * @param Task_Interface ...$tasks All the tasks.
	 */
	public function __construct( Task_Interface ...$tasks ) {
		$tasks_with_id = [];
		foreach ( $tasks as $task ) {
			if ( $task instanceof Post_Type_Task_Interface ) {
				continue;
			}
			$tasks_with_id[ $task->get_id() ] = $task;
		}

		$this->tasks = $tasks_with_id;
	}

	/**
	 * Gets the tasks.
	 *
	 * @return array<string, array<string, Task_Interface>> The tasks.
	 */
	public function get_tasks(): array {
		return $this->tasks;
	}
}
