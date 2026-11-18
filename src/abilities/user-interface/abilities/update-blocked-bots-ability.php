<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface\Abilities;

use WP_Error;

/**
 * The ability that blocks or allows bots through the robots.txt file.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Update_Blocked_Bots_Ability extends Abstract_Blocked_Bots_Ability {

	/**
	 * Blocks or allows the given bots and returns the blocking status of all of them.
	 *
	 * @param array<string, array<string, bool>> $input The input holding whether to block each given bot.
	 *
	 * @return array<string, array<string, bool>>|WP_Error The blocking status, or an error when a bot could not be saved.
	 */
	public function execute( array $input ) {
		return $this->bot_blocking_manager->set_status( $input['blocked'] );
	}

	/**
	 * Returns the part of the ability name that follows the category slug.
	 *
	 * @return string The ability slug.
	 */
	protected function get_slug(): string {
		return 'update-blocked-bots';
	}

	/**
	 * Returns the label of the ability.
	 *
	 * @return string The label.
	 */
	protected function get_label(): string {
		return \__( 'Update Blocked Bots', 'wordpress-seo-premium' );
	}

	/**
	 * Returns the description of the ability, without the physical robots.txt file note.
	 *
	 * @return string The description.
	 */
	protected function get_description(): string {
		return \sprintf(
			/* translators: 1: expands to Yoast SEO, 2: expands to robots.txt. */
			\__( 'Block or allow bots by adding or removing %1$s’s ‘disallow’ rules for them in the site’s %2$s file. Only the bots you provide are changed; the others keep their current status. If a bot can’t be saved, an error names it; the bots before it stay saved, and the ones after it are left unchanged.', 'wordpress-seo-premium' ),
			'Yoast SEO',
			'robots.txt',
		);
	}

	/**
	 * Returns whether the ability only reads the blocking status.
	 *
	 * @return bool Whether the ability is read-only.
	 */
	protected function is_readonly(): bool {
		return false;
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Returns the input schema of the ability.
	 *
	 * @return array<string, mixed> The input schema.
	 */
	protected function get_input_schema(): array {
		return [
			'type'                 => 'object',
			'additionalProperties' => false,
			'required'             => [ 'blocked' ],
			'properties'           => [
				'blocked' => [
					'type'                 => 'object',
					'description'          => \sprintf(
						/* translators: 1: expands to true, 2: expands to false. */
						\__( 'The bots to change: %1$s adds a ‘disallow’ rule to prevent crawling by the bot, %2$s removes it. Provide at least one bot.', 'wordpress-seo-premium' ),
						'true',
						'false',
					),
					'additionalProperties' => false,
					'minProperties'        => 1,
					'properties'           => $this->get_bot_properties(),
				],
			],
		];
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
}
