<?php
/**
 * Field Registration Trait
 *
 * Provides common field registration functionality shared across handlers.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core\Traits;

use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;

/**
 * Trait FieldRegistrationTrait
 *
 * Common field registration logic for CPT and Settings handlers.
 */
trait FieldRegistrationTrait {

	/**
	 * Registered fields by context
	 *
	 * @var array<string, array<string, FieldInterface>>
	 */
	protected array $fields = array();

	/**
	 * Nested field names that should not be rendered separately
	 *
	 * @var array<string, array<string>>
	 */
	protected array $nested_field_names = array();

	/**
	 * Add fields for a context
	 *
	 * @param string $context Context identifier (post type or settings page ID).
	 * @param array  $fields  Array of field configs or FieldInterface instances.
	 * @return void
	 */
	public function add_fields( string $context, array $fields ): void {
		if ( ! isset( $this->fields[ $context ] ) ) {
			$this->fields[ $context ] = array();
		}

		foreach ( $fields as $key => $field ) {
			$this->add_single_field( $context, $field, $key );
		}
	}

	/**
	 * Add a single field
	 *
	 * @param string              $context Context identifier.
	 * @param mixed               $field   Field config array or FieldInterface instance.
	 * @param string|int          $key     Array key for fallback name.
	 * @return void
	 */
	protected function add_single_field( string $context, $field, $key ): void {
		if ( $field instanceof FieldInterface ) {
			$field_name                              = $field->get_name();
			$this->fields[ $context ][ $field_name ] = $field;
			$this->register_nested_fields( $context, $field );
			return;
		}

		if ( ! is_array( $field ) ) {
			return;
		}

		// Ensure field has a name
		if ( empty( $field['name'] ) ) {
			$field['name'] = is_string( $key ) ? $key : '';
		}

		if ( empty( $field['name'] ) ) {
			return;
		}

		try {
			$field_instance                          = FieldFactory::create( $field );
			$field_name                              = $field_instance->get_name();
			$this->fields[ $context ][ $field_name ] = $field_instance;
			$this->register_nested_fields( $context, $field_instance );
		} catch ( \InvalidArgumentException $e ) {
			// Skip invalid fields
			return;
		}
	}

	/**
	 * Register nested fields from container fields
	 *
	 * @param string         $context Context identifier.
	 * @param FieldInterface $field   Field instance to check.
	 * @return void
	 */
	protected function register_nested_fields( string $context, FieldInterface $field ): void {
		if ( ! $field instanceof ContainerFieldInterface ) {
			return;
		}

		$nested_configs = $field->get_nested_fields();

		foreach ( $nested_configs as $nested_config ) {
			if ( empty( $nested_config['name'] ) ) {
				continue;
			}

			try {
				$nested_field = FieldFactory::create( $nested_config );
				$nested_name  = $nested_field->get_name();

				$this->fields[ $context ][ $nested_name ] = $nested_field;

				// Track as nested field
				if ( ! isset( $this->nested_field_names[ $context ] ) ) {
					$this->nested_field_names[ $context ] = array();
				}
				$this->nested_field_names[ $context ][] = $nested_name;

				// Recursively handle nested containers
				$this->register_nested_fields( $context, $nested_field );
			} catch ( \InvalidArgumentException $e ) {
				continue;
			}
		}
	}

	/**
	 * Check if a field is nested
	 *
	 * @param string $context    Context identifier.
	 * @param string $field_name Field name.
	 * @return bool
	 */
	protected function is_nested_field( string $context, string $field_name ): bool {
		return isset( $this->nested_field_names[ $context ] )
			&& in_array( $field_name, $this->nested_field_names[ $context ], true );
	}

	/**
	 * Get fields for a context
	 *
	 * @param string $context Context identifier.
	 * @return array<string, FieldInterface>
	 */
	public function get_fields( string $context ): array {
		return $this->fields[ $context ] ?? array();
	}

	/**
	 * Get all registered fields
	 *
	 * @return array<string, array<string, FieldInterface>>
	 */
	public function get_all_fields(): array {
		return $this->fields;
	}

	/**
	 * Check if context has fields
	 *
	 * @param string $context Context identifier.
	 * @return bool
	 */
	public function has_fields( string $context ): bool {
		return ! empty( $this->fields[ $context ] );
	}

	/**
	 * Create field instances from nested configs
	 *
	 * @param array $nested_configs Array of field configuration arrays.
	 * @return array<FieldInterface>
	 */
	protected function create_nested_field_instances( array $nested_configs ): array {
		$instances = array();

		foreach ( $nested_configs as $config ) {
			if ( empty( $config['name'] ) ) {
				continue;
			}

			try {
				$instances[] = FieldFactory::create( $config );
			} catch ( \InvalidArgumentException $e ) {
				continue;
			}
		}

		return $instances;
	}
}
