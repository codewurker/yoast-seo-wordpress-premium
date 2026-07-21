<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Application;

use WP_User;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject_Build_Exception;

/**
 * Builds a bulk suggestions subject for a post.
 */
interface Subject_Builder_Interface {

	/**
	 * Builds a subject for the given post.
	 *
	 * @param WP_User $user     The user the suggestion is built for.
	 * @param int     $post_id  The post ID.
	 * @param string  $type     The suggestion type.
	 * @param string  $platform The platform the suggestion is intended for.
	 *
	 * @return Subject The subject.
	 *
	 * @throws Subject_Build_Exception When no subject can be built for the post.
	 */
	public function build( WP_User $user, int $post_id, string $type, string $platform ): Subject;
}
