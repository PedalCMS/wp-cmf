# Add Fields to Existing Post Type (JSON Configuration)

This example demonstrates how to add custom fields to WordPress's built-in **post** post type using JSON configuration.

## Overview

This is the JSON-based version of example 07. It shows how to use an external JSON configuration file to add fields to existing WordPress post types, making it easy to manage configuration across environments or via CI/CD pipelines.

## Features Demonstrated

- ✅ Adding fields to existing WordPress post types via JSON
- ✅ External configuration file management
- ✅ Schema validation for JSON config
- ✅ Multiple field types with validation
- ✅ Error handling for invalid JSON

## JSON Configuration Structure

```json
{
  "cpts": [
    {
      "id": "post",
      "fields": [
        {
          "name": "reading_time",
          "type": "number",
          "label": "Estimated Reading Time",
          ...
        }
      ]
    }
  ]
}
```

**Note:** When the `id` matches an existing WordPress post type (like `post`, `page`, or `attachment`), WP-CMF automatically detects this and only adds fields without creating a new post type.

## Installation

1. Copy this example folder to your WordPress plugins directory
2. Activate the plugin through the WordPress admin panel
3. Edit or create a post to see the custom fields

## Fields Included

The `config.json` file defines 5 custom fields for posts:

1. **Reading Time** - Estimated reading time (1-120 minutes)
2. **Subtitle** - Optional subtitle text (max 200 chars)
3. **Featured Content** - Checkbox to mark as featured
4. **Content Type** - Select from Article, Tutorial, Review, News, Interview
5. **External Source URL** - Link to original source

## Retrieving Field Data

Use standard WordPress functions:

```php
// Get reading time
$reading_time = get_post_meta( get_the_ID(), 'reading_time', true );

// Get subtitle
$subtitle = get_post_meta( get_the_ID(), 'post_subtitle', true );

// Check if featured
$is_featured = get_post_meta( get_the_ID(), 'featured_content', true );
```

## Advantages of JSON Configuration

### 1. **Version Control**
```bash
git diff config.json
# Clear view of configuration changes
```

### 2. **Environment-Specific Configs**
```php
$env = wp_get_environment_type();
$config_file = __DIR__ . "/config.{$env}.json";
```

### 3. **Easy Validation**
JSON schema automatically validates:
- Required fields
- Field types
- Value ranges
- Pattern matching

### 4. **CI/CD Integration**
```yaml
# .github/workflows/deploy.yml
- name: Validate config
  run: jsonlint config.json
```

### 5. **Documentation as Code**
The JSON file serves as living documentation of your field structure.

## Modifying the Configuration

Edit `config.json` to customize fields:

```json
{
  "cpts": [
    {
      "id": "post",
      "fields": [
        {
          "name": "author_bio",
          "type": "textarea",
          "label": "Author Bio",
          "description": "Short bio for this guest author",
          "rows": 5,
          "maxlength": 500
        }
      ]
    }
  ]
}
```

Changes take effect immediately after saving the file.

## Adding Fields to Multiple Post Types

```json
{
  "cpts": [
    {
      "id": "post",
      "fields": [ /* fields for posts */ ]
    },
    {
      "id": "page",
      "fields": [ /* fields for pages */ ]
    }
  ]
}
```

## Schema Validation

The JSON configuration is validated against the WP-CMF schema. Common validation errors:

**Missing required fields:**
```json
{
  "id": "post",
  // ❌ Missing "fields" array (still valid, but no fields will be added)
}
```

**Invalid field type:**
```json
{
  "name": "test",
  "type": "invalid_type"  // ❌ Not a valid field type
}
```

**Out of range values:**
```json
{
  "type": "number",
  "min": 10,
  "max": 5  // ❌ max must be > min
}
```

## Error Handling

The plugin includes comprehensive error handling:

### File Not Found
```
WP-CMF: Configuration file not found at /path/to/config.json
```
**Solution:** Verify the config.json file exists in the plugin folder

### Invalid JSON
```
WP-CMF Error: Invalid JSON: Syntax error
```
**Solution:** Validate JSON syntax using [jsonlint.com](https://jsonlint.com)

### Schema Validation Failed
```
WP-CMF Error: Field 'name' is required
```
**Solution:** Check the error message and fix the configuration

## Environment-Specific Configuration

```php
function existing_post_type_json_init() {
    // Get WordPress environment (dev, staging, production)
    $env = defined( 'WP_ENVIRONMENT_TYPE' ) ? WP_ENVIRONMENT_TYPE : 'production';

    // Load environment-specific config
    $config_file = __DIR__ . "/config.{$env}.json";

    // Fallback to default config
    if ( ! file_exists( $config_file ) ) {
        $config_file = __DIR__ . '/config.json';
    }

    Manager::init()->register_from_json( $config_file );
}
```

Then create:
- `config.dev.json` - Development fields (debugging, test data)
- `config.staging.json` - Staging fields
- `config.production.json` - Production fields
- `config.json` - Default/fallback

## Testing JSON Configuration

### Validate Syntax
```bash
# Using jsonlint
jsonlint config.json

# Using jq
jq empty config.json
```

### Validate Against Schema
```bash
# Using ajv-cli
ajv validate -s ../../schema.json -d config.json
```

### Test in Development
1. Copy example to wp-content/plugins
2. Activate plugin
3. Check for admin notices
4. Edit a post to verify fields appear

## Comparison: JSON vs Array

### Use JSON When:
- ✅ Configuration managed by non-developers
- ✅ Multi-environment deployments
- ✅ Configuration versioned separately
- ✅ Need external validation tools
- ✅ CI/CD pipeline integration

### Use Array When:
- ✅ Dynamic configuration needed
- ✅ Configuration depends on WordPress state
- ✅ Programmatic field generation
- ✅ Complex PHP logic required
- ✅ Simpler for simple cases

## Example: Dynamic Fields Based on User Role

JSON can't do conditional logic, but you can load different configs:

```php
function get_config_for_role() {
    if ( current_user_can( 'administrator' ) ) {
        return __DIR__ . '/config.admin.json';
    }
    return __DIR__ . '/config.editor.json';
}

Manager::init()->register_from_json( get_config_for_role() );
```

## Best Practices

### 1. Always Include Error Handling
```php
try {
    Manager::init()->register_from_json( $config_file );
} catch ( Exception $e ) {
    error_log( 'WP-CMF Error: ' . $e->getMessage() );
}
```

### 2. Validate JSON Before Deployment
```bash
# In your deployment script
if ! jsonlint config.json; then
    echo "Invalid JSON configuration!"
    exit 1
fi
```

### 3. Use Comments in Development
```json
{
  "_comment": "This field is for editorial workflow",
  "name": "editor_notes",
  "type": "textarea"
}
```
Note: JSON doesn't officially support comments, but keys starting with `_` are ignored by WP-CMF.

### 4. Version Your Config
```json
{
  "_version": "1.2.0",
  "_last_updated": "2025-11-11",
  "existing_post_types": [ ... ]
}
```

### 5. Document Field Purpose
```json
{
  "name": "reading_time",
  "description": "Used by theme to display estimated reading time. Calculated manually or via plugin."
}
```

## Troubleshooting

**Changes not appearing?**
- Clear object cache if using Redis/Memcached
- Deactivate and reactivate the plugin
- Check for JSON syntax errors

**Schema validation failing?**
- Ensure all required properties are present
- Check field types match allowed values
- Verify nested structure is correct

**Can't edit JSON in Windows?**
- Save with UTF-8 encoding (no BOM)
- Use LF line endings, not CRLF

## Related Examples

- **[07-existing-post-type-array](../07-existing-post-type-array/)** - Same functionality using array configuration
- **[06-complete-json-example](../06-complete-json-example/)** - Comprehensive JSON configuration
- **[02-basic-cpt-json](../02-basic-cpt-json/)** - Creating new CPT with JSON

## Additional Resources

- [JSON Schema Validation](../../schema.json)
- [Field API Documentation](../../docs/field-api.md)
- [WP-CMF Configuration Guide](../../docs/usage.md)

---

**Part of WP-CMF Examples** | [View All Examples](../README.md)
