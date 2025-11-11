# Complete Array Configuration Example

This is the most comprehensive example demonstrating **all 11 WP-CMF field types** with 2 Custom Post Types and 2 Settings Pages using array-based configuration.

## 📋 What This Example Shows

- ✅ **2 Custom Post Types** (Products & Events)
- ✅ **2 Settings Pages** (Shop Settings & Event Management)
- ✅ **All 11 Core Field Types** demonstrated
- ✅ Multiple metabox contexts (normal, side, advanced)
- ✅ Field validation and requirements
- ✅ Default values and placeholders
- ✅ Frontend display helpers and shortcodes
- ✅ Production-ready patterns

## 🎯 What's Included

### Custom Post Types

#### 1. Product CPT
**11 Fields demonstrating all field types:**
1. **SKU** (text) - Required, with validation
2. **Detailed Description** (textarea) - 8 rows
3. **Price** (number) - Min/max/step validation
4. **Category** (select) - 6 options
5. **Condition** (radio) - 4 options
6. **In Stock** (checkbox) - Boolean toggle
7. **Supplier Email** (email) - With validation
8. **Product URL** (url) - External link
9. **Release Date** (date) - Min/max dates
10. **Admin Access Code** (password) - Secured field
11. **Primary Color** (color) - Color picker

**Metabox Contexts:**
- Normal: SKU, Description, Release Date
- Side: Price, Category, Condition, In Stock, Color
- Advanced: Supplier Email, Product URL, Access Code

#### 2. Event CPT
**5 Essential Fields:**
- Event Date (date) - Required
- Location (text) - Required
- Max Attendees (number)
- Registration URL (url)
- Contact Email (email)

### Settings Pages

#### 1. Shop Settings
**11 Fields (all types):**
- Store Name (text) - Required
- Store Description (textarea)
- Enable Shopping Cart (checkbox)
- Currency (select)
- Primary Payment Method (radio)
- Support Email (email) - Required
- Store URL (url)
- Brand Color (color)
- API Key (password)
- Maximum Order Amount (number)
- Next Sale Start Date (date)

#### 2. Event Management Settings
**4 Configuration Fields:**
- Enable Events (checkbox)
- Default Duration (number)
- Notification Email (email)
- Event Page Color (color)

## 📁 Files

- `example.php` - Complete plugin file (500+ lines)
- `README.md` - This comprehensive documentation

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Activate the plugin in WordPress admin
3. You'll see:
   - "Products" CPT in admin menu
   - "Events" CPT in admin menu
   - "Shop Settings" in admin menu
   - "Events Config" in admin menu

### Creating Content

**Add a Product:**
1. Go to Products → Add New
2. Fill in the title and editor content
3. Scroll down to see metaboxes with all 11 field types
4. Fill in fields (SKU and Description are required)
5. Publish

**Add an Event:**
1. Go to Events → Add New
2. Fill in Event Date and Location (required)
3. Add optional fields
4. Publish

**Configure Settings:**
1. Go to Shop Settings
2. Configure store options
3. Save Changes
4. Go to Events Config for event settings

## 💻 Code Structure

### Main Configuration Array

```php
$config = [
    'cpts' => [
        [
            'id' => 'product',
            'args' => [...],
            'fields' => [
                // 11 fields demonstrating all types
            ]
        ],
        [
            'id' => 'event',
            'args' => [...],
            'fields' => [...]
        ]
    ],
    'settings_pages' => [
        [
            'id' => 'shop_settings',
            'fields' => [
                // 11 fields demonstrating all types
            ]
        ],
        [
            'id' => 'event_settings',
            'fields' => [...]
        ]
    ]
];
```

### All 11 Field Types Explained

#### 1. Text Field
```php
[
    'name' => 'sku',
    'type' => 'text',
    'label' => 'Product SKU',
    'placeholder' => 'PROD-001',
    'required' => true
]
```
**Use for:** Short text inputs, identifiers, names

#### 2. Textarea Field
```php
[
    'name' => 'description',
    'type' => 'textarea',
    'label' => 'Detailed Description',
    'rows' => 8,
    'cols' => 50
]
```
**Use for:** Long text, descriptions, notes

#### 3. Number Field
```php
[
    'name' => 'price',
    'type' => 'number',
    'label' => 'Price',
    'min' => 0,
    'max' => 99999,
    'step' => 0.01
]
```
**Use for:** Prices, quantities, measurements

#### 4. Select Field
```php
[
    'name' => 'category',
    'type' => 'select',
    'label' => 'Product Category',
    'options' => [
        'electronics' => 'Electronics',
        'clothing' => 'Clothing'
    ]
]
```
**Use for:** Single choice from dropdown

#### 5. Radio Field
```php
[
    'name' => 'condition',
    'type' => 'radio',
    'label' => 'Product Condition',
    'options' => [
        'new' => 'Brand New',
        'used' => 'Used'
    ]
]
```
**Use for:** Single choice, visible options

#### 6. Checkbox Field
```php
[
    'name' => 'in_stock',
    'type' => 'checkbox',
    'label' => 'In Stock',
    'default' => true
]
```
**Use for:** Boolean toggles, on/off states

#### 7. Email Field
```php
[
    'name' => 'supplier_email',
    'type' => 'email',
    'label' => 'Supplier Email',
    'placeholder' => 'supplier@example.com'
]
```
**Use for:** Email addresses (auto-validated)

#### 8. URL Field
```php
[
    'name' => 'product_url',
    'type' => 'url',
    'label' => 'External Product URL',
    'placeholder' => 'https://example.com'
]
```
**Use for:** Website links (auto-validated)

#### 9. Date Field
```php
[
    'name' => 'release_date',
    'type' => 'date',
    'label' => 'Release Date',
    'min' => '2020-01-01',
    'max' => '2030-12-31'
]
```
**Use for:** Dates, deadlines, schedules

#### 10. Password Field
```php
[
    'name' => 'admin_access_code',
    'type' => 'password',
    'label' => 'Admin Access Code'
]
```
**Use for:** Sensitive data, API keys, passwords

#### 11. Color Field
```php
[
    'name' => 'primary_color',
    'type' => 'color',
    'label' => 'Primary Color',
    'default' => '#FF5733'
]
```
**Use for:** Color schemes, branding, themes

## 🔍 Retrieving Data

### CPT Fields (Post Meta)

```php
// Get single field
$sku = get_post_meta($post_id, 'sku', true);

// Get all product data
$product_data = [
    'sku' => get_post_meta($post_id, 'sku', true),
    'price' => get_post_meta($post_id, 'price', true),
    'category' => get_post_meta($post_id, 'category', true),
    'in_stock' => get_post_meta($post_id, 'in_stock', true),
    'color' => get_post_meta($post_id, 'primary_color', true),
];
```

### Settings Fields (Options)

```php
// Get individual settings
$store_name = get_option('store_name', 'Default Store');
$brand_color = get_option('brand_color', '#E74C3C');

// Get all shop settings
$shop_settings = [
    'name' => get_option('store_name'),
    'email' => get_option('support_email'),
    'currency' => get_option('currency', 'USD'),
    'cart_enabled' => get_option('enable_cart', false),
];
```

## 🎨 Frontend Display

### Using Shortcodes

```
[product_info]
[product_info id="123"]
[event_info]
[event_info id="456"]
```

### In Theme Templates

**Single Product Template (single-product.php):**
```php
<?php
// Display product info
$sku = get_post_meta(get_the_ID(), 'sku', true);
$price = get_post_meta(get_the_ID(), 'price', true);
$in_stock = get_post_meta(get_the_ID(), 'in_stock', true);
?>

<div class="product-details">
    <p><strong>SKU:</strong> <?php echo esc_html($sku); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format((float)$price, 2); ?></p>
    <p class="stock">
        <?php if ($in_stock): ?>
            <span style="color: green;">✓ In Stock</span>
        <?php else: ?>
            <span style="color: red;">Out of Stock</span>
        <?php endif; ?>
    </p>
</div>
```

**Using Store Settings in Header:**
```php
// themes/your-theme/header.php
<?php
$store_name = get_option('store_name', get_bloginfo('name'));
$brand_color = get_option('brand_color', '#E74C3C');
?>
<header style="background-color: <?php echo esc_attr($brand_color); ?>">
    <h1><?php echo esc_html($store_name); ?></h1>
</header>
```

## 🎯 Advanced Patterns

### Dynamic Field Configuration

```php
// Build fields array dynamically
$product_fields = [];

// Always add SKU
$product_fields[] = [
    'name' => 'sku',
    'type' => 'text',
    'label' => 'SKU',
    'required' => true
];

// Conditionally add fields
if (current_user_can('manage_options')) {
    $product_fields[] = [
        'name' => 'admin_access_code',
        'type' => 'password',
        'label' => 'Admin Access Code'
    ];
}

$config['cpts'][0]['fields'] = $product_fields;
```

### Metabox Organization

Fields are organized by context for better UX:

**Normal (main content area):**
- Primary fields (SKU, Description, Release Date)
- High priority, high visibility

**Side (sidebar):**
- Quick-access fields (Price, Category, Stock Status)
- Commonly used options

**Advanced (below main content):**
- Technical/admin fields (Emails, URLs, Access Codes)
- Less frequently used options

### Field Validation Examples

```php
// Required field
[
    'name' => 'sku',
    'type' => 'text',
    'required' => true
]

// Number with constraints
[
    'name' => 'price',
    'type' => 'number',
    'min' => 0,
    'max' => 99999,
    'step' => 0.01
]

// Date with range
[
    'name' => 'release_date',
    'type' => 'date',
    'min' => '2020-01-01',
    'max' => '2030-12-31'
]
```

## 📊 Data Architecture

### Database Tables

**Post Meta (CPT Fields):**
```
wp_postmeta
- meta_key: 'sku', 'price', 'category', etc.
- meta_value: Field value
- post_id: Links to wp_posts
```

**Options (Settings Fields):**
```
wp_options
- option_name: 'store_name', 'brand_color', etc.
- option_value: Field value
- autoload: 'yes' (cached)
```

## 📚 Related Examples

- **[01-basic-cpt-array](../01-basic-cpt-array/)** - Simple CPT introduction
- **[03-settings-page-array](../03-settings-page-array/)** - Simple settings page
- **[06-complete-json-example](../06-complete-json-example/)** - Same features in JSON format

## ❓ Common Questions

**Q: Can I use only some of these field types?**
A: Absolutely! This example shows all types for reference. Use only what you need.

**Q: How do I add more CPTs or settings pages?**
A: Add more arrays to the `cpts` or `settings_pages` arrays.

**Q: Can I customize the metabox titles?**
A: Yes, add `'title' => 'Custom Title'` to individual fields or create custom metaboxes.

**Q: How do I handle file uploads?**
A: Use WordPress's native media uploader or check examples_bak for advanced patterns.

**Q: Can I add custom validation?**
A: Yes, use the `validation` property or WordPress filters for custom validation logic.

## 💡 Pro Tips

1. **Start Simple**: Don't use all 11 types at once. Add fields as needed.
2. **Organize by Context**: Use normal/side/advanced for better UX.
3. **Set Defaults**: Always provide sensible default values.
4. **Validate Input**: Use required, min/max, and validation rules.
5. **Document Fields**: Use clear labels and helpful descriptions.
6. **Test Thoroughly**: Test all field types before production.
7. **Escape Output**: Always use esc_html(), esc_attr(), etc. on frontend.
8. **Cache Options**: Settings fields are auto-cached by WordPress.
9. **Use Capabilities**: Restrict sensitive fields to appropriate user roles.
10. **Consider UX**: Don't overwhelm users with too many fields.

## 🚦 Next Steps

1. Study each field type's configuration
2. Test creating products and events
3. Configure the settings pages
4. Try the frontend shortcodes
5. Compare with JSON version in `06-complete-json-example`
6. Adapt this example for your specific needs
7. Read the Field API docs for advanced features

---

**Questions or Issues?** This is the most complete example available. Check the main README and Field API documentation for additional details.
