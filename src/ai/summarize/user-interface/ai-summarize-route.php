<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Summarize\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Remote_Request_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Premium\AI\Summarize\Application\Summarizer;
use Yoast\WP\SEO\Premium\Conditionals\AI_Summarize_Support_Conditional;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers the route for the AI_Summarizer integration.
 */
class AI_Summarize_Route implements Route_Interface {

	/**
	 * The AI_Summarizer route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'ai';

	/**
	 * The summarize route constant.
	 *
	 * @var string
	 */
	public const AI_SUMMARIZE_ROUTE = self::ROUTE_PREFIX . '/summarize';

	/**
	 * Instance of the Summarizer.
	 *
	 * @var Summarizer
	 */
	protected $summarizer;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals(): array {
		return [ AI_Conditional::class, AI_Summarize_Support_Conditional::class ];
	}

	/**
	 * AI_Summarize_Route constructor.
	 *
	 * @param Summarizer $summarizer The action to handle the requests to the endpoint.
	 */
	public function __construct( Summarizer $summarizer ) {
		$this->summarizer = $summarizer;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			Main::API_V1_NAMESPACE,
			self::AI_SUMMARIZE_ROUTE,
			[
				'methods'             => 'POST',
				'args'                => [
					'language'        => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The language the post is written in.',
					],
					'prompt_content'  => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The content needed by the prompt to ask for a summary.',
					],
					'focus_keyphrase' => [
						'required'    => false,
						'type'        => 'string',
						'description' => 'The focus keyphrase associated to the post.',
					],
				],
				'callback'            => [ $this, 'summarize' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback to summary results through AI.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the summary action.
	 */
	public function summarize( WP_REST_Request $request ): WP_REST_Response {
		try {
			$user = \wp_get_current_user();
			$data = $this->summarizer->summarize( $user, $request['language'], $request['prompt_content'], $request['focus_keyphrase'] );
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
