<?php
/**
 * CheckboxField - Checkbox input field
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field\Fields;

use Pedalcms\WpCmf\Field\AbstractField;

/**
 * CheckboxField class
 *
 * Renders either a single checkbox or a group of checkboxes.
 * Single checkbox returns boolean, multiple checkboxes return array.
 */
class CheckboxField extends AbstractField {

	/**
	 * Get field type defaults
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		return array_merge(
			parent::get_defaults(),
			array(
				'type'    => 'checkbox',
				'options' => array(),  // Empty for single checkbox, array for multiple
				'inline'  => false,  // Display options inline or stacked
			)
		);
	}

	/**
	 * Render the checkbox field
	 *
	 * @param mixed $value Current field value.
	 * @return string HTML output.
	 */
	public function render( $value = null ): string {
		$output = $this->render_wrapper_start();

		$field_value = $value ?? $this->config['default'] ?? '';

		// Single checkbox
		if ( empty( $this->config['options'] ) ) {
			$output .= $this->render_single_checkbox( $field_value );
		} else {
			// Multiple checkboxes
			$output .= $this->render_label();
			$output .= $this->render_checkbox_group( $field_value );
		}

		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		return $output;
	}

	/**
	 * Render a single checkbox
	 *
	 * @param mixed $value Current value.
	 * @return string HTML output.
	 */
	protected function render_single_checkbox( $value ): string {
		$checked = ! empty( $value ) || $value === '1' || $value === 1 || $value === true;

		$attributes = array(
			'type'  => 'checkbox',
			'id'    => $this->get_field_id(),
			'name'  => $this->name,
			'value' => '1',
		);

		if ( $checked ) {
			$attributes['checked'] = true;
		}

		if ( ! empty( $this->config['disabled'] ) ) {
			$attributes['disabled'] = true;
		}

		$output  = '<label>';
		$output .= '<input' . $this->build_attributes( $attributes ) . ' />';
		$output .= ' ' . $this->esc_html( $this->get_label() );
		$output .= '</label>';

		return $output;
	}

	/**
	 * Render a group of checkboxes
	 *
	 * @param mixed $value Current value(s).
	 * @return string HTML output.
	 */
	protected function render_checkbox_group( $value ): string {
		$output         = '<fieldset>';
		$checked_values = is_array( $value ) ? $value : array( $value );

		$wrapper_class = 'wp-cmf-field-checkbox-group';
		if ( $this->config['inline'] ) {
			$wrapper_class .= ' inline';
		}
		$output .= '<div class="' . $this->esc_attr( $wrapper_class ) . '">';

		foreach ( $this->config['options'] as $opt_value => $opt_label ) {
			$checkbox_id = $this->get_field_id() . '-' . $this->sanitize_key( $opt_value );
			$is_checked  = in_array( $opt_value, $checked_values, true );

			$attributes = array(
				'type'  => 'checkbox',
				'id'    => $checkbox_id,
				'name'  => $this->name . '[]',
				'value' => $opt_value,
			);

			if ( $is_checked ) {
				$attributes['checked'] = true;
			}

			if ( ! empty( $this->config['disabled'] ) ) {
				$attributes['disabled'] = true;
			}

			$output .= '<label>';
			$output .= '<input' . $this->build_attributes( $attributes ) . ' />';
			$output .= ' ' . $this->esc_html( $opt_label );
			$output .= '</label>';

			if ( ! $this->config['inline'] ) {
				$output .= '<br />';
			}
		}

		$output .= '</div>';
		$output .= '</fieldset>';

		return $output;
	}

	/**
	 * Sanitize checkbox input
	 *
	 * @param mixed $input Input value.
	 * @return mixed
	 */
	public function sanitize( $input ) {
		// Single checkbox
		if ( empty( $this->config['options'] ) ) {
			return ! empty( $input ) ? '1' : '0';
		}

		// Multiple checkboxes
		if ( ! is_array( $input ) ) {
			return array();
		}

		// Filter to only allowed values
		$allowed_values = array_keys( $this->config['options'] );
		return array_intersect( $input, $allowed_values );
	}

	/**
	 * Sanitize key for use in HTML ID
	 *
	 * @param string $key Key to sanitize.
	 * @return string
	 */
	protected function sanitize_key( string $key ): string {
		if ( function_exists( 'sanitize_key' ) ) {
			return \sanitize_key( $key );
		}
		return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', $key ) );
	}
}
