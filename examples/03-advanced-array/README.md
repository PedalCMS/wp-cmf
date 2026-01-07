# Advanced Example - PHP Array Configuration

This comprehensive example demonstrates **ALL** WP-CMF capabilities using PHP array configuration.

## What This Example Demonstrates

### 1. New Custom Post Type: Product
A complete e-commerce product post type with:

**Metabox: Basic Information**
- SKU (text, required)
- Price (number)
- Sale Price (number)
- Stock Status (select)
- Quantity (number)

**Metabox: Product Details (with Horizontal Tabs)**
- Tab 1: Description - Short description (textarea), Full description (wysiwyg)
- Tab 2: Specifications - Weight, dimensions, material, colors (text)
- Tab 3: Shipping - Free shipping (checkbox), shipping class (select), handling time (number)

**Metabox: Categorization (Sidebar)**
- Categories (checkbox, multiple)
- Brand (select)
- Featured (checkbox)

**Metabox: Variations (with Repeater)**
- Repeatable variation fields: name, SKU, price, stock

### 2. New Settings Page: Store Settings
A comprehensive settings page demonstrating proper container field usage:

**Store Settings Metabox** (wraps tabs - required on settings pages)
- **Vertical Tabs Container**:
  - **General Tab**: Store name, tagline, email, URL
  - **Pricing Tab**: Currency, position, decimals, tax settings
  - **Shipping Tab**: Domestic & international shipping (groups within tabs)
  - **Appearance Tab**: Colors, button styles, custom CSS
  - **Advanced Tab**: API key (password), webhook URL, dates, terms (wysiwyg)

> **Note**: Tabs and other container fields (except Group and Metabox) must be wrapped in a Metabox on settings pages. Group fields can be used directly and render as WordPress Settings API sections.

### 3. Adding Fields to Existing Post Types

**Posts (built-in):**
- Post Options (side metabox): Sponsored post checkbox, sponsor name/URL
- Reading Info (side metabox): Reading time, difficulty level

**Pages (built-in):**
- Page Settings: Hide title, sidebar position, layout, header color

### 4. Adding Fields to Existing Settings Pages

**General Settings (options-general.php):**
- Social Media Links (group): Facebook, Twitter, Instagram, LinkedIn URLs
- Brand Color (color picker)

## All 18 Field Types Demonstrated

| Field Type | Used In |
|------------|---------|
| `text` | SKU, Store Name, Dimensions, etc. |
| `textarea` | Short Description, Custom CSS |
| `number` | Price, Quantity, Tax Rate, etc. |
| `email` | Store Email, Admin Email |
| `url` | Webhook URL, Social Links |
| `password` | API Key |
| `date` | Launch Date |
| `color` | Primary Color, Header Background |
| `select` | Stock Status, Currency, Layout |
| `checkbox` | Featured, Free Shipping, Sponsored |
| `radio` | Currency Position, Button Style |
| `wysiwyg` | Full Description, Terms |
| `tabs` | Product Details, Store Settings |
| `metabox` | All CPT field containers |
| `group` | Shipping Settings, Social Links |
| `repeater` | Product Variations |

## Before-Save Filters

```php
// Ensure SKU is uppercase
add_filter( 'wp_cmf_before_save_field_sku', function( $value ) {
    return strtoupper( $value );
});

// Auto-calculate reading time
add_filter( 'wp_cmf_before_save_field_read_time', function( $value, $post_id ) {
    if ( empty( $value ) ) {
        $content = get_post_field( 'post_content', $post_id );
        $word_count = str_word_count( strip_tags( $content ) );
        $value = max( 1, ceil( $word_count / 200 ) );
    }
    return $value;
}, 10, 2 );
```

## Retrieving Values

```php
// Product CPT meta
$sku = get_post_meta( $product_id, 'sku', true );
$price = get_post_meta( $product_id, 'price', true );
$variations = get_post_meta( $product_id, 'variations', true ); // array

// Store settings
$currency = get_option( 'store-settings_currency', 'USD' );
$tax_rate = get_option( 'store-settings_tax_rate', 0 );

// Fields added to built-in posts
$sponsored = get_post_meta( $post_id, 'sponsored', true );
$read_time = get_post_meta( $post_id, 'read_time', true );

// Fields added to built-in pages
$layout = get_post_meta( $page_id, 'page_layout', true );

// Fields added to General Settings
$facebook = get_option( 'general_facebook_url' );
$brand_color = get_option( 'general_site_logo_color', '#0073aa' );
```

## Container Field Patterns

### Metabox Container
```php
[
    'name'     => 'my_metabox',
    'type'     => 'metabox',
    'label'    => 'My Metabox',
    'context'  => 'normal', // normal, side, advanced
    'priority' => 'high',   // high, default, low
    'fields'   => [ /* nested fields */ ],
]
```

### Tabs Container
```php
[
    'name'        => 'my_tabs',
    'type'        => 'tabs',
    'orientation' => 'horizontal', // or 'vertical'
    'tabs'        => [
        [
            'id'     => 'tab_1',
            'label'  => 'Tab One',
            'icon'   => 'dashicons-admin-generic',
            'fields' => [ /* nested fields */ ],
        ],
    ],
]
```

### Group Container
```php
[
    'name'        => 'my_group',
    'type'        => 'group',
    'label'       => 'Grouped Fields',
    'description' => 'Optional description',
    'fields'      => [ /* nested fields */ ],
]
```

### Repeater Container
```php
[
    'name'         => 'my_repeater',
    'type'         => 'repeater',
    'label'        => 'Repeatable Items',
    'button_label' => 'Add Item',
    'min'          => 1,
    'max'          => 10,
    'fields'       => [ /* fields to repeat */ ],
]
```

## Key Concepts

1. **Multiple register_from_array() calls** - Configuration can be split logically
2. **Existing post type extension** - Use built-in IDs like `post`, `page`
3. **Existing settings extension** - Use `parent` for submenu placement
4. **Container nesting** - Metabox → Tabs → Group → Fields
5. **Before-save filters** - `wp_cmf_before_save_field_{field_name}`
6. **Repeater data** - Returns array of arrays

## File Structure

```
03-advanced-array/
├── example.php    # Complete example code (500+ lines)
└── README.md      # This documentation
```
