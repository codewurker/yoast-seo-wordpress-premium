<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\Infrastructure;

/**
 * Validates the date fields the abilities can write beyond what their JSON schema covers.
 */
class Date_Field_Validator {

	/**
	 * Skips saving a date that does not exist, like the 31st of February.
	 *
	 * @param string $value      The value to save, which the schema guarantees to be empty or in the YYYY-MM-DD format.
	 * @param string $field_name The name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate( $value, string $field_name ): ?string {
		if ( $value === '' ) {
			return null;
		}

		[ $year, $month, $day ] = \explode( '-', $value );

		if ( \checkdate( (int) $month, (int) $day, (int) $year ) ) {
			return null;
		}

		return \sprintf(
			/* translators: %s expands to the name of a setting. */
			\__( 'The %s setting was not changed, because it is not an existing date.', 'wordpress-seo-premium' ),
			$field_name,
		);
	}
}
