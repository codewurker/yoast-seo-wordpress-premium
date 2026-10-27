<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface;

use Yoast\WP\SEO\Conditionals\Abilities_API_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Adds the site representation settings of Yoast SEO Premium to the fields the site representation abilities can write.
 */
class Organization_Fields_Integration implements Integration_Interface {

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Abilities_API_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_site_representation_ability_fields', [ $this, 'add_fields' ] );
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Adds the site representation settings of Yoast SEO Premium to the writable fields.
	 *
	 * @param array<string, array<string, mixed>>|mixed $fields The JSON schema of each field, keyed by option name.
	 *
	 * @return array<string, array<string, mixed>>|mixed The fields, with the ones of Yoast SEO Premium added.
	 */
	public function add_fields( $fields ) {
		if ( ! \is_array( $fields ) ) {
			return $fields;
		}

		$fields['org-description']      = [
			'type'        => 'string',
			'maxLength'   => 2000,
			'description' => \__( 'A description of the organization, of at most 2000 characters.', 'wordpress-seo-premium' ),
		];
		$fields['org-email']            = [
			'type'              => 'string',
			'description'       => $this->get_local_seo_field_description(
				\__( 'The email address of the organization.', 'wordpress-seo-premium' ),
				'org-email',
			),
			'validate_callback' => [ $this, 'validate_email' ],
		];
		$fields['org-phone']            = [
			'type'              => 'string',
			'description'       => $this->get_local_seo_field_description(
				\__( 'The phone number of the organization.', 'wordpress-seo-premium' ),
				'org-phone',
			),
			'validate_callback' => [ $this, 'validate_local_seo_field' ],
		];
		$fields['org-legal-name']       = [
			'type'        => 'string',
			'description' => \__( 'The registered legal name of the organization.', 'wordpress-seo-premium' ),
		];
		$fields['org-founding-date']    = [
			'type'              => 'string',
			'pattern'           => '^(\d{4}-\d{2}-\d{2})?$',
			'description'       => \__( 'The date the organization was founded, in the YYYY-MM-DD format.', 'wordpress-seo-premium' ),
			'validate_callback' => [ $this, 'validate_founding_date' ],
		];
		$fields['org-number-employees'] = [
			'type'              => 'string',
			'pattern'           => '^(\d+(-\d+)?)?$',
			'description'       => \__( 'The number of employees of the organization, either as a number, like "25", or as a range, like "11-50".', 'wordpress-seo-premium' ),
			'validate_callback' => [ $this, 'validate_number_of_employees' ],
		];
		$fields['org-vat-id']           = [
			'type'              => 'string',
			'description'       => $this->get_local_seo_field_description(
				\__( 'The VAT ID of the organization.', 'wordpress-seo-premium' ),
				'org-vat-id',
			),
			'validate_callback' => [ $this, 'validate_local_seo_field' ],
		];
		$fields['org-tax-id']           = [
			'type'              => 'string',
			'description'       => $this->get_local_seo_field_description(
				\__( 'The tax ID of the organization.', 'wordpress-seo-premium' ),
				'org-tax-id',
			),
			'validate_callback' => [ $this, 'validate_local_seo_field' ],
		];
		$fields['org-iso']              = [
			'type'        => 'string',
			'description' => \__( 'The ISO 6523 code of the organization.', 'wordpress-seo-premium' ),
		];
		$fields['org-duns']             = [
			'type'        => 'string',
			'description' => \__( 'The DUNS number of the organization.', 'wordpress-seo-premium' ),
		];
		$fields['org-leicode']          = [
			'type'        => 'string',
			'description' => \__( 'The LEI code of the organization.', 'wordpress-seo-premium' ),
		];
		$fields['org-naics']            = [
			'type'        => 'string',
			'description' => \__( 'The NAICS code of the organization.', 'wordpress-seo-premium' ),
		];
		// Validated by the validator it is registered with as an organization social profile, see the Mastodon integration.
		$fields['mastodon_url'] = [
			'type'        => 'string',
			'description' => \__( 'The URL of the Mastodon profile of the organization. Only used when company_or_person is "company". For a person, the Mastodon URL from the user\'s profile is used instead.', 'wordpress-seo-premium' ),
		];

		return $fields;
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint

	/**
	 * Skips saving the fields that Yoast Local SEO provides, as their values would have no effect.
	 *
	 * An empty string is still saved, so a value that was saved before Yoast Local SEO was activated can be cleared.
	 *
	 * @param string $value      The value to save.
	 * @param string $field_name The option name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate_local_seo_field( $value, string $field_name ): ?string {
		if ( $value === '' || ! $this->is_local_seo_active() ) {
			return null;
		}

		return \sprintf(
			/* translators: 1: the name of a setting, 2: Yoast Local SEO, 3: the kind of value, like "email address". */
			\__( 'The %1$s setting was not changed, because %2$s is active and provides the %3$s of the organization.', 'wordpress-seo-premium' ),
			$field_name,
			'Yoast Local SEO',
			$this->get_local_seo_field_label( $field_name ),
		);
	}

	/**
	 * Skips saving an invalid email address, and the email address that Yoast Local SEO provides.
	 *
	 * Not done with the email format of the schema, as that rejects the empty string that clears the setting.
	 *
	 * @param string $value      The value to save.
	 * @param string $field_name The option name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate_email( $value, string $field_name ): ?string {
		if ( $value !== '' && ! \is_email( $value ) ) {
			return \sprintf(
				/* translators: %s expands to the name of a setting. */
				\__( 'The %s setting was not changed, because it is not a valid email address.', 'wordpress-seo-premium' ),
				$field_name,
			);
		}

		return $this->validate_local_seo_field( $value, $field_name );
	}

	/**
	 * Skips saving a founding date that does not exist, like the 31st of February.
	 *
	 * @param string $value      The value to save, which the schema guarantees to be empty or in the YYYY-MM-DD format.
	 * @param string $field_name The option name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate_founding_date( $value, string $field_name ): ?string {
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

	/**
	 * Skips saving a range of employees whose lower bound is above its upper bound.
	 *
	 * @param string $value      The value to save, which the schema guarantees to be empty, a number or a range.
	 * @param string $field_name The option name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate_number_of_employees( $value, string $field_name ): ?string {
		$range = \explode( '-', $value );

		if ( \count( $range ) !== 2 || (int) $range[0] <= (int) $range[1] ) {
			return null;
		}

		return \sprintf(
			/* translators: %s expands to the name of a setting. */
			\__( 'The %s setting was not changed, because the lower bound of the range is above its upper bound.', 'wordpress-seo-premium' ),
			$field_name,
		);
	}

	/**
	 * Returns the description of a field that Yoast Local SEO provides, which tells agents upfront where its value
	 * comes from.
	 *
	 * @param string $description The description of the field.
	 * @param string $field_name  The option name of the field.
	 *
	 * @return string The description.
	 */
	private function get_local_seo_field_description( string $description, string $field_name ): string {
		if ( ! $this->is_local_seo_active() ) {
			return $description;
		}

		return $description . ' ' . \sprintf(
			/* translators: 1: Yoast Local SEO, 2: the kind of value, like "email address". */
			\__( '%1$s is active on this site and provides the %2$s of the organization.', 'wordpress-seo-premium' ),
			'Yoast Local SEO',
			$this->get_local_seo_field_label( $field_name ),
		);
	}

	/**
	 * Returns the kind of value a field that Yoast Local SEO provides holds.
	 *
	 * @param string $field_name The option name of the field.
	 *
	 * @return string The kind of value, like "email address".
	 */
	private function get_local_seo_field_label( string $field_name ): string {
		switch ( $field_name ) {
			case 'org-email':
				return \__( 'email address', 'wordpress-seo-premium' );
			case 'org-phone':
				return \__( 'phone number', 'wordpress-seo-premium' );
			case 'org-vat-id':
				return \__( 'VAT ID', 'wordpress-seo-premium' );
			default:
				return \__( 'tax ID', 'wordpress-seo-premium' );
		}
	}

	/**
	 * Returns whether Yoast Local SEO is active.
	 *
	 * @todo Use Free's Local_SEO_Active_Conditional instead, once a Free release that includes it is required.
	 *
	 * @return bool Whether Yoast Local SEO is active.
	 */
	protected function is_local_seo_active(): bool {
		return \defined( 'WPSEO_LOCAL_FILE' );
	}
}
