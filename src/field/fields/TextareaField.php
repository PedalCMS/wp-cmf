<?php
/**
 * TextareaField - Multi-line text input field
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field\Fields;

use Pedalcms\WpCmf\Field\AbstractField;

/**
 * TextareaField class
 *
 * Renders a textarea element for multi-line text input.
 * Supports rows, cols, placeholder, and maxlength attributes.
 */
class TextareaField extends AbstractField {

	/**
	 * Get field type defaults
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		return array_merge(
			parent::get_defaults(),
			array(
				'type'        => 'textarea',
				'rows'        => 5,
				'cols'        => 50,
				'placeholder' => '',
				'maxlength'   => '',
			)
		);
	}

	/**
	 * Render the textarea field
	 *
	 * @param mixed $value Current field value.
	 * @return string HTML output.
	 */
	public function render( $value = null ): string {
		$output  = $this->render_wrapper_start();
		$output .= $this->render_label();

		$attributes = array(
			'id'    => $this->get_field_id(),
			'name'  => $this->name,
			'class' => 'large-text',
			'rows'  => $this->config['rows'],
			'cols'  => $this->config['cols'],
		);

		if ( ! empty( $this->config['placeholder'] ) ) {
			$attributes['placeholder'] = $this->config['placeholder'];
		}

		if ( ! empty( $this->config['maxlength'] ) ) {
			$attributes['maxlength'] = $this->config['maxlength'];
		}

		if ( ! empty( $this->config['required'] ) ) {
			$attributes['required'] = true;
		}

		if ( ! empty( $this->config['readonly'] ) ) {
			$attributes['readonly'] = true;
		}

		if ( ! empty( $this->config['disabled'] ) ) {
			$attributes['disabled'] = true;
		}

		$field_value = $value ?? $this->config['default'] ?? '';

		$output .= '<textarea' . $this->build_attributes( $attributes ) . '>';
		$output .= $this->esc_html( $field_value );
		$output .= '</textarea>';
		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		return $output;
	}
}
