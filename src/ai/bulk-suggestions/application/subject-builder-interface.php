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
	 * @param WP_User  $user           The user the suggestion is built for.
	 * @param int      $post_id        The post ID.
	 * @param string   $type           The suggestion type.
	 * @param string   $platform       The platform the suggestion is intended for.
	 * @param string   $content        The prompt content the suggestion should be based on, collected by the client.
	 * @param int|null $content_length The visible-text length of the post, measured by the client. Null when the
	 *                                 client could not measure it, in which case the length is not judged.
	 *
	 * @return Subject The subject.
	 *
	 * @throws Subject_Build_Exception When no subject can be built for the post.
	 */
	public function build(
		WP_User $user,
		int $post_id,
		string $type,
		string $platform,
		string $content,
		?int $content_length = null
	): Subject;
}
