<?php
/**
 * Example: Manager and Registrar Usage
 *
 * This example demonstrates how to register custom post types using
 * the WP-CMF Manager and Registrar classes with array configuration.
 *
 * @package Pedalcms\WpCmf
 */

// Include the WP-CMF autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Register multiple CPTs using Manager and Registrar
 */
function register_multiple_cpts() {
	// Get the WP-CMF Manager instance
	$manager = Manager::init();
	$registrar = $manager->get_registrar();

	// Register a custom post type using array configuration
	$registrar->add_custom_post_type( 'portfolio', [
		'singular' => 'Portfolio Item',
		'plural'   => 'Portfolio Items',
		'public'   => true,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'menu_icon' => 'dashicons-portfolio',
		'has_archive' => true,
		'rewrite' => [ 'slug' => 'portfolio' ],
	] );

	// Register another CPT for testimonials
	$registrar->add_custom_post_type( 'testimonial', [
		'singular' => 'Testimonial',
		'plural'   => 'Testimonials',
		'public'   => true,
		'supports' => [ 'title', 'editor' ],
		'menu_icon' => 'dashicons-format-quote',
		'show_in_rest' => true,
	] );
}

// Hook into WordPress init
add_action( 'init', 'register_multiple_cpts' );

/**
 * Usage Instructions:
 *
 * 1. Include this file in your WordPress plugin
 * 2. Make sure WP-CMF is loaded via Composer
 * 3. Portfolio and Testimonial CPTs will be registered automatically
 *
 * Features demonstrated:
 * - Using the WP-CMF Manager singleton
 * - Array-based configuration
 * - Registering multiple CPTs efficiently
 * - Different configurations for different use cases
 * - Integration with the WP-CMF architecture
 */