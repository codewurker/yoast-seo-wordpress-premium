<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface;

use Yoast\WP\SEO\Conditionals\Abilities_API_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Abilities\Infrastructure\Date_Field_Validator;
use Yoast\WP\SEO\Premium\Abilities\Infrastructure\User_Schema_Repository;

/**
 * Adds the author schema settings of Yoast SEO Premium to the fields the author schema abilities can write.
 */
class Author_Fields_Integration implements Integration_Interface {

	/**
	 * The repository of the user schema settings.
	 *
	 * @var User_Schema_Repository
	 */
	private $user_schema_repository;

	/**
	 * The validator of the date fields.
	 *
	 * @var Date_Field_Validator
	 */
	private $date_field_validator;

	/**
	 * Constructor.
	 *
	 * @param User_Schema_Repository $user_schema_repository The repository of the user schema settings.
	 * @param Date_Field_Validator   $date_field_validator   The validator of the date fields.
	 */
	public function __construct( User_Schema_Repository $user_schema_repository, Date_Field_Validator $date_field_validator ) {
		$this->user_schema_repository = $user_schema_repository;
		$this->date_field_validator   = $date_field_validator;
	}

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
		\add_filter( 'wpseo_author_schema_ability_fields', [ $this, 'add_fields' ] );
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Adds the author schema settings of Yoast SEO Premium to the writable fields.
	 *
	 * @param array<string, array<string, mixed>>|mixed $fields The JSON schema of each field, keyed by field name.
	 *
	 * @return array<string, array<string, mixed>>|mixed The fields, with the ones of Yoast SEO Premium added.
	 */
	public function add_fields( $fields ) {
		if ( ! \is_array( $fields ) ) {
			return $fields;
		}

		$user_schema_fields = [
			'honorificPrefix' => [
				'type'        => 'string',
				'description' => \__( 'The honorific prefix of the user, like "Dr", "Ms" or "Mr".', 'wordpress-seo-premium' ),
			],
			'honorificSuffix' => [
				'type'        => 'string',
				'description' => \__( 'The honorific suffix of the user, like "MD" or "PhD".', 'wordpress-seo-premium' ),
			],
			'birthDate'       => [
				'type'              => 'string',
				'pattern'           => '^(\d{4}-\d{2}-\d{2})?$',
				'description'       => \__( 'The birth date of the user, in the YYYY-MM-DD format.', 'wordpress-seo-premium' ),
				'validate_callback' => [ $this->date_field_validator, 'validate' ],
			],
			'gender'          => [
				'type'        => 'string',
				'description' => \__( 'The gender of the user, like "female", "male" or "non-binary".', 'wordpress-seo-premium' ),
			],
			'award'           => [
				'type'              => 'array',
				'items'             => [ 'type' => 'string' ],
				'description'       => \__( 'The awards the user received, like "Most likely to succeed - 1991". A provided list replaces the current one; an empty list clears it. Items cannot contain commas.', 'wordpress-seo-premium' ),
				'validate_callback' => [ $this, 'validate_list' ],
			],
			'knowsAbout'      => [
				'type'              => 'array',
				'items'             => [ 'type' => 'string' ],
				'description'       => \__( 'The topics the user has expertise in, like "PHP" or "90\'s rock music". A provided list replaces the current one; an empty list clears it. Items cannot contain commas.', 'wordpress-seo-premium' ),
				'validate_callback' => [ $this, 'validate_list' ],
			],
			'knowsLanguage'   => [
				'type'              => 'array',
				'items'             => [ 'type' => 'string' ],
				'description'       => \__( 'The languages the user speaks, like "English" or "Dutch". A provided list replaces the current one; an empty list clears it. Items cannot contain commas.', 'wordpress-seo-premium' ),
				'validate_callback' => [ $this, 'validate_list' ],
			],
			'jobTitle'        => [
				'type'        => 'string',
				'description' => \__( 'The job title of the user, like "software engineer".', 'wordpress-seo-premium' ),
			],
			'worksFor'        => [
				'type'        => 'string',
				'description' => \__( 'The name of the employer of the user, like "Acme inc".', 'wordpress-seo-premium' ),
			],
		];

		foreach ( $user_schema_fields as $field_name => $schema ) {
			$schema['repository']  = $this->user_schema_repository;
			$fields[ $field_name ] = $schema;
		}

		// Validated by the validator it is registered with as a person social profile, see the Mastodon integration.
		$fields['mastodon'] = [
			'type'        => 'string',
			'description' => \__( 'The URL of the user\'s Mastodon profile.', 'wordpress-seo-premium' ),
		];

		return $fields;
	}

	/**
	 * Skips saving a value that is not a list, or a list with an item that contains a comma.
	 *
	 * The schema accepts a comma-separated string as a list, which reaches this point unconverted when the ability is
	 * executed directly instead of over REST. And the user profile page edits a list as comma-separated text, so its
	 * next save would split an item with a comma.
	 *
	 * @param array<string>|mixed $value      The value to save.
	 * @param string              $field_name The name of the field.
	 *
	 * @return string|null A warning when the value cannot be saved, null otherwise.
	 */
	public function validate_list( $value, string $field_name ): ?string {
		if ( ! \is_array( $value ) ) {
			return \sprintf(
				/* translators: %s expands to the name of a setting. */
				\__( 'The %s setting was not changed, because it is not a list.', 'wordpress-seo-premium' ),
				$field_name,
			);
		}

		foreach ( $value as $item ) {
			if ( \strpos( $item, ',' ) !== false ) {
				return \sprintf(
					/* translators: %s expands to the name of a setting. */
					\__( 'The %s setting was not changed, because its items cannot contain commas.', 'wordpress-seo-premium' ),
					$field_name,
				);
			}
		}

		return null;
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
}
