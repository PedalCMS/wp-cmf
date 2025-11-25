<?php
/**
 * Example 16: Multiple Metaboxes for Custom Post Type (JSON Configuration)
 *
 * Demonstrates how to create multiple meta boxes per post type using JSON configuration.
 * Each metabox is a container field that groups related fields together.
 *
 * The metabox field:
 * - Creates separate WordPress meta boxes with individual titles and positions
 * - Checks if metabox exists, creates it if not, adds fields to existing ones
 * - Works like the tabs field - it's a container with nested fields
 * - Doesn't store values itself - nested fields handle their own data
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

// Initialize WP-CMF Manager
$manager = Manager::init();

// Load configuration from JSON file
$json_file = __DIR__ . '/config.json';

if ( file_exists( $json_file ) ) {
	$manager->register_from_json( $json_file );
} else {
	add_action( 'admin_notices', function() {
		echo '<div class="notice notice-error"><p>JSON configuration file not found: ' . esc_html( __DIR__ . '/config.json' ) . '</p></div>';
	} );
}
