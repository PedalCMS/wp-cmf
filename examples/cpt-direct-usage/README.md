# Direct CustomPostType Usage Example

This example demonstrates the simplest way to register custom post types using WP-CMF's `CustomPostType` class directly.

## What This Example Shows

- Direct instantiation of `CustomPostType`
- Fluent interface method chaining
- Automatic label generation from singular/plural names
- Setting WordPress defaults with `set_defaults()`
- Customizing individual arguments and supports

## The Code

```php
<?php
use Pedalcms\WpCmf\CPT\CustomPostType;

function register_books_cpt() {
    $book_cpt = new CustomPostType( 'book' );

    $book_cpt->generate_labels( 'Book', 'Books' )
             ->set_defaults()
             ->set_arg( 'menu_icon', 'dashicons-book' )
             ->set_arg( 'has_archive', true )
             ->set_supports( [ 'title', 'editor', 'thumbnail', 'excerpt' ] );

    $book_cpt->register();
}

add_action( 'init', 'register_books_cpt' );
```

## Result

This will create a "Books" custom post type in WordPress with:
- Book/Books labels automatically generated
- Standard WordPress defaults applied
- Custom book icon in the admin menu
- Archive page support enabled
- Support for title, editor, thumbnail, and excerpt

## Key Methods Used

- `new CustomPostType( 'slug' )` - Create new CPT instance
- `generate_labels( $singular, $plural )` - Auto-generate WordPress labels
- `set_defaults()` - Apply sensible WordPress defaults
- `set_arg( $key, $value )` - Set individual WordPress arguments
- `set_supports( $array )` - Define which features the CPT supports
- `register()` - Register with WordPress
