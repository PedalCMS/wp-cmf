<?php
/**
 * Plugin Name: Add Fields to Existing Post Type (Array)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Example showing how to add custom fields to WordPress's built-in 'post' type using array configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: existing-post-type-array
 *
 * @package ExistingPostTypeArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register fields for the existing 'post' type
 *
 * This example demonstrates adding custom fields to WordPress's built-in
 * 'post' post type without creating a new custom post type.
 */
function existing_post_type_array_init() {
	$config = [
		'cpts' => [
			[
				// The post type slug for WordPress's built-in posts
				// Since 'post' already exists, only fields will be added (no new CPT created)
				'id' => 'post',
				
				// Fields to add to the post edit screen
				'fields' => [
					[
						'name'        => 'reading_time',
						'type'        => 'number',
						'label'       => 'Estimated Reading Time',
						'description' => 'Estimated reading time in minutes',
						'placeholder' => '5',
						'min'         => 1,
						'max'         => 120,
						'step'        => 1,
					],
					[
						'name'        => 'post_subtitle',
						'type'        => 'text',
						'label'       => 'Subtitle',
						'description' => 'Optional subtitle for this post',
						'placeholder' => 'Enter a catchy subtitle',
						'maxlength'   => 200,
					],
					[
						'name'        => 'featured_content',
						'type'        => 'checkbox',
						'label'       => 'Featured Content',
						'description' => 'Mark this post as featured content',
					],
					[
						'name'        => 'content_type',
						'type'        => 'select',
						'label'       => 'Content Type',
						'description' => 'Select the type of content',
						'options'     => [
							'article'   => 'Article',
							'tutorial'  => 'Tutorial',
							'review'    => 'Review',
							'news'      => 'News',
							'interview' => 'Interview',
						],
						'default'     => 'article',
					],
					[
						'name'        => 'external_source',
						'type'        => 'url',
						'label'       => 'External Source URL',
						'description' => 'If this content is from an external source, provide the URL',
						'placeholder' => 'https://example.com/article',
					],
				],
			],
		],
	];

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'existing_post_type_array_init' );

/**
 * Activation hook
 */
function existing_post_type_array_activate() {
	existing_post_type_array_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'existing_post_type_array_activate' );

/**
 * Deactivation hook
 */
function existing_post_type_array_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'existing_post_type_array_deactivate' );
