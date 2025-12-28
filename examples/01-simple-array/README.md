# Simple Example - PHP Array Configuration

This is a minimal example demonstrating WP-CMF basics using PHP array configuration.

## What This Example Creates

### Custom Post Type: Book
A simple "Books" post type with the following fields:
- **ISBN** (text) - Book identifier
- **Author** (text, required) - Author name
- **Page Count** (number) - Number of pages
- **Publication Date** (date) - When published
- **Genre** (select) - Book category
- **In Stock** (checkbox) - Availability status
- **Synopsis** (textarea) - Book description

### Settings Page: Library Settings
A top-level settings page with:
- **Library Name** (text) - Name of the library
- **Contact Email** (email) - Contact information
- **Website URL** (url) - Library website
- **Max Borrowing Days** (number) - Loan period
- **Enable Notifications** (checkbox) - Email reminders
- **Display Theme** (radio) - Light/Dark/Auto theme
- **Accent Color** (color) - UI accent color

## Usage

```php
// Get book meta
$author = get_post_meta( $post_id, 'author_name', true );
$isbn   = get_post_meta( $post_id, 'isbn', true );

// Get settings
$library_name = get_option( 'library-settings_library_name' );
$accent_color = get_option( 'library-settings_accent_color', '#2271b1' );
```

## Key Concepts Demonstrated

1. **CPT Registration** - Simple post type with labels, icons, supports
2. **Settings Page** - Top-level menu with icon and position
3. **Common Field Types** - text, textarea, number, date, select, checkbox, radio, email, url, color
4. **Field Options** - required, placeholder, default, min/max, description
5. **Data Retrieval** - `get_post_meta()` and `get_option()` patterns

## For Advanced Features

See `advanced-array` or `advanced-json` examples for:
- All 16 field types
- Tabs, Metaboxes, Groups, Repeaters
- Adding fields to existing post types (posts, pages)
- Adding fields to existing settings (General, Reading, etc.)
- Nested containers and complex layouts
- Before-save filters
