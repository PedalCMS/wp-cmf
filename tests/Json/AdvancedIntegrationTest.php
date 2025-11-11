<?php
/**
 * Tests for advanced integration scenarios
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Json;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Json\SchemaValidator;

/**
 * Advanced Integration Test class
 */
class AdvancedIntegrationTest extends TestCase {

	/**
	 * Reset Manager instance between tests
	 */
	protected function tearDown(): void {
		parent::tearDown();
		$reflection = new \ReflectionClass( Manager::class );
		$instance   = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );
	}

	/**
	 * Test large configuration with many CPTs and fields
	 */
	public function test_large_configuration(): void {
		$cpts = array();

		// Generate 10 CPTs with 10 fields each
		for ( $i = 1; $i <= 10; $i++ ) {
			$fields = array();
			for ( $j = 1; $j <= 10; $j++ ) {
				$fields[] = array(
					'name'  => "field_{$i}_{$j}",
					'type'  => 'text',
					'label' => "Field {$i}-{$j}",
				);
			}

			// Use letter suffixes to avoid pattern issue
			$letter_suffix = chr( 96 + $i ); // a-j
			$cpts[]        = array(
				'id'     => "cpt_{$letter_suffix}",
				'args'   => array(
					'label' => "CPT {$i}",
				),
				'fields' => $fields,
			);
		}

		$config = array( 'cpts' => $cpts );

		$validator = new SchemaValidator();
		$is_valid  = $validator->validate( $config );
		if ( ! $is_valid ) {
			$this->fail( 'Validation failed: ' . $validator->get_error_message() );
		}
		$this->assertTrue( $is_valid );
		$this->assertEmpty( $validator->get_errors() );

		// Test registration
		$json    = json_encode( $config );
		$manager = Manager::init();
		$this->assertInstanceOf( Manager::class, $manager->register_from_json( $json ) );
	}

	/**
	 * Test configuration with all 11 field types
	 */
	public function test_all_field_types_together(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'comprehensive',
					'fields' => array(
						array(
							'name' => 'text_field',
							'type' => 'text',
						),
						array(
							'name' => 'textarea_field',
							'type' => 'textarea',
						),
						array(
							'name'    => 'select_field',
							'type'    => 'select',
							'options' => array(
								'opt1' => 'Option 1',
							),
						),
						array(
							'name'    => 'checkbox_field',
							'type'    => 'checkbox',
							'options' => array(
								'chk1' => 'Check 1',
							),
						),
						array(
							'name'    => 'radio_field',
							'type'    => 'radio',
							'options' => array(
								'rad1' => 'Radio 1',
							),
						),
						array(
							'name' => 'number_field',
							'type' => 'number',
						),
						array(
							'name' => 'email_field',
							'type' => 'email',
						),
						array(
							'name' => 'url_field',
							'type' => 'url',
						),
						array(
							'name' => 'date_field',
							'type' => 'date',
						),
						array(
							'name' => 'password_field',
							'type' => 'password',
						),
						array(
							'name' => 'color_field',
							'type' => 'color',
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ) );

		$json    = json_encode( $config );
		$manager = Manager::init();
		$this->assertInstanceOf( Manager::class, $manager->register_from_json( $json ) );
	}

	/**
	 * Test mixed CPTs and settings pages with complex field configurations
	 */
	public function test_mixed_complex_configuration(): void {
		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'product',
					'args'   => array(
						'label'        => 'Products',
						'public'       => true,
						'show_in_rest' => true,
						'supports'     => array( 'title', 'editor', 'thumbnail' ),
					),
					'fields' => array(
						array(
							'name'        => 'sku',
							'type'        => 'text',
							'label'       => 'SKU',
							'required'    => true,
							'pattern'     => '^[A-Z0-9-]+$',
							'description' => 'Product SKU code',
						),
						array(
							'name'  => 'price',
							'type'  => 'number',
							'label' => 'Price',
							'min'   => 0,
							'step'  => 0.01,
						),
						array(
							'name'     => 'description',
							'type'     => 'textarea',
							'label'    => 'Description',
							'rows'     => 10,
							'cols'     => 50,
							'context'  => 'normal',
							'priority' => 'high',
						),
						array(
							'name'    => 'category',
							'type'    => 'select',
							'label'   => 'Category',
							'options' => array(
								'electronics' => 'Electronics',
								'clothing'    => 'Clothing',
								'books'       => 'Books',
							),
						),
						array(
							'name'     => 'tags',
							'type'     => 'checkbox',
							'label'    => 'Tags',
							'multiple' => true,
							'inline'   => true,
							'options'  => array(
								'new'      => 'New',
								'featured' => 'Featured',
								'sale'     => 'Sale',
							),
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'shop_settings',
					'page_title' => 'Shop Settings',
					'menu_title' => 'Shop',
					'capability' => 'manage_options',
					'fields'     => array(
						array(
							'name'        => 'store_name',
							'type'        => 'text',
							'label'       => 'Store Name',
							'required'    => true,
							'placeholder' => 'My Awesome Store',
						),
						array(
							'name'     => 'store_email',
							'type'     => 'email',
							'label'    => 'Store Email',
							'required' => true,
						),
						array(
							'name'    => 'currency',
							'type'    => 'select',
							'label'   => 'Currency',
							'options' => array(
								'USD' => 'US Dollar',
								'EUR' => 'Euro',
								'GBP' => 'British Pound',
							),
						),
						array(
							'name'    => 'theme_color',
							'type'    => 'color',
							'label'   => 'Theme Color',
							'default' => '#4CAF50',
						),
						array(
							'name'     => 'opening_date',
							'type'     => 'date',
							'label'    => 'Store Opening Date',
							'min'      => '2020-01-01',
							'max'      => '2030-12-31',
							'readonly' => false,
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ) );
		$this->assertEmpty( $validator->get_errors() );

		// Test registration
		$json    = json_encode( $config );
		$manager = Manager::init();
		$result  = $manager->register_from_json( $json );
		$this->assertInstanceOf( Manager::class, $result );
	}

	/**
	 * Test field validation rules combinations
	 */
	public function test_field_validation_combinations(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'validation_test',
					'fields' => array(
						array(
							'name'        => 'username',
							'type'        => 'text',
							'required'    => true,
							'pattern'     => '^[a-z0-9_]+$',
							'maxlength'   => 20,
							'placeholder' => 'username',
							'class'       => 'regular-text',
						),
						array(
							'name'    => 'age',
							'type'    => 'number',
							'min'     => 18,
							'max'     => 120,
							'step'    => 1,
							'default' => 25,
						),
						array(
							'name' => 'bio',
							'type' => 'textarea',
							'rows' => 5,
							'cols' => 50,
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ) );

		$json    = json_encode( $config );
		$manager = Manager::init();
		$this->assertInstanceOf( Manager::class, $manager->register_from_json( $json ) );
	}

	/**
	 * Test metabox context and priority variations
	 */
	public function test_metabox_contexts_and_priorities(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'document',
					'fields' => array(
						array(
							'name'     => 'normal_high',
							'type'     => 'text',
							'context'  => 'normal',
							'priority' => 'high',
						),
						array(
							'name'     => 'normal_default',
							'type'     => 'text',
							'context'  => 'normal',
							'priority' => 'default',
						),
						array(
							'name'     => 'normal_low',
							'type'     => 'text',
							'context'  => 'normal',
							'priority' => 'low',
						),
						array(
							'name'    => 'side_high',
							'type'    => 'text',
							'context' => 'side',
						),
						array(
							'name'    => 'advanced_default',
							'type'    => 'text',
							'context' => 'advanced',
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ) );

		$json    = json_encode( $config );
		$manager = Manager::init();
		$this->assertInstanceOf( Manager::class, $manager->register_from_json( $json ) );
	}

	/**
	 * Test readonly and disabled field combinations
	 */
	public function test_readonly_disabled_fields(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'readonly_test',
					'fields' => array(
						array(
							'name'     => 'readonly_field',
							'type'     => 'text',
							'readonly' => true,
							'default'  => 'Cannot edit',
						),
						array(
							'name'     => 'disabled_field',
							'type'     => 'text',
							'disabled' => true,
						),
						array(
							'name'     => 'normal_field',
							'type'     => 'text',
							'readonly' => false,
							'disabled' => false,
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ) );

		$json    = json_encode( $config );
		$manager = Manager::init();
		$this->assertInstanceOf( Manager::class, $manager->register_from_json( $json ) );
	}

	/**
	 * Test configuration with maximum complexity
	 */
	public function test_maximum_complexity_configuration(): void {
		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'complex_cpt',
					'args'   => array(
						'label'              => 'Complex Items',
						'labels'             => array(
							'name'          => 'Complex Items',
							'singular_name' => 'Complex Item',
							'add_new'       => 'Add Complex',
							'edit_item'     => 'Edit Complex',
						),
						'description'        => 'A highly complex custom post type',
						'public'             => true,
						'hierarchical'       => false,
						'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
						'menu_icon'          => 'dashicons-admin-generic',
						'menu_position'      => 25,
						'show_in_rest'       => true,
						'has_archive'        => true,
						'publicly_queryable' => true,
						'rewrite'            => array(
							'slug'       => 'complex',
							'with_front' => false,
						),
					),
					'fields' => array(
						array(
							'name'          => 'complex_text',
							'type'          => 'text',
							'label'         => 'Complex Text Field',
							'description'   => 'This is a complex text field with many options',
							'default'       => 'Default value',
							'required'      => true,
							'placeholder'   => 'Enter text here',
							'maxlength'     => 200,
							'class'         => 'regular-text custom-class',
							'wrapper_class' => 'field-wrapper',
							'context'       => 'normal',
							'priority'      => 'high',
						),
						array(
							'name'     => 'complex_number',
							'type'     => 'number',
							'label'    => 'Complex Number',
							'min'      => 0,
							'max'      => 1000,
							'step'     => 0.5,
							'default'  => 10,
							'required' => false,
							'context'  => 'side',
						),
						array(
							'name'        => 'complex_textarea',
							'type'        => 'textarea',
							'label'       => 'Complex Textarea',
							'description' => 'Long description field',
							'rows'        => 10,
							'cols'        => 50,
							'maxlength'   => 1000,
							'placeholder' => 'Enter long text',
							'context'     => 'normal',
							'priority'    => 'default',
						),
						array(
							'name'     => 'complex_select',
							'type'     => 'select',
							'label'    => 'Complex Select',
							'multiple' => false,
							'options'  => array(
								'opt1' => 'Option 1',
								'opt2' => 'Option 2',
								'opt3' => 'Option 3',
								'opt4' => 'Option 4',
							),
							'default'  => 'opt1',
							'context'  => 'side',
						),
						array(
							'name'     => 'complex_checkbox',
							'type'     => 'checkbox',
							'label'    => 'Complex Checkbox',
							'multiple' => true,
							'inline'   => true,
							'options'  => array(
								'chk1' => 'Check 1',
								'chk2' => 'Check 2',
								'chk3' => 'Check 3',
							),
							'default'  => array( 'chk1' ),
							'context'  => 'advanced',
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'complex_settings',
					'page_title' => 'Complex Settings Page',
					'menu_title' => 'Complex',
					'capability' => 'manage_options',
					'icon'       => 'dashicons-admin-settings',
					'position'   => 50,
					'fields'     => array(
						array(
							'name'        => 'setting_text',
							'type'        => 'text',
							'label'       => 'Setting Text',
							'description' => 'A text setting',
							'default'     => 'Default',
						),
						array(
							'name'    => 'setting_email',
							'type'    => 'email',
							'label'   => 'Contact Email',
							'default' => 'admin@example.com',
						),
						array(
							'name'    => 'setting_url',
							'type'    => 'url',
							'label'   => 'Website URL',
							'default' => 'https://example.com',
						),
						array(
							'name'    => 'setting_color',
							'type'    => 'color',
							'label'   => 'Brand Color',
							'default' => '#0073aa',
						),
						array(
							'name'  => 'setting_date',
							'type'  => 'date',
							'label' => 'Launch Date',
							'min'   => '2024-01-01',
							'max'   => '2025-12-31',
						),
					),
				),
			),
		);

		$validator = new SchemaValidator();
		$this->assertTrue( $validator->validate( $config ), 'Configuration should be valid' );
		$this->assertEmpty( $validator->get_errors(), 'Should have no validation errors' );

		// Test registration
		$json    = json_encode( $config );
		$manager = Manager::init();
		$result  = $manager->register_from_json( $json );
		$this->assertInstanceOf( Manager::class, $result );
	}

	/**
	 * Test performance with stress configuration
	 */
	public function test_performance_stress(): void {
		$fields = array();
		// Create 50 fields of various types
		for ( $i = 1; $i <= 50; $i++ ) {
			$type = array( 'text', 'textarea', 'number', 'email', 'url', 'date', 'password', 'color' )[ $i % 8 ];

			$field = array(
				'name'  => "field_{$i}",
				'type'  => $type,
				'label' => "Field {$i}",
			);

			if ( in_array( $type, array( 'number' ), true ) ) {
				$field['min'] = 0;
				$field['max'] = 100;
			}

			if ( in_array( $type, array( 'date' ), true ) ) {
				$field['min'] = '2024-01-01';
				$field['max'] = '2024-12-31';
			}

			if ( 'textarea' === $type ) {
				$field['rows'] = 5;
			}

			$fields[] = $field;
		}

		$config = array(
			'cpts' => array(
				array(
					'id'     => 'stress_test',
					'fields' => $fields,
				),
			),
		);

		$start_time = microtime( true );
		$validator  = new SchemaValidator();
		$result     = $validator->validate( $config );
		$end_time   = microtime( true );

		$this->assertTrue( $result );
		$this->assertEmpty( $validator->get_errors() );

		// Validation should complete in reasonable time (< 1 second)
		$duration = $end_time - $start_time;
		$this->assertLessThan( 1.0, $duration, 'Validation should complete within 1 second' );
	}
}
