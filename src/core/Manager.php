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

use Pedalcms\WpCmf\Core\Handlers\NewSettingsPageHandler;
use Pedalcms\WpCmf\Core\Handlers\ExistingSettingsPageHandler;
use Pedalcms\WpCmf\Core\Handlers\NewPostTypeHandler;
use Pedalcms\WpCmf\Core\Handlers\ExistingPostTypeHandler;
use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Json\SchemaValidator;

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
	 * Handler for new settings pages
	 *
	 * @var NewSettingsPageHandler
	 */
	private NewSettingsPageHandler $new_settings_handler;

	/**
	 * Handler for existing settings pages
	 *
	 * @var ExistingSettingsPageHandler
	 */
	private ExistingSettingsPageHandler $existing_settings_handler;

	/**
	 * Handler for new custom post types
	 *
	 * @var NewPostTypeHandler
	 */
	private NewPostTypeHandler $new_cpt_handler;

	/**
	 * Handler for existing post types
	 *
	 * @var ExistingPostTypeHandler
	 */
	private ExistingPostTypeHandler $existing_cpt_handler;

	/**
	 * Legacy registrar for backward compatibility
	 *
	 * @var Registrar|null
	 */
	private ?Registrar $registrar = null;

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
		$this->options = $options;

		// Initialize handlers
		$this->new_settings_handler      = new NewSettingsPageHandler();
		$this->existing_settings_handler = new ExistingSettingsPageHandler();
		$this->new_cpt_handler           = new NewPostTypeHandler();
		$this->existing_cpt_handler      = new ExistingPostTypeHandler();

		// Initialize hooks if WordPress is available
		if ( function_exists( 'add_action' ) ) {
			$this->new_settings_handler->init_hooks();
			$this->existing_settings_handler->init_hooks();
			$this->new_cpt_handler->init_hooks();
			$this->existing_cpt_handler->init_hooks();
		}

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
	 * Reset the singleton instance (for testing)
	 *
	 * @return void
	 */
	public static function reset(): void {
		self::$instance = null;
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
	 * Get the new settings page handler
	 *
	 * @return NewSettingsPageHandler
	 */
	public function get_new_settings_handler(): NewSettingsPageHandler {
		return $this->new_settings_handler;
	}

	/**
	 * Get the existing settings page handler
	 *
	 * @return ExistingSettingsPageHandler
	 */
	public function get_existing_settings_handler(): ExistingSettingsPageHandler {
		return $this->existing_settings_handler;
	}

	/**
	 * Get the new CPT handler
	 *
	 * @return NewPostTypeHandler
	 */
	public function get_new_cpt_handler(): NewPostTypeHandler {
		return $this->new_cpt_handler;
	}

	/**
	 * Get the existing CPT handler
	 *
	 * @return ExistingPostTypeHandler
	 */
	public function get_existing_cpt_handler(): ExistingPostTypeHandler {
		return $this->existing_cpt_handler;
	}

	/**
	 * Get the legacy registrar instance (for backward compatibility)
	 *
	 * @return Registrar The registrar instance.
	 * @deprecated Use specific handlers instead.
	 */
	public function get_registrar(): Registrar {
		if ( null === $this->registrar ) {
			$this->registrar = new Registrar( function_exists( 'add_action' ) );
		}
		return $this->registrar;
	}

	/**
	 * Register configuration from array
	 *
	 * Accepts a configuration array and registers custom post types,
	 * settings pages, and their associated fields.
	 *
	 * @param array<string, mixed> $config Configuration array.
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

		// Trigger late registration if hooks have already fired
		$this->trigger_late_registration();

		return $this;
	}

	/**
	 * Register a custom post type from array configuration
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

		if ( $is_existing ) {
			// Add fields to existing post type
			if ( ! empty( $fields ) ) {
				$this->existing_cpt_handler->add_fields( $post_type, $fields );
			}
		} else {
			// Create new post type
			if ( ! empty( $args ) ) {
				$this->new_cpt_handler->add_post_type( $post_type, $args );
			}

			// Add fields
			if ( ! empty( $fields ) ) {
				$this->new_cpt_handler->add_fields( $post_type, $fields );
			}
		}
	}

	/**
	 * Register a settings page from array configuration
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

		// Check if this is creating a new settings page or adding to existing
		$settings_properties = array(
			'page_title',
			'menu_title',
			'capability',
			'menu_slug',
			'callback',
			'icon_url',
			'position',
			'parent_slug',
		);

		$has_settings_config = false;
		foreach ( $settings_properties as $prop ) {
			if ( isset( $config[ $prop ] ) ) {
				$has_settings_config = true;
				break;
			}
		}

		if ( $has_settings_config ) {
			// Create new settings page
			$page_args = $config;
			unset( $page_args['fields'] );
			$this->new_settings_handler->add_page( $page_id, $page_args );

			// Add fields to new page
			if ( ! empty( $fields ) ) {
				$this->new_settings_handler->add_fields( $page_id, $fields );
			}
		} else {
			// Add fields to existing settings page
			if ( ! empty( $fields ) ) {
				$this->existing_settings_handler->add_fields( $page_id, $fields );
			}
		}
	}

	/**
	 * Trigger late registration if hooks have already fired
	 *
	 * @return void
	 */
	private function trigger_late_registration(): void {
		if ( ! function_exists( 'did_action' ) ) {
			return;
		}

		// Register CPTs if 'init' has already fired
		if ( did_action( 'init' ) ) {
			$this->new_cpt_handler->register_post_types();
		}

		// Register admin pages if 'admin_menu' has already fired
		if ( did_action( 'admin_menu' ) ) {
			$this->new_settings_handler->register_pages();
		}

		// Register settings fields if 'admin_init' has already fired
		if ( did_action( 'admin_init' ) ) {
			$this->new_settings_handler->register_settings();
			$this->existing_settings_handler->register_settings();
		}
	}

	/**
	 * Register configuration from JSON
	 *
	 * @param string $path_or_json File path to JSON file or JSON string.
	 * @param bool   $validate     Whether to validate against schema (default: true).
	 * @return self
	 * @throws \InvalidArgumentException If JSON is invalid or validation fails.
	 */
	public function register_from_json( string $path_or_json, bool $validate = true ): self {
		$json_string = $this->get_json_content( $path_or_json );

		$config = json_decode( $json_string, true );

		if ( null === $config ) {
			throw new \InvalidArgumentException( 'Invalid JSON: ' . json_last_error_msg() );
		}

		if ( ! is_array( $config ) ) {
			throw new \InvalidArgumentException( 'JSON must decode to an array/object' );
		}

		if ( $validate ) {
			$validator = new SchemaValidator();
			if ( ! $validator->validate( $config ) ) {
				throw new \InvalidArgumentException( $validator->get_error_message() );
			}
		}

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
		if ( file_exists( $path_or_json ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$content = file_get_contents( $path_or_json );
			if ( false === $content ) {
				throw new \InvalidArgumentException( "Unable to read file: {$path_or_json}" );
			}
			return $content;
		}

		return $path_or_json;
	}

	/**
	 * Register a custom field type
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
