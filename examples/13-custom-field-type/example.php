<?php
/**
 * Plugin Name: Custom Field Type Example
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Example showing how to create a custom field type (SliderField) and use it in WordPress General Settings page
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-field-type
 *
 * @package CustomFieldType
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/SliderField.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register custom field type with General Settings
 *
 * This example demonstrates:
 * 1. Creating a custom SliderField type that extends AbstractField
 * 2. Registering the custom field type with WP-CMF
 * 3. Using the custom field in WordPress General Settings page
 */
function custom_field_type_init() {
	// Get the Manager instance
	$manager = Manager::init();

	// Register the custom SliderField type
	$manager->register_field_type( 'slider', 'SliderField' );

	// Configuration array adding custom slider fields to General Settings
	$config = [
		'settings_pages' => [
			[
				// The settings page ID - 'general' is WordPress's built-in General Settings
				// Since 'general' already exists, only fields will be added
				'id'     => 'general',

				// Fields to add to the General Settings page
				'fields' => [
					[
						'name'        => 'site_quality',
						'type'        => 'slider',
						'label'       => 'Site Quality Level',
						'description' => 'Set the overall quality/performance level for your site (affects image compression, caching, etc.)',
						'default'     => 75,
						'min'         => 0,
						'max'         => 100,
						'step'        => 5,
						'unit'        => '%',
						'show_value'  => true,
						'marks'       => [
							0   => 'Low',
							50  => 'Medium',
							100 => 'High',
						],
					],
					[
						'name'        => 'content_width',
						'type'        => 'slider',
						'label'       => 'Content Width',
						'description' => 'Maximum width for content area in pixels',
						'default'     => 1200,
						'min'         => 600,
						'max'         => 1920,
						'step'        => 50,
						'unit'        => 'px',
						'show_value'  => true,
					],
					[
						'name'        => 'image_quality',
						'type'        => 'slider',
						'label'       => 'Image Quality',
						'description' => 'JPEG compression quality for uploaded images',
						'default'     => 85,
						'min'         => 1,
						'max'         => 100,
						'step'        => 1,
						'unit'        => '%',
						'show_value'  => true,
						'marks'       => [
							1   => 'Min',
							85  => 'Recommended',
							100 => 'Max',
						],
					],
					[
						'name'        => 'admin_menu_opacity',
						'type'        => 'slider',
						'label'       => 'Admin Menu Opacity',
						'description' => 'Transparency level for the WordPress admin menu',
						'default'     => 1.0,
						'min'         => 0.3,
						'max'         => 1.0,
						'step'        => 0.1,
						'show_value'  => true,
					],
				],
			],
		],
	];

	$manager->register_from_array( $config );
}
add_action( 'init', 'custom_field_type_init' );

/**
 * Activation hook
 */
function custom_field_type_activate() {
	custom_field_type_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'custom_field_type_activate' );

/**
 * Deactivation hook
 */
function custom_field_type_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_field_type_deactivate' );
