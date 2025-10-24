<?php
/**
 * FieldInterface for WP-CMF
 *
 * Defines the contract that all field types must implement.
 * Provides methods for rendering, validation, sanitization, and schema generation.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field;

/**
 * FieldInterface - Contract for all field types
 *
 * All field classes must implement this interface to ensure
 * consistent behavior across different field types.
 */
interface FieldInterface {

	/**
	 * Render the field HTML
	 *
	 * @param mixed $value Current field value.
	 * @return string HTML output for the field.
	 */
	public function render( $value = null ): string;

	/**
	 * Sanitize the input value
	 *
	 * @param mixed $input Raw input value.
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $input );

	/**
	 * Validate the input value
	 *
	 * @param mixed $input Input value to validate.
	 * @return array Array with 'valid' (bool) and 'errors' (array) keys.
	 */
	public function validate( $input ): array;

	/**
	 * Get the field name
	 *
	 * @return string Field name/identifier.
	 */
	public function get_name(): string;

	/**
	 * Get the field label
	 *
	 * @return string Field label for display.
	 */
	public function get_label(): string;

	/**
	 * Get the field type
	 *
	 * @return string Field type identifier.
	 */
	public function get_type(): string;

	/**
	 * Get the field schema
	 *
	 * Returns schema information for JSON schema generation
	 * and documentation purposes.
	 *
	 * @return array<string, mixed> Schema definition.
	 */
	public function get_schema(): array;

	/**
	 * Get field configuration
	 *
	 * @param string $key     Configuration key.
	 * @param mixed  $default Default value if key not found.
	 * @return mixed Configuration value.
	 */
	public function get_config( string $key, $default = null );

	/**
	 * Set field configuration
	 *
	 * @param string $key   Configuration key.
	 * @param mixed  $value Configuration value.
	 * @return self
	 */
	public function set_config( string $key, $value ): self;

	/**
	 * Enqueue field assets (CSS and JS)
	 *
	 * This method is called when the field is being rendered,
	 * allowing fields to load their required stylesheets and scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void;
}
