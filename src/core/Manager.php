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
	 * @param array<string, mixed> $config Configuration array containing CPTs, settings pages, and fields.
	 * @return self
	 */
	public function register_from_array( array $config ): self {
		// TODO: Implement array-based registration
		// This will be implemented in Milestone 4
		unset( $config ); // Prevent unused parameter warning
		return $this;
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
