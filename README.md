# WP-CMF (WordPress Content Modeling Framework)

A powerful, flexible Composer library for building WordPress plugins with custom post types, settings pages, and dynamic form fields.

## Features

- **Custom Post Types**: Easy registration with fluent interface and array configuration
- **Settings Pages**: Top-level and submenu pages with automatic rendering
- **Dynamic Fields**: 11 core field types with extensibility via custom field types
- **Array Configuration**: Register CPTs, settings, and fields from a single array ✨ NEW
- **Asset System**: Automatic CSS/JS loading for field styling and validation ✨ NEW
- **Configuration-Driven**: Create fields from PHP arrays or JSON files
- **JSON Configuration**: Load configurations from JSON files with schema validation ✨ NEW
- **Advanced Validation**: Enhanced schema with boundary checks and type-specific rules ✨ NEW
- **Validation & Sanitization**: Built-in security with customizable rules
- **Asset Management**: Context-aware CSS/JS enqueuing for fields
- **Type-Safe**: PSR-4 autoloading with full interface contracts
- **Well-Tested**: 229 PHPUnit tests with 691 assertions ✨ UPDATED
- **Security**: Nonces, capability checks, output escaping, and input sanitization
- **i18n Ready**: Full internationalization support with translation infrastructure ✨ NEW

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

## Core Field Types

WP-CMF includes 11 ready-to-use field types:

| Type       | Description                    | Features                          |
|------------|--------------------------------|-----------------------------------|
| `text`     | Single-line text input         | Placeholder, maxlength, pattern   |
| `textarea` | Multi-line text input          | Rows, cols, maxlength             |
| `select`   | Dropdown select                | Single/multiple, options          |
| `checkbox` | Single or multiple checkboxes  | Inline/stacked layout             |
| `radio`    | Radio button group             | Inline/stacked layout             |
| `number`   | Numeric input                  | Min, max, step, validation        |
| `email`    | Email input                    | Automatic validation              |
| `url`      | URL input                      | Automatic validation              |
| `date`     | Date picker                    | Min/max date, format validation   |
| `password` | Password input                 | Masked, security-focused          |
| `color`    | Color picker                   | WordPress color picker integration|

## Documentation

- **[Field API](docs/field-api.md)** - Complete field system documentation
- **[Usage Guide](docs/usage.md)** - Comprehensive usage examples
- **[Examples](examples/)** - 13 working examples covering all features

## Examples

WP-CMF includes 13 complete, working examples demonstrating all major features:

### Basic Examples
1. **[Basic CPT (Array)](examples/01-basic-cpt-array/)** - Simple custom post type with array configuration
2. **[Basic CPT (JSON)](examples/02-basic-cpt-json/)** - Simple custom post type with JSON configuration
3. **[Settings Page (Array)](examples/03-settings-page-array/)** - Basic settings page with array config
4. **[Settings Page (JSON)](examples/04-settings-page-json/)** - Basic settings page with JSON config

### Complete Integration Examples
5. **[Complete Array Example](examples/05-complete-array-example/)** - Multiple CPTs and settings pages with all field types (array)
6. **[Complete JSON Example](examples/06-complete-json-example/)** - Multiple CPTs and settings pages with all field types (JSON)

### Advanced Features
7. **[Existing Post Type (Array)](examples/07-existing-post-type-array/)** - Add fields to WordPress core post types (array)
8. **[Existing Post Type (JSON)](examples/08-existing-post-type-json/)** - Add fields to WordPress core post types (JSON)
9. **[Existing Settings Page (Array)](examples/09-existing-settings-page-array/)** - Add fields to WordPress General Settings (array)
10. **[Existing Settings Page (JSON)](examples/10-existing-settings-page-json/)** - Add fields to WordPress General Settings (JSON)

### Advanced Integration Examples
11. **[CPT with Submenu Settings (Array)](examples/11-cpt-with-submenu-settings/)** ⭐ - Product CPT with settings submenu (13 fields + 19 settings, array config)
12. **[CPT with Submenu Settings (JSON)](examples/12-cpt-with-submenu-settings-json/)** ⭐ - Product CPT with settings submenu (13 fields + 19 settings, JSON config)

### Custom Field Types
13. **[Custom Field Type](examples/13-custom-field-type/)** ⭐ - Create custom SliderField and use in settings

**⭐ = Recommended starting points for complex projects**

## Requirements

- **PHP**: 8.1 or higher
- **WordPress**: 6.0 or higher
- **Composer**: For autoloading

## Development Status

### ✅ Completed Features

**Core Framework**
- ✅ Custom Post Types with full WordPress integration
- ✅ Settings Pages (top-level and submenu)
- ✅ 11 production-ready field types
- ✅ Custom field type extensibility via `AbstractField`
- ✅ FieldFactory for dynamic field creation
- ✅ Array-based configuration system
- ✅ JSON configuration with schema validation
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
- ✅ 229 PHPUnit tests (691 assertions)
- ✅ Edge case and integration testing
- ✅ 13 complete working examples

### 🎯 Roadmap
- 📋 Milestone 6: Documentation expansion
- 🚀 Milestone 7: CI/CD pipeline & v1.0 release

## Testing

**Current Status: 229/229 tests passing (691 assertions)** ✅

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
