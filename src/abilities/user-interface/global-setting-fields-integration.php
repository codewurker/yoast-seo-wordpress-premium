<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface;

use Yoast\WP\SEO\Abilities\Infrastructure\Global_Setting_Targets\Author_Archives_Target;
use Yoast\WP\SEO\Abilities\Infrastructure\Global_Setting_Targets\Date_Archives_Target;
use Yoast\WP\SEO\Abilities\Infrastructure\Global_Setting_Targets\Homepage_Target;
use Yoast\WP\SEO\Abilities\Infrastructure\Global_Setting_Targets\Post_Type_Archive_Target;
use Yoast\WP\SEO\Conditionals\Abilities_API_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Integrations\OpenGraph_Author_Archive;
use Yoast\WP\SEO\Premium\Integrations\OpenGraph_Date_Archive;
use Yoast\WP\SEO\Premium\Integrations\OpenGraph_PostType_Archive;

/**
 * Adds the social title and description to the archive and special page SEO settings abilities.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Global_Setting_Fields_Integration implements Integration_Interface {

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
		\add_filter( 'wpseo_global_setting_ability_fields', [ $this, 'add_fields' ] );
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Adds the social title and description to the fields of the archive and special page SEO settings abilities.
	 *
	 * @param array<string, array<string, mixed>>|mixed $fields The JSON schema of each field, along with its option
	 *                                                          patterns, keyed by field name.
	 *
	 * @return array<string, array<string, mixed>>|mixed The fields, with the social fields added.
	 */
	public function add_fields( $fields ) {
		if ( ! \is_array( $fields ) ) {
			return $fields;
		}

		$fields['social_title'] = [
			'type'            => 'string',
			'description'     => \sprintf(
				/* translators: %s: the format of a snippet variable. */
				\__( 'The social title template, shown when the page is shared on social media. It can contain snippet variables in the %s format, which are replaced with the values of the page it is shown on. When empty, the SEO title is used.', 'wordpress-seo-premium' ),
				'%%variable%%',
			),
			'option_patterns' => [
				Homepage_Target::NAME          => 'open_graph_frontpage_title',
				Author_Archives_Target::NAME   => OpenGraph_Author_Archive::OPTION_TITLES_KEY_TITLE,
				Date_Archives_Target::NAME     => OpenGraph_Date_Archive::OPTION_TITLES_KEY_TITLE,
				Post_Type_Archive_Target::NAME => OpenGraph_PostType_Archive::OPTION_TITLES_KEY_TITLE . '%s',
			],
		];

		$fields['social_description'] = [
			'type'            => 'string',
			'description'     => \sprintf(
				/* translators: %s: the format of a snippet variable. */
				\__( 'The social description template, shown when the page is shared on social media. It can contain snippet variables in the %s format, which are replaced with the values of the page it is shown on. When empty, the meta description is used.', 'wordpress-seo-premium' ),
				'%%variable%%',
			),
			'option_patterns' => [
				Homepage_Target::NAME          => 'open_graph_frontpage_desc',
				Author_Archives_Target::NAME   => OpenGraph_Author_Archive::OPTION_TITLES_KEY_DESCRIPTION,
				Date_Archives_Target::NAME     => OpenGraph_Date_Archive::OPTION_TITLES_KEY_DESCRIPTION,
				Post_Type_Archive_Target::NAME => OpenGraph_PostType_Archive::OPTION_TITLES_KEY_DESCRIPTION . '%s',
			],
		];

		return $fields;
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
}
