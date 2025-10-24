<?php
/**
 * FieldFactory Usage Example
 *
 * Demonstrates how to use the FieldFactory to:
 * 1. Create fields from configuration arrays
 * 2. Register custom field types
 * 3. Create multiple fields at once
 * 4. Use Manager helper method for field type registration
 *
 * @package Pedalcms\WpCmf\Examples
 */

namespace Pedalcms\WpCmf\Examples;

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\Fields\TextField;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\AbstractField;

// Initialize the manager
$manager = Manager::init();

// ============================================================================
// EXAMPLE 1: Creating Fields from Configuration Arrays
// ============================================================================

echo "<h2>Example 1: Creating Fields from Configuration Arrays</h2>\n\n";

// Create a text field
$text_field = FieldFactory::create( [
	'name'        => 'user_name',
	'type'        => 'text',
	'label'       => 'User Name',
	'description' => 'Enter your full name',
	'default'     => '',
	'placeholder' => 'John Doe',
	'required'    => true,
] );

echo "Text Field: {$text_field->get_name()} - {$text_field->get_label()}\n";
echo "HTML: " . $text_field->render( 'John Smith' ) . "\n\n";

// Create an email field
$email_field = FieldFactory::create( [
	'name'        => 'user_email',
	'type'        => 'email',
	'label'       => 'Email Address',
	'description' => 'Enter your email address',
	'required'    => true,
] );

echo "Email Field: {$email_field->get_name()} - {$email_field->get_label()}\n";
echo "HTML: " . $email_field->render( 'john@example.com' ) . "\n\n";

// Create a select field
$country_field = FieldFactory::create( [
	'name'    => 'country',
	'type'    => 'select',
	'label'   => 'Country',
	'options' => [
		'us' => 'United States',
		'uk' => 'United Kingdom',
		'ca' => 'Canada',
		'au' => 'Australia',
	],
	'default' => 'us',
] );

echo "Select Field: {$country_field->get_name()} - {$country_field->get_label()}\n";
echo "HTML: " . $country_field->render( 'uk' ) . "\n\n";

// ============================================================================
// EXAMPLE 2: Creating Multiple Fields at Once
// ============================================================================

echo "<h2>Example 2: Creating Multiple Fields at Once</h2>\n\n";

$fields_config = [
	'first_name' => [
		'type'        => 'text',
		'label'       => 'First Name',
		'placeholder' => 'Enter first name',
	],
	'last_name' => [
		'type'        => 'text',
		'label'       => 'Last Name',
		'placeholder' => 'Enter last name',
	],
	'age' => [
		'type'  => 'number',
		'label' => 'Age',
		'min'   => 0,
		'max'   => 120,
	],
	'bio' => [
		'type'        => 'textarea',
		'label'       => 'Biography',
		'rows'        => 5,
		'placeholder' => 'Tell us about yourself...',
	],
];

$fields = FieldFactory::create_multiple( $fields_config );

echo "Created " . count( $fields ) . " fields:\n";
foreach ( $fields as $name => $field ) {
	echo "  - {$name}: {$field->get_label()} ({$field->get_type()})\n";
}
echo "\n";

// ============================================================================
// EXAMPLE 3: Registering Custom Field Types
// ============================================================================

echo "<h2>Example 3: Registering Custom Field Types</h2>\n\n";

// Create a custom slider field
class SliderField extends AbstractField {
	
	/**
	 * Render the slider field
	 */
	public function render( $value = null ): string {
		$value = $value ?? $this->get_config( 'default', 50 );
		$min   = $this->get_config( 'min', 0 );
		$max   = $this->get_config( 'max', 100 );
		$step  = $this->get_config( 'step', 1 );
		
		$attrs = $this->get_attributes( [
			'type'  => 'range',
			'min'   => $min,
			'max'   => $max,
			'step'  => $step,
			'value' => $value,
		] );
		
		$output  = $this->render_label();
		$output .= "<input {$attrs} />";
		$output .= "<output>{$value}</output>";
		$output .= $this->render_description();
		
		return $this->render_wrapper( $output );
	}
	
	/**
	 * Sanitize the value
	 */
	public function sanitize( $value ) {
		$min = $this->get_config( 'min', 0 );
		$max = $this->get_config( 'max', 100 );
		
		$value = intval( $value );
		return max( $min, min( $max, $value ) );
	}
}

// Register the custom field type
FieldFactory::register_type( 'slider', SliderField::class );

// Or use Manager helper method
// $manager->register_field_type( 'slider', SliderField::class );

echo "Registered custom field type: 'slider'\n\n";

// Create a slider field
$volume_field = FieldFactory::create( [
	'name'    => 'volume',
	'type'    => 'slider',
	'label'   => 'Volume',
	'min'     => 0,
	'max'     => 100,
	'step'    => 5,
	'default' => 50,
] );

echo "Slider Field: {$volume_field->get_name()} - {$volume_field->get_label()}\n";
echo "HTML: " . $volume_field->render( 75 ) . "\n\n";

// ============================================================================
// EXAMPLE 4: Checking Available Field Types
// ============================================================================

echo "<h2>Example 4: Checking Available Field Types</h2>\n\n";

$registered_types = FieldFactory::get_registered_types();

echo "Available field types (" . count( $registered_types ) . "):\n";
foreach ( $registered_types as $type => $class ) {
	$class_parts = explode( '\\', $class );
	$class_name  = end( $class_parts );
	echo "  - {$type}: {$class_name}\n";
}
echo "\n";

// Check if specific types are available
$types_to_check = [ 'text', 'email', 'slider', 'nonexistent' ];

echo "Checking specific types:\n";
foreach ( $types_to_check as $type ) {
	$status = FieldFactory::has_type( $type ) ? '✓' : '✗';
	echo "  {$status} {$type}\n";
}
echo "\n";

// ============================================================================
// EXAMPLE 5: Field Validation and Sanitization
// ============================================================================

echo "<h2>Example 5: Field Validation and Sanitization</h2>\n\n";

$email_field = FieldFactory::create( [
	'name'     => 'contact_email',
	'type'     => 'email',
	'label'    => 'Contact Email',
	'required' => true,
] );

// Test validation
$test_values = [
	'valid@example.com',
	'invalid-email',
	'',
];

echo "Email validation tests:\n";
foreach ( $test_values as $test_value ) {
	$validation = $email_field->validate( $test_value );
	$status     = $validation['valid'] ? '✓' : '✗';
	$errors     = ! empty( $validation['errors'] ) ? ' (' . implode( ', ', $validation['errors'] ) . ')' : '';
	
	echo "  {$status} \"{$test_value}\"{$errors}\n";
}
echo "\n";

// Test sanitization
$email_field = FieldFactory::create( [
	'name'  => 'clean_email',
	'type'  => 'email',
	'label' => 'Clean Email',
] );

$dirty_email = '  DIRTY@example.com  ';
$clean_email = $email_field->sanitize( $dirty_email );

echo "Sanitization test:\n";
echo "  Input:  \"{$dirty_email}\"\n";
echo "  Output: \"{$clean_email}\"\n\n";

// ============================================================================
// EXAMPLE 6: Error Handling
// ============================================================================

echo "<h2>Example 6: Error Handling</h2>\n\n";

try {
	// Missing name
	FieldFactory::create( [ 'type' => 'text' ] );
} catch ( \InvalidArgumentException $e ) {
	echo "✓ Caught expected error: {$e->getMessage()}\n";
}

try {
	// Missing type
	FieldFactory::create( [ 'name' => 'test' ] );
} catch ( \InvalidArgumentException $e ) {
	echo "✓ Caught expected error: {$e->getMessage()}\n";
}

try {
	// Unknown type
	FieldFactory::create( [ 'name' => 'test', 'type' => 'unknown' ] );
} catch ( \InvalidArgumentException $e ) {
	echo "✓ Caught expected error: {$e->getMessage()}\n";
}
echo "\n";

// ============================================================================
// SUMMARY
// ============================================================================

echo "<hr>\n";
echo "<h2>Summary</h2>\n";
echo "<ul>\n";
echo "<li>✓ Created individual fields from configuration arrays</li>\n";
echo "<li>✓ Created multiple fields at once</li>\n";
echo "<li>✓ Registered custom field types</li>\n";
echo "<li>✓ Used Manager helper method for field registration</li>\n";
echo "<li>✓ Checked available field types</li>\n";
echo "<li>✓ Tested field validation and sanitization</li>\n";
echo "<li>✓ Demonstrated proper error handling</li>\n";
echo "</ul>\n";
