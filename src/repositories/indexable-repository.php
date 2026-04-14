<?php

namespace Yoast\WP\SEO\Premium\Repositories;

use Yoast\WP\Lib\Model;
use Yoast\WP\Lib\ORM;

/**
 * Repository for querying indexables with premium-specific methods.
 */
class Indexable_Repository {

	/**
	 * Starts a query for this repository.
	 *
	 * @return ORM
	 */
	public function query() {
		return Model::of_type( 'Indexable' );
	}

	/**
	 * Returns the most recently modified posts with their OpenGraph image for a post type.
	 *
	 * @param string      $post_type  The post type.
	 * @param int|null    $limit      The maximum number of posts to return.
	 * @param string|null $date_limit Only include content modified after this date.
	 *
	 * @return array<array<string, string>>|false The array of indexable columns. False if the query failed.
	 */
	public function get_recent_posts_with_og_image_for_post_type( string $post_type, ?int $limit = null, ?string $date_limit = null ) {
		$query = $this->query()
			->select( 'object_id' )
			->select( 'open_graph_image_source' )
			->select( 'breadcrumb_title' )
			->where( 'object_type', 'post' )
			->where( 'object_sub_type', $post_type )
			->where_raw( "( post_status = 'publish' OR post_status IS NULL )" )
			->where_raw( '( is_robots_noindex IS NULL OR is_robots_noindex <> 1 )' )
			->order_by_desc( 'object_last_modified' );

		if ( $limit !== null ) {
			$query->limit( $limit );
		}

		if ( $date_limit !== null ) {
			$query->where_gte( 'object_last_modified', $date_limit );
		}

		return $query->find_array();
	}
}
