<?php
/**
 * Plugin Name: Add Fields to Existing Settings Page (Array)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Example showing how to add custom fields to WordPress's built-in General Settings page using array configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: existing-settings-page-array
 *
 * @package ExistingSettingsPageArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register fields to existing WordPress General Settings page
 *
 * This example demonstrates adding custom fields directly to WordPress's
 * built-in General Settings page (Settings > General) using array configuration.
 */
function existing_settings_page_array_init() {
	$config = [
		'settings_pages' => [
			[
				// The settings page ID - 'general' is WordPress's built-in General Settings
				// Since 'general' already exists, only fields will be added (no new page created)
				'id'     => 'general',

				// Fields to add to the General Settings page
				'fields' => [
					[
						'name'        => 'site_tagline_extended',
						'type'        => 'text',
						'label'       => 'Extended Tagline',
						'description' => 'Additional tagline text to complement the default WordPress tagline',
						'placeholder' => 'More about your site',
						'maxlength'   => 150,
					],
					[
						'name'        => 'maintenance_mode',
						'type'        => 'checkbox',
						'label'       => 'Enable Maintenance Mode',
						'description' => 'Show maintenance message to non-admin users',
					],
					[
						'name'        => 'maintenance_message',
						'type'        => 'textarea',
						'label'       => 'Maintenance Message',
						'description' => 'Message to display during maintenance',
						'rows'        => 4,
						'placeholder' => 'We are currently performing scheduled maintenance...',
					],
					[
						'name'        => 'contact_email',
						'type'        => 'email',
						'label'       => 'Contact Email',
						'description' => 'Primary contact email for inquiries',
						'placeholder' => 'contact@example.com',
					],
					[
						'name'        => 'social_sharing',
						'type'        => 'checkbox',
						'label'       => 'Enable Social Sharing',
						'options'     => [
							'facebook'  => 'Facebook',
							'twitter'   => 'Twitter',
							'linkedin'  => 'LinkedIn',
							'pinterest' => 'Pinterest',
						],
						'description' => 'Select which social sharing buttons to display',
					],
					[
						'name'          => 'site_footer_text',
						'type'          => 'wysiwyg',
						'label'         => 'Footer Text',
						'description'   => 'Custom HTML for site footer',
						'media_buttons' => false,
						'teeny'         => true,
						'textarea_rows' => 6,
					],
				],
			],
		],
	];

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'existing_settings_page_array_init' );

/**
 * Activation hook
 */
function existing_settings_page_array_activate() {
	existing_settings_page_array_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'existing_settings_page_array_activate' );

/**
 * Deactivation hook
 */
function existing_settings_page_array_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'existing_settings_page_array_deactivate' );
