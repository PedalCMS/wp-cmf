# Add Fields to Existing WordPress Settings Page (JSON Configuration)

This example demonstrates how to add custom fields directly to WordPress's built-in **General Settings** page (Settings > General) using JSON configuration, following the same pattern as adding fields to existing post types.

## Overview

This example uses JSON configuration to extend WordPress's existing General Settings page with custom fields. This approach offers the benefits of:

- External configuration management
- Version control friendly
- Environment-specific configurations
- CI/CD integration
- Schema validation

## Features Demonstrated

- ✅ Adding fields to existing WordPress settings pages via JSON
- ✅ JSON-based configuration using the same pattern as CPTs
- ✅ Automatic detection of existing settings pages
- ✅ Schema validation for configuration
- ✅ Multiple field types with proper validation
- ✅ No new menu items created - extends existing page

## JSON Configuration Structure

```json
{
  "settings_pages": [
    {
      "id": "general",
      "fields": [
        {
          "name": "site_tagline_extended",
          "type": "text",
          "label": "Extended Tagline",
          "description": "Additional tagline text"
        }
      ]
    }
  ]
}
```

**Note:** When the `id` matches an existing WordPress settings page (like `general`, `writing`, `reading`), WP-CMF automatically detects this and only adds fields. Any `args` properties (like `title`, `menu_title`) will be ignored for existing settings pages.

## Supported WordPress Settings Pages

You can add fields to any WordPress built-in settings page by using the appropriate `id`:

| ID | Settings Page | Access |
|----|---------------|--------|
| `general` | General Settings | Settings > General |
| `writing` | Writing Settings | Settings > Writing |
| `reading` | Reading Settings | Settings > Reading |
| `discussion` | Discussion Settings | Settings > Discussion |
| `media` | Media Settings | Settings > Media |
| `permalink` | Permalink Settings | Settings > Permalinks |
| `privacy` | Privacy Settings | Settings > Privacy |

## Fields Included

This example adds 5 custom fields to the General Settings page:

1. **Extended Tagline** (text) - Additional tagline text
2. **Maintenance Mode** (checkbox) - Enable/disable maintenance mode
3. **Maintenance Message** (textarea) - Custom maintenance message
4. **Contact Email** (email) - Primary contact email
5. **Social Sharing** (checkbox) - Multiple social sharing options

## Installation

1. Copy this example folder to your WordPress plugins directory
2. Ensure `config.json` is present in the same directory as `example.php`
3. Activate the plugin through the WordPress admin panel
4. Navigate to **Settings > General** to see your new fields

## Accessing Your Fields

After activation:
1. Go to WordPress admin
2. Click on **Settings** in the left menu
3. Click on **General**
4. Scroll down to see your custom fields alongside WordPress's built-in fields
5. Configure your settings and click "Save Changes"

## Retrieving Settings Values

Use WordPress options API with the page ID as prefix:

```php
<?php
// Get extended tagline
$extended_tagline = get_option( 'general_site_tagline_extended' );

// Check if maintenance mode is enabled
$maintenance_mode = get_option( 'general_maintenance_mode' );

// Get maintenance message
$maintenance_message = get_option( 'general_maintenance_message' );

// Get contact email
$contact_email = get_option( 'general_contact_email' );

// Get enabled social networks
$social_sharing = get_option( 'general_social_sharing' );
// Returns array: ['facebook', 'twitter', 'linkedin']
?>
```

## Modifying the Configuration

Edit `config.json` to change fields. The configuration follows the same structure as CPT examples:

### Adding Fields to Multiple Settings Pages

```json
{
  "settings_pages": [
    {
      "id": "general",
      "fields": [...]
    },
    {
      "id": "reading",
      "fields": [...]
    }
  ]
}
```

### Mixed Configuration (New and Existing)

```json
{
  "settings_pages": [
    {
      "id": "general",
      "fields": [...]
    },
    {
      "id": "my_plugin_settings",
      "title": "My Plugin Settings",
      "menu_title": "My Plugin",
      "capability": "manage_options",
      "fields": [...]
    }
  ]
}
```

## Schema Validation

The JSON configuration is automatically validated against WP-CMF's schema. Common validation errors:

### Missing Required Fields
```json
{
  "settings_pages": [
    {
      "fields": [...]  // ERROR: Missing "id"
    }
  ]
}
```

**Fix:** Add the `id` property
```json
{
  "settings_pages": [
    {
      "id": "general",
      "fields": [...]
    }
  ]
}
```

### Invalid Field Type
```json
{
  "name": "test_field",
  "type": "invalid_type"  // ERROR: Invalid type
}
```

**Fix:** Use a valid field type (text, email, number, etc.)

## Benefits of JSON Configuration

### Version Control
```bash
# Easy to track changes
git diff config.json
```

### Environment-Specific Settings
```php
// Load different config based on environment
$config_file = WP_ENV === 'production'
    ? 'config.production.json'
    : 'config.development.json';

Manager::init()->register_from_json($config_file);
```

### CI/CD Integration
```yaml
# GitHub Actions example
- name: Validate WP-CMF Config
  run: |
    php validate-config.php config.json
```

### Documentation
JSON schema provides built-in documentation for your configuration structure.

## Comparison: JSON vs Array

### JSON Configuration (This Example)
```json
{
  "settings_pages": [{
    "id": "general",
    "fields": [...]
  }]
}
```

**Pros:**
- ✅ External file, easy to edit
- ✅ Version control friendly
- ✅ Schema validation
- ✅ Non-developers can edit
- ✅ CI/CD integration

### Array Configuration
```php
$config = [
    'settings_pages' => [[
        'id' => 'general',
        'fields' => [...]
    ]]
];
```

**Pros:**
- ✅ Dynamic values (functions, constants)
- ✅ Conditional logic
- ✅ IDE autocomplete
- ✅ No file parsing overhead

## Common Use Cases

### Site Configuration
```json
{
  "settings_pages": [{
    "id": "general",
    "fields": [
      {"name": "api_key", "type": "text", "label": "API Key"},
      {"name": "api_secret", "type": "password", "label": "API Secret"}
    ]
  }]
}
```

### Content Management
```json
{
  "settings_pages": [{
    "id": "writing",
    "fields": [
      {"name": "default_author", "type": "number", "label": "Default Author ID"},
      {"name": "auto_save_interval", "type": "number", "label": "Auto-save Interval (seconds)"}
    ]
  }]
}
```

### Reading Settings
```json
{
  "settings_pages": [{
    "id": "reading",
    "fields": [
      {"name": "excerpt_length", "type": "number", "label": "Custom Excerpt Length"},
      {"name": "show_author_bio", "type": "checkbox", "label": "Show Author Bio"}
    ]
  }]
}
```

## Using Settings in Your Theme/Plugin

### Display Maintenance Mode

```php
<?php
if ( get_option( 'general_maintenance_mode' ) && ! current_user_can( 'administrator' ) ) {
    $message = get_option( 'general_maintenance_message', 'Site under maintenance' );
    wp_die( wp_kses_post( $message ), 'Maintenance Mode' );
}
?>
```

### Display Social Sharing Buttons

```php
<?php
$enabled_networks = get_option( 'general_social_sharing', [] );

if ( in_array( 'facebook', $enabled_networks, true ) ) {
    echo '<a href="https://facebook.com/sharer.php?u=' . esc_url( get_permalink() ) . '">Share on Facebook</a>';
}

if ( in_array( 'twitter', $enabled_networks, true ) ) {
    echo '<a href="https://twitter.com/intent/tweet?url=' . esc_url( get_permalink() ) . '">Share on Twitter</a>';
}
?>
```

## Advanced Patterns

### Multiple Configuration Files

```php
function load_wp_cmf_configs() {
    $manager = Manager::init();

    // Load base configuration
    $manager->register_from_json(__DIR__ . '/config/base.json');

    // Load environment-specific overrides
    if (defined('WP_ENV') && file_exists(__DIR__ . '/config/' . WP_ENV . '.json')) {
        $manager->register_from_json(__DIR__ . '/config/' . WP_ENV . '.json');
    }
}
```

### Configuration with Fallback

```php
function safe_load_config() {
    $config_file = __DIR__ . '/config.json';

    if (!file_exists($config_file)) {
        // Fallback to array configuration
        Manager::init()->register_from_array([
            'settings_pages' => [
                ['id' => 'general', 'fields' => [...]]
            ]
        ]);
        return;
    }

    try {
        Manager::init()->register_from_json($config_file);
    } catch (Exception $e) {
        error_log('WP-CMF Config Error: ' . $e->getMessage());
    }
}
```

## When to Use This Approach

- ✅ Configuration managed by non-developers
- ✅ Settings that don't require dynamic PHP logic
- ✅ Environment-specific configurations
- ✅ CI/CD pipelines with validation
- ✅ Documentation through JSON schema

## When to Use Array Configuration Instead

- ❌ Need dynamic values (current_time(), etc.)
- ❌ Conditional field definitions
- ❌ Complex PHP logic in configuration
- ❌ Runtime-generated settings

## Related Examples

- **[09-existing-settings-page-array](../09-existing-settings-page-array/)** - Same functionality using array configuration
- **[08-existing-post-type-json](../08-existing-post-type-json/)** - Similar pattern for adding fields to existing post types
- **[04-settings-page-json](../04-settings-page-json/)** - Creating a new settings page with JSON

## Troubleshooting

**JSON parsing errors?**
- Validate your JSON: https://jsonlint.com
- Check for trailing commas (not allowed in JSON)
- Ensure proper escaping of quotes in strings

**Fields not showing?**
- Verify `config.json` is in the correct directory
- Check the `id` matches exactly: 'general', 'writing', etc.
- Look for error messages in admin notices

**Schema validation failures?**
- Ensure all required properties are present
- Check field types are valid
- Verify options format for select/checkbox/radio fields

**Values not saving?**
- Check user permissions (`manage_options` capability)
- Verify field names don't contain invalid characters
- Look for PHP errors in debug log

## Support

For issues or questions about this example:
- Check the [WP-CMF Documentation](../../docs/)
- Review the [JSON Schema](../../schema.json)
- Open an issue on GitHub

---

**Part of WP-CMF Examples** | [View All Examples](../README.md)
