# Basic Custom Post Type with JSON Configuration

This example demonstrates how to create a Custom Post Type with fields using JSON-based configuration.

## 📋 What This Example Shows

- ✅ Register a Custom Post Type (Book) using external JSON file
- ✅ Add 5 common field types to the CPT
- ✅ Separate configuration from code
- ✅ JSON schema validation
- ✅ Error handling for invalid JSON

## 🎯 Features

**Custom Post Type:** Book (identical to array example)
- Public post type with standard WordPress UI
- Supports: title, editor, thumbnail
- Appears in WordPress admin menu

**Fields Included:**
1. **ISBN** - Text field for book identification
2. **Author** - Text field for author name
3. **Publication Year** - Number field with min/max validation
4. **Genre** - Select dropdown with predefined options
5. **In Stock** - Checkbox for availability status

## 📁 Files

- `config.json` - JSON configuration file with CPT and field definitions
- `example.php` - Main plugin file that loads the JSON config
- `README.md` - This documentation file

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Rename it to your plugin name (e.g., `my-books-plugin`)
3. Activate the plugin in WordPress admin
4. The plugin reads `config.json` and registers everything automatically

### Accessing the CPT

Same as the array example - after activation:
1. Go to WordPress admin
2. Look for "Books" in the admin menu
3. Click "Add New" to create a book
4. Fill in the custom fields
5. Publish the post

## 💻 Code Breakdown

### JSON Configuration File (config.json)

```json
{
  "cpts": [
    {
      "id": "book",
      "args": {
        "label": "Books",
        "public": true,
        "supports": ["title", "editor", "thumbnail"]
      },
      "fields": [
        {
          "name": "isbn",
          "type": "text",
          "label": "ISBN",
          "description": "International Standard Book Number"
        }
      ]
    }
  ]
}
```

### PHP File (example.php)

```php
<?php
use Pedalcms\WpCmf\Core\Manager;

function basic_cpt_json_init() {
    $config_file = __DIR__ . '/config.json';

    // Load and register from JSON file
    Manager::init()->register_from_json($config_file);
}
add_action('init', 'basic_cpt_json_init');
```

That's it! WP-CMF:
- Reads and parses the JSON file
- Validates against the schema
- Registers the CPT and fields
- Handles all the WordPress integration

## 🔍 JSON Schema Validation

WP-CMF automatically validates your JSON configuration against a built-in schema. If there are errors, you'll see admin notices with helpful error messages.

**Common Validation Errors:**
- Missing required properties (id, name, type)
- Invalid field types
- Missing options for select/radio/checkbox fields
- Field names exceeding 64 characters
- Invalid CPT id format

**Example Error Notice:**
```
WP-CMF Error: JSON validation failed:
- cpts[0].fields[2] requires 'options' property for type 'select'
```

## 🔄 JSON vs Array Comparison

### JSON Advantages ✅
- **Separation of Concerns**: Config separate from code
- **Version Control**: Easy to track config changes
- **Non-PHP Editors**: Edit config without PHP knowledge
- **Schema Validation**: Automatic error detection
- **CI/CD Friendly**: Easy to swap configs per environment
- **No Code Execution**: Safer for non-developers

### Array Advantages ✅
- **Dynamic Logic**: Use PHP variables and functions
- **IDE Support**: Better autocomplete in PHP
- **No File I/O**: Slightly faster (no file reading)
- **Inline Documentation**: Comments in PHP

## 🎨 Customization Tips

### Modify the JSON Configuration

Edit `config.json` to add more fields or change settings:

```json
{
  "cpts": [
    {
      "id": "book",
      "args": {
        "label": "Books",
        "menu_icon": "dashicons-book",
        "has_archive": true
      },
      "fields": [
        {
          "name": "publisher",
          "type": "text",
          "label": "Publisher",
          "description": "Book publisher name"
        }
      ]
    }
  ]
}
```

### Environment-Specific Configurations

Use different JSON files for different environments:

```php
function basic_cpt_json_init() {
    // Load environment-specific config
    $env = wp_get_environment_type(); // 'production', 'staging', 'development'
    $config_file = __DIR__ . "/config-{$env}.json";

    // Fallback to default config
    if (!file_exists($config_file)) {
        $config_file = __DIR__ . '/config.json';
    }

    Manager::init()->register_from_json($config_file);
}
```

### Multiple JSON Files

You can load multiple JSON files:

```php
function basic_cpt_json_init() {
    $manager = Manager::init();

    // Load CPT configuration
    $manager->register_from_json(__DIR__ . '/cpts.json');

    // Load settings configuration
    $manager->register_from_json(__DIR__ . '/settings.json');
}
```

## 🧪 Testing Your JSON

Before activating the plugin, validate your JSON:

1. **Online Validators**: Use JSONLint.com
2. **VS Code**: Install "JSON Schema Validator" extension
3. **Command Line**: Use `jq` tool: `jq . config.json`
4. **WP-CMF Schema**: Validate against `wp-cmf/schema.json`

## 🔍 Retrieving Field Data

Identical to the array example:

```php
// Get field values
$isbn = get_post_meta($post_id, 'isbn', true);
$author = get_post_meta($post_id, 'author', true);

// Display in template
echo esc_html($author);
```

## 📚 Related Examples

- **[01-basic-cpt-array](../01-basic-cpt-array/)** - Same example using array configuration
- **[06-complete-json-example](../06-complete-json-example/)** - Advanced JSON example with all field types

## ❓ Common Questions

**Q: What if my JSON file has syntax errors?**
A: WP-CMF will show an admin notice with the parse error. Fix the JSON syntax and reload.

**Q: Can I use JSON for some CPTs and arrays for others?**
A: Yes! You can call both `register_from_json()` and `register_from_array()` in the same plugin.

**Q: Where should I store my JSON files?**
A: Keep them in your plugin directory, not publicly accessible. Never in `uploads/`.

**Q: Can I use JSON comments?**
A: Standard JSON doesn't support comments. Use JSON5 or strip comments before parsing if needed.

**Q: How do I validate my JSON against the WP-CMF schema?**
A: The schema is at `wp-cmf/schema.json`. Use online validators or VS Code extensions to validate against it.

## 🚦 Next Steps

1. Try modifying the `config.json` file
2. Add more fields or CPTs to the JSON
3. Compare with the array version in `01-basic-cpt-array`
4. Explore the comprehensive example in `06-complete-json-example`
5. Learn about JSON schema validation in the main docs

## 💡 Pro Tips

- **Version Your Config**: Keep config.json in git for change tracking
- **Use Environment Variables**: For sensitive data, use PHP to inject values
- **Validate First**: Always validate JSON before deploying
- **Start Simple**: Begin with minimal config, add complexity gradually
- **Test Locally**: Always test JSON changes in development first

---

**Questions or Issues?** Check the main README or Field API documentation.
