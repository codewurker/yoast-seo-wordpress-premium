<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Domain;

/**
 * Represents a single subject to request a bulk suggestion for.
 */
class Subject {

	/**
	 * The identifier of the subject, echoed back by the AI service.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The suggestion type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The content the suggestion should be based on.
	 *
	 * @var string
	 */
	private $content;

	/**
	 * The focus keyphrase associated to the post.
	 *
	 * @var string
	 */
	private $focus_keyphrase;

	/**
	 * The language the post is written in.
	 *
	 * @var string
	 */
	private $language;

	/**
	 * The platform the suggestion is intended for.
	 *
	 * @var string
	 */
	private $platform;

	/**
	 * Sets the identifier of the subject.
	 *
	 * @param string $id The identifier of the subject.
	 *
	 * @return void
	 */
	public function set_id( string $id ): void {
		$this->id = $id;
	}

	/**
	 * Sets the suggestion type.
	 *
	 * @param string $type The suggestion type.
	 *
	 * @return void
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * Sets the content the suggestion should be based on.
	 *
	 * @param string $content The content the suggestion should be based on.
	 *
	 * @return void
	 */
	public function set_content( string $content ): void {
		$this->content = $content;
	}

	/**
	 * Sets the focus keyphrase associated to the post.
	 *
	 * @param string $focus_keyphrase The focus keyphrase associated to the post.
	 *
	 * @return void
	 */
	public function set_focus_keyphrase( string $focus_keyphrase ): void {
		$this->focus_keyphrase = $focus_keyphrase;
	}

	/**
	 * Sets the language the post is written in.
	 *
	 * @param string $language The language the post is written in.
	 *
	 * @return void
	 */
	public function set_language( string $language ): void {
		$this->language = $language;
	}

	/**
	 * Sets the platform the suggestion is intended for.
	 *
	 * @param string $platform The platform the suggestion is intended for.
	 *
	 * @return void
	 */
	public function set_platform( string $platform ): void {
		$this->platform = $platform;
	}

	/**
	 * Returns the subject in the shape expected by the bulk suggestions endpoint of the AI service.
	 *
	 * @return array<string, string> The subject as an array.
	 */
	public function to_array(): array {
		return [
			'id'              => $this->id,
			'type'            => $this->type,
			'content'         => $this->content,
			'focus_keyphrase' => $this->focus_keyphrase,
			'language'        => $this->language,
			'platform'        => $this->platform,
		];
	}
}
