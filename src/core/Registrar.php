<?php
/**
 * Registrar class for WP-CMF
 *
 * Handles WordPress hook binding and registration coordination.
 * Manages the actual registration of custom post types, settings pages, and fields.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core;

use Pedalcms\WpCmf\CPT\CustomPostType;
use Pedalcms\WpCmf\Settings\SettingsPage;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\FieldInterface;

/**
 * Registrar class - Handles WordPress hook binding
 *
 * Coordinates the registration of various WordPress components
 * including custom post types, admin pages, fields, and related hooks.
 */
class Registrar {

	/**
	 * Registered custom post types
	 *
	 * @var array<string, CustomPostType>
	 */
	private array $custom_post_types = array();

	/**
	 * Registered settings pages
	 *
	 * @var array<string, SettingsPage>
	 */
	private array $settings_pages = array();

	/**
	 * Registered field definitions
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $fields = array();

	/**
	 * Nested field names that should not be rendered separately
	 *
	 * @var array<string, array<string>>
	 */
	private array $nested_field_names = array();

	/**
	 * Whether hooks have been initialized
	 *
	 * @var bool
	 */
	private bool $hooks_initialized = false;

	/**
	 * Constructor
	 *
	 * @param bool $initialize_hooks Whether to initialize WordPress hooks immediately.
	 */
	public function __construct( bool $initialize_hooks = true ) {
		if ( $initialize_hooks ) {
			$this->init_hooks();
		}
	}

	/**
	 * Initialize WordPress hooks
	 *
	 * @return void
	 */
	public function init_hooks(): void {
		if ( $this->hooks_initialized ) {
			return;
		}

		// Only initialize hooks if WordPress functions are available
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		// Register custom post types
		add_action( 'init', array( $this, 'register_custom_post_types' ) );

		// Register admin pages and settings
		add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
		add_action( 'admin_init', array( $this, 'register_settings_fields' ) );

		// Enqueue field assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_field_assets' ) );

		// Register meta boxes for CPTs
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );

		// Handle form submissions
		add_action( 'save_post', array( $this, 'save_meta_box_data' ) );

		$this->hooks_initialized = true;
	}

	/**
	 * Add a custom post type for registration
	 *
	 * @param string               $post_type Post type slug.
	 * @param array<string, mixed> $args      Post type arguments.
	 * @return self
	 */
	public function add_custom_post_type( string $post_type, array $args ): self {
		$cpt                                   = CustomPostType::from_array( $post_type, $args );
		$this->custom_post_types[ $post_type ] = $cpt;
		return $this;
	}

	/**
	 * Add a CustomPostType instance directly
	 *
	 * @param CustomPostType $cpt CustomPostType instance.
	 * @return self
	 */
	public function add_cpt_instance( CustomPostType $cpt ): self {
		$this->custom_post_types[ $cpt->get_post_type() ] = $cpt;
		return $this;
	}

	/**
	 * Add a settings page for registration
	 *
	 * @param string               $page_id Page identifier.
	 * @param array<string, mixed> $args    Page arguments.
	 * @return self
	 */
	public function add_settings_page( string $page_id, array $args ): self {
		$settings_page                    = SettingsPage::from_array( $page_id, $args );
		$this->settings_pages[ $page_id ] = $settings_page;
		return $this;
	}

	/**
	 * Add a SettingsPage instance directly
	 *
	 * @param SettingsPage $page SettingsPage instance.
	 * @return self
	 */
	public function add_settings_page_instance( SettingsPage $page ): self {
		$this->settings_pages[ $page->get_page_id() ] = $page;
		return $this;
	}

	/**
	 * Add field definitions
	 *
	 * Accepts field configuration arrays and creates field instances using FieldFactory.
	 * Fields can be added as configuration arrays or as FieldInterface instances.
	 *
	 * Works for both new custom post types and existing WordPress post types (like 'post', 'page').
	 * The method automatically handles field registration regardless of whether the post type
	 * was created by WP-CMF or already exists in WordPress.
	 *
	 * @param string               $context Context (post_type slug or settings page ID).
	 * @param array<string, mixed> $fields  Field definitions (config arrays or FieldInterface instances).
	 * @return self
	 */
	public function add_fields( string $context, array $fields ): self {
		if ( ! isset( $this->fields[ $context ] ) ) {
			$this->fields[ $context ] = array();
		}

		// Process each field
		foreach ( $fields as $key => $field ) {
			// If it's already a FieldInterface instance, use it directly
			if ( $field instanceof FieldInterface ) {
				$field_name                              = $field->get_name();
				$this->fields[ $context ][ $field_name ] = $field;
			} elseif ( is_array( $field ) ) {
				// If it's a config array, create field using FieldFactory
				// Ensure the field has a name
				if ( empty( $field['name'] ) ) {
					$field['name'] = $key;
				}

				try {
					$field_instance                          = FieldFactory::create( $field );
					$field_name                              = $field_instance->get_name();
					$this->fields[ $context ][ $field_name ] = $field_instance;

					// If this is a container field, register nested fields too
					$this->register_nested_fields( $context, $field_instance );
				} catch ( \InvalidArgumentException $e ) {
					// If field creation fails, store the error or skip
					// For now, we'll skip invalid fields
					continue;
				}
			}
		}

		return $this;
	}

	/**
	 * Register nested fields from container fields
	 *
	 * Container fields (like tabs) contain other fields that need to be
	 * registered individually so they can save/load their own values.
	 *
	 * @param string         $context Context (post type or settings page ID).
	 * @param FieldInterface $field   Field instance to check.
	 * @return void
	 */
	protected function register_nested_fields( string $context, FieldInterface $field ): void {
		// Check if this is a container field
		if ( ! $field instanceof \Pedalcms\WpCmf\Field\ContainerFieldInterface ) {
			return;
		}

		// Get nested fields from the container
		$nested_fields = $field->get_nested_fields();

		// Register each nested field
		foreach ( $nested_fields as $nested_config ) {
			if ( empty( $nested_config['name'] ) ) {
				continue;
			}

			try {
				$nested_field = FieldFactory::create( $nested_config );
				$nested_name  = $nested_field->get_name();
				$this->fields[ $context ][ $nested_name ] = $nested_field;

				// Track this as a nested field so we don't render it separately
				if ( ! isset( $this->nested_field_names[ $context ] ) ) {
					$this->nested_field_names[ $context ] = array();
				}
				$this->nested_field_names[ $context ][] = $nested_name;

				// Recursively handle nested containers (if a container contains another container)
				$this->register_nested_fields( $context, $nested_field );
			} catch ( \InvalidArgumentException $e ) {
				// Skip invalid nested fields
				continue;
			}
		}
	}

	/**
	 * Register custom post types with WordPress
	 *
	 * This method is called on the 'init' hook, but can also be called
	 * directly if CPTs are added during or after the 'init' hook.
	 *
	 * @return void
	 */
	public function register_custom_post_types(): void {
		foreach ( $this->custom_post_types as $cpt ) {
			$cpt->register();
		}
	}

	/**
	 * Register admin pages with WordPress
	 *
	 * This method is called on the 'admin_menu' hook, but can also be called
	 * directly if pages are added during or after the 'admin_menu' hook.
	 *
	 * @return void
	 */
	public function register_admin_pages(): void {
		foreach ( $this->settings_pages as $page ) {
			$page->register();
		}
	}

	/**
	 * Register settings fields with WordPress
	 *
	 * Registers fields for settings pages using WordPress Settings API.
	 * This method is called on the 'admin_init' hook, but can also be called
	 * directly if fields are added during or after the 'admin_init' hook.
	 *
	 * Handles both custom settings pages and existing WordPress settings pages.
	 *
	 * @return void
	 */
	public function register_settings_fields(): void {
		if ( ! function_exists( 'register_setting' ) || ! function_exists( 'add_settings_section' ) || ! function_exists( 'add_settings_field' ) ) {
			return;
		}

		// Register fields for custom settings pages (those we created)
		foreach ( $this->settings_pages as $page_id => $page ) {
			// Check if this settings page has fields
			if ( empty( $this->fields[ $page_id ] ) ) {
				continue;
			}

			// Add a default section for the fields
			$section_id = $page_id . '_section';
			add_settings_section(
				$section_id,
				__( 'Settings', 'wp-cmf' ),
				'__return_empty_string',
				$page->get_menu_slug()
			);

			// Register each field
			foreach ( $this->fields[ $page_id ] as $field ) {
				if ( ! $field instanceof FieldInterface ) {
					continue;
				}

				$field_name  = $field->get_name();
				$option_name = $page_id . '_' . $field_name;
				$is_nested   = isset( $this->nested_field_names[ $page_id ] ) && in_array( $field_name, $this->nested_field_names[ $page_id ], true );

				// Always register settings with WordPress (even nested fields need to be registered to save)
				register_setting(
					$page->get_menu_slug(),
					$option_name,
					array(
						'sanitize_callback' => array( $field, 'sanitize' ),
					)
				);

				// Skip rendering nested fields as separate fields - they're rendered inside their container
				if ( $is_nested ) {
					continue;
				}

				// Add settings field (only for non-nested fields)
				add_settings_field(
					$field_name,
					$field->get_label(),
					array( $this, 'render_settings_field' ),
					$page->get_menu_slug(),
					$section_id,
					array(
						'field'       => $field,
						'option_name' => $option_name,
						'page_id'     => $page_id,
					)
				);
			}
		}

		// Register fields for existing settings pages (any registered settings page we didn't create)
		foreach ( $this->fields as $page_id => $field_definitions ) {
			// Skip if already handled above (custom settings pages we created)
			if ( isset( $this->settings_pages[ $page_id ] ) ) {
				continue;
			}

			// Skip if it's a CPT context
			if ( isset( $this->custom_post_types[ $page_id ] ) ) {
				continue;
			}

			// At this point, assume it's an existing settings page (built-in or from another plugin)
			// Check if this settings page has fields
			if ( empty( $field_definitions ) ) {
				continue;
			}

			// Register each field for the existing settings page
			foreach ( $field_definitions as $field ) {
				if ( ! $field instanceof FieldInterface ) {
					continue;
				}

				$field_name  = $field->get_name();
				$option_name = $page_id . '_' . $field_name;

				// Register setting with the built-in settings group
				register_setting(
					$page_id,
					$option_name,
					array(
						'sanitize_callback' => array( $field, 'sanitize' ),
					)
				);

				// Add settings field to the existing page's default section
				add_settings_field(
					$field_name,
					$field->get_label(),
					array( $this, 'render_settings_field' ),
					$page_id,
					'default',
					array(
						'field'       => $field,
						'option_name' => $option_name,
					)
				);
			}
		}
	}

	/**
	 * Render a settings field
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function render_settings_field( array $args ): void {
		if ( empty( $args['field'] ) || ! $args['field'] instanceof FieldInterface ) {
			return;
		}

		$field       = $args['field'];
		$option_name = $args['option_name'] ?? $field->get_name();

		// Container fields don't have their own values - they only render UI
		// Their nested fields handle their own value loading
		if ( $field instanceof \Pedalcms\WpCmf\Field\ContainerFieldInterface ) {
			// Pass page_id as context so container can construct correct option names
			$page_id = $args['page_id'] ?? null;
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field's render method handles escaping
			echo $field->render( $page_id );
			return;
		}

		// Get current value
		$value = function_exists( 'get_option' ) ? get_option( $option_name, '' ) : '';

		// Get the rendered field HTML
		$field_html = $field->render( $value );

		// Replace the field's name attribute with the option name
		// This ensures WordPress Settings API can save the value correctly
		$original_name = $field->get_name();
		$field_html    = str_replace(
			'name="' . $original_name . '"',
			'name="' . $option_name . '"',
			$field_html
		);

		// Also handle array names for checkboxes/multi-select
		$field_html = str_replace(
			'name="' . $original_name . '[]"',
			'name="' . $option_name . '[]"',
			$field_html
		);

		// Render field with correct name
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field's render method handles escaping
		echo $field_html;
	}

	/**
	 * Enqueue field assets (CSS and JS)
	 *
	 * Calls enqueue_assets() on all registered field instances,
	 * allowing fields to load their required stylesheets and scripts.
	 *
	 * @return void
	 */
	public function enqueue_field_assets(): void {
		// Get current screen to determine context
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		// Enqueue assets for fields in the current context
		foreach ( $this->fields as $context => $field_definitions ) {
			// Check if we're on a relevant screen for this context
			// For CPTs, check post_type; for settings pages, check base/id
			$is_relevant_screen = (
				$screen->post_type === $context ||
				$screen->base === $context ||
				$screen->id === $context ||
				strpos( $screen->id, $context ) !== false
			);

			if ( $is_relevant_screen ) {
				// If field definitions contain FieldInterface instances, call enqueue_assets
				foreach ( $field_definitions as $field ) {
					if ( is_object( $field ) && method_exists( $field, 'enqueue_assets' ) ) {
						$field->enqueue_assets();
					}
				}
			}
		}

		// Also enqueue common assets for all WP-CMF fields
		$this->enqueue_common_assets();
	}

	/**
	 * Enqueue common assets used by all fields
	 *
	 * Enqueues core WP-CMF CSS and JS files on admin screens where CMF fields are present.
	 *
	 * @return void
	 */
	protected function enqueue_common_assets(): void {
		if ( ! function_exists( 'wp_enqueue_style' ) || ! function_exists( 'wp_enqueue_script' ) ) {
			return;
		}

		// Only enqueue if we have fields registered
		if ( empty( $this->fields ) ) {
			return;
		}

		// Get the base path to WP-CMF assets
		$assets_url = $this->get_assets_url();
		$version    = $this->get_version();

		// Enqueue WP-CMF CSS
		wp_enqueue_style(
			'wp-cmf',
			$assets_url . 'css/wp-cmf.css',
			array(),
			$version,
			'all'
		);

		// Enqueue WP-CMF JS (depends on jQuery and wp-color-picker)
		wp_enqueue_script(
			'wp-cmf',
			$assets_url . 'js/wp-cmf.js',
			array( 'jquery', 'wp-color-picker' ),
			$version,
			true
		);

		// Enqueue color picker styles (required for ColorField)
		wp_enqueue_style( 'wp-color-picker' );

		// Hook for plugins/themes to add additional common WP-CMF assets
		if ( function_exists( 'do_action' ) ) {
			do_action( 'wp_cmf_enqueue_common_assets' );
		}
	}

	/**
	 * Get URL to WP-CMF assets directory
	 *
	 * @return string Assets URL with trailing slash.
	 */
	protected function get_assets_url(): string {
		// Get the directory containing this file (src/Core/)
		$dir = __DIR__;

		// Navigate up to src directory, then to assets
		$assets_dir = dirname( $dir ) . '/assets/';

		// Convert filesystem path to URL
		// Replace the WordPress installation path with the site URL
		if ( defined( 'ABSPATH' ) && function_exists( 'site_url' ) ) {
			// Normalize paths for comparison
			$abspath     = str_replace( '\\', '/', ABSPATH );
			$assets_path = str_replace( '\\', '/', $assets_dir );

			// Replace ABSPATH with site_url
			$assets_url = str_replace( $abspath, trailingslashit( site_url() ), $assets_path );

			return $assets_url;
		}

		// Fallback: try to construct URL using wp-content
		if ( defined( 'WP_CONTENT_DIR' ) && defined( 'WP_CONTENT_URL' ) ) {
			$content_dir = str_replace( '\\', '/', WP_CONTENT_DIR );
			$assets_path = str_replace( '\\', '/', $assets_dir );

			$assets_url = str_replace( $content_dir, WP_CONTENT_URL, $assets_path );

			return $assets_url;
		}

		return '';
	}

	/**
	 * Get WP-CMF version for cache busting
	 *
	 * @return string Version string.
	 */
	protected function get_version(): string {
		// Try to read version from composer.json
		$composer_json_path = dirname( dirname( __DIR__ ) ) . '/composer.json';

		if ( file_exists( $composer_json_path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading local file, not remote URL
			$composer_json = json_decode( file_get_contents( $composer_json_path ), true );
			if ( isset( $composer_json['version'] ) ) {
				return $composer_json['version'];
			}
		}

		// Fallback to a timestamp-based version for development
		return gmdate( 'YmdHis', filemtime( __FILE__ ) );
	}

	/**
	 * Register meta boxes for custom post types
	 *
	 * Creates meta boxes for fields associated with custom post types.
	 * Also handles fields added to existing WordPress post types.
	 * Supports multiple meta boxes per post type using MetaboxField containers.
	 *
	 * @return void
	 */
	public function register_meta_boxes(): void {
		if ( ! function_exists( 'add_meta_box' ) ) {
			return;
		}

		// Register meta boxes for our custom post types
		foreach ( $this->custom_post_types as $post_type => $cpt ) {
			// Check if this CPT has fields
			if ( empty( $this->fields[ $post_type ] ) ) {
				continue;
			}

			$this->register_post_type_meta_boxes( $post_type );
		}

		// Register meta boxes for existing post types that have fields
		// but aren't in our custom_post_types array
		foreach ( $this->fields as $post_type => $fields ) {
			// Skip if already handled above or if it's a settings page
			if ( isset( $this->custom_post_types[ $post_type ] ) || isset( $this->settings_pages[ $post_type ] ) ) {
				continue;
			}

			// Check if this is a valid post type
			if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( $post_type ) ) {
				continue;
			}

			$this->register_post_type_meta_boxes( $post_type );
		}
	}

	/**
	 * Register meta boxes for a specific post type
	 *
	 * Handles both MetaboxField containers (creates individual meta boxes)
	 * and regular fields (groups them into a default meta box).
	 *
	 * @param string $post_type Post type slug.
	 * @return void
	 */
	protected function register_post_type_meta_boxes( string $post_type ): void {
		if ( empty( $this->fields[ $post_type ] ) ) {
			return;
		}

		$metabox_fields  = [];
		$regular_fields  = [];
		$registered_metaboxes = [];

		// Separate MetaboxField containers from regular fields
		foreach ( $this->fields[ $post_type ] as $field ) {
			if ( ! $field instanceof FieldInterface ) {
				continue;
			}

			// Skip nested fields - they're rendered inside their container
			$field_name = $field->get_name();
			if ( isset( $this->nested_field_names[ $post_type ] ) && in_array( $field_name, $this->nested_field_names[ $post_type ], true ) ) {
				continue;
			}

			// Check if this is a MetaboxField
			if ( $field instanceof \Pedalcms\WpCmf\Field\fields\MetaboxField ) {
				$metabox_fields[] = $field;
			} else {
				$regular_fields[] = $field;
			}
		}

		// Register individual meta boxes for MetaboxField containers
		foreach ( $metabox_fields as $metabox_field ) {
			$metabox_id = $metabox_field->get_metabox_id();

			// Skip if this metabox ID was already registered
			if ( in_array( $metabox_id, $registered_metaboxes, true ) ) {
				continue;
			}

			add_meta_box(
				$metabox_id,
				$metabox_field->get_metabox_title(),
				array( $this, 'render_metabox_container' ),
				$post_type,
				$metabox_field->get_context(),
				$metabox_field->get_priority(),
				array( 'metabox_field' => $metabox_field )
			);

			$registered_metaboxes[] = $metabox_id;
		}

		// Register default meta box for regular fields (if any)
		if ( ! empty( $regular_fields ) ) {
			$post_type_obj = function_exists( 'get_post_type_object' ) ? get_post_type_object( $post_type ) : null;
			$label         = $post_type_obj && isset( $post_type_obj->labels->singular_name )
				? $post_type_obj->labels->singular_name
				: ucfirst( $post_type );

			add_meta_box(
				$post_type . '_cmf_fields',
				$label . ' Fields',
				array( $this, 'render_meta_box' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render a MetaboxField container
	 *
	 * This is called by WordPress for meta boxes created from MetaboxField definitions.
	 *
	 * @param \WP_Post $post Current post object.
	 * @param array    $args Additional arguments including the metabox_field.
	 * @return void
	 */
	public function render_metabox_container( $post, $args ): void {
		if ( ! isset( $args['args']['metabox_field'] ) ) {
			return;
		}

		$metabox_field = $args['args']['metabox_field'];

		if ( ! $metabox_field instanceof \Pedalcms\WpCmf\Field\fields\MetaboxField ) {
			return;
		}

		// Add nonce for security (once per post type)
		$post_type  = $post->post_type;
		$nonce_name = $post_type . '_fields_nonce';

		static $nonce_rendered = [];
		if ( ! isset( $nonce_rendered[ $post_type ] ) ) {
			if ( function_exists( 'wp_nonce_field' ) ) {
				wp_nonce_field( 'save_' . $post_type . '_fields', $nonce_name );
			}
			$nonce_rendered[ $post_type ] = true;
		}

		// Render the metabox field container
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field's render method handles escaping
		echo $metabox_field->render( null );
	}

	/**
	 * Render meta box content
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function render_meta_box( $post ): void {
		$post_type = $post->post_type;

		if ( empty( $this->fields[ $post_type ] ) ) {
			return;
		}

		// Add nonce for security
		if ( function_exists( 'wp_nonce_field' ) ) {
			wp_nonce_field( 'save_' . $post_type . '_fields', $post_type . '_fields_nonce' );
		}

		echo '<div class="wp-cmf-fields">';

		foreach ( $this->fields[ $post_type ] as $field ) {
			if ( ! $field instanceof FieldInterface ) {
				continue;
			}

			// Skip nested fields - they're rendered inside their container
			$field_name = $field->get_name();
			if ( isset( $this->nested_field_names[ $post_type ] ) && in_array( $field_name, $this->nested_field_names[ $post_type ], true ) ) {
				continue;
			}

			// Container fields don't have their own values - they only render UI
			// Their nested fields handle their own value loading
			if ( $field instanceof \Pedalcms\WpCmf\Field\ContainerFieldInterface ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field's render method handles escaping
				echo $field->render( null );
				continue;
			}

			// Get field value from post meta
			$value = function_exists( 'get_post_meta' )
				? get_post_meta( $post->ID, $field->get_name(), true )
				: '';

			// Render the field
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field's render method handles escaping
			echo $field->render( $value );
		}

		echo '</div>';
	}

	/**
	 * Save meta box data
	 *
	 * Saves field values from post edit screen to post meta.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta_box_data( int $post_id ): void {
		// Check if this is an autosave
		if ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if this is a revision
		if ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Get post type
		$post_type = function_exists( 'get_post_type' ) ? get_post_type( $post_id ) : '';
		if ( ! $post_type || empty( $this->fields[ $post_type ] ) ) {
			return;
		}

		// Verify nonce
		$nonce_name = $post_type . '_fields_nonce';
		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return;
		}

		if ( function_exists( 'wp_verify_nonce' ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), 'save_' . $post_type . '_fields' ) ) {
				return;
			}
		}

		// Check user permissions
		$post_type_object = function_exists( 'get_post_type_object' ) ? get_post_type_object( $post_type ) : null;
		$edit_cap         = $post_type_object && isset( $post_type_object->cap->edit_post )
			? $post_type_object->cap->edit_post
			: 'edit_post';

		if ( function_exists( 'current_user_can' ) && ! current_user_can( $edit_cap, $post_id ) ) {
			return;
		}

		// Save each field
		foreach ( $this->fields[ $post_type ] as $field ) {
			if ( ! $field instanceof FieldInterface ) {
				continue;
			}

			// Skip container fields - they don't store values
			// Their nested fields are registered separately and save independently
			if ( $field instanceof \Pedalcms\WpCmf\Field\ContainerFieldInterface ) {
				continue;
			}

			$field_name = $field->get_name();

			// Check if field value was submitted
			if ( ! isset( $_POST[ $field_name ] ) ) {
				continue;
			}

			// Get raw value (WordPress will handle sanitization via field's sanitize method)
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw_value = wp_unslash( $_POST[ $field_name ] );

			// Sanitize using field's sanitize method
			$sanitized_value = $field->sanitize( $raw_value );

			// Validate using field's validate method
			$validation_result = $field->validate( $sanitized_value );

			// Only save if validation passes
			if ( ! empty( $validation_result['valid'] ) ) {
				if ( function_exists( 'update_post_meta' ) ) {
					update_post_meta( $post_id, $field_name, $sanitized_value );
				}
			}
		}
	}

	/**
	 * Render settings page
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		// TODO: Implement settings page rendering
		// This will be implemented when field classes are available in Milestone 3
		echo '<div class="wrap"><h1>WP-CMF Settings</h1><p>Settings page rendering coming soon...</p></div>';
	}

	/**
	 * Get registered custom post types
	 *
	 * @return array<string, CustomPostType>
	 */
	public function get_custom_post_types(): array {
		return $this->custom_post_types;
	}

	/**
	 * Get registered settings pages
	 *
	 * @return array<string, SettingsPage>
	 */
	public function get_settings_pages(): array {
		return $this->settings_pages;
	}

	/**
	 * Get registered fields
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_fields(): array {
		return $this->fields;
	}

	/**
	 * Check if hooks are initialized
	 *
	 * @return bool
	 */
	public function are_hooks_initialized(): bool {
		return $this->hooks_initialized;
	}
}
