<?php
/**
 * Example: Custom Field with Asset Enqueuing
 *
 * This example demonstrates how to create a custom field type that
 * enqueues its own CSS and JavaScript files.
 *
 * @package Pedalcms\WpCmf
 */

// Require Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\Fields\ColorField;
use Pedalcms\WpCmf\Core\Manager;

/**
 * Example Custom Field with Asset Enqueuing
 *
 * This field demonstrates how to enqueue custom CSS and JS files
 * for advanced field functionality.
 */
class CustomSliderField extends AbstractField {

	/**
	 * Enqueue field assets
	 *
	 * This method is automatically called by the Registrar when
	 * the field is being rendered on an admin page.
	 */
	public function enqueue_assets(): void {
		// Enqueue custom CSS
		wp_enqueue_style(
			'custom-slider-field',
			plugin_dir_url( __FILE__ ) . 'assets/slider.css',
			[],
			'1.0.0'
		);

		// Enqueue custom JavaScript
		wp_enqueue_script(
			'custom-slider-field',
			plugin_dir_url( __FILE__ ) . 'assets/slider.js',
			[ 'jquery' ],
			'1.0.0',
			true
		);

		// Pass data to JavaScript
		wp_localize_script(
			'custom-slider-field',
			'sliderFieldData',
			[
				'min'  => $this->config['min'] ?? 0,
				'max'  => $this->config['max'] ?? 100,
				'step' => $this->config['step'] ?? 1,
			]
		);
	}

	/**
	 * Render the slider field
	 */
	public function render( $value = null ): string {
		$output       = $this->render_wrapper_start();
		$output      .= $this->render_label();
		$field_value  = $value ?? $this->config['default'] ?? 0;

		$attributes = [
			'type'  => 'range',
			'id'    => $this->get_field_id(),
			'name'  => $this->name,
			'value' => $field_value,
			'class' => 'custom-slider-field',
			'min'   => $this->config['min'] ?? 0,
			'max'   => $this->config['max'] ?? 100,
			'step'  => $this->config['step'] ?? 1,
		];

		$output .= '<input' . $this->build_attributes( $attributes ) . ' />';
		$output .= '<span class="slider-value">' . $this->esc_html( $field_value ) . '</span>';
		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		return $output;
	}
}

// Example 1: Using the built-in ColorField (has asset enqueuing)
$manager = Manager::init();

$manager->get_registrar()->add_settings_page(
	'theme-settings',
	[
		'title'      => 'Theme Settings',
		'menu_title' => 'Theme',
		'capability' => 'manage_options',
	]
);

// ColorField automatically enqueues WordPress color picker
$color_field = new ColorField(
	'primary_color',
	'color',
	[
		'label'         => 'Primary Color',
		'description'   => 'Choose your theme primary color',
		'default'       => '#007cba',
		'use_wp_picker' => true,  // Enable WordPress color picker
	]
);

// Add the field to the settings page
$manager->get_registrar()->add_fields( 'theme-settings', [ 'primary_color' => $color_field ] );

// Example 2: Custom field with custom assets
$slider_field = new CustomSliderField(
	'brightness',
	'slider',
	[
		'label'       => 'Brightness',
		'description' => 'Adjust the brightness level',
		'min'         => 0,
		'max'         => 100,
		'step'        => 5,
		'default'     => 50,
	]
);

$manager->get_registrar()->add_fields( 'theme-settings', [ 'brightness' => $slider_field ] );

// Example 3: Hooking into common assets
add_action( 'wp_cmf_enqueue_common_assets', function() {
	// Enqueue common styles for all WP-CMF fields
	wp_enqueue_style(
		'my-cmf-common',
		plugin_dir_url( __FILE__ ) . 'assets/common.css',
		[],
		'1.0.0'
	);
} );

// The assets will be automatically enqueued when:
// 1. On the settings page (when rendering fields)
// 2. On CPT edit screens (when rendering meta box fields)
// 3. Any admin page where WP-CMF fields are present
