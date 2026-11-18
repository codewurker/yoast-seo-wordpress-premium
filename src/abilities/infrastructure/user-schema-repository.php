<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\Infrastructure;

use Yoast\WP\SEO\Abilities\Domain\Author_Schema_Field_Repository_Interface;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Stores an author schema setting of Yoast SEO Premium as an entry of the user schema user meta value, sanitized
 * like the user profile page does.
 *
 * Saving rewrites the whole user schema user meta value, so parallel saves of different settings of the same user
 * can overwrite each other.
 */
class User_Schema_Repository implements Author_Schema_Field_Repository_Interface {

	/**
	 * The user meta key that holds the user schema settings.
	 *
	 * @var string
	 */
	public const META_KEY = 'wpseo_user_schema';

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Constructor.
	 *
	 * @param User_Helper $user_helper The user helper.
	 */
	public function __construct( User_Helper $user_helper ) {
		$this->user_helper = $user_helper;
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The settings are either strings or lists of strings.

	/**
	 * Returns the current value of a setting of a user.
	 *
	 * @param int    $user_id    The ID of the user.
	 * @param string $field_name The name of the setting, which is its key in the user schema settings.
	 *
	 * @return string|array<string> The current value, or an empty string when the setting is not set.
	 */
	public function get( int $user_id, string $field_name ) {
		return ( $this->get_user_schema( $user_id )[ $field_name ] ?? '' );
	}

	/**
	 * Sanitizes and saves a setting of a user, leaving the other user schema settings untouched.
	 *
	 * Like on the user profile page, an empty value removes the setting.
	 *
	 * @param int    $user_id    The ID of the user.
	 * @param string $field_name The name of the setting, which is its key in the user schema settings.
	 * @param mixed  $value      The value to save: a string, or a list of strings.
	 *
	 * @return bool Whether the value was saved as provided, so false as well when it got sanitized into something else.
	 */
	public function save( int $user_id, string $field_name, $value ): bool {
		$sanitized   = $this->sanitize( $value );
		$user_schema = $this->get_user_schema( $user_id );

		if ( $sanitized === '' || $sanitized === [] ) {
			unset( $user_schema[ $field_name ] );
		}
		else {
			$user_schema[ $field_name ] = $sanitized;
		}

		$this->user_helper->update_meta( $user_id, self::META_KEY, $user_schema );

		// Updating user meta also returns false when the value did not change, so the stored value is checked instead.
		$stored_schema = $this->get_user_schema( $user_id );

		// An empty value removes the setting, so a missing setting means it was saved as provided only when empty.
		if ( ! isset( $stored_schema[ $field_name ] ) ) {
			return $value === '' || $value === [];
		}

		return $stored_schema[ $field_name ] === $value;
	}

	/**
	 * Sanitizes a value like the user profile page does: as text, and without the empty entries of a list.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string|array<string> The sanitized value.
	 */
	private function sanitize( $value ) {
		if ( ! \is_array( $value ) ) {
			return \sanitize_text_field( (string) $value );
		}

		$sanitized = \array_map(
			static function ( $item ) {
				return \sanitize_text_field( (string) $item );
			},
			$value,
		);

		// Reindexed, so the list is output as a list in the schema.
		return \array_values(
			\array_filter(
				$sanitized,
				static function ( $item ) {
					return $item !== '';
				},
			),
		);
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint

	/**
	 * Returns the user schema settings of a user.
	 *
	 * @param int $user_id The ID of the user.
	 *
	 * @return array<string, string|array<string>> The user schema settings.
	 */
	private function get_user_schema( int $user_id ): array {
		$user_schema = $this->user_helper->get_meta( $user_id, self::META_KEY, true );

		return ( \is_array( $user_schema ) ) ? $user_schema : [];
	}
}
