<?php
/**
 * Complete Settings Page with Fields Example
 *
 * This example demonstrates a production-ready settings page with:
 * - Multiple field types (text, email, textarea, select, checkbox, radio, color, number)
 * - Field organization into sections
 * - Field validation and sanitization
 * - Settings saving and retrieval
 * - Custom rendering with tabs
 *
 * @package Pedalcms\WpCmf\Examples
 */

namespace Pedalcms\WpCmf\Examples\SettingsWithFields;

// Require Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Field\FieldFactory;

// Initialize WP-CMF
$manager = Manager::init();
$registrar = $manager->get_registrar();

/**
 * Register the settings page
 */
add_action(
	'admin_menu',
	function () use ( $registrar ) {
		// Create a top-level settings page
		$registrar->add_settings_page(
			'my-plugin-settings',
			[
				'page_title' => 'My Plugin Settings',
				'menu_title' => 'My Plugin',
				'capability' => 'manage_options',
				'callback'   => function () {
					render_settings_page();
				},
			]
		);
	}
);

/**
 * Register all settings fields
 */
add_action(
	'admin_init',
	function () use ( $registrar ) {
		register_settings_sections();
		register_general_fields( $registrar );
		register_appearance_fields( $registrar );
		register_email_fields( $registrar );
		register_advanced_fields( $registrar );
	}
);

/**
 * Register WordPress settings sections
 */
function register_settings_sections() {
	add_settings_section(
		'general_section',
		'General Settings',
		function () {
			echo '<p>Configure general plugin settings.</p>';
		},
		'my-plugin-settings'
	);

	add_settings_section(
		'appearance_section',
		'Appearance Settings',
		function () {
			echo '<p>Customize the appearance of your plugin.</p>';
		},
		'my-plugin-settings'
	);

	add_settings_section(
		'email_section',
		'Email Settings',
		function () {
			echo '<p>Configure email notifications and templates.</p>';
		},
		'my-plugin-settings'
	);

	add_settings_section(
		'advanced_section',
		'Advanced Settings',
		function () {
			echo '<p>Advanced configuration options.</p>';
		},
		'my-plugin-settings'
	);
}

/**
 * Register general settings fields
 */
function register_general_fields( $registrar ) {
	$fields = FieldFactory::create_multiple(
		[
			'site_name'        => [
				'type'        => 'text',
				'label'       => 'Site Name',
				'description' => 'The name of your site (used in emails and notifications)',
				'default'     => get_bloginfo( 'name' ),
				'required'    => true,
				'placeholder' => 'Enter site name',
			],
			'tagline'          => [
				'type'        => 'text',
				'label'       => 'Tagline',
				'description' => 'A short description of your site',
				'default'     => get_bloginfo( 'description' ),
				'maxlength'   => 100,
			],
			'welcome_message'  => [
				'type'        => 'textarea',
				'label'       => 'Welcome Message',
				'description' => 'Message shown to new users',
				'rows'        => 5,
				'placeholder' => 'Welcome to our site!',
			],
			'enable_features'  => [
				'type'        => 'checkbox',
				'label'       => 'Enable Features',
				'description' => 'Select which features to enable',
				'options'     => [
					'comments'      => 'Enable Comments',
					'social_share'  => 'Enable Social Sharing',
					'analytics'     => 'Enable Analytics',
					'notifications' => 'Enable Email Notifications',
				],
				'layout'      => 'stacked',
			],
			'default_language' => [
				'type'        => 'select',
				'label'       => 'Default Language',
				'description' => 'Choose the default language for your site',
				'options'     => [
					'en' => 'English',
					'es' => 'Spanish',
					'fr' => 'French',
					'de' => 'German',
					'it' => 'Italian',
				],
				'default'     => 'en',
			],
		]
	);

	// Register each field with WordPress settings API
	foreach ( $fields as $field ) {
		$field_name = $field->get_name();

		register_setting( 'my-plugin-settings', $field_name );

		add_settings_field(
			$field_name,
			$field->get_label(),
			function () use ( $field, $field_name ) {
				$value = get_option( $field_name, $field->get_config( 'default' ) );
				echo $field->render( $value );
			},
			'my-plugin-settings',
			'general_section'
		);
	}
}

/**
 * Register appearance settings fields
 */
function register_appearance_fields( $registrar ) {
	$fields = FieldFactory::create_multiple(
		[
			'theme_style'    => [
				'type'        => 'radio',
				'label'       => 'Theme Style',
				'description' => 'Choose your preferred theme style',
				'options'     => [
					'light'   => 'Light',
					'dark'    => 'Dark',
					'auto'    => 'Auto (based on system)',
					'custom'  => 'Custom',
				],
				'default'     => 'light',
				'layout'      => 'inline',
			],
			'primary_color'  => [
				'type'        => 'color',
				'label'       => 'Primary Color',
				'description' => 'Main color used throughout the plugin',
				'default'     => '#3498db',
			],
			'secondary_color' => [
				'type'        => 'color',
				'label'       => 'Secondary Color',
				'description' => 'Secondary accent color',
				'default'     => '#2ecc71',
			],
			'items_per_page' => [
				'type'        => 'number',
				'label'       => 'Items Per Page',
				'description' => 'Number of items to display per page',
				'min'         => 5,
				'max'         => 100,
				'step'        => 5,
				'default'     => 10,
			],
			'enable_animations' => [
				'type'        => 'checkbox',
				'label'       => 'Enable Animations',
				'description' => 'Enable smooth animations and transitions',
			],
		]
	);

	foreach ( $fields as $field ) {
		$field_name = $field->get_name();

		register_setting( 'my-plugin-settings', $field_name );

		add_settings_field(
			$field_name,
			$field->get_label(),
			function () use ( $field, $field_name ) {
				$value = get_option( $field_name, $field->get_config( 'default' ) );
				echo $field->render( $value );
			},
			'my-plugin-settings',
			'appearance_section'
		);
	}
}

/**
 * Register email settings fields
 */
function register_email_fields( $registrar ) {
	$fields = FieldFactory::create_multiple(
		[
			'admin_email'        => [
				'type'        => 'email',
				'label'       => 'Admin Email',
				'description' => 'Email address for administrative notifications',
				'default'     => get_option( 'admin_email' ),
				'required'    => true,
			],
			'support_email'      => [
				'type'        => 'email',
				'label'       => 'Support Email',
				'description' => 'Email address shown to users for support',
				'placeholder' => 'support@example.com',
			],
			'email_subject'      => [
				'type'        => 'text',
				'label'       => 'Email Subject Prefix',
				'description' => 'Prefix added to all email subjects',
				'default'     => '[My Plugin]',
				'maxlength'   => 50,
			],
			'email_template'     => [
				'type'        => 'select',
				'label'       => 'Email Template',
				'description' => 'Choose email template style',
				'options'     => [
					'plain'      => 'Plain Text',
					'basic'      => 'Basic HTML',
					'modern'     => 'Modern',
					'newsletter' => 'Newsletter Style',
				],
				'default'     => 'basic',
			],
			'email_footer'       => [
				'type'        => 'textarea',
				'label'       => 'Email Footer',
				'description' => 'Text shown at the bottom of all emails',
				'rows'        => 3,
				'placeholder' => 'Thank you for using our service!',
			],
		]
	);

	foreach ( $fields as $field ) {
		$field_name = $field->get_name();

		register_setting( 'my-plugin-settings', $field_name );

		add_settings_field(
			$field_name,
			$field->get_label(),
			function () use ( $field, $field_name ) {
				$value = get_option( $field_name, $field->get_config( 'default' ) );
				echo $field->render( $value );
			},
			'my-plugin-settings',
			'email_section'
		);
	}
}

/**
 * Register advanced settings fields
 */
function register_advanced_fields( $registrar ) {
	$fields = FieldFactory::create_multiple(
		[
			'api_key'            => [
				'type'        => 'password',
				'label'       => 'API Key',
				'description' => 'Your API key for external service integration',
				'placeholder' => 'Enter API key',
			],
			'api_endpoint'       => [
				'type'        => 'url',
				'label'       => 'API Endpoint',
				'description' => 'URL of the API endpoint',
				'placeholder' => 'https://api.example.com',
			],
			'cache_duration'     => [
				'type'        => 'number',
				'label'       => 'Cache Duration (seconds)',
				'description' => 'How long to cache API responses',
				'min'         => 0,
				'max'         => 86400,
				'step'        => 60,
				'default'     => 3600,
			],
			'debug_mode'         => [
				'type'        => 'checkbox',
				'label'       => 'Enable Debug Mode',
				'description' => 'Log detailed debugging information',
			],
			'data_retention'     => [
				'type'        => 'number',
				'label'       => 'Data Retention (days)',
				'description' => 'Number of days to keep logs and data',
				'min'         => 1,
				'max'         => 365,
				'default'     => 30,
			],
		]
	);

	foreach ( $fields as $field ) {
		$field_name = $field->get_name();

		register_setting( 'my-plugin-settings', $field_name );

		add_settings_field(
			$field_name,
			$field->get_label(),
			function () use ( $field, $field_name ) {
				$value = get_option( $field_name, $field->get_config( 'default' ) );
				echo $field->render( $value );
			},
			'my-plugin-settings',
			'advanced_section'
		);
	}
}

/**
 * Render the settings page with custom HTML
 */
function render_settings_page() {
	// Check user permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'my-plugin' ) );
	}

	// Handle settings save
	if ( isset( $_POST['my_plugin_settings_nonce'] ) ) {
		if ( ! wp_verify_nonce( $_POST['my_plugin_settings_nonce'], 'my_plugin_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'my-plugin' ) );
		}

		// Settings are automatically saved by WordPress settings API
		echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<p class="description">
			Configure your plugin settings below. All changes are saved automatically.
		</p>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'my-plugin-settings' );
			do_settings_sections( 'my-plugin-settings' );
			wp_nonce_field( 'my_plugin_save_settings', 'my_plugin_settings_nonce' );
			submit_button( 'Save Settings' );
			?>
		</form>

		<div class="settings-info" style="margin-top: 30px; padding: 20px; background: #f0f0f1; border-left: 4px solid #2271b1;">
			<h3>How to Use These Settings</h3>
			<ul>
				<li><strong>General Settings:</strong> Configure basic site information and features</li>
				<li><strong>Appearance Settings:</strong> Customize colors, theme, and display options</li>
				<li><strong>Email Settings:</strong> Set up email notifications and templates</li>
				<li><strong>Advanced Settings:</strong> Configure API integration and debugging</li>
			</ul>
		</div>

		<div class="settings-export" style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #c3c4c7;">
			<h3>Export/Import Settings</h3>
			<p>
				<button type="button" class="button" onclick="exportSettings()">Export Settings</button>
				<button type="button" class="button" onclick="importSettings()">Import Settings</button>
			</p>
		</div>
	</div>

	<script>
	function exportSettings() {
		// Collect all settings
		var settings = {};
		<?php
		$all_settings = [
			'site_name', 'tagline', 'welcome_message', 'enable_features', 'default_language',
			'theme_style', 'primary_color', 'secondary_color', 'items_per_page', 'enable_animations',
			'admin_email', 'support_email', 'email_subject', 'email_template', 'email_footer',
			'api_key', 'api_endpoint', 'cache_duration', 'debug_mode', 'data_retention'
		];
		foreach ( $all_settings as $setting ) {
			$value = get_option( $setting );
			if ( $value !== false ) {
				echo "settings['" . esc_js( $setting ) . "'] = " . wp_json_encode( $value ) . ";\n";
			}
		}
		?>

		var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(settings, null, 2));
		var downloadAnchor = document.createElement('a');
		downloadAnchor.setAttribute("href", dataStr);
		downloadAnchor.setAttribute("download", "my-plugin-settings.json");
		document.body.appendChild(downloadAnchor);
		downloadAnchor.click();
		downloadAnchor.remove();
	}

	function importSettings() {
		alert('Import functionality would allow you to upload a JSON file with settings.');
	}
	</script>

	<style>
	.form-table th {
		width: 250px;
		font-weight: 600;
	}
	.form-table td p.description {
		margin-top: 5px;
		font-style: italic;
		color: #646970;
	}
	</style>
	<?php
}
