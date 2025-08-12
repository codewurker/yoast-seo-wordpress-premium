<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium
 */

/**
 * The service for the redirects to WordPress.
 */
class WPSEO_Premium_Redirect_Service {

	/**
	 * Saves the redirect to the redirects.
	 *
	 * This save function is only used in the deprecated google-search-console integration.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back.
	 */
	public function save( WP_REST_Request $request ) {
		$redirect       = $this->map_request_to_redirect( $request );
		$ignore_warning = $request->get_param( 'ignore_warning' );

		$validator = new WPSEO_Redirect_Validator();

		if ( ! $validator->validate( $redirect ) ) {
			$errors = $validator->get_error()->to_array();

			// If it's not a warning or ignore_warning is false, return the error.
			if ( $errors['type'] === 'error' || ( $errors['type'] === 'warning' && ! $ignore_warning ) ) {
				return new WP_REST_Response(
					[
						'title'   => __( 'Redirect not created.', 'wordpress-seo-premium' ),
						'message' => $errors['message'],
						'fields'  => $errors['fields'],
						'type'    => $errors['type'],
						'success' => false,
					],
					200
				);
			}
			// If it's a warning and ignore_warning is true, continue to create the redirect below.
		}

		if ( $this->get_redirect_manager()->create_redirect( $redirect ) ) {
			return new WP_REST_Response(
				[
					'title'   => __( 'Redirect created.', 'wordpress-seo-premium' ),
					'message' => __( 'The redirect was created successfully.', 'wordpress-seo-premium' ),
					'success' => true,
				]
			);
		}

		return new WP_REST_Response(
			[
				'title'   => __( 'Redirect not created.', 'wordpress-seo-premium' ),
				'message' => __( 'Something went wrong when creating this redirect.', 'wordpress-seo-premium' ),
				'success' => false,
			],
			400
		);
	}

	/**
	 * Deletes the redirect from the redirects.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back.
	 */
	public function delete( WP_REST_Request $request ) {
		$redirect  = $this->map_request_to_redirect( $request );
		$redirects = [ $redirect ];

		$redirect_format = $request->get_param( 'format' );
		if ( ! $redirect_format ) {
			$redirect_format = WPSEO_Redirect_Formats::PLAIN;
		}

		if ( $this->get_redirect_manager( $redirect_format )->delete_redirects( $redirects ) ) {
			return new WP_REST_Response(
				[
					'title'   => __( 'Redirect deleted.', 'wordpress-seo-premium' ),
					'message' => __( 'The redirect was deleted successfully.', 'wordpress-seo-premium' ),
					'success' => true,
				]
			);
		}

		return new WP_REST_Response(
			[
				'title'   => __( 'Redirect not deleted.', 'wordpress-seo-premium' ),
				'message' => __( 'Something went wrong when deleting this redirect.', 'wordpress-seo-premium' ),
				'success' => false,
			],
			400
		);
	}

	/**
	 * Returns a list of redirects.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back.
	 */
	public function list( WP_REST_Request $request ) {
		$format = $request->get_param( 'format' );

		$redirect_manager = new WPSEO_Redirect_Manager( $format );

		return new WP_REST_Response(
			[
				'success'   => true,
				'redirects' => $redirect_manager->get_redirects(),
			]
		);
	}

	/**
	 * Attempts to update an existing redirect.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back
	 */
	public function update( WP_REST_Request $request ) {
		$redirects = $this->map_request_to_redirect_update( $request );

		$ignore_warning = $request->get_param( 'ignore_warning' );

		$errors = $this->validate_update_request( $redirects, $ignore_warning );

		if ( $errors !== true ) {
			return new WP_REST_Response(
				[
					'title'   => __( 'Redirect not deleted.', 'wordpress-seo-premium' ),
					'message' => $errors['message'],
					'fields'  => $errors['fields'],
					'type'    => ( $errors['type'] ?? 'error' ),
					'success' => false,
				],
				400
			);
		}

		if ( $this->get_redirect_manager()->update_redirect( $redirects['old_redirect'], $redirects['new_redirect'] ) ) {
			return new WP_REST_Response(
				[
					'title'   => __( 'Redirect updated.', 'wordpress-seo-premium' ),
					'message' => __( 'The redirect was updated successfully.', 'wordpress-seo-premium' ),
					'success' => true,
				]
			);
		}

		return new WP_REST_Response(
			[
				'title'   => __( 'Redirect not updated.', 'wordpress-seo-premium' ),
				'message' => __( 'Something went wrong when updated this redirect.', 'wordpress-seo-premium' ),
				'success' => false,
			],
			400
		);
	}

	/**
	 * Returns a list of redirects settings.
	 *
	 * @return WP_REST_Response
	 */
	public function settings() {
		$is_apache     = WPSEO_Utils::is_apache();
		$separate_file = WPSEO_Options::get( 'separate_file' );

		if ( $is_apache && $separate_file === 'off' ) {
			$htaccess_file = WPSEO_Redirect_Htaccess_Util::get_htaccess_file_path();

			if ( ! WPSEO_Redirect_File_Util::is_writable( $htaccess_file ) ) {
				return new WP_REST_Response(
					[
						'success'              => false,
						'is_apache'            => $is_apache,
						'disable_php_redirect' => WPSEO_Options::get( 'disable_php_redirect' ),
						'separate_file'        => $separate_file,
						'file_path'            => 'cannot_write_htaccess',
					]
				);
			}
		}

		return new WP_REST_Response(
			[
				'success'              => true,
				'is_apache'            => $is_apache,
				'disable_php_redirect' => WPSEO_Options::get( 'disable_php_redirect' ),
				'separate_file'        => $separate_file,
				'file_path'            => WPSEO_Redirect_File_Util::get_file_path(),
			]
		);
	}

	/**
	 * Updates the redirect settings via the REST API.
	 *
	 * Handles the update of plugin redirect options like using PHP or .htaccess,
	 * and whether to write redirects to a separate file.
	 *
	 * Expected request parameters:
	 * - disable_php_redirect: (string) 'on' or 'off'
	 * - separate_file:        (string) 'on' or 'off'
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 *
	 * @return WP_REST_Response Returns a JSON response indicating success or failure.
	 */
	public function update_settings( WP_REST_Request $request ) {
		$disable_php_redirect = $request->get_param( 'disable_php_redirect' );
		$separate_file        = $request->get_param( 'separate_file' );

		if ( ! in_array( $disable_php_redirect, [ 'on', 'off' ], true ) ) {
			return new WP_REST_Response(
				[
					'error'   => true,
					'message' => 'Invalid value for disable_php_redirect.',
				],
				400
			);
		}

		if ( ! in_array( $separate_file, [ 'on', 'off' ], true ) ) {
			return new WP_REST_Response(
				[
					'error'   => true,
					'message' => 'Invalid value for separate_file.',
				],
				400
			);
		}

		$current_options = get_option( 'wpseo_redirect', [] );

		$new_options = array_merge(
			$current_options,
			[
				'disable_php_redirect' => $disable_php_redirect,
				'separate_file'        => $separate_file,
			]
		);

		update_option( 'wpseo_redirect', $new_options );

		return new WP_REST_Response(
			[
				'success' => true,
				'message' => 'Redirect settings updated successfully.',
				'data'    => $new_options,
			]
		);
	}

	/**
	 * Creates and returns an instance of the redirect manager.
	 *
	 * @param string $format The redirect format.
	 *
	 * @return WPSEO_Redirect_Manager The redirect maanger.
	 */
	protected function get_redirect_manager( $format = WPSEO_Redirect_Formats::PLAIN ) {
		return new WPSEO_Redirect_Manager( $format );
	}

	/**
	 * Maps the given request to an instance of the WPSEO_Redirect.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WPSEO_Redirect Redirect instance.
	 */
	protected function map_request_to_redirect( WP_REST_Request $request ) {
		$origin = $request->get_param( 'origin' );
		$target = $request->get_param( 'target' );
		$type   = ( $request->get_param( 'type' ) ?? WPSEO_Redirect_Types::PERMANENT );
		$format = ( $request->get_param( 'format' ) ?? WPSEO_Redirect_Formats::PLAIN );

		return new WPSEO_Redirect( $origin, $target, $type, $format );
	}

	/**
	 * Maps the given request to an instance of the WPSEO_Redirect.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WPSEO_Redirect[]|array<string, WPSEO_Redirect> Redirect instances.
	 */
	protected function map_request_to_redirect_update( WP_REST_Request $request ) {
		$old_origin = $request->get_param( 'old_origin' );
		$old_target = $request->get_param( 'old_target' );
		$old_type   = $request->get_param( 'old_type' );

		$new_origin = $request->get_param( 'new_origin' );
		$new_target = $request->get_param( 'new_target' );
		$new_type   = $request->get_param( 'new_type' );

		$format = $request->get_param( 'format' );

		return [
			'old_redirect' => new WPSEO_Redirect( $old_origin, $old_target, $old_type, $format ),
			'new_redirect' => new WPSEO_Redirect( $new_origin, $new_target, $new_type, $format ),
		];
	}

	/**
	 * Run the validation.
	 *
	 * @param array<string,WPSEO_Redirect> $redirects      Array of WPSEO_Redirect instances keyed by 'old_redirect' and 'new_redirect'.
	 * @param bool                         $ignore_warning Whether to ignore warnings during validation.
	 *
	 * @return true|array<string,string> Returns true if validation passes, or an array of error messages.
	 */
	protected function validate_update_request( array $redirects, bool $ignore_warning = false ) {
		$validator = new WPSEO_Redirect_Validator();

		foreach ( [ 'old_redirect', 'new_redirect' ] as $key ) {
			if ( empty( $redirects[ $key ] ) || ! $redirects[ $key ] instanceof WPSEO_Redirect ) {
				return [
					'message' => __( 'Something went wrong when updated this redirect.', 'wordpress-seo-premium' ),
					'type'    => 'error',
				];
			}
		}

		if ( $validator->validate( $redirects['new_redirect'], $redirects['old_redirect'] ) === true ) {
			return true;
		}

		$error = $validator->get_error();

		if ( $error->get_type() === 'warning' && $ignore_warning ) {
			return true;
		}

		$error_array         = $error->to_array();
		$error_array['type'] = $error->get_type();
		return $error_array;
	}
}
