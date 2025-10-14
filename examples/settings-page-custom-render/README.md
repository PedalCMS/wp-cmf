# Custom Rendering for Settings Pages

## Overview

This example demonstrates advanced rendering techniques for WordPress admin settings pages, including custom classes, tabbed interfaces, dashboard widgets, and reusable rendering logic.

## What This Example Shows

- Creating a custom renderer class for organized code
- Implementing tabbed interfaces using WordPress nav-tabs
- Building dashboard-style pages with widget boxes
- Creating help/documentation pages with card layouts
- Using class methods as callbacks for better organization
- Creating parent pages with multiple submenus

## Custom Renderer Class

The example uses a dedicated renderer class to separate presentation logic:

```php
class MyPluginSettingsRenderer {
    public function render_main_page() { /* ... */ }
    public function render_dashboard() { /* ... */ }
    public function render_help_page() { /* ... */ }
}
```

### Benefits:
- **Organized**: All rendering logic in one place
- **Reusable**: Methods can be called from multiple contexts
- **Testable**: Easier to unit test rendering logic
- **Maintainable**: Clear separation of concerns

## Rendering Techniques

### 1. Tabbed Interface
```php
<h2 class="nav-tab-wrapper">
    <a href="?page=my-plugin-settings&tab=general" class="nav-tab nav-tab-active">General</a>
    <a href="?page=my-plugin-settings&tab=advanced" class="nav-tab">Advanced</a>
</h2>
```

Uses WordPress core styles (`nav-tab`, `nav-tab-wrapper`, `nav-tab-active`) for consistent UI.

### 2. Dashboard Widgets
```php
<div class="dashboard-widgets-wrap">
    <div id="dashboard-widgets" class="metabox-holder">
        <div class="postbox">
            <h2 class="hndle"><span>Quick Stats</span></h2>
            <div class="inside">
                <!-- Widget content -->
            </div>
        </div>
    </div>
</div>
```

Creates dashboard-style layout using WordPress postbox markup.

### 3. Card Layout
```php
<div class="card">
    <h2>Section Title</h2>
    <p>Section content...</p>
</div>
```

Uses WordPress card component for clean, organized content sections.

## Using Class Methods as Callbacks

```php
$renderer = new MyPluginSettingsRenderer();

$page->set_callback( [ $renderer, 'render_main_page' ] );
```

This allows for:
- Better code organization
- State management within the renderer class
- Shared helper methods
- Dependency injection possibilities

## Page Structure

The example creates a complete admin section:

1. **Main Page** (Top-Level)
   - Menu Title: "My Plugin"
   - Tabbed settings interface
   - WordPress Settings API integration

2. **Dashboard Submenu**
   - Parent: Main page
   - Widget-based layout
   - Stats and quick info

3. **Help Submenu**
   - Parent: Main page
   - Card-based documentation
   - Getting started guide

## WordPress Integration

### Settings API
```php
settings_fields( 'my_plugin_settings' );
do_settings_sections( 'my_plugin_settings' );
```

Integrates with WordPress Settings API for proper nonce validation and option handling.

### Admin Styles
Uses core WordPress CSS classes:
- `wrap` - Standard admin page wrapper
- `nav-tab-wrapper` / `nav-tab` - Tab navigation
- `postbox` / `metabox-holder` - Dashboard widgets
- `card` - Content cards
- `hndle` / `inside` - Postbox structure

## Best Practices

1. **Separate Concerns**: Keep rendering logic separate from business logic
2. **Use WordPress Styles**: Leverage core CSS for consistent UI
3. **Escape Output**: Always use `esc_html()`, `esc_attr()`, etc.
4. **Capability Checks**: WordPress handles this, but verify in sensitive operations
5. **Nonce Verification**: Use Settings API or add your own for form submissions

## Advanced Patterns

### Conditional Rendering
```php
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

if ( $active_tab === 'general' ) {
    // Render general tab
} elseif ( $active_tab === 'advanced' ) {
    // Render advanced tab
}
```

### AJAX Integration
Add AJAX endpoints to your renderer class:
```php
public function handle_ajax_action() {
    check_ajax_referer( 'my_plugin_nonce' );
    // Process AJAX request
    wp_send_json_success( $data );
}
```

## Next Steps

See other examples for:
- Basic pages (`settings-page-basic/`)
- Submenu pages (`settings-page-submenu/`)
- Manager integration (`settings-page-manager-usage/`)
