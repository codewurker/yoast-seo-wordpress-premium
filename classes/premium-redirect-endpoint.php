<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium
 */

/**
 * Registers the endpoint for the redirects to WordPress.
 */
class WPSEO_Premium_Redirect_EndPoint implements WPSEO_WordPress_Integration {

	public const REST_NAMESPACE    = 'yoast/v1';
	public const ENDPOINT_QUERY    = 'redirects';
	public const ENDPOINT_UNDO     = 'redirects/delete';
	public const ENDPOINT_LIST     = 'redirects/list';
	public const ENDPOINT_UPDATE   = 'redirects/update';
	public const ENDPOINT_SETTINGS = 'redirects/settings';

	public const CAPABILITY_STORE = 'wpseo_manage_redirects';

	/**
	 * Instance of the WPSEO_Premium_Redirect_Service class.
	 *
	 * @var WPSEO_Premium_Redirect_Service
	 */
	protected $service;

	/**
	 * Sets the service to handle the request.
	 *
	 * @param WPSEO_Premium_Redirect_Service $service The service to handle the requests to the endpoint.
	 */
	public function __construct( WPSEO_Premium_Redirect_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register' ] );
	}

	/**
	 * Register the REST endpoint to WordPress.
	 *
	 * @return void
	 */
	public function register() {
		$args = [
			'origin' => [
				'required'    => true,
				'type'        => 'string',
				'description' => 'The origin to redirect',
			],
			'target' => [
				'required'    => false,
				'type'        => 'string',
				'description' => 'The redirect target',
			],
			'type' => [
				'required'    => true,
				'type'        => 'integer',
				'description' => 'The redirect type',
			],
			'ignore_warning' => [
				'required'    => false,
				'type'        => 'boolean',
				'description' => 'Whether to ignore warnings',
				'default'     => false,
			],
			'format' => [
				'description' => __( 'The format of the redirect to create.', 'wordpress-seo-premium' ),
				'type'        => 'string',
				'required'    => false,
				'enum'        => [
					WPSEO_Redirect_Formats::PLAIN,
					WPSEO_Redirect_Formats::REGEX,
				],
				'default'     => WPSEO_Redirect_Formats::PLAIN,
			],
		];

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_QUERY,
			[
				'methods'             => 'POST',
				'args'                => $args,
				'callback'            => [
					$this->service,
					'save',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_UNDO,
			[
				'methods'             => 'POST',
				'args'                => array_merge(
					$args,
					[
						'type' => [
							'required'    => false,
							'type'        => 'string',
							'description' => 'The redirect format',
						],
					]
				),
				'callback'            => [
					$this->service,
					'delete',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_LIST,
			[
				'methods'             => 'GET',
				'args'                => [
					'format' => [
						'description'       => __( 'The format of the redirects to retrieve.', 'wordpress-seo-premium' ),
						'type'              => 'string',
						'required'          => false,
						'enum'              => [
							WPSEO_Redirect_Formats::PLAIN,
							WPSEO_Redirect_Formats::REGEX,
						],
						'default'           => WPSEO_Redirect_Formats::PLAIN,
					],
				],
				'callback'            => [
					$this->service,
					'list',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_UPDATE,
			[
				'methods'             => 'PUT',
				'args'                => [
					'old_origin' => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The origin to redirect (old)',
					],
					'old_target' => [
						'required'    => false,
						'type'        => 'string',
						'description' => 'The redirect target (old)',
					],
					'old_type' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => 'The redirect type (old)',
					],
					'new_origin' => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The origin to redirect (new)',
					],
					'new_target' => [
						'required'    => false,
						'type'        => 'string',
						'description' => 'The redirect target (new)',
					],
					'new_type' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => 'The redirect type (new)',
					],
					'ignore_warning' => [
						'required'    => false,
						'type'        => 'boolean',
						'description' => 'Whether to ignore warnings',
						'default'     => false,
					],
					'format' => [
						'description' => __( 'The format of the redirects to retrieve.', 'wordpress-seo-premium' ),
						'type'        => 'string',
						'required'    => false,
						'enum'        => [
							WPSEO_Redirect_Formats::PLAIN,
							WPSEO_Redirect_Formats::REGEX,
						],
						'default'     => WPSEO_Redirect_Formats::PLAIN,
					],
				],
				'callback'            => [
					$this->service,
					'update',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_SETTINGS,
			[
				'methods'             => 'GET',
				'callback'            => [
					$this->service,
					'settings',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::ENDPOINT_SETTINGS,
			[
				'methods'             => 'PUT',
				'args'                => [
					'disable_php_redirect' => [
						'description'       => __( 'Whether to disable PHP-based redirects and use .htaccess instead.', 'wordpress-seo-premium' ),
						'type'              => 'string',
						'required'          => true,
						'enum'              => [ 'on', 'off' ],
					],
					'separate_file' => [
						'description'       => __( 'Whether to write redirects into a separate file instead of .htaccess.', 'wordpress-seo-premium' ),
						'type'              => 'string',
						'required'          => true,
						'enum'              => [ 'on', 'off' ],
					],
				],
				'callback'            => [
					$this->service,
					'update_settings',
				],
				'permission_callback' => [
					$this,
					'can_save_data',
				],
			]
		);
	}

	/**
	 * Determines if the current user is allowed to use this endpoint.
	 *
	 * @return bool True user is allowed to use this endpoint.
	 */
	public function can_save_data() {
		return current_user_can( self::CAPABILITY_STORE );
	}
}
