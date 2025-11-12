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

### Array-Based Configuration (Recommended)

```php
use Pedalcms\WpCmf\Core\Manager;

$config = [
    'cpts' => [
        [
            'id'   => 'book',
            'args' => [
                'label'    => 'Books',
                'supports' => ['title', 'editor', 'thumbnail'],
                'public'   => true,
            ],
            'fields' => [
                [
                    'name'     => 'isbn',
                    'type'     => 'text',
                    'label'    => 'ISBN',
                    'required' => true,
                ],
                [
                    'name'  => 'price',
                    'type'  => 'number',
                    'label' => 'Price',
                    'min'   => 0,
                ],
            ],
        ],
    ],
    'settings_pages' => [
        [
            'id'         => 'my-settings',
            'page_title' => 'My Plugin Settings',
            'menu_title' => 'My Plugin',
            'capability' => 'manage_options',
            'fields'     => [
                [
                    'name'  => 'api_key',
                    'type'  => 'text',
                    'label' => 'API Key',
                ],
            ],
        ],
    ],
];

Manager::init()->register_from_array($config);
```

### Custom Post Type

```php
use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\CPT\CustomPostType;

$manager = Manager::init();
$registrar = $manager->get_registrar();

$registrar->add_custom_post_type('book', [
    'label'    => 'Books',
    'supports' => ['title', 'editor', 'thumbnail'],
    'public'   => true,
]);
```

### Settings Page with Fields

```php
use Pedalcms\WpCmf\Field\FieldFactory;

// Create settings page
$registrar->add_settings_page('my-settings', [
    'page_title' => 'My Plugin Settings',
    'menu_title' => 'My Plugin',
    'capability' => 'manage_options',
]);

// Add fields
$fields = FieldFactory::create_multiple([
    'site_name' => [
        'type'     => 'text',
        'label'    => 'Site Name',
        'required' => true,
    ],
    'contact_email' => [
        'type'  => 'email',
        'label' => 'Contact Email',
    ],
    'theme_color' => [
        'type'    => 'color',
        'label'   => 'Theme Color',
        'default' => '#3498db',
    ],
]);

$registrar->add_fields('my-settings', $fields);
```

### Custom Field Type

```php
use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\FieldFactory;

class SliderField extends AbstractField {
    public function render($value = null): string {
        $value = $value ?? $this->get_config('default', 50);
        $min = $this->get_config('min', 0);
        $max = $this->get_config('max', 100);

        $attrs = $this->get_attributes([
            'type'  => 'range',
            'min'   => $min,
            'max'   => $max,
            'value' => $value,
        ]);

        return $this->render_wrapper(
            $this->render_label() .
            "<input {$attrs} />" .
            "<output>{$value}</output>" .
            $this->render_description()
        );
    }
}

// Register and use
FieldFactory::register_type('slider', SliderField::class);

$volume = FieldFactory::create([
    'name'  => 'volume',
    'type'  => 'slider',
    'label' => 'Volume',
]);
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

### ✅ Completed Milestones

#### Milestone 1-4: Core Framework ✅
- ✅ Custom Post Type registration
- ✅ Settings Page registration
- ✅ Field Interface & AbstractField
- ✅ 11 Core field types
- ✅ FieldFactory for dynamic field creation
- ✅ Field asset enqueuing system
- ✅ Core CSS and JavaScript files
- ✅ Automatic asset loading with context awareness
- ✅ Array-based configuration
- ✅ JSON configuration with schema validation
- ✅ Enhanced JSON schema with strict validation
- ✅ Extended testing with edge cases & integration tests

#### Milestone 5: Security & Internationalization ✅
- ✅ **M5F1**: Sanitize & validate pipeline for fields
- ✅ **M5F2**: Nonces and capability checks (CSRF protection)
- ✅ **M5F3**: Escaping output on render (esc_attr, esc_html)
- ✅ **M5F4**: i18n support with translation infrastructure
  - Text domain 'wp-cmf' implemented
  - Translation helper with WordPress fallback
  - POT template file (languages/wp-cmf.pot)
  - Complete translation documentation
  - 10 translatable strings

### 🔄 In Progress
- Milestone 6: Additional documentation & examples
- Milestone 7: CI/CD pipeline & first release

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
