# Example 14: Tabs Field (Container Field)

This example demonstrates the new **Tabs Field** - a container field type that organizes nested fields into tabbed interfaces. The tabs field supports both horizontal (browser-style) and vertical (sidebar-style) layouts.

## What's New: Container Fields

Container fields are a special type of field that:
- **Don't store their own values** - they only organize other fields
- **Nested fields save independently** - each field uses its own field name
- **Work seamlessly** - nested fields behave exactly like regular fields
- **Support recursion** - containers can contain other containers

## Features Demonstrated

### ✅ Horizontal Tabs
- Browser-style tab navigation
- Perfect for settings pages
- Clean, modern interface

### ✅ Vertical Tabs
- Sidebar navigation layout
- Ideal for metaboxes with many options
- Better for longer forms

### ✅ Both CPT and Settings
- Example 1 & 2: Custom Post Types (Product, Event)
- Example 3 & 4: Settings Pages (Store, App Config)

### ✅ Rich Tab Configuration
- Tab icons (Dashicons)
- Tab descriptions
- Default active tab
- Multiple tabs per field

## How It Works

### Container Field Architecture

1. **Registration**: When you add a tabs field, WP-CMF automatically:
   - Registers the tabs field (container)
   - Extracts all nested fields from all tabs
   - Registers each nested field individually

2. **Rendering**: When the tabs field renders:
   - Creates the tab navigation UI
   - Loads each nested field's value using standard WordPress functions
   - Renders nested fields within tab panels

3. **Saving**: Nested fields save automatically:
   - Each field saves to its own meta key/option name
   - No special handling needed
   - Works exactly like regular fields

### Example Configuration

```php
[
    'name'        => 'product_tabs',
    'type'        => 'tabs',
    'label'       => 'Product Details',
    'orientation' => 'horizontal', // or 'vertical'
    'default_tab' => 'basic',
    'tabs'        => [
        [
            'id'          => 'basic',
            'label'       => 'Basic Info',
            'icon'        => 'dashicons-info',
            'description' => 'Basic product information',
            'fields'      => [
                // Regular field configurations
                [
                    'name'  => 'product_sku',
                    'type'  => 'text',
                    'label' => 'SKU',
                ],
                [
                    'name'  => 'product_price',
                    'type'  => 'number',
                    'label' => 'Price',
                ],
            ],
        ],
        [
            'id'     => 'details',
            'label'  => 'Details',
            'icon'   => 'dashicons-list-view',
            'fields' => [
                // More fields...
            ],
        ],
    ],
]
```

## Tab Field Options

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `name` | string | Field name (required) | - |
| `type` | string | Must be 'tabs' | - |
| `label` | string | Field label | - |
| `orientation` | string | 'horizontal' or 'vertical' | 'horizontal' |
| `default_tab` | string | ID of default active tab | First tab ID |
| `tabs` | array | Array of tab definitions | [] |

## Tab Definition Options

| Option | Type | Description | Required |
|--------|------|-------------|----------|
| `id` | string | Unique tab identifier | Yes |
| `label` | string | Tab display label | Yes |
| `icon` | string | Dashicon class (e.g., 'dashicons-info') | No |
| `description` | string | Tab description text | No |
| `fields` | array | Array of field configurations | Yes |

## Examples in This File

### Example 1: Product CPT (Horizontal Tabs)
- **Post Type**: `product`
- **Tabs**: Basic Info, Details, Shipping
- **Fields**: 8 fields total across 3 tabs
- **Features**: SKU, pricing, stock status, shipping options

### Example 2: Event CPT (Vertical Tabs)
- **Post Type**: `event`
- **Tabs**: Date & Time, Location, Tickets, Organizer
- **Fields**: 12 fields total across 4 tabs
- **Features**: Event scheduling, venue details, ticketing

### Example 3: Store Settings (Horizontal Tabs)
- **Settings Page**: `store-settings`
- **Tabs**: General, Checkout, Payments
- **Fields**: 8 fields total across 3 tabs
- **Features**: Store configuration, payment gateways

### Example 4: App Config (Vertical Tabs)
- **Settings Page**: `app-config`
- **Tabs**: API Settings, Email Settings, Advanced
- **Fields**: 10 fields total across 3 tabs
- **Features**: API keys, email config, debug mode

## Usage Instructions

### 1. Install the Example

Copy this file to your WordPress plugins directory or include it in your theme:

```php
require_once 'path/to/example.php';
```

### 2. View in WordPress Admin

**For Custom Post Types:**
1. Go to **Products → Add New** or **Events → Add New**
2. Scroll down to see the tabbed metaboxes
3. Click tabs to switch between different field groups
4. Enter data and save

**For Settings Pages:**
1. Go to **Store Settings** or **App Config** in the admin menu
2. Use tabs to navigate between setting groups
3. Configure options and save

### 3. Access Field Values

Nested fields save to their own meta keys/options, so access them normally:

```php
// For CPT fields (post meta)
$sku = get_post_meta( $post_id, 'product_sku', true );
$price = get_post_meta( $post_id, 'product_price', true );

// For settings fields (options)
$store_name = get_option( 'store_name' );
$api_key = get_option( 'app_api_key' );
```

## Styling

The tabs field includes built-in CSS for both orientations:

### Horizontal Tabs
- Browser-style tab buttons
- Active tab highlighted with bottom border
- Responsive layout

### Vertical Tabs
- Sidebar navigation
- Active tab with blue background
- Fixed sidebar width (200px)

You can override styles by targeting these classes:
- `.wp-cmf-tabs-horizontal` - Horizontal tabs container
- `.wp-cmf-tabs-vertical` - Vertical tabs container
- `.wp-cmf-tab-button` - Tab button
- `.wp-cmf-tab-button.active` - Active tab button
- `.wp-cmf-tab-panel` - Tab content panel
- `.wp-cmf-tab-panel.active` - Active tab panel

## Technical Details

### Container Field Interface

The tabs field implements `ContainerFieldInterface` which requires:

```php
interface ContainerFieldInterface extends FieldInterface {
    public function get_nested_fields(): array;
    public function is_container(): bool;
}
```

### Automatic Nested Field Registration

When you add a tabs field, the Registrar:
1. Creates the tabs field instance
2. Calls `get_nested_fields()` to extract all nested field configs
3. Creates and registers each nested field individually
4. Supports recursive nesting (containers in containers)

### Value Loading

Each nested field loads its own value:
- **CPT metaboxes**: Uses `get_post_meta( $post_id, $field_name, true )`
- **Settings pages**: Uses `get_option( $field_name, '' )`

This happens automatically during rendering - no special logic required.

## Benefits of This Approach

### 🎯 Simple & Clean
- No complex save/load logic
- Nested fields work like regular fields
- Easy to understand and maintain

### 🔧 Flexible
- Add any field type inside tabs
- Mix different field types in same tab
- Support for all existing fields

### 🚀 Extensible
- Create new container types easily
- Implement `ContainerFieldInterface`
- Automatic nested field handling

### ✅ Consistent
- Same validation rules
- Same sanitization
- Same data access patterns

## Creating Your Own Container Fields

To create a custom container field:

1. **Implement ContainerFieldInterface**:
```php
class MyContainerField extends AbstractField implements ContainerFieldInterface {
    public function is_container(): bool {
        return true;
    }

    public function get_nested_fields(): array {
        // Return array of nested field configs
    }

    public function render( $value = null ): string {
        // Render your container UI
        // Load and render nested fields
    }
}
```

2. **Register your field type**:
```php
FieldFactory::register_type( 'my-container', MyContainerField::class );
```

3. **Use in configurations**:
```php
[
    'name' => 'my_field',
    'type' => 'my-container',
    // Your container config
]
```

## Next Steps

- Explore other examples in the `examples/` directory
- Read `docs/field-api.md` for field development guide
- Check `tests/` for test examples
- Review source code in `src/Field/fields/TabsField.php`

## Support

For issues, questions, or contributions:
- GitHub: https://github.com/PedalCMS/wp-cmf
- Documentation: `docs/` directory
- Examples: `examples/` directory
