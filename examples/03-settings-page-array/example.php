<?php
/**
 * Plugin Name: Settings Page with Array Configuration
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Simple example of creating a WordPress settings page with fields using array configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: settings-array
 *
 * @package SettingsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

function settings_array_init() {
	$config = array(
		'settings_pages' => array(
			array(
				'id'         => 'my_plugin_settings',
				'title'      => 'My Plugin Settings',
				'menu_title' => 'My Plugin',
				'capability' => 'manage_options',
				'slug'       => 'my-plugin-settings',
				'icon'       => 'dashicons-admin-generic',
				'position'   => 80,
				'fields'     => array(
					array(
						'name'        => 'site_title',
						'type'        => 'text',
						'label'       => 'Site Title',
						'description' => 'Custom site title for your plugin',
						'placeholder' => 'Enter site title',
						'required'    => true,
						'default'     => 'My Awesome Site',
					),
					array(
						'name'        => 'site_description',
						'type'        => 'textarea',
						'label'       => 'Site Description',
						'description' => 'Brief description of your site',
						'rows'        => 5,
						'cols'        => 50,
						'placeholder' => 'Enter description',
					),
					array(
						'name'        => 'enable_feature',
						'type'        => 'checkbox',
						'label'       => 'Enable Feature',
						'description' => 'Check to enable this feature',
						'default'     => true,
					),
					array(
						'name'        => 'theme_color',
						'type'        => 'color',
						'label'       => 'Theme Color',
						'description' => 'Choose your theme color',
						'default'     => '#0073aa',
					),
					array(
						'name'        => 'contact_email',
						'type'        => 'email',
						'label'       => 'Contact Email',
						'description' => 'Email address for contact',
						'placeholder' => 'you@example.com',
						'required'    => true,
					),
					array(
						'name'          => 'welcome_message',
						'type'          => 'wysiwyg',
						'label'         => 'Welcome Message',
						'description'   => 'Rich text welcome message for visitors',
						'media_buttons' => true,
						'textarea_rows' => 8,
					),
					array(
						'name'            => 'license_key',
						'type'            => 'text',
						'label'           => 'License Key',
						'description'     => 'Your plugin license key (saved without page prefix)',
						'placeholder'     => 'XXXX-XXXX-XXXX-XXXX',
						'use_name_prefix' => false,
					),
				),
			),
		),
	);

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'settings_array_init' );
