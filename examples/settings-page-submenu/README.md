# Submenu Settings Page Example

## Overview

This example demonstrates how to create submenu pages that appear under existing WordPress admin menus (Settings, Tools, custom post types, etc.).

## What This Example Shows

- Creating submenu pages under core WordPress menus
- Using the fluent interface with SettingsPage instances
- Adding pages to Settings, Tools, and custom post type menus
- Different capability requirements for different contexts

## Common Parent Slugs

### Core WordPress Menus
- `options-general.php` - Settings menu
- `tools.php` - Tools menu
- `themes.php` - Appearance menu
- `plugins.php` - Plugins menu
- `users.php` - Users menu
- `management/upload.php` - Media menu
- `edit.php` - Posts menu
- `edit.php?post_type=page` - Pages menu

### Custom Post Type Menus
- `edit.php?post_type={post_type}` - Custom post type menu
- Example: `edit.php?post_type=book` - Books CPT menu

## Example 1: Settings Submenu
```php
$settings_submenu = new SettingsPage( 'my-plugin-settings' );
$settings_submenu
    ->set_page_title( 'My Plugin Settings' )
    ->set_parent( 'options-general.php' );
```

This creates a page under **Settings > My Plugin**.

## Example 2: Tools Submenu
```php
$tools_submenu = new SettingsPage( 'my-plugin-tools' );
$tools_submenu
    ->set_page_title( 'My Plugin Tools' )
    ->set_parent( 'tools.php' );
```

This creates a page under **Tools > My Tools**.

## Example 3: Custom Post Type Submenu
```php
$cpt_submenu = new SettingsPage( 'book-settings' );
$cpt_submenu
    ->set_page_title( 'Book Settings' )
    ->set_parent( 'edit.php?post_type=book' );
```

This creates a page under **Books > Settings** (assuming Books CPT exists).

## Key Differences from Top-Level Pages

1. **No Icon**: Submenu pages don't use icons
2. **No Position**: Position is determined by parent menu
3. **Parent Required**: Must specify `parent_slug` via `set_parent()`

## Capabilities

Each submenu can have different capabilities:
- `manage_options` - For settings pages (admin only)
- `edit_posts` - For editor-level access
- `edit_pages` - For page editors
- Custom capabilities based on your plugin's needs

## Usage

1. Choose the appropriate parent menu slug
2. Create a SettingsPage instance
3. Set the parent using `set_parent()`
4. Add to registrar using `add_settings_page_instance()`

## Best Practices

- Use `manage_options` for administrative settings
- Use contextual capabilities for CPT-related pages
- Keep menu titles short and clear
- Group related functionality under the same parent

## Next Steps

See other examples for:
- Top-level pages (`settings-page-basic/`)
- Custom rendering (`settings-page-custom-render/`)
- Advanced patterns (`settings-page-manager-usage/`)
