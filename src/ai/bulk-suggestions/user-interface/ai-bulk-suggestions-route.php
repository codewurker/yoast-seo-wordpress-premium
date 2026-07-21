<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI\HTTP_Request\Domain\Exceptions\Remote_Request_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Premium\AI\Bulk_Suggestions\Application\Bulk_Suggestions_Provider;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers the route to fetch AI suggestions for multiple posts at once.
 *
 * phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class AI_Bulk_Suggestions_Route implements Route_Interface {

	/**
	 * The AI route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'ai';

	/**
	 * The bulk suggestions route constant.
	 *
	 * @var string
	 */
	public const AI_BULK_SUGGESTIONS_ROUTE = self::ROUTE_PREFIX . '/bulk_suggestions';

	/**
	 * Instance of the Bulk_Suggestions_Provider.
	 *
	 * @var Bulk_Suggestions_Provider
	 */
	protected $bulk_suggestions_provider;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals(): array {
		return [ AI_Conditional::class ];
	}

	/**
	 * AI_Bulk_Suggestions_Route constructor.
	 *
	 * @param Bulk_Suggestions_Provider $bulk_suggestions_provider The provider to handle the requests to the endpoint.
	 */
	public function __construct( Bulk_Suggestions_Provider $bulk_suggestions_provider ) {
		$this->bulk_suggestions_provider = $bulk_suggestions_provider;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			Main::API_V1_NAMESPACE,
			self::AI_BULK_SUGGESTIONS_ROUTE,
			[
				'methods'             => 'POST',
				'args'                => [
					'subjects' => [
						'required'    => true,
						'type'        => 'array',
						'minItems'    => 1,
						'maxItems'    => 20,
						'description' => 'The subjects to fetch suggestions for.',
						'items'       => [
							'type'       => 'object',
							'required'   => [ 'id', 'type' ],
							'properties' => [
								'id'       => [
									'type'        => 'integer',
									'minimum'     => 1,
									'description' => 'The post ID.',
								],
								'type'     => [
									'type'        => 'string',
									'enum'        => [
										'seo-title',
										'meta-description',
									],
									'description' => 'The type of suggestion requested.',
								],
								'platform' => [
									'type'        => 'string',
									'enum'        => [
										'Google',
										'Facebook',
										'Twitter',
									],
									'description' => 'The platform the suggestion is intended for.',
								],
							],
						],
					],
				],
				'callback'            => [ $this, 'get_bulk_suggestions' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			],
		);
	}

	/**
	 * Runs the callback to fetch bulk suggestions through AI.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the bulk suggestions action.
	 */
	public function get_bulk_suggestions( WP_REST_Request $request ): WP_REST_Response {
		try {
			$user = \wp_get_current_user();
			$data = $this->bulk_suggestions_provider->get_bulk_suggestions( $user, $request->get_param( 'subjects' ) );
		} catch ( Remote_Request_Exception $e ) {
			$message = [
				'message'         => $e->getMessage(),
				'errorIdentifier' => $e->get_error_identifier(),
				'data'            => [ 'status' => $e->getCode() ],
			];
			if ( $e instanceof Payment_Required_Exception ) {
				$message['missingLicenses'] = $e->get_missing_licenses();
			}
			return new WP_REST_Response(
				$message,
				$e->getCode(),
			);
		} catch ( RuntimeException $e ) {
			return new WP_REST_Response( 'Failed to retrieve AI bulk suggestions.', 500 );
		}

		return new WP_REST_Response( $data );
	}

	/**
	 * Checks:
	 * - if the user is logged in
	 * - if the user can manage Yoast SEO options
	 *
	 * The per-post edit_post capability is enforced per subject when building the subjects.
	 *
	 * @return bool Whether the user is logged in and can manage Yoast SEO options.
	 */
	public function check_permissions(): bool {
		$user = \wp_get_current_user();
		if ( $user === null || $user->ID < 1 ) {
			return false;
		}

		return \user_can( $user, 'wpseo_manage_options' );
	}
}
