# Settings Page with Array Configuration

This example demonstrates how to create a WordPress settings page with fields using array-based configuration.

## 📋 What This Example Shows

- ✅ Create a top-level settings page in WordPress admin
- ✅ Add 5 different field types to the settings page
- ✅ WordPress Settings API integration
- ✅ Save and retrieve settings values
- ✅ Display settings on frontend via shortcode

## 🎯 Features

**Settings Page:** My Plugin Settings
- Top-level menu item in WordPress admin
- Custom icon and position in admin menu
- Restricted to users with `manage_options` capability

**Fields Included:**
1. **Site Title** - Text field (required)
2. **Site Description** - Textarea for longer text
3. **Enable Feature** - Checkbox for on/off toggle
4. **Theme Color** - Color picker with default color
5. **Contact Email** - Email field with validation (required)
6. **Welcome Message** - WYSIWYG rich text editor
7. **License Key** - Text field with `use_name_prefix: false`


## 📁 Files

- `example.php` - Main plugin file with array configuration
- `README.md` - This documentation file

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Rename it to your plugin name (e.g., `my-settings-plugin`)
3. Activate the plugin in WordPress admin

### Accessing the Settings Page

After activation:
1. Go to WordPress admin
2. Look for "My Plugin" in the main admin menu
3. Click to open the settings page
4. Fill in the fields
5. Click "Save Changes"

## 💻 Code Breakdown

### Configuration Array Structure

```php
$config = [
    'settings_pages' => [
        [
            'id' => 'my_plugin_settings',      // Unique identifier
            'title' => 'My Plugin Settings',   // Page title
            'menu_title' => 'My Plugin',       // Menu label
            'capability' => 'manage_options',  // Required capability
            'slug' => 'my-plugin-settings',    // URL slug
            'icon' => 'dashicons-admin-generic', // Menu icon
            'position' => 80,                  // Menu position
            'fields' => [
                // Field configurations...
            ]
        ]
    ]
];
```

### Registration

```php
Manager::init()->register_from_array($config);
```

WP-CMF handles:
- Settings page registration
- WordPress Settings API integration
- Field rendering in admin
- Data sanitization and validation
- Saving to WordPress options table

## 🔍 Retrieving Settings Data

### Get Individual Options

```php
// Get specific option
$site_title = get_option('site_title', 'Default Title');

// Get with custom helper function
function get_my_plugin_option($option_name, $default = '') {
    return get_option($option_name, $default);
}

$title = get_my_plugin_option('site_title');
```

### Use in Templates

```php
<?php
// Get settings values
$title = get_option('site_title', 'My Site');
$color = get_option('theme_color', '#0073aa');
$email = get_option('contact_email');

// Display
echo '<h1 style="color: ' . esc_attr($color) . '">' . esc_html($title) . '</h1>';

if ($email) {
    echo '<a href="mailto:' . esc_attr($email) . '">Contact Us</a>';
}
?>
```

### Shortcode Usage

This example includes a shortcode to display settings:

```
[my_plugin_settings]
```

Add it to any post or page to display the configured settings.

## 🎨 Customization Tips

### Add More Fields

Simply add more field arrays:

```php
'fields' => [
    // ... existing fields
    [
        'name' => 'logo_url',
        'type' => 'url',
        'label' => 'Logo URL',
        'description' => 'URL to your logo image'
    ]
]
```

### Change Settings Page Type

Create a submenu page instead of top-level:

```php
[
    'id' => 'my_plugin_settings',
    'title' => 'My Plugin Settings',
    'menu_title' => 'My Plugin',
    'parent_slug' => 'options-general.php',  // Add under Settings
    'capability' => 'manage_options',
    'slug' => 'my-plugin-settings',
    'fields' => [...]
]
```

**Common parent_slug values:**
- `options-general.php` - Settings menu
- `tools.php` - Tools menu
- `themes.php` - Appearance menu
- `plugins.php` - Plugins menu
- `users.php` - Users menu

### Add Field Validation

```php
[
    'name' => 'contact_email',
    'type' => 'email',
    'label' => 'Contact Email',
    'required' => true,
    'validation' => [
        'email' => true  // Ensures valid email format
    ]
]
```

### Organize Fields into Sections

Use description fields to create visual sections:

```php
'fields' => [
    // General Settings Section
    [
        'name' => 'site_title',
        'type' => 'text',
        'label' => 'Site Title'
    ],
    [
        'name' => 'site_description',
        'type' => 'textarea',
        'label' => 'Description'
    ],

    // Appearance Section
    [
        'name' => 'theme_color',
        'type' => 'color',
        'label' => 'Theme Color'
    ]
]
```

## 📊 Data Storage

Settings are stored in the WordPress `wp_options` table:

```sql
-- Fields with use_name_prefix: true (default)
SELECT * FROM wp_options
WHERE option_name IN (
    'my_plugin_settings_site_title',
    'my_plugin_settings_theme_color',
    'my_plugin_settings_contact_email',
    'my_plugin_settings_welcome_message'
);

-- Fields with use_name_prefix: false
SELECT * FROM wp_options WHERE option_name = 'license_key';
```

Each field is stored as a separate option. The naming convention depends on `use_name_prefix`.

## 🧩 Integration Examples

### Use in Theme Header

```php
// themes/your-theme/header.php
<?php
$site_title = get_option('site_title', get_bloginfo('name'));
$theme_color = get_option('theme_color', '#0073aa');
?>
<header style="background-color: <?php echo esc_attr($theme_color); ?>">
    <h1><?php echo esc_html($site_title); ?></h1>
</header>
```

### Use in Widget

```php
class My_Plugin_Widget extends WP_Widget {
    public function widget($args, $instance) {
        $title = get_option('site_title');
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        echo $args['after_widget'];
    }
}
```

### Use in REST API

```php
add_action('rest_api_init', function() {
    register_rest_route('my-plugin/v1', '/settings', [
        'methods' => 'GET',
        'callback' => function() {
            return [
                'title' => get_option('site_title'),
                'color' => get_option('theme_color'),
                'email' => get_option('contact_email'),
            ];
        },
        'permission_callback' => '__return_true'
    ]);
});
```

## 📚 Related Examples

- **[04-settings-page-json](../04-settings-page-json/)** - Same example using JSON configuration
- **[05-complete-array-example](../05-complete-array-example/)** - Advanced example with all field types

## ❓ Common Questions

**Q: Where are settings stored?**
A: In the `wp_options` table, each field as a separate option.

**Q: Can I create multiple settings pages?**
A: Yes! Add more arrays to the `settings_pages` array.

**Q: How do I reset settings to defaults?**
A: Delete the options from the database or add a "Reset" button in your settings page.

**Q: Can I export/import settings?**
A: Yes, you can create custom functions to export options to JSON and import them back.

**Q: How do I add custom validation?**
A: Use the `validation` property in field configuration or add WordPress filters.

## 🚦 Next Steps

1. Try modifying the field configuration
2. Add more fields to the settings page
3. Create a submenu page under Settings
4. Check out the JSON version in `04-settings-page-json`
5. Explore the comprehensive example in `05-complete-array-example`

## 💡 Pro Tips

- **Capability Check**: Always use appropriate capabilities (`manage_options` for settings)
- **Sanitization**: WP-CMF handles it, but you can add custom sanitizers
- **Default Values**: Always provide defaults when getting options
- **Option Names**: Use unique prefixes to avoid conflicts
- **Cache**: Options are cached by WordPress, so they're fast to retrieve

---

**Questions or Issues?** Check the main README or Field API documentation.
