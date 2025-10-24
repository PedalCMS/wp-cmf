# Custom Field Assets Example

This example demonstrates how to create custom field types that enqueue their own CSS and JavaScript files.

## Overview

WP-CMF provides a built-in mechanism for fields to load their required assets (stylesheets and scripts). This is essential for complex field types that need custom styling or JavaScript functionality.

## How It Works

### 1. The `enqueue_assets()` Method

Every field implements the `FieldInterface` which includes the `enqueue_assets()` method. This method is called automatically by the Registrar when fields are being rendered on admin pages.

```php
public function enqueue_assets(): void {
    // Enqueue your CSS
    wp_enqueue_style( 'my-field-style', $url, [], '1.0.0' );

    // Enqueue your JS
    wp_enqueue_script( 'my-field-script', $url, ['jquery'], '1.0.0', true );
}
```

### 2. When Assets Are Enqueued

The Registrar hooks into `admin_enqueue_scripts` and automatically calls `enqueue_assets()` on all fields in the current context:

- **Settings Pages**: When viewing a settings page with WP-CMF fields
- **CPT Edit Screens**: When editing a post with meta box fields (coming in future milestone)
- **Any Admin Page**: Where WP-CMF fields are present

### 3. Context-Aware Loading

Assets are only loaded when needed:
- The Registrar checks the current screen (`get_current_screen()`)
- Only loads assets for fields relevant to that screen
- Prevents unnecessary asset loading on other admin pages

## Built-in Example: ColorField

The `ColorField` class demonstrates real-world asset enqueuing:

```php
use Pedalcms\WpCmf\Field\Fields\ColorField;

$field = new ColorField(
    'theme_color',
    'color',
    [
        'label'         => 'Theme Color',
        'default'       => '#007cba',
        'use_wp_picker' => true,  // Enables WordPress color picker
    ]
);
```

**What it does:**
- Enqueues WordPress core `wp-color-picker` style and script
- Adds inline JavaScript to initialize the color picker
- Falls back to HTML5 color input if WordPress picker is disabled

## Creating Custom Fields with Assets

### Step 1: Extend AbstractField

```php
use Pedalcms\WpCmf\Field\AbstractField;

class MyCustomField extends AbstractField {

    public function enqueue_assets(): void {
        // Your asset enqueuing code here
    }

    public function render( $value = null ): string {
        // Your rendering code here
    }
}
```

### Step 2: Enqueue Your Assets

```php
public function enqueue_assets(): void {
    // Enqueue CSS
    wp_enqueue_style(
        'my-custom-field',
        plugin_dir_url( __FILE__ ) . 'assets/style.css',
        [],              // Dependencies
        '1.0.0'          // Version
    );

    // Enqueue JavaScript
    wp_enqueue_script(
        'my-custom-field',
        plugin_dir_url( __FILE__ ) . 'assets/script.js',
        ['jquery'],      // Dependencies
        '1.0.0',         // Version
        true             // In footer
    );

    // Pass data to JavaScript
    wp_localize_script(
        'my-custom-field',
        'myFieldData',
        [
            'option1' => $this->config['option1'] ?? 'default',
            'option2' => $this->config['option2'] ?? 'default',
        ]
    );
}
```

### Step 3: Use Your Custom Field

```php
$manager = Manager::init();

$custom_field = new MyCustomField(
    'my_field',
    'custom',
    [
        'label'   => 'My Custom Field',
        'option1' => 'value1',
        'option2' => 'value2',
    ]
);

$manager->get_registrar()->add_fields( 'my-context', [
    'my_field' => $custom_field
] );
```

## Common Assets Hook

You can also enqueue assets that are common to all WP-CMF fields:

```php
add_action( 'wp_cmf_enqueue_common_assets', function() {
    wp_enqueue_style(
        'my-cmf-common',
        plugin_dir_url( __FILE__ ) . 'assets/common.css',
        [],
        '1.0.0'
    );
} );
```

This is useful for:
- Shared styles across all your custom fields
- Global JavaScript utilities
- Icon fonts or common resources

## Best Practices

### 1. **Check for WordPress Functions**

Always check if WordPress functions are available:

```php
public function enqueue_assets(): void {
    if ( ! function_exists( 'wp_enqueue_style' ) ) {
        return;
    }

    // Your enqueuing code...
}
```

### 2. **Use Proper Dependencies**

Declare script dependencies to ensure proper loading order:

```php
wp_enqueue_script(
    'my-script',
    $url,
    ['jquery', 'wp-color-picker'],  // Dependencies
    '1.0.0',
    true
);
```

### 3. **Unique Handle Names**

Use unique handle names to avoid conflicts:

```php
wp_enqueue_style(
    'my-plugin-my-field-style',  // Unique and descriptive
    $url
);
```

### 4. **Conditional Loading**

Only enqueue when needed based on config:

```php
public function enqueue_assets(): void {
    if ( $this->config['use_advanced_feature'] ) {
        wp_enqueue_script( 'advanced-feature', $url );
    }
}
```

### 5. **Version Numbers**

Use version numbers for cache busting:

```php
wp_enqueue_style(
    'my-style',
    $url,
    [],
    '1.0.0'  // Update when files change
);
```

## Testing Asset Enqueuing

The test suite includes tests for asset enqueuing:

```php
public function test_field_enqueue_assets() {
    $field = new MyCustomField( 'test', 'custom' );

    // Method should exist
    $this->assertTrue( method_exists( $field, 'enqueue_assets' ) );

    // Should be callable without errors
    $field->enqueue_assets();

    $this->assertTrue( true );
}
```

## Real-World Examples

### Color Picker Field
- Enqueues WordPress `wp-color-picker`
- Adds initialization JavaScript
- Provides fallback to HTML5 color input

### Date Picker Field (Future)
- Could enqueue jQuery UI datepicker
- Or use flatpickr for modern UI
- Pass locale settings via wp_localize_script

### Media Uploader Field (Future)
- Enqueues WordPress media uploader scripts
- Adds custom JavaScript for handling media selection
- Displays selected media preview

### WYSIWYG Editor Field (Future)
- Enqueues WordPress TinyMCE/Gutenberg editor
- Configures editor settings
- Handles content saving

## Summary

The asset enqueuing system provides:
- ✅ **Automatic loading** - Assets loaded only when needed
- ✅ **Context-aware** - Only loads on relevant admin screens
- ✅ **Extensible** - Easy to add custom assets to any field
- ✅ **Performance-friendly** - Prevents unnecessary asset loading
- ✅ **WordPress integration** - Uses WordPress enqueue system
- ✅ **Common assets hook** - Share assets across all fields

This makes it easy to create rich, interactive field types while maintaining good performance and following WordPress best practices.
