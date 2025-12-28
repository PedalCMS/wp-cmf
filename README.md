# WP-CMF (WordPress Content Modeling Framework)

A powerful, flexible Composer library for building WordPress plugins with custom post types, settings pages, and dynamic form fields.

## Features

- **Custom Post Types**: Easy registration with fluent interface and array configuration
- **Settings Pages**: Top-level and submenu pages with automatic rendering
- **16 Field Types**: Complete set including containers (tabs, metabox, group, repeater)
- **Container Fields**: Organize fields with tabs, metaboxes, groups, and repeaters
- **Array Configuration**: Register CPTs, settings, and fields from a single array
- **JSON Configuration**: Load configurations from JSON files with schema validation
- **Before-Save Filters**: Modify or validate field values before saving
- **Validation & Sanitization**: Built-in security with customizable rules
- **Asset Management**: Context-aware CSS/JS enqueuing for fields
- **Type-Safe**: PSR-4 autoloading with full interface contracts
- **Well-Tested**: 298 PHPUnit tests with 877 assertions
- **Security**: Nonces, capability checks, output escaping, and input sanitization
- **i18n Ready**: Full internationalization support with translation infrastructure

## Installation

```bash
composer require pedalcms/wp-cmf
```

## Quick Start

### Complete Example - Array Configuration (Recommended)

Create a complete plugin with custom post types and settings in one configuration array:

```php
<?php
/**
 * Plugin Name: My Custom Plugin
 * Description: Built with WP-CMF
 */

use Pedalcms\WpCmf\Core\Manager;

function my_plugin_init() {
    $config = [
        'cpts' => [
            [
                'id'   => 'product',
                'args' => [
                    'label'       => 'Products',
                    'public'      => true,
                    'has_archive' => true,
                    'supports'    => [ 'title', 'editor', 'thumbnail' ],
                    'menu_icon'   => 'dashicons-cart',
                ],
                'fields' => [
                    [
                        'name'        => 'sku',
                        'type'        => 'text',
                        'label'       => 'SKU',
                        'required'    => true,
                        'placeholder' => 'PROD-001',
                    ],
                    [
                        'name'    => 'price',
                        'type'    => 'number',
                        'label'   => 'Price',
                        'min'     => 0,
                        'step'    => 0.01,
                        'default' => 0,
                    ],
                    [
                        'name'    => 'stock_status',
                        'type'    => 'select',
                        'label'   => 'Stock Status',
                        'options' => [
                            'in_stock'    => 'In Stock',
                            'out_of_stock' => 'Out of Stock',
                            'on_backorder' => 'On Backorder',
                        ],
                        'default' => 'in_stock',
                    ],
                    [
                        'name'        => 'featured',
                        'type'        => 'checkbox',
                        'label'       => 'Featured Product',
                        'description' => 'Display this product prominently',
                    ],
                ],
            ],
        ],
        'settings_pages' => [
            [
                'id'         => 'my-plugin-settings',
                'page_title' => 'My Plugin Settings',
                'menu_title' => 'My Plugin',
                'capability' => 'manage_options',
                'icon'       => 'dashicons-admin-settings',
                'fields'     => [
                    [
                        'name'        => 'api_key',
                        'type'        => 'text',
                        'label'       => 'API Key',
                        'required'    => true,
                        'description' => 'Enter your API key from the service provider',
                    ],
                    [
                        'name'    => 'api_secret',
                        'type'    => 'password',
                        'label'   => 'API Secret',
                        'required' => true,
                    ],
                    [
                        'name'        => 'enable_cache',
                        'type'        => 'checkbox',
                        'label'       => 'Enable Caching',
                        'description' => 'Cache API responses for better performance',
                        'default'     => true,
                    ],
                    [
                        'name'        => 'cache_duration',
                        'type'        => 'number',
                        'label'       => 'Cache Duration (hours)',
                        'min'         => 1,
                        'max'         => 168,
                        'default'     => 24,
                    ],
                ],
            ],
        ],
    ];

    Manager::init()->register_from_array( $config );
}
add_action( 'init', 'my_plugin_init' );
```

### Minimal Example - Just a Custom Post Type

```php
use Pedalcms\WpCmf\Core\Manager;

function my_cpt_init() {
    Manager::init()->register_from_array([
        'cpts' => [
            [
                'id'   => 'book',
                'args' => [
                    'label'    => 'Books',
                    'public'   => true,
                    'supports' => [ 'title', 'editor', 'thumbnail' ],
                ],
                'fields' => [
                    [
                        'name'  => 'isbn',
                        'type'  => 'text',
                        'label' => 'ISBN',
                    ],
                    [
                        'name'  => 'author',
                        'type'  => 'text',
                        'label' => 'Author',
                    ],
                ],
            ],
        ],
    ]);
}
add_action( 'init', 'my_cpt_init' );
```

### Add Fields to Existing WordPress Settings

```php
use Pedalcms\WpCmf\Core\Manager;

function extend_general_settings() {
    Manager::init()->register_from_array([
        'settings_pages' => [
            [
                'id'     => 'general', // WordPress built-in page
                'fields' => [
                    [
                        'name'        => 'company_name',
                        'type'        => 'text',
                        'label'       => 'Company Name',
                        'description' => 'Your company or organization name',
                    ],
                    [
                        'name'  => 'contact_email',
                        'type'  => 'email',
                        'label' => 'Contact Email',
                    ],
                ],
            ],
        ],
    ]);
}
add_action( 'init', 'extend_general_settings' );
```

## Field Value Filters (Before Save Hooks)

WP-CMF provides a powerful filter system that allows you to modify, validate, or reject field values just before they are saved to the database. This works for both CPT metaboxes and settings pages.

### Available Filters

#### 1. Global Filter: `wp_cmf_before_save_field`

Runs for **all fields** just before saving. Receives three parameters:

```php
add_filter( 'wp_cmf_before_save_field', function( $value, $field_name, $page_id ) {
    // $value     - The sanitized field value
    // $field_name - The field name/key
    // $page_id   - The context (CPT slug or settings page ID)
    
    return $value; // Return modified value, or null to skip saving
}, 10, 3 );
```

#### 2. Field-Specific Filter: `wp_cmf_before_save_field_{field_name}`

Runs only for a specific field. Receives just the value:

```php
add_filter( 'wp_cmf_before_save_field_my_field', function( $value ) {
    // Modify value for 'my_field' only
    return $value;
} );
```

### Filter Examples

**Auto-calculate reading time:**
```php
add_filter( 'wp_cmf_before_save_field', function( $value, $field_name, $page_id ) {
    if ( 'reading_time' === $field_name && 'post' === $page_id && empty( $value ) ) {
        if ( isset( $_POST['content'] ) ) {
            $content = wp_unslash( $_POST['content'] );
            $word_count = str_word_count( wp_strip_all_tags( $content ) );
            return max( 1, ceil( $word_count / 200 ) );
        }
    }
    return $value;
}, 10, 3 );
```

**Sanitize and format a subtitle:**
```php
add_filter( 'wp_cmf_before_save_field_post_subtitle', function( $value ) {
    return ucwords( strtolower( sanitize_text_field( $value ) ) );
} );
```

**Prevent saving based on user role:**
```php
add_filter( 'wp_cmf_before_save_field_featured_content', function( $value ) {
    if ( ! current_user_can( 'edit_others_posts' ) ) {
        return null; // Skip saving - only editors can feature content
    }
    return $value;
} );
```

**Block URLs from specific domains:**
```php
add_filter( 'wp_cmf_before_save_field_external_source', function( $value ) {
    $blocked = [ 'spam-site.com', 'malware.net' ];
    $host = wp_parse_url( $value, PHP_URL_HOST );
    
    foreach ( $blocked as $domain ) {
        if ( stripos( $host, $domain ) !== false ) {
            return null; // Don't save blocked URLs
        }
    }
    return $value;
} );
```

**Log field changes for auditing:**
```php
add_filter( 'wp_cmf_before_save_field', function( $value, $field_name, $page_id ) {
    $post_id = isset( $_POST['post_ID'] ) ? absint( $_POST['post_ID'] ) : 0;
    if ( $post_id ) {
        $old_value = get_post_meta( $post_id, $field_name, true );
        if ( $old_value !== $value ) {
            error_log( sprintf( '[Audit] %s changed on post %d', $field_name, $post_id ) );
        }
    }
    return $value;
}, 20, 3 );
```

> **Note:** Return `null` from any filter to prevent the field from being saved. The existing value will be preserved.

See [examples 7-10](examples/) for complete filter implementations.

## Core Field Types

WP-CMF includes 16 ready-to-use field types:

### Basic Fields

| Type       | Description                    | Features                          |
|------------|--------------------------------|-----------------------------------|
| `text`     | Single-line text input         | Placeholder, maxlength, pattern   |
| `textarea` | Multi-line text input          | Rows, cols, maxlength             |
| `number`   | Numeric input                  | Min, max, step, validation        |
| `email`    | Email input                    | Automatic validation              |
| `url`      | URL input                      | Automatic validation              |
| `password` | Password input                 | Masked, security-focused          |
| `date`     | Date picker                    | Min/max date, format validation   |
| `color`    | Color picker                   | WordPress color picker integration|

### Choice Fields

| Type       | Description                    | Features                          |
|------------|--------------------------------|-----------------------------------|
| `select`   | Dropdown select                | Single/multiple, options          |
| `checkbox` | Single or multiple checkboxes  | Inline/stacked layout             |
| `radio`    | Radio button group             | Inline/stacked layout             |

### Rich Content

| Type       | Description                    | Features                          |
|------------|--------------------------------|-----------------------------------|
| `wysiwyg`  | WordPress visual editor        | Full TinyMCE with media buttons   |

### Container Fields

| Type       | Description                    | Features                          |
|------------|--------------------------------|-----------------------------------|
| `tabs`     | Tabbed container               | Horizontal/vertical, icons        |
| `metabox`  | Metabox container              | Context, priority, nested fields  |
| `group`    | Grouped fields section         | Label, description, visual group  |
| `repeater` | Repeatable field set           | Min/max rows, dynamic add/remove  |

## Documentation

- **[Field API](docs/field-api.md)** - Complete field system documentation
- **[Usage Guide](docs/usage.md)** - Comprehensive usage examples
- **[Examples](examples/)** - 4 focused examples covering all features

## Examples

WP-CMF includes 4 comprehensive examples organized by complexity and configuration style:

### Simple Examples
Basic usage demonstrating core features with minimal configuration:

1. **[Simple Array](examples/01-simple-array/)** - Book CPT + Library Settings using PHP arrays
2. **[Simple JSON](examples/02-simple-json/)** - Event CPT + Events Settings using JSON configuration

### Advanced Examples ⭐
Comprehensive examples demonstrating **all 16 field types** and advanced features:

3. **[Advanced Array](examples/03-advanced-array/)** - Complete demonstration with PHP arrays:
   - Product CPT with multiple metaboxes
   - Store Settings page with vertical tabs
   - Adding fields to existing post types (`post`, `page`)
   - Adding fields to existing settings (General)
   - Container nesting, repeaters, groups
   - Before-save filters

4. **[Advanced JSON](examples/04-advanced-json/)** - Complete demonstration with JSON files:
   - Property CPT with tabs, groups, repeaters
   - Agency Settings page with all field types
   - Extensions for posts, pages, and settings
   - Multi-file JSON organization

**⭐ Advanced examples are recommended starting points for complex projects**

## Requirements

- **PHP**: 8.1 or higher
- **WordPress**: 6.0 or higher
- **Composer**: For autoloading

## Development Status

### ✅ Completed Features

**Core Framework**
- ✅ Custom Post Types with full WordPress integration
- ✅ Settings Pages (top-level and submenu)
- ✅ 16 production-ready field types (including containers)
- ✅ Container fields: tabs, metabox, group, repeater
- ✅ Custom field type extensibility via `AbstractField`
- ✅ FieldFactory for dynamic field creation
- ✅ Array-based configuration system
- ✅ JSON configuration with schema validation
- ✅ Before-save filters for field value modification
- ✅ Context-aware asset enqueuing (CSS/JS)

**Security & Standards**
- ✅ Input sanitization and validation
- ✅ CSRF protection (nonces and capability checks)
- ✅ Output escaping (XSS prevention)
- ✅ WordPress coding standards compliant
- ✅ PSR-4 autoloading

**Internationalization**
- ✅ Full i18n support with `wp-cmf` text domain
- ✅ Translation template (POT file)
- ✅ WordPress and non-WordPress fallbacks

**Quality Assurance**
- ✅ 298 PHPUnit tests (877 assertions)
- ✅ Edge case and integration testing
- ✅ 4 comprehensive working examples

### 🎯 Roadmap
- 📋 Milestone 6: Documentation expansion
- 🚀 Milestone 7: CI/CD pipeline & v1.0 release

## Testing

**Current Status: 321/321 tests passing (974 assertions)** ✅

Run the test suite:

```bash
composer test
```

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`composer test`)
5. Submit a pull request

## License

This project is licensed under the GPL-2.0-or-later License. See [LICENSE](LICENSE) for details.

## Support

- **Issues**: [GitHub Issues](https://github.com/PedalCMS/wp-cmf/issues)
- **Documentation**: [docs/](docs/)
- **Examples**: [examples/](examples/)
