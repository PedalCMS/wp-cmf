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
	 *       'id' => 'book',
	 *       'args' => array('label' => 'Books', 'supports' => array('title', 'editor')),
	 *       'fields' => array( ... field definitions ... )
	 *     )
	 *   ),
	 *   'settings_pages' => array(
	 *     array(
	 *       'id' => 'my-plugin-settings',
	 *       'title' => 'My Plugin',
	 *       'menu_title' => 'My Plugin',
	 *       'capability' => 'manage_options',
	 *       'slug' => 'my-plugin',
	 *       'fields' => array( ... field definitions ... )
	 *     )
	 *   )
	 * )
	 * </code>
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

		// Register the CPT
		$this->registrar->add_custom_post_type( $post_type, $args );

		// Register fields if provided
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			$this->registrar->add_fields( $post_type, $fields );
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

		// Remove 'fields' from config before passing to SettingsPage
		$page_args = $config;
		unset( $page_args['fields'] );

		// Register the settings page
		$this->registrar->add_settings_page( $page_id, $page_args );

		// Register fields if provided
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			$this->registrar->add_fields( $page_id, $fields );
		}
	}

	/**
	 * Register configuration from JSON
	 *
	 * @param string $path_or_json File path to JSON file or JSON string.
	 * @return self
	 */
	public function register_from_json( string $path_or_json ): self {
		// TODO: Implement JSON-based registration
		// This will be implemented in Milestone 4
		unset( $path_or_json ); // Prevent unused parameter warning
		return $this;
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
