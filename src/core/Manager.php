<?php
/**
 * Manager class for WP-CMF
 *
 * Central registry and bootstrap for the Content Modeling Framework.
 * Coordinates registration of custom post types, settings pages, and fields.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core;

use Pedalcms\WpCmf\Core\Registrar;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * Manager class - Central coordination point for WP-CMF
 *
 * Provides a singleton pattern for managing registration and configuration
 * of custom post types, settings pages, and field definitions.
 */
class Manager {

	/**
	 * The singleton instance
	 *
	 * @var Manager|null
	 */
	private static ?Manager $instance = null;

	/**
	 * The registrar instance
	 *
	 * @var Registrar
	 */
	private Registrar $registrar;

	/**
	 * Configuration options
	 *
	 * @var array<string, mixed>
	 */
	private array $options;

	/**
	 * Private constructor to prevent direct instantiation
	 *
	 * @param array<string, mixed> $options Configuration options.
	 */
	private function __construct( array $options = array() ) {
		$this->options   = $options;
		$this->registrar = new Registrar( function_exists( 'add_action' ) );
		$this->load_textdomain();
	}

	/**
	 * Get the singleton instance
	 *
	 * @param array<string, mixed> $options Configuration options (only used on first call).
	 * @return Manager The singleton instance.
	 */
	public static function init( array $options = array() ): Manager {
		if ( null === self::$instance ) {
			self::$instance = new self( $options );
		}

		return self::$instance;
	}

	/**
	 * Load plugin text domain for translations
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain(
				'wp-cmf',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/../../languages'
			);
		}
	}

	/**
	 * Get the registrar instance
	 *
	 * @return Registrar The registrar instance.
	 */
	public function get_registrar(): Registrar {
		return $this->registrar;
	}

	/**
	 * Register configuration from array
	 *
	 * Accepts a configuration array and registers custom post types,
	 * settings pages, and their associated fields.
	 *
	 * Expected structure:
	 * <code>
	 * array(
	 *   'cpts' => array(
	 *     array(
	 *       'id' => 'book',  // For new CPTs, include 'args'
	 *       'args' => array('label' => 'Books', 'supports' => array('title', 'editor')),
	 *       'fields' => array( ... field definitions ... )
	 *     ),
	 *     array(
	 *       'id' => 'post',  // For existing post types, omit 'args' or leave empty
	 *       'fields' => array( ... field definitions ... )  // Fields will be added to existing type
	 *     )
	 *   ),
	 *   'settings_pages' => array(
	 *     array(
	 *       'id' => 'my-plugin-settings',  // For new settings pages, include 'args'
	 *       'title' => 'My Plugin',
	 *       'menu_title' => 'My Plugin',
	 *       'capability' => 'manage_options',
	 *       'slug' => 'my-plugin',
	 *       'fields' => array( ... field definitions ... )
	 *     ),
	 *     array(
	 *       'id' => 'general',  // For existing WordPress settings pages, omit 'args'
	 *       'fields' => array( ... field definitions ... )  // Fields will be added to existing page
	 *     )
	 *   )
	 * )
	 * </code>
	 *
	 * Note: The system automatically detects existing post types and settings pages.
	 * - For post types: If 'id' matches an existing post type (e.g., 'post', 'page'), only fields are added.
	 * - For settings: If 'id' matches a built-in settings page (e.g., 'general', 'reading'), only fields are added.
	 *
	 * @param array<string, mixed> $config Configuration array containing CPTs, settings pages, and fields.
	 * @return self
	 * @throws \InvalidArgumentException If configuration is invalid.
	 */
	public function register_from_array( array $config ): self {
		// Register custom post types
		if ( ! empty( $config['cpts'] ) && is_array( $config['cpts'] ) ) {
			foreach ( $config['cpts'] as $cpt_config ) {
				$this->register_cpt_from_array( $cpt_config );
			}
		}

		// Register settings pages
		if ( ! empty( $config['settings_pages'] ) && is_array( $config['settings_pages'] ) ) {
			foreach ( $config['settings_pages'] as $page_config ) {
				$this->register_settings_page_from_array( $page_config );
			}
		}

		// If we're already past certain hooks, we need to immediately register
		// instead of waiting for the hooks to fire (which won't happen again)
		if ( function_exists( 'did_action' ) ) {
			// Register CPTs if 'init' has already fired
			if ( did_action( 'init' ) ) {
				$this->registrar->register_custom_post_types();
			}

			// Register admin pages if 'admin_menu' has already fired
			if ( did_action( 'admin_menu' ) ) {
				$this->registrar->register_admin_pages();
			}

			// Register settings fields if 'admin_init' has already fired
			if ( did_action( 'admin_init' ) ) {
				$this->registrar->register_settings_fields();
			}
		}

		return $this;
	}

	/**
	 * Register a custom post type from array configuration
	 *
	 * Automatically detects if the post type already exists and only adds fields,
	 * or creates a new post type if it doesn't exist.
	 *
	 * @param array<string, mixed> $config CPT configuration.
	 * @return void
	 * @throws \InvalidArgumentException If required fields are missing.
	 */
	private function register_cpt_from_array( array $config ): void {
		if ( empty( $config['id'] ) ) {
			throw new \InvalidArgumentException( 'CPT configuration must include "id".' );
		}

		$post_type = $config['id'];
		$args      = $config['args'] ?? array();
		$fields    = $config['fields'] ?? array();

		// Check if this is an existing post type
		$is_existing = function_exists( 'post_type_exists' ) && post_type_exists( $post_type );

		// Only register new CPT if it doesn't already exist and args are provided
		if ( ! $is_existing && ! empty( $args ) ) {
			$this->registrar->add_custom_post_type( $post_type, $args );
		}

		// Register fields if provided (works for both new and existing post types)
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			$this->registrar->add_fields( $post_type, $fields );
		}
	}

	/**
	 * Register a settings page from array configuration
	 *
	 * Automatically detects if this is an existing WordPress settings page
	 * (like 'general', 'writing', 'reading') and only adds fields,
	 * or creates a new settings page if it doesn't exist.
	 *
	 * @param array<string, mixed> $config Settings page configuration.
	 * @return void
	 * @throws \InvalidArgumentException If required fields are missing.
	 */
	private function register_settings_page_from_array( array $config ): void {
		if ( empty( $config['id'] ) ) {
			throw new \InvalidArgumentException( 'Settings page configuration must include "id".' );
		}

		$page_id = $config['id'];
		$fields  = $config['fields'] ?? array();

		// Determine if this is creating a new settings page or adding to an existing one
		// Settings pages that define properties like page_title, menu_title, capability, etc. are new
		// Settings pages with ONLY id and fields are adding to existing pages
		$settings_properties = array( 'page_title', 'menu_title', 'capability', 'menu_slug', 'callback', 'icon_url', 'position', 'parent_slug' );
		$has_settings_config = false;

		foreach ( $settings_properties as $prop ) {
			if ( isset( $config[ $prop ] ) ) {
				$has_settings_config = true;
				break;
			}
		}

		// Only register new settings page if configuration properties are provided
		// If only id and fields provided, assume it's for adding fields to an existing settings page
		if ( $has_settings_config ) {
			// Remove 'fields' from config before passing to SettingsPage
			$page_args = $config;
			unset( $page_args['fields'] );
			$this->registrar->add_settings_page( $page_id, $page_args );
		}

		// Register fields if provided (works for both new and existing settings pages)
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			$this->registrar->add_fields( $page_id, $fields );
		}
	}

	/**
	 * Register configuration from JSON
	 *
	 * Accepts either a file path to a JSON file or a JSON string and
	 * registers the configuration after validation.
	 *
	 * @param string $path_or_json File path to JSON file or JSON string.
	 * @param bool   $validate     Whether to validate against schema (default: true).
	 * @return self
	 * @throws \InvalidArgumentException If JSON is invalid or validation fails.
	 */
	public function register_from_json( string $path_or_json, bool $validate = true ): self {
		// Determine if it's a file path or JSON string
		$json_string = $this->get_json_content( $path_or_json );

		// Decode JSON
		$config = json_decode( $json_string, true );

		if ( null === $config ) {
			$error = json_last_error_msg();
			throw new \InvalidArgumentException( "Invalid JSON: {$error}" );
		}

		if ( ! is_array( $config ) ) {
			throw new \InvalidArgumentException( 'JSON must decode to an array/object' );
		}

		// Validate against schema if requested
		if ( $validate ) {
			$validator = new \Pedalcms\WpCmf\Json\SchemaValidator();
			if ( ! $validator->validate( $config ) ) {
				throw new \InvalidArgumentException( $validator->get_error_message() );
			}
		}

		// Register using array registration
		return $this->register_from_array( $config );
	}

	/**
	 * Get JSON content from file path or string
	 *
	 * @param string $path_or_json File path or JSON string.
	 * @return string JSON content.
	 * @throws \InvalidArgumentException If file doesn't exist or is not readable.
	 */
	private function get_json_content( string $path_or_json ): string {
		// Check if it looks like a file path
		if ( file_exists( $path_or_json ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$content = file_get_contents( $path_or_json );
			if ( false === $content ) {
				throw new \InvalidArgumentException( "Unable to read file: {$path_or_json}" );
			}
			return $content;
		}

		// Treat as JSON string
		return $path_or_json;
	}

	/**
	 * Register a custom field type
	 *
	 * This is an alias to FieldFactory::register_type() for convenience.
	 *
	 * @param string $type       Field type name.
	 * @param string $class_name Field class name.
	 * @return self
	 * @throws \InvalidArgumentException If class doesn't exist or doesn't implement FieldInterface.
	 */
	public function register_field_type( string $type, string $class_name ): self {
		FieldFactory::register_type( $type, $class_name );
		return $this;
	}

	/**
	 * Get configuration options
	 *
	 * @return array<string, mixed>
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Set a configuration option
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 * @return self
	 */
	public function set_option( string $key, $value ): self {
		$this->options[ $key ] = $value;
		return $this;
	}

	/**
	 * Get a configuration option
	 *
	 * @param string $key           Option key.
	 * @param mixed  $default_value Default value if option doesn't exist.
	 * @return mixed
	 */
	public function get_option( string $key, $default_value = null ) {
		return $this->options[ $key ] ?? $default_value;
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {
		// Singleton pattern - prevent cloning
	}

	/**
	 * Prevent unserialization
	 */
	public function __wakeup(): void {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}
