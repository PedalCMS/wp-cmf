# WP-CMF (WordPress Content Modeling Framework)

A powerful, flexible Composer library for building WordPress plugins with custom post types, settings pages, and dynamic form fields.

## Features

- **Custom Post Types**: Easy registration with fluent interface and array configuration
- **Settings Pages**: Top-level and submenu pages with automatic rendering
- **Dynamic Fields**: 11 core field types with extensibility via custom field types
- **Array Configuration**: Register CPTs, settings, and fields from a single array ✨ NEW
- **Asset System**: Automatic CSS/JS loading for field styling and validation ✨ NEW
- **Configuration-Driven**: Create fields from PHP arrays or JSON (Milestone 4)
- **Validation & Sanitization**: Built-in security with customizable rules
- **Asset Management**: Context-aware CSS/JS enqueuing for fields
- **Type-Safe**: PSR-4 autoloading with full interface contracts
- **Well-Tested**: 156 PHPUnit tests with 481 assertions

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

## Documentation

- **[Field API](docs/field-api.md)** - Complete field system documentation
- **[Usage Guide](docs/usage.md)** - Comprehensive usage examples
- **[Examples](examples/)** - 10 working examples covering all features

## Requirements

- **PHP**: 8.1 or higher
- **WordPress**: 6.0 or higher
- **Composer**: For autoloading

## Development Status

### ✅ Completed (Milestone 3)
- ✅ Custom Post Type registration
- ✅ Settings Page registration
- ✅ Field Interface & AbstractField
- ✅ 11 Core field types
- ✅ FieldFactory for dynamic field creation
- ✅ Field asset enqueuing system
- ✅ Core CSS and JavaScript files
- ✅ Automatic asset loading with context awareness
- ✅ Comprehensive documentation

### 🔄 In Progress
- Milestone 4: Array/JSON-driven configuration (COMPLETE - testing phase)
- Milestone 5: Security hardening
- Milestone 6: Additional documentation
- Milestone 7: CI/CD pipeline

## Testing

Run the test suite:

```bash
composer test
```

Current coverage: **156 tests, 481 assertions** ✅

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
