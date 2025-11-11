# Add Fields to Existing WordPress Settings Page (Array Configuration)

This example demonstrates how to add custom fields directly to WordPress's built-in **General Settings** page (Settings > General) using array configuration, following the same pattern as adding fields to existing post types.

## Overview

Instead of creating a new settings page, this example extends WordPress's existing General Settings page by adding custom fields directly to it. This is useful when you want to:

- Add site-wide configuration options alongside WordPress's built-in settings
- Keep all general settings in one place for users
- Avoid creating additional menu items
- Provide a seamless, native WordPress experience

## Features Demonstrated

- ✅ Adding fields to existing WordPress settings pages
- ✅ Array-based configuration using the same pattern as CPTs
- ✅ Automatic detection of existing settings pages
- ✅ Multiple field types (text, textarea, email, checkbox)
- ✅ Field validation and sanitization
- ✅ No new menu items created - extends existing page

## Configuration Structure

```php
$config = [
    'settings_pages' => [
        [
            'id'     => 'general',  // Existing WordPress settings page
            'fields' => [           // Array of field definitions
                // ... field configurations
            ],
        ],
    ],
];
```

**Note:** When the `id` matches an existing WordPress settings page (like `general`, `writing`, `reading`), WP-CMF automatically detects this and only adds fields without attempting to create a new settings page. If you include `args` (like `title`, `menu_title`, etc.), they will be ignored for existing settings pages.

## Supported WordPress Settings Pages

You can add fields to any WordPress built-in settings page by using the appropriate `id`:

### General Settings
```php
['id' => 'general', 'fields' => [...]]
```
**Access:** Settings > General

### Writing Settings
```php
['id' => 'writing', 'fields' => [...]]
```
**Access:** Settings > Writing

### Reading Settings
```php
['id' => 'reading', 'fields' => [...]]
```
**Access:** Settings > Reading

### Discussion Settings
```php
['id' => 'discussion', 'fields' => [...]]
```
**Access:** Settings > Discussion

### Media Settings
```php
['id' => 'media', 'fields' => [...]]
```
**Access:** Settings > Media

### Permalink Settings
```php
['id' => 'permalink', 'fields' => [...]]
```
**Access:** Settings > Permalinks

### Privacy Settings
```php
['id' => 'privacy', 'fields' => [...]]
```
**Access:** Settings > Privacy

## Fields Included

This example adds 5 custom fields to the General Settings page:

1. **Extended Tagline** (text) - Additional tagline text
2. **Maintenance Mode** (checkbox) - Enable/disable maintenance mode
3. **Maintenance Message** (textarea) - Custom maintenance message
4. **Contact Email** (email) - Primary contact email
5. **Social Sharing** (checkbox) - Multiple social sharing options

## Installation

1. Copy this example folder to your WordPress plugins directory
2. Activate the plugin through the WordPress admin panel
3. Navigate to **Settings > General** to see your new fields

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

**Note:** Option names are prefixed with `general_` because these are stored in the general settings group.

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

### Use Extended Tagline

```php
<?php
// In your theme's header.php
$tagline = get_bloginfo( 'description' );
$extended = get_option( 'general_site_tagline_extended' );

if ( $extended ) {
    echo '<p class="site-description">' . esc_html( $tagline . ' - ' . $extended ) . '</p>';
}
?>
```

## Example: Adding Fields to Multiple Existing Settings Pages

You can add fields to multiple existing settings pages in one configuration:

```php
$config = [
    'settings_pages' => [
        [
            'id'     => 'general',
            'fields' => [ /* fields for General Settings */ ],
        ],
        [
            'id'     => 'reading',
            'fields' => [ /* fields for Reading Settings */ ],
        ],
        [
            'id'     => 'writing',
            'fields' => [ /* fields for Writing Settings */ ],
        ],
    ],
];
```

## Example: Mixed Configuration (New and Existing)

You can mix new settings pages with existing ones:

```php
$config = [
    'settings_pages' => [
        // Add fields to existing General Settings
        [
            'id'     => 'general',
            'fields' => [ /* fields */ ],
        ],
        // Create new custom settings page
        [
            'id'         => 'my_plugin_settings',
            'title'      => 'My Plugin Settings',
            'menu_title' => 'My Plugin',
            'capability' => 'manage_options',
            'fields'     => [ /* fields */ ],
        ],
    ],
];
```

## Common Use Cases

### Site Configuration
- API keys, external service credentials
- Custom site metadata, branding elements
- Feature toggles, experimental settings

### Content Management
- Default content templates
- Editorial guidelines, content policies
- Publishing workflows, approval settings

### User Experience
- Maintenance mode, coming soon page
- Custom messages, notifications
- Social media integration

## Comparison with Creating New Settings Page

### Adding to Existing Page (This Example)
✅ **No UI clutter** - No new menu items
✅ **User familiarity** - Settings in expected location
✅ **Simpler for users** - Everything in one place
✅ **Quick setup** - No page configuration needed

### Creating New Page
✅ **Better organization** - For many complex settings
✅ **Custom branding** - Your own menu item
✅ **Separation** - Clear distinction from WordPress settings
✅ **Advanced layouts** - Custom tabs, sections, styling

## When to Use This Approach

- ✅ Adding a few simple site-wide settings
- ✅ Extending WordPress's core functionality
- ✅ Settings that logically fit with General/Reading/Writing
- ✅ When UI simplicity is priority
- ✅ For theme or small plugin settings

## When to Create a Custom Settings Page Instead

- ❌ Many complex settings (10+ fields)
- ❌ Need custom UI, tabs, or advanced layout
- ❌ Settings specific to your plugin/theme brand
- ❌ Requires separate capabilities or permissions
- ❌ Settings don't fit conceptually with WordPress pages

## Advantages vs. Existing Post Types Pattern

This follows the **exact same pattern** as adding fields to existing post types:

### CPT Pattern
```php
'cpts' => [
    ['id' => 'post', 'fields' => [...]]  // Adds to existing 'post' type
]
```

### Settings Pattern
```php
'settings_pages' => [
    ['id' => 'general', 'fields' => [...]]  // Adds to existing 'general' page
]
```

**Benefits:**
- ✅ Consistent API across CPTs and settings
- ✅ Same auto-detection logic
- ✅ Easy to understand and remember
- ✅ Works with array and JSON configuration

## Pro Tips

1. **Field Naming**: Use unique, descriptive names to avoid conflicts
   ```php
   'name' => 'my_plugin_contact_email'  // Good
   'name' => 'email'                     // Too generic
   ```

2. **Validation**: Add appropriate validation rules
   ```php
   'required' => true,
   'maxlength' => 150,
   ```

3. **Descriptions**: Help users understand what each field does
   ```php
   'description' => 'This email will be used for all system notifications',
   ```

4. **Defaults**: Provide sensible default values
   ```php
   'default' => 'contact@example.com',
   ```

## Related Examples

- **[10-existing-settings-page-json](../10-existing-settings-page-json/)** - Same functionality using JSON configuration
- **[07-existing-post-type-array](../07-existing-post-type-array/)** - Similar pattern for adding fields to existing post types
- **[03-settings-page-array](../03-settings-page-array/)** - Creating a new settings page instead

## Troubleshooting

**Fields not showing?**
- Ensure the settings page exists (check the 'id' matches: general, writing, reading, etc.)
- Verify the plugin is activated
- Check for JavaScript errors in browser console

**Values not saving?**
- Check user permissions (`manage_options` capability)
- Verify nonce is present in the form (WordPress handles this)
- Look for PHP errors in debug log

**Conflicts with other plugins?**
- Use unique field names with a prefix
- Check if another plugin uses the same option names
- Test with all other plugins temporarily disabled

## Support

For issues or questions about this example:
- Check the [WP-CMF Documentation](../../docs/)
- Review the [Field API Documentation](../../docs/field-api.md)
- Open an issue on GitHub

---

**Part of WP-CMF Examples** | [View All Examples](../README.md)
