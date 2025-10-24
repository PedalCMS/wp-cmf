# Custom Post Type with Metabox Fields Example

This example demonstrates a **production-ready custom post type (Book)** with comprehensive metabox fields organized across multiple contexts.

## Features

### 🎯 Complete Implementation
- **Custom Post Type** registration (Book CPT)
- **4 Metaboxes** with different contexts and priorities
- **25+ Fields** demonstrating all field types
- **Field Organization** by logical grouping
- **Validation & Sanitization** for all inputs
- **Nonce Security** for form submissions
- **Data Retrieval** for frontend display
- **WordPress Standards** compliant

### 📦 Metabox Organization

#### 1. Book Details (Normal Context, High Priority)
Primary book information in the main content area:
- **ISBN** (text with pattern validation)
- **Author Name** (text, required)
- **Co-Authors** (textarea)
- **Genre** (select dropdown)
- **Number of Pages** (number with range)
- **Language** (select dropdown)
- **Series Name** (text)
- **Series Number** (number)

#### 2. Pricing & Availability (Sidebar)
Sales and inventory information in the sidebar:
- **Price** (number with decimals)
- **Sale Price** (number)
- **In Stock** (checkbox)
- **Stock Count** (number)

#### 3. Publication Information (Normal Context)
Publishing details in the main area:
- **Publisher** (text)
- **Publication Date** (date picker)
- **Edition** (text)
- **Available Formats** (multiple checkboxes)
- **Awards & Recognition** (textarea)

#### 4. Additional Information (Advanced Context)
Supplementary data below the main content:
- **Target Audience** (radio buttons)
- **Content Rating** (select)
- **Featured Book** (checkbox)
- **Bestseller** (checkbox)
- **Keywords** (text, comma-separated)
- **Book Website** (URL with validation)

## Code Structure

```php
// 1. Initialize WP-CMF
$manager = Manager::init();
$registrar = $manager->get_registrar();

// 2. Register Custom Post Type
add_action('init', function() use ($registrar) {
    $registrar->add_custom_post_type([
        'id' => 'book',
        'args' => [
            'labels' => [...],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'dashicons-book',
        ],
    ]);
});

// 3. Register Metaboxes
add_action('add_meta_boxes', function() {
    add_meta_box(
        'book_details',
        'Book Details',
        'render_book_details_fields',
        'book',
        'normal',
        'high'
    );
});

// 4. Save Metadata
add_action('save_post_book', function($post_id) {
    // Verify nonce, check autosave, check permissions
    save_book_metadata($post_id);
}, 10, 1);
```

## Field Creation Pattern

### Creating Fields
```php
$fields = FieldFactory::create_multiple([
    'isbn' => [
        'type'        => 'text',
        'label'       => 'ISBN',
        'description' => 'International Standard Book Number',
        'placeholder' => '978-3-16-148410-0',
        'pattern'     => '[\d\-]+',
    ],
    'author' => [
        'type'     => 'text',
        'label'    => 'Author Name',
        'required' => true,
    ],
    // ... more fields
]);
```

### Rendering Fields
```php
foreach ($fields as $field) {
    $field_name = $field->get_name();
    $value = get_post_meta($post->ID, '_book_' . $field_name, true);

    // Modify field name for proper form submission
    $html = $field->render($value);
    $html = str_replace(
        'name="' . $field_name . '"',
        'name="book_meta[' . $field_name . ']"',
        $html
    );

    echo $html;
}
```

### Saving Fields
```php
function save_book_metadata($post_id) {
    if (!isset($_POST['book_meta']) || !is_array($_POST['book_meta'])) {
        return;
    }

    // Create fields for sanitization
    $fields = FieldFactory::create_multiple($field_configs);

    foreach ($_POST['book_meta'] as $field_name => $value) {
        if (!isset($fields[$field_name])) {
            continue;
        }

        $field = $fields[$field_name];

        // Sanitize and validate
        $sanitized = $field->sanitize($value);
        $validation = $field->validate($sanitized);

        // Save if valid
        if ($validation['valid']) {
            update_post_meta($post_id, '_book_' . $field_name, $sanitized);
        }
    }
}
```

## Metabox Contexts

### Normal Context (Main Content Area)
```php
add_meta_box(
    'metabox_id',
    'Metabox Title',
    'render_callback',
    'book',          // Post type
    'normal',        // Context: above/below editor
    'high'           // Priority: high/default/low
);
```

### Side Context (Sidebar)
```php
add_meta_box(
    'metabox_id',
    'Metabox Title',
    'render_callback',
    'book',
    'side',          // Context: sidebar
    'default'        // Priority
);
```

### Advanced Context (Below Normal)
```php
add_meta_box(
    'metabox_id',
    'Metabox Title',
    'render_callback',
    'book',
    'advanced',      // Context: below normal
    'low'            // Priority
);
```

## Security Implementation

### Nonce Field
```php
// Output in metabox
wp_nonce_field('save_book_meta', 'book_meta_nonce');

// Verify on save
if (!isset($_POST['book_meta_nonce']) ||
    !wp_verify_nonce($_POST['book_meta_nonce'], 'save_book_meta')) {
    return;
}
```

### Autosave Check
```php
if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
}
```

### Permission Check
```php
if (!current_user_can('edit_post', $post_id)) {
    return;
}
```

### Data Sanitization
All fields automatically sanitize through their `sanitize()` methods:
```php
$sanitized_value = $field->sanitize($value);
```

### Data Validation
All fields validate before saving:
```php
$validation = $field->validate($sanitized_value);
if ($validation['valid']) {
    update_post_meta($post_id, $meta_key, $sanitized_value);
}
```

## Retrieving Metadata

### In Admin (Edit Screen)
```php
$value = get_post_meta($post->ID, '_book_author', true);
```

### In Frontend (Template)
```php
// In single-book.php template
$author = get_post_meta(get_the_ID(), '_book_author', true);
$isbn = get_post_meta(get_the_ID(), '_book_isbn', true);
$price = get_post_meta(get_the_ID(), '_book_price', true);

if ($author) {
    echo '<p><strong>Author:</strong> ' . esc_html($author) . '</p>';
}
```

### Using Helper Function
```php
display_book_metadata(get_the_ID());
```

## Field Examples by Type

### Text Field with Pattern
```php
'isbn' => [
    'type'        => 'text',
    'label'       => 'ISBN',
    'pattern'     => '[\d\-]+',
    'placeholder' => '978-3-16-148410-0',
],
```

### Required Field
```php
'author' => [
    'type'     => 'text',
    'label'    => 'Author Name',
    'required' => true,
],
```

### Number with Range
```php
'pages' => [
    'type'  => 'number',
    'label' => 'Number of Pages',
    'min'   => 1,
    'max'   => 10000,
    'step'  => 1,
],
```

### Select Dropdown
```php
'genre' => [
    'type'    => 'select',
    'label'   => 'Genre',
    'options' => [
        'fiction'     => 'Fiction',
        'non-fiction' => 'Non-Fiction',
        'mystery'     => 'Mystery',
        // ... more options
    ],
],
```

### Multiple Checkboxes
```php
'format' => [
    'type'    => 'checkbox',
    'label'   => 'Available Formats',
    'options' => [
        'hardcover' => 'Hardcover',
        'paperback' => 'Paperback',
        'ebook'     => 'E-Book',
        'audiobook' => 'Audiobook',
    ],
    'layout' => 'stacked',
],
```

### Radio Buttons
```php
'target_audience' => [
    'type'    => 'radio',
    'label'   => 'Target Audience',
    'options' => [
        'children'    => 'Children (0-12)',
        'teen'        => 'Teen (13-17)',
        'young-adult' => 'Young Adult (18-24)',
        'adult'       => 'Adult (25+)',
    ],
    'layout' => 'stacked',
],
```

### Date Field
```php
'publication_date' => [
    'type'  => 'date',
    'label' => 'Publication Date',
],
```

### URL Field
```php
'website' => [
    'type'        => 'url',
    'label'       => 'Book Website',
    'placeholder' => 'https://example.com',
],
```

### Textarea
```php
'awards' => [
    'type'        => 'textarea',
    'label'       => 'Awards & Recognition',
    'rows'        => 3,
    'placeholder' => 'e.g., Pulitzer Prize',
],
```

## Frontend Display

### Single Book Template
Create `single-book.php` in your theme:

```php
<?php get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </header>

    <div class="entry-content">
        <?php the_content(); ?>

        <!-- Display book metadata -->
        <?php display_book_metadata(); ?>
    </div>

    <?php if (has_post_thumbnail()): ?>
        <div class="book-cover">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>
</article>

<?php get_footer(); ?>
```

### Archive Template
Create `archive-book.php`:

```php
<?php get_header(); ?>

<header class="page-header">
    <h1 class="page-title">Book Library</h1>
</header>

<?php if (have_posts()): ?>
    <div class="book-grid">
        <?php while (have_posts()): the_post(); ?>
            <article id="post-<?php the_ID(); ?>">
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                <?php endif; ?>

                <h2>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <?php
                $author = get_post_meta(get_the_ID(), '_book_author', true);
                $price = get_post_meta(get_the_ID(), '_book_price', true);
                ?>

                <?php if ($author): ?>
                    <p class="author">by <?php echo esc_html($author); ?></p>
                <?php endif; ?>

                <?php if ($price): ?>
                    <p class="price">$<?php echo esc_html(number_format($price, 2)); ?></p>
                <?php endif; ?>

                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; ?>
    </div>

    <?php the_posts_navigation(); ?>
<?php else: ?>
    <p>No books found.</p>
<?php endif; ?>

<?php get_footer(); ?>
```

## Advanced Usage

### Conditional Fields
Show fields based on other field values:

```php
// Add this JavaScript to your metabox
<script>
jQuery(document).ready(function($) {
    $('select[name="book_meta[genre]"]').on('change', function() {
        if ($(this).val() === 'fiction') {
            $('.series-fields').show();
        } else {
            $('.series-fields').hide();
        }
    }).trigger('change');
});
</script>
```

### Field Groups
Organize fields into collapsible groups:

```php
<div class="field-group">
    <h4 class="field-group-title">Series Information</h4>
    <div class="field-group-content">
        <?php echo $series_name_field->render($series_name); ?>
        <?php echo $series_number_field->render($series_number); ?>
    </div>
</div>
```

### Repeater Fields
Allow multiple values for a field:

```php
// Store as serialized array
$co_authors = get_post_meta($post_id, '_book_co_authors', true);
if (!is_array($co_authors)) {
    $co_authors = [];
}

foreach ($co_authors as $index => $author) {
    echo '<input type="text" name="book_meta[co_authors][]" value="' .
         esc_attr($author) . '">';
}
```

## Best Practices

1. ✅ **Use meta key prefix** (_book_) to avoid conflicts
2. ✅ **Always verify nonce** before saving
3. ✅ **Check autosave** to prevent data loss
4. ✅ **Validate permissions** before saving
5. ✅ **Sanitize all input** using field sanitizers
6. ✅ **Validate before saving** using field validators
7. ✅ **Escape output** in templates
8. ✅ **Use proper metabox contexts** for organization
9. ✅ **Set appropriate priorities** for metabox ordering
10. ✅ **Provide helpful descriptions** for all fields

## Customization

### Custom Metabox Styling
```css
#book_details .inside {
    padding: 20px;
}

#book_details .form-table th {
    width: 200px;
    font-weight: 600;
}

#book_pricing {
    background: #f9f9f9;
    padding: 15px;
}
```

### Add Tooltips
```php
echo '<span class="tooltip" title="' . esc_attr($help_text) . '">?</span>';
```

### Custom Validation Messages
```php
$validation = $field->validate($value);
if (!$validation['valid']) {
    add_settings_error(
        'book_meta',
        'invalid_' . $field_name,
        implode(', ', $validation['errors']),
        'error'
    );
}
```

## Related Examples

- **[CPT Direct Usage](../cpt-direct-usage/)** - Basic CPT registration
- **[CPT Advanced Config](../cpt-advanced-config/)** - Advanced CPT setup
- **[Settings with Fields](../settings-with-fields/)** - Settings page fields
- **[Field Factory Usage](../field-factory-usage/)** - FieldFactory patterns

## Next Steps

- Add **REST API support** for fields
- Implement **Gutenberg blocks** for book display
- Add **custom taxonomies** (author, series, genre)
- Create **advanced search** by metadata
- Add **import/export** functionality for books
- Implement **inventory management** system
