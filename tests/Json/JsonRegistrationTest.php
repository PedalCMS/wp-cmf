<?php
/**
 * Tests for JSON registration in Manager
 *
 * @package Pedalcms\WpCmf\Tests
 */

namespace Pedalcms\WpCmf\Tests\Json;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Core\Manager;

/**
 * JSON Registration Tests
 */
class JsonRegistrationTest extends TestCase {

	/**
	 * Temporary JSON file for testing
	 *
	 * @var string|null
	 */
	private ?string $temp_file = null;

	/**
	 * Set up before each test
	 */
	protected function setUp(): void {
		parent::setUp();

		// Reset singleton
		$reflection = new \ReflectionClass( Manager::class );
		$instance   = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );
	}

	/**
	 * Clean up after each test
	 */
	protected function tearDown(): void {
		if ( $this->temp_file && file_exists( $this->temp_file ) ) {
			unlink( $this->temp_file );
		}
		parent::tearDown();
	}

	/**
	 * Test register from JSON string
	 */
	public function test_register_from_json_string(): void {
		$json = '{
			"settings_pages": [{
				"id": "test_settings",
				"page_title": "Test Settings"
			}]
		}';

		$manager = Manager::init();
		$result  = $manager->register_from_json( $json );

		$this->assertSame( $manager, $result, 'Should return manager instance for chaining' );
	}

	/**
	 * Test register from JSON file
	 */
	public function test_register_from_json_file(): void {
		$config = array(
			'cpts' => array(
				array(
					'id'   => 'product',
					'args' => array( 'label' => 'Products' ),
				),
			),
		);

		$this->temp_file = sys_get_temp_dir() . '/wp-cmf-test-' . uniqid() . '.json';
		file_put_contents( $this->temp_file, json_encode( $config ) );

		$manager = Manager::init();
		$result  = $manager->register_from_json( $this->temp_file );

		$this->assertSame( $manager, $result );
	}

	/**
	 * Test invalid JSON throws exception
	 */
	public function test_invalid_json_throws_exception(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid JSON' );

		Manager::init()->register_from_json( '{invalid json}' );
	}

	/**
	 * Test validation failure throws exception
	 */
	public function test_validation_failure_throws_exception(): void {
		$json = '{
			"cpts": [{
				"args": {"label": "Test"}
			}]
		}';

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'missing required field' );

		Manager::init()->register_from_json( $json );
	}

	/**
	 * Test skip validation
	 */
	public function test_skip_validation(): void {
		$json = '{
			"cpts": [{
				"id": "test",
				"args": {"label": "Test"}
			}]
		}';

		$manager = Manager::init();

		// Should not throw exception when validation is disabled
		$result = $manager->register_from_json( $json, false );

		$this->assertSame( $manager, $result );
	}

	/**
	 * Test treats non-existent file as JSON string
	 */
	public function test_nonexistent_file_as_json_string(): void {
		// When file doesn't exist, it treats input as JSON string
		$json = '{
			"settings_pages": [{
				"id": "test",
				"page_title": "Test"
			}]
		}';

		$manager = Manager::init();
		$result  = $manager->register_from_json( $json );

		// Should not throw exception - treats as JSON string
		$this->assertSame( $manager, $result );
	}

	/**
	 * Test complete configuration from JSON
	 */
	public function test_complete_json_configuration(): void {
		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'book',
					'args'   => array(
						'label'    => 'Books',
						'public'   => true,
						'supports' => array( 'title', 'editor' ),
					),
					'fields' => array(
						array(
							'name'  => 'isbn',
							'type'  => 'text',
							'label' => 'ISBN',
						),
						array(
							'name'  => 'price',
							'type'  => 'number',
							'label' => 'Price',
							'min'   => 0,
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'book_settings',
					'page_title' => 'Book Settings',
					'fields'     => array(
						array(
							'name'  => 'default_author',
							'type'  => 'text',
							'label' => 'Default Author',
						),
					),
				),
			),
		);

		$this->temp_file = sys_get_temp_dir() . '/wp-cmf-test-complete-' . uniqid() . '.json';
		file_put_contents( $this->temp_file, json_encode( $config ) );

		$manager   = Manager::init();
		$result    = $manager->register_from_json( $this->temp_file );
		$registrar = $manager->get_registrar();

		$this->assertSame( $manager, $result );

		// Check that CPT was registered
		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'custom_post_types' );
		$property->setAccessible( true );
		$cpts = $property->getValue( $registrar );

		$this->assertArrayHasKey( 'book', $cpts );

		// Check that settings page was registered
		$property = $reflection->getProperty( 'settings_pages' );
		$property->setAccessible( true );
		$pages = $property->getValue( $registrar );

		$this->assertArrayHasKey( 'book_settings', $pages );
	}

	/**
	 * Test JSON with fields creates field instances
	 */
	public function test_json_fields_become_field_instances(): void {
		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'test_page',
					'fields' => array(
						array(
							'name'  => 'field1',
							'type'  => 'text',
							'label' => 'Field 1',
						),
						array(
							'name'  => 'field2',
							'type'  => 'email',
							'label' => 'Field 2',
						),
					),
				),
			),
		);

		$json = json_encode( $config );

		$manager = Manager::init();
		$manager->register_from_json( $json );
		$registrar = $manager->get_registrar();

		$reflection = new \ReflectionClass( $registrar );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$fields = $property->getValue( $registrar );

		$this->assertArrayHasKey( 'test_page', $fields );
		$this->assertCount( 2, $fields['test_page'] );

		foreach ( $fields['test_page'] as $field ) {
			$this->assertInstanceOf( \Pedalcms\WpCmf\Field\FieldInterface::class, $field );
		}
	}

	/**
	 * Test all field types from JSON
	 */
	public function test_all_field_types_from_json(): void {
		$types = array( 'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'url', 'date', 'password', 'color' );

		$fields = array();
		foreach ( $types as $type ) {
			$field = array(
				'name'  => $type . '_field',
				'type'  => $type,
				'label' => ucfirst( $type ) . ' Field',
			);

			// Add options for fields that require them
			if ( in_array( $type, array( 'select', 'checkbox', 'radio' ), true ) ) {
				$field['options'] = array(
					'opt1' => 'Option 1',
					'opt2' => 'Option 2',
				);
			}

			$fields[] = $field;
		}

		$config = array(
			'settings_pages' => array(
				array(
					'id'     => 'all_types',
					'fields' => $fields,
				),
			),
		);

		$json = json_encode( $config );

		$manager = Manager::init();
		$result  = $manager->register_from_json( $json );

		$this->assertSame( $manager, $result );
	}
}
