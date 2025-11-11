<?php
/**
 * Tests for adding fields to existing post types and settings pages
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Core;

use Pedalcms\WpCmf\Core\Manager;
use PHPUnit\Framework\TestCase;

/**
 * Test adding fields to existing post types and settings pages
 */
class ExistingRegistrationTest extends TestCase {

	/**
	 * Test adding fields to existing post type (post) via array
	 */
	public function test_add_fields_to_existing_post_type_array(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'post',
					'fields' => array(
						array(
							'name'  => 'subtitle',
							'type'  => 'text',
							'label' => 'Subtitle',
						),
						array(
							'name'  => 'featured',
							'type'  => 'checkbox',
							'label' => 'Featured Post',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Should NOT create a new CPT (post already exists)
		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayNotHasKey( 'post', $cpts, 'Should not create new CPT for existing post type' );

		// Should have fields registered for 'post' context
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'post', $fields, 'Should have fields for post context' );
		$this->assertCount( 2, $fields['post'], 'Should have 2 fields' );
	}

	/**
	 * Test adding fields to existing post type (page) via array
	 */
	public function test_add_fields_to_existing_page_type_array(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'page',
					'fields' => array(
						array(
							'name'  => 'page_subtitle',
							'type'  => 'text',
							'label' => 'Page Subtitle',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayNotHasKey( 'page', $cpts, 'Should not create new CPT for existing page type' );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'page', $fields );
		$this->assertCount( 1, $fields['page'] );
	}

	/**
	 * Test adding fields to existing settings page (general) via array
	 */
	public function test_add_fields_to_existing_settings_page_array(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'  => 'site_tagline_extended',
							'type'  => 'text',
							'label' => 'Extended Tagline',
						),
						array(
							'name'  => 'maintenance_mode',
							'type'  => 'checkbox',
							'label' => 'Enable Maintenance Mode',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Should NOT create a new settings page (general already exists in WordPress)
		$pages = $registrar->get_settings_pages();
		$this->assertArrayNotHasKey( 'general', $pages, 'Should not create new settings page for existing general settings' );

		// Should have fields registered for 'general' context
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields, 'Should have fields for general context' );
		$this->assertCount( 2, $fields['general'], 'Should have 2 fields' );
	}

	/**
	 * Test adding fields to multiple existing settings pages via array
	 */
	public function test_add_fields_to_multiple_existing_settings_pages_array(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'  => 'custom_field_1',
							'type'  => 'text',
							'label' => 'Custom Field 1',
						),
					),
				),
				array(
					'id'     => 'reading',
					'fields' => array(
						array(
							'name'  => 'custom_field_2',
							'type'  => 'number',
							'label' => 'Custom Field 2',
						),
					),
				),
				array(
					'id'     => 'writing',
					'fields' => array(
						array(
							'name'  => 'custom_field_3',
							'type'  => 'checkbox',
							'label' => 'Custom Field 3',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$pages = $registrar->get_settings_pages();
		$this->assertArrayNotHasKey( 'general', $pages, 'Should not create general page' );
		$this->assertArrayNotHasKey( 'reading', $pages, 'Should not create reading page' );
		$this->assertArrayNotHasKey( 'writing', $pages, 'Should not create writing page' );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );
		$this->assertArrayHasKey( 'reading', $fields );
		$this->assertArrayHasKey( 'writing', $fields );
		$this->assertGreaterThanOrEqual( 1, count( $fields['general'] ), 'General should have at least 1 field' );
		$this->assertGreaterThanOrEqual( 1, count( $fields['reading'] ), 'Reading should have at least 1 field' );
		$this->assertGreaterThanOrEqual( 1, count( $fields['writing'] ), 'Writing should have at least 1 field' );
	}

	/**
	 * Test adding fields to existing post type via JSON
	 */
	public function test_add_fields_to_existing_post_type_json(): void {
		$json = json_encode(
			array(
				'cpts' => array(
					array(
						'id'     => 'post',
						'fields' => array(
							array(
								'name'  => 'reading_time',
								'type'  => 'number',
								'label' => 'Reading Time (minutes)',
								'min'   => 1,
							),
						),
					),
				),
			)
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_json( $json );

		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayNotHasKey( 'post', $cpts );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'post', $fields );
		$this->assertGreaterThanOrEqual( 1, count( $fields['post'] ), 'Post should have at least 1 field' );
	}

	/**
	 * Test adding fields to existing settings page via JSON
	 */
	public function test_add_fields_to_existing_settings_page_json(): void {
		$json = json_encode(
			array(
				'settings_pages' => array(
					array(
						'id'     => 'general',
						'fields' => array(
							array(
								'name'        => 'contact_email',
								'type'        => 'email',
								'label'       => 'Contact Email',
								'placeholder' => 'contact@example.com',
							),
						),
					),
				),
			)
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_json( $json );

		$pages = $registrar->get_settings_pages();
		$this->assertArrayNotHasKey( 'general', $pages );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );
		$this->assertGreaterThanOrEqual( 1, count( $fields['general'] ), 'General should have at least 1 field' );
	}

	/**
	 * Test mixed scenario: new and existing CPTs
	 */
	public function test_mixed_new_and_existing_cpts(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'post',  // Existing
					'fields' => array(
						array(
							'name'  => 'custom_meta',
							'type'  => 'text',
							'label' => 'Custom Meta',
						),
					),
				),
				array(
					'id'     => 'book',  // New
					'args'   => array(
						'label' => 'Books',
					),
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

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayNotHasKey( 'post', $cpts, 'Post should not be registered as new CPT' );
		$this->assertArrayHasKey( 'book', $cpts, 'Book should be registered as new CPT' );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'post', $fields, 'Post should have fields' );
		$this->assertArrayHasKey( 'book', $fields, 'Book should have fields' );
	}

	/**
	 * Test mixed scenario: new and existing settings pages
	 */
	public function test_mixed_new_and_existing_settings_pages(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',  // Existing
					'fields' => array(
						array(
							'name'  => 'site_footer_text',
							'type'  => 'textarea',
							'label' => 'Footer Text',
						),
					),
				),
				array(
					'id'         => 'my-plugin-settings',  // New
					'page_title' => 'My Plugin',
					'menu_title' => 'My Plugin',
					'capability' => 'manage_options',
					'fields'     => array(
						array(
							'name'  => 'api_key',
							'type'  => 'text',
							'label' => 'API Key',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$pages = $registrar->get_settings_pages();
		$this->assertArrayNotHasKey( 'general', $pages, 'General should not be registered as new page' );
		$this->assertArrayHasKey( 'my-plugin-settings', $pages, 'My Plugin should be registered as new page' );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields, 'General should have fields' );
		$this->assertArrayHasKey( 'my-plugin-settings', $fields, 'My Plugin should have fields' );
	}

	/**
	 * Test that field instances are created correctly for existing post types
	 */
	public function test_field_instances_for_existing_post_types(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'post',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'text',
							'label' => 'Test Field',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'post', $fields, 'Post should have fields' );
		$this->assertNotEmpty( $fields['post'], 'Post fields should not be empty' );

		// Find our test field
		$test_field = null;
		foreach ( $fields['post'] as $field ) {
			if ( $field->get_name() === 'test_field' ) {
				$test_field = $field;
				break;
			}
		}

		$this->assertNotNull( $test_field, 'Test field should exist' );
		$this->assertInstanceOf(
			\Pedalcms\WpCmf\Field\FieldInterface::class,
			$test_field,
			'Field should be FieldInterface instance'
		);
	}

	/**
	 * Test that field instances are created correctly for existing settings pages
	 */
	public function test_field_instances_for_existing_settings_pages(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'  => 'test_field',
							'type'  => 'email',
							'label' => 'Test Email',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields, 'General should have fields' );
		$this->assertNotEmpty( $fields['general'], 'General fields should not be empty' );

		// Find our test field
		$test_field = null;
		foreach ( $fields['general'] as $field ) {
			if ( $field->get_name() === 'test_field' ) {
				$test_field = $field;
				break;
			}
		}

		$this->assertNotNull( $test_field, 'Test field should exist' );
		$this->assertInstanceOf(
			\Pedalcms\WpCmf\Field\FieldInterface::class,
			$test_field,
			'Field should be FieldInterface instance'
		);
	}

	/**
	 * Test single checkbox without options for existing settings page
	 */
	public function test_single_checkbox_for_existing_settings_page(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'  => 'maintenance_mode',
							'type'  => 'checkbox',
							'label' => 'Enable Maintenance Mode',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );

		// Find our maintenance_mode field
		$maintenance_field = null;
		foreach ( $fields['general'] as $field ) {
			if ( $field->get_name() === 'maintenance_mode' ) {
				$maintenance_field = $field;
				break;
			}
		}

		$this->assertNotNull( $maintenance_field, 'Maintenance mode field should exist' );
		$this->assertInstanceOf(
			\Pedalcms\WpCmf\Field\Fields\CheckboxField::class,
			$maintenance_field
		);
	}

	/**
	 * Test multiple checkboxes with options for existing settings page
	 */
	public function test_multiple_checkboxes_for_existing_settings_page(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'    => 'social_sharing',
							'type'    => 'checkbox',
							'label'   => 'Social Sharing',
							'options' => array(
								'facebook' => 'Facebook',
								'twitter'  => 'Twitter',
								'linkedin' => 'LinkedIn',
							),
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );

		// Find our social_sharing field
		$social_field = null;
		foreach ( $fields['general'] as $field ) {
			if ( $field->get_name() === 'social_sharing' ) {
				$social_field = $field;
				break;
			}
		}

		$this->assertNotNull( $social_field, 'Social sharing field should exist' );
		$this->assertInstanceOf(
			\Pedalcms\WpCmf\Field\Fields\CheckboxField::class,
			$social_field
		);
	}

	/**
	 * Test all 11 field types for existing settings page
	 */
	public function test_all_field_types_for_existing_settings_page(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'general',
					'fields' => array(
						array(
							'name'  => 'text_field',
							'type'  => 'text',
							'label' => 'Text',
						),
						array(
							'name'  => 'textarea_field',
							'type'  => 'textarea',
							'label' => 'Textarea',
						),
						array(
							'name'    => 'select_field',
							'type'    => 'select',
							'label'   => 'Select',
							'options' => array( 'a' => 'A' ),
						),
						array(
							'name'  => 'checkbox_field',
							'type'  => 'checkbox',
							'label' => 'Checkbox',
						),
						array(
							'name'    => 'radio_field',
							'type'    => 'radio',
							'label'   => 'Radio',
							'options' => array( 'b' => 'B' ),
						),
						array(
							'name'  => 'number_field',
							'type'  => 'number',
							'label' => 'Number',
						),
						array(
							'name'  => 'email_field',
							'type'  => 'email',
							'label' => 'Email',
						),
						array(
							'name'  => 'url_field',
							'type'  => 'url',
							'label' => 'URL',
						),
						array(
							'name'  => 'date_field',
							'type'  => 'date',
							'label' => 'Date',
						),
						array(
							'name'  => 'password_field',
							'type'  => 'password',
							'label' => 'Password',
						),
						array(
							'name'  => 'color_field',
							'type'  => 'color',
							'label' => 'Color',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );
		$this->assertGreaterThanOrEqual( 11, count( $fields['general'] ), 'Should have at least all 11 field types' );

		// Verify specific field names exist
		$field_names = array_map(
			function ( $field ) {
				return $field->get_name();
			},
			$fields['general']
		);
		$this->assertContains( 'text_field', $field_names );
		$this->assertContains( 'textarea_field', $field_names );
		$this->assertContains( 'select_field', $field_names );
		$this->assertContains( 'checkbox_field', $field_names );
		$this->assertContains( 'radio_field', $field_names );
		$this->assertContains( 'number_field', $field_names );
		$this->assertContains( 'email_field', $field_names );
		$this->assertContains( 'url_field', $field_names );
		$this->assertContains( 'date_field', $field_names );
		$this->assertContains( 'password_field', $field_names );
		$this->assertContains( 'color_field', $field_names );
	}

	/**
	 * Test adding fields to plugin settings page (non-WordPress core)
	 */
	public function test_add_fields_to_plugin_settings_page(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'woocommerce',  // Example plugin settings page
					'fields' => array(
						array(
							'name'  => 'custom_woo_field',
							'type'  => 'text',
							'label' => 'Custom WooCommerce Field',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Should not create new settings page
		$pages = $registrar->get_settings_pages();
		$this->assertArrayNotHasKey( 'woocommerce', $pages, 'Should not create new page for plugin settings' );

		// Should have fields registered
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'woocommerce', $fields, 'Should have fields for plugin settings page' );
	}

	/**
	 * Test JSON validation for existing settings page with single checkbox
	 */
	public function test_json_validation_existing_settings_single_checkbox(): void {
		$json = json_encode(
			array(
				'settings_pages' => array(
					array(
						'id'     => 'general',
						'fields' => array(
							array(
								'name'        => 'maintenance_mode',
								'type'        => 'checkbox',
								'label'       => 'Enable Maintenance Mode',
								'description' => 'Show maintenance message to non-admin users',
							),
						),
					),
				),
			)
		);

		$manager = Manager::init();

		// Should not throw exception
		$manager->register_from_json( $json );

		$registrar = $manager->get_registrar();
		$fields    = $registrar->get_fields();
		$this->assertArrayHasKey( 'general', $fields );
	}

	/**
	 * Test that existing post type detection doesn't interfere with custom post types
	 */
	public function test_existing_detection_doesnt_break_custom_cpts(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'product',
					'args'   => array(
						'label' => 'Products',
					),
					'fields' => array(
						array(
							'name'  => 'price',
							'type'  => 'number',
							'label' => 'Price',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Should create new CPT because args are provided
		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayHasKey( 'product', $cpts, 'Product should be created as new CPT' );

		// Should also have fields
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'product', $fields );
	}

	/**
	 * Test that existing settings page detection doesn't interfere with custom settings
	 */
	public function test_existing_detection_doesnt_break_custom_settings(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'         => 'my-settings',
					'page_title' => 'My Settings',
					'menu_title' => 'My Settings',
					'fields'     => array(
						array(
							'name'  => 'setting1',
							'type'  => 'text',
							'label' => 'Setting 1',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Should create new settings page because page_title is provided
		$pages = $registrar->get_settings_pages();
		$this->assertArrayHasKey( 'my-settings', $pages, 'My Settings should be created as new page' );

		// Should also have fields
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'my-settings', $fields );
	}
}
