<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Task_List\Application\Tasks;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Task_List\Domain\Components\Call_To_Action_Entry;
use Yoast\WP\SEO\Task_List\Domain\Components\Copy_Set;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Abstract_Task;

/**
 * Represents the task for filling the organization schema.
 */
class Organization_Schema extends Abstract_Task {

	/**
	 * Holds the id.
	 *
	 * @var string
	 */
	protected $id = 'organization-schema';

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
	protected $duration = 20;

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
		$orgnization_data_fields = [
			'org-description',
			'org-email',
			'org-phone',
			'org-legal-name',
			'org-founding-date',
			'org-number-employees',
		];

		// If either of these fields is filled, we consider the task complete.
		foreach ( $orgnization_data_fields as $field ) {
			$value = $this->options_helper->get( $field, '' );
			if ( $value !== '' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the task's link.
	 *
	 * @return string|null
	 */
	public function get_link(): ?string {
		return \self_admin_url( 'admin.php?page=wpseo_page_settings#/site-representation#input-wpseo_titles-org-description' );
	}

	/**
	 * Returns the task's call to action entry.
	 *
	 * @return string|null
	 */
	public function get_call_to_action(): Call_To_Action_Entry {
		return new Call_To_Action_Entry(
			\__( 'Take me there', 'wordpress-seo-premium' ),
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
			\__( 'Fill in Organization Schema', 'wordpress-seo-premium' ),
			\__( 'Missing organization details make it harder for search engines to recognize your brand. Completing this helps your business appear correctly in search results.', 'wordpress-seo-premium' ),
			\__( 'Go to Site representation, open Additional organization info, and fill in the information.', 'wordpress-seo-premium' )
		);
	}
}
