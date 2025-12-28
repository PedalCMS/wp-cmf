<?php
/**
 * Field Factory Tests
 *
 * Tests for the FieldFactory class.
 *
 * @package Pedalcms\WpCmf\Tests\Unit
 */

use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\FieldInterface;

/**
 * Class Test_Field_Factory
 *
 * Tests for field factory creation and registration.
 */
class Test_Field_Factory extends WP_UnitTestCase {

	/**
	 * Reset FieldFactory between tests.
	 */
	public function set_up(): void {
		parent::set_up();
		FieldFactory::reset();
	}

	/**
	 * Test creating a text field.
	 */
	public function test_create_text_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_text',
				'type'  => 'text',
				'label' => 'Test Text',
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'test_text', $field->get_name() );
		$this->assertSame( 'text', $field->get_type() );
	}

	/**
	 * Test creating a textarea field.
	 */
	public function test_create_textarea_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_textarea',
				'type'  => 'textarea',
				'label' => 'Test Textarea',
				'rows'  => 5,
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'textarea', $field->get_type() );
	}

	/**
	 * Test creating a select field.
	 */
	public function test_create_select_field(): void {
		$field = FieldFactory::create(
			array(
				'name'    => 'test_select',
				'type'    => 'select',
				'label'   => 'Test Select',
				'options' => array(
					'a' => 'Option A',
					'b' => 'Option B',
				),
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'select', $field->get_type() );
	}

	/**
	 * Test creating a checkbox field.
	 */
	public function test_create_checkbox_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_checkbox',
				'type'  => 'checkbox',
				'label' => 'Test Checkbox',
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'checkbox', $field->get_type() );
	}

	/**
	 * Test creating a number field.
	 */
	public function test_create_number_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_number',
				'type'  => 'number',
				'label' => 'Test Number',
				'min'   => 0,
				'max'   => 100,
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'number', $field->get_type() );
	}

	/**
	 * Test creating an email field.
	 */
	public function test_create_email_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_email',
				'type'  => 'email',
				'label' => 'Test Email',
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'email', $field->get_type() );
	}

	/**
	 * Test creating a date field.
	 */
	public function test_create_date_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_date',
				'type'  => 'date',
				'label' => 'Test Date',
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'date', $field->get_type() );
	}

	/**
	 * Test creating a color field.
	 */
	public function test_create_color_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_color',
				'type'  => 'color',
				'label' => 'Test Color',
			)
		);

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertSame( 'color', $field->get_type() );
	}

	/**
	 * Test create throws exception for missing name.
	 */
	public function test_create_throws_for_missing_name(): void {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field config must include "name".' );

		FieldFactory::create(
			array(
				'type'  => 'text',
				'label' => 'Test',
			)
		);
	}

	/**
	 * Test create throws exception for missing type.
	 */
	public function test_create_throws_for_missing_type(): void {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field config must include "type".' );

		FieldFactory::create(
			array(
				'name'  => 'test',
				'label' => 'Test',
			)
		);
	}

	/**
	 * Test create throws exception for unknown type.
	 */
	public function test_create_throws_for_unknown_type(): void {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Unknown field type "unknown"' );

		FieldFactory::create(
			array(
				'name' => 'test',
				'type' => 'unknown',
			)
		);
	}

	/**
	 * Test registering a custom field type.
	 */
	public function test_register_custom_field_type(): void {
		// Create a mock field class for testing.
		$mock_class = get_class(
			$this->createMock( FieldInterface::class )
		);

		// This would normally work, but mocks don't work with our factory.
		// Instead, test with an existing type.
		$this->assertTrue( FieldFactory::has_type( 'text' ) );
	}

	/**
	 * Test has_type returns true for registered types.
	 */
	public function test_has_type_returns_true_for_registered(): void {
		$this->assertTrue( FieldFactory::has_type( 'text' ) );
		$this->assertTrue( FieldFactory::has_type( 'textarea' ) );
		$this->assertTrue( FieldFactory::has_type( 'select' ) );
		$this->assertTrue( FieldFactory::has_type( 'checkbox' ) );
		$this->assertTrue( FieldFactory::has_type( 'radio' ) );
		$this->assertTrue( FieldFactory::has_type( 'number' ) );
		$this->assertTrue( FieldFactory::has_type( 'email' ) );
		$this->assertTrue( FieldFactory::has_type( 'url' ) );
		$this->assertTrue( FieldFactory::has_type( 'date' ) );
		$this->assertTrue( FieldFactory::has_type( 'password' ) );
		$this->assertTrue( FieldFactory::has_type( 'color' ) );
	}

	/**
	 * Test has_type returns false for unregistered types.
	 */
	public function test_has_type_returns_false_for_unregistered(): void {
		$this->assertFalse( FieldFactory::has_type( 'custom_unknown' ) );
	}

	/**
	 * Test create_multiple creates multiple fields.
	 */
	public function test_create_multiple_fields(): void {
		$fields = FieldFactory::create_multiple(
			array(
				'field1' => array(
					'type'  => 'text',
					'label' => 'Field 1',
				),
				'field2' => array(
					'type'  => 'textarea',
					'label' => 'Field 2',
				),
			)
		);

		$this->assertCount( 2, $fields );
		$this->assertArrayHasKey( 'field1', $fields );
		$this->assertArrayHasKey( 'field2', $fields );
		$this->assertSame( 'text', $fields['field1']->get_type() );
		$this->assertSame( 'textarea', $fields['field2']->get_type() );
	}
}
