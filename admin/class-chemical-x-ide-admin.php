<?php

class Chemical_X_Ide_Admin {

	private string $plugin_name;
	private string $version;

	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_plugin_admin_menu(): void {
		add_options_page(
			'Chemical X IDE Settings',
			'Chemical X IDE',
			'manage_options',
			'xophz-compass-chemical-x-ide',
			array( $this, 'display_settings_page' )
		);
	}

	public function register_settings(): void {
		register_setting( 'xophz_compass_chemical_x_ide_options', 'xophz_compass_chemical_x_ide_custom_slug' );
	}

	public function flush_rewrites_on_save(): void {
		flush_rewrite_rules();
	}

	public function display_settings_page(): void {
		$custom_slug = get_option( 'xophz_compass_chemical_x_ide_custom_slug', '' );
		$ide_url = home_url( '/ide' );
		$chem_url = home_url( '/chemical-x-ide' );
		?>
		<div class="wrap">
			<h2>Chemical X IDE Settings</h2>
			<p>High-velocity Molecular Architecture IDE editor SPA and live AST code auditor.</p>

			<div class="card" style="max-width: 600px; padding: 16px 24px; margin-bottom: 20px;">
				<h3>Active SPA Routes</h3>
				<ul style="list-style: disc; margin-left: 20px;">
					<li><a href="<?php echo esc_url( $ide_url ); ?>" target="_blank"><?php echo esc_html( $ide_url ); ?></a> (Primary)</li>
					<li><a href="<?php echo esc_url( $chem_url ); ?>" target="_blank"><?php echo esc_html( $chem_url ); ?></a> (Secondary)</li>
				</ul>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'xophz_compass_chemical_x_ide_options' );
				do_settings_sections( 'xophz_compass_chemical_x_ide_options' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Optional Custom Slug</th>
						<td>
							<input
								type="text"
								name="xophz_compass_chemical_x_ide_custom_slug"
								value="<?php echo esc_attr( $custom_slug ); ?>"
								class="regular-text"
								placeholder="e.g. editor"
							/>
							<p class="description">Optional additional URL slug to deploy the IDE alongside /ide and /chemical-x-ide.</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
