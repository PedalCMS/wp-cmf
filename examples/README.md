# WP-CMF Examples

This directory contains organized examples demonstrating various WP-CMF features and usage patterns. Each example is in its own folder with dedicated code and documentation.

## Available Examples

### 📁 `cpt-direct-usage/`
**Direct CustomPostType Usage**  
Demonstrates the simplest way to register custom post types using the `CustomPostType` class directly with a fluent interface.

**Features:**
- Direct CustomPostType instantiation
- Fluent interface method chaining  
- Automatic label generation
- WordPress defaults application

```php
$book_cpt = new CustomPostType( 'book' );
$book_cpt->generate_labels( 'Book', 'Books' )
         ->set_defaults()
         ->set_arg( 'menu_icon', 'dashicons-book' )
         ->register();
```

### 📁 `cpt-manager-usage/`
**Manager and Registrar Integration**  
Shows how to use the WP-CMF Manager and Registrar architecture for registering multiple CPTs with array-based configuration.

**Features:**
- WP-CMF Manager singleton pattern
- Array-based configuration
- Multiple CPT registration
- Framework integration

```php
$manager = Manager::init();
$manager->get_registrar()->add_custom_post_type( 'portfolio', [
    'singular' => 'Portfolio Item',
    'plural'   => 'Portfolio Items',
    'public'   => true,
    'supports' => [ 'title', 'editor', 'thumbnail' ],
] );
```

### 📁 `cpt-advanced-config/`
**Advanced Configuration**  
Demonstrates professional-grade custom post type registration with fully customized labels, advanced WordPress arguments, and detailed configuration.

**Features:**
- Complete custom labels control
- Advanced WordPress arguments
- Menu positioning and icons
- Custom rewrite rules
- REST API integration

```php
$registrar->add_custom_post_type( 'event', [
    'labels' => [
        'name'          => 'Events',
        'singular_name' => 'Event',
        'add_new_item'  => 'Add New Event',
        // ... more labels
    ],
    'menu_icon'     => 'dashicons-calendar-alt',
    'menu_position' => 20,
    'rewrite'       => [
        'slug'       => 'events',
        'with_front' => false,
    ],
] );
```

### 📁 `cpt-custom-capabilities/`
**Custom Capabilities & Permissions**  
Shows how to create custom post types with custom capability mapping for advanced permission control and role-based access management.

**Features:**
- Custom capability mapping
- Granular permission control
- Role-based access control
- Security through capability checking

```php
$cpt = CustomPostType::from_array( 'project', [
    'capability_type' => 'project',
    'map_meta_cap'    => true,
    'capabilities'    => [
        'edit_post'   => 'edit_project',
        'read_post'   => 'read_project',
        'delete_post' => 'delete_project',
        // ... more capabilities
    ],
] );
```

## Running the Examples

Each example folder contains:
- `example.php` - The working code
- `README.md` - Detailed documentation and explanation

To use any example:

1. **Include in your plugin:**
   ```php
   require_once __DIR__ . '/path/to/example/example.php';
   ```

2. **Or copy the relevant code** into your plugin's main file

3. **Ensure WP-CMF is loaded** via Composer autoloader

## Quick Start

The simplest way to get started:

```php
<?php
// Load WP-CMF
require_once __DIR__ . '/vendor/autoload.php';

use Pedalcms\WpCmf\CPT\CustomPostType;

// Create and register a CPT
add_action( 'init', function() {
    $cpt = new CustomPostType( 'book' );
    $cpt->generate_labels( 'Book', 'Books' )
         ->set_defaults()
         ->register();
} );
```

## Milestone 2 Feature 1 ✅

These examples demonstrate the complete implementation of **Milestone 2 Feature 1: Custom Post Type Registration**.

**Acceptance Criteria Met:**
- ✅ Register labels, args, supports
- ✅ Accepts array config  
- ✅ Example plugin registers CPT
- ✅ CPT appears in WP admin

**Test Coverage:** 13/13 tests passing (8 CustomPostType + 3 Manager + 2 Registrar tests)
