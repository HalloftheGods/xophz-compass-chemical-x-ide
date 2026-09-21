<?php
/**
 * Plugin Name:       Chemical X IDE
 * Description:       High-velocity Chemical X Molecular Architecture IDE editor SPA, live AST hazard auditor, and WordPress workspace bridge.
 * Version:           26.9.11
 * Author:            Hall of the Gods, Inc.
 * Category:          Command Deck
 * Group:             Ecosystem
 * Text Domain:       xophz-compass-chemical-x-ide
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION', '26.9.11' );
define( 'XOPHZ_COMPASS_CHEMICAL_X_IDE_PATH', plugin_dir_path( __FILE__ ) );
define( 'XOPHZ_COMPASS_CHEMICAL_X_IDE_URL', plugin_dir_url( __FILE__ ) );

require_once XOPHZ_COMPASS_CHEMICAL_X_IDE_PATH . 'admin/class-chemical-x-ide-admin.php';
require_once XOPHZ_COMPASS_CHEMICAL_X_IDE_PATH . 'public/class-chemical-x-ide-public.php';
require_once XOPHZ_COMPASS_CHEMICAL_X_IDE_PATH . 'includes/class-chemical-x-ide-rest.php';

function run_xophz_compass_chemical_x_ide() {
	$admin = new Chemical_X_Ide_Admin( 'xophz-compass-chemical-x-ide', XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION );
	add_action( 'admin_menu', array( $admin, 'add_plugin_admin_menu' ) );
	add_action( 'admin_init', array( $admin, 'register_settings' ) );
	add_action( 'update_option_xophz_compass_chemical_x_ide_custom_slug', array( $admin, 'flush_rewrites_on_save' ), 10, 2 );

	$public = new Chemical_X_Ide_Public( 'xophz-compass-chemical-x-ide', XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION );
	add_action( 'init', array( $public, 'register_endpoints' ) );
	add_filter( 'query_vars', array( $public, 'register_query_vars' ) );
	add_action( 'template_redirect', array( $public, 'template_redirect' ) );

	$rest = new Chemical_X_Ide_REST();
	add_action( 'rest_api_init', array( $rest, 'register_routes' ) );

	// Register with Event Horizon / YouMeOS Spark Registry
	add_filter( 'xophz_register_sparks', function( $sparks ) {
		$sparks['chemical-x-ide'] = array(
			'id'          => 'chemical-x-ide',
			'title'       => 'Chemical X IDE',
			'description' => 'Molecular architecture editor and AST hazard inspector',
			'icon'        => 'fal fa-code',
			'color'       => '#62c9ff',
			'url'         => '/ide',
			'category'    => 'developer',
			'type'        => 'webspark',
			'status'      => 'pi',
			'weight'      => 90,
			'active'      => true,
			'version'     => XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION,
		);
		return $sparks;
	} );
}

add_action( 'plugins_loaded', 'run_xophz_compass_chemical_x_ide' );

function xophz_compass_chemical_x_ide_activate() {
	$public = new Chemical_X_Ide_Public( 'xophz-compass-chemical-x-ide', XOPHZ_COMPASS_CHEMICAL_X_IDE_VERSION );
	$public->register_endpoints();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'xophz_compass_chemical_x_ide_activate' );

function xophz_compass_chemical_x_ide_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'xophz_compass_chemical_x_ide_deactivate' );
