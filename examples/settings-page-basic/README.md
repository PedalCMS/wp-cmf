# Basic Settings Page Example

## Overview

This example demonstrates how to create a simple top-level settings page using the WP-CMF SettingsPage class through the Manager and Registrar.

## What This Example Shows

- Creating a top-level settings page with custom title and icon
- Using a callback function to render page content
- Setting capability requirements (`manage_options`)
- Positioning the menu item in the WordPress admin
- Basic form rendering with WordPress styling

## Key Features

### Top-Level Page
```php
$manager->get_registrar()->add_settings_page( 'my-settings', [
    'page_title' => 'My Plugin Settings',
    'menu_title' => 'My Settings',
    'capability' => 'manage_options',
    'icon_url'   => 'dashicons-admin-generic',
    'position'   => 80,
] );
```

### Custom Render Callback
The example includes a custom callback that renders a settings form with:
- WordPress-standard form wrapper (`wrap` class)
- Form table layout (`form-table` class)
- Integration with WordPress options API
- Standard submit button

## Usage

1. Include this file in your plugin's main file or use it as a reference
2. The settings page will appear in the WordPress admin menu
3. Only users with `manage_options` capability can access it

## WordPress Integration

- **Hook**: Automatically registered via `admin_menu` action
- **Capability Check**: WordPress verifies `manage_options` before displaying
- **Menu Position**: Position 80 places it near the bottom of the admin menu
- **Icon**: Uses WordPress Dashicons (`dashicons-admin-generic`)

## Next Steps

See other examples for:
- Submenu pages (`settings-page-submenu/`)
- Custom rendering techniques (`settings-page-custom-render/`)
- Advanced Manager usage (`settings-page-manager-usage/`)
