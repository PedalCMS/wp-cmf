# Complete JSON Configuration Example

This is the most comprehensive JSON-based example demonstrating **all 11 WP-CMF field types** with 2 Custom Post Types and 2 Settings Pages.

## 📋 What This Example Shows

- ✅ **2 Custom Post Types** (Products & Events) configured in JSON
- ✅ **2 Settings Pages** (Shop Settings & Event Management) configured in JSON
- ✅ **All 11 Core Field Types** demonstrated
- ✅ **JSON Schema Validation** in action
- ✅ **Clean separation** of configuration and code
- ✅ **Production-ready** JSON structure
- ✅ **Environment-friendly** configuration management

## 🎯 What's Included

### Custom Post Types

#### 1. Product CPT
**11 Fields demonstrating all field types:**
1. **SKU** (text) - Required, high priority
2. **Detailed Description** (textarea) - 8 rows
3. **Price** (number) - Min/max/step
4. **Category** (select) - 6 options
5. **Condition** (radio) - 4 options
6. **In Stock** (checkbox) - Boolean
7. **Supplier Email** (email) - Validated
8. **Product URL** (url) - External link
9. **Release Date** (date) - Date range
10. **Admin Access Code** (password) - Secured
11. **Primary Color** (color) - Color picker

#### 2. Event CPT
**5 Essential Fields:**
- Event Date (date) - Required
- Location (text) - Required
- Max Attendees (number)
- Registration URL (url)
- Contact Email (email)

### Settings Pages

#### 1. Shop Settings
**11 Fields (all types):**
- Store Name (text) - Required
- Store Description (textarea)
- Enable Shopping Cart (checkbox)
- Currency (select)
- Primary Payment Method (radio)
- Support Email (email) - Required
- Store URL (url)
- Brand Color (color)
- API Key (password)
- Maximum Order Amount (number)
- Next Sale Start Date (date)

#### 2. Event Management Settings
**4 Configuration Fields:**
- Enable Events (checkbox)
- Default Duration (number)
- Notification Email (email)
- Event Page Color (color)

## 📁 Files

- `config.json` - Complete JSON configuration (300+ lines)
- `example.php` - Minimal PHP file that loads JSON
- `README.md` - This comprehensive documentation

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Activate the plugin in WordPress admin
3. The plugin reads `config.json` and registers everything automatically
4. You'll see:
   - "Products" CPT in admin menu
   - "Events" CPT in admin menu
   - "Shop Settings" in admin menu
   - "Events Config" in admin menu

### Configuration File Structure

**config.json Overview:**
```json
{
  "cpts": [
    {
      "id": "product",
      "args": { ... },
      "fields": [ ... ]
    },
    {
      "id": "event",
      "args": { ... },
      "fields": [ ... ]
    }
  ],
  "settings_pages": [
    {
      "id": "shop_settings",
      "fields": [ ... ]
    },
    {
      "id": "event_settings",
      "fields": [ ... ]
    }
  ]
}
```

## 💻 JSON Configuration Benefits

### 1. Clean Separation ✅
- Configuration completely separate from code
- Non-developers can edit JSON
- Easy to understand structure
- No PHP knowledge required

### 2. Schema Validation ✅
- Automatic validation against WP-CMF schema
- Catches errors before they cause problems
- Detailed error messages
- Type checking and constraint validation

### 3. Version Control ✅
```bash
git diff config.json
# See exactly what changed
```

### 4. Environment Management ✅
```php
// Different configs per environment
$env = wp_get_environment_type();
$config = __DIR__ . "/config-{$env}.json";
Manager::init()->register_from_json($config);
```

### 5. CI/CD Friendly ✅
- Deploy different configs automatically
- Test configs in pipelines
- Swap configurations without code changes
- Easy config rollback

### 6. Multi-Site Ready ✅
```php
// Different config per site
$site_id = get_current_blog_id();
$config = __DIR__ . "/config-site-{$site_id}.json";
Manager::init()->register_from_json($config);
```

## 🔍 All 11 Field Types in JSON

### 1. Text Field
```json
{
  "name": "sku",
  "type": "text",
  "label": "Product SKU",
  "placeholder": "PROD-001",
  "required": true
}
```

### 2. Textarea Field
```json
{
  "name": "description",
  "type": "textarea",
  "label": "Detailed Description",
  "rows": 8,
  "cols": 50
}
```

### 3. Number Field
```json
{
  "name": "price",
  "type": "number",
  "label": "Price",
  "min": 0,
  "max": 99999,
  "step": 0.01
}
```

### 4. Select Field
```json
{
  "name": "category",
  "type": "select",
  "label": "Product Category",
  "options": {
    "electronics": "Electronics",
    "clothing": "Clothing"
  }
}
```

### 5. Radio Field
```json
{
  "name": "condition",
  "type": "radio",
  "label": "Product Condition",
  "options": {
    "new": "Brand New",
    "used": "Used"
  }
}
```

### 6. Checkbox Field
```json
{
  "name": "in_stock",
  "type": "checkbox",
  "label": "In Stock",
  "default": true
}
```

### 7. Email Field
```json
{
  "name": "supplier_email",
  "type": "email",
  "label": "Supplier Email",
  "placeholder": "supplier@example.com"
}
```

### 8. URL Field
```json
{
  "name": "product_url",
  "type": "url",
  "label": "External Product URL",
  "placeholder": "https://example.com"
}
```

### 9. Date Field
```json
{
  "name": "release_date",
  "type": "date",
  "label": "Release Date",
  "min": "2020-01-01",
  "max": "2030-12-31"
}
```

### 10. Password Field
```json
{
  "name": "admin_access_code",
  "type": "password",
  "label": "Admin Access Code"
}
```

### 11. Color Field
```json
{
  "name": "primary_color",
  "type": "color",
  "label": "Primary Color",
  "default": "#FF5733"
}
```

## 🧪 JSON Schema Validation

### Automatic Validation

WP-CMF validates your JSON against the built-in schema (`wp-cmf/schema.json`):

**What Gets Validated:**
- ✅ Required properties (id, name, type)
- ✅ Field type validity (text, number, email, etc.)
- ✅ Options presence for select/radio/checkbox
- ✅ Field name length (max 64 characters)
- ✅ CPT id format (lowercase, max 20 chars)
- ✅ Number constraints (min ≤ max)
- ✅ Date format (YYYY-MM-DD)
- ✅ Color format (#RRGGBB)
- ✅ And much more...

### Example Validation Errors

**Missing Options:**
```
WP-CMF Error: JSON validation failed:
- cpts[0].fields[3] field type 'select' requires 'options' property
```

**Invalid Field Name:**
```
WP-CMF Error: JSON validation failed:
- cpts[0].fields[0].name exceeds maximum length of 64 characters
```

**Invalid CPT ID:**
```
WP-CMF Error: JSON validation failed:
- cpts[0].id must be lowercase letters/underscores, max 20 chars
```

## 🎨 Advanced JSON Patterns

### Environment-Specific Configs

**config-production.json:**
```json
{
  "settings_pages": [{
    "id": "shop_settings",
    "fields": [{
      "name": "api_key",
      "type": "password",
      "label": "Production API Key"
    }]
  }]
}
```

**config-development.json:**
```json
{
  "settings_pages": [{
    "id": "shop_settings",
    "fields": [{
      "name": "api_key",
      "type": "text",
      "label": "Dev API Key (visible)"
    }]
  }]
}
```

**Load Based on Environment:**
```php
function complete_json_example_init() {
    $env = wp_get_environment_type(); // 'production', 'development', 'staging'
    $config_file = __DIR__ . "/config-{$env}.json";

    // Fallback to default
    if (!file_exists($config_file)) {
        $config_file = __DIR__ . '/config.json';
    }

    Manager::init()->register_from_json($config_file);
}
```

### Multiple JSON Files

**Organize by Feature:**
```
/plugin-folder
├── config-cpts.json        # All CPTs
├── config-settings.json    # All settings pages
├── config-fields.json      # Shared field definitions
└── example.php
```

**Load All:**
```php
function complete_json_example_init() {
    $manager = Manager::init();

    // Load CPTs
    $manager->register_from_json(__DIR__ . '/config-cpts.json');

    // Load Settings
    $manager->register_from_json(__DIR__ . '/config-settings.json');
}
```

### Conditional Loading

**Based on User Role:**
```php
function complete_json_example_init() {
    $manager = Manager::init();

    // Basic config for all users
    $manager->register_from_json(__DIR__ . '/config-basic.json');

    // Advanced config for administrators
    if (current_user_can('manage_options')) {
        $manager->register_from_json(__DIR__ . '/config-advanced.json');
    }
}
```

## 🔍 Retrieving Data

### CPT Fields (Post Meta)

```php
// Get product data
$product_data = [
    'sku' => get_post_meta($post_id, 'sku', true),
    'price' => get_post_meta($post_id, 'price', true),
    'category' => get_post_meta($post_id, 'category', true),
    'in_stock' => get_post_meta($post_id, 'in_stock', true),
    'color' => get_post_meta($post_id, 'primary_color', true),
];

// Display in template
$price = get_post_meta($post_id, 'price', true);
echo '$' . number_format((float)$price, 2);
```

### Settings Fields (Options)

```php
// Get shop settings
$shop_settings = [
    'name' => get_option('store_name'),
    'email' => get_option('support_email'),
    'currency' => get_option('currency', 'USD'),
    'color' => get_option('brand_color', '#E74C3C'),
];

// Use in templates
$brand_color = get_option('brand_color', '#E74C3C');
echo '<header style="background: ' . esc_attr($brand_color) . '">';
```

## 🎯 Shortcodes

```
[product_info]
[product_info id="123"]
[event_info]
[event_info id="456"]
```

## 🧪 Testing & Validation

### Before Deployment

1. **Validate JSON Syntax:**
```bash
jq . config.json
# or use JSONLint.com
```

2. **Validate Against Schema:**
```bash
# Use ajv-cli or online validators
ajv validate -s wp-cmf/schema.json -d config.json
```

3. **VS Code Setup:**
Add to your `config.json`:
```json
{
  "$schema": "../../schema.json",
  "cpts": [...]
}
```
VS Code will validate as you type!

### Common JSON Errors

**Trailing Comma:**
```json
{
  "name": "field1",
  "type": "text",  ← Remove this comma
}
```

**Missing Comma:**
```json
{
  "name": "field1"
  "type": "text"  ← Add comma here
}
```

**Single Quotes:**
```json
{
  'name': 'field1'  ← Use double quotes
}
```

## 📊 Comparison: Array vs JSON

### Use JSON When:
- ✅ You want config separate from code
- ✅ Non-developers will edit configuration
- ✅ You need environment-specific configs
- ✅ You have CI/CD pipelines
- ✅ You want schema validation
- ✅ You need version control clarity

### Use Array When:
- ✅ You need dynamic PHP logic
- ✅ You want IDE autocomplete in PHP
- ✅ Configuration is simple and rarely changes
- ✅ You need computed values
- ✅ You prefer everything in one file

### Both Approaches Work!
```php
// You can even mix them!
Manager::init()
    ->register_from_json(__DIR__ . '/config.json')
    ->register_from_array($additional_config);
```

## 📚 Related Examples

- **[05-complete-array-example](../05-complete-array-example/)** - Same features in array format
- **[02-basic-cpt-json](../02-basic-cpt-json/)** - Simple JSON introduction
- **[04-settings-page-json](../04-settings-page-json/)** - Simple settings in JSON

## ❓ Common Questions

**Q: Can I add comments to JSON?**
A: Standard JSON doesn't support comments. Use a separate README or documentation file.

**Q: What if I have sensitive data?**
A: Don't put sensitive data in JSON. Load from environment variables:
```php
// After loading JSON
update_option('api_key', getenv('PRODUCTION_API_KEY'));
```

**Q: How do I handle different data types?**
A: JSON supports strings, numbers, booleans, arrays, and objects. WP-CMF handles conversion.

**Q: Can I validate before activation?**
A: Yes! WP-CMF validates on load and shows admin notices for errors.

**Q: What's the performance impact?**
A: Minimal. JSON is parsed once on init. Consider caching for very large configs.

## 💡 Pro Tips

1. **Use Schema Validation**: Set up VS Code with JSON Schema for real-time validation
2. **Start Small**: Begin with basic config, add complexity gradually
3. **Version Control**: Commit config.json, track changes with git
4. **Environment Vars**: Use for sensitive data, not JSON
5. **Pretty Print**: Keep JSON formatted for readability
6. **Test Locally**: Always test JSON changes in development first
7. **Backup Configs**: Keep copies of working configurations
8. **Document Changes**: Use git commit messages to explain config changes
9. **Lint Before Deploy**: Run through JSON validator before deployment
10. **Schema Reference**: Keep `wp-cmf/schema.json` handy for reference

## 🚦 Next Steps

1. Study the `config.json` file structure
2. Try modifying field configurations
3. Add more CPTs or settings pages to JSON
4. Test schema validation with invalid JSON
5. Compare with array version in `05-complete-array-example`
6. Set up environment-specific configs
7. Integrate into your CI/CD pipeline

---

**Questions or Issues?** This is the most complete JSON example available. Check the main README, Field API documentation, and `schema.json` for additional details.
