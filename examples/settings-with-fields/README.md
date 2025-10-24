# Settings Page with Fields Example

This example demonstrates a **production-ready settings page** with comprehensive field usage, organized sections, and proper WordPress integration.

## Features

### 🎯 Complete Implementation
- **20+ Fields** across 4 organized sections
- **All 11 Field Types** demonstrated (text, email, textarea, select, checkbox, radio, color, number, url, password)
- **WordPress Settings API** integration
- **Field Validation** and sanitization
- **Section Organization** with descriptions
- **Settings Export/Import** functionality (demo)
- **Custom Rendering** with styled output
- **Nonce Security** for form submissions

### 📋 Field Sections

#### 1. General Settings
- **Site Name** (text, required)
- **Tagline** (text with maxlength)
- **Welcome Message** (textarea)
- **Enable Features** (multiple checkboxes)
- **Default Language** (select dropdown)

#### 2. Appearance Settings
- **Theme Style** (radio buttons)
- **Primary Color** (color picker)
- **Secondary Color** (color picker)
- **Items Per Page** (number with min/max)
- **Enable Animations** (single checkbox)

#### 3. Email Settings
- **Admin Email** (email, required)
- **Support Email** (email)
- **Email Subject Prefix** (text)
- **Email Template** (select dropdown)
- **Email Footer** (textarea)

#### 4. Advanced Settings
- **API Key** (password field)
- **API Endpoint** (URL with validation)
- **Cache Duration** (number in seconds)
- **Debug Mode** (checkbox)
- **Data Retention** (number in days)

## Code Structure

```php
// 1. Initialize WP-CMF
$manager = Manager::init();
$registrar = $manager->get_registrar();

// 2. Register settings page
add_action('admin_menu', function() use ($registrar) {
    $registrar->add_settings_page([
        'id'    => 'my-plugin-settings',
        'title' => 'My Plugin Settings',
        // ... more config
    ]);
});

// 3. Register fields in admin_init
add_action('admin_init', function() use ($registrar) {
    register_settings_sections();
    register_general_fields($registrar);
    register_appearance_fields($registrar);
    // ... more sections
});

// 4. Create fields with FieldFactory
$fields = FieldFactory::create_multiple([
    'site_name' => [
        'type'     => 'text',
        'label'    => 'Site Name',
        'required' => true,
    ],
    // ... more fields
]);

// 5. Register with WordPress Settings API
foreach ($fields as $field) {
    register_setting('my-plugin-settings', $field->get_name());
    add_settings_field(
        $field->get_name(),
        $field->get_label(),
        function() use ($field) {
            $value = get_option($field->get_name());
            echo $field->render($value);
        },
        'my-plugin-settings',
        'general_section'
    );
}
```

## Field Configuration Examples

### Text Field with Validation
```php
'site_name' => [
    'type'        => 'text',
    'label'       => 'Site Name',
    'description' => 'The name of your site',
    'default'     => get_bloginfo('name'),
    'required'    => true,
    'placeholder' => 'Enter site name',
],
```

### Multiple Checkboxes
```php
'enable_features' => [
    'type'    => 'checkbox',
    'label'   => 'Enable Features',
    'options' => [
        'comments'      => 'Enable Comments',
        'social_share'  => 'Enable Social Sharing',
        'analytics'     => 'Enable Analytics',
        'notifications' => 'Enable Email Notifications',
    ],
    'layout'  => 'stacked',
],
```

### Color Picker
```php
'primary_color' => [
    'type'        => 'color',
    'label'       => 'Primary Color',
    'description' => 'Main color used throughout the plugin',
    'default'     => '#3498db',
],
```

### Number with Range
```php
'items_per_page' => [
    'type'    => 'number',
    'label'   => 'Items Per Page',
    'min'     => 5,
    'max'     => 100,
    'step'    => 5,
    'default' => 10,
],
```

### Radio Buttons
```php
'theme_style' => [
    'type'    => 'radio',
    'label'   => 'Theme Style',
    'options' => [
        'light'  => 'Light',
        'dark'   => 'Dark',
        'auto'   => 'Auto (based on system)',
        'custom' => 'Custom',
    ],
    'default' => 'light',
    'layout'  => 'inline',
],
```

## WordPress Settings API Integration

### Register Setting
```php
register_setting('my-plugin-settings', $field_name);
```

### Add Settings Section
```php
add_settings_section(
    'general_section',
    'General Settings',
    function() {
        echo '<p>Configure general plugin settings.</p>';
    },
    'my-plugin-settings'
);
```

### Add Settings Field
```php
add_settings_field(
    $field_name,
    $field->get_label(),
    function() use ($field, $field_name) {
        $value = get_option($field_name, $field->get_config('default'));
        echo $field->render($value);
    },
    'my-plugin-settings',
    'general_section'
);
```

### Render Settings Page
```php
<form method="post" action="options.php">
    <?php
    settings_fields('my-plugin-settings');
    do_settings_sections('my-plugin-settings');
    submit_button('Save Settings');
    ?>
</form>
```

## Saving and Retrieving Values

### Get Option Value
```php
$site_name = get_option('site_name', 'Default Name');
```

### Update Option
```php
update_option('site_name', 'New Site Name');
```

### Delete Option
```php
delete_option('site_name');
```

## Security Features

### Nonce Verification
```php
if (isset($_POST['my_plugin_settings_nonce'])) {
    if (!wp_verify_nonce($_POST['my_plugin_settings_nonce'], 'my_plugin_save_settings')) {
        wp_die(__('Security check failed.', 'my-plugin'));
    }
}
```

### Capability Check
```php
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions.', 'my-plugin'));
}
```

### Field Sanitization
All fields automatically sanitize input through their `sanitize()` methods:
- **Text fields**: `sanitize_text_field()`
- **Email fields**: `sanitize_email()`
- **URL fields**: `esc_url_raw()`
- **Number fields**: Type casting and range validation
- **Checkboxes**: Only allowed values

## Advanced Features

### Export Settings
```javascript
function exportSettings() {
    var settings = {
        site_name: '<?php echo get_option('site_name'); ?>',
        // ... collect all settings
    };

    // Create JSON download
    var dataStr = "data:text/json;charset=utf-8," +
                  encodeURIComponent(JSON.stringify(settings, null, 2));
    var downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", "settings.json");
    downloadAnchor.click();
}
```

### Default Values
Use WordPress defaults or custom values:
```php
'default' => get_bloginfo('name'),    // WordPress default
'default' => 'Custom Value',          // Custom default
'default' => get_option('admin_email'), // Another option
```

### Conditional Fields
Show/hide fields based on other values:
```php
// In JavaScript
jQuery(document).ready(function($) {
    $('input[name="debug_mode"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.debug-options').show();
        } else {
            $('.debug-options').hide();
        }
    });
});
```

## Customization

### Custom Section Descriptions
```php
add_settings_section(
    'section_id',
    'Section Title',
    function() {
        echo '<div class="section-description">';
        echo '<p>Your custom HTML here</p>';
        echo '<a href="#">Learn more</a>';
        echo '</div>';
    },
    'my-plugin-settings'
);
```

### Custom Field Rendering
```php
add_settings_field(
    $field_name,
    $field->get_label(),
    function() use ($field, $field_name) {
        $value = get_option($field_name);

        // Custom wrapper
        echo '<div class="custom-field-wrapper">';
        echo $field->render($value);
        echo '<span class="help-icon">?</span>';
        echo '</div>';
    },
    'my-plugin-settings',
    'section_name'
);
```

### Custom Styling
```css
.form-table th {
    width: 250px;
    font-weight: 600;
}

.form-table td p.description {
    margin-top: 5px;
    font-style: italic;
    color: #646970;
}

.settings-info {
    margin-top: 30px;
    padding: 20px;
    background: #f0f0f1;
    border-left: 4px solid #2271b1;
}
```

## Usage in Your Plugin

1. **Copy the example code** to your plugin
2. **Customize the fields** for your needs
3. **Update the capability** if needed (`manage_options`, custom capability, etc.)
4. **Add your custom logic** for processing settings
5. **Style the page** to match your plugin's design

## Testing

```php
// Get all settings
$site_name = get_option('site_name');
$primary_color = get_option('primary_color');
$enable_features = get_option('enable_features', []);

// Use in your plugin
if (in_array('comments', $enable_features)) {
    // Enable comments feature
}

// Apply theme color
echo '<style>:root { --primary-color: ' . esc_attr($primary_color) . '; }</style>';
```

## Related Examples

- **[Settings Page Basic](../settings-page-basic/)** - Simple settings page
- **[Settings Page Submenu](../settings-page-submenu/)** - Submenu pages
- **[Field Factory Usage](../field-factory-usage/)** - FieldFactory patterns
- **[Field Custom Assets](../field-custom-assets/)** - Custom field assets

## Best Practices

1. ✅ **Use WordPress Settings API** for automatic saving
2. ✅ **Group related fields** into sections
3. ✅ **Provide default values** for all fields
4. ✅ **Add descriptions** to help users
5. ✅ **Validate and sanitize** all input
6. ✅ **Check capabilities** before showing settings
7. ✅ **Use nonces** for security
8. ✅ **Make fields accessible** with proper labels
9. ✅ **Test with different roles** (admin, editor, etc.)
10. ✅ **Document your settings** in code comments

## Next Steps

- Add **field validation** with custom rules
- Implement **settings import** functionality
- Add **reset to defaults** button
- Create **settings backup/restore**
- Add **field dependencies** (conditional display)
- Implement **settings versioning**
