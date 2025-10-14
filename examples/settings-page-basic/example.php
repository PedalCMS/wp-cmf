<?php
/**
 * Example: Basic Settings Page Usage
 *
 * Demonstrates how to create a simple top-level settings page
 * using the WP-CMF SettingsPage class.
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

// Initialize the WP-CMF Manager
$manager = Manager::init();

// Create a basic top-level settings page
$manager->get_registrar()->add_settings_page( 'my-settings', [
	'page_title' => 'My Plugin Settings',
	'menu_title' => 'My Settings',
	'capability' => 'manage_options',
	'icon_url'   => 'dashicons-admin-generic',
	'position'   => 80,
	'callback'   => function() {
		?>
		<div class="wrap">
			<h1>My Plugin Settings</h1>
			<p>Welcome to the settings page for my plugin.</p>
			<form method="post" action="options.php">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="my_option">My Option</label></th>
						<td>
							<input name="my_option" type="text" id="my_option"
								   value="<?php echo esc_attr( get_option( 'my_option' ) ); ?>"
								   class="regular-text" />
							<p class="description">Enter a value for this option.</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	},
] );
