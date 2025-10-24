<?php
/**
 * AbstractField base class for WP-CMF
 *
 * Provides common functionality and helpers for all field types.
 * Field classes should extend this to get standard behavior.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Field;

/**
 * AbstractField - Base implementation for field types
 *
 * Provides common properties and helper methods that all fields can use.
 * Concrete field classes should extend this and implement render().
 */
abstract class AbstractField implements FieldInterface {

	/**
	 * Field name/identifier
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * Field configuration
	 *
	 * @var array<string, mixed>
	 */
	protected array $config = array();

	/**
	 * Validation rules
	 *
	 * @var array<string, mixed>
	 */
	protected array $validation_rules = array();

	/**
	 * Constructor
	 *
	 * @param string               $name   Field name/identifier.
	 * @param string               $type   Field type.
	 * @param array<string, mixed> $config Field configuration.
	 */
	public function __construct( string $name, string $type, array $config = array() ) {
		$this->name   = $name;
		$this->type   = $type;
		$this->config = array_merge( $this->get_defaults(), $config );

		// Extract validation rules if provided
		if ( isset( $this->config['validation'] ) ) {
			$this->validation_rules = $this->config['validation'];
		}
	}

	/**
	 * Get default configuration values
	 *
	 * @return array<string, mixed>
	 */
	protected function get_defaults(): array {
		return array(
			'label'       => ucwords( str_replace( array( '_', '-' ), ' ', $this->name ?? '' ) ),
			'description' => '',
			'placeholder' => '',
			'default'     => '',
			'required'    => false,
			'class'       => '',
			'attributes'  => array(),
		);
	}

	/**
	 * Get the field name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the field label
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->config['label'] ?? '';
	}

	/**
	 * Get the field type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get field configuration
	 *
	 * @param string $key          Configuration key.
	 * @param mixed  $default_value Default value if key not found.
	 * @return mixed
	 */
	public function get_config( string $key, $default_value = null ) {
		return $this->config[ $key ] ?? $default_value;
	}

	/**
	 * Set field configuration
	 *
	 * @param string $key   Configuration key.
	 * @param mixed  $value Configuration value.
	 * @return self
	 */
	public function set_config( string $key, $value ): self {
		$this->config[ $key ] = $value;
		return $this;
	}

	/**
	 * Get all configuration
	 *
	 * @return array<string, mixed>
	 */
	public function get_all_config(): array {
		return $this->config;
	}

	/**
	 * Sanitize the input value
	 *
	 * Default implementation - field types should override as needed.
	 *
	 * @param mixed $input Raw input value.
	 * @return mixed
	 */
	public function sanitize( $input ) {
		// Default: sanitize as text
		if ( is_string( $input ) ) {
			if ( function_exists( 'sanitize_text_field' ) ) {
				return \sanitize_text_field( $input );
			}
			// Fallback sanitization if WordPress function not available
			$sanitized = strip_tags( $input );
			$sanitized = trim( preg_replace( '/\s+/', ' ', $sanitized ) );
			return $sanitized;
		}
		return $input;
	}

	/**
	 * Validate the input value
	 *
	 * @param mixed $input Input value to validate.
	 * @return array
	 */
	public function validate( $input ): array {
		$errors = array();

		// Check required
		if ( ! empty( $this->config['required'] ) && empty( $input ) ) {
			$errors[] = sprintf( '%s is required.', $this->get_label() );
		}

		// Apply custom validation rules
		foreach ( $this->validation_rules as $rule => $rule_value ) {
			$error = $this->apply_validation_rule( $rule, $rule_value, $input );
			if ( $error ) {
				$errors[] = $error;
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Apply a specific validation rule
	 *
	 * @param string $rule       Rule name.
	 * @param mixed  $rule_value Rule value/parameter.
	 * @param mixed  $input      Input to validate.
	 * @return string|null Error message or null if valid.
	 */
	protected function apply_validation_rule( string $rule, $rule_value, $input ): ?string {
		switch ( $rule ) {
			case 'min':
				if ( is_numeric( $input ) && $input < $rule_value ) {
					return sprintf( '%s must be at least %s.', $this->get_label(), $rule_value );
				}
				if ( is_string( $input ) && strlen( $input ) < $rule_value ) {
					return sprintf( '%s must be at least %s characters.', $this->get_label(), $rule_value );
				}
				break;

			case 'max':
				if ( is_numeric( $input ) && $input > $rule_value ) {
					return sprintf( '%s must be at most %s.', $this->get_label(), $rule_value );
				}
				if ( is_string( $input ) && strlen( $input ) > $rule_value ) {
					return sprintf( '%s must be at most %s characters.', $this->get_label(), $rule_value );
				}
				break;

			case 'pattern':
				if ( is_string( $input ) && ! preg_match( $rule_value, $input ) ) {
					return sprintf( '%s format is invalid.', $this->get_label() );
				}
				break;

			case 'email':
				if ( $rule_value ) {
					$is_valid_email = function_exists( 'is_email' )
						? \is_email( $input )
						: filter_var( $input, FILTER_VALIDATE_EMAIL );
					if ( ! $is_valid_email ) {
						return sprintf( '%s must be a valid email address.', $this->get_label() );
					}
				}
				break;

			case 'url':
				if ( $rule_value && ! filter_var( $input, FILTER_VALIDATE_URL ) ) {
					return sprintf( '%s must be a valid URL.', $this->get_label() );
				}
				break;
		}

		return null;
	}

	/**
	 * Get the field schema
	 *
	 * @return array<string, mixed>
	 */
	public function get_schema(): array {
		return array(
			'name'        => $this->name,
			'type'        => $this->type,
			'label'       => $this->get_label(),
			'description' => $this->config['description'] ?? '',
			'required'    => $this->config['required'] ?? false,
			'default'     => $this->config['default'] ?? '',
			'validation'  => $this->validation_rules,
		);
	}

	/**
	 * Escape attribute value
	 *
	 * @param string $text
	 * @return string
	 */
	protected function esc_attr( string $text ): string {
		if ( function_exists( 'esc_attr' ) ) {
			return \esc_attr( $text );
		}
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape HTML
	 *
	 * @param string $text
	 * @return string
	 */
	protected function esc_html( string $text ): string {
		if ( function_exists( 'esc_html' ) ) {
			return \esc_html( $text );
		}
		return htmlspecialchars( $text, ENT_NOQUOTES, 'UTF-8' );
	}

	/**
	 * Render field wrapper start
	 *
	 * @return string
	 */
	protected function render_wrapper_start(): string {
		$classes = array( 'wp-cmf-field', 'wp-cmf-field-' . $this->type );

		if ( ! empty( $this->config['class'] ) ) {
			$classes[] = $this->config['class'];
		}

		if ( ! empty( $this->config['required'] ) ) {
			$classes[] = 'wp-cmf-field-required';
		}

		return sprintf(
			'<div class="%s" data-field-name="%s" data-field-type="%s">',
			$this->esc_attr( implode( ' ', $classes ) ),
			$this->esc_attr( $this->name ),
			$this->esc_attr( $this->type )
		);
	}

	/**
	 * Render field wrapper end
	 *
	 * @return string
	 */
	protected function render_wrapper_end(): string {
		return '</div>';
	}

	/**
	 * Render field label
	 *
	 * @return string
	 */
	protected function render_label(): string {
		$label = $this->get_label();

		if ( empty( $label ) ) {
			return '';
		}

		$required = ! empty( $this->config['required'] ) ? ' <span class="required">*</span>' : '';

		return sprintf(
			'<label for="%s" class="wp-cmf-field-label">%s%s</label>',
			$this->esc_attr( $this->get_field_id() ),
			$this->esc_html( $label ),
			$required
		);
	}

	/**
	 * Render field description
	 *
	 * @return string
	 */
	protected function render_description(): string {
		$description = $this->config['description'] ?? '';

		if ( empty( $description ) ) {
			return '';
		}

		return sprintf(
			'<p class="description wp-cmf-field-description">%s</p>',
			$this->esc_html( $description )
		);
	}

	/**
	 * Get field HTML ID
	 *
	 * @return string
	 */
	protected function get_field_id(): string {
		$key = function_exists( 'sanitize_key' )
			? \sanitize_key( $this->name )
			: strtolower( preg_replace( '/[^a-z0-9_\-]/', '', $this->name ) );
		return 'wp-cmf-field-' . $key;
	}

	/**
	 * Build HTML attributes string
	 *
	 * @param array<string, mixed> $attributes Attributes array.
	 * @return string
	 */
	protected function build_attributes( array $attributes ): string {
		$attr_string = '';

		foreach ( $attributes as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attr_string .= ' ' . $this->esc_attr( $key );
				}
			} else {
				$attr_string .= sprintf( ' %s="%s"', $this->esc_attr( $key ), $this->esc_attr( (string) $value ) );
			}
		}

		return $attr_string;
	}

	/**
	 * Enqueue field assets (CSS and JS)
	 *
	 * Default implementation does nothing. Override in field classes
	 * that need to load custom assets.
	 *
	 * Example:
	 * ```php
	 * public function enqueue_assets(): void {
	 *     wp_enqueue_style( 'my-field-style', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
	 *     wp_enqueue_script( 'my-field-script', plugin_dir_url( __FILE__ ) . 'assets/script.js', ['jquery'], '1.0', true );
	 * }
	 * ```
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		// Default: no assets to enqueue
		// Override in concrete field classes that need custom assets
	}

	/**
	 * Render the field HTML
	 *
	 * Must be implemented by concrete field classes.
	 *
	 * @param mixed $value Current field value.
	 * @return string
	 */
	abstract public function render( $value = null ): string;
}
