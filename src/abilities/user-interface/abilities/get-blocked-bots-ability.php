<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface\Abilities;

use stdClass;

/**
 * The ability that reports which bots are blocked through the robots.txt file.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Get_Blocked_Bots_Ability extends Abstract_Blocked_Bots_Ability {

	/**
	 * Returns the blocking status of every bot.
	 *
	 * @return array<string, array<string, bool>> The blocking status.
	 */
	public function execute(): array {
		return $this->bot_blocking_manager->get_status();
	}

	/**
	 * Returns the part of the ability name that follows the category slug.
	 *
	 * @return string The ability slug.
	 */
	protected function get_slug(): string {
		return 'get-blocked-bots';
	}

	/**
	 * Returns the label of the ability.
	 *
	 * @return string The label.
	 */
	protected function get_label(): string {
		return \__( 'Get Blocked Bots', 'wordpress-seo-premium' );
	}

	/**
	 * Returns the description of the ability, without the physical robots.txt file note.
	 *
	 * @return string The description.
	 */
	protected function get_description(): string {
		return \sprintf(
			/* translators: 1: expands to Yoast SEO, 2: expands to robots.txt, 3: expands to a list of bots, e.g. GPTBot and CCBot. */
			\__( 'Get which bots %1$s prevents from crawling the site with ‘disallow’ rules in the %2$s file. Covers %3$s.', 'wordpress-seo-premium' ),
			'Yoast SEO',
			'robots.txt',
			$this->get_user_agent_list(),
		);
	}

	/**
	 * Returns whether the ability only reads the blocking status.
	 *
	 * @return bool Whether the ability is read-only.
	 */
	protected function is_readonly(): bool {
		return true;
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Returns the input schema of the ability.
	 *
	 * @return array<string, mixed> The input schema.
	 */
	protected function get_input_schema(): array {
		// Core rejects any input for an ability without an input schema, and some callers always send `{}`. The
		// default lets a call without input pass validation, and is an object so that it serializes to `{}` where
		// the schema is output as is, like in the REST API.
		return [
			'type'                 => 'object',
			'additionalProperties' => false,
			'default'              => new stdClass(),
		];
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
}
