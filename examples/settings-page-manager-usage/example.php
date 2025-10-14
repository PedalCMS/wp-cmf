<?php
/**
 * Example: Advanced Manager Usage with Settings Pages
 *
 * Demonstrates using Manager patterns and advanced configurations
 * for settings pages in WP-CMF.
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Settings\SettingsPage;

// Method 1: Using Manager's get_registrar() method
$manager = Manager::init();
$registrar = $manager->get_registrar();

// Add settings pages using from_array factory
$registrar->add_settings_page( 'plugin-general', [
	'page_title' => 'Plugin General Settings',
	'menu_title' => 'General',
	'capability' => 'manage_options',
	'icon_url'   => 'dashicons-admin-settings',
	'position'   => 75,
] );

// Method 2: Create instances and add them directly
$advanced_page = SettingsPage::from_array( 'plugin-advanced', [
	'page_title' => 'Advanced Settings',
	'menu_title' => 'Advanced',
	'parent_slug' => 'plugin-general',
	'capability' => 'manage_options',
] );

$registrar->add_settings_page_instance( $advanced_page );

// Method 3: Fluent interface with instance
$api_page = new SettingsPage( 'plugin-api' );
$api_page
	->set_page_title( 'API Configuration' )
	->set_menu_title( 'API' )
	->set_parent( 'plugin-general' )
	->set_capability( 'manage_options' )
	->set_callback( function() {
		?>
		<div class="wrap">
			<h1>API Configuration</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'plugin_api_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="api_key">API Key</label></th>
						<td>
							<input name="api_key" type="text" id="api_key" 
								   value="<?php echo esc_attr( get_option( 'api_key' ) ); ?>" 
								   class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="api_secret">API Secret</label></th>
						<td>
							<input name="api_secret" type="password" id="api_secret" 
								   value="<?php echo esc_attr( get_option( 'api_secret' ) ); ?>" 
								   class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="api_endpoint">API Endpoint</label></th>
						<td>
							<input name="api_endpoint" type="url" id="api_endpoint" 
								   value="<?php echo esc_attr( get_option( 'api_endpoint', 'https://api.example.com' ) ); ?>" 
								   class="regular-text" />
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	} );

$registrar->add_settings_page_instance( $api_page );

// Example: Retrieving registered pages
$all_pages = $registrar->get_settings_pages();

// Example: Checking if a page is registered
if ( isset( $all_pages['plugin-api'] ) ) {
	$page = $all_pages['plugin-api'];
	
	// Access page properties
	$page_id = $page->get_page_id();
	$menu_slug = $page->get_menu_slug();
	$is_submenu = $page->is_submenu();
	
	// Get configuration
	$page_title = $page->get_config( 'page_title' );
	$capability = $page->get_config( 'capability' );
}

// Example: Conditional page registration based on settings
if ( get_option( 'enable_debug_page', false ) ) {
	$debug_page = new SettingsPage( 'plugin-debug' );
	$debug_page
		->set_page_title( 'Debug Information' )
		->set_menu_title( 'Debug' )
		->set_parent( 'plugin-general' )
		->set_capability( 'manage_options' )
		->set_callback( function() {
			?>
			<div class="wrap">
				<h1>Debug Information</h1>
				<h2>System Info</h2>
				<pre><?php
					echo 'PHP Version: ' . PHP_VERSION . "\n";
					echo 'WordPress Version: ' . get_bloginfo( 'version' ) . "\n";
					echo 'Active Theme: ' . wp_get_theme()->get( 'Name' ) . "\n";
				?></pre>
			</div>
			<?php
		} );
	
	$registrar->add_settings_page_instance( $debug_page );
}

// Example: Dynamic page creation from configuration
$page_configs = [
	'reports' => [
		'page_title' => 'Reports',
		'menu_title' => 'Reports',
		'parent_slug' => 'plugin-general',
	],
	'logs' => [
		'page_title' => 'Activity Logs',
		'menu_title' => 'Logs',
		'parent_slug' => 'plugin-general',
	],
];

foreach ( $page_configs as $page_id => $config ) {
	$registrar->add_settings_page( "plugin-{$page_id}", $config );
}
