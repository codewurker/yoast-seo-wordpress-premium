<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\User_Interface\Abilities;

use Yoast\WP\SEO\Abilities\Domain\Ability_Interface;
use Yoast\WP\SEO\Abilities\User_Interface\Ability_Categories_Integration;
use Yoast\WP\SEO\Conditionals\Non_Multisite_Conditional;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Premium\Abilities\Application\Bot_Blocking_Manager;
use Yoast\WP\SEO\Premium\Abilities\Infrastructure\Blockable_Bot_Map;

/**
 * Base class for the abilities that read or write which bots are blocked through the robots.txt file.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
abstract class Abstract_Blocked_Bots_Ability implements Ability_Interface {

	/**
	 * The capability helper.
	 *
	 * @var Capability_Helper
	 */
	private $capability_helper;

	/**
	 * The non-multisite conditional.
	 *
	 * @var Non_Multisite_Conditional
	 */
	private $non_multisite_conditional;

	/**
	 * The bot blocking manager.
	 *
	 * @var Bot_Blocking_Manager
	 */
	protected $bot_blocking_manager;

	/**
	 * The blockable bot map.
	 *
	 * @var Blockable_Bot_Map
	 */
	private $bot_map;

	/**
	 * Constructor.
	 *
	 * @param Capability_Helper         $capability_helper         The capability helper.
	 * @param Non_Multisite_Conditional $non_multisite_conditional The non-multisite conditional.
	 * @param Bot_Blocking_Manager      $bot_blocking_manager      The bot blocking manager.
	 * @param Blockable_Bot_Map         $bot_map                   The blockable bot map.
	 */
	public function __construct(
		Capability_Helper $capability_helper,
		Non_Multisite_Conditional $non_multisite_conditional,
		Bot_Blocking_Manager $bot_blocking_manager,
		Blockable_Bot_Map $bot_map
	) {
		$this->capability_helper         = $capability_helper;
		$this->non_multisite_conditional = $non_multisite_conditional;
		$this->bot_blocking_manager      = $bot_blocking_manager;
		$this->bot_map                   = $bot_map;
	}

	/**
	 * Returns the part of the ability name that follows the category slug.
	 *
	 * @return string The ability slug.
	 */
	abstract protected function get_slug(): string;

	/**
	 * Returns the label of the ability.
	 *
	 * @return string The label.
	 */
	abstract protected function get_label(): string;

	/**
	 * Returns the description of the ability, without the physical robots.txt file note.
	 *
	 * @return string The description.
	 */
	abstract protected function get_description(): string;

	/**
	 * Returns whether the ability only reads the blocking status.
	 *
	 * @return bool Whether the ability is read-only.
	 */
	abstract protected function is_readonly(): bool;

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Returns the input schema of the ability.
	 *
	 * @return array<string, mixed> The input schema.
	 */
	abstract protected function get_input_schema(): array;

	/**
	 * Returns the arguments to register the ability with.
	 *
	 * @return array<string, mixed> The registration arguments.
	 */
	public function get_args(): array {
		return [
			'label'               => $this->get_label(),
			'description'         => $this->get_description() . ' ' . $this->get_physical_file_note(),
			'category'            => Ability_Categories_Integration::CATEGORY_SLUG,
			'input_schema'        => $this->get_input_schema(),
			'output_schema'       => $this->get_output_schema(),
			'permission_callback' => [ $this, 'can_manage_seo' ],
			'execute_callback'    => [ $this, 'execute' ],
			'meta'                => [
				'show_in_rest' => true,
				'annotations'  => [
					'readonly'    => $this->is_readonly(),
					'destructive' => ( $this->is_readonly() ) ? false : null,
					'idempotent'  => true,
				],
				'mcp'          => [
					'public' => true,
				],
			],
		];
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint

	/**
	 * Returns the fully qualified ability name.
	 *
	 * @return string The ability name.
	 */
	public function get_name(): string {
		return Ability_Categories_Integration::CATEGORY_SLUG . '/' . $this->get_slug();
	}

	/**
	 * Returns whether the ability is available: the robots.txt rules are not served on multisite
	 * installations, so the ability is only offered outside of them.
	 *
	 * @return bool Whether the ability is available.
	 */
	public function is_available(): bool {
		return $this->non_multisite_conditional->is_met();
	}

	/**
	 * Checks whether the current user can manage the SEO settings.
	 *
	 * @return bool Whether the current user can manage the SEO settings.
	 */
	public function can_manage_seo(): bool {
		return $this->capability_helper->current_user_can( 'wpseo_manage_options' );
	}

	/**
	 * Returns the note that a physical robots.txt file overrides the rules, to append to the ability description.
	 *
	 * @return string The note.
	 */
	private function get_physical_file_note(): string {
		return \sprintf(
			/* translators: 1: expands to Yoast SEO, 2: expands to robots.txt, 3: expands to /robots.txt. */
			\__( 'The rules take effect only when %1$s generates the %2$s file: a physical %2$s file in the site’s root directory overrides them. To confirm the rules are in effect, check the site’s %3$s URL.', 'wordpress-seo-premium' ),
			'Yoast SEO',
			'robots.txt',
			'/robots.txt',
		);
	}

	/**
	 * Returns the robots.txt user agents of the bots as a localized list, e.g. `A, B and C`.
	 *
	 * @return string The list of user agents.
	 */
	protected function get_user_agent_list(): string {
		$user_agents = [];
		foreach ( $this->bot_map->get_bots() as $bot_data ) {
			$user_agents[] = $bot_data['user_agent'];
		}

		return \wp_sprintf( '%l', $user_agents );
	}

	// phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint -- The JSON schema arrays are heterogeneous by nature.

	/**
	 * Returns the boolean schema properties of the bots.
	 *
	 * @return array<string, array<string, string>> The bot properties.
	 */
	protected function get_bot_properties(): array {
		$properties = [];
		foreach ( $this->bot_map->get_bots() as $bot => $bot_data ) {
			$properties[ $bot ] = [
				'type'        => 'boolean',
				'description' => $bot_data['description'],
			];
		}

		return $properties;
	}

	/**
	 * Returns the output schema: the blocking status of every bot.
	 *
	 * @return array<string, mixed> The output schema.
	 */
	private function get_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'blocked' => [
					'type'        => 'object',
					'description' => \sprintf(
						/* translators: %s expands to robots.txt. */
						\__( 'Whether each bot is prevented from crawling by a ‘disallow’ rule in the %s file.', 'wordpress-seo-premium' ),
						'robots.txt',
					),
					'properties'  => $this->get_bot_properties(),
				],
			],
		];
	}

	// phpcs:enable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
}
