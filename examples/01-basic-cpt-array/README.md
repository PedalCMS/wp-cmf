# Basic Custom Post Type with Array Configuration

This example demonstrates the simplest way to create a Custom Post Type with fields using array-based configuration.

## 📋 What This Example Shows

- ✅ Register a Custom Post Type (Book)
- ✅ Add 5 common field types to the CPT
- ✅ Display fields in the WordPress admin
- ✅ Save and retrieve field data
- ✅ Simple, beginner-friendly approach

## 🎯 Features

**Custom Post Type:** Book
- Public post type with standard WordPress UI
- Supports: title, editor, thumbnail
- Appears in WordPress admin menu

**Fields Included:**
1. **ISBN** - Text field for book identification
2. **Author** - Text field for author name
3. **Publication Year** - Number field with min/max validation
4. **Genre** - Select dropdown with predefined options
5. **In Stock** - Checkbox for availability status

## 📁 Files

- `example.php` - Main plugin file with array configuration
- `README.md` - This documentation file

## 🚀 How to Use

### Installation

1. Copy this folder to `wp-content/plugins/`
2. Rename it to your plugin name (e.g., `my-books-plugin`)
3. Activate the plugin in WordPress admin

### Accessing the CPT

After activation:
1. Go to WordPress admin
2. Look for "Books" in the admin menu
3. Click "Add New" to create a book
4. Fill in the custom fields
5. Publish the post

## 💻 Code Breakdown

### Configuration Array Structure

```php
$config = [
    'cpts' => [
        [
            'id' => 'book',                    // CPT identifier (max 20 chars)
            'args' => [                        // WordPress CPT arguments
                'label' => 'Books',
                'public' => true,
                'supports' => ['title', 'editor', 'thumbnail']
            ],
            'fields' => [                      // Array of field configurations
                [
                    'name' => 'isbn',
                    'type' => 'text',
                    'label' => 'ISBN',
                    'description' => 'International Standard Book Number'
                ],
                // ... more fields
            ]
        ]
    ]
];
```

### Registration

```php
Manager::init()->register_from_array($config);
```

That's it! WP-CMF handles:
- CPT registration with WordPress
- Metabox creation for fields
- Field rendering in admin
- Data sanitization and validation
- Saving field values to post meta

## 🔍 Retrieving Field Data

### In Templates

```php
<?php
// Get the ISBN
$isbn = get_post_meta(get_the_ID(), 'isbn', true);
echo esc_html($isbn);

// Get the author
$author = get_post_meta(get_the_ID(), 'author', true);
echo esc_html($author);

// Get publication year
$year = get_post_meta(get_the_ID(), 'publication_year', true);
echo absint($year);

// Get genre
$genre = get_post_meta(get_the_ID(), 'genre', true);
echo esc_html($genre);

// Check if in stock (checkbox returns '1' or empty)
$in_stock = get_post_meta(get_the_ID(), 'in_stock', true);
if ($in_stock) {
    echo 'Available';
} else {
    echo 'Out of Stock';
}
?>
```

### In Theme Functions

```php
function display_book_info($post_id) {
    $isbn = get_post_meta($post_id, 'isbn', true);
    $author = get_post_meta($post_id, 'author', true);
    $year = get_post_meta($post_id, 'publication_year', true);
    
    return sprintf(
        '%s by %s (ISBN: %s, %d)',
        get_the_title($post_id),
        esc_html($author),
        esc_html($isbn),
        absint($year)
    );
}
```

## 🎨 Customization Tips

### Add More Fields

Simply add more field arrays to the `fields` array:

```php
'fields' => [
    // ... existing fields
    [
        'name' => 'publisher',
        'type' => 'text',
        'label' => 'Publisher',
        'description' => 'Book publisher name'
    ]
]
```

### Change CPT Arguments

Modify the `args` array to customize the CPT:

```php
'args' => [
    'label' => 'Books',
    'public' => true,
    'menu_icon' => 'dashicons-book',      // Add custom icon
    'has_archive' => true,                // Enable archives
    'rewrite' => ['slug' => 'books'],     // Custom URL slug
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt']
]
```

### Add Field Validation

```php
[
    'name' => 'isbn',
    'type' => 'text',
    'label' => 'ISBN',
    'required' => true,                    // Make field required
    'validation' => [
        'pattern' => '/^[0-9-]+$/',        // Only numbers and hyphens
        'min_length' => 10,
        'max_length' => 17
    ]
]
```

## 📚 Related Examples

- **[02-basic-cpt-json](../02-basic-cpt-json/)** - Same example using JSON configuration
- **[05-complete-array-example](../05-complete-array-example/)** - Advanced example with all field types

## ❓ Common Questions

**Q: Where is my data stored?**
A: Field values are stored in the `wp_postmeta` table using WordPress's `post_meta` system.

**Q: Can I add more CPTs?**
A: Yes! Just add more arrays to the `cpts` array in the configuration.

**Q: How do I style the fields?**
A: Use the `class` and `wrapper_class` properties, or add custom CSS targeting the field names.

**Q: Can I use this in a theme?**
A: Yes, but it's recommended to use plugins for CPT registration so they persist across theme changes.

## 🚦 Next Steps

1. Try modifying the field configuration
2. Add more fields or CPTs
3. Check out the JSON version in `02-basic-cpt-json`
4. Explore the comprehensive example in `05-complete-array-example`

---

**Questions or Issues?** Check the main README or Field API documentation.
