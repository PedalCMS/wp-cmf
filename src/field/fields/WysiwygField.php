<?php
/**
 * WysiwygField - WordPress WYSIWYG editor field
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field\Fields;

use Pedalcms\WpCmf\Field\AbstractField;

/**
 * WysiwygField class
 *
 * Renders a WordPress WYSIWYG editor using wp_editor().
 * Supports media buttons, teeny mode, and custom editor settings.
 */
class WysiwygField extends AbstractField {

	/**
	 * Get field type defaults
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		return array_merge(
			parent::get_defaults(),
			array(
				'media_buttons' => true,
				'teeny'         => false,
				'textarea_rows' => 10,
				'editor_class'  => '',
				'wpautop'       => true,
				'quicktags'     => true,
			)
		);
	}

	/**
	 * Render the WYSIWYG field
	 *
	 * @param mixed $value Current field value.
	 * @return string HTML output.
	 */
	public function render( $value = null ): string {
		$output  = $this->render_wrapper_start();
		$output .= $this->render_label();

		// Get editor settings
		$editor_id = $this->get_field_id();
		$settings  = array(
			'media_buttons' => $this->config['media_buttons'] ?? true,
			'teeny'         => $this->config['teeny'] ?? false,
			'textarea_rows' => $this->config['textarea_rows'] ?? 10,
			'textarea_name' => $this->name,
			'editor_class'  => $this->config['editor_class'] ?? '',
			'wpautop'       => $this->config['wpautop'] ?? true,
			'quicktags'     => $this->config['quicktags'] ?? true,
		);

		// Get the content value
		$content = $value ?? $this->config['default'] ?? '';

		// Buffer the editor output
		ob_start();

		// Use wp_editor if available, otherwise render textarea
		if ( function_exists( 'wp_editor' ) ) {
			wp_editor( $content, $editor_id, $settings );
		} else {
			// Fallback for non-WordPress environments
			echo '<textarea id="' . $this->esc_attr( $editor_id ) . '" '
				. 'name="' . $this->esc_attr( $this->name ) . '" '
				. 'rows="' . $this->esc_attr( (string) $settings['textarea_rows'] ) . '" '
				. 'class="large-text">'
				. $this->esc_html( $content )
				. '</textarea>';
		}

		$output .= ob_get_clean();
		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		return $output;
	}

	/**
	 * Sanitize the WYSIWYG field value
	 *
	 * Uses wp_kses_post to allow safe HTML.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return string Sanitized value.
	 */
	public function sanitize( $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}

		// Use wp_kses_post if available to allow safe HTML
		if ( function_exists( 'wp_kses_post' ) ) {
			return wp_kses_post( $value );
		}

		// Fallback: strip all tags
		return strip_tags( $value );
	}

	/**
	 * Validate the WYSIWYG field value
	 *
	 * @param mixed $input Input value.
	 * @return array Validation result.
	 */
	public function validate( $input ): array {
		$errors = array();

		// Check required
		if ( ! empty( $this->config['required'] ) && empty( $input ) ) {
			$errors[] = $this->translate( 'This field is required.', 'wp-cmf' );
		}

		// Check minimum length
		if ( ! empty( $this->config['min'] ) && strlen( (string) $input ) < $this->config['min'] ) {
			$errors[] = sprintf(
				$this->translate( 'Content must be at least %d characters.', 'wp-cmf' ),
				$this->config['min']
			);
		}

		// Check maximum length
		if ( ! empty( $this->config['max'] ) && strlen( (string) $input ) > $this->config['max'] ) {
			$errors[] = sprintf(
				$this->translate( 'Content must not exceed %d characters.', 'wp-cmf' ),
				$this->config['max']
			);
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Get field schema for JSON validation
	 *
	 * @return array<string, mixed>
	 */
	public function get_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'media_buttons' => array( 'type' => 'boolean' ),
				'teeny'         => array( 'type' => 'boolean' ),
				'textarea_rows' => array(
					'type'    => 'integer',
					'minimum' => 1,
					'maximum' => 50,
				),
				'editor_class'  => array( 'type' => 'string' ),
				'wpautop'       => array( 'type' => 'boolean' ),
				'quicktags'     => array( 'type' => 'boolean' ),
				'min'           => array(
					'type'    => 'integer',
					'minimum' => 0,
				),
				'max'           => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
			),
		);
	}
}
