# FieldFactory Usage Example

This example demonstrates how to use the `FieldFactory` class to create and manage fields dynamically from configuration arrays.

## What is FieldFactory?

`FieldFactory` is a factory class that:
- **Creates field instances** from configuration arrays
- **Registers custom field types** for extensibility
- **Manages the field type registry** with all available field types
- **Supports batch field creation** with `create_multiple()`

## Key Features

### 1. Field Creation from Config Arrays

Create any field type using a simple configuration array:

```php
use Pedalcms\WpCmf\Field\FieldFactory;

$field = FieldFactory::create([
    'name'        => 'user_email',
    'type'        => 'email',
    'label'       => 'Email Address',
    'description' => 'Your contact email',
    'required'    => true,
]);
```

### 2. Custom Field Type Registration

Extend the framework with your own custom field types:

```php
use Pedalcms\WpCmf\Field\AbstractField;
use Pedalcms\WpCmf\Field\FieldFactory;

class SliderField extends AbstractField {
    public function render($value = null): string {
        // Custom slider implementation
    }
}

// Register your custom field
FieldFactory::register_type('slider', SliderField::class);

// Now you can create slider fields
$volume = FieldFactory::create([
    'name' => 'volume',
    'type' => 'slider',
    'min'  => 0,
    'max'  => 100,
]);
```

### 3. Batch Field Creation

Create multiple fields at once:

```php
$fields_config = [
    'first_name' => [
        'type'  => 'text',
        'label' => 'First Name',
    ],
    'last_name' => [
        'type'  => 'text',
        'label' => 'Last Name',
    ],
    'age' => [
        'type'  => 'number',
        'label' => 'Age',
    ],
];

$fields = FieldFactory::create_multiple($fields_config);
// Returns: ['first_name' => TextField, 'last_name' => TextField, 'age' => NumberField]
```

### 4. Manager Integration

Use the Manager class for a fluent interface:

```php
use Pedalcms\WpCmf\Core\Manager;

$manager = Manager::init();

// Register custom field types through Manager
$manager->register_field_type('slider', SliderField::class);
```

## Available Methods

### FieldFactory::create()

```php
FieldFactory::create(array $config): FieldInterface
```

Creates a single field instance from configuration.

**Parameters:**
- `$config` (array) - Field configuration with required keys: `name`, `type`

**Returns:** `FieldInterface` - The created field instance

**Throws:** `InvalidArgumentException` if config is invalid or type is unknown

### FieldFactory::create_multiple()

```php
FieldFactory::create_multiple(array $fields_config): array
```

Creates multiple fields from an associative array.

**Parameters:**
- `$fields_config` (array) - Associative array where keys become field names if not specified

**Returns:** `array<string, FieldInterface>` - Array of field instances keyed by name

### FieldFactory::register_type()

```php
FieldFactory::register_type(string $type, string $class_name): void
```

Registers a custom field type or overrides an existing one.

**Parameters:**
- `$type` (string) - Field type identifier
- `$class_name` (string) - Fully qualified class name implementing `FieldInterface`

**Throws:** `InvalidArgumentException` if class doesn't exist or doesn't implement `FieldInterface`

### FieldFactory::has_type()

```php
FieldFactory::has_type(string $type): bool
```

Checks if a field type is registered.

**Parameters:**
- `$type` (string) - Field type identifier

**Returns:** `bool` - True if type is registered

### FieldFactory::get_registered_types()

```php
FieldFactory::get_registered_types(): array
```

Gets all registered field types.

**Returns:** `array<string, string>` - Map of type names to class names

### FieldFactory::unregister_type()

```php
FieldFactory::unregister_type(string $type): void
```

Unregisters a field type. Useful for testing or replacing types.

**Parameters:**
- `$type` (string) - Field type identifier

### FieldFactory::reset()

```php
FieldFactory::reset(): void
```

Resets the factory to its initial state. Mainly for testing.

## Core Field Types

The following field types are registered by default:

| Type       | Class            | Description                      |
|------------|------------------|----------------------------------|
| `text`     | TextField        | Single-line text input           |
| `textarea` | TextareaField    | Multi-line text input            |
| `select`   | SelectField      | Dropdown select (single/multiple)|
| `checkbox` | CheckboxField    | Single or multiple checkboxes    |
| `radio`    | RadioField       | Radio button group               |
| `number`   | NumberField      | Numeric input with validation    |
| `email`    | EmailField       | Email input with validation      |
| `url`      | URLField         | URL input with validation        |
| `date`     | DateField        | Date picker                      |
| `password` | PasswordField    | Masked password input            |
| `color`    | ColorField       | Color picker                     |

## Configuration Options

All field types support these common configuration keys:

```php
[
    'name'        => 'field_name',      // Required: Field identifier
    'type'        => 'text',            // Required: Field type
    'label'       => 'Field Label',     // Optional: Display label
    'description' => 'Help text',       // Optional: Help text
    'default'     => 'default value',   // Optional: Default value
    'required'    => true,              // Optional: Is field required?
    'class'       => 'custom-class',    // Optional: CSS class
    'placeholder' => 'Enter text...',   // Optional: Placeholder text
    'disabled'    => false,             // Optional: Is field disabled?
    'readonly'    => false,             // Optional: Is field read-only?
]
```

Additional options vary by field type. See the individual field type examples for details.

## Error Handling

FieldFactory throws `InvalidArgumentException` in these cases:

1. **Missing required config**: `name` or `type` not provided
2. **Unknown field type**: Type not registered
3. **Invalid class**: Registered class doesn't exist
4. **Invalid implementation**: Class doesn't implement `FieldInterface`

Example error handling:

```php
try {
    $field = FieldFactory::create([
        'name' => 'test',
        'type' => 'unknown_type',
    ]);
} catch (\InvalidArgumentException $e) {
    error_log("Field creation failed: " . $e->getMessage());
}
```

## Best Practices

### 1. Validate Configuration Early

```php
$config = [
    'name' => 'user_email',
    'type' => 'email',
    'required' => true,
];

if (empty($config['name']) || empty($config['type'])) {
    throw new \InvalidArgumentException('Invalid field config');
}

$field = FieldFactory::create($config);
```

### 2. Check Type Availability

```php
$type = 'custom_slider';

if (!FieldFactory::has_type($type)) {
    // Register it or use a fallback
    $type = 'text';
}

$field = FieldFactory::create([
    'name' => 'volume',
    'type' => $type,
]);
```

### 3. Use Batch Creation for Forms

```php
// Define all form fields in one array
$form_fields = [
    'first_name' => ['type' => 'text', 'label' => 'First Name', 'required' => true],
    'last_name'  => ['type' => 'text', 'label' => 'Last Name', 'required' => true],
    'email'      => ['type' => 'email', 'label' => 'Email', 'required' => true],
    'age'        => ['type' => 'number', 'label' => 'Age', 'min' => 18],
];

// Create all fields at once
$fields = FieldFactory::create_multiple($form_fields);

// Render the form
foreach ($fields as $field) {
    echo $field->render();
}
```

### 4. Register Custom Types Early

```php
// Register your custom types during plugin initialization
add_action('init', function() {
    FieldFactory::register_type('slider', SliderField::class);
    FieldFactory::register_type('color_picker', ColorPickerField::class);
    FieldFactory::register_type('wysiwyg', WysiwygField::class);
});
```

## Integration with Milestone 4 (Array/JSON Config)

FieldFactory is designed to work seamlessly with array and JSON-based configuration (coming in Milestone 4):

```php
// Array-based registration (Milestone 4)
$manager->register_from_array([
    'settings_pages' => [
        [
            'id' => 'my-settings',
            'title' => 'My Settings',
            'fields' => [
                // FieldFactory will create these automatically
                ['name' => 'site_title', 'type' => 'text'],
                ['name' => 'contact_email', 'type' => 'email'],
            ],
        ],
    ],
]);
```

## Running the Example

```bash
# From the wp-cmf directory
php examples/field-factory-usage/example.php
```

## Related Examples

- **[Field Custom Assets](../field-custom-assets/)** - Custom fields with CSS/JS
- **[CPT Manager Usage](../cpt-manager-usage/)** - Using fields with Custom Post Types
- **[Settings Page Basic](../settings-page-basic/)** - Using fields with Settings Pages

## Next Steps

1. **Milestone 4**: Implement `Manager::register_from_array()` and `Manager::register_from_json()`
2. **JSON Schema**: Define JSON schema for field configuration validation
3. **Field Groups**: Support for organizing fields into collapsible groups
4. **Conditional Fields**: Show/hide fields based on other field values
