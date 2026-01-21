<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Task_List\Application\Tasks;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Route_Helper;
use Yoast\WP\SEO\Task_List\Domain\Components\Call_To_Action_Entry;
use Yoast\WP\SEO\Task_List\Domain\Components\Copy_Set;
use Yoast\WP\SEO\Task_List\Domain\Tasks\Abstract_Post_Type_Task;

/**
 * Represents the task for setting social appearance templates.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Set_Social_Appearance_Templates extends Abstract_Post_Type_Task {

	/**
	 * Holds the id.
	 *
	 * @var string
	 */
	protected $id = 'set-social-appearance-templates';

	/**
	 * Holds the priority.
	 *
	 * @var string
	 */
	protected $priority = 'high';

	/**
	 * Holds the duration.
	 *
	 * @var int
	 */
	protected $duration = 10;

	/**
	 * Holds the post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Holds the options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Holds the route helper.
	 *
	 * @var Route_Helper
	 */
	private $route_helper;

	/**
	 * Constructs the task.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 * @param Route_Helper   $route_helper   The route helper.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Route_Helper $route_helper
	) {
		$this->options_helper = $options_helper;
		$this->route_helper   = $route_helper;
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
		$post_type = \get_post_type_object( $this->get_post_type() );

		// First check if the social title has been customized.
		if ( $this->options_helper->get_title_default( 'social-title-' . $post_type->name ) !== $this->options_helper->get( 'social-title-' . $post_type->name ) ) {
			return true;
		}

		// Then check if the social description has been customized.
		if ( $this->options_helper->get_title_default( 'social-description-' . $post_type->name ) !== $this->options_helper->get( 'social-description-' . $post_type->name ) ) {
			return true;
		}

		// Then check if the social image URL has been customized.
		if ( $this->options_helper->get_title_default( 'social-image-url-' . $post_type->name ) !== $this->options_helper->get( 'social-image-url-' . $post_type->name ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the task's link.
	 *
	 * @return string|null
	 */
	public function get_link(): ?string {
		$post_type = \get_post_type_object( $this->get_post_type() );
		$link      = \sprintf(
			'admin.php?page=wpseo_page_settings#/post-type/%s#button-wpseo_titles-social-image-%s-preview',
			$this->route_helper->get_route( $post_type->name, $post_type->rewrite, $post_type->rest_base ),
			$post_type->name
		);

		return \self_admin_url( $link );
	}

	/**
	 * Returns the task's call to action entry.
	 *
	 * @return string|null
	 */
	public function get_call_to_action(): Call_To_Action_Entry {
		return new Call_To_Action_Entry(
			\__( 'Set social templates', 'wordpress-seo-premium' ),
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
		$post_type = \get_post_type_object( $this->get_post_type() );

		return new Copy_Set(
			/* translators: %1$s expands to the post type label this task is about */
			\sprintf( \__( 'Set social media appearance templates for your content type: %1$s', 'wordpress-seo-premium' ), $post_type->label ),
			\__( 'Unstyled previews and general descriptions can lower engagement when shared on social media. Templates keep content on brand and share-ready automatically.', 'wordpress-seo-premium' ),
			/* translators: %1$s expands to the post type label this task is about */
			\sprintf( \__( 'Go to Settings and choose %1$s under Content types. Then use the Social media appearance section to set your default image, title, and description.', 'wordpress-seo-premium' ), $post_type->label )
		);
	}
}
