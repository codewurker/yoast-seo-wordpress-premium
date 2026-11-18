<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.

namespace Yoast\WP\SEO\Premium\Abilities\User_Interface;

use Yoast\WP\SEO\Abilities\Domain\Ability_Interface;
use Yoast\WP\SEO\Conditionals\Abilities_API_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Integration that registers Yoast SEO Premium abilities with the WordPress Abilities API.
 *
 * The category is registered by the free plugin, which Premium requires and which is gated on the same conditional.
 */
class Abilities_Integration implements Integration_Interface {

	/**
	 * The abilities to register.
	 *
	 * @var Ability_Interface[]
	 */
	private $abilities;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Abilities_API_Conditional::class ];
	}

	/**
	 * Constructor.
	 *
	 * @param Ability_Interface ...$abilities The abilities to register.
	 */
	public function __construct( Ability_Interface ...$abilities ) {
		$this->abilities = $abilities;
	}

	/**
	 * Registers hooks with WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	/**
	 * Registers the available Yoast SEO Premium abilities.
	 *
	 * @return void
	 */
	public function register_abilities() {
		foreach ( $this->abilities as $ability ) {
			if ( ! $ability->is_available() ) {
				continue;
			}

			\wp_register_ability( $ability->get_name(), $ability->get_args() );
		}
	}
}
