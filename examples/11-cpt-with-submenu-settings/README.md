# Example 11: Custom Post Type with Submenu Settings Page (Array)

This example demonstrates how to create a **custom post type** with its own **settings page as a submenu** using **PHP array configuration**. Perfect for CPT-specific configurations with dynamic logic.

## What This Example Shows

✅ **Custom Post Type**: Complete "Product" CPT with 13 fields
✅ **Submenu Settings Page**: Settings page under the Products menu
✅ **Related Configuration**: 19 settings fields that control CPT behavior
✅ **Array Configuration**: PHP array-based configuration
✅ **All Field Types**: Demonstrates all 11 core field types## Use Case

**Product Management System**

- Custom Post Type: "Products" with SKU, pricing, inventory, specifications
- Settings Page: Configure currency, units, tax rates, thresholds, and alerts
- Submenu Integration: Settings appear under the Products menu for easy access

## Configuration Structure

```php
$config = [
    'cpts' => [
        [
            'id'     => 'product',
            'args'   => [ /* CPT args */ ],
            'fields' => [ /* Product fields */ ],
        ],
    ],
    'settings_pages' => [
        [
            'id'          => 'product-settings',
            'parent_slug' => 'edit.php?post_type=product', // ← Submenu!
            'fields'      => [ /* Settings fields */ ],
        ],
    ],
];
```

## Product Fields (13 Fields)

### Basic Information (3 fields)
- **SKU** (text) - Product identifier, required
- **Price** (number) - Regular price with decimals
- **Sale Price** (number) - Optional discount price

### Inventory Management (3 fields)
- **Stock Quantity** (number) - Current inventory count
- **Stock Status** (select) - In Stock, Out of Stock, Backorder, Pre-Order
- **Track Inventory** (checkbox) - Enable/disable tracking

### Specifications (3 fields)
- **Weight** (number) - Product weight
- **Dimensions** (text) - Length × Width × Height
- **Color** (select) - Color variant

### Features (3 fields)
- **Featured** (checkbox) - Mark as featured product
- **On Sale** (checkbox) - Display sale badge
- **Sale Badge Text** (text) - Custom badge text

## Settings Fields (19 Fields)

### Currency & Pricing (5 fields)
- **Currency** (select) - USD, EUR, GBP, JPY, AUD, CAD, INR
- **Currency Position** (radio) - Before/after amount
- **Decimal Separator** (text) - Character for decimals
- **Thousand Separator** (text) - Character for thousands
- **Tax Rate** (number) - Default tax percentage

### Measurements (2 fields)
- **Weight Unit** (select) - kg, g, lb, oz
- **Dimension Unit** (select) - cm, m, in, ft

### Inventory Settings (3 fields)
- **Low Stock Threshold** (number) - Warning quantity
- **Hide Out of Stock** (checkbox) - Visibility control
- **Enable Backorders** (checkbox) - Allow out-of-stock orders

### Display Options (4 fields)
- **Products Per Page** (number) - Archive pagination
- **Default Sort Order** (select) - Sort options
- **Show Sale Badges** (checkbox) - Enable badges
- **Badge Color** (color) - Badge color picker

### Email Notifications (2 fields)
- **Low Stock Alert Email** (email) - Notification recipient
- **Enable Stock Alerts** (checkbox) - Toggle alerts

### Archive Page (2 fields)
- **Archive Page Title** (text) - Custom archive title
- **Archive Page Description** (textarea) - Archive description

## Key Features Demonstrated

### 1. Submenu Integration

```php
'parent_slug' => 'edit.php?post_type=product'
```

This creates a settings submenu under the Products CPT menu in WordPress admin.

### 2. Comprehensive Field Coverage

The example demonstrates all 11 core field types:
- **text** - SKU, dimensions, sale badge text
- **number** - Price, sale price, stock quantity, weight, tax rate, threshold
- **select** - Stock status, color, currency, units, sort order
- **checkbox** - Track inventory, featured, on sale, visibility options
- **radio** - Currency position
- **email** - Low stock alert email
- **textarea** - Archive page description
- **color** - Badge color picker

### 3. Metabox Contexts

Demonstrates different WordPress metabox contexts:
- **normal** - Main content area (basic info, specifications)
- **side** - Sidebar area (inventory, features)

### 4. Field Priorities

Shows how to organize fields with priorities:
- **high** - Important fields shown first (SKU, price, stock)
- **default** - Secondary fields (specs, features)## Installation

1. **Copy the example file** to your plugin or theme:
   ```php
   require_once 'path/to/example.php';
   ```

2. **Activate your plugin** or refresh your theme

3. **Access the admin**:
   - Go to **Products** → See your custom post type
   - Go to **Products** → **Settings** → Configure options

4. **Create a product**:
   - Click "Add New Product"
   - Fill in fields (SKU, price, stock, etc.)
   - Save the product

5. **Configure settings**:
   - Go to Products → Settings
   - Set currency, units, thresholds
   - Configure display options

## Usage Examples

### Retrieve Product Data

```php
$product_id = 123;

// Get product fields
$sku = get_post_meta( $product_id, 'sku', true );
$price = get_post_meta( $product_id, 'price', true );
$stock = get_post_meta( $product_id, 'stock_quantity', true );
$weight = get_post_meta( $product_id, 'weight', true );
```

### Retrieve Settings

```php
// Get configuration from settings page
$currency = get_option( 'currency', 'USD' );
$weight_unit = get_option( 'weight_unit', 'kg' );
$low_stock_threshold = get_option( 'low_stock_threshold', 5 );
$tax_rate = get_option( 'tax_rate', 0 );
```

### Use Together

```php
// Combine product data with settings
$product_id = 123;
$price = get_post_meta( $product_id, 'price', true );
$currency = get_option( 'currency', 'USD' );

echo "Price: {$price} {$currency}";
```## Admin Interface

### Menu Structure
```
├── Products (dashicons-cart)
│   ├── All Products
│   ├── Add New Product
│   ├── Categories
│   ├── Tags
│   └── Settings ← Your submenu settings page
```

### Product Edit Screen

**Basic Information** (high priority, normal context)
- SKU: [________]
- Price: [________]
- Sale Price: [________]

**Specifications** (default priority, normal context)
- Weight: [________]
- Dimensions: [________]
- Color: [dropdown]

**Inventory** (high priority, side context)
- Stock Quantity: [________]
- Stock Status: [dropdown]
- ☑ Track Inventory

**Product Features** (default priority, side context)
- ☑ Featured Product
- ☑ On Sale
- Sale Badge Text: [________]

### Settings Page

**Currency & Pricing**
- Currency: [USD ▼]
- Currency Position: ● Before amount ○ After amount
- Decimal Separator: [.]
- Thousand Separator: [,]
- Tax Rate: [__] %

**Measurements**
- Weight Unit: [kg ▼]
- Dimension Unit: [cm ▼]

**Inventory**
- Low Stock Threshold: [5]
- ☑ Enable Stock Alerts
- Low Stock Alert Email: [admin@example.com]

**Display**
- Products Per Page: [12]
- Default Sort: [Newest First ▼]
- ☑ Show Sale Badges
- Badge Color: [🎨 #e74c3c]

## Real-World Applications

- **E-commerce**: Product catalog with inventory
- **Equipment Rental**: Availability tracking
- **Service Catalog**: Pricing tiers
- **Restaurant Menu**: Ingredient tracking
- **Real Estate**: Property listings with specs

## Extending This Example

You can easily extend this configuration by:

1. **Add More Fields**: Add new fields to either the CPT or settings page
2. **Add Taxonomies**: Register categories and tags for products
3. **Custom Capabilities**: Define custom user roles and permissions
4. **REST API**: Enable `show_in_rest` for Gutenberg and API access
5. **Custom Meta Boxes**: Add additional metaboxes with different contexts

## Best Practices

1. **Logical Grouping**: Group related fields together (Basic Info, Inventory, Specs)
2. **Default Values**: Provide sensible defaults for all fields
3. **Validation**: Use field validation rules (min, max, required)
4. **Helper Text**: Add descriptions to explain each field
5. **Context Organization**: Use appropriate metabox contexts (normal/side)
6. **Field Priorities**: Set priorities to control field order (high/default)

## Array vs JSON

**Use Array (this example) when:**
- ✅ Need PHP logic in configuration
- ✅ Dynamic field generation
- ✅ Complex conditional configurations
- ✅ Tight integration with theme/plugin code

**Use JSON (Example 12) when:**
- ✅ Configuration managed by non-developers
- ✅ Multi-environment deployments
- ✅ Version control for configuration
- ✅ CI/CD pipelines

## Related Examples

- **Example 12**: Same example using JSON configuration
- **Example 5**: Complete array example with multiple CPTs
- **Example 9**: Existing settings page (array)

## Further Reading

- [WordPress register_post_type()](https://developer.wordpress.org/reference/functions/register_post_type/)
- [WordPress add_submenu_page()](https://developer.wordpress.org/reference/functions/add_submenu_page/)
- [WordPress Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [Custom Post Type UI Best Practices](https://developer.wordpress.org/plugins/post-types/)

## Support

For issues or questions about this example:
- Check the [Field API documentation](../../docs/field-api.md)
- Review other [examples](../)
- Open an issue on GitHub
