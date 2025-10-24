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

## Settings Page Examples

### 📁 `settings-page-basic/`
**Basic Settings Page**  
Demonstrates creating a simple top-level settings page with the SettingsPage class.

### 📁 `settings-page-submenu/`
**Submenu Pages**  
Shows how to create submenu pages under core WordPress menus.

### 📁 `settings-page-custom-render/`
**Advanced Rendering**  
Advanced rendering with tabs, widgets, and custom classes.

### 📁 `settings-page-manager-usage/`
**Manager Patterns**  
Manager patterns and dynamic page creation.

## Field Examples

### 📁 `field-custom-assets/`
**Custom Field Assets**  
Custom field types with CSS/JS asset enqueuing, including:
- ColorField with WordPress color picker
- Custom slider field with custom assets
- Common assets hook demonstration

### 📁 `field-factory-usage/`
**FieldFactory Patterns**  
FieldFactory patterns and dynamic field creation:
- Creating fields from configuration arrays
- Registering custom field types
- Batch field creation
- Manager integration
- Error handling

## Complete Integration Examples

### 📁 `settings-with-fields/`
**Complete Settings Page with Fields**  
Production-ready settings page demonstrating:
- 20+ fields across 4 organized sections
- All 11 field types in action
- WordPress Settings API integration
- Field validation and sanitization
- Section organization with descriptions
- Settings export/import (demo)
- Custom rendering and styling
- Nonce security

**Features:**
- General Settings (site name, tagline, welcome message, features, language)
- Appearance Settings (theme style, colors, items per page, animations)
- Email Settings (admin email, support email, templates, footer)
- Advanced Settings (API key, endpoint, cache, debug mode, data retention)

### 📁 `cpt-with-metabox-fields/`
**Custom Post Type with Metabox Fields**  
Complete Book CPT with comprehensive metadata:
- Custom post type registration (Book)
- 4 metaboxes with different contexts (normal, side, advanced)
- 25+ fields demonstrating all field types
- Field organization by logical grouping
- Validation and sanitization
- Nonce security
- Data retrieval for frontend display
- WordPress standards compliant

**Metaboxes:**
- Book Details (ISBN, author, genre, pages, language, series)
- Pricing & Availability (price, sale price, stock)
- Publication Information (publisher, date, edition, formats, awards)
- Additional Information (audience, rating, featured, keywords, website)

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

## Implementation Status

### ✅ Milestone 2 - Custom Post Types & Settings Pages
**Complete** - All features implemented with comprehensive examples and tests
- 4 CPT examples demonstrating various configuration approaches
- 4 Settings page examples showing different use cases

### ✅ Milestone 3 - Field System
**Complete** - Full field API with extensibility
- 11 core field types (Text, Textarea, Select, Checkbox, Radio, Number, Email, URL, Date, Password, Color)
- FieldFactory for dynamic field creation
- Custom field type registration
- Asset enqueuing system
- 2 comprehensive field examples

**Test Coverage:** 130/130 tests passing (414 assertions)
