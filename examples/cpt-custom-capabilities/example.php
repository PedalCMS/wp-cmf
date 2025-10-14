<?php
/**
 * Example: Custom Capabilities CPT
 *
 * This example demonstrates creating a custom post type with
 * custom capability mapping for advanced permission control.
 *
 * @package Pedalcms\WpCmf
 */

// Include the WP-CMF autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\CPT\CustomPostType;

/**
 * Register a Projects CPT with custom capabilities
 */
function register_projects_cpt() {
	$cpt = CustomPostType::from_array( 'project', [
		'singular' => 'Project',
		'plural'   => 'Projects',
		'public'   => true,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'capability_type' => 'project',
		'map_meta_cap'    => true,
		'capabilities'    => [
			'edit_post'          => 'edit_project',
			'read_post'          => 'read_project',
			'delete_post'        => 'delete_project',
			'edit_posts'         => 'edit_projects',
			'edit_others_posts'  => 'edit_others_projects',
			'publish_posts'      => 'publish_projects',
			'read_private_posts' => 'read_private_projects',
		],
	] );

	$cpt->register();
}

// Hook into WordPress init
add_action( 'init', 'register_projects_cpt' );

/**
 * Optional: Grant capabilities to specific roles
 */
function grant_project_capabilities() {
	// Grant all project capabilities to administrators
	$admin_role = get_role( 'administrator' );
	if ( $admin_role ) {
		$admin_role->add_cap( 'edit_project' );
		$admin_role->add_cap( 'read_project' );
		$admin_role->add_cap( 'delete_project' );
		$admin_role->add_cap( 'edit_projects' );
		$admin_role->add_cap( 'edit_others_projects' );
		$admin_role->add_cap( 'publish_projects' );
		$admin_role->add_cap( 'read_private_projects' );
	}

	// Grant limited capabilities to editors
	$editor_role = get_role( 'editor' );
	if ( $editor_role ) {
		$editor_role->add_cap( 'edit_project' );
		$editor_role->add_cap( 'read_project' );
		$editor_role->add_cap( 'edit_projects' );
		$editor_role->add_cap( 'publish_projects' );
	}
}

// Run once on activation
add_action( 'wp_loaded', 'grant_project_capabilities' );

/**
 * Usage Instructions:
 *
 * 1. Include this file in your WordPress plugin
 * 2. Make sure WP-CMF is loaded via Composer
 * 3. Projects CPT will be registered with custom capabilities
 * 4. Use a role management plugin to assign capabilities to users
 *
 * Features demonstrated:
 * - Custom capability mapping
 * - Granular permission control
 * - Role-based access control
 * - Security through capability checking
 * - Professional user management
 */
