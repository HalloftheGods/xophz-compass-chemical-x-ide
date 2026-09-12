<?php

class Chemical_X_Ide_Public {

	private string $plugin_name;
	private string $version;
	private int $dev_port = 8095;

	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function register_endpoints(): void {
		$custom_slug = get_option( 'xophz_compass_chemical_x_ide_custom_slug', '' );

		// Standard primary slugs
		add_rewrite_rule( '^ide(/.*)?$', 'index.php?xophz_compass_chemical_x_ide=1', 'top' );
		add_rewrite_rule( '^chemical-x-ide(/.*)?$', 'index.php?xophz_compass_chemical_x_ide=1', 'top' );

		// Optional custom slug
		if ( ! empty( $custom_slug ) && $custom_slug !== 'ide' && $custom_slug !== 'chemical-x-ide' ) {
			add_rewrite_rule( '^' . preg_quote( $custom_slug, '/' ) . '(/.*)?$', 'index.php?xophz_compass_chemical_x_ide=1', 'top' );
		}
	}

	public function register_query_vars( array $vars ): array {
		$vars[] = 'xophz_compass_chemical_x_ide';
		return $vars;
	}

	public function template_redirect(): void {
		global $wp_query;

		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( str_starts_with( $request_uri, '/wp-admin' ) || str_starts_with( $request_uri, '/wp-login.php' ) ) {
			return;
		}

		$is_route_match = isset( $wp_query->query_vars['xophz_compass_chemical_x_ide'] );
		if ( ! $is_route_match ) {
			return;
		}

		status_header( 200 );
		$wp_query->is_404 = false;

		$this->render_ide_shell();
		exit;
	}

	private function is_dev_mode(): boolean {
		if ( isset( $_GET['prod'] ) ) {
			return false;
		}
		if ( isset( $_GET['dev'] ) ) {
			return true;
		}
		return ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	private function render_ide_shell(): void {
		$wp_host = wp_parse_url( home_url(), PHP_URL_HOST ) ?: 'localhost';
		$vite_url = '//' . $wp_host . ':' . $this->dev_port;
		$is_dev = $this->is_dev_mode();

		$user_id = get_current_user_id();
		$nonce = wp_create_nonce( 'wp_rest' );
		$roles = array();
		if ( $user_id > 0 ) {
			$u = wp_get_current_user();
			$roles = $u ? (array) $u->roles : array();
		}

		$wp_api_settings = sprintf(
			'<script>window.wpApiSettings = { root: "%s", nonce: "%s", pluginUrl: "%s", version: "%s", userId: %d, roles: %s };</script>',
			esc_url_raw( rest_url() ),
			esc_js( $nonce ),
			esc_url_raw( XOPHZ_COMPASS_CHEMICAL_X_IDE_URL ),
			esc_js( $this->version ),
			$user_id,
			wp_json_encode( $roles )
		);

		// Dev Server Proxy Attempt
		if ( $is_dev ) {
			$dev_html = @file_get_contents( "http://127.0.0.1:{$this->dev_port}/" );
			if ( $dev_html ) {
				$dev_html = str_replace( 'src="/', 'src="' . $vite_url . '/', $dev_html );
				$dev_html = str_replace( 'href="/', 'href="' . $vite_url . '/', $dev_html );
				$dev_html = str_replace( 'import("/', 'import("' . $vite_url . '/', $dev_html );

				if ( ! str_contains( $dev_html, '/@vite/client' ) ) {
					$vite_client = '<script type="module" src="' . esc_url( $vite_url ) . '/@vite/client"></script>';
					$dev_html = str_replace( '</head>', $vite_client . "\n</head>", $dev_html );
				}

				$dev_html = str_replace( '</head>', $wp_api_settings . "\n</head>", $dev_html );
				header( 'Content-Type: text/html; charset=utf-8' );
				echo $dev_html;
				return;
			}
		}

		// Production Dist Mode
		$index_file = XOPHZ_COMPASS_CHEMICAL_X_IDE_PATH . 'public/dist/index.html';
		if ( file_exists( $index_file ) ) {
			$html = file_get_contents( $index_file );
			$dist_url = XOPHZ_COMPASS_CHEMICAL_X_IDE_URL . 'public/dist/';

			$html = str_replace( '"/assets/', '"' . $dist_url . 'assets/', $html );
			$html = str_replace( "'/assets/", "'" . $dist_url . 'assets/', $html );
			$html = str_replace( '"/_nuxt/', '"' . $dist_url . '_nuxt/', $html );
			$html = str_replace( "'/_nuxt/", "'" . $dist_url . '_nuxt/', $html );
			$html = str_replace( '</head>', $wp_api_settings . "\n</head>", $html );

			header( 'Content-Type: text/html; charset=utf-8' );
			echo $html;
			return;
		}

		// Clean Fallback when dist is not yet built
		$this->render_fallback_shell( $wp_api_settings );
	}

	private function render_fallback_shell( string $wp_api_settings ): void {
		header( 'Content-Type: text/html; charset=utf-8' );
		?>
		<!DOCTYPE html>
		<html lang="en" class="dark">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Chemical X IDE</title>
			<style>
				body { margin: 0; background: #181818; color: #cccccc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; }
				.card { background: #252526; border: 1px solid #333333; border-radius: 12px; padding: 32px; max-width: 520px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
				h1 { color: #62c9ff; margin-top: 0; font-size: 20px; }
				p { font-size: 13px; line-height: 1.6; color: #888888; }
				code { background: #181818; padding: 3px 6px; border-radius: 4px; color: #38bdf8; font-family: monospace; font-size: 12px; }
			</style>
			<?php echo $wp_api_settings; ?>
		</head>
		<body>
			<div class="card">
				<h1>Chemical X IDE Shell Ready</h1>
				<p>The companion WordPress router is active on <code>/ide</code> and <code>/chemical-x-ide</code>.</p>
				<p>To start the frontend dev server, run: <br/><code>cd apps/my-chemical-x-ide && pnpm dev</code></p>
			</div>
		</body>
		</html>
		<?php
	}
}
