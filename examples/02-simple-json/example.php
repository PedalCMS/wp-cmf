<?php
/**
 * Plugin Name: WP-CMF Simple Example (JSON)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Simple example demonstrating WP-CMF basics using JSON configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * License: GPL v2 or later
 *
 * @package WpCmfSimpleJson
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * =============================================================================
 * SIMPLE JSON EXAMPLE
 * =============================================================================
 *
 * This example demonstrates the same functionality as simple-array, but using
 * an external JSON configuration file.
 *
 * Benefits of JSON configuration:
 * - Easy to edit without PHP knowledge
 * - Can be validated against JSON Schema
 * - Easy to share/export configuration
 * - Good for CI/CD pipelines
 * - Multi-environment configuration
 * =============================================================================
 */
function wp_cmf_simple_json_init() {
	$config_file = __DIR__ . '/config.json';

	Manager::init()->register_from_json( $config_file );
}
add_action( 'init', 'wp_cmf_simple_json_init' );

/**
 * =============================================================================
 * RETRIEVING SAVED VALUES
 * =============================================================================
 */

/**
 * Get event meta value
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return mixed
 */
function get_event_field( $post_id, $field ) {
	return get_post_meta( $post_id, $field, true );
}

/**
 * Get events setting
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function get_events_setting( $field, $default = '' ) {
	return get_option( 'events-settings_' . $field, $default );
}

/**
 * Example: Display event details
 */
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! is_singular( 'event' ) ) {
			return $content;
		}

		$post_id  = get_the_ID();
		$date     = get_event_field( $post_id, 'event_date' );
		$location = get_event_field( $post_id, 'location' );
		$capacity = get_event_field( $post_id, 'capacity' );

		$details = '<div class="event-details">';
		if ( $date ) {
			$details .= '<p><strong>Date:</strong> ' . esc_html( $date ) . '</p>';
		}
		if ( $location ) {
			$details .= '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
		}
		if ( $capacity ) {
			$details .= '<p><strong>Capacity:</strong> ' . esc_html( $capacity ) . ' attendees</p>';
		}
		$details .= '</div>';

		return $details . $content;
	}
);
