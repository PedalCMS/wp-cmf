# Simple Example - JSON Configuration

This is a minimal example demonstrating WP-CMF basics using JSON configuration.
It provides the same capabilities as `01-simple-array` but with external JSON config.

## What This Example Creates

### Custom Post Type: Event
An "Events" post type with:
- **Event Notice** (custom_html) - Informational display banner
- **Event Date** (date, required) - When the event occurs
- **Location** (text) - Venue name/address
- **Capacity** (number) - Max attendees
- **Free Event** (checkbox) - Is it free?
- **Ticket Price** (number) - Cost if not free
- **Registration URL** (url) - Registration link
- **Contact Email** (email) - Event contact
- **Short Description** (textarea) - Brief description

### Taxonomy: Event Type
A hierarchical taxonomy for categorizing events:
- **Type Color** (color) - Color for event type badges
- **Icon Class** (text) - Dashicons class for this event type
- **Default Capacity** (number) - Default capacity for this type of event

### Taxonomy: Venue
A non-hierarchical taxonomy for event venues:
- **Full Address** (textarea) - Complete venue address
- **Venue Capacity** (number) - Maximum capacity of the venue
- **Venue Website** (url) - Venue's website
- **Contact Phone** (text) - Venue contact number

### Settings Page: Events Settings
A top-level settings page with:
- **Organization Name** (text)
- **Default Location** (text)
- **Default Capacity** (number)
- **Currency Symbol** (text)
- **Enable Registration** (checkbox)
- **Notification Email** (email)
- **Date Display Format** (radio)
- **Primary Color** (color)

## File Structure

```
02-simple-json/
├── example.php    # Plugin file (PHP loader)
├── config.json    # Configuration (JSON)
└── README.md      # This file
```

## Usage

### Loading JSON Config

```php
use Pedalcms\WpCmf\Core\Manager;

// From file path
Manager::init()->register_from_json( __DIR__ . '/config.json' );

// From JSON string (useful for database storage)
$json = '{"cpts":[...],"taxonomies":[...],"settings_pages":[...]}';
Manager::init()->register_from_json( $json );
```

### Retrieving Values

```php
// Get event meta
$date     = get_post_meta( $post_id, 'event_date', true );
$location = get_post_meta( $post_id, 'location', true );

// Get taxonomy term meta
$type_color = get_term_meta( $term_id, 'type_color', true );
$venue_capacity = get_term_meta( $term_id, 'venue_capacity', true );

// Get settings (pattern: {page_id}_{field_name})
$currency = get_option( 'events-settings_currency_symbol', '$' );
$color    = get_option( 'events-settings_primary_color', '#0073aa' );
```

## JSON vs Array Configuration

| Feature | JSON | Array |
|---------|------|-------|
| External file | ✅ Yes | ❌ No |
| Schema validation | ✅ Yes | ❌ No |
| PHP knowledge needed | ❌ No | ✅ Yes |
| PHP callbacks | ❌ No | ✅ Yes |
| Dynamic values | ❌ No | ✅ Yes |
| CI/CD friendly | ✅ Yes | ⚠️ Limited |

## JSON Schema Validation

WP-CMF validates JSON against its schema by default:

```php
// With validation (default)
Manager::init()->register_from_json( $path );

// Skip validation
Manager::init()->register_from_json( $path, false );
```

## For Advanced Features

See `advanced-json` example for:
- All 16 field types
- Tabs, Metaboxes, Groups, Repeaters
- Adding to existing post types
- Adding to existing taxonomies
- Adding to existing settings pages
