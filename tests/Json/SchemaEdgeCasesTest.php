<?php
/**
 * Tests for JSON Schema edge cases and boundary conditions
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Json;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Json\SchemaValidator;

/**
 * Schema Edge Cases Test class
 */
class SchemaEdgeCasesTest extends TestCase {

	/**
	 * Test field name at maximum length (64 characters)
	 */
	public function test_field_name_maximum_length(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => str_repeat( 'a', 64 ), // Exactly 64 chars
							'type' => 'text',
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test field name exceeding maximum length
	 */
	public function test_field_name_exceeds_maximum_length(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => str_repeat( 'a', 65 ), // 65 chars - too long
							'type' => 'text',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].name must be maximum 64 characters', $validator->get_errors() );
	}

	/**
	 * Test CPT id at maximum length (20 characters)
	 */
	public function test_cpt_id_maximum_length(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id' => str_repeat( 'a', 20 ), // Exactly 20 chars
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
	}

	/**
	 * Test CPT id exceeding maximum length
	 */
	public function test_cpt_id_exceeds_maximum_length(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id' => str_repeat( 'a', 21 ), // 21 chars - too long
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].id must be lowercase letters/underscores, max 20 chars', $validator->get_errors() );
	}

	/**
	 * Test select field requires options
	 */
	public function test_select_field_requires_options(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_select',
							'type' => 'select',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( "settings_pages[0].fields[0] field type 'select' requires 'options' property", $validator->get_errors() );
	}

	/**
	 * Test radio field requires options
	 */
	public function test_radio_field_requires_options(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_radio',
							'type' => 'radio',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( "cpts[0].fields[0] field type 'radio' requires 'options' property", $validator->get_errors() );
	}

	/**
	 * Test checkbox field options are optional (single vs multiple mode)
	 */
	public function test_checkbox_field_requires_options(): void {
		$validator = new SchemaValidator();

		// Single checkbox without options is VALID
		$config_single = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'  => 'test_checkbox',
							'type'  => 'checkbox',
							'label' => 'Enable Feature',
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config_single ), 'Single checkbox without options should be valid' );

		// Checkbox with empty options array is INVALID
		$validator_empty = new SchemaValidator();
		$config_empty    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'    => 'test_checkbox',
							'type'    => 'checkbox',
							'options' => array(),
						),
					),
				),
			),
		);

		$this->assertFalse( $validator_empty->validate( $config_empty ), 'Checkbox with empty options should be invalid' );
		$this->assertContains( 'settings_pages[0].fields[0].options must contain at least one option', $validator_empty->get_errors() );
	}

	/**
	 * Test select field with empty options
	 */
	public function test_select_field_with_empty_options(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'    => 'test_select',
							'type'    => 'select',
							'options' => array(),
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].options must contain at least one option', $validator->get_errors() );
	}

	/**
	 * Test number field with min greater than max
	 */
	public function test_number_field_min_greater_than_max(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_number',
							'type' => 'number',
							'min'  => 100,
							'max'  => 50,
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'settings_pages[0].fields[0].min cannot be greater than max', $validator->get_errors() );
	}

	/**
	 * Test number field with valid min/max
	 */
	public function test_number_field_valid_min_max(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_number',
							'type' => 'number',
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test number field with non-numeric min
	 */
	public function test_number_field_non_numeric_min(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_number',
							'type' => 'number',
							'min'  => 'invalid',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].min must be numeric for number field', $validator->get_errors() );
	}

	/**
	 * Test date field with invalid date format
	 */
	public function test_date_field_invalid_date_format(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_date',
							'type' => 'date',
							'min'  => '2024/01/01', // Wrong format
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'settings_pages[0].fields[0].min must be valid date (YYYY-MM-DD) for date field', $validator->get_errors() );
	}

	/**
	 * Test date field with valid date format
	 */
	public function test_date_field_valid_date_format(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_date',
							'type' => 'date',
							'min'  => '2024-01-01',
							'max'  => '2024-12-31',
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test date field with invalid date (e.g., February 30)
	 */
	public function test_date_field_invalid_date(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_date',
							'type' => 'date',
							'min'  => '2024-02-30', // Invalid date
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].min must be valid date (YYYY-MM-DD) for date field', $validator->get_errors() );
	}

	/**
	 * Test color field with invalid hex color
	 */
	public function test_color_field_invalid_hex_color(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'    => 'test_color',
							'type'    => 'color',
							'default' => 'red', // Not hex format
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'settings_pages[0].fields[0].default must be valid hex color (#RRGGBB) for color field', $validator->get_errors() );
	}

	/**
	 * Test color field with valid hex color
	 */
	public function test_color_field_valid_hex_color(): void {
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'    => 'test_color',
							'type'    => 'color',
							'default' => '#FF5733',
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test textarea rows boundary validation
	 */
	public function test_textarea_rows_boundary(): void {
		$validator = new SchemaValidator();

		// Test minimum (1)
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'rows' => 1,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test maximum (50)
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'rows' => 50,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test below minimum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'rows' => 0,
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].rows must be between 1 and 50', $validator->get_errors() );

		// Test above maximum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'rows' => 51,
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].rows must be between 1 and 50', $validator->get_errors() );
	}

	/**
	 * Test textarea cols boundary validation
	 */
	public function test_textarea_cols_boundary(): void {
		$validator = new SchemaValidator();

		// Test minimum (10)
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'cols' => 10,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test maximum (200)
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'cols' => 200,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test below minimum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name' => 'test_textarea',
							'type' => 'textarea',
							'cols' => 9,
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].cols must be between 10 and 200', $validator->get_errors() );
	}

	/**
	 * Test maxlength boundary validation
	 */
	public function test_maxlength_boundary(): void {
		$validator = new SchemaValidator();

		// Test minimum (1)
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'      => 'test_text',
							'type'      => 'text',
							'maxlength' => 1,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test maximum (65535)
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'      => 'test_text',
							'type'      => 'text',
							'maxlength' => 65535,
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test below minimum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'      => 'test_text',
							'type'      => 'text',
							'maxlength' => 0,
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].maxlength must be between 1 and 65535', $validator->get_errors() );
	}

	/**
	 * Test label length validation
	 */
	public function test_label_length_validation(): void {
		$validator = new SchemaValidator();

		// Test at maximum (200)
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'text',
							'label' => str_repeat( 'a', 200 ),
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test exceeding maximum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'text',
							'label' => str_repeat( 'a', 201 ),
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].label must be maximum 200 characters', $validator->get_errors() );
	}

	/**
	 * Test description length validation
	 */
	public function test_description_length_validation(): void {
		$validator = new SchemaValidator();

		// Test at maximum (500)
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'        => 'test_field',
							'type'        => 'text',
							'description' => str_repeat( 'a', 500 ),
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test exceeding maximum
		$validator = new SchemaValidator();
		$config    = array(
			'settings_pages' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'        => 'test_field',
							'type'        => 'text',
							'description' => str_repeat( 'a', 501 ),
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'settings_pages[0].fields[0].description must be maximum 500 characters', $validator->get_errors() );
	}

	/**
	 * Test placeholder length validation
	 */
	public function test_placeholder_length_validation(): void {
		$validator = new SchemaValidator();

		// Test at maximum (200)
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'        => 'test_field',
							'type'        => 'text',
							'placeholder' => str_repeat( 'a', 200 ),
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test exceeding maximum
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'        => 'test_field',
							'type'        => 'text',
							'placeholder' => str_repeat( 'a', 201 ),
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].placeholder must be maximum 200 characters', $validator->get_errors() );
	}

	/**
	 * Test CSS class validation
	 */
	public function test_css_class_validation(): void {
		$validator = new SchemaValidator();

		// Test valid classes
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'text',
							'class' => 'my-class another-class third_class',
						),
					),
				),
			),
		);
		$this->assertTrue( $validator->validate( $config ) );

		// Test invalid class name
		$validator = new SchemaValidator();
		$config    = array(
			'cpts' => array(
				array(
					'id'     => 'test',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'text',
							'class' => 'invalid@class',
						),
					),
				),
			),
		);
		$this->assertFalse( $validator->validate( $config ) );
		$this->assertContains( 'cpts[0].fields[0].class must contain only valid CSS class names', $validator->get_errors() );
	}

	/**
	 * Test deeply nested configuration
	 */
	public function test_deeply_nested_configuration(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'book',
					'fields' => array(
						array(
							'name' => 'title',
							'type' => 'text',
						),
						array(
							'name' => 'author',
							'type' => 'text',
						),
						array(
							'name'    => 'genre',
							'type'    => 'select',
							'options' => array(
								'fiction'     => 'Fiction',
								'non_fiction' => 'Non-Fiction',
							),
						),
					),
				),
				array(
					'id'     => 'review',
					'fields' => array(
						array(
							'name' => 'rating',
							'type' => 'number',
							'min'  => 1,
							'max'  => 5,
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'     => 'library_settings',
					'fields' => array(
						array(
							'name' => 'opening_hours',
							'type' => 'text',
						),
						array(
							'name'    => 'notification_enabled',
							'type'    => 'checkbox',
							'options' => array(
								'email' => 'Email',
								'sms'   => 'SMS',
							),
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test multiple validation errors accumulated
	 */
	public function test_multiple_validation_errors(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'invalid@id', // Invalid
					'fields' => array(
						array(
							'name' => 'test_field',
							// Missing type
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					// Missing id
					'fields' => array(
						array(
							'name' => 'another_field',
							'type' => 'select',
							// Missing options
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$errors = $validator->get_errors();

		$this->assertGreaterThan( 2, count( $errors ) );
		$this->assertContains( 'cpts[0].id must be lowercase letters/underscores, max 20 chars', $errors );
		$this->assertContains( "settings_pages[0] missing required field 'id'", $errors );
	}
}
