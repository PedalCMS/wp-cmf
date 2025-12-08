<?php
/**
 * Tests for AbstractField base class
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Field;

use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\FieldInterface;
use WP_UnitTestCase;

/**
 * Concrete test field for testing AbstractField
 */
class TestField extends AbstractField {
	public function render( $value = null ): string {
		return sprintf(
			'%s<input type="text" id="%s" name="%s" value="%s" />%s%s',
			$this->render_wrapper_start(),
			$this->esc_attr( $this->get_field_id() ),
			$this->esc_attr( $this->name ),
			$this->esc_attr( $value ?? '' ),
			$this->render_description(),
			$this->render_wrapper_end()
		);
	}
}

/**
 * AbstractField test case
 */
class AbstractFieldTest extends WP_UnitTestCase {

	/**
	 * Test field implements FieldInterface
	 */
	public function test_field_implements_interface() {
		$field = new TestField( 'test_field', 'text' );

		$this->assertInstanceOf( FieldInterface::class, $field );
		$this->assertInstanceOf( AbstractField::class, $field );
	}

	/**
	 * Test field construction
	 */
	public function test_field_construction() {
		$field = new TestField(
			'username',
			'text',
			array(
				'label'       => 'Username',
				'description' => 'Enter your username',
			)
		);

		$this->assertEquals( 'username', $field->get_name() );
		$this->assertEquals( 'text', $field->get_type() );
		$this->assertEquals( 'Username', $field->get_label() );
	}

	/**
	 * Test default label generation
	 */
	public function test_default_label_generation() {
		$field = new TestField( 'first_name', 'text' );

		$this->assertEquals( 'First Name', $field->get_label() );
	}

	/**
	 * Test get/set config
	 */
	public function test_get_set_config() {
		$field = new TestField( 'email', 'email' );

		$field->set_config( 'placeholder', 'user@example.com' );
		$this->assertEquals( 'user@example.com', $field->get_config( 'placeholder' ) );
	}

	/**
	 * Test get config with default
	 */
	public function test_get_config_with_default() {
		$field = new TestField( 'test', 'text' );

		$this->assertEquals( 'default_value', $field->get_config( 'nonexistent', 'default_value' ) );
		$this->assertNull( $field->get_config( 'nonexistent' ) );
	}

	/**
	 * Test get all config
	 */
	public function test_get_all_config() {
		$config = array(
			'label'       => 'Test Label',
			'description' => 'Test Description',
			'required'    => true,
		);

		$field      = new TestField( 'test', 'text', $config );
		$all_config = $field->get_all_config();

		$this->assertIsArray( $all_config );
		$this->assertEquals( 'Test Label', $all_config['label'] );
		$this->assertEquals( 'Test Description', $all_config['description'] );
		$this->assertTrue( $all_config['required'] );
	}

	/**
	 * Test default sanitize method
	 */
	public function test_default_sanitize() {
		$field = new TestField( 'test', 'text' );

		$sanitized = $field->sanitize( '  Test Value  <script>alert("xss")</script>' );

		// Ensure tags are removed
		$this->assertStringNotContainsString( '<script>', $sanitized );
		$this->assertStringNotContainsString( '</script>', $sanitized );

		// Ensure trimming works
		$this->assertStringStartsNotWith( ' ', $sanitized );
		$this->assertStringEndsNotWith( ' ', $sanitized );

		// Ensure "Test Value" is preserved
		$this->assertStringContainsString( 'Test Value', $sanitized );
	}

	/**
	 * Test validate required field
	 */
	public function test_validate_required_field() {
		$field = new TestField( 'required_field', 'text', array( 'required' => true ) );

		// Empty value should fail
		$result = $field->validate( '' );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );

		// Non-empty value should pass
		$result = $field->validate( 'value' );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	/**
	 * Test validate with min length
	 */
	public function test_validate_min_length() {
		$field = new TestField(
			'username',
			'text',
			array(
				'validation' => array(
					'min' => 5,
				),
			)
		);

		// Too short
		$result = $field->validate( 'user' );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );

		// Valid length
		$result = $field->validate( 'username' );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	/**
	 * Test validate with max length
	 */
	public function test_validate_max_length() {
		$field = new TestField(
			'short_text',
			'text',
			array(
				'validation' => array(
					'max' => 10,
				),
			)
		);

		// Too long
		$result = $field->validate( 'This is a very long text' );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );

		// Valid length
		$result = $field->validate( 'Short' );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	/**
	 * Test validate with pattern
	 */
	public function test_validate_pattern() {
		$field = new TestField(
			'username',
			'text',
			array(
				'validation' => array(
					'pattern' => '/^[a-zA-Z0-9_]+$/',
				),
			)
		);

		// Invalid pattern
		$result = $field->validate( 'user@name' );
		$this->assertFalse( $result['valid'] );

		// Valid pattern
		$result = $field->validate( 'username_123' );
		$this->assertTrue( $result['valid'] );
	}

	/**
	 * Test get schema
	 */
	public function test_get_schema() {
		$field = new TestField(
			'email',
			'email',
			array(
				'label'       => 'Email Address',
				'description' => 'Your email',
				'required'    => true,
				'validation'  => array(
					'email' => true,
				),
			)
		);

		$schema = $field->get_schema();

		$this->assertEquals( 'email', $schema['name'] );
		$this->assertEquals( 'email', $schema['type'] );
		$this->assertEquals( 'Email Address', $schema['label'] );
		$this->assertEquals( 'Your email', $schema['description'] );
		$this->assertTrue( $schema['required'] );
		$this->assertArrayHasKey( 'validation', $schema );
	}

	/**
	 * Test render method
	 */
	public function test_render() {
		$field = new TestField(
			'test_field',
			'text',
			array(
				'label'       => 'Test Field',
				'description' => 'This is a test field',
			)
		);

		$output = $field->render( 'test_value' );

		$this->assertStringContainsString( 'wp-cmf-field', $output );
		$this->assertStringContainsString( 'test_field', $output );
		$this->assertStringContainsString( 'test_value', $output );
		$this->assertStringContainsString( 'This is a test field', $output );
	}

	/**
	 * Test render with required field
	 */
	public function test_render_required_field() {
		$field = new TestField(
			'required_field',
			'text',
			array(
				'label'    => 'Required Field',
				'required' => true,
			)
		);

		$output = $field->render();

		$this->assertStringContainsString( 'wp-cmf-field-required', $output );
	}

	/**
	 * Test fluent interface
	 */
	public function test_fluent_interface() {
		$field = new TestField( 'test', 'text' );

		$result = $field->set_config( 'placeholder', 'Enter value' )
			->set_config( 'class', 'custom-class' );

		$this->assertSame( $field, $result );
		$this->assertEquals( 'Enter value', $field->get_config( 'placeholder' ) );
		$this->assertEquals( 'custom-class', $field->get_config( 'class' ) );
	}

	/**
	 * Test validation with multiple rules
	 */
	public function test_validate_multiple_rules() {
		$field = new TestField(
			'username',
			'text',
			array(
				'required'   => true,
				'validation' => array(
					'min'     => 5,
					'max'     => 20,
					'pattern' => '/^[a-zA-Z0-9_]+$/',
				),
			)
		);

		// Valid input
		$result = $field->validate( 'valid_user' );
		$this->assertTrue( $result['valid'] );

		// Invalid: too short
		$result = $field->validate( 'usr' );
		$this->assertFalse( $result['valid'] );

		// Invalid: too long
		$result = $field->validate( 'this_username_is_way_too_long' );
		$this->assertFalse( $result['valid'] );

		// Invalid: bad pattern
		$result = $field->validate( 'user@name' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test custom class in config
	 */
	public function test_custom_class() {
		$field = new TestField(
			'test',
			'text',
			array(
				'class' => 'my-custom-class',
			)
		);

		$output = $field->render();
		$this->assertStringContainsString( 'my-custom-class', $output );
	}

	/**
	 * Test field ID generation
	 */
	public function test_field_id_generation() {
		$field  = new TestField( 'my_test_field', 'text' );
		$output = $field->render();

		$this->assertStringContainsString( 'wp-cmf-field-my_test_field', $output );
	}

	/**
	 * Test enqueue_assets method exists and is callable
	 */
	public function test_enqueue_assets_method() {
		$field = new TestField( 'test', 'text' );

		// Method should exist
		$this->assertTrue( method_exists( $field, 'enqueue_assets' ) );

		// Should be callable without errors
		$field->enqueue_assets();

		// Default implementation should do nothing (no exception)
		$this->assertTrue( true );
	}

	/**
	 * Test get_option_name with prefix (default behavior)
	 */
	public function test_get_option_name_with_prefix() {
		$field = new TestField( 'api_key', 'text' );

		// Default: use_name_prefix is true
		$this->assertTrue( $field->uses_name_prefix() );
		$this->assertEquals( 'my_plugin_api_key', $field->get_option_name( 'my_plugin' ) );
		$this->assertEquals( 'general_api_key', $field->get_option_name( 'general' ) );
	}

	/**
	 * Test get_option_name without prefix
	 */
	public function test_get_option_name_without_prefix() {
		$field = new TestField(
			'api_key',
			'text',
			[ 'use_name_prefix' => false ]
		);

		// use_name_prefix is false
		$this->assertFalse( $field->uses_name_prefix() );
		$this->assertEquals( 'api_key', $field->get_option_name( 'my_plugin' ) );
		$this->assertEquals( 'api_key', $field->get_option_name( 'general' ) );
	}

	/**
	 * Test get_option_name with empty prefix
	 */
	public function test_get_option_name_empty_prefix() {
		$field = new TestField( 'api_key', 'text' );

		// Even with use_name_prefix = true, empty prefix returns just field name
		$this->assertEquals( 'api_key', $field->get_option_name( '' ) );
		$this->assertEquals( 'api_key', $field->get_option_name() );
	}
}
