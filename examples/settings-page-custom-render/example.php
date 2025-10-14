<?php
/**
 * Example: Custom Rendering for Settings Pages
 *
 * Demonstrates advanced rendering techniques for settings pages
 * including custom classes, tabs, sections, and reusable rendering.
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Settings\SettingsPage;

/**
 * Custom renderer class for settings pages
 */
class MyPluginSettingsRenderer {
	/**
	 * Render the main settings page
	 */
	public function render_main_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php settings_errors(); ?>
			
			<h2 class="nav-tab-wrapper">
				<a href="?page=my-plugin-settings&tab=general" class="nav-tab nav-tab-active">General</a>
				<a href="?page=my-plugin-settings&tab=advanced" class="nav-tab">Advanced</a>
				<a href="?page=my-plugin-settings&tab=api" class="nav-tab">API</a>
			</h2>
			
			<form method="post" action="options.php">
				<?php
				settings_fields( 'my_plugin_settings' );
				do_settings_sections( 'my_plugin_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a dashboard page
	 */
	public function render_dashboard() {
		?>
		<div class="wrap">
			<h1>Plugin Dashboard</h1>
			
			<div class="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<h2 class="hndle"><span>Quick Stats</span></h2>
								<div class="inside">
									<p>Total Items: <strong>1,234</strong></p>
									<p>Active Users: <strong>567</strong></p>
									<p>Last Updated: <strong><?php echo esc_html( date( 'Y-m-d H:i:s' ) ); ?></strong></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a help/documentation page
	 */
	public function render_help_page() {
		?>
		<div class="wrap">
			<h1>Help &amp; Documentation</h1>
			
			<div class="card">
				<h2>Getting Started</h2>
				<p>Welcome to our plugin! Here are some quick tips to get you started:</p>
				<ol>
					<li>Configure your basic settings</li>
					<li>Set up your API credentials</li>
					<li>Test the connection</li>
					<li>Start using the features</li>
				</ol>
			</div>
			
			<div class="card">
				<h2>Common Issues</h2>
				<ul>
					<li><strong>Connection Failed:</strong> Check your API credentials</li>
					<li><strong>Missing Data:</strong> Ensure cron jobs are running</li>
					<li><strong>Slow Performance:</strong> Enable caching in settings</li>
				</ul>
			</div>
		</div>
		<?php
	}
}

// Initialize the manager and renderer
$manager = Manager::init();
$renderer = new MyPluginSettingsRenderer();

// Create top-level page with custom renderer
$main_page = new SettingsPage( 'my-plugin-settings' );
$main_page
	->set_page_title( 'My Plugin Settings' )
	->set_menu_title( 'My Plugin' )
	->set_icon( 'dashicons-admin-generic' )
	->set_capability( 'manage_options' )
	->set_callback( [ $renderer, 'render_main_page' ] );

$manager->get_registrar()->add_settings_page_instance( $main_page );

// Add dashboard submenu
$dashboard_page = new SettingsPage( 'my-plugin-dashboard' );
$dashboard_page
	->set_page_title( 'Dashboard' )
	->set_menu_title( 'Dashboard' )
	->set_parent( 'my-plugin-settings' )
	->set_capability( 'manage_options' )
	->set_callback( [ $renderer, 'render_dashboard' ] );

$manager->get_registrar()->add_settings_page_instance( $dashboard_page );

// Add help submenu
$help_page = new SettingsPage( 'my-plugin-help' );
$help_page
	->set_page_title( 'Help & Documentation' )
	->set_menu_title( 'Help' )
	->set_parent( 'my-plugin-settings' )
	->set_capability( 'manage_options' )
	->set_callback( [ $renderer, 'render_help_page' ] );

$manager->get_registrar()->add_settings_page_instance( $help_page );
