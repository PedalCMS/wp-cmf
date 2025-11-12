# Example 12: Custom Post Type with Submenu Settings Page (JSON)

This example demonstrates creating a **custom post type** with a **settings page as a submenu** using **JSON configuration**. Perfect for CPT-specific configurations loaded from external config files.

## Overview

- **Configuration Format**: JSON file (`config.json`)
- **Custom Post Type**: "Products" with 13 fields
- **Settings Page**: Submenu under Products with 19 configuration fields
- **Minimal PHP**: Just a few lines to load the JSON

## Files

- `config.json` - Complete JSON configuration (270+ lines)
- `example.php` - Minimal PHP loader (~15 lines)

## Quick Start

```php
use Pedalcms\WpCmf\Core\Manager;

// Load configuration from JSON file
Manager::init()->register_from_json( __DIR__ . '/config.json' );
```

## Configuration Structure

```json
{
  "cpts": [
    {
      "id": "product",
      "args": { /* WordPress CPT args */ },
      "fields": [ /* 13 product fields */ ]
    }
  ],
  "settings_pages": [
    {
      "id": "product-settings",
      "parent_slug": "edit.php?post_type=product",
      "fields": [ /* 19 settings fields */ ]
    }
  ]
}
```

## Product Fields (13)

### Basic Information
- **sku** (text, required) - Product identifier
- **price** (number, required) - Regular price
- **sale_price** (number) - Optional discount price

### Inventory
- **stock_quantity** (number) - Current stock
- **stock_status** (select) - In Stock, Out of Stock, Backorder, Pre-Order
- **track_inventory** (checkbox) - Enable tracking

### Specifications
- **weight** (number) - Product weight
- **dimensions** (text) - L × W × H
- **color** (select) - Color variant

### Features
- **featured** (checkbox) - Featured product flag
- **on_sale** (checkbox) - On sale flag
- **sale_badge** (text) - Custom badge text

## Settings Fields (19)

### Currency & Pricing (5 fields)
- **currency** (select) - USD, EUR, GBP, etc.
- **currency_position** (radio) - Before/after price
- **decimal_separator** (text) - Price decimal separator
- **thousand_separator** (text) - Price thousand separator
- **tax_rate** (number) - Tax percentage

### Measurements (2 fields)
- **weight_unit** (select) - kg, lbs, g, oz
- **dimension_unit** (select) - cm, m, in, ft

### Inventory (3 fields)
- **low_stock_threshold** (number) - Alert threshold
- **out_of_stock_visibility** (checkbox) - Hide out-of-stock products
- **backorders_allowed** (checkbox) - Allow backorders

### Display (4 fields)
- **products_per_page** (number) - Archive page limit
- **default_sorting** (select) - Default product order
- **show_sale_badge** (checkbox) - Display sale badge
- **sale_badge_color** (color) - Badge background color

### Email (2 fields)
- **low_stock_alert_email** (email) - Alert recipient
- **enable_stock_alerts** (checkbox) - Enable email alerts

### Archive (2 fields)
- **archive_page_title** (text) - Product archive title
- **archive_description** (textarea) - Archive page description

## Usage Examples

### Retrieve Product Data

```php
$product_id = 123;

// Get product fields
$sku = get_post_meta( $product_id, 'sku', true );
$price = get_post_meta( $product_id, 'price', true );
$stock = get_post_meta( $product_id, 'stock_quantity', true );
```

### Retrieve Settings

```php
$currency = get_option( 'currency', 'USD' );
$threshold = get_option( 'low_stock_threshold', 5 );
$tax_rate = get_option( 'tax_rate', 0 );
```

### Use Together

```php
$price = get_post_meta( $product_id, 'price', true );
$currency = get_option( 'currency', 'USD' );

echo "Price: {$price} {$currency}";
```

## What This Example Shows

✅ **JSON Configuration**: External `config.json` file for easy editing  
✅ **Same Features as Example 11**: Products CPT + Settings submenu  
✅ **Schema Validation**: Validates JSON against WP-CMF schema  
✅ **CI/CD Friendly**: JSON config can be version controlled and deployed  
✅ **Multi-Environment**: Same code, different JSON files per environment  
✅ **13 Product Fields + 19 Settings Fields**: Complete product management system

## Related Examples

- **Example 11**: Same example using PHP arrays
- **Example 6**: Complete JSON example with multiple CPTs
- **Example 10**: Existing settings page (JSON)

## Key Features

- ✅ External JSON configuration file
- ✅ Automatic JSON schema validation
- ✅ Submenu integration (`parent_slug`)
- ✅ 13 product fields + 19 settings fields
- ✅ All 11 core field types demonstrated
- ✅ Minimal PHP code (~15 lines)

## Benefits of JSON Configuration

1. **Separation of Concerns**: Configuration separate from logic
2. **Easy Editing**: Modify fields without touching PHP
3. **Validation**: Automatic JSON schema validation
4. **Portability**: Configuration easily shared across projects
5. **Version Control**: Clear diff tracking for config changes
6. **Environment-Specific**: Different configs per environment

## Further Reading

- [JSON Configuration Guide](../../docs/usage.md)
- [Field API Documentation](../../docs/field-api.md)
- [JSON Schema](../../schema.json)
