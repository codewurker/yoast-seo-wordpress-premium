<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Remote_Request_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Optimizer;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers the route for the AI_Optimizer integration.
 */
class AI_Optimize_Route implements Route_Interface {

	/**
	 * The AI_Optimizer route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'ai';

	/**
	 * The optimize route constant.
	 *
	 * @var string
	 */
	public const AI_OPTIMIZE_ROUTE = self::ROUTE_PREFIX . '/optimize';

	/**
	 * Instance of the Optimizer.
	 *
	 * @var Optimizer
	 */
	protected $optimizer;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals(): array {
		return [ AI_Conditional::class ];
	}

	/**
	 * AI_Generator_Route constructor.
	 *
	 * @param Optimizer $optimizer The action to handle the requests to the endpoint.
	 */
	public function __construct( Optimizer $optimizer ) {
		$this->optimizer = $optimizer;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			Main::API_V1_NAMESPACE,
			self::AI_OPTIMIZE_ROUTE,
			[
				'methods'             => 'POST',
				'args'                => [
					'assessment'      => [
						'required'    => true,
						'type'        => 'string',
						'enum'        => [
							'seo-keyphrase-introduction',
							'seo-keyphrase-density',
							'seo-keyphrase-distribution',
							'seo-keyphrase-subheadings',
							'read-sentence-length',
							'read-paragraph-length',
						],
						'description' => 'The assessment.',
					],
					'language'        => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The language the post is written in.',
					],
					'prompt_content'  => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The content needed by the prompt to ask for suggestions.',
					],
					'focus_keyphrase' => [
						'required'    => false,
						'type'        => 'string',
						'description' => 'The focus keyphrase associated to the post.',
					],
					'synonyms' => [
						'required'    => false,
						'type'        => 'string',
						'description' => 'The synonyms for the focus keyphrase.',
					],
					'editor' => [
						'required'    => true,
						'type'        => 'string',
						'enum'        => [ 'Classic', 'Gutenberg' ],
						'description' => 'The editor type, either Classic or Gutenberg.',
					],
				],
				'callback'            => [ $this, 'optimize' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback to improve assessment results through AI.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the assess action.
	 */
	public function optimize( WP_REST_Request $request ): WP_REST_Response {
		try {
			$user = \wp_get_current_user();
			$data = $this->optimizer->optimize( $user, $request['assessment'], $request['language'], $request['prompt_content'], $request['focus_keyphrase'], $request['synonyms'], $request['editor'] );
		} catch ( Remote_Request_Exception $e ) {
			$message = [
				'message'         => $e->getMessage(),
				'errorIdentifier' => $e->get_error_identifier(),
			];
			if ( $e instanceof Payment_Required_Exception ) {
				$message['missingLicenses'] = $e->get_missing_licenses();
			}
			return new WP_REST_Response(
				$message,
				$e->getCode()
			);
		} catch ( RuntimeException $e ) {
			return new WP_REST_Response( 'Failed to retrieve text improvements.', 500 );
		}

		return new WP_REST_Response( $data );
	}

	/**
	 * Checks:
	 * - if the user is logged
	 * - if the user can edit posts
	 *
	 * @return bool Whether the user is logged in and can edit posts.
	 */
	public function check_permissions(): bool {
		$user = \wp_get_current_user();
		if ( $user === null || $user->ID < 1 ) {
			return false;
		}

		return \user_can( $user, 'edit_posts' );
	}
}
