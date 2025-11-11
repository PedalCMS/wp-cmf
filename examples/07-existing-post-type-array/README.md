# Add Fields to Existing Post Type (Array Configuration)

This example demonstrates how to add custom fields to WordPress's built-in **post** post type using array configuration.

## Overview

Instead of creating a new custom post type, this example shows how to extend an existing WordPress post type (in this case, `post`) with additional custom fields. This is useful when you want to add metadata to standard WordPress posts without changing their core functionality.

## Features Demonstrated

- ✅ Adding fields to existing WordPress post types
- ✅ Array-based configuration for existing post types
- ✅ Multiple field types (number, text, checkbox, select, url)
- ✅ Field validation and defaults
- ✅ Metabox integration with built-in post editor

## Configuration Structure

```php
$config = [
    'cpts' => [
        [
            'id'     => 'post',      // Existing post type slug
            'fields' => [            // Array of field definitions
                // ... field configurations
            ],
        ],
    ],
];
```

**Note:** When the `id` matches an existing post type (like `post`, `page`, or `attachment`), WP-CMF automatically detects this and only adds fields without attempting to create a new post type. If you include `args`, they will be ignored for existing post types.

## Fields Added

This example adds 5 custom fields to the WordPress post edit screen:

1. **Reading Time** (number)
   - Estimated reading time in minutes
   - Min: 1, Max: 120

2. **Subtitle** (text)
   - Optional subtitle for the post
   - Max length: 200 characters

3. **Featured Content** (checkbox)
   - Mark post as featured content

4. **Content Type** (select)
   - Article, Tutorial, Review, News, or Interview
   - Defaults to "Article"

5. **External Source URL** (url)
   - Link to original source if content is republished

## Installation

1. Copy this example folder to your WordPress plugins directory
2. Activate the plugin through the WordPress admin panel
3. Edit or create a post to see the custom fields

## Usage

### Activating the Plugin

The fields are automatically added when the plugin is activated. Simply enable it from:
`WordPress Admin > Plugins > Add Fields to Existing Post Type (Array)`

### Viewing the Fields

After activation:
1. Go to `Posts > Add New` or edit an existing post
2. Scroll down below the content editor
3. You'll see a new metabox titled "Post Fields" with all 5 custom fields

### Retrieving Field Data

Use standard WordPress post meta functions to retrieve the field values:

```php
<?php
// Get reading time
$reading_time = get_post_meta( get_the_ID(), 'reading_time', true );

// Get subtitle
$subtitle = get_post_meta( get_the_ID(), 'post_subtitle', true );

// Check if featured
$is_featured = get_post_meta( get_the_ID(), 'featured_content', true );

// Get content type
$content_type = get_post_meta( get_the_ID(), 'content_type', true );

// Get external source
$external_url = get_post_meta( get_the_ID(), 'external_source', true );
?>
```

### Displaying Fields in Your Theme

Add this to your theme's `single.php` or `content.php` template:

```php
<?php
// Display subtitle
$subtitle = get_post_meta( get_the_ID(), 'post_subtitle', true );
if ( $subtitle ) {
    echo '<h2 class="post-subtitle">' . esc_html( $subtitle ) . '</h2>';
}

// Display reading time
$reading_time = get_post_meta( get_the_ID(), 'reading_time', true );
if ( $reading_time ) {
    echo '<p class="reading-time">⏱️ ' . absint( $reading_time ) . ' min read</p>';
}

// Show featured badge
if ( get_post_meta( get_the_ID(), 'featured_content', true ) ) {
    echo '<span class="featured-badge">Featured</span>';
}

// Display content type
$content_type = get_post_meta( get_the_ID(), 'content_type', true );
if ( $content_type ) {
    echo '<span class="content-type">' . esc_html( ucfirst( $content_type ) ) . '</span>';
}

// Show external source attribution
$external_url = get_post_meta( get_the_ID(), 'external_source', true );
if ( $external_url ) {
    echo '<p class="source">Source: <a href="' . esc_url( $external_url ) . '" target="_blank" rel="noopener">View Original</a></p>';
}
?>
```

## Supported Post Types

This approach works with any existing WordPress post type:

- `post` - Standard blog posts
- `page` - WordPress pages
- `attachment` - Media attachments
- Any registered custom post type from plugins

Just change the `post_type` value in the configuration array.

## Example: Adding Fields to Pages

```php
$config = [
    'cpts' => [
        [
            'id'     => 'page',
            'fields' => [
                [
                    'name'    => 'page_layout',
                    'type'    => 'select',
                    'label'   => 'Page Layout',
                    'options' => [
                        'full-width' => 'Full Width',
                        'sidebar'    => 'With Sidebar',
                        'landing'    => 'Landing Page',
                    ],
                ],
            ],
        ],
    ],
];
```

## Example: Multiple Existing Post Types

You can add fields to multiple existing post types in one configuration:

```php
$config = [
    'cpts' => [
        [
            'id'     => 'post',
            'fields' => [ /* fields for posts */ ],
        ],
        [
            'id'     => 'page',
            'fields' => [ /* fields for pages */ ],
        ],
        [
            'id'     => 'product', // WooCommerce product
            'fields' => [ /* fields for products */ ],
        ],
    ],
];
```

## Common Use Cases

### Blog Enhancement
- Reading time, difficulty level, series information
- Post format indicators, content ratings
- External attribution, original publication date

### Editorial Workflow
- Editor notes, review status, publication checklist
- SEO scores, readability metrics
- Social media snippets

### Content Organization
- Content pillars, topic clusters, related resources
- Target audience, content maturity
- Premium/free content indicators

## Pro Tips

1. **Field Naming**: Use unique, descriptive names to avoid conflicts with existing post meta
   ```php
   'name' => 'my_plugin_reading_time' // Good
   'name' => 'time'                    // Too generic
   ```

2. **Validation**: Add appropriate validation rules
   ```php
   'min' => 1,
   'max' => 120,
   'required' => true,
   ```

3. **Defaults**: Provide sensible default values
   ```php
   'default' => 'article',
   ```

4. **Descriptions**: Help users understand what each field does
   ```php
   'description' => 'Estimated reading time in minutes',
   ```

## Advantages vs. Creating New CPT

✅ **Preserves existing workflows** - Users continue using familiar post editor
✅ **Maintains post archives** - Posts appear in standard archives and feeds
✅ **No URL structure changes** - Existing permalinks remain intact
✅ **Simpler for editors** - No need to learn new post types
✅ **Plugin compatibility** - Works with SEO plugins, page builders, etc.

## When to Use This Approach

- ✅ Adding metadata to existing content types
- ✅ Enhancing posts without changing structure
- ✅ Site-specific custom fields for standard posts
- ✅ Editorial or workflow-related fields
- ✅ Extending third-party plugin post types

## When to Create a Custom Post Type Instead

- ❌ Content has different archive requirements
- ❌ Need custom permalinks or templates
- ❌ Content represents a distinct entity (products, events, portfolios)
- ❌ Requires different capabilities or permissions
- ❌ Should be separated from regular posts in admin

## Related Examples

- **[08-existing-post-type-json](../08-existing-post-type-json/)** - Same functionality using JSON configuration
- **[01-basic-cpt-array](../01-basic-cpt-array/)** - Creating a new custom post type instead
- **[05-complete-array-example](../05-complete-array-example/)** - Comprehensive example with all field types

## Troubleshooting

**Fields not showing?**
- Ensure the post type exists (check with `post_type_exists('post')`)
- Verify the plugin is activated
- Check for JavaScript errors in browser console

**Values not saving?**
- Check user permissions (`edit_posts` capability)
- Verify nonce is present in the form
- Look for PHP errors in debug log

**Conflicts with other plugins?**
- Use unique field names with a prefix
- Check if another plugin uses the same meta keys
- Test with all other plugins temporarily disabled

## Support

For issues or questions about this example:
- Check the [WP-CMF Documentation](../../docs/)
- Review the [main examples README](../README.md)
- Open an issue on GitHub

---

**Part of WP-CMF Examples** | [View All Examples](../README.md)
