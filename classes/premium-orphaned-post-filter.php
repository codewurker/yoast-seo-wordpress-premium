<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium
 */

use Yoast\WP\SEO\Config\Migration_Status;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Registers the filter for filtering posts by orphaned content.
 */
class WPSEO_Premium_Orphaned_Post_Filter extends WPSEO_Abstract_Post_Filter {

	/**
	 * Returns the query value this filter uses.
	 *
	 * @return string The query value this filter uses.
	 */
	public function get_query_val() {
		return 'orphaned';
	}

	/**
	 * Checks if the orphaned count cache is enabled via filter.
	 *
	 * @return bool
	 */
	private function is_orphaned_count_cache_enabled(): bool {
		/**
		 * Filter: 'wpseo_premium_orphaned_count_cache' - Allows the control the orphaned count cache.
		 *
		 * Note: This is a Premium plugin-only hook.
		 *
		 * @since 26.1
		 *
		 * @param bool $load Whether to have cache for orphaned counts enabled. Default: true.
		*/
		return apply_filters( 'wpseo_premium_orphaned_count_cache', true );
	}

	/**
	 * Registers the hooks when the link feature is enabled.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! YoastSEO()->classes->get( Migration_Status::class )->is_version( 'free', WPSEO_VERSION ) ) {
			return;
		}

		if ( WPSEO_Premium_Orphaned_Content_Utils::is_feature_enabled() ) {
			parent::register_hooks();
		}

		add_action( 'wpseo_related_indexables_incoming_links_updated', [ $this, 'flush_cache_orphaned_counts' ], 10, 1 );
	}

	/**
	 * Returns a text explaining this filter.
	 *
	 * @return string|null The explanation or null if the current post stype is unknown.
	 */
	protected function get_explanation() {
		$post_type_object = get_post_type_object( $this->get_current_post_type() );

		if ( $post_type_object === null ) {
			return null;
		}

		$unprocessed     = WPSEO_Premium_Orphaned_Content_Utils::has_unprocessed_content();
		$can_recalculate = WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' );

		$learn_more = sprintf(
		/* translators: %1$s expands to the link to an article to read more about orphaned content, %2$s expands to </a> */
			__( '%1$sLearn more about orphaned content%2$s.', 'wordpress-seo-premium' ),
			'<a href="' . WPSEO_Shortlinker::get( 'https://yoa.st/1ja' ) . '" target="_blank">',
			'</a>'
		);

		if ( $unprocessed && ! $can_recalculate ) {
			return sprintf(
			/* translators: %1$s: plural form of the current post type, %2$s: a Learn more about link */
				__( 'Ask your SEO Manager or Site Administrator to count links in all texts, so we can identify orphaned %1$s. %2$s', 'wordpress-seo-premium' ),
				strtolower( $post_type_object->labels->name ),
				$learn_more
			);
		}

		if ( $unprocessed ) {
			return sprintf(
			/* translators: %1$s expands to link to the recalculation option, %2$s: anchor closing, %3$s: plural form of the current post type, %4$s: a Learn more about link */
				__( '%1$sClick here%2$s to index your links, so we can identify orphaned %3$s. %4$s', 'wordpress-seo-premium' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&reIndexLinks=1' ) ) . '">',
				'</a>',
				strtolower( $post_type_object->labels->name ),
				$learn_more
			);
		}

		return sprintf(
		/* translators: %1$s: plural form of the current post type, %2$s: a Learn more about link */
			__( '\'Orphaned content\' refers to %1$s that have no inbound links, consider adding links towards these %1$s. %2$s', 'wordpress-seo-premium' ),
			strtolower( $post_type_object->labels->name ),
			$learn_more
		);
	}

	/**
	 * Modifies the query based on the seo_filter variable in $_GET.
	 *
	 * @param string $where Query variables.
	 *
	 * @return string The modified query.
	 */
	public function filter_posts( $where ) {
		if ( $this->is_filter_active() ) {
			$where .= $this->get_where_filter();
			$where .= $this->filter_published_posts();
		}

		return $where;
	}

	/**
	 * Returns the where clause to use.
	 *
	 * @return string The where clause.
	 */
	protected function get_where_filter() {
		global $wpdb;

		if ( WPSEO_Premium_Orphaned_Content_Utils::has_unprocessed_content() ) {
			// Hide all posts, because we cannot tell anything for certain.
			return 'AND 1 = 0';
		}

		$subquery = WPSEO_Premium_Orphaned_Post_Query::get_orphaned_content_query();

		return ' AND ' . $wpdb->posts . '.ID IN ( ' . $subquery . ' ) ';
	}

	/**
	 * Adds a published posts filter so we don't show unpublished posts in the orphaned pages results.
	 *
	 * @return string A published posts filter.
	 */
	protected function filter_published_posts() {
		global $wpdb;

		return " AND {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_password = ''";
	}

	/**
	 * Returns the label for this filter.
	 *
	 * @return string The label for this filter.
	 */
	protected function get_label() {
		static $label;

		if ( $label === null ) {
			$label = __( 'Orphaned content', 'wordpress-seo-premium' );
		}

		return $label;
	}

	/**
	 * Returns the total amount of articles that are orphaned content.
	 *
	 * @return int
	 */
	protected function get_post_total() {
		global $wpdb;
		$post_type = $this->get_current_post_type();
		$cache_key = 'orphaned_count_' . $post_type;
		$count     = false;

		if ( $this->is_orphaned_count_cache_enabled() ) {
			$count = wp_cache_get( $cache_key, 'orphaned_counts' );
		}

		if ( WPSEO_Premium_Orphaned_Content_Utils::has_unprocessed_content() ) {
			return '?';
		}
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Will be supported in the next WPcs version. $subquery is a prepared subquery from get_orphaned_content_query().
		if ( $count === false ) {
			$subquery = WPSEO_Premium_Orphaned_Post_Query::get_orphaned_content_query();
			$count    = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(ID)
						FROM %i
						WHERE ID IN ( $subquery )
							AND %i = 'publish'
							AND %i = ''
							AND %i = %s",
					$wpdb->posts,
					'post_status',
					'post_password',
					'post_type',
					$post_type
				)
			);

			$count = (int) $count;

			if ( $this->is_orphaned_count_cache_enabled() ) {
				$expiry = ( wp_using_ext_object_cache() && wp_cache_supports( 'flush_group' ) ) ? DAY_IN_SECONDS : MINUTE_IN_SECONDS;
				wp_cache_set( $cache_key, $count, 'orphaned_counts', $expiry );
			}
		}

		// phpcs:enable

		return $count;
	}

	/**
	 * Returns the post types to which this filter should be added.
	 *
	 * @return array The post types to which this filter should be added.
	 */
	protected function get_post_types() {
		$orphaned_content_support = new WPSEO_Premium_Orphaned_Content_Support();

		return $orphaned_content_support->get_supported_post_types();
	}

	/**
	 * Flushes the orphaned_counts cache group.
	 *
	 * @param int[] $related_indexable_ids The related indexable Ids to this link change.
	 *
	 * @return void
	 */
	public function flush_cache_orphaned_counts( $related_indexable_ids ) {
		if ( ! $this->is_orphaned_count_cache_enabled() ) {
			return;
		}

		/**
		 * Filter: 'wpseo_premium_orphaned_count_cache_invalidation_method' - Allows the ability to choose the way to invalidate the current cache.
		 *
		 * Note: This is a Premium plugin-only hook.
		 *
		 * @since 26.1
		 *
		 * @param string $method The method to invalidate the current cache, either 'flush' or 'delete'. Default: 'flush'.
		 */
		$method = apply_filters( 'wpseo_premium_orphaned_count_cache_invalidation_method', 'flush' );

		switch ( $method ) {
			case 'flush':
				if ( wp_cache_supports( 'flush_group' ) ) {
					wp_cache_flush_group( 'orphaned_counts' );
				}
				break;
			case 'delete':
				if ( ! is_array( $related_indexable_ids ) || empty( $related_indexable_ids ) ) {
					break;
				}

				$indexables = YoastSEO()->classes->get( Indexable_Repository::class )
					->find_by_ids( $related_indexable_ids );
				$post_types = [];
				foreach ( $indexables as $indexable ) {
					$post_types[ $indexable->object_sub_type ] = $indexable->object_sub_type;
				}
				foreach ( $post_types as $post_type ) {
					wp_cache_delete( 'orphaned_count_' . $post_type, 'orphaned_counts' );
				}

				break;
			default:
				// Do nothing.
				break;
		}
	}
}
