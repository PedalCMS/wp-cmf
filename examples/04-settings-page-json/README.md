# Settings Page with JSON Configuration

This example demonstrates how to create a WordPress settings page with fields using JSON-based configuration.

## 📋 What This Example Shows

- ✅ Create a settings page using external JSON file
- ✅ Add 5 different field types
- ✅ Separate configuration from code
- ✅ JSON schema validation for settings
- ✅ Error handling and admin notices

## 🎯 Features

**Settings Page:** My Plugin Settings (identical to array example)
- Top-level menu item in WordPress admin
- Custom icon and position
- Restricted to `manage_options` capability

**Fields Included:**
1. **Site Title** - Text field (required)
2. **Site Description** - Textarea
3. **Enable Feature** - Checkbox toggle
4. **Theme Color** - Color picker
5. **Contact Email** - Email with validation (required)

## 📁 Files

- `config.json` - JSON configuration file
- `example.php` - Plugin file that loads JSON
- `README.md` - This documentation

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Activate the plugin
3. Go to WordPress admin → "My Plugin"
4. Configure settings and save

## 💻 Code Breakdown

### JSON Configuration (config.json)

```json
{
  "settings_pages": [
    {
      "id": "my_plugin_settings",
      "title": "My Plugin Settings",
      "menu_title": "My Plugin",
      "capability": "manage_options",
      "slug": "my-plugin-settings",
      "fields": [
        {
          "name": "site_title",
          "type": "text",
          "label": "Site Title",
          "required": true
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

function settings_json_init() {
    $config_file = __DIR__ . '/config.json';
    Manager::init()->register_from_json($config_file);
}
add_action('init', 'settings_json_init');
```

## 🎨 Advantages of JSON Configuration

### Clean Separation ✅
- Configuration separate from logic
- Easy to understand structure
- Non-developers can edit settings

### Version Control Friendly ✅
- Track config changes easily
- See exactly what changed in diffs
- Roll back to previous configs

### Environment Management ✅
```php
// Load environment-specific configs
$env = wp_get_environment_type();
$config = __DIR__ . "/config-{$env}.json";
Manager::init()->register_from_json($config);
```

### Validation Built-in ✅
- Automatic schema validation
- Clear error messages
- Catches mistakes early

## 🔍 Retrieving Settings

Same as array example:

```php
// Get settings
$title = get_option('site_title', 'Default');
$color = get_option('theme_color', '#0073aa');

// Use in templates
echo esc_html($title);
```

## 📊 JSON Schema Benefits

The JSON is automatically validated against WP-CMF's schema:

**Validates:**
- Required properties (id, name, type)
- Field type validity
- Options for select/radio/checkbox
- Field name length (max 64 chars)
- Value constraints (min/max, etc.)

**Example Validation Error:**
```
WP-CMF Error: JSON validation failed:
- settings_pages[0].fields[1] requires 'label' property
```

## 🎨 Customization Examples

### Multiple Settings Pages

**config.json:**
```json
{
  "settings_pages": [
    {
      "id": "general_settings",
      "title": "General Settings",
      "menu_title": "General",
      "slug": "my-plugin-general",
      "fields": [...]
    },
    {
      "id": "advanced_settings",
      "title": "Advanced Settings",
      "menu_title": "Advanced",
      "slug": "my-plugin-advanced",
      "fields": [...]
    }
  ]
}
```

### Submenu Page

```json
{
  "settings_pages": [
    {
      "id": "my_settings",
      "title": "My Settings",
      "menu_title": "My Settings",
      "parent_slug": "options-general.php",
      "slug": "my-settings",
      "fields": [...]
    }
  ]
}
```

### All Field Types Example

```json
{
  "fields": [
    {"name": "text_field", "type": "text", "label": "Text"},
    {"name": "textarea_field", "type": "textarea", "label": "Textarea"},
    {"name": "number_field", "type": "number", "label": "Number", "min": 0, "max": 100},
    {"name": "email_field", "type": "email", "label": "Email"},
    {"name": "url_field", "type": "url", "label": "URL"},
    {"name": "date_field", "type": "date", "label": "Date"},
    {"name": "color_field", "type": "color", "label": "Color"},
    {"name": "password_field", "type": "password", "label": "Password"},
    {
      "name": "select_field",
      "type": "select",
      "label": "Select",
      "options": {
        "option1": "Option 1",
        "option2": "Option 2"
      }
    },
    {
      "name": "radio_field",
      "type": "radio",
      "label": "Radio",
      "options": {
        "yes": "Yes",
        "no": "No"
      }
    },
    {
      "name": "checkbox_field",
      "type": "checkbox",
      "label": "Checkbox"
    }
  ]
}
```

## 🧪 Testing Your JSON

**Before Activating:**

1. **Validate Syntax**: Use [JSONLint.com](https://jsonlint.com)
2. **VS Code**: Install JSON Schema extension
3. **Command Line**: `jq . config.json`
4. **Schema Validation**: Against `wp-cmf/schema.json`

**Common JSON Errors:**
- Missing commas between properties
- Trailing commas (not allowed in JSON)
- Unquoted property names
- Single quotes instead of double quotes

## 📚 Related Examples

- **[03-settings-page-array](../03-settings-page-array/)** - Same using array config
- **[06-complete-json-example](../06-complete-json-example/)** - Advanced JSON with all features

## ❓ Common Questions

**Q: Can I use comments in JSON?**
A: Standard JSON doesn't support comments. Use a README or documentation file instead.

**Q: How do I handle sensitive data?**
A: Don't store sensitive values in JSON. Load them from environment variables in PHP.

```php
// In example.php, after loading JSON
update_option('api_key', getenv('MY_API_KEY'));
```

**Q: Can I combine JSON and array configs?**
A: Yes! Load JSON first, then add more with arrays:

```php
Manager::init()
    ->register_from_json(__DIR__ . '/config.json')
    ->register_from_array($additional_config);
```

**Q: What if JSON parsing fails?**
A: WP-CMF shows an admin notice with the error. Check JSON syntax.

## 💡 Pro Tips

- **Validate Early**: Check JSON before deployment
- **Use Schema**: Reference `wp-cmf/schema.json` in your editor
- **Keep It Simple**: Start with basic config, add complexity gradually
- **Version Control**: Commit config.json to track changes
- **Environment-Specific**: Use different JSON files per environment
- **Documentation**: Add a README explaining your fields

## 🚦 Next Steps

1. Modify `config.json` to add more fields
2. Try adding a submenu page
3. Compare with array version in `03-settings-page-array`
4. Explore comprehensive example in `06-complete-json-example`
5. Read about JSON schema validation in main docs

---

**Questions or Issues?** Check the main README or Field API documentation.
