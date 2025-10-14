# Advanced CPT Configuration Example

This example demonstrates advanced custom post type registration with fully customized labels, detailed WordPress configuration, and professional-grade settings.

## What This Example Shows

- Complete custom labels control
- Advanced WordPress arguments
- Menu positioning and icons
- Custom rewrite rules
- REST API integration
- Professional event management setup

## The Code

```php
<?php
use Pedalcms\WpCmf\Core\Manager;

function register_events_cpt() {
    $manager = Manager::init();
    $registrar = $manager->get_registrar();

    $registrar->add_custom_post_type( 'event', [
        'labels' => [
            'name'          => 'Events',
            'singular_name' => 'Event',
            'add_new_item'  => 'Add New Event',
            'edit_item'     => 'Edit Event',
            'view_item'     => 'View Event',
            'search_items'  => 'Search Events',
            'not_found'     => 'No events found',
        ],
        'public'        => true,
        'has_archive'   => true,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'menu_icon'     => 'dashicons-calendar-alt',
        'menu_position' => 20,
        'rewrite'       => [
            'slug'       => 'events',
            'with_front' => false,
        ],
        'capability_type' => 'post',
        'show_in_rest'    => true,
    ] );
}

add_action( 'init', 'register_events_cpt' );
```

## Result

Creates a professional Events custom post type with:
- Custom calendar icon in the admin menu
- Menu positioned after "Pages" (position 20)
- Clean URLs: `/events/event-name` (no front base)
- Full REST API support for headless/block editor
- Custom fields support for additional metadata
- Complete archive functionality

## Advanced Features Demonstrated

### Custom Labels
- Complete control over all admin interface text
- Consistent user experience
- Professional presentation

### URL Configuration
```php
'rewrite' => [
    'slug'       => 'events',      // Custom URL base
    'with_front' => false,         // Remove permalink front base
]
```

### Menu Configuration
```php
'menu_icon'     => 'dashicons-calendar-alt',  // Calendar icon
'menu_position' => 20,                         // After "Pages"
```

### Modern WordPress Features
- `show_in_rest` enables Gutenberg and REST API
- `custom-fields` support for metadata
- Archive pages for event listings

## Use Cases

Perfect for:
- Event management systems
- Conference websites
- Meetup platforms
- Calendar applications
- Any date-based content system
