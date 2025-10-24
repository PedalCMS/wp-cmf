<?php
/**
 * Tests for FieldFactory
 *
 * @package Pedalcms\WpCmf\Tests
 */

namespace Pedalcms\WpCmf\Tests\Field;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\Fields\TextField;
use Pedalcms\WpCmf\Field\Fields\TextareaField;
use Pedalcms\WpCmf\Field\Fields\SelectField;
use Pedalcms\WpCmf\Field\Fields\CheckboxField;
use Pedalcms\WpCmf\Field\Fields\RadioField;
use Pedalcms\WpCmf\Field\Fields\NumberField;
use Pedalcms\WpCmf\Field\Fields\EmailField;
use Pedalcms\WpCmf\Field\Fields\URLField;
use Pedalcms\WpCmf\Field\Fields\DateField;
use Pedalcms\WpCmf\Field\Fields\PasswordField;
use Pedalcms\WpCmf\Field\Fields\ColorField;

/**
 * FieldFactory test case
 */
class FieldFactoryTest extends TestCase {

	/**
	 * Set up before each test
	 */
	protected function setUp(): void {
		parent::setUp();
		FieldFactory::reset();
	}

	/**
	 * Tear down after each test
	 */
	protected function tearDown(): void {
		FieldFactory::reset();
		parent::tearDown();
	}

	/**
	 * Test that default field types are registered automatically
	 */
	public function test_registers_default_field_types(): void {
		$types = FieldFactory::get_registered_types();

		$this->assertIsArray( $types );
		$this->assertCount( 11, $types );
		$this->assertArrayHasKey( 'text', $types );
		$this->assertArrayHasKey( 'textarea', $types );
		$this->assertArrayHasKey( 'select', $types );
		$this->assertArrayHasKey( 'checkbox', $types );
		$this->assertArrayHasKey( 'radio', $types );
		$this->assertArrayHasKey( 'number', $types );
		$this->assertArrayHasKey( 'email', $types );
		$this->assertArrayHasKey( 'url', $types );
		$this->assertArrayHasKey( 'date', $types );
		$this->assertArrayHasKey( 'password', $types );
		$this->assertArrayHasKey( 'color', $types );
	}

	/**
	 * Test that default field types map to correct classes
	 */
	public function test_default_field_types_have_correct_classes(): void {
		$types = FieldFactory::get_registered_types();

		$this->assertEquals( TextField::class, $types['text'] );
		$this->assertEquals( TextareaField::class, $types['textarea'] );
		$this->assertEquals( SelectField::class, $types['select'] );
		$this->assertEquals( CheckboxField::class, $types['checkbox'] );
		$this->assertEquals( RadioField::class, $types['radio'] );
		$this->assertEquals( NumberField::class, $types['number'] );
		$this->assertEquals( EmailField::class, $types['email'] );
		$this->assertEquals( URLField::class, $types['url'] );
		$this->assertEquals( DateField::class, $types['date'] );
		$this->assertEquals( PasswordField::class, $types['password'] );
		$this->assertEquals( ColorField::class, $types['color'] );
	}

	/**
	 * Test creating a text field from config
	 */
	public function test_creates_text_field_from_config(): void {
		$config = array(
			'name'  => 'test_field',
			'type'  => 'text',
			'label' => 'Test Field',
		);

		$field = FieldFactory::create( $config );

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertInstanceOf( TextField::class, $field );
		$this->assertEquals( 'test_field', $field->get_name() );
	}

	/**
	 * Test creating multiple different field types
	 */
	public function test_creates_different_field_types(): void {
		$configs = array(
			array(
				'name' => 'text_field',
				'type' => 'text',
			),
			array(
				'name' => 'email_field',
				'type' => 'email',
			),
			array(
				'name' => 'number_field',
				'type' => 'number',
			),
			array(
				'name' => 'select_field',
				'type' => 'select',
			),
		);

		$text   = FieldFactory::create( $configs[0] );
		$email  = FieldFactory::create( $configs[1] );
		$number = FieldFactory::create( $configs[2] );
		$select = FieldFactory::create( $configs[3] );

		$this->assertInstanceOf( TextField::class, $text );
		$this->assertInstanceOf( EmailField::class, $email );
		$this->assertInstanceOf( NumberField::class, $number );
		$this->assertInstanceOf( SelectField::class, $select );
	}

	/**
	 * Test registering a custom field type
	 */
	public function test_registers_custom_field_type(): void {
		// Use an existing field class as a custom type
		FieldFactory::register_type( 'custom', TextField::class );

		$this->assertTrue( FieldFactory::has_type( 'custom' ) );

		$types = FieldFactory::get_registered_types();
		$this->assertArrayHasKey( 'custom', $types );
	}

	/**
	 * Test creating a custom field type
	 */
	public function test_creates_custom_field_type(): void {
		// Use an existing field class as a custom type
		FieldFactory::register_type( 'custom', TextField::class );

		$field = FieldFactory::create(
			array(
				'name' => 'custom_field',
				'type' => 'custom',
			)
		);

		$this->assertInstanceOf( TextField::class, $field );
	}

	/**
	 * Test exception when creating field without name
	 */
	public function test_throws_exception_when_name_missing(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field config must include "name".' );

		FieldFactory::create( array( 'type' => 'text' ) );
	}

	/**
	 * Test exception when creating field without type
	 */
	public function test_throws_exception_when_type_missing(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field config must include "type".' );

		FieldFactory::create( array( 'name' => 'test' ) );
	}

	/**
	 * Test exception when creating field with unknown type
	 */
	public function test_throws_exception_for_unknown_type(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Unknown field type "unknown_type"' );

		FieldFactory::create(
			array(
				'name' => 'test',
				'type' => 'unknown_type',
			)
		);
	}

	/**
	 * Test exception when registering non-existent class
	 */
	public function test_throws_exception_for_non_existent_class(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Class "NonExistentClass" does not exist.' );

		FieldFactory::register_type( 'custom', 'NonExistentClass' );
	}

	/**
	 * Test exception when registering class that doesn't implement FieldInterface
	 */
	public function test_throws_exception_for_invalid_field_class(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'must implement FieldInterface' );

		FieldFactory::register_type( 'invalid', \stdClass::class );
	}

	/**
	 * Test has_type method
	 */
	public function test_has_type_method(): void {
		$this->assertTrue( FieldFactory::has_type( 'text' ) );
		$this->assertTrue( FieldFactory::has_type( 'email' ) );
		$this->assertFalse( FieldFactory::has_type( 'nonexistent' ) );
	}

	/**
	 * Test unregister_type method
	 */
	public function test_unregister_type_method(): void {
		$this->assertTrue( FieldFactory::has_type( 'text' ) );

		FieldFactory::unregister_type( 'text' );

		$this->assertFalse( FieldFactory::has_type( 'text' ) );
	}

	/**
	 * Test create_multiple method
	 */
	public function test_creates_multiple_fields_from_array(): void {
		$configs = array(
			'field1' => array( 'type' => 'text' ),
			'field2' => array( 'type' => 'email' ),
			'field3' => array(
				'name' => 'custom_name',
				'type' => 'number',
			),
		);

		$fields = FieldFactory::create_multiple( $configs );

		$this->assertIsArray( $fields );
		$this->assertCount( 3, $fields );
		$this->assertArrayHasKey( 'field1', $fields );
		$this->assertArrayHasKey( 'field2', $fields );
		$this->assertArrayHasKey( 'custom_name', $fields );

		$this->assertInstanceOf( TextField::class, $fields['field1'] );
		$this->assertInstanceOf( EmailField::class, $fields['field2'] );
		$this->assertInstanceOf( NumberField::class, $fields['custom_name'] );
	}

	/**
	 * Test that create_multiple uses array key as name if name not provided
	 */
	public function test_create_multiple_uses_array_key_as_name(): void {
		$configs = array(
			'my_field' => array( 'type' => 'text' ),
		);

		$fields = FieldFactory::create_multiple( $configs );

		$this->assertEquals( 'my_field', $fields['my_field']->get_name() );
	}

	/**
	 * Test that field configuration is passed to field instance
	 */
	public function test_field_receives_configuration(): void {
		$config = array(
			'name'        => 'test_field',
			'type'        => 'text',
			'label'       => 'Test Label',
			'description' => 'Test Description',
			'default'     => 'Default Value',
		);

		$field = FieldFactory::create( $config );

		$this->assertEquals( 'Test Label', $field->get_config( 'label' ) );
		$this->assertEquals( 'Test Description', $field->get_config( 'description' ) );
		$this->assertEquals( 'Default Value', $field->get_config( 'default' ) );
	}

	/**
	 * Test that defaults are only registered once
	 */
	public function test_defaults_only_registered_once(): void {
		// First call registers defaults
		FieldFactory::get_registered_types();

		// Register a custom type
		$custom_field_class = new class('test', 'custom', array()) extends TextField {
		};
		FieldFactory::register_type( 'custom', get_class( $custom_field_class ) );

		// Second call should not reset the custom type
		$types = FieldFactory::get_registered_types();

		$this->assertArrayHasKey( 'custom', $types );
		$this->assertCount( 12, $types ); // 11 defaults + 1 custom
	}

	/**
	 * Test reset method clears all registrations
	 */
	public function test_reset_clears_all_registrations(): void {
		// Ensure defaults are registered first
		FieldFactory::get_registered_types();

		// Register a custom type
		FieldFactory::register_type( 'custom', TextField::class );

		$this->assertTrue( FieldFactory::has_type( 'custom' ) );

		// Reset
		FieldFactory::reset();

		// Custom type should be gone, but defaults will re-register
		$types = FieldFactory::get_registered_types();
		$this->assertArrayNotHasKey( 'custom', $types );
		$this->assertCount( 11, $types ); // Only defaults
	}

	/**
	 * Test creating all core field types
	 */
	public function test_creates_all_core_field_types(): void {
		$core_types = array(
			'text'     => TextField::class,
			'textarea' => TextareaField::class,
			'select'   => SelectField::class,
			'checkbox' => CheckboxField::class,
			'radio'    => RadioField::class,
			'number'   => NumberField::class,
			'email'    => EmailField::class,
			'url'      => URLField::class,
			'date'     => DateField::class,
			'password' => PasswordField::class,
			'color'    => ColorField::class,
		);

		foreach ( $core_types as $type => $expected_class ) {
			$field = FieldFactory::create(
				array(
					'name' => "test_{$type}",
					'type' => $type,
				)
			);
			$this->assertInstanceOf( $expected_class, $field, "Failed to create {$type} field" );
		}
	}
}
