# WP-CMF Examples

This directory contains organized examples demonstrating various WP-CMF features and usage patterns. Each example is in its own folder with dedicated code and documentation.

## 🌟 Recommended Starting Points

### 1. **`plugin-array-config/`** - Complete Array-Based Configuration
**Best for:** New projects, learning WP-CMF, production use

Start here if you want to see a complete, real-world example with:
- Multiple CPTs with comprehensive fields
- Multiple settings pages
- All 11 field types in action
- Single configuration array
- Best practices throughout

### 2. **`cpt-with-metabox-fields/`** - Complete CPT with Metaboxes
**Best for:** Understanding CPT field integration

Complete example showing:
- Custom post type with 25+ fields
- Multiple metaboxes with different contexts
- Field saving and validation
- Frontend display patterns

### 3. **`settings-with-fields/`** - Complete Settings Page
**Best for:** WordPress Settings API integration

Production-ready settings page with:
- 20+ fields across organized sections
- WordPress Settings API integration
- Export/import functionality

## Available Examples

### Custom Post Type Examples

### Custom Post Type Examples

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

### 📁 `cpt-advanced-config/`
**Advanced Configuration**  
Demonstrates professional-grade custom post type registration with fully customized labels, advanced WordPress arguments, and detailed configuration.

**Features:**
- Complete custom labels control
- Advanced WordPress arguments
- Menu positioning and icons
- Custom rewrite rules
- REST API integration

### 📁 `cpt-custom-capabilities/`
**Custom Capabilities & Permissions**  
Shows how to create custom post types with custom capability mapping for advanced permission control and role-based access management.

**Features:**
- Custom capability mapping
- Granular permission control
- Role-based access control
- Security through capability checking

### Settings Page Examples

### 📁 `settings-page-basic/`
**Basic Settings Page**  
Demonstrates creating a simple top-level settings page with the SettingsPage class.

### 📁 `settings-page-submenu/`
**Submenu Pages**  
Shows how to create submenu pages under core WordPress menus.

### 📁 `settings-page-custom-render/`
**Advanced Rendering**  
Advanced rendering with tabs, widgets, and custom classes.

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

### 📁 `plugin-array-config/`
**Complete Array-Based Configuration** ⭐ RECOMMENDED  
Production-ready plugin using array-based registration:
- Book and Movie custom post types with 10+ fields each
- Library and Movie Catalog settings pages
- All 11 field types demonstrated
- Single configuration array for entire plugin
- 400+ lines of documented configuration examples
- Best practices for field naming, validation, defaults

**Perfect for:**
- New projects starting with WP-CMF
- Configuration-driven development
- Rapid prototyping
- Learning WP-CMF's capabilities

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

**New to WP-CMF?** Start with `plugin-array-config/` for a complete overview.

**Need a specific feature?** Check the examples above organized by category.

**Simplest possible usage:**

```php
<?php
// Load WP-CMF
require_once __DIR__ . '/vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

// Array-based configuration (Recommended)
add_action( 'init', function() {
    Manager::init()->register_from_array([
        'cpts' => [
            [
                'id' => 'book',
                'args' => [
                    'label' => 'Books',
                    'public' => true,
                ],
            ],
        ],
    ]);
});
```

## Running the Examples

Each example folder contains:
- `example.php` - Working code you can use directly
- `README.md` - Detailed documentation and explanation

**To use any example:**

1. **Copy to your plugin** or include directly
2. **Ensure Composer autoloader is loaded**
3. **Activate and test** in WordPress

## Example Count by Category

- **Complete Integration**: 3 examples (`plugin-array-config`, `cpt-with-metabox-fields`, `settings-with-fields`)
- **CPT Patterns**: 3 examples (direct, advanced, capabilities)
- **Settings Pages**: 3 examples (basic, submenu, custom render)
- **Field System**: 2 examples (custom assets, factory usage)

**Total: 11 examples** covering all major WP-CMF features

## Implementation Status

### ✅ Milestone 2 - Custom Post Types & Settings Pages
**Complete** - All features implemented with comprehensive examples and tests

### ✅ Milestone 3 - Field System
**Complete** - Full field API with 11 core field types and extensibility

### ✅ Milestone 4 Feature 1 - Array-Based Registration
**Complete** - Single-array configuration with automatic field creation

**Test Coverage:** 143/143 tests passing (455 assertions)
