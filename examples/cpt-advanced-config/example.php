<?php
/**
 * Example: Advanced CPT Configuration
 *
 * This example demonstrates advanced custom post type registration
 * with fully customized labels and detailed WordPress configuration.
 *
 * @package Pedalcms\WpCmf
 */

// Include the WP-CMF autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Register an Events CPT with advanced configuration
 */
function register_events_cpt() {
	$manager = Manager::init();
	$registrar = $manager->get_registrar();

	// Create a CPT with fully customized labels and advanced options
	$registrar->add_custom_post_type( 'event', [
		'labels' => [
			'name'          => 'Events',
			'singular_name' => 'Event',
			'add_new_item'  => 'Add New Event',
			'edit_item'     => 'Edit Event',
			'view_item'     => 'View Event',
			'search_items'  => 'Search Events',
			'not_found'     => 'No events found',
		],
		'public'        => true,
		'has_archive'   => true,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 20,
		'rewrite'       => [
			'slug'       => 'events',
			'with_front' => false,
		],
		'capability_type' => 'post',
		'show_in_rest'    => true,
	] );
}

// Hook into WordPress init
add_action( 'init', 'register_events_cpt' );

/**
 * Usage Instructions:
 *
 * 1. Include this file in your WordPress plugin
 * 2. Make sure WP-CMF is loaded via Composer
 * 3. The Events CPT will be registered with full customization
 *
 * Features demonstrated:
 * - Custom labels array for complete control
 * - Menu position and icon customization
 * - Advanced rewrite rules
 * - Custom fields support
 * - REST API integration
 * - Archive page configuration
 */