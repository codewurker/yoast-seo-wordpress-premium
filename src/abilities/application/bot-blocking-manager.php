<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\Application;

use WP_Error;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Premium\Abilities\Infrastructure\Blockable_Bot_Map;

/**
 * Application service that reads and writes which bots are blocked through the robots.txt file.
 */
class Bot_Blocking_Manager {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The blockable bot map.
	 *
	 * @var Blockable_Bot_Map
	 */
	private $bot_map;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper    $options_helper The options helper.
	 * @param Blockable_Bot_Map $bot_map        The blockable bot map.
	 */
	public function __construct( Options_Helper $options_helper, Blockable_Bot_Map $bot_map ) {
		$this->options_helper = $options_helper;
		$this->bot_map        = $bot_map;
	}

	/**
	 * Returns whether each bot is blocked.
	 *
	 * @return array<string, array<string, bool>> The blocking status.
	 */
	public function get_status(): array {
		$blocked = [];
		foreach ( $this->bot_map->get_bots() as $bot => $bot_data ) {
			$blocked[ $bot ] = ( $this->options_helper->get( $bot_data['option_name'], false ) === true );
		}

		return [ 'blocked' => $blocked ];
	}

	/**
	 * Blocks or allows the given bots and returns the blocking status of all of them.
	 *
	 * The bots are saved one by one, so the ones before a failing bot remain saved.
	 *
	 * @param array<string, bool|int|string> $blocked Whether to block each bot, keyed by the bot.
	 *
	 * @return array<string, array<string, bool>>|WP_Error The blocking status, or an error when a bot could not be saved.
	 */
	public function set_status( array $blocked ) {
		$bots = $this->bot_map->get_bots();
		foreach ( $blocked as $bot => $is_blocked ) {
			if ( $this->options_helper->set( $bots[ $bot ]['option_name'], \rest_sanitize_boolean( $is_blocked ) ) !== true ) {
				return new WP_Error(
					'yoast_seo_bot_blocking_not_saved',
					\sprintf(
						/* translators: 1: the key of the bot, e.g. gptbot, 2: the name of an ability. */
						\__( 'The blocking status of the %1$s bot couldn’t be saved. The bots before it were saved and the ones after it were left unchanged; check the current status of every bot with the %2$s ability before retrying.', 'wordpress-seo-premium' ),
						$bot,
						'yoast-seo/get-blocked-bots',
					),
					[ 'status' => 500 ],
				);
			}
		}

		return $this->get_status();
	}
}
