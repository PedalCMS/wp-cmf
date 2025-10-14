# Manager and Registrar Usage Example

This example demonstrates using the WP-CMF Manager and Registrar architecture to register multiple custom post types with array-based configuration.

## What This Example Shows

- Using the WP-CMF Manager singleton pattern
- Array-based CPT configuration
- Registering multiple CPTs efficiently
- Integration with the WP-CMF architecture

## The Code

```php
<?php
use Pedalcms\WpCmf\Core\Manager;

function register_multiple_cpts() {
    $manager = Manager::init();
    $registrar = $manager->get_registrar();

    $registrar->add_custom_post_type( 'portfolio', [
        'singular' => 'Portfolio Item',
        'plural'   => 'Portfolio Items',
        'public'   => true,
        'supports' => [ 'title', 'editor', 'thumbnail' ],
        'menu_icon' => 'dashicons-portfolio',
        'has_archive' => true,
        'rewrite' => [ 'slug' => 'portfolio' ],
    ] );

    $registrar->add_custom_post_type( 'testimonial', [
        'singular' => 'Testimonial',
        'plural'   => 'Testimonials',
        'public'   => true,
        'supports' => [ 'title', 'editor' ],
        'menu_icon' => 'dashicons-format-quote',
        'show_in_rest' => true,
    ] );
}

add_action( 'init', 'register_multiple_cpts' );
```

## Result

This will create two custom post types:

1. **Portfolio Items** - For showcasing work/projects
2. **Testimonials** - For client feedback and reviews

## Key Advantages

- **Centralized Management**: Uses WP-CMF's Manager singleton
- **Array Configuration**: Clean, readable configuration arrays
- **Batch Registration**: Register multiple CPTs in one function
- **Framework Integration**: Works seamlessly with WP-CMF architecture
- **Automatic Processing**: CPTs are created as CustomPostType instances internally

## Configuration Options

Each array can include:
- `singular`/`plural` - For automatic label generation
- `public` - Whether CPT is public
- `supports` - Which WordPress features to support
- `menu_icon` - Dashicon for admin menu
- `has_archive` - Enable archive pages
- `rewrite` - URL rewrite rules
- `show_in_rest` - Enable REST API support
