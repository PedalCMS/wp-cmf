# Array-Based Configuration Example

Complete demonstration of WP-CMF's array-based configuration system. This example shows how to register custom post types, settings pages, and fields using a single configuration array.

## Features

This example demonstrates:

- **Array-based registration** via `Manager::register_from_array()`
- **Two custom post types**: Books and Movies with comprehensive fields
- **Two settings pages**: Library Settings and Movie Catalog Settings
- **All 11 field types** in action: text, textarea, select, checkbox, radio, number, email, url, date, password, color
- **Field validation** and sanitization
- **Single configuration array** for entire plugin setup

## What's Included

### Custom Post Types

#### 1. Book CPT
- **Fields**: ISBN, Author, Co-Authors, Genre, Pages, Publication Date, Publisher, Price, In Stock, Featured
- **Field Types**: text, textarea, select, number, date, checkbox
- **Features**: Full CRUD support, REST API enabled, archive pages

#### 2. Movie CPT
- **Fields**: Director, Release Year, MPAA Rating, Runtime, Genres (multiple), Streaming URL
- **Field Types**: text, number, select, checkbox (multiple), url
- **Features**: Video integration, genre taxonomy alternative

### Settings Pages

#### 1. Library Settings
- **Fields**: Library Name, Contact Email, Website, Theme Color, Enable Reviews, Books Per Page, Sort Order, Featured Categories, Opening Hours
- **Field Types**: text, email, url, color, checkbox, number, radio, checkbox (multiple), textarea
- **Location**: Top-level admin menu

#### 2. Movie Catalog Settings
- **Fields**: Enable Movies, Movies Per Page, Embed Player, Poster Height
- **Field Types**: checkbox, number
- **Location**: Top-level admin menu

## Code Structure

```php
$config = array(
	'cpts' => array(
		array(
			'id'     => 'book',
			'args'   => array( /* register_post_type args */ ),
			'fields' => array( /* field configurations */ ),
		),
		// More CPTs...
	),
	'settings_pages' => array(
		array(
			'id'         => 'library-settings',
			'page_title' => 'Library Settings',
			// ...more page config
			'fields'     => array( /* field configurations */ ),
		),
		// More pages...
	),
);

Manager::init()->register_from_array( $config );
```

## Field Configuration Format

Each field in the `fields` array follows this structure:

```php
array(
	'name'        => 'field_name',        // Required: Unique field identifier
	'type'        => 'text',              // Required: Field type
	'label'       => 'Field Label',       // Optional: Display label
	'description' => 'Help text',         // Optional: Field description
	'required'    => true,                // Optional: Make field required
	'default'     => 'default value',     // Optional: Default value
	// Type-specific options:
	'placeholder' => 'Enter text...',     // For text inputs
	'min'         => 0,                   // For number/date fields
	'max'         => 100,                 // For number/date fields
	'step'        => 0.01,                // For number fields
	'rows'        => 5,                   // For textarea
	'options'     => array(               // For select/radio/checkbox
		'key1' => 'Label 1',
		'key2' => 'Label 2',
	),
	'multiple'    => true,                // For checkbox/select
)
```

## CPT Configuration Format

```php
array(
	'id'     => 'post_type_slug',         // Required
	'args'   => array(
		'singular'        => 'Book',
		'plural'          => 'Books',
		'public'          => true,
		'has_archive'     => true,
		'show_in_rest'    => true,        // Enable Gutenberg
		'menu_icon'       => 'dashicons-book',
		'supports'        => array( 'title', 'editor' ),
		'rewrite'         => array( 'slug' => 'books' ),
	),
	'fields' => array( /* field configs */ ),
)
```

## Settings Page Configuration Format

```php
array(
	'id'         => 'page-id',            // Required
	'page_title' => 'Page Title',        // Page <title>
	'menu_title' => 'Menu Label',        // Admin menu label
	'capability' => 'manage_options',    // Required capability
	'menu_slug'  => 'page-slug',         // URL slug
	'icon_url'   => 'dashicons-admin-generic',
	'position'   => 60,                  // Menu position
	'fields'     => array( /* field configs */ ),
)
```

## Usage Instructions

### Installation

1. Copy this example to `wp-content/plugins/wp-cmf-array-example/`
2. Ensure WP-CMF is installed via Composer
3. Activate the plugin in WordPress admin

### Accessing Features

**Custom Post Types**:
- Navigate to "Books" or "Movies" in the admin menu
- Create new entries and fill in the metabox fields
- Fields are automatically saved with validation

**Settings Pages**:
- Navigate to "Library" or "Movies" in the admin menu
- Configure settings using the generated forms
- Values are saved to WordPress options table

### Retrieving Data

**CPT Fields** (post meta):
```php
$isbn = get_post_meta( $post_id, 'book_isbn', true );
$author = get_post_meta( $post_id, 'book_author', true );
$genres = get_post_meta( $post_id, 'movie_genres', true ); // Array
```

**Settings** (options):
```php
$library_name = get_option( 'library-settings_library_name' );
$theme_color = get_option( 'library-settings_library_theme_color' );
$per_page = get_option( 'library-settings_library_books_per_page', 12 );
```

## Advantages of Array Configuration

### 1. **Centralized Configuration**
All post types, pages, and fields defined in one place.

### 2. **Easy to Read**
Clear hierarchical structure makes it easy to understand relationships.

### 3. **Version Control Friendly**
Configuration can be easily tracked in Git.

### 4. **Portable**
Move configuration between projects by copying the array.

### 5. **Dynamic Configuration**
Load configuration from files, databases, or external APIs:
```php
$config = include 'config/cmf-config.php';
// or
$config = json_decode( file_get_contents( 'config.json' ), true );
Manager::init()->register_from_array( $config );
```

### 6. **Environment-Specific**
Easily switch configurations based on environment:
```php
$config = WP_ENV === 'production' 
	? include 'config/production.php'
	: include 'config/development.php';
```

## Validation and Sanitization

All fields are automatically:
- **Sanitized** using field-specific sanitization methods
- **Validated** according to field type and configuration
- **Escaped** on output for security

### Custom Validation

Add validation rules to fields:
```php
array(
	'name'     => 'email',
	'type'     => 'email',
	'required' => true,        // Must not be empty
)

array(
	'name'  => 'username',
	'type'  => 'text',
	'min'   => 3,              // Minimum length
	'max'   => 20,             // Maximum length
	'pattern' => '^[a-z0-9_]+$', // Regex pattern
)
```

## Extending This Example

### Add More CPTs
```php
$config['cpts'][] = array(
	'id'   => 'event',
	'args' => array( /* ... */ ),
	'fields' => array( /* ... */ ),
);
```

### Add More Settings Pages
```php
$config['settings_pages'][] = array(
	'id' => 'advanced-settings',
	// ...
);
```

### Use Custom Field Types
```php
// Register custom field type first
Manager::init()->register_field_type( 'slider', CustomSliderField::class );

// Then use in config
array(
	'name' => 'volume',
	'type' => 'slider',
	'min'  => 0,
	'max'  => 100,
)
```

## Best Practices

1. **Prefix Field Names**: Use a unique prefix for all field names to avoid conflicts
   ```php
   'name' => 'myprefix_field_name'
   ```

2. **Group Related Fields**: Organize fields logically in the array

3. **Provide Descriptions**: Always include helpful descriptions for settings

4. **Use Sensible Defaults**: Provide default values for optional settings

5. **Validate User Input**: Use built-in validation or add custom rules

6. **Keep Arrays Manageable**: For very large configs, split into multiple files:
   ```php
   $config = array(
   	'cpts'           => require __DIR__ . '/config/cpts.php',
   	'settings_pages' => require __DIR__ . '/config/settings.php',
   );
   ```

7. **Document Your Config**: Add comments explaining complex configurations

8. **Test Thoroughly**: Verify all fields save and retrieve data correctly

## Related Examples

- **[Field Factory Usage](../field-factory-usage/)** - Dynamic field creation
- **[CPT Direct Usage](../cpt-direct-usage/)** - Object-oriented CPT registration
- **[Settings Page Basic](../settings-page-basic/)** - Simple settings page
- **[CPT with Metabox Fields](../cpt-with-metabox-fields/)** - Detailed metabox implementation
- **[Settings with Fields](../settings-with-fields/)** - Advanced settings with fields

## Next Steps

- **Milestone 4 Feature 2**: JSON-based configuration (coming soon)
- **Custom Field Types**: Create specialized fields for your needs
- **Field Groups**: Organize fields into collapsible groups
- **Conditional Fields**: Show/hide fields based on other field values
- **Import/Export**: Build data migration tools
