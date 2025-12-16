<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Task_List\Application\Tasks;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Task_List\Domain\Components\Call_To_Action_Entry;
use Yoast\WP\SEO\Task_List\Domain\Components\Copy_Set;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Abstract_Task;

/**
 * Represents the task for completing the cornerstone content workout.
 */
class Cornerstone_Workout extends Abstract_Task {

	/**
	 * Holds the id.
	 *
	 * @var string
	 */
	protected $id = 'cornerstone-workout';

	/**
	 * Holds the priority.
	 *
	 * @var string
	 */
	protected $priority = 'medium';

	/**
	 * Holds the duration.
	 *
	 * @var int
	 */
	protected $duration = 30;

	/**
	 * Holds the options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructs the task.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
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
	 * Returns whether this task is completed.
	 *
	 * @return bool Whether this task is completed.
	 */
	public function get_is_completed(): bool {
		$workouts_data = $this->options_helper->get( 'workouts', [] );

		if ( ! isset( $workouts_data['cornerstone'], $workouts_data['cornerstone']['finishedSteps'] ) || ! \is_array( $workouts_data['cornerstone'] ) ) {
			return false;
		}

		return \count( $workouts_data['cornerstone']['finishedSteps'] ) === 3;
	}

	/**
	 * Returns the task's link.
	 *
	 * @return string|null
	 */
	public function get_link(): ?string {
		return \self_admin_url( 'admin.php?page=wpseo_workouts#cornerstone' );
	}

	/**
	 * Returns the task's call to action entry.
	 *
	 * @return string|null
	 */
	public function get_call_to_action(): Call_To_Action_Entry {
		return new Call_To_Action_Entry(
			\__( 'Start the workout', 'wordpress-seo-premium' ),
			'link',
			$this->get_link()
		);
	}

	/**
	 * Returns the task's copy set.
	 *
	 * @return string|null
	 */
	public function get_copy_set(): Copy_Set {
		return new Copy_Set(
			\__( 'Do the Cornerstone Approach workout', 'wordpress-seo-premium' ),
			\__( 'When important pages aren’t well-linked, search engines can’t tell they matter most. Strengthening cornerstone content helps your main topics rank better.', 'wordpress-seo-premium' )
		);
	}
}
