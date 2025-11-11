<?php
/**
 * Tests for JSON registration functionality
 *
 * @package Pedalcms\WpCmf\Tests
 */

namespace Pedalcms\WpCmf\Tests\Json;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Json\SchemaValidator;

/**
 * Schema Validator Tests
 */
class SchemaValidatorTest extends TestCase {

	/**
	 * Test valid configuration passes validation
	 */
	public function test_valid_configuration(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts' => array(
				array(
					'id'     => 'book',
					'args'   => array( 'label' => 'Books' ),
					'fields' => array(
						array(
							'name'  => 'isbn',
							'type'  => 'text',
							'label' => 'ISBN',
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertFalse( $validator->has_errors() );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test CPT missing required id field
	 */
	public function test_cpt_missing_id(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts' => array(
				array(
					'args' => array( 'label' => 'Books' ),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertTrue( $validator->has_errors() );
		$this->assertStringContainsString( 'missing required field \'id\'', $validator->get_error_message() );
	}

	/**
	 * Test CPT with invalid id format
	 */
	public function test_cpt_invalid_id_format(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts' => array(
				array(
					'id' => 'Invalid-CPT-Name', // Uppercase and hyphens not allowed
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must be lowercase', $validator->get_error_message() );
	}

	/**
	 * Test settings page missing required id
	 */
	public function test_settings_page_missing_id(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'page_title' => 'Settings',
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'missing required field \'id\'', $validator->get_error_message() );
	}

	/**
	 * Test field missing required name
	 */
	public function test_field_missing_name(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'settings',
					'fields' => array(
						array(
							'type'  => 'text',
							'label' => 'Field',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'missing required field \'name\'', $validator->get_error_message() );
	}

	/**
	 * Test field missing required type
	 */
	public function test_field_missing_type(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'settings',
					'fields' => array(
						array(
							'name'  => 'my_field',
							'label' => 'Field',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'missing required field \'type\'', $validator->get_error_message() );
	}

	/**
	 * Test field with invalid type
	 */
	public function test_field_invalid_type(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'settings',
					'fields' => array(
						array(
							'name'  => 'my_field',
							'type'  => 'invalid_type',
							'label' => 'Field',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must be one of:', $validator->get_error_message() );
	}

	/**
	 * Test field with invalid name format
	 */
	public function test_field_invalid_name_format(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'settings',
					'fields' => array(
						array(
							'name'  => '123-invalid',
							'type'  => 'text',
							'label' => 'Field',
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must start with letter', $validator->get_error_message() );
	}

	/**
	 * Test all core field types are valid
	 */
	public function test_all_core_field_types_valid(): void {
		$validator = new SchemaValidator();

		$types = array( 'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'url', 'date', 'password', 'color' );

		foreach ( $types as $type ) {
			$field = array(
				'name' => 'test_field',
				'type' => $type,
			);

			// Fields that require options
			if ( in_array( $type, array( 'select', 'checkbox', 'radio' ), true ) ) {
				$field['options'] = array(
					'option1' => 'Option 1',
					'option2' => 'Option 2',
				);
			}

			$config = array(
				'settings_pages' => array(
					array(
						'id'     => 'settings',
						'fields' => array( $field ),
					),
				),
			);

			$this->assertTrue( $validator->validate( $config ), "Field type '{$type}' should be valid" );
		}
	}

	/**
	 * Test field with invalid boolean values
	 */
	public function test_field_invalid_boolean(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'settings',
					'fields' => array(
						array(
							'name'     => 'test_field',
							'type'     => 'text',
							'required' => 'yes', // Should be boolean
						),
					),
				),
			),
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must be a boolean', $validator->get_error_message() );
	}

	/**
	 * Test complex valid configuration
	 */
	public function test_complex_valid_configuration(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'product',
					'args'   => array(
						'label'    => 'Products',
						'public'   => true,
						'supports' => array( 'title', 'editor' ),
					),
					'fields' => array(
						array(
							'name'     => 'price',
							'type'     => 'number',
							'label'    => 'Price',
							'required' => true,
							'min'      => 0,
						),
						array(
							'name'     => 'category',
							'type'     => 'select',
							'label'    => 'Category',
							'options'  => array(
								'electronics' => 'Electronics',
								'books'       => 'Books',
							),
							'multiple' => false,
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'shop_settings',
					'page_title' => 'Shop Settings',
					'capability' => 'manage_options',
					'fields'     => array(
						array(
							'name'     => 'store_email',
							'type'     => 'email',
							'label'    => 'Store Email',
							'required' => true,
						),
					),
				),
			),
		);

		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );
	}

	/**
	 * Test empty configuration is valid
	 */
	public function test_empty_configuration(): void {
		$validator = new SchemaValidator();

		$config = array();

		$this->assertTrue( $validator->validate( $config ) );
	}

	/**
	 * Test cpts not array
	 */
	public function test_cpts_not_array(): void {
		$validator = new SchemaValidator();

		$config = array(
			'cpts' => 'not an array',
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must be an array', $validator->get_error_message() );
	}

	/**
	 * Test settings_pages not array
	 */
	public function test_settings_pages_not_array(): void {
		$validator = new SchemaValidator();

		$config = array(
			'settings_pages' => 'not an array',
		);

		$this->assertFalse( $validator->validate( $config ) );
		$this->assertStringContainsString( 'must be an array', $validator->get_error_message() );
	}
}
