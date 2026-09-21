<?php

class Chemical_X_Ide_REST {

	private string $namespace = 'chemical-x/v1';

	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/health',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_health' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->namespace,
			'/workspace',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_workspace' ),
					'permission_callback' => array( $this, 'check_read_permission' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_workspace' ),
					'permission_callback' => array( $this, 'check_write_permission' ),
				),
			)
		);
	}

	public function get_health(): WP_REST_Response {
		return rest_ensure_response(
			array(
				'status'    => 'ok',
				'engine'    => 'Chemical X Molecular IDE Bridge',
				'version'   => XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION,
				'timestamp' => current_time( 'mysql' ),
			)
		);
	}

	public function check_read_permission(): bool {
		return is_user_logged_in();
	}

	public function check_write_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function get_workspace( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();
		$workspace = get_user_meta( $user_id, 'chemx_ide_workspace', true );

		return rest_ensure_response(
			array(
				'success' => true,
				'userId'  => $user_id,
				'files'   => is_array( $workspace ) ? $workspace : array(),
			)
		);
	}

	public function save_workspace( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();
		$files = $request->get_param( 'files' );

		if ( ! is_array( $files ) ) {
			return new WP_REST_Response( array( 'error' => 'Invalid files format' ), 400 );
		}

		update_user_meta( $user_id, 'chemx_ide_workspace', $files );

		return rest_ensure_response(
			array(
				'success' => true,
				'userId'  => $user_id,
				'count'   => count( $files ),
			)
		);
	}
}
