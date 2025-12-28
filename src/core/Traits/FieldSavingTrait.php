<?php
/**
 * Field Saving Trait
 *
 * Provides common field saving functionality shared across handlers.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core\Traits;

use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * Trait FieldSavingTrait
 *
 * Common field saving logic for CPT and Settings handlers.
 */
trait FieldSavingTrait {

	/**
	 * Apply before-save filters to a value
	 *
	 * @param mixed  $value      The value to filter.
	 * @param string $field_name The field name.
	 * @param string $context    The context (post type or page ID).
	 * @return mixed The filtered value, or null to skip saving.
	 */
	protected function apply_before_save_filters( $value, string $field_name, string $context ) {
		if ( ! function_exists( 'apply_filters' ) ) {
			return $value;
		}

		// Apply global filter
		$value = apply_filters( 'wp_cmf_before_save_field', $value, $field_name, $context );

		if ( null === $value ) {
			return null;
		}

		// Apply field-specific filter
		return apply_filters( 'wp_cmf_before_save_field_' . $field_name, $value );
	}

	/**
	 * Sanitize and validate a field value
	 *
	 * @param FieldInterface $field Field instance.
	 * @param mixed          $value Raw value.
	 * @return array{value: mixed, valid: bool, errors: array}
	 */
	protected function sanitize_and_validate( FieldInterface $field, $value ): array {
		$sanitized = $field->sanitize( $value );
		$validation = $field->validate( $sanitized );

		return array(
			'value'  => $sanitized,
			'valid'  => $validation['valid'] ?? false,
			'errors' => $validation['errors'] ?? array(),
		);
	}

	/**
	 * Get submitted value from POST data
	 *
	 * @param string $option_name Primary option name to check.
	 * @param string $field_name  Fallback field name to check.
	 * @return mixed The submitted value or empty string.
	 */
	protected function get_submitted_value( string $option_name, string $field_name = '' ) {
		if ( isset( $_POST[ $option_name ] ) ) {
			return wp_unslash( $_POST[ $option_name ] );
		}

		if ( ! empty( $field_name ) && isset( $_POST[ $field_name ] ) ) {
			return wp_unslash( $_POST[ $field_name ] );
		}

		return '';
	}

	/**
	 * Add a settings error for display
	 *
	 * @param string $option_name Option name for error key.
	 * @param string $label       Field label for error message.
	 * @param array  $errors      Array of error messages.
	 * @return void
	 */
	protected function add_field_error( string $option_name, string $label, array $errors ): void {
		if ( ! function_exists( 'add_settings_error' ) ) {
			return;
		}

		add_settings_error(
			$option_name,
			$option_name . '_error',
			sprintf(
				/* translators: 1: field label, 2: error messages */
				__( '%1$s: %2$s', 'wp-cmf' ),
				$label,
				implode( ', ', $errors )
			),
			'error'
		);
	}

	/**
	 * Process container field and save nested fields
	 *
	 * @param ContainerFieldInterface $field   Container field.
	 * @param string                  $context Context identifier.
	 * @param callable                $save_callback Callback to save individual field.
	 * @return void
	 */
	protected function process_container_fields(
		ContainerFieldInterface $field,
		string $context,
		callable $save_callback
	): void {
		$nested_configs = $field->get_nested_fields();

		foreach ( $nested_configs as $config ) {
			if ( empty( $config['name'] ) ) {
				continue;
			}

			try {
				$nested_field = FieldFactory::create( $config );

				if ( $nested_field instanceof ContainerFieldInterface ) {
					// Recursively process nested containers
					$this->process_container_fields( $nested_field, $context, $save_callback );
				} else {
					// Save regular field
					$save_callback( $nested_field, $context );
				}
			} catch ( \InvalidArgumentException $e ) {
				continue;
			}
		}
	}
}
