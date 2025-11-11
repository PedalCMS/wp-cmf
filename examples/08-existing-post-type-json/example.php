<?php
/**
 * Plugin Name: Add Fields to Existing Post Type (JSON)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Example showing how to add custom fields to WordPress's built-in 'post' type using JSON configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: existing-post-type-json
 *
 * @package ExistingPostTypeJson
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register fields for the existing 'post' type using JSON
 *
 * This example demonstrates loading field configuration from a JSON file
 * to add custom fields to WordPress's built-in 'post' post type.
 */
function existing_post_type_json_init() {
	$config_file = __DIR__ . '/config.json';

	if ( ! file_exists( $config_file ) ) {
		add_action(
			'admin_notices',
			function() {
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
			function() use ( $e ) {
				echo '<div class="error"><p>WP-CMF Error: ' . esc_html( $e->getMessage() ) . '</p></div>';
			}
		);
	}
}
add_action( 'init', 'existing_post_type_json_init' );

/**
 * Activation hook
 */
function existing_post_type_json_activate() {
	existing_post_type_json_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'existing_post_type_json_activate' );

/**
 * Deactivation hook
 */
function existing_post_type_json_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'existing_post_type_json_deactivate' );
