<?php
/**
 * RepeaterField for WP-CMF
 *
 * A container field that creates repeatable sets of nested fields.
 * Unlike other container fields, the repeater stores its own data as a serialized array
 * containing all the repeated field values.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field\fields;

use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * RepeaterField - Creates repeatable sets of fields
 *
 * Configuration options:
 * - fields: Array of field configurations for each repeatable row
 * - min_rows: Minimum number of rows (default: 0)
 * - max_rows: Maximum number of rows (default: unlimited)
 * - button_label: Label for the "Add Row" button (default: 'Add Row')
 * - row_label: Label template for each row (supports {{index}} placeholder)
 * - collapsible: Whether rows can be collapsed (default: true)
 * - collapsed: Whether rows start collapsed (default: false)
 * - sortable: Whether rows can be reordered (default: true)
 *
 * Data is stored as a serialized array with structure:
 * [
 *   [ 'field1' => 'value1', 'field2' => 'value2' ],
 *   [ 'field1' => 'value3', 'field2' => 'value4' ],
 *   ...
 * ]
 */
class RepeaterField extends AbstractField {

	/**
	 * Get default configuration values
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		$defaults = parent::get_defaults();
		return array_merge(
			$defaults,
			[
				'fields'       => [],
				'min_rows'     => 0,
				'max_rows'     => 0, // 0 = unlimited
				'button_label' => 'Add Row',
				'row_label'    => 'Row {{index}}',
				'collapsible'  => true,
				'collapsed'    => false,
				'sortable'     => true,
			]
		);
	}

	/**
	 * Get the field configurations for each row
	 *
	 * @return array<array<string, mixed>>
	 */
	public function get_sub_fields(): array {
		return $this->config['fields'] ?? [];
	}

	/**
	 * Render the repeater field
	 *
	 * @param mixed $value Current value (array of rows).
	 * @return string HTML output.
	 */
	public function render( $value = null ): string {
		$field_name   = $this->get_name();
		$field_id     = $this->get_field_id();
		$sub_fields   = $this->get_sub_fields();
		$min_rows     = (int) ( $this->config['min_rows'] ?? 0 );
		$max_rows     = (int) ( $this->config['max_rows'] ?? 0 );
		$button_label = $this->config['button_label'] ?? 'Add Row';
		$row_label    = $this->config['row_label'] ?? 'Row {{index}}';
		$collapsible  = $this->config['collapsible'] ?? true;
		$collapsed    = $this->config['collapsed'] ?? false;
		$sortable     = $this->config['sortable'] ?? true;

		// Ensure value is an array
		$rows = is_array( $value ) ? $value : [];

		// Add minimum rows if needed
		while ( count( $rows ) < $min_rows ) {
			$rows[] = [];
		}

		$output  = $this->render_wrapper_start();
		$output .= $this->render_label();

		// Main repeater container
		$output .= '<div class="wp-cmf-repeater" ';
		$output .= 'id="' . $this->esc_attr( $field_id ) . '" ';
		$output .= 'data-field-name="' . $this->esc_attr( $field_name ) . '" ';
		$output .= 'data-min-rows="' . $this->esc_attr( (string) $min_rows ) . '" ';
		$output .= 'data-max-rows="' . $this->esc_attr( (string) $max_rows ) . '" ';
		$output .= 'data-sortable="' . ( $sortable ? 'true' : 'false' ) . '" ';
		$output .= 'data-collapsible="' . ( $collapsible ? 'true' : 'false' ) . '">';

		// Rows container
		$output .= '<div class="wp-cmf-repeater-rows">';

		// Render existing rows
		foreach ( $rows as $row_index => $row_data ) {
			$output .= $this->render_row( $row_index, $row_data, $sub_fields, $field_name, $row_label, $collapsible, $collapsed );
		}

		$output .= '</div>'; // .wp-cmf-repeater-rows

		// Add row button
		$can_add = ( 0 === $max_rows || count( $rows ) < $max_rows );
		$output .= '<div class="wp-cmf-repeater-actions">';
		$output .= '<button type="button" class="button wp-cmf-repeater-add" ' . ( ! $can_add ? 'disabled' : '' ) . '>';
		$output .= '<span class="dashicons dashicons-plus-alt2"></span> ';
		$output .= $this->esc_html( $button_label );
		$output .= '</button>';
		$output .= '</div>';

		// Hidden template for new rows (used by JavaScript)
		$output .= '<script type="text/template" class="wp-cmf-repeater-template">';
		$output .= $this->render_row( '{{INDEX}}', [], $sub_fields, $field_name, $row_label, $collapsible, false );
		$output .= '</script>';

		$output .= '</div>'; // .wp-cmf-repeater

		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		$this->enqueue_repeater_scripts();
		$this->enqueue_assets();

		return $output;
	}

	/**
	 * Render a single repeater row
	 *
	 * @param int|string                  $row_index   Row index.
	 * @param array<string, mixed>        $row_data    Row data.
	 * @param array<array<string, mixed>> $sub_fields  Sub-field configurations.
	 * @param string                      $field_name  Parent field name.
	 * @param string                      $row_label   Row label template.
	 * @param bool                        $collapsible Whether row is collapsible.
	 * @param bool                        $collapsed   Whether row starts collapsed.
	 * @return string HTML output.
	 */
	protected function render_row( $row_index, array $row_data, array $sub_fields, string $field_name, string $row_label, bool $collapsible, bool $collapsed ): string {
		$label       = str_replace( '{{index}}', (string) ( is_int( $row_index ) ? $row_index + 1 : $row_index ), $row_label );
		$row_classes = 'wp-cmf-repeater-row';

		if ( $collapsed && $collapsible ) {
			$row_classes .= ' collapsed';
		}

		$output = '<div class="' . $row_classes . '" data-row-index="' . $this->esc_attr( (string) $row_index ) . '">';

		// Row header
		$output .= '<div class="wp-cmf-repeater-row-header">';

		// Drag handle (if sortable)
		$output .= '<span class="wp-cmf-repeater-drag-handle dashicons dashicons-move" title="Drag to reorder"></span>';

		// Row label
		$output .= '<span class="wp-cmf-repeater-row-label">' . $this->esc_html( $label ) . '</span>';

		// Row actions
		$output .= '<div class="wp-cmf-repeater-row-actions">';

		if ( $collapsible ) {
			$output .= '<button type="button" class="wp-cmf-repeater-toggle" title="Toggle">';
			$output .= '<span class="dashicons dashicons-arrow-down"></span>';
			$output .= '</button>';
		}

		$output .= '<button type="button" class="wp-cmf-repeater-remove" title="Remove">';
		$output .= '<span class="dashicons dashicons-trash"></span>';
		$output .= '</button>';

		$output .= '</div>'; // .wp-cmf-repeater-row-actions
		$output .= '</div>'; // .wp-cmf-repeater-row-header

		// Row content (fields)
		$content_style = ( $collapsed && $collapsible ) ? ' style="display: none;"' : '';
		$output       .= '<div class="wp-cmf-repeater-row-content"' . $content_style . '>';
		$output       .= '<table class="form-table wp-cmf-repeater-fields" role="presentation">';

		foreach ( $sub_fields as $sub_field_config ) {
			$sub_field_name = $sub_field_config['name'] ?? '';

			if ( empty( $sub_field_name ) ) {
				continue;
			}

			try {
				$sub_field = FieldFactory::create( $sub_field_config );

				// Get value for this sub-field from row data
				$sub_value = $row_data[ $sub_field_name ] ?? '';

				// Render the sub-field
				$sub_html = $sub_field->render( $sub_value );

				// Update the name attribute to use array notation for the repeater
				// From: name="sub_field_name"
				// To: name="repeater_name[row_index][sub_field_name]"
				$original_name = $sub_field_name;
				$new_name      = $field_name . '[' . $row_index . '][' . $original_name . ']';

				$sub_html = str_replace(
					'name="' . $original_name . '"',
					'name="' . $new_name . '"',
					$sub_html
				);

				// Also handle array fields like checkboxes
				$sub_html = str_replace(
					'name="' . $original_name . '[]"',
					'name="' . $new_name . '[]"',
					$sub_html
				);

				// Update ID to be unique per row
				$original_id = 'field-' . $original_name;
				$new_id      = 'field-' . $field_name . '-' . $row_index . '-' . $original_name;
				$sub_html    = str_replace(
					'id="' . $original_id . '"',
					'id="' . $new_id . '"',
					$sub_html
				);

				$output .= '<tr>';
				$output .= '<th scope="row">' . $this->esc_html( $sub_field->get_label() ) . '</th>';
				$output .= '<td>' . $sub_html . '</td>';
				$output .= '</tr>';
			} catch ( \Exception $e ) {
				$output .= '<tr><td colspan="2">Error: ' . $this->esc_html( $e->getMessage() ) . '</td></tr>';
			}
		}

		$output .= '</table>';
		$output .= '</div>'; // .wp-cmf-repeater-row-content
		$output .= '</div>'; // .wp-cmf-repeater-row

		return $output;
	}

	/**
	 * Sanitize the repeater value
	 *
	 * Sanitizes each row's fields using their respective sanitize methods.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return array Sanitized array of rows.
	 */
	public function sanitize( $value ) {
		if ( ! is_array( $value ) ) {
			return [];
		}

		$sub_fields     = $this->get_sub_fields();
		$sanitized_rows = [];

		foreach ( $value as $row_index => $row_data ) {
			if ( ! is_array( $row_data ) ) {
				continue;
			}

			$sanitized_row = [];

			foreach ( $sub_fields as $sub_field_config ) {
				$sub_field_name = $sub_field_config['name'] ?? '';

				if ( empty( $sub_field_name ) ) {
					continue;
				}

				try {
					$sub_field   = FieldFactory::create( $sub_field_config );
					$field_value = $row_data[ $sub_field_name ] ?? '';

					$sanitized_row[ $sub_field_name ] = $sub_field->sanitize( $field_value );
				} catch ( \Exception $e ) {
					// If field creation fails, skip this field
					continue;
				}
			}

			if ( ! empty( $sanitized_row ) ) {
				$sanitized_rows[] = $sanitized_row;
			}
		}

		return $sanitized_rows;
	}

	/**
	 * Validate the repeater value
	 *
	 * Validates each row's fields using their respective validate methods.
	 *
	 * @param mixed $input Value to validate.
	 * @return array Validation result.
	 */
	public function validate( $input ): array {
		$errors     = [];
		$sub_fields = $this->get_sub_fields();
		$min_rows   = (int) ( $this->config['min_rows'] ?? 0 );
		$max_rows   = (int) ( $this->config['max_rows'] ?? 0 );

		if ( ! is_array( $input ) ) {
			$input = [];
		}

		$row_count = count( $input );

		// Check minimum rows
		if ( $min_rows > 0 && $row_count < $min_rows ) {
			$errors[] = sprintf( 'At least %d row(s) required.', $min_rows );
		}

		// Check maximum rows
		if ( $max_rows > 0 && $row_count > $max_rows ) {
			$errors[] = sprintf( 'Maximum %d row(s) allowed.', $max_rows );
		}

		// Validate each row
		foreach ( $input as $row_index => $row_data ) {
			if ( ! is_array( $row_data ) ) {
				continue;
			}

			foreach ( $sub_fields as $sub_field_config ) {
				$sub_field_name = $sub_field_config['name'] ?? '';

				if ( empty( $sub_field_name ) ) {
					continue;
				}

				try {
					$sub_field   = FieldFactory::create( $sub_field_config );
					$field_value = $row_data[ $sub_field_name ] ?? '';

					$result = $sub_field->validate( $field_value );

					if ( ! $result['valid'] && ! empty( $result['errors'] ) ) {
						foreach ( $result['errors'] as $error ) {
							$errors[] = sprintf( 'Row %d - %s: %s', $row_index + 1, $sub_field->get_label(), $error );
						}
					}
				} catch ( \Exception $e ) {
					continue;
				}
			}
		}

		return [
			'valid'  => empty( $errors ),
			'errors' => $errors,
		];
	}

	/**
	 * Enqueue repeater JavaScript
	 *
	 * @return void
	 */
	protected function enqueue_repeater_scripts(): void {
		static $scripts_enqueued = false;

		if ( $scripts_enqueued ) {
			return;
		}

		$scripts_enqueued = true;

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					// Initialize sortable if enabled
					$('.wp-cmf-repeater[data-sortable="true"] .wp-cmf-repeater-rows').sortable({
						handle: '.wp-cmf-repeater-drag-handle',
						axis: 'y',
						update: function(event, ui) {
							$(this).closest('.wp-cmf-repeater').trigger('wp-cmf-repeater-reindex');
						}
					});

					// Add row
					$(document).on('click', '.wp-cmf-repeater-add', function(e) {
						e.preventDefault();
						var $repeater = $(this).closest('.wp-cmf-repeater');
						var $rows = $repeater.find('.wp-cmf-repeater-rows');
						var template = $repeater.find('.wp-cmf-repeater-template').html();
						var maxRows = parseInt($repeater.data('max-rows')) || 0;
						var currentRows = $rows.find('.wp-cmf-repeater-row').length;
						var newIndex = currentRows;

						// Check max rows
						if (maxRows > 0 && currentRows >= maxRows) {
							return;
						}

						// Replace {{INDEX}} with actual index
						var newRow = template.replace(/\{\{INDEX\}\}/g, newIndex);

						// Update row label
						newRow = newRow.replace(/Row \{\{index\}\}/g, 'Row ' + (newIndex + 1));

						$rows.append(newRow);

						// Reinitialize sortable
						if ($repeater.data('sortable') === true || $repeater.data('sortable') === 'true') {
							$rows.sortable('refresh');
						}

						// Check if max rows reached
						if (maxRows > 0 && currentRows + 1 >= maxRows) {
							$(this).prop('disabled', true);
						}

						// Check min rows for remove button
						$repeater.trigger('wp-cmf-repeater-check-min');

						// Trigger custom event
						$repeater.trigger('wp-cmf-repeater-row-added', [newIndex]);
					});

					// Remove row
					$(document).on('click', '.wp-cmf-repeater-remove', function(e) {
						e.preventDefault();
						var $row = $(this).closest('.wp-cmf-repeater-row');
						var $repeater = $row.closest('.wp-cmf-repeater');
						var $rows = $repeater.find('.wp-cmf-repeater-rows');
						var minRows = parseInt($repeater.data('min-rows')) || 0;
						var currentRows = $rows.find('.wp-cmf-repeater-row').length;

						// Check min rows
						if (minRows > 0 && currentRows <= minRows) {
							alert('Minimum ' + minRows + ' row(s) required.');
							return;
						}

						// Confirm removal
						if (confirm('Are you sure you want to remove this row?')) {
							$row.fadeOut(200, function() {
								$(this).remove();
								$repeater.trigger('wp-cmf-repeater-reindex');
								$repeater.trigger('wp-cmf-repeater-check-min');

								// Re-enable add button
								var maxRows = parseInt($repeater.data('max-rows')) || 0;
								if (maxRows === 0 || $rows.find('.wp-cmf-repeater-row').length < maxRows) {
									$repeater.find('.wp-cmf-repeater-add').prop('disabled', false);
								}
							});
						}
					});

					// Toggle row collapse
					$(document).on('click', '.wp-cmf-repeater-toggle', function(e) {
						e.preventDefault();
						var $row = $(this).closest('.wp-cmf-repeater-row');
						var $content = $row.find('.wp-cmf-repeater-row-content');
						var $icon = $(this).find('.dashicons');

						$row.toggleClass('collapsed');
						$content.slideToggle(200);
						$icon.toggleClass('dashicons-arrow-down dashicons-arrow-up');
					});

					// Reindex rows after sorting or removal
					$(document).on('wp-cmf-repeater-reindex', '.wp-cmf-repeater', function() {
						var $repeater = $(this);
						var fieldName = $repeater.data('field-name');

						$repeater.find('.wp-cmf-repeater-row').each(function(index) {
							var $row = $(this);
							var oldIndex = $row.data('row-index');

							// Update data attribute
							$row.attr('data-row-index', index);
							$row.data('row-index', index);

							// Update row label
							var rowLabel = $row.find('.wp-cmf-repeater-row-label').text();
							$row.find('.wp-cmf-repeater-row-label').text(rowLabel.replace(/\d+/, index + 1));

							// Update input names
							$row.find('input, select, textarea').each(function() {
								var name = $(this).attr('name');
								if (name) {
									// Replace [oldIndex] with [newIndex]
									name = name.replace(
										new RegExp('\\[' + oldIndex + '\\]'),
										'[' + index + ']'
									);
									$(this).attr('name', name);
								}

								// Update ID
								var id = $(this).attr('id');
								if (id) {
									id = id.replace(
										new RegExp('-' + oldIndex + '-'),
										'-' + index + '-'
									);
									$(this).attr('id', id);
								}
							});
						});
					});

					// Check minimum rows and disable/enable remove buttons
					$(document).on('wp-cmf-repeater-check-min', '.wp-cmf-repeater', function() {
						var $repeater = $(this);
						var minRows = parseInt($repeater.data('min-rows')) || 0;
						var currentRows = $repeater.find('.wp-cmf-repeater-row').length;

						if (minRows > 0 && currentRows <= minRows) {
							$repeater.find('.wp-cmf-repeater-remove').prop('disabled', true);
						} else {
							$repeater.find('.wp-cmf-repeater-remove').prop('disabled', false);
						}
					});

					// Initial check
					$('.wp-cmf-repeater').trigger('wp-cmf-repeater-check-min');
				});
				</script>
				<?php
			}
		);
	}

	/**
	 * Enqueue assets for repeater field
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! function_exists( 'wp_add_inline_style' ) ) {
			return;
		}

		// Make sure jQuery UI Sortable is available
		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

		wp_add_inline_style(
			'wp-admin',
			'
			.wp-cmf-repeater {
				margin: 10px 0;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				background: #fff;
			}
			.wp-cmf-repeater-rows {
				padding: 0;
			}
			.wp-cmf-repeater-row {
				border-bottom: 1px solid #c3c4c7;
				background: #fff;
			}
			.wp-cmf-repeater-row:last-child {
				border-bottom: none;
			}
			.wp-cmf-repeater-row.ui-sortable-helper {
				box-shadow: 0 3px 10px rgba(0,0,0,0.15);
			}
			.wp-cmf-repeater-row.ui-sortable-placeholder {
				visibility: visible !important;
				background: #f0f0f1;
				border: 2px dashed #c3c4c7;
			}
			.wp-cmf-repeater-row-header {
				display: flex;
				align-items: center;
				padding: 10px 15px;
				background: #f6f7f7;
				cursor: default;
				gap: 10px;
			}
			.wp-cmf-repeater-drag-handle {
				cursor: move;
				color: #787c82;
				font-size: 20px;
			}
			.wp-cmf-repeater-drag-handle:hover {
				color: #2271b1;
			}
			.wp-cmf-repeater-row-label {
				flex: 1;
				font-weight: 600;
				color: #1d2327;
			}
			.wp-cmf-repeater-row-actions {
				display: flex;
				gap: 5px;
			}
			.wp-cmf-repeater-row-actions button {
				background: none;
				border: none;
				padding: 5px;
				cursor: pointer;
				color: #787c82;
				border-radius: 3px;
			}
			.wp-cmf-repeater-row-actions button:hover {
				background: #dcdcde;
				color: #1d2327;
			}
			.wp-cmf-repeater-remove:hover {
				color: #d63638 !important;
				background: #fcecec !important;
			}
			.wp-cmf-repeater-row-actions button:disabled {
				opacity: 0.5;
				cursor: not-allowed;
			}
			.wp-cmf-repeater-row-content {
				padding: 15px;
			}
			.wp-cmf-repeater-row.collapsed .wp-cmf-repeater-toggle .dashicons {
				transform: rotate(-90deg);
			}
			.wp-cmf-repeater-fields {
				margin: 0;
			}
			.wp-cmf-repeater-fields th {
				padding-left: 0;
				width: 150px;
			}
			.wp-cmf-repeater-actions {
				padding: 15px;
				background: #f6f7f7;
				border-top: 1px solid #c3c4c7;
			}
			.wp-cmf-repeater-add {
				display: inline-flex;
				align-items: center;
				gap: 5px;
			}
			.wp-cmf-repeater-add .dashicons {
				font-size: 16px;
				width: 16px;
				height: 16px;
			}
			.wp-cmf-repeater-template {
				display: none !important;
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
		$base_schema = parent::get_schema();

		return array_merge(
			$base_schema,
			[
				'fields'       => $this->get_sub_fields(),
				'min_rows'     => $this->config['min_rows'] ?? 0,
				'max_rows'     => $this->config['max_rows'] ?? 0,
				'button_label' => $this->config['button_label'] ?? 'Add Row',
				'row_label'    => $this->config['row_label'] ?? 'Row {{index}}',
				'collapsible'  => $this->config['collapsible'] ?? true,
				'collapsed'    => $this->config['collapsed'] ?? false,
				'sortable'     => $this->config['sortable'] ?? true,
			]
		);
	}
}
