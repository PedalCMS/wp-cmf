<?php
/**
 * Example 18: Multiple Metaboxes for Settings Page (JSON Configuration)
 *
 * Demonstrates how to organize settings page fields into multiple sections using JSON configuration.
 * While WordPress Settings API uses "sections" instead of "meta boxes", the MetaboxField
 * provides a consistent API for organizing fields into logical groups.
 *
 * Note: On settings pages, metaboxes appear as grouped sections within the form,
 * not as draggable boxes like on post edit screens.
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
