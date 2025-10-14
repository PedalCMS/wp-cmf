# Advanced Manager Usage with Settings Pages

## Overview

This example demonstrates comprehensive patterns for using the Manager and Registrar with SettingsPage, including multiple registration methods, conditional pages, dynamic creation, and page retrieval.

## What This Example Shows

- Three different methods for registering settings pages
- Using the Manager singleton pattern
- Conditional page registration based on options
- Dynamic page creation from configurations
- Retrieving and inspecting registered pages
- Building complete admin sections with parent and child pages

## Registration Methods

### Method 1: Array Configuration via Registrar
```php
$registrar->add_settings_page( 'plugin-general', [
    'page_title' => 'Plugin General Settings',
    'menu_title' => 'General',
    'capability' => 'manage_options',
] );
```

**Best for:**
- Quick page registration
- Simple configurations
- When you don't need the instance reference

### Method 2: Factory Method with Instance
```php
$page = SettingsPage::from_array( 'plugin-advanced', [
    'page_title' => 'Advanced Settings',
    'parent_slug' => 'plugin-general',
] );

$registrar->add_settings_page_instance( $page );
```

**Best for:**
- When you need to keep a reference to the page
- Complex configurations
- Situations requiring page inspection before registration

### Method 3: Fluent Interface
```php
$page = new SettingsPage( 'plugin-api' );
$page->set_page_title( 'API Configuration' )
     ->set_parent( 'plugin-general' )
     ->set_callback( $callback );

$registrar->add_settings_page_instance( $page );
```

**Best for:**
- Maximum clarity and readability
- When building complex pages programmatically
- Step-by-step configuration

## Manager Singleton Pattern

```php
$manager = Manager::init();
$registrar = $manager->get_registrar();
```

The Manager provides:
- **Singleton Access**: Consistent instance across your plugin
- **Registrar Access**: Central registration coordinator
- **Hook Management**: Automatic WordPress hook initialization

## Advanced Patterns

### 1. Conditional Registration
```php
if ( get_option( 'enable_debug_page', false ) ) {
    $debug_page = new SettingsPage( 'plugin-debug' );
    // Configure and register...
}
```

Register pages based on:
- User preferences/options
- Capability checks
- Environment (development vs. production)
- Feature flags

### 2. Dynamic Page Creation
```php
$page_configs = [
    'reports' => [ /* config */ ],
    'logs'    => [ /* config */ ],
];

foreach ( $page_configs as $page_id => $config ) {
    $registrar->add_settings_page( "plugin-{$page_id}", $config );
}
```

Create multiple pages from:
- Configuration arrays
- Database settings
- Plugin options
- External data sources

### 3. Page Inspection
```php
$all_pages = $registrar->get_settings_pages();

if ( isset( $all_pages['plugin-api'] ) ) {
    $page = $all_pages['plugin-api'];
    $is_submenu = $page->is_submenu();
    $capability = $page->get_config( 'capability' );
}
```

Inspect pages to:
- Verify registration
- Check configuration
- Debug issues
- Build admin interfaces dynamically

## Complete Admin Section Structure

The example creates a full admin section:

```
Plugin General (Top-Level)
├── General (first submenu, auto-created)
├── Advanced
├── API
├── Reports
└── Logs
```

Plus conditionally:
```
└── Debug (if enabled)
```

## Best Practices

### 1. Organize by Hierarchy
```php
// Main page
$main = new SettingsPage( 'my-plugin' );
$main->set_page_title( 'My Plugin' );

// Subpages
$sub1 = new SettingsPage( 'my-plugin-sub1' );
$sub1->set_parent( 'my-plugin' );
```

### 2. Use Meaningful IDs
```php
// Good
'plugin-api-settings'
'plugin-dashboard'

// Avoid
'page1'
'settings'
```

### 3. Consistent Capabilities
```php
// Admin-only
->set_capability( 'manage_options' )

// Editor access
->set_capability( 'edit_posts' )

// Custom
->set_capability( 'manage_my_plugin' )
```

### 4. Reusable Callbacks
```php
class SettingsRenderer {
    public function render_api_page() { /* ... */ }
    public function render_logs_page() { /* ... */ }
}

$renderer = new SettingsRenderer();
$page->set_callback( [ $renderer, 'render_api_page' ] );
```

## Integration with Other Features

### With Custom Post Types
```php
// Add settings under CPT menu
$cpt_settings = new SettingsPage( 'book-settings' );
$cpt_settings->set_parent( 'edit.php?post_type=book' );
```

### With Fields (Future)
```php
// When Field classes are available (Milestone 3)
$registrar->add_fields( 'plugin-api', [
    'api_key' => [ /* field config */ ],
    'api_secret' => [ /* field config */ ],
] );
```

## Troubleshooting

### Page Not Showing
1. Check capability - user may not have access
2. Verify parent slug is correct
3. Ensure registration happens on `admin_menu` hook

### Wrong Menu Position
- Top-level: Use `set_position()` (ignored for submenus)
- Submenu: Order determined by registration order

### Callback Not Firing
1. Verify callback is callable
2. Check for PHP errors in callback
3. Ensure proper escaping of output

## Next Steps

See other examples for:
- Basic pages (`settings-page-basic/`)
- Submenu patterns (`settings-page-submenu/`)
- Custom rendering (`settings-page-custom-render/`)

When Milestone 3 is complete, see field integration examples for complete settings management with automatic form generation and validation.
