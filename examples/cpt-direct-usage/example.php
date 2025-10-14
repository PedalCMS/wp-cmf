<?php
/**
 * Example: Direct CustomPostType Usage
 *
 * This example demonstrates how to create and register custom post types
 * directly using the CustomPostType class with a fluent interface.
 *
 * @package Pedalcms\WpCmf
 */

// Include the WP-CMF autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\CPT\CustomPostType;

/**
 * Register a Books custom post type using direct CustomPostType usage
 */
function register_books_cpt() {
	// Create a new custom post type for books
	$book_cpt = new CustomPostType( 'book' );

	// Configure using fluent interface
	$book_cpt->generate_labels( 'Book', 'Books' )
	         ->set_defaults()
	         ->set_arg( 'menu_icon', 'dashicons-book' )
	         ->set_arg( 'has_archive', true )
	         ->set_supports( [ 'title', 'editor', 'thumbnail', 'excerpt' ] );

	// Register with WordPress
	$book_cpt->register();
}

// Hook into WordPress init
add_action( 'init', 'register_books_cpt' );

/**
 * Usage Instructions:
 *
 * 1. Include this file in your WordPress plugin
 * 2. Make sure WP-CMF is loaded via Composer
 * 3. The Books CPT will be registered automatically
 *
 * Features demonstrated:
 * - Direct CustomPostType instantiation
 * - Fluent interface method chaining
 * - Automatic label generation
 * - Setting default WordPress arguments
 * - Custom menu icon and archive support
 * - Multiple post type supports
 */