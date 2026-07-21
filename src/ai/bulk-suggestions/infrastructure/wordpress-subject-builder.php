<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Infrastructure;

use WP_User;
use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Application\Subject_Builder_Interface;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain\Subject_Build_Exception;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Builds a bulk suggestions subject for a post, based on its indexable.
 */
class WordPress_Subject_Builder implements Subject_Builder_Interface {

	/**
	 * The maximum number of tokens sent as the subject content, mirroring the 300 token cap of the
	 * client-side prompt content in the single suggestion flow. A token is a word, a word separator
	 * (whitespace, dash, bracket) or a punctuation mark, counted the same way as the client.
	 */
	private const MAX_CONTENT_TOKENS = 300;

	/**
	 * The maximum number of characters sent as the subject content for Japanese.
	 * The word-separator tokenizer used for MAX_CONTENT_TOKENS does not split Japanese text into tokens:
	 * the client segments them with a dedicated tokenizer (TinySegmenter) that cannot be replicated server-side,
	 * so we approximate the same content budget with a character cap instead. The single suggestion flow's 300 token
	 * cap corresponds to roughly 500 Japanese characters, which is also a comparable payload once converted to OpenAI tokens.
	 */
	private const MAX_CONTENT_CHARACTERS_JA = 500;

	/**
	 * The word separators kept as tokens by `truncate_to_tokens`, mirroring the client's default
	 * `splitIntoTokens` tokenizer: whitespace, non-breaking space, en/em-dash, hyphen and brackets.
	 */
	private const WORD_SEPARATORS = '\s\x{00A0}\x{2013}\x{2014}\x{002D}\[\]';

	/**
	 * The word separators for Indonesian: the same set as WORD_SEPARATORS but WITHOUT the hyphen
	 * (\x{002D}). Hyphens are not word boundaries in Indonesian; they most often mark reduplication
	 * (e.g. 'buku' meaning 'book' and 'buku-buku' meaning 'books'), mirroring the client's Indonesian
	 * `splitIntoTokensCustom` tokenizer.
	 */
	private const WORD_SEPARATORS_ID = '\s\x{00A0}\x{2013}\x{2014}\[\]';

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
	 * WordPress_Subject_Builder constructor.
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 * @param Language_Helper      $language_helper      The language helper.
	 */
	public function __construct( Indexable_Repository $indexable_repository, Language_Helper $language_helper ) {
		$this->indexable_repository = $indexable_repository;
		$this->language_helper      = $language_helper;
	}

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
	public function build( WP_User $user, int $post_id, string $type, string $platform ): Subject {
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
		$subject->set_content( $this->get_prompt_content( $post_id ) );
		$subject->set_focus_keyphrase( $focus_keyphrase );
		$subject->set_language( $this->language_helper->get_language() );
		$subject->set_platform( $platform );

		return $subject;
	}

	/**
	 * Extracts the prompt content from the post.
	 *
	 * Server-side equivalent of the client-side prompt content in the single suggestion flow: plain text,
	 * collapsed whitespace, capped in length. Blocks and shortcodes are not rendered on purpose, to avoid
	 * the cost and side effects of running render filters for up to 20 posts in a single request.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return string The prompt content.
	 *
	 * @throws Subject_Build_Exception When the post no longer exists.
	 */
	private function get_prompt_content( int $post_id ): string {
		$post = \get_post( $post_id );
		if ( $post === null ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Error code and static message, returned as JSON, never output.
			throw new Subject_Build_Exception( Subject_Build_Exception::POST_NOT_FOUND, 'The post could not be found' );
		}

		$content = \wp_strip_all_tags( \strip_shortcodes( $post->post_content ) );
		$content = \trim( (string) \preg_replace( '/\s+/', ' ', $content ) );

		// `get_researcher_language()` mirrors the client's tokenizer selection, so we can match its
		// per-language behavior: Japanese is segmented by a client-side tokenizer we cannot replicate,
		// so it uses a character cap; Indonesian uses the same token cap as the default but does not
		// treat hyphens as word boundaries.
		$researcher_language = $this->language_helper->get_researcher_language();
		if ( $researcher_language === 'ja' ) {
			$content = $this->truncate_to_characters( $content, self::MAX_CONTENT_CHARACTERS_JA );
		}
		elseif ( $researcher_language === 'id' ) {
			$content = $this->truncate_to_tokens( $content, self::MAX_CONTENT_TOKENS, self::WORD_SEPARATORS_ID );
		}
		else {
			$content = $this->truncate_to_tokens( $content, self::MAX_CONTENT_TOKENS );
		}

		// The AI service requires a non-empty content; mirror the client-side fallback of a single full stop.
		if ( $content === '' ) {
			return '.';
		}

		return $content;
	}

	/**
	 * Truncates the text to at most the given number of tokens.
	 *
	 * This approximates the client-side tokenizer (`splitIntoTokens`): the text is split on word
	 * separators (whitespace, non-breaking space, en/em-dash, hyphen, brackets) keeping those
	 * separators as tokens, and leading/trailing punctuation is split off words into separate tokens.
	 * It intentionally does not replicate the client's abbreviation list, HTML-entity exceptions or
	 * sentence segmentation, which depend on the analysis engine; the goal is token-count parity so
	 * the bulk payload matches the single suggestion flow, not token-for-token identity.
	 *
	 * The separator set is passed in so language-specific tokenizers can be matched: Indonesian, for
	 * example, uses WORD_SEPARATORS_ID, which excludes the hyphen.
	 *
	 * @param string $text            The text to truncate.
	 * @param int    $max_tokens      The maximum number of tokens to keep.
	 * @param string $word_separators The character class contents (without the enclosing brackets) of
	 *                                the word separators to split on.
	 *
	 * @return string The text truncated to the token limit.
	 */
	private function truncate_to_tokens( string $text, int $max_tokens, string $word_separators = self::WORD_SEPARATORS ): string {
		if ( $text === '' ) {
			return '';
		}

		// Split on word separators while keeping them as tokens, then split punctuation off word edges.
		$raw_tokens = \preg_split( '/([' . $word_separators . '])/u', $text, -1, ( \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY ) );
		$tokens     = [];
		foreach ( $raw_tokens as $raw_token ) {
			foreach ( \preg_split( '/(^\p{P}+|\p{P}+$)/u', $raw_token, -1, ( \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY ) ) as $token ) {
				$tokens[] = $token;
			}
		}

		if ( \count( $tokens ) <= $max_tokens ) {
			return $text;
		}

		return \trim( \implode( '', \array_slice( $tokens, 0, $max_tokens ) ) );
	}

	/**
	 * Truncates the text to at most the given number of characters.
	 *
	 * Used for Japanese, where the word-token tokenizer of `truncate_to_tokens` would often leave the text as a single token
	 * and never truncate. A multibyte-safe character cap approximates the client's content budget instead.
	 * Within that budget the text is cut back to the last sentence boundary, mirroring the client,
	 * which builds prompt content one whole sentence at a time and never ends mid-word or mid-sentence.
	 * The sentence delimiters are those of the analysis engine's Japanese sentence tokenizer. When the
	 * budget contains no delimiter (a single sentence longer than the cap), the hard character cut is used.
	 *
	 * @param string $text           The text to truncate.
	 * @param int    $max_characters The maximum number of characters to keep.
	 *
	 * @return string The text truncated to the character limit.
	 */
	private function truncate_to_characters( string $text, int $max_characters ): string {
		if ( $text === '' ) {
			return '';
		}

		\preg_match( '/^(.{0,' . $max_characters . '})(.{1})?/us', $text, $matches );
		if ( ! isset( $matches[2] ) ) {
			return $text;
		}

		$truncated = $matches[1];

		// Cut back to the last sentence delimiter within the budget. The greedy `.*` matches through the
		// last delimiter, so $matches[0] is the text up to and including it. Falls through when none is found.
		if ( \preg_match( '/^.*[\x{3002}\x{FF61}\x{FF01}\x{203C}\x{FF1F}\x{2047}\x{2048}\x{2049}\x{2026}\x{2025}!?]/us', $truncated, $matches ) === 1 ) {
			$truncated = $matches[0];
		}

		return \trim( $truncated );
	}
}
