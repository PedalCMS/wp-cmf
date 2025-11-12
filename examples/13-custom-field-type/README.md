# Example 13: Custom Field Type

This example demonstrates how to **create a custom field type** by extending `AbstractField` and then **use it in WordPress settings**.

## Overview

- **Custom Field**: SliderField (HTML5 range input with enhancements)
- **Field Features**: Min/max/step values, unit display, value markers, live updates
- **Integration**: Adds slider fields to WordPress General Settings page
- **Complete Implementation**: Shows all required methods and best practices

## What This Example Shows

✅ **Creating Custom Field Types**: Complete SliderField implementation
✅ **Extending AbstractField**: Proper inheritance and method overrides
✅ **Field Registration**: Using `Manager::register_field_type()`
✅ **Asset Enqueuing**: Custom CSS and JavaScript for the field
✅ **Field Configuration**: All standard field options plus custom properties
✅ **Integration Example**: Using the custom field in actual settings

## Files

- `SliderField.php` - Complete custom field class (330+ lines)
- `example.php` - Registration and usage example (135 lines)
- `README.md` - This documentation

## Quick Start

```php
use Pedalcms\WpCmf\Core\Manager;

// 1. Register the custom field type
$manager = Manager::init();
$manager->register_field_type( 'slider', 'SliderField' );

// 2. Use it in your configuration
$config = [
	'settings_pages' => [
		[
			'id'     => 'general',
			'fields' => [
				[
					'name'  => 'my_slider',
					'type'  => 'slider', // Your custom type!
					'label' => 'My Slider',
					'min'   => 0,
					'max'   => 100,
					'step'  => 5,
				],
			],
		],
	],
];

$manager->register_from_array( $config );
```

## SliderField Configuration

The SliderField extends the standard field configuration with additional options:

### Standard Options (from AbstractField)
- `name` (string, required) - Field identifier
- `type` (string) - Must be 'slider'
- `label` (string) - Field label
- `description` (string) - Help text below field
- `default` (number) - Default value
- `required` (bool) - Whether field is required

### Slider-Specific Options
- `min` (number) - Minimum value (default: 0)
- `max` (number) - Maximum value (default: 100)
- `step` (number) - Increment step (default: 1)
- `unit` (string) - Unit to display after value (e.g., '%', 'px', 'ms')
- `show_value` (bool) - Display current value (default: true)
- `marks` (array) - Value markers to display below slider
  - Format: `[value => label]`
  - Example: `[0 => 'Low', 50 => 'Med', 100 => 'High']`

## Complete Examples from example.php

### Example 1: Site Quality Level (with markers)
```php
[
	'name'        => 'site_quality',
	'type'        => 'slider',
	'label'       => 'Site Quality Level',
	'description' => 'Set the overall quality/performance level for your site',
	'default'     => 75,
	'min'         => 0,
	'max'         => 100,
	'step'        => 5,
	'unit'        => '%',
	'show_value'  => true,
	'marks'       => [
		0   => 'Low',
		50  => 'Medium',
		100 => 'High',
	],
]
```

### Example 2: Content Width (pixels)
```php
[
	'name'        => 'content_width',
	'type'        => 'slider',
	'label'       => 'Content Width',
	'description' => 'Maximum width for content area in pixels',
	'default'     => 1200,
	'min'         => 600,
	'max'         => 1920,
	'step'        => 50,
	'unit'        => 'px',
	'show_value'  => true,
]
```

### Example 3: Image Quality (percentage)
```php
[
	'name'        => 'image_quality',
	'type'        => 'slider',
	'label'       => 'Image Quality',
	'description' => 'JPEG compression quality for uploaded images',
	'default'     => 85,
	'min'         => 1,
	'max'         => 100,
	'step'        => 1,
	'unit'        => '%',
	'show_value'  => true,
	'marks'       => [
		1   => 'Min',
		85  => 'Recommended',
		100 => 'Max',
	],
]
```

### Example 4: Opacity (decimal)
```php
[
	'name'        => 'admin_menu_opacity',
	'type'        => 'slider',
	'label'       => 'Admin Menu Opacity',
	'description' => 'Transparency level for the WordPress admin menu',
	'default'     => 1.0,
	'min'         => 0.3,
	'max'         => 1.0,
	'step'        => 0.1,
	'show_value'  => true,
]
```

## Creating Your Own Custom Field Type

### Step 1: Extend AbstractField

```php
use Pedalcms\WpCmf\Field\AbstractField;

class MyCustomField extends AbstractField {

	/**
	 * Get the field type identifier
	 */
	public function get_type(): string {
		return 'my-custom-type';
	}

	/**
	 * Render the field HTML
	 */
	public function render( $value = null ): string {
		// Your rendering logic here
		$output  = $this->render_wrapper_start();
		$output .= $this->render_label();

		// Your custom HTML
		$output .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" />',
			$this->esc_attr( $this->get_field_id() ),
			$this->esc_attr( $this->get_name() ),
			$this->esc_attr( $value ?? $this->get_config( 'default', '' ) )
		);

		$output .= $this->render_description();
		$output .= $this->render_wrapper_end();

		return $output;
	}

	/**
	 * Sanitize input value
	 */
	public function sanitize( $value ) {
		// Your sanitization logic
		return sanitize_text_field( $value );
	}

	/**
	 * Validate input value
	 */
	public function validate( $value ): array {
		$errors = [];

		// Your validation logic
		if ( empty( $value ) && $this->get_config( 'required' ) ) {
			$errors[] = $this->get_label() . ' is required.';
		}

		return [
			'valid'  => empty( $errors ),
			'errors' => $errors,
		];
	}
}
```

### Step 2: Register the Field Type

```php
Manager::init()->register_field_type( 'my-custom-type', 'MyCustomField' );
```

### Step 3: Use in Configuration

```php
$config = [
	'settings_pages' => [
		[
			'id'     => 'general',
			'fields' => [
				[
					'type'  => 'my-custom-type',
					'name'  => 'my_field',
					'label' => 'My Field',
				],
			],
		],
	],
];
```

## Required Methods (from FieldInterface)

Your custom field class must implement these methods:

1. **`render( $value = null ): string`** - Generate HTML for the field
2. **`sanitize( $value )`** - Clean/sanitize user input
3. **`validate( $value ): array`** - Validate input and return errors
4. **`get_name(): string`** - Return field name (inherited from AbstractField)
5. **`get_label(): string`** - Return field label (inherited from AbstractField)
6. **`get_type(): string`** - Return field type identifier
7. **`get_schema(): array`** - Return JSON schema (inherited from AbstractField)
8. **`get_config( $key, $default )`** - Get config value (inherited from AbstractField)
9. **`set_config( array $config )`** - Set config values (inherited from AbstractField)

## Optional Methods

Override these for enhanced functionality:

- **`enqueue_assets(): void`** - Load custom CSS/JS
- **`get_defaults(): array`** - Set default config values
- **`get_schema(): array`** - Define JSON schema for your field

## Helper Methods Available from AbstractField

Your custom field has access to these helpers:

- `$this->get_name()` - Get field name
- `$this->get_label()` - Get field label
- `$this->get_config( $key, $default )` - Get config value
- `$this->get_field_id()` - Get HTML ID for the field
- `$this->esc_attr( $text )` - Escape attribute value
- `$this->esc_html( $text )` - Escape HTML content
- `$this->translate( $text )` - Translate text with fallback
- `$this->render_wrapper_start()` - Start field wrapper div
- `$this->render_wrapper_end()` - End field wrapper div
- `$this->render_label()` - Render field label
- `$this->render_description()` - Render field description
- `$this->build_attributes( $attrs )` - Build HTML attributes string

## Usage Examples

### Retrieve Slider Values

```php
// Get values from WordPress options
$quality = get_option( 'site_quality', 75 );
$width   = get_option( 'content_width', 1200 );
$img_quality = get_option( 'image_quality', 85 );
$opacity = get_option( 'admin_menu_opacity', 1.0 );

// Use in your code
if ( $quality >= 80 ) {
	// High quality mode
	enable_advanced_features();
}

echo '<div style="max-width: ' . $width . 'px;">';
```

## Assets & Styling

The SliderField includes:

**JavaScript:**
- Live value updates as slider moves
- jQuery-based event handling

**CSS:**
- Custom styled range input
- Responsive design
- WordPress admin color scheme integration
- Positioned value markers

## Key Features of SliderField Implementation

1. **Value Display** - Shows current value with unit in real-time
2. **Value Markers** - Optional labeled markers at specific positions
3. **Constraints** - Min/max/step validation and sanitization
4. **Responsive** - Works on mobile and desktop
5. **Accessible** - Proper labels and ARIA attributes
6. **WordPress Integration** - Uses admin styles and jQuery
7. **Flexible** - Supports integers, floats, custom units

## Field Registration Patterns

### Pattern 1: Direct Class Reference
```php
Manager::init()->register_field_type( 'slider', 'SliderField' );
```

### Pattern 2: Using FieldFactory (alternative)
```php
use Pedalcms\WpCmf\Field\FieldFactory;

FieldFactory::register_type( 'slider', SliderField::class );
```

### Pattern 3: Global Registration (for plugins)
```php
add_action( 'init', function() {
	Manager::init()->register_field_type( 'slider', 'SliderField' );
}, 5 ); // Priority 5 ensures it runs before other inits
```

## Best Practices

1. **Extend AbstractField** - Don't implement FieldInterface directly
2. **Use Helper Methods** - Leverage esc_attr(), esc_html(), render_label(), etc.
3. **Enqueue Assets** - Use `enqueue_assets()` for field-specific CSS/JS
4. **Proper Validation** - Return array with 'valid' and 'errors' keys
5. **Sanitize Data** - Always clean user input in `sanitize()`
6. **Document Configuration** - Add PHPDoc for custom config options
7. **Provide Defaults** - Override `get_defaults()` for field-specific defaults
8. **Test Thoroughly** - Test with various config combinations
9. **Follow Standards** - Match WordPress coding standards
10. **Add Schema** - Define JSON schema for JSON configuration support

## Related Examples

- **Example 5**: Complete array configuration (uses core fields)
- **Example 6**: Complete JSON configuration (uses core fields)
- **Example 9**: Adding fields to existing settings page
- **Field Factory Usage**: `examples/field-factory-usage/`

## Further Reading

- [Field API Documentation](../../docs/field-api.md)
- [AbstractField Source](../../src/Field/AbstractField.php)
- [FieldInterface Source](../../src/Field/FieldInterface.php)
- [Core Field Examples](../../src/Field/fields/)
