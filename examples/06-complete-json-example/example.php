<?php
/**
 * Plugin Name: Complete JSON Configuration Example
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Comprehensive example demonstrating all 11 field types with CPTs and Settings Pages using JSON configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: complete-json-example
 *
 * @package CompleteJsonExample
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

function complete_json_example_init() {
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
add_action( 'init', 'complete_json_example_init' );

function complete_json_example_activate() {
	complete_json_example_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'complete_json_example_activate' );

function complete_json_example_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'complete_json_example_deactivate' );
