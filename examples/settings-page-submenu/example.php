<?php

/**
 * Example: Submenu Settings Page
 *
 * Demonstrates how to create a submenu page under an existing
 * WordPress admin menu using the WP-CMF SettingsPage class.
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Settings\SettingsPage;

// Initialize the WP-CMF Manager
$manager = Manager::init();

// Example 1: Submenu under Settings
$settings_submenu = new SettingsPage( 'my-plugin-settings' );
$settings_submenu
	->set_page_title( 'My Plugin Settings' )
	->set_menu_title( 'My Plugin' )
	->set_parent( 'options-general.php' )  // Adds under Settings menu
	->set_capability( 'manage_options' )
	->set_callback(
		function () {
			?>
	<div class="wrap">
		<h1>My Plugin Settings</h1>
		<p>This page appears under Settings in the WordPress admin.</p>
	</div>
			<?php
		}
	);

$manager->get_registrar()->add_settings_page_instance( $settings_submenu );

// Example 2: Submenu under Tools
$tools_submenu = new SettingsPage( 'my-plugin-tools' );
$tools_submenu
	->set_page_title( 'My Plugin Tools' )
	->set_menu_title( 'My Tools' )
	->set_parent( 'tools.php' )  // Adds under Tools menu
	->set_capability( 'manage_options' )
	->set_callback(
		function () {
			?>
	<div class="wrap">
		<h1>My Plugin Tools</h1>
		<p>This page appears under Tools in the WordPress admin.</p>
		<h2>Available Tools</h2>
		<ul>
			<li>Tool 1: Data Import</li>
			<li>Tool 2: Data Export</li>
			<li>Tool 3: Cache Clear</li>
		</ul>
	</div>
			<?php
		}
	);

$manager->get_registrar()->add_settings_page_instance( $tools_submenu );

// Example 3: Submenu under custom CPT menu
// (This would be registered after a custom post type is created)
$cpt_submenu = new SettingsPage( 'book-settings' );
$cpt_submenu
	->set_page_title( 'Book Settings' )
	->set_menu_title( 'Settings' )
	->set_parent( 'edit.php?post_type=book' )  // Adds under Books CPT menu
	->set_capability( 'edit_posts' )
	->set_callback(
		function () {
			?>
	<div class="wrap">
		<h1>Book Settings</h1>
		<p>Configure settings specific to the Books post type.</p>
	</div>
			<?php
		}
	);

$manager->get_registrar()->add_settings_page_instance( $cpt_submenu );
