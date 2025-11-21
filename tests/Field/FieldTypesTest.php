<?php
/**
 * Tests for concrete field implementations
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Field;

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
use Pedalcms\WpCmf\Field\Fields\WysiwygField;
use WP_UnitTestCase;

/**
 * FieldTypesTest class
 */
class FieldTypesTest extends WP_UnitTestCase {

	/**
	 * Test TextField renders correctly
	 */
	public function test_text_field_render() {
		$field = new TextField( 'username', 'text', array( 'label' => 'Username' ) );
		$html  = $field->render( 'testuser' );

		$this->assertStringContainsString( 'type="text"', $html );
		$this->assertStringContainsString( 'name="username"', $html );
		$this->assertStringContainsString( 'value="testuser"', $html );
		$this->assertStringContainsString( '<label', $html );
	}

	/**
	 * Test TextareaField renders correctly
	 */
	public function test_textarea_field_render() {
		$field = new TextareaField( 'bio', 'textarea', array( 'label' => 'Bio' ) );
		$html  = $field->render( 'Test bio' );

		$this->assertStringContainsString( '<textarea', $html );
		$this->assertStringContainsString( 'name="bio"', $html );
		$this->assertStringContainsString( 'Test bio', $html );
		$this->assertStringContainsString( '</textarea>', $html );
	}

	/**
	 * Test SelectField renders options
	 */
	public function test_select_field_render() {
		$field = new SelectField(
			'country',
			'select',
			array(
				'label'   => 'Country',
				'options' => array(
					'us' => 'United States',
					'uk' => 'United Kingdom',
					'ca' => 'Canada',
				),
			)
		);

		$html = $field->render( 'uk' );

		$this->assertStringContainsString( '<select', $html );
		$this->assertStringContainsString( 'name="country"', $html );
		$this->assertStringContainsString( 'United States', $html );
		$this->assertStringContainsString( 'United Kingdom', $html );
		$this->assertStringContainsString( 'selected', $html );
	}

	/**
	 * Test SelectField validates options
	 */
	public function test_select_field_validation() {
		$field = new SelectField(
			'country',
			'select',
			array(
				'options' => array(
					'us' => 'United States',
					'uk' => 'United Kingdom',
				),
			)
		);

		// Valid option
		$result = $field->validate( 'us' );
		$this->assertTrue( $result['valid'] );

		// Invalid option
		$result = $field->validate( 'invalid' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test CheckboxField single checkbox
	 */
	public function test_checkbox_field_single() {
		$field = new CheckboxField( 'agree', 'checkbox', array( 'label' => 'I agree' ) );
		$html  = $field->render( '1' );

		$this->assertStringContainsString( 'type="checkbox"', $html );
		$this->assertStringContainsString( 'name="agree"', $html );
		$this->assertStringContainsString( 'checked', $html );
	}

	/**
	 * Test CheckboxField multiple checkboxes
	 */
	public function test_checkbox_field_multiple() {
		$field = new CheckboxField(
			'interests',
			'checkbox',
			array(
				'label'   => 'Interests',
				'options' => array(
					'music'  => 'Music',
					'sports' => 'Sports',
					'travel' => 'Travel',
				),
			)
		);

		$html = $field->render( array( 'music', 'travel' ) );

		$this->assertStringContainsString( 'name="interests[]"', $html );
		$this->assertStringContainsString( 'Music', $html );
		$this->assertStringContainsString( 'Sports', $html );
		$this->assertGreaterThan( 1, substr_count( $html, 'checked' ) );
	}

	/**
	 * Test RadioField renders correctly
	 */
	public function test_radio_field_render() {
		$field = new RadioField(
			'size',
			'radio',
			array(
				'label'   => 'Size',
				'options' => array(
					'small'  => 'Small',
					'medium' => 'Medium',
					'large'  => 'Large',
				),
			)
		);

		$html = $field->render( 'medium' );

		$this->assertStringContainsString( 'type="radio"', $html );
		$this->assertStringContainsString( 'name="size"', $html );
		$this->assertStringContainsString( 'Small', $html );
		$this->assertStringContainsString( 'checked', $html );
	}

	/**
	 * Test NumberField renders with attributes
	 */
	public function test_number_field_render() {
		$field = new NumberField(
			'quantity',
			'number',
			array(
				'label' => 'Quantity',
				'min'   => 1,
				'max'   => 100,
				'step'  => 1,
			)
		);

		$html = $field->render( 5 );

		$this->assertStringContainsString( 'type="number"', $html );
		$this->assertStringContainsString( 'min="1"', $html );
		$this->assertStringContainsString( 'max="100"', $html );
		$this->assertStringContainsString( 'value="5"', $html );
	}

	/**
	 * Test NumberField validation
	 */
	public function test_number_field_validation() {
		$field = new NumberField(
			'age',
			'number',
			array(
				'min' => 18,
				'max' => 100,
			)
		);

		// Valid
		$result = $field->validate( 25 );
		$this->assertTrue( $result['valid'] );

		// Too low
		$result = $field->validate( 10 );
		$this->assertFalse( $result['valid'] );

		// Too high
		$result = $field->validate( 150 );
		$this->assertFalse( $result['valid'] );

		// Not a number
		$result = $field->validate( 'abc' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test NumberField sanitization
	 */
	public function test_number_field_sanitize() {
		$field = new NumberField( 'qty', 'number' );

		$this->assertSame( 42, $field->sanitize( '42' ) );
		$this->assertSame( 3.14, $field->sanitize( '3.14' ) );
		$this->assertSame( '', $field->sanitize( '' ) );
	}

	/**
	 * Test EmailField renders correctly
	 */
	public function test_email_field_render() {
		$field = new EmailField( 'email', 'email', array( 'label' => 'Email' ) );
		$html  = $field->render( 'test@example.com' );

		$this->assertStringContainsString( 'type="email"', $html );
		$this->assertStringContainsString( 'value="test@example.com"', $html );
	}

	/**
	 * Test EmailField validation
	 */
	public function test_email_field_validation() {
		$field = new EmailField( 'email', 'email' );

		// Valid email
		$result = $field->validate( 'test@example.com' );
		$this->assertTrue( $result['valid'] );

		// Invalid email
		$result = $field->validate( 'not-an-email' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test URLField renders correctly
	 */
	public function test_url_field_render() {
		$field = new URLField( 'website', 'url', array( 'label' => 'Website' ) );
		$html  = $field->render( 'https://example.com' );

		$this->assertStringContainsString( 'type="url"', $html );
		$this->assertStringContainsString( 'value="https://example.com"', $html );
	}

	/**
	 * Test URLField validation
	 */
	public function test_url_field_validation() {
		$field = new URLField( 'website', 'url' );

		// Valid URL
		$result = $field->validate( 'https://example.com' );
		$this->assertTrue( $result['valid'] );

		// Invalid URL
		$result = $field->validate( 'not a url' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test DateField renders correctly
	 */
	public function test_date_field_render() {
		$field = new DateField(
			'birthdate',
			'date',
			array(
				'label' => 'Birth Date',
				'min'   => '1900-01-01',
				'max'   => '2025-12-31',
			)
		);

		$html = $field->render( '1990-05-15' );

		$this->assertStringContainsString( 'type="date"', $html );
		$this->assertStringContainsString( 'min="1900-01-01"', $html );
		$this->assertStringContainsString( 'value="1990-05-15"', $html );
	}

	/**
	 * Test DateField validation
	 */
	public function test_date_field_validation() {
		$field = new DateField( 'date', 'date' );

		// Valid date
		$result = $field->validate( '2023-06-15' );
		$this->assertTrue( $result['valid'] );

		// Invalid format
		$result = $field->validate( '15/06/2023' );
		$this->assertFalse( $result['valid'] );

		// Invalid date
		$result = $field->validate( '2023-13-45' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test PasswordField renders without value
	 */
	public function test_password_field_render() {
		$field = new PasswordField( 'password', 'password', array( 'label' => 'Password' ) );
		$html  = $field->render( 'secret123' );

		$this->assertStringContainsString( 'type="password"', $html );
		$this->assertStringContainsString( 'value=""', $html );
		$this->assertStringNotContainsString( 'secret123', $html );
	}

	/**
	 * Test PasswordField sanitization preserves special chars
	 */
	public function test_password_field_sanitize() {
		$field = new PasswordField( 'pwd', 'password' );

		// Should keep special characters
		$password = 'P@ssw0rd!#$';
		$this->assertSame( $password, $field->sanitize( $password ) );
	}

	/**
	 * Test field with required attribute
	 */
	public function test_field_required_attribute() {
		$field = new TextField( 'name', 'text', array( 'required' => true ) );
		$html  = $field->render();

		$this->assertStringContainsString( 'required', $html );
	}

	/**
	 * Test field with placeholder
	 */
	public function test_field_placeholder() {
		$field = new TextField( 'name', 'text', array( 'placeholder' => 'Enter your name' ) );
		$html  = $field->render();

		$this->assertStringContainsString( 'placeholder="Enter your name"', $html );
	}

	/**
	 * Test field with description
	 */
	public function test_field_description() {
		$field = new TextField( 'name', 'text', array( 'description' => 'This is a helpful hint' ) );
		$html  = $field->render();

		$this->assertStringContainsString( 'This is a helpful hint', $html );
		$this->assertStringContainsString( 'class="description', $html );
	}

	/**
	 * Test SelectField multiple selection
	 */
	public function test_select_field_multiple() {
		$field = new SelectField(
			'colors',
			'select',
			array(
				'multiple' => true,
				'options'  => array(
					'red'   => 'Red',
					'blue'  => 'Blue',
					'green' => 'Green',
				),
			)
		);

		$html = $field->render( array( 'red', 'blue' ) );

		$this->assertStringContainsString( 'multiple', $html );
		$this->assertStringContainsString( 'name="colors[]"', $html );
		$this->assertSame( 2, substr_count( $html, 'selected' ) );
	}

	/**
	 * Test CheckboxField sanitization
	 */
	public function test_checkbox_field_sanitize() {
		// Single checkbox
		$field = new CheckboxField( 'agree', 'checkbox' );
		$this->assertSame( '1', $field->sanitize( '1' ) );
		$this->assertSame( '0', $field->sanitize( '' ) );

		// Multiple checkboxes
		$field = new CheckboxField(
			'items',
			'checkbox',
			array(
				'options' => array(
					'a' => 'Option A',
					'b' => 'Option B',
				),
			)
		);

		$sanitized = $field->sanitize( array( 'a', 'invalid', 'b' ) );
		$this->assertCount( 2, $sanitized );
		$this->assertContains( 'a', $sanitized );
		$this->assertNotContains( 'invalid', $sanitized );
	}

	/**
	 * Test ColorField renders correctly
	 */
	public function test_color_field_render() {
		$field = new \Pedalcms\WpCmf\Field\Fields\ColorField(
			'theme_color',
			'color',
			array( 'label' => 'Theme Color' )
		);

		$html = $field->render( '#FF5733' );

		$this->assertStringContainsString( 'name="theme_color"', $html );
		$this->assertStringContainsString( 'value="#FF5733"', $html );
	}

	/**
	 * Test ColorField validation
	 */
	public function test_color_field_validation() {
		$field = new \Pedalcms\WpCmf\Field\Fields\ColorField( 'color', 'color' );

		// Valid 6-digit hex
		$result = $field->validate( '#FF5733' );
		$this->assertTrue( $result['valid'] );

		// Valid 3-digit hex
		$result = $field->validate( '#F53' );
		$this->assertTrue( $result['valid'] );

		// Invalid format
		$result = $field->validate( 'red' );
		$this->assertFalse( $result['valid'] );

		// Invalid hex
		$result = $field->validate( '#GGGGGG' );
		$this->assertFalse( $result['valid'] );
	}

	/**
	 * Test ColorField sanitization
	 */
	public function test_color_field_sanitize() {
		$field = new \Pedalcms\WpCmf\Field\Fields\ColorField( 'color', 'color' );

		// Valid color
		$this->assertSame( '#FF5733', $field->sanitize( '#FF5733' ) );

		// Color without hash
		$this->assertSame( '#FF5733', $field->sanitize( 'FF5733' ) );

		// Invalid color should return default
		$this->assertSame( '#000000', $field->sanitize( 'invalid' ) );
	}

	/**
	 * Test ColorField enqueue_assets method
	 */
	public function test_color_field_enqueue_assets() {
		$field = new \Pedalcms\WpCmf\Field\Fields\ColorField( 'color', 'color' );

		// Method should exist
		$this->assertTrue( method_exists( $field, 'enqueue_assets' ) );

		// Should be callable without errors
		$field->enqueue_assets();

		// No exception means success
		$this->assertTrue( true );
	}

	/**
	 * Test WysiwygField renders correctly
	 */
	public function test_wysiwyg_field_render() {
		$field = new WysiwygField( 'content', 'wysiwyg', [ 'label' => 'Content' ] );
		$html  = $field->render( '<p>Test content</p>' );

		// Should contain editor or textarea
		$this->assertIsString( $html );
		$this->assertStringContainsString( 'name="content"', $html );
	}

	/**
	 * Test WysiwygField sanitizes HTML
	 */
	public function test_wysiwyg_field_sanitize() {
		$field = new WysiwygField( 'content', 'wysiwyg' );

		// In non-WordPress context, strip_tags is used (no HTML allowed)
		// In WordPress context, wp_kses_post is used (safe HTML allowed)
		$safe_html = '<p>This is <strong>bold</strong> text.</p>';
		$sanitized = $field->sanitize( $safe_html );

		// Check that sanitization happened (string is returned)
		$this->assertIsString( $sanitized );
		$this->assertNotEmpty( $sanitized );

		// Should strip script tags (but content remains in non-WP context)
		$dangerous_html = '<p>Safe text</p><script>danger</script>';
		$sanitized      = $field->sanitize( $dangerous_html );
		$this->assertStringNotContainsString( '<script>', $sanitized );
		$this->assertStringContainsString( 'Safe text', $sanitized );
	}

	/**
	 * Test WysiwygField validates required
	 */
	public function test_wysiwyg_field_validation() {
		$field = new WysiwygField(
			'content',
			'wysiwyg',
			[
				'required' => true,
				'min'      => 20,  // Set reasonable min
			]
		);

		// Empty should fail required
		$result = $field->validate( '' );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );

		// Too short should fail min (HTML tags count toward length)
		$result = $field->validate( '<p>Short</p>' );
		$this->assertFalse( $result['valid'] );

		// Valid content should pass (HTML tags count toward length)
		$result = $field->validate( '<p>This is long enough content with sufficient characters to pass validation.</p>' );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}
}
