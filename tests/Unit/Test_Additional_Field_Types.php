<?php
/**
 * Additional Field Type Tests
 *
 * Tests for field types that were not fully covered in Test_Field_Types.php
 * Includes: WysiwygField, RepeaterField, and additional rendering tests.
 *
 * @package Pedalcms\WpCmf\Tests\Unit
 */

use Pedalcms\WpCmf\Field\Field_Factory;

/**
 * Class Test_Additional_Field_Types
 *
 * Additional tests for field types.
 */
class Test_Additional_Field_Types extends WP_UnitTestCase {

	/**
	 * Reset Field_Factory between tests.
	 */
	public function set_up(): void {
		parent::set_up();
		Field_Factory::reset();
	}

	// =========================================================================
	// WysiwygField Tests
	// =========================================================================

	/**
	 * Test WysiwygField renders correctly.
	 */
	public function test_wysiwyg_field_render(): void {
		$field = Field_Factory::create(
			[
				'name'  => 'test_wysiwyg',
				'type'  => 'wysiwyg',
				'label' => 'Content Editor',
			]
		);

		$html = $field->render( '<p>Test content</p>' );

		// Should contain textarea or editor element
		$this->assertNotEmpty( $html );
		$this->assertStringContainsString( 'test_wysiwyg', $html );
	}

	/**
	 * Test WysiwygField with custom rows.
	 */
	public function test_wysiwyg_field_custom_rows(): void {
		$field = Field_Factory::create(
			[
				'name'          => 'test_wysiwyg',
				'type'          => 'wysiwyg',
				'label'         => 'Content',
				'textarea_rows' => 20,
			]
		);

		$html = $field->render( '' );

		// Output should exist
		$this->assertNotEmpty( $html );
	}

	/**
	 * Test WysiwygField without media buttons.
	 */
	public function test_wysiwyg_field_no_media_buttons(): void {
		$field = Field_Factory::create(
			[
				'name'          => 'test_wysiwyg',
				'type'          => 'wysiwyg',
				'media_buttons' => false,
			]
		);

		$html = $field->render( '' );

		$this->assertNotEmpty( $html );
	}

	/**
	 * Test WysiwygField teeny mode.
	 */
	public function test_wysiwyg_field_teeny_mode(): void {
		$field = Field_Factory::create(
			[
				'name'  => 'test_wysiwyg',
				'type'  => 'wysiwyg',
				'teeny' => true,
			]
		);

		$html = $field->render( '' );

		$this->assertNotEmpty( $html );
	}

	/**
	 * Test WysiwygField get_type returns correct type.
	 */
	public function test_wysiwyg_field_type(): void {
		$field = Field_Factory::create(
			[
				'name' => 'test_wysiwyg',
				'type' => 'wysiwyg',
			]
		);

		$this->assertSame( 'wysiwyg', $field->get_type() );
	}

	// =========================================================================
	// RepeaterField Tests
	// =========================================================================

	/**
	 * Test RepeaterField renders correctly.
	 */
	public function test_repeater_field_render(): void {
		$field = Field_Factory::create(
			[
				'name'   => 'test_repeater',
				'type'   => 'repeater',
				'label'  => 'Repeater Field',
				'fields' => [
					[
						'name'  => 'sub_text',
						'type'  => 'text',
						'label' => 'Sub Text',
					],
				],
			]
		);

		$html = $field->render( [] );

		$this->assertNotEmpty( $html );
		$this->assertStringContainsString( 'wp-cmf-repeater', $html );
	}

	/**
	 * Test RepeaterField with existing data.
	 */
	public function test_repeater_field_with_data(): void {
		$field = Field_Factory::create(
			[
				'name'   => 'test_repeater',
				'type'   => 'repeater',
				'fields' => [
					[
						'name' => 'name',
						'type' => 'text',
					],
				],
			]
		);

		$data = [
			[ 'name' => 'Row 1' ],
			[ 'name' => 'Row 2' ],
		];

		$html = $field->render( $data );

		$this->assertNotEmpty( $html );
	}

	/**
	 * Test RepeaterField get_sub_fields returns config.
	 */
	public function test_repeater_get_sub_fields(): void {
		$field = Field_Factory::create(
			[
				'name'   => 'test_repeater',
				'type'   => 'repeater',
				'fields' => [
					[
						'name' => 'field_a',
						'type' => 'text',
					],
					[
						'name' => 'field_b',
						'type' => 'number',
					],
				],
			]
		);

		$sub_fields = $field->get_sub_fields();

		$this->assertIsArray( $sub_fields );
		$this->assertCount( 2, $sub_fields );
	}

	/**
	 * Test RepeaterField with min_rows.
	 */
	public function test_repeater_min_rows(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_repeater',
				'type'     => 'repeater',
				'min_rows' => 2,
				'fields'   => [
					[
						'name' => 'text',
						'type' => 'text',
					],
				],
			]
		);

		$html = $field->render( [] );

		// Should have at least 2 rows rendered
		$this->assertNotEmpty( $html );
		$this->assertStringContainsString( 'data-min-rows="2"', $html );
	}

	/**
	 * Test RepeaterField with max_rows.
	 */
	public function test_repeater_max_rows(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_repeater',
				'type'     => 'repeater',
				'max_rows' => 5,
				'fields'   => [
					[
						'name' => 'text',
						'type' => 'text',
					],
				],
			]
		);

		$html = $field->render( [] );

		$this->assertStringContainsString( 'data-max-rows="5"', $html );
	}

	/**
	 * Test RepeaterField custom button label.
	 */
	public function test_repeater_button_label(): void {
		$field = Field_Factory::create(
			[
				'name'         => 'test_repeater',
				'type'         => 'repeater',
				'button_label' => 'Add New Item',
				'fields'       => [
					[
						'name' => 'text',
						'type' => 'text',
					],
				],
			]
		);

		$html = $field->render( [] );

		$this->assertStringContainsString( 'Add New Item', $html );
	}

	/**
	 * Test RepeaterField collapsible option.
	 */
	public function test_repeater_collapsible(): void {
		$field = Field_Factory::create(
			[
				'name'        => 'test_repeater',
				'type'        => 'repeater',
				'collapsible' => true,
				'fields'      => [
					[
						'name' => 'text',
						'type' => 'text',
					],
				],
			]
		);

		$html = $field->render( [ [ 'text' => 'Test' ] ] );

		$this->assertStringContainsString( 'data-collapsible="true"', $html );
	}

	/**
	 * Test RepeaterField sortable option.
	 */
	public function test_repeater_sortable(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_repeater',
				'type'     => 'repeater',
				'sortable' => true,
				'fields'   => [
					[
						'name' => 'text',
						'type' => 'text',
					],
				],
			]
		);

		$html = $field->render( [] );

		$this->assertStringContainsString( 'data-sortable="true"', $html );
	}

	/**
	 * Test RepeaterField type.
	 */
	public function test_repeater_field_type(): void {
		$field = Field_Factory::create(
			[
				'name'   => 'test_repeater',
				'type'   => 'repeater',
				'fields' => [],
			]
		);

		$this->assertSame( 'repeater', $field->get_type() );
	}

	// =========================================================================
	// URLField Additional Tests
	// =========================================================================

	/**
	 * Test URLField renders with placeholder.
	 */
	public function test_url_field_placeholder(): void {
		$field = Field_Factory::create(
			[
				'name'        => 'test_url',
				'type'        => 'url',
				'placeholder' => 'https://example.com',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'placeholder="https://example.com"', $html );
	}

	/**
	 * Test URLField renders with required.
	 */
	public function test_url_field_required(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_url',
				'type'     => 'url',
				'required' => true,
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'required', $html );
	}

	/**
	 * Test URLField renders with readonly.
	 */
	public function test_url_field_readonly(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_url',
				'type'     => 'url',
				'readonly' => true,
			]
		);

		$html = $field->render( 'https://example.com' );

		$this->assertStringContainsString( 'readonly', $html );
	}

	/**
	 * Test URLField renders with disabled.
	 */
	public function test_url_field_disabled(): void {
		$field = Field_Factory::create(
			[
				'name'     => 'test_url',
				'type'     => 'url',
				'disabled' => true,
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'disabled', $html );
	}

	// =========================================================================
	// Password Field Additional Tests
	// =========================================================================

	/**
	 * Test PasswordField with autocomplete off.
	 */
	public function test_password_field_autocomplete(): void {
		$field = Field_Factory::create(
			[
				'name'         => 'test_password',
				'type'         => 'password',
				'autocomplete' => 'new-password',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'type="password"', $html );
	}

	/**
	 * Test PasswordField with placeholder.
	 */
	public function test_password_field_placeholder(): void {
		$field = Field_Factory::create(
			[
				'name'        => 'test_password',
				'type'        => 'password',
				'placeholder' => 'Enter password',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'placeholder="Enter password"', $html );
	}

	// =========================================================================
	// Color Field Additional Tests
	// =========================================================================

	/**
	 * Test ColorField with default value.
	 */
	public function test_color_field_default(): void {
		$field = Field_Factory::create(
			[
				'name'    => 'test_color',
				'type'    => 'color',
				'default' => '#ff0000',
			]
		);

		$html = $field->render( null );

		$this->assertStringContainsString( 'value="#ff0000"', $html );
	}

	// =========================================================================
	// Date Field Additional Tests
	// =========================================================================

	/**
	 * Test DateField with min date.
	 */
	public function test_date_field_min(): void {
		$field = Field_Factory::create(
			[
				'name' => 'test_date',
				'type' => 'date',
				'min'  => '2025-01-01',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'min="2025-01-01"', $html );
	}

	/**
	 * Test DateField with max date.
	 */
	public function test_date_field_max(): void {
		$field = Field_Factory::create(
			[
				'name' => 'test_date',
				'type' => 'date',
				'max'  => '2025-12-31',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'max="2025-12-31"', $html );
	}

	// =========================================================================
	// Number Field Additional Tests
	// =========================================================================

	/**
	 * Test NumberField with step.
	 */
	public function test_number_field_step(): void {
		$field = Field_Factory::create(
			[
				'name' => 'test_number',
				'type' => 'number',
				'step' => 0.01,
			]
		);

		$html = $field->render( 0 );

		$this->assertStringContainsString( 'step="0.01"', $html );
	}

	/**
	 * Test NumberField with placeholder.
	 */
	public function test_number_field_placeholder(): void {
		$field = Field_Factory::create(
			[
				'name'        => 'test_number',
				'type'        => 'number',
				'placeholder' => 'Enter number',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'placeholder="Enter number"', $html );
	}

	// =========================================================================
	// Textarea Field Additional Tests
	// =========================================================================

	/**
	 * Test TextareaField with cols.
	 */
	public function test_textarea_field_cols(): void {
		$field = Field_Factory::create(
			[
				'name' => 'test_textarea',
				'type' => 'textarea',
				'cols' => 50,
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'cols="50"', $html );
	}

	/**
	 * Test TextareaField with maxlength.
	 */
	public function test_textarea_field_maxlength(): void {
		$field = Field_Factory::create(
			[
				'name'      => 'test_textarea',
				'type'      => 'textarea',
				'maxlength' => 500,
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'maxlength="500"', $html );
	}

	// =========================================================================
	// Field Description Tests
	// =========================================================================

	/**
	 * Test field renders description.
	 */
	public function test_field_renders_description(): void {
		$field = Field_Factory::create(
			[
				'name'        => 'test',
				'type'        => 'text',
				'description' => 'This is a helpful description',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'This is a helpful description', $html );
	}

	/**
	 * Test field renders label.
	 */
	public function test_field_renders_label(): void {
		$field = Field_Factory::create(
			[
				'name'  => 'test',
				'type'  => 'text',
				'label' => 'My Field Label',
			]
		);

		$html = $field->render( '' );

		$this->assertStringContainsString( 'My Field Label', $html );
	}
}
