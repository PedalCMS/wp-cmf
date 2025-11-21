<?php
/**
 * Tests for Container Fields (Tabs Field)
 *
 * Tests that container fields properly register nested fields,
 * and that nested fields save and load their values correctly.
 *
 * @package Pedalcms\WpCmf\Tests
 */

namespace Pedalcms\WpCmf\Tests\Field;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Core\Registrar;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;
use Pedalcms\WpCmf\Field\Fields\TabsField;

/**
 * Container Fields test case
 */
class ContainerFieldsTest extends TestCase {

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
	 * Test that tabs field implements ContainerFieldInterface
	 */
	public function test_tabs_field_implements_container_interface(): void {
		$tabs_field = FieldFactory::create(
			[
				'name' => 'test_tabs',
				'type' => 'tabs',
				'tabs' => [],
			]
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $tabs_field );
		$this->assertTrue( $tabs_field->is_container() );
	}

	/**
	 * Test that tabs field extracts nested fields correctly
	 */
	public function test_tabs_field_extracts_nested_fields(): void {
		$tabs_field = FieldFactory::create(
			[
				'name'        => 'product_tabs',
				'type'        => 'tabs',
				'orientation' => 'horizontal',
				'tabs'        => [
					[
						'id'     => 'basic',
						'label'  => 'Basic',
						'fields' => [
							[
								'name'  => 'product_sku',
								'type'  => 'text',
								'label' => 'SKU',
							],
							[
								'name'  => 'product_price',
								'type'  => 'number',
								'label' => 'Price',
							],
						],
					],
					[
						'id'     => 'details',
						'label'  => 'Details',
						'fields' => [
							[
								'name'  => 'product_brand',
								'type'  => 'text',
								'label' => 'Brand',
							],
						],
					],
				],
			]
		);

		$nested_fields = $tabs_field->get_nested_fields();

		$this->assertIsArray( $nested_fields );
		$this->assertCount( 3, $nested_fields );
		$this->assertEquals( 'product_sku', $nested_fields[0]['name'] );
		$this->assertEquals( 'product_price', $nested_fields[1]['name'] );
		$this->assertEquals( 'product_brand', $nested_fields[2]['name'] );
	}

	/**
	 * Test that nested fields are registered when tabs field is added
	 */
	public function test_nested_fields_are_registered(): void {
		$registrar = new Registrar();

		$registrar->add_fields(
			'product',
			[
				[
					'name' => 'product_tabs',
					'type' => 'tabs',
					'tabs' => [
						[
							'id'     => 'tab1',
							'fields' => [
								[
									'name'  => 'field1',
									'type'  => 'text',
									'label' => 'Field 1',
								],
								[
									'name'  => 'field2',
									'type'  => 'text',
									'label' => 'Field 2',
								],
							],
						],
					],
				],
			]
		);

		// Use reflection to access protected fields property
		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		// Should have 3 fields: tabs field + 2 nested fields
		$this->assertArrayHasKey( 'product', $fields );
		$this->assertCount( 3, $fields['product'] );
		$this->assertArrayHasKey( 'product_tabs', $fields['product'] );
		$this->assertArrayHasKey( 'field1', $fields['product'] );
		$this->assertArrayHasKey( 'field2', $fields['product'] );
	}

	/**
	 * Test container field doesn't store its own value
	 */
	public function test_container_field_sanitize_returns_empty(): void {
		$tabs_field = FieldFactory::create(
			[
				'name' => 'test_tabs',
				'type' => 'tabs',
				'tabs' => [],
			]
		);

		$result = $tabs_field->sanitize( [ 'some' => 'value' ] );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	/**
	 * Test container field validation always passes
	 */
	public function test_container_field_validate_always_passes(): void {
		$tabs_field = FieldFactory::create(
			[
				'name' => 'test_tabs',
				'type' => 'tabs',
				'tabs' => [],
			]
		);

		$result = $tabs_field->validate( [ 'some' => 'value' ] );

		$this->assertIsArray( $result );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	/**
	 * Test nested fields with Manager integration
	 */
	public function test_nested_fields_with_manager(): void {
		$manager = Manager::init();

		$manager->register_from_array(
			[
				'cpts' => [
					[
						'id'     => 'product',
						'fields' => [
							[
								'name' => 'product_details',
								'type' => 'tabs',
								'tabs' => [
									[
										'id'     => 'basic',
										'fields' => [
											[
												'name'  => 'sku',
												'type'  => 'text',
												'label' => 'SKU',
											],
											[
												'name'  => 'price',
												'type'  => 'number',
												'label' => 'Price',
											],
										],
									],
								],
							],
						],
					],
				],
			]
		);

		$registrar = $manager->get_registrar();

		// Use reflection to check registered fields
		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		// Should have tabs field + 2 nested fields
		$this->assertArrayHasKey( 'product', $fields );
		$this->assertCount( 3, $fields['product'] );
		$this->assertArrayHasKey( 'product_details', $fields['product'] );
		$this->assertArrayHasKey( 'sku', $fields['product'] );
		$this->assertArrayHasKey( 'price', $fields['product'] );
	}

	/**
	 * Test multiple tabs with many nested fields
	 */
	public function test_multiple_tabs_with_many_fields(): void {
		$registrar = new Registrar();

		$registrar->add_fields(
			'event',
			[
				[
					'name' => 'event_tabs',
					'type' => 'tabs',
					'tabs' => [
						[
							'id'     => 'datetime',
							'fields' => [
								[
									'name'  => 'event_date',
									'type'  => 'date',
									'label' => 'Date',
								],
								[
									'name'  => 'event_time',
									'type'  => 'text',
									'label' => 'Time',
								],
							],
						],
						[
							'id'     => 'location',
							'fields' => [
								[
									'name'  => 'event_venue',
									'type'  => 'text',
									'label' => 'Venue',
								],
								[
									'name'  => 'event_address',
									'type'  => 'textarea',
									'label' => 'Address',
								],
							],
						],
						[
							'id'     => 'tickets',
							'fields' => [
								[
									'name'  => 'ticket_price',
									'type'  => 'number',
									'label' => 'Price',
								],
							],
						],
					],
				],
			]
		);

		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		// Should have tabs field + 5 nested fields (2 + 2 + 1)
		$this->assertCount( 6, $fields['event'] );
		$this->assertArrayHasKey( 'event_tabs', $fields['event'] );
		$this->assertArrayHasKey( 'event_date', $fields['event'] );
		$this->assertArrayHasKey( 'event_time', $fields['event'] );
		$this->assertArrayHasKey( 'event_venue', $fields['event'] );
		$this->assertArrayHasKey( 'event_address', $fields['event'] );
		$this->assertArrayHasKey( 'ticket_price', $fields['event'] );
	}

	/**
	 * Test nested fields in settings pages
	 */
	public function test_nested_fields_in_settings_pages(): void {
		$manager = Manager::init();

		$manager->register_from_array(
			[
				'settings_pages' => [
					[
						'id'     => 'store-settings',
						'title'  => 'Store Settings',
						'fields' => [
							[
								'name' => 'store_tabs',
								'type' => 'tabs',
								'tabs' => [
									[
										'id'     => 'general',
										'fields' => [
											[
												'name'  => 'store_name',
												'type'  => 'text',
												'label' => 'Store Name',
											],
											[
												'name'  => 'store_email',
												'type'  => 'email',
												'label' => 'Email',
											],
										],
									],
									[
										'id'     => 'checkout',
										'fields' => [
											[
												'name'  => 'store_currency',
												'type'  => 'text',
												'label' => 'Currency',
											],
										],
									],
								],
							],
						],
					],
				],
			]
		);

		$registrar  = $manager->get_registrar();
		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		// Should have tabs field + 3 nested fields
		$this->assertArrayHasKey( 'store-settings', $fields );
		$this->assertCount( 4, $fields['store-settings'] );
		$this->assertArrayHasKey( 'store_tabs', $fields['store-settings'] );
		$this->assertArrayHasKey( 'store_name', $fields['store-settings'] );
		$this->assertArrayHasKey( 'store_email', $fields['store-settings'] );
		$this->assertArrayHasKey( 'store_currency', $fields['store-settings'] );
	}

	/**
	 * Test vertical tabs orientation
	 */
	public function test_vertical_tabs_orientation(): void {
		$tabs_field = FieldFactory::create(
			[
				'name'        => 'my_tabs',
				'type'        => 'tabs',
				'orientation' => 'vertical',
				'tabs'        => [
					[
						'id'     => 'tab1',
						'label'  => 'Tab 1',
						'fields' => [
							[
								'name'  => 'field1',
								'type'  => 'text',
								'label' => 'Field 1',
							],
						],
					],
				],
			]
		);

		$html = $tabs_field->render();

		$this->assertStringContainsString( 'wp-cmf-tabs-vertical', $html );
		$this->assertStringNotContainsString( 'wp-cmf-tabs-horizontal', $html );
	}

	/**
	 * Test horizontal tabs orientation (default)
	 */
	public function test_horizontal_tabs_orientation(): void {
		$tabs_field = FieldFactory::create(
			[
				'name'        => 'my_tabs',
				'type'        => 'tabs',
				'orientation' => 'horizontal',
				'tabs'        => [
					[
						'id'     => 'tab1',
						'label'  => 'Tab 1',
						'fields' => [
							[
								'name'  => 'field1',
								'type'  => 'text',
								'label' => 'Field 1',
							],
						],
					],
				],
			]
		);

		$html = $tabs_field->render();

		$this->assertStringContainsString( 'wp-cmf-tabs-horizontal', $html );
		$this->assertStringNotContainsString( 'wp-cmf-tabs-vertical', $html );
	}

	/**
	 * Test tabs with icons and descriptions
	 */
	public function test_tabs_with_icons_and_descriptions(): void {
		$tabs_field = FieldFactory::create(
			[
				'name' => 'my_tabs',
				'type' => 'tabs',
				'tabs' => [
					[
						'id'          => 'tab1',
						'label'       => 'Tab 1',
						'icon'        => 'dashicons-admin-generic',
						'description' => 'This is tab 1',
						'fields'      => [],
					],
				],
			]
		);

		$html = $tabs_field->render();

		$this->assertStringContainsString( 'dashicons-admin-generic', $html );
		$this->assertStringContainsString( 'This is tab 1', $html );
	}

	/**
	 * Test empty tabs configuration
	 */
	public function test_empty_tabs_configuration(): void {
		$tabs_field = FieldFactory::create(
			[
				'name' => 'my_tabs',
				'type' => 'tabs',
				'tabs' => [],
			]
		);

		$html = $tabs_field->render();

		$this->assertEmpty( $html );
	}

	/**
	 * Test nested container fields (tabs within tabs) - recursive registration
	 */
	public function test_nested_container_fields(): void {
		$registrar = new Registrar();

		$registrar->add_fields(
			'product',
			[
				[
					'name' => 'outer_tabs',
					'type' => 'tabs',
					'tabs' => [
						[
							'id'     => 'tab1',
							'fields' => [
								[
									'name' => 'inner_tabs',
									'type' => 'tabs',
									'tabs' => [
										[
											'id'     => 'inner1',
											'fields' => [
												[
													'name' => 'deep_field',
													'type' => 'text',
													'label' => 'Deep Field',
												],
											],
										],
									],
								],
							],
						],
					],
				],
			]
		);

		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		// Should have outer_tabs + inner_tabs + deep_field
		$this->assertCount( 3, $fields['product'] );
		$this->assertArrayHasKey( 'outer_tabs', $fields['product'] );
		$this->assertArrayHasKey( 'inner_tabs', $fields['product'] );
		$this->assertArrayHasKey( 'deep_field', $fields['product'] );
	}
}
