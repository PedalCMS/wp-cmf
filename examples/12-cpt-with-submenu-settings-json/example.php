<?php
/**
 * Example 12: Custom Post Type with Submenu Settings Page (JSON)
 *
 * This example demonstrates how to create a custom post type with its own
 * settings page as a submenu using JSON configuration.
 *
 * Use Case: A "Product" CPT with a submenu settings page for configuring
 * product catalog defaults, tax rates, currency, and display options.
 *
 * @package    WP-CMF
 * @subpackage Examples
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register CPT with submenu settings page using JSON
 *
 * This example demonstrates loading configuration from JSON to create
 * a Product CPT with its own submenu settings page.
 */
function cpt_with_submenu_settings_json_init() {
	$config_file = __DIR__ . '/config.json';

	if ( ! file_exists( $config_file ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="error"><p>WP-CMF: Configuration file not found at ' . esc_html( __DIR__ . '/config.json' ) . '</p></div>';
			}
		);
		return;
	}

	try {
		Manager::init()->register_from_json( $config_file );
	} catch ( Exception $e ) {
		add_action(
			'admin_notices',
			function () use ( $e ) {
				echo '<div class="error"><p>WP-CMF Error: ' . esc_html( $e->getMessage() ) . '</p></div>';
			}
		);
	}
}
add_action( 'init', 'cpt_with_submenu_settings_json_init' );

/**
 * Activation hook
 */
function cpt_with_submenu_settings_json_activate() {
	cpt_with_submenu_settings_json_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cpt_with_submenu_settings_json_activate' );

/**
 * Deactivation hook
 */
function cpt_with_submenu_settings_json_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cpt_with_submenu_settings_json_deactivate' );
