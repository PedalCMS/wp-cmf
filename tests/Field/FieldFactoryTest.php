<?php
/**
 * FieldFactory Tests
 *
 * Tests for the FieldFactory class.
 *
 * @package Pedalcms\WpCmf\Tests\Field
 */

namespace Pedalcms\WpCmf\Tests\Field;

use Pedalcms\WpCmf\Tests\WpCmfTestCase;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\fields\TextField;
use Pedalcms\WpCmf\Field\fields\SelectField;

/**
 * Class FieldFactoryTest
 */
class FieldFactoryTest extends WpCmfTestCase {

	/**
	 * Set up test environment
	 */
	protected function setUp(): void {
		parent::setUp();
		FieldFactory::reset();
	}

	/**
	 * Test factory creates text field
	 */
	public function test_create_text_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_text',
				'type'  => 'text',
				'label' => 'Test Text',
			)
		);

		$this->assertInstanceOf( TextField::class, $field );
		$this->assertSame( 'test_text', $field->get_name() );
	}

	/**
	 * Test factory creates textarea field
	 */
	public function test_create_textarea_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_textarea',
				'type'  => 'textarea',
				'label' => 'Test Textarea',
			)
		);

		$this->assertSame( 'textarea', $field->get_type() );
	}

	/**
	 * Test factory creates select field
	 */
	public function test_create_select_field(): void {
		$field = FieldFactory::create(
			array(
				'name'    => 'test_select',
				'type'    => 'select',
				'label'   => 'Test Select',
				'options' => array( 'a' => 'A', 'b' => 'B' ),
			)
		);

		$this->assertInstanceOf( SelectField::class, $field );
	}

	/**
	 * Test factory creates checkbox field
	 */
	public function test_create_checkbox_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_checkbox',
				'type'  => 'checkbox',
				'label' => 'Test Checkbox',
			)
		);

		$this->assertSame( 'checkbox', $field->get_type() );
	}

	/**
	 * Test factory creates radio field
	 */
	public function test_create_radio_field(): void {
		$field = FieldFactory::create(
			array(
				'name'    => 'test_radio',
				'type'    => 'radio',
				'label'   => 'Test Radio',
				'options' => array( 'a' => 'A', 'b' => 'B' ),
			)
		);

		$this->assertSame( 'radio', $field->get_type() );
	}

	/**
	 * Test factory creates number field
	 */
	public function test_create_number_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_number',
				'type'  => 'number',
				'label' => 'Test Number',
			)
		);

		$this->assertSame( 'number', $field->get_type() );
	}

	/**
	 * Test factory creates email field
	 */
	public function test_create_email_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_email',
				'type'  => 'email',
				'label' => 'Test Email',
			)
		);

		$this->assertSame( 'email', $field->get_type() );
	}

	/**
	 * Test factory creates URL field
	 */
	public function test_create_url_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_url',
				'type'  => 'url',
				'label' => 'Test URL',
			)
		);

		$this->assertSame( 'url', $field->get_type() );
	}

	/**
	 * Test factory creates date field
	 */
	public function test_create_date_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_date',
				'type'  => 'date',
				'label' => 'Test Date',
			)
		);

		$this->assertSame( 'date', $field->get_type() );
	}

	/**
	 * Test factory creates password field
	 */
	public function test_create_password_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_password',
				'type'  => 'password',
				'label' => 'Test Password',
			)
		);

		$this->assertSame( 'password', $field->get_type() );
	}

	/**
	 * Test factory creates color field
	 */
	public function test_create_color_field(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_color',
				'type'  => 'color',
				'label' => 'Test Color',
			)
		);

		$this->assertSame( 'color', $field->get_type() );
	}

	/**
	 * Test factory creates group field
	 */
	public function test_create_group_field(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_group',
				'type'   => 'group',
				'label'  => 'Test Group',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		$this->assertSame( 'group', $field->get_type() );
	}

	/**
	 * Test factory creates metabox field
	 */
	public function test_create_metabox_field(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_metabox',
				'type'   => 'metabox',
				'label'  => 'Test Metabox',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		$this->assertSame( 'metabox', $field->get_type() );
	}

	/**
	 * Test factory creates tabs field
	 */
	public function test_create_tabs_field(): void {
		$field = FieldFactory::create(
			array(
				'name' => 'test_tabs',
				'type' => 'tabs',
				'label' => 'Test Tabs',
				'tabs' => array(
					array(
						'id'     => 'tab1',
						'title'  => 'Tab 1',
						'fields' => array(
							array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
						),
					),
				),
			)
		);

		$this->assertSame( 'tabs', $field->get_type() );
	}

	/**
	 * Test factory throws for missing name
	 */
	public function test_throws_for_missing_name(): void {
		$this->expectException( \InvalidArgumentException::class );

		FieldFactory::create(
			array(
				'type'  => 'text',
				'label' => 'Test',
			)
		);
	}

	/**
	 * Test factory throws for missing type
	 */
	public function test_throws_for_missing_type(): void {
		$this->expectException( \InvalidArgumentException::class );

		FieldFactory::create(
			array(
				'name'  => 'test',
				'label' => 'Test',
			)
		);
	}

	/**
	 * Test factory throws for unknown type
	 */
	public function test_throws_for_unknown_type(): void {
		$this->expectException( \InvalidArgumentException::class );

		FieldFactory::create(
			array(
				'name'  => 'test',
				'type'  => 'unknown_type',
				'label' => 'Test',
			)
		);
	}

	/**
	 * Test register custom type
	 */
	public function test_register_custom_type(): void {
		FieldFactory::register_type( 'custom_text', TextField::class );

		$field = FieldFactory::create(
			array(
				'name'  => 'test',
				'type'  => 'custom_text',
				'label' => 'Test',
			)
		);

		$this->assertInstanceOf( TextField::class, $field );
	}

	/**
	 * Test has_type
	 */
	public function test_has_type(): void {
		$this->assertTrue( FieldFactory::has_type( 'text' ) );
		$this->assertTrue( FieldFactory::has_type( 'select' ) );
		$this->assertFalse( FieldFactory::has_type( 'non_existent' ) );
	}

	/**
	 * Test get_registered_types
	 */
	public function test_get_registered_types(): void {
		$types = FieldFactory::get_registered_types();

		$this->assertIsArray( $types );
		$this->assertContains( 'text', $types );
		$this->assertContains( 'select', $types );
		$this->assertContains( 'checkbox', $types );
	}

	/**
	 * Test unregister_type
	 */
	public function test_unregister_type(): void {
		FieldFactory::register_type( 'temp_type', TextField::class );
		$this->assertTrue( FieldFactory::has_type( 'temp_type' ) );

		FieldFactory::unregister_type( 'temp_type' );
		$this->assertFalse( FieldFactory::has_type( 'temp_type' ) );
	}

	/**
	 * Test create_multiple
	 */
	public function test_create_multiple(): void {
		$fields = FieldFactory::create_multiple(
			array(
				array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				array( 'name' => 'field2', 'type' => 'textarea', 'label' => 'Field 2' ),
				array( 'name' => 'field3', 'type' => 'number', 'label' => 'Field 3' ),
			)
		);

		$this->assertCount( 3, $fields );
		$this->assertSame( 'text', $fields['field1']->get_type() );
		$this->assertSame( 'textarea', $fields['field2']->get_type() );
		$this->assertSame( 'number', $fields['field3']->get_type() );
	}

	/**
	 * Test reset
	 */
	public function test_reset(): void {
		FieldFactory::register_type( 'custom', TextField::class );
		$this->assertTrue( FieldFactory::has_type( 'custom' ) );

		FieldFactory::reset();
		$this->assertFalse( FieldFactory::has_type( 'custom' ) );

		// Core types should still work
		$this->assertTrue( FieldFactory::has_type( 'text' ) );
	}
}
