<?php
/**
 * GroupField for WP-CMF
 *
 * A container field that groups nested fields together in a section.
 * The group field itself doesn't store data - only nested fields save/load values.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field\fields;

use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * GroupField - Groups fields together in a section
 *
 * Configuration options:
 * - label: Optional section heading (default: empty)
 * - description: Optional section description (default: empty)
 * - fields: Array of field configurations to group together
 * - class: Optional CSS class for styling
 */
class GroupField extends AbstractField implements ContainerFieldInterface {

	/**
	 * Get default configuration values
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		$defaults = parent::get_defaults();
		return array_merge(
			$defaults,
			array(
				'label'       => '', // No label by default
				'description' => '', // No description by default
				'fields'      => array(),
			)
		);
	}

	/**
	 * Check if this is a container field
	 *
	 * @return bool
	 */
	public function is_container(): bool {
		return true;
	}

	/**
	 * Get all nested field configurations
	 *
	 * Extracts all field configurations from the group so they can be
	 * registered individually for saving/loading.
	 *
	 * @return array<array<string, mixed>>
	 */
	public function get_nested_fields(): array {
		return $this->config['fields'] ?? array();
	}

	/**
	 * Render the group field
	 *
	 * @param mixed $value Not used - container fields don't store values.
	 * @return string HTML output.
	 */
	public function render( $value = null ): string {
		global $post;

		$fields = $this->config['fields'] ?? array();

		if ( empty( $fields ) ) {
			return '';
		}

		// Determine context:
		// - For CPT metaboxes: use post ID from global $post
		// - For settings pages: use page_id passed as $value parameter
		if ( isset( $post ) && is_object( $post ) && isset( $post->ID ) ) {
			$context = $post->ID;
		} elseif ( is_string( $value ) ) {
			// Settings page: $value is the page_id
			$context = $value;
		} else {
			$context = null;
		}

		error_log( sprintf( 'WP-CMF GroupField render: name=%s, context=%s, fields_count=%d',
			$this->name,
			var_export( $context, true ),
			count( $fields )
		) );

		$output  = $this->render_wrapper_start();
		$output .= $this->render_label();
		$output .= $this->render_description();
		$output .= '<div class="wp-cmf-group-fields">';
		$output .= $this->render_group_fields( $fields, $context );
		$output .= '</div>';
		$output .= $this->render_wrapper_end();

		return $output;
	}

	/**
	 * Render fields within the group
	 *
	 * Creates field instances and renders them with their current values.
	 * Each nested field loads its own value using standard WordPress functions.
	 *
	 * @param array<array<string, mixed>> $fields  Field configurations.
	 * @param mixed                       $context Context (post ID for CPT, page_id for settings).
	 * @return string HTML output.
	 */
	protected function render_group_fields( array $fields, $context = null ): string {
		if ( empty( $fields ) ) {
			return '';
		}

		$output = '';

		foreach ( $fields as $field_config ) {
			$field_name = $field_config['name'] ?? '';

			if ( class_exists( '\Pedalcms\WpCmf\Field\FieldFactory' ) ) {
				try {
					$field = FieldFactory::create( $field_config );

					// For container fields (tabs, etc), pass context directly
					// For regular fields, load and pass the field value
					if ( $field instanceof \Pedalcms\WpCmf\Field\ContainerFieldInterface ) {
						// Container fields need context to pass to nested fields
						$field_html = $field->render( $context );
					} else {
						// Regular fields: load value and render
						$field_value = $this->load_field_value( $field_name, $context );
						$field_html  = $field->render( $field_value );
					}

					// For settings pages (when context is a string page_id), fix the name attribute
					if ( is_string( $context ) && ! empty( $context ) ) {
						$option_name = $field->get_option_name( $context );
						$field_html  = str_replace(
							'name="' . $field_name . '"',
							'name="' . $option_name . '"',
							$field_html
						);
						// Also handle array fields like checkboxes: name="field_name[]"
						$field_html = str_replace(
							'name="' . $field_name . '[]"',
							'name="' . $option_name . '[]"',
							$field_html
						);
					}

					$output .= $field_html;
				} catch ( \Exception $e ) {
					$output .= '<div class="error"><p>Error rendering field: ' . $this->esc_html( $e->getMessage() ) . '</p></div>';
				}
			}
		}

		return $output;
	}

	/**
	 * Load a field value from WordPress
	 *
	 * Determines context and loads value accordingly:
	 * - For CPT metaboxes: use get_post_meta() with post ID
	 * - For settings pages: use get_option() with field name
	 *
	 * @param string $field_name    Field name.
	 * @param mixed  $context_value Context (post ID for CPT, page_id for settings).
	 * @return mixed Field value.
	 */
	protected function load_field_value( string $field_name, $context_value = null ) {
		// For CPT metaboxes: use post meta (context is post ID)
		if ( is_int( $context_value ) && function_exists( 'get_post_meta' ) ) {
			return get_post_meta( $context_value, $field_name, true );
		}

		// For settings pages: use options (context is page_id string)
		if ( function_exists( 'get_option' ) ) {
			// If context is a string (page_id), use get_option_name helper
			if ( is_string( $context_value ) && ! empty( $context_value ) ) {
				// Create temporary field to use get_option_name
				$temp_field  = FieldFactory::create(
					array(
						'name' => $field_name,
						'type' => 'text',
					)
				);
				$option_name = $temp_field->get_option_name( $context_value );
				$value       = get_option( $option_name, '' );
				error_log( sprintf( 'WP-CMF GroupField: Loading field "%s" with option_name "%s" = %s',
					$field_name,
					$option_name,
					var_export( $value, true )
				) );
				return $value;
			}
			// Fallback: try without prefix
			return get_option( $field_name, '' );
		}

		return '';
	}

	/**
	 * Sanitize the group field value
	 *
	 * Container fields don't store values themselves, so return empty array.
	 * Nested fields handle their own sanitization.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return array Empty array.
	 */
	public function sanitize( $value ) {
		return array();
	}

	/**
	 * Validate the group field value
	 *
	 * Container fields don't validate themselves.
	 * Nested fields handle their own validation.
	 *
	 * @param mixed $input Input value.
	 * @return array Validation result.
	 */
	public function validate( $input ): array {
		return array(
			'valid'  => true,
			'errors' => array(),
		);
	}

	/**
	 * Enqueue assets for group field
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! function_exists( 'wp_add_inline_style' ) ) {
			return;
		}

		// Enqueue inline styles for group fields
		wp_add_inline_style(
			'wp-admin',
			'
			.wp-cmf-group-fields {
				margin: 15px 0;
			}
			.wp-cmf-field-group > .wp-cmf-field-label {
				font-size: 14px;
				font-weight: 600;
				margin-bottom: 10px;
				display: block;
			}
			.wp-cmf-field-group > .wp-cmf-field-description {
				color: #646970;
				font-style: italic;
				margin-bottom: 15px;
			}
			.wp-cmf-group-fields > .wp-cmf-field {
				margin-bottom: 15px;
			}
			'
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
				'label'       => array( 'type' => 'string' ),
				'description' => array( 'type' => 'string' ),
				'fields'      => array( 'type' => 'array' ),
			),
		);
	}
}
