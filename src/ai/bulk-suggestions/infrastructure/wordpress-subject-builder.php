<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Infrastructure;

use WP_User;
use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Application\Subject_Builder_Interface;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Minimal_Content_Policy;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject_Build_Exception;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Builds a bulk suggestions subject for a post, based on its indexable.
 */
class WordPress_Subject_Builder implements Subject_Builder_Interface {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The language helper.
	 *
	 * @var Language_Helper
	 */
	private $language_helper;

	/**
	 * The policy that decides whether a post has minimal content.
	 *
	 * @var Minimal_Content_Policy
	 */
	private $minimal_content_policy;

	/**
	 * WordPress_Subject_Builder constructor.
	 *
	 * @param Indexable_Repository   $indexable_repository   The indexable repository.
	 * @param Language_Helper        $language_helper        The language helper.
	 * @param Minimal_Content_Policy $minimal_content_policy The policy that decides whether a post has minimal content.
	 */
	public function __construct(
		Indexable_Repository $indexable_repository,
		Language_Helper $language_helper,
		Minimal_Content_Policy $minimal_content_policy
	) {
		$this->indexable_repository   = $indexable_repository;
		$this->language_helper        = $language_helper;
		$this->minimal_content_policy = $minimal_content_policy;
	}

	/**
	 * Builds a subject for the given post.
	 *
	 * The prompt content is supplied by the client, which collects it through the analysis engine
	 * (`yoastseo`) exactly as the single suggestion flow does; see {@see get_prompt_content()}.
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
	): Subject {
		$indexable = $this->indexable_repository->find_by_id_and_type( $post_id, 'post' );
		// Skip posts without an indexable or that are trashed.
		if ( ! $indexable || $indexable->post_status === 'trash' ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Error code and static message, returned as JSON, never output.
			throw new Subject_Build_Exception( Subject_Build_Exception::POST_NOT_FOUND, 'The post could not be found' );
		}

		if ( ! \user_can( $user, 'edit_post', $post_id ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Error code and static message, returned as JSON, never output.
			throw new Subject_Build_Exception( Subject_Build_Exception::NOT_ALLOWED_TO_EDIT, 'You are not allowed to edit this post' );
		}

		$focus_keyphrase = \trim( (string) $indexable->primary_focus_keyword );
		if ( $focus_keyphrase === '' ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Error code and static message, returned as JSON, never output.
			throw new Subject_Build_Exception( Subject_Build_Exception::MISSING_FOCUS_KEYPHRASE, 'The post has no focus keyphrase' );
		}

		$subject = new Subject();
		$subject->set_id( (string) $post_id );
		$subject->set_type( $type );
		$subject->set_content( $this->get_prompt_content( $content ) );
		$subject->set_focus_keyphrase( $focus_keyphrase );
		$subject->set_language( $this->language_helper->get_language() );
		$subject->set_platform( $platform );

		// The length is measured by the client, on the same text the prompt content is built from, so this flag and
		// the prompt agree about what the AI received. Measuring it here instead would strip the markup differently
		// -- `strip_shortcodes()` deletes the text an enclosing shortcode wraps, which the client's parser keeps --
		// and warn about posts that in fact sent plenty of content. A null length means the client could not measure
		// it, so the length is left unjudged rather than reported as minimal.
		if ( $content_length !== null ) {
			$subject->set_has_minimal_content(
				$this->minimal_content_policy->is_minimal_content(
					$content_length,
					(string) $indexable->object_sub_type,
				),
			);
		}

		return $subject;
	}

	/**
	 * Normalizes the prompt content supplied by the client.
	 *
	 * The content is collected client-side by the analysis engine (`yoastseo`), so the bulk flow sends the
	 * same prompt content the single suggestion flow would send for the same post: whole sentences only, cut
	 * to the per-content-type token budget by the one tokenizer, including the language-specific tokenizers
	 * (TinySegmenter for Japanese, the hyphen-preserving variant for Indonesian) that cannot be replicated here.
	 *
	 * @param string $content The prompt content supplied by the client.
	 *
	 * @return string The prompt content.
	 */
	private function get_prompt_content( string $content ): string {
		$content = \trim( $content );

		// The AI service requires a non-empty content; mirror the client-side fallback of a single full stop.
		if ( $content === '' ) {
			return '.';
		}

		return $content;
	}
}
