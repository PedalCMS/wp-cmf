<?php
/**
 * Custom Slider Field Type
 *
 * This is an example of creating a custom field type by extending AbstractField.
 * SliderField creates an HTML5 range input with visual feedback and optional markers.
 *
 * @package CustomFieldType
 */

use Pedalcms\WpCmf\Field\AbstractField;

/**
 * SliderField class - HTML5 range input with enhancements
 *
 * Configuration options:
 * - min (number): Minimum value (default: 0)
 * - max (number): Maximum value (default: 100)
 * - step (number): Step increment (default: 1)
 * - unit (string): Unit to display after value (e.g., '%', 'px', 'ms')
 * - show_value (bool): Whether to display current value (default: true)
 * - marks (array): Optional markers to display below slider (e.g., [0 => 'Low', 50 => 'Med', 100 => 'High'])
 */
class SliderField extends AbstractField {

	/**
	 * Get the field type identifier
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'slider';
	}

	/**
	 * Render the slider field HTML
	 *
	 * @param mixed $value Current field value.
	 * @return string HTML output
	 */
	public function render( $value = null ): string {
		$name        = $this->get_name();
		$id          = $this->get_field_id();
		$min         = $this->get_config( 'min', 0 );
		$max         = $this->get_config( 'max', 100 );
		$step        = $this->get_config( 'step', 1 );
		$unit        = $this->get_config( 'unit', '' );
		$show_value  = $this->get_config( 'show_value', true );
		$marks       = $this->get_config( 'marks', [] );
		$default     = $this->get_config( 'default', $min );
		$description = $this->get_config( 'description', '' );

		// Use provided value or default
		$current_value = ( null !== $value && '' !== $value ) ? $value : $default;

		// Build attributes
		$attrs = [
			'type'  => 'range',
			'id'    => $id,
			'name'  => $name,
			'min'   => $min,
			'max'   => $max,
			'step'  => $step,
			'value' => $current_value,
			'class' => 'wp-cmf-slider',
		];

		// Build HTML
		$html = $this->render_wrapper_start();

		// Label
		$html .= $this->render_label();

		// Slider container
		$html .= '<div class="wp-cmf-slider-container">';

		// Range input
		$html .= '<input' . $this->build_attributes( $attrs ) . ' />';

		// Value display
		if ( $show_value ) {
			$html .= sprintf(
				'<span class="wp-cmf-slider-value" id="%s-value">%s%s</span>',
				$this->esc_attr( $id ),
				$this->esc_html( $current_value ),
				$this->esc_html( $unit )
			);
		}

		// Marks (visual indicators below slider)
		if ( ! empty( $marks ) && is_array( $marks ) ) {
			$html .= '<div class="wp-cmf-slider-marks">';
			foreach ( $marks as $mark_value => $mark_label ) {
				$position = ( ( $mark_value - $min ) / ( $max - $min ) ) * 100;
				$html    .= sprintf(
					'<span class="wp-cmf-slider-mark" style="left: %s%%;" data-value="%s">%s</span>',
					$this->esc_attr( $position ),
					$this->esc_attr( $mark_value ),
					$this->esc_html( $mark_label )
				);
			}
			$html .= '</div>';
		}

		$html .= '</div>'; // .wp-cmf-slider-container

		// Description
		if ( $description ) {
			$html .= $this->render_description();
		}

		$html .= $this->render_wrapper_end();

		return $html;
	}

	/**
	 * Sanitize the slider value
	 *
	 * @param mixed $value Value to sanitize.
	 * @return float|int Sanitized numeric value
	 */
	public function sanitize( $value ) {
		// Convert to number
		$sanitized = is_numeric( $value ) ? (float) $value : 0;

		// Get min/max constraints
		$min = $this->get_config( 'min', 0 );
		$max = $this->get_config( 'max', 100 );

		// Clamp value between min and max
		$sanitized = max( $min, min( $max, $sanitized ) );

		// Round to step if specified
		$step = $this->get_config( 'step', 1 );
		if ( $step > 0 ) {
			$sanitized = round( $sanitized / $step ) * $step;
		}

		return $sanitized;
	}

	/**
	 * Validate the slider value
	 *
	 * @param mixed $value Value to validate.
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate( $value ): array {
		$errors = [];

		// Check if value is numeric
		if ( ! is_numeric( $value ) ) {
			$errors[] = sprintf(
				'%s must be a numeric value.',
				$this->get_label()
			);
			return [
				'valid'  => false,
				'errors' => $errors,
			];
		}

		$numeric_value = (float) $value;
		$min           = $this->get_config( 'min', 0 );
		$max           = $this->get_config( 'max', 100 );

		// Check min constraint
		if ( $numeric_value < $min ) {
			$errors[] = sprintf(
				'%s must be at least %s.',
				$this->get_label(),
				$min
			);
		}

		// Check max constraint
		if ( $numeric_value > $max ) {
			$errors[] = sprintf(
				'%s must be no more than %s.',
				$this->get_label(),
				$max
			);
		}

		return [
			'valid'  => empty( $errors ),
			'errors' => $errors,
		];
	}

	/**
	 * Enqueue assets for the slider field
	 *
	 * This method is called automatically by the Registrar on admin_enqueue_scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		// Enqueue JavaScript for live value updates
		wp_add_inline_script(
			'jquery',
			"
			jQuery(document).ready(function($) {
				// Update value display when slider changes
				$('.wp-cmf-slider').on('input change', function() {
					var slider = $(this);
					var value = slider.val();
					var unit = slider.closest('.wp-cmf-slider-container').find('.wp-cmf-slider-value').text().replace(/[0-9.]/g, '');
					slider.closest('.wp-cmf-slider-container').find('.wp-cmf-slider-value').text(value + unit);
				});
			});
			"
		);

		// Enqueue CSS for styling
		wp_add_inline_style(
			'wp-admin',
			"
			.wp-cmf-slider-container {
				margin: 10px 0;
			}
			.wp-cmf-slider {
				width: 100%;
				max-width: 400px;
				vertical-align: middle;
			}
			.wp-cmf-slider-value {
				display: inline-block;
				margin-left: 15px;
				font-weight: 600;
				font-size: 14px;
				color: #2271b1;
				min-width: 60px;
			}
			.wp-cmf-slider-marks {
				position: relative;
				width: 100%;
				max-width: 400px;
				height: 20px;
				margin-top: 5px;
			}
			.wp-cmf-slider-mark {
				position: absolute;
				font-size: 11px;
				color: #666;
				transform: translateX(-50%);
				white-space: nowrap;
			}
			input[type='range'].wp-cmf-slider {
				-webkit-appearance: none;
				appearance: none;
				height: 6px;
				background: #ddd;
				border-radius: 3px;
				outline: none;
			}
			input[type='range'].wp-cmf-slider::-webkit-slider-thumb {
				-webkit-appearance: none;
				appearance: none;
				width: 18px;
				height: 18px;
				background: #2271b1;
				border-radius: 50%;
				cursor: pointer;
			}
			input[type='range'].wp-cmf-slider::-moz-range-thumb {
				width: 18px;
				height: 18px;
				background: #2271b1;
				border-radius: 50%;
				cursor: pointer;
				border: none;
			}
			input[type='range'].wp-cmf-slider::-webkit-slider-thumb:hover {
				background: #135e96;
			}
			input[type='range'].wp-cmf-slider::-moz-range-thumb:hover {
				background: #135e96;
			}
			"
		);
	}

	/**
	 * Get JSON schema for this field type
	 *
	 * @return array Schema definition
	 */
	public function get_schema(): array {
		return [
			'type'       => 'slider',
			'properties' => [
				'min'        => [
					'type'        => 'number',
					'description' => 'Minimum value',
				],
				'max'        => [
					'type'        => 'number',
					'description' => 'Maximum value',
				],
				'step'       => [
					'type'        => 'number',
					'description' => 'Step increment',
				],
				'unit'       => [
					'type'        => 'string',
					'description' => 'Unit to display after value',
				],
				'show_value' => [
					'type'        => 'boolean',
					'description' => 'Whether to show current value',
				],
				'marks'      => [
					'type'        => 'object',
					'description' => 'Value markers to display',
				],
			],
		];
	}

}
