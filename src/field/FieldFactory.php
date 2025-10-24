<?php
/**
 * FieldFactory for WP-CMF
 *
 * Factory class for creating field instances from configuration arrays.
 * Provides a registry for field types and supports custom field registration.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field;

use Pedalcms\WpCmf\Field\Fields\TextField;
use Pedalcms\WpCmf\Field\Fields\TextareaField;
use Pedalcms\WpCmf\Field\Fields\SelectField;
use Pedalcms\WpCmf\Field\Fields\CheckboxField;
use Pedalcms\WpCmf\Field\Fields\RadioField;
use Pedalcms\WpCmf\Field\Fields\NumberField;
use Pedalcms\WpCmf\Field\Fields\EmailField;
use Pedalcms\WpCmf\Field\Fields\URLField;
use Pedalcms\WpCmf\Field\Fields\DateField;
use Pedalcms\WpCmf\Field\Fields\PasswordField;
use Pedalcms\WpCmf\Field\Fields\ColorField;

/**
 * FieldFactory class
 *
 * Creates field instances from configuration arrays and maintains
 * a registry of available field types.
 */
class FieldFactory {

	/**
	 * Registered field types
	 *
	 * @var array<string, string> Map of type name to class name
	 */
	private static array $field_types = array();

	/**
	 * Whether default field types have been registered
	 *
	 * @var bool
	 */
	private static bool $defaults_registered = false;

	/**
	 * Register a field type
	 *
	 * Allows registration of custom field types or overriding core field types.
	 *
	 * @param string $type       Field type identifier.
	 * @param string $class_name Fully qualified class name.
	 * @return void
	 * @throws \InvalidArgumentException If class doesn't implement FieldInterface.
	 */
	public static function register_type( string $type, string $class_name ): void {
		// Validate class exists
		if ( ! class_exists( $class_name ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Class "%s" does not exist.', $class_name )
			);
		}

		// Validate class implements FieldInterface
		if ( ! in_array( FieldInterface::class, class_implements( $class_name ) ?: array(), true ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Class "%s" must implement FieldInterface.', $class_name )
			);
		}

		self::$field_types[ $type ] = $class_name;
	}

	/**
	 * Register default core field types
	 *
	 * @return void
	 */
	public static function register_defaults(): void {
		if ( self::$defaults_registered ) {
			return;
		}

		// Preserve any custom types that were registered before defaults
		$custom_types = self::$field_types;

		self::$field_types = array(
			'text'     => TextField::class,
			'textarea' => TextareaField::class,
			'select'   => SelectField::class,
			'checkbox' => CheckboxField::class,
			'radio'    => RadioField::class,
			'number'   => NumberField::class,
			'email'    => EmailField::class,
			'url'      => URLField::class,
			'date'     => DateField::class,
			'password' => PasswordField::class,
			'color'    => ColorField::class,
		);

		// Merge back any custom types
		self::$field_types = array_merge( self::$field_types, $custom_types );

		self::$defaults_registered = true;
	}

	/**
	 * Create a field instance from configuration
	 *
	 * @param array<string, mixed> $config Field configuration array.
	 * @return FieldInterface Field instance.
	 * @throws \InvalidArgumentException If required config is missing or type is unknown.
	 */
	public static function create( array $config ): FieldInterface {
		// Ensure defaults are registered
		if ( ! self::$defaults_registered ) {
			self::register_defaults();
		}

		// Validate required config
		if ( empty( $config['name'] ) ) {
			throw new \InvalidArgumentException( 'Field config must include "name".' );
		}

		if ( empty( $config['type'] ) ) {
			throw new \InvalidArgumentException( 'Field config must include "type".' );
		}

		$type = $config['type'];
		$name = $config['name'];

		// Check if field type is registered
		if ( ! isset( self::$field_types[ $type ] ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Unknown field type "%s". Register it with FieldFactory::register_type().', $type )
			);
		}

		$class_name = self::$field_types[ $type ];

		// Create and return field instance
		return new $class_name( $name, $type, $config );
	}

	/**
	 * Create multiple fields from configuration array
	 *
	 * @param array<string, array<string, mixed>> $fields_config Array of field configurations.
	 * @return array<string, FieldInterface> Array of field instances keyed by field name.
	 */
	public static function create_multiple( array $fields_config ): array {
		$fields = array();

		foreach ( $fields_config as $key => $config ) {
			// If config doesn't have a name, use the array key
			if ( empty( $config['name'] ) ) {
				$config['name'] = $key;
			}

			$fields[ $config['name'] ] = self::create( $config );
		}

		return $fields;
	}

	/**
	 * Get all registered field types
	 *
	 * @return array<string, string> Map of type names to class names.
	 */
	public static function get_registered_types(): array {
		if ( ! self::$defaults_registered ) {
			self::register_defaults();
		}

		return self::$field_types;
	}

	/**
	 * Check if a field type is registered
	 *
	 * @param string $type Field type identifier.
	 * @return bool
	 */
	public static function has_type( string $type ): bool {
		if ( ! self::$defaults_registered ) {
			self::register_defaults();
		}

		return isset( self::$field_types[ $type ] );
	}

	/**
	 * Unregister a field type
	 *
	 * Useful for testing or replacing field types.
	 *
	 * @param string $type Field type identifier.
	 * @return void
	 */
	public static function unregister_type( string $type ): void {
		unset( self::$field_types[ $type ] );
	}

	/**
	 * Reset the factory (mainly for testing)
	 *
	 * @return void
	 */
	public static function reset(): void {
		self::$field_types         = array();
		self::$defaults_registered = false;
	}
}
