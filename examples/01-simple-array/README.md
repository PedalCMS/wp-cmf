# Simple Example - PHP Array Configuration

This is a minimal example demonstrating WP-CMF basics using PHP array configuration.

## What This Example Creates

### Custom Post Type: Book
A simple "Books" post type with the following fields:
- **Book Info Banner** (custom_html) - Informational display banner
- **ISBN** (text) - Book identifier
- **Author** (text, required) - Author name
- **Page Count** (number) - Number of pages
- **Publication Date** (date) - When published
- **In Stock** (checkbox) - Availability status
- **Synopsis** (textarea) - Book description
- **Book Cover** (upload) - Cover image upload

### Taxonomy: Book Genre
A hierarchical taxonomy for categorizing books with custom fields:
- **Genre Color** (color) - Color for genre badges and labels
- **Icon Class** (text) - Dashicons class for the genre
- **Featured Genre** (checkbox) - Display prominently on the site

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

// Get taxonomy term meta
$genre_color = get_term_meta( $term_id, 'genre_color', true );
$is_featured = get_term_meta( $term_id, 'is_featured', true );

// Get genres for a book with their custom colors
$genres = get_the_terms( $post_id, 'book_genre' );
foreach ( $genres as $genre ) {
    $color = get_term_meta( $genre->term_id, 'genre_color', true );
}

// Get settings
$library_name = get_option( 'library-settings_library_name' );
$accent_color = get_option( 'library-settings_accent_color', '#2271b1' );
```

## Key Concepts Demonstrated

1. **CPT Registration** - Simple post type with labels, icons, supports
2. **Taxonomy Registration** - Hierarchical taxonomy with custom fields
3. **Settings Page** - Top-level menu with icon and position
4. **Common Field Types** - text, textarea, number, date, select, checkbox, radio, email, url, color, custom_html, upload
5. **Field Options** - required, placeholder, default, min/max, description
6. **Data Retrieval** - `get_post_meta()`, `get_term_meta()`, and `get_option()` patterns

## For Advanced Features

See `advanced-array` or `advanced-json` examples for:
- All 18 field types
- Tabs, Metaboxes, Groups, Repeaters
- Adding fields to existing post types (posts, pages)
- Adding fields to existing taxonomies (categories, tags)
- Adding fields to existing settings (General, Reading, etc.)
- Nested containers and complex layouts
- Before-save filters
