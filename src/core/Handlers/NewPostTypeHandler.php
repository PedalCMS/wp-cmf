<?php
/**
 * New Post Type Handler
 *
 * Handles registration and field management for new custom post types.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core\Handlers;

use Pedalcms\WpCmf\CPT\CustomPostType;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;
use Pedalcms\WpCmf\Field\fields\MetaboxField;

/**
 * Class NewPostTypeHandler
 *
 * Manages creation and field registration for new custom post types.
 */
class NewPostTypeHandler extends AbstractHandler {

	/**
	 * Registered custom post types
	 *
	 * @var array<string, CustomPostType>
	 */
	private array $post_types = array();

	/**
	 * Initialize WordPress hooks
	 *
	 * @return void
	 */
	public function init_hooks(): void {
		if ( $this->hooks_initialized || ! $this->has_wordpress() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		$this->hooks_initialized = true;
	}

	/**
	 * Add a custom post type
	 *
	 * @param string $post_type Post type slug.
	 * @param array  $args      Post type configuration.
	 * @return self
	 */
	public function add_post_type( string $post_type, array $args ): self {
		$this->post_types[ $post_type ] = CustomPostType::from_array( $post_type, $args );
		return $this;
	}

	/**
	 * Add a CustomPostType instance
	 *
	 * @param CustomPostType $cpt CustomPostType instance.
	 * @return self
	 */
	public function add_post_type_instance( CustomPostType $cpt ): self {
		$this->post_types[ $cpt->get_post_type() ] = $cpt;
		return $this;
	}

	/**
	 * Get a custom post type
	 *
	 * @param string $post_type Post type slug.
	 * @return CustomPostType|null
	 */
	public function get_post_type( string $post_type ): ?CustomPostType {
		return $this->post_types[ $post_type ] ?? null;
	}

	/**
	 * Get all custom post types
	 *
	 * @return array<string, CustomPostType>
	 */
	public function get_post_types(): array {
		return $this->post_types;
	}

	/**
	 * Check if a post type is registered
	 *
	 * @param string $post_type Post type slug.
	 * @return bool
	 */
	public function has_post_type( string $post_type ): bool {
		return isset( $this->post_types[ $post_type ] );
	}

	/**
	 * Register custom post types with WordPress
	 *
	 * @return void
	 */
	public function register_post_types(): void {
		foreach ( $this->post_types as $cpt ) {
			$cpt->register();
		}
	}

	/**
	 * Register meta boxes for post types
	 *
	 * @return void
	 */
	public function register_meta_boxes(): void {
		if ( ! function_exists( 'add_meta_box' ) ) {
			return;
		}

		foreach ( $this->post_types as $post_type => $cpt ) {
			if ( ! $this->has_fields( $post_type ) ) {
				continue;
			}

			$this->register_post_type_meta_boxes( $post_type );
		}
	}

	/**
	 * Register meta boxes for a specific post type
	 *
	 * @param string $post_type Post type slug.
	 * @return void
	 */
	private function register_post_type_meta_boxes( string $post_type ): void {
		$metabox_fields = array();
		$regular_fields = array();

		// Separate MetaboxField containers from regular fields
		foreach ( $this->get_fields( $post_type ) as $field ) {
			if ( $this->is_nested_field( $post_type, $field->get_name() ) ) {
				continue;
			}

			if ( $field instanceof MetaboxField ) {
				$metabox_fields[] = $field;
			} else {
				$regular_fields[] = $field;
			}
		}

		// Register MetaboxField containers as meta boxes
		$registered_ids = array();
		foreach ( $metabox_fields as $field ) {
			$metabox_id = $field->get_metabox_id();

			if ( in_array( $metabox_id, $registered_ids, true ) ) {
				continue;
			}

			add_meta_box(
				$metabox_id,
				$field->get_metabox_title(),
				array( $this, 'render_metabox_container' ),
				$post_type,
				$field->get_context(),
				$field->get_priority(),
				array( 'metabox_field' => $field )
			);

			$registered_ids[] = $metabox_id;
		}

		// Register default meta box for regular fields
		if ( ! empty( $regular_fields ) ) {
			$cpt   = $this->post_types[ $post_type ] ?? null;
			$label = $cpt ? $cpt->get_singular_label() : ucfirst( $post_type );

			add_meta_box(
				$post_type . '_cmf_fields',
				$label . ' Fields',
				array( $this, 'render_default_meta_box' ),
				$post_type,
				'normal',
				'high',
				array( 'fields' => $regular_fields )
			);
		}
	}

	/**
	 * Render a MetaboxField container
	 *
	 * @param \WP_Post $post Post object.
	 * @param array    $args Metabox arguments.
	 * @return void
	 */
	public function render_metabox_container( \WP_Post $post, array $args ): void {
		if ( ! isset( $args['args']['metabox_field'] ) ) {
			return;
		}

		$field = $args['args']['metabox_field'];

		if ( ! $field instanceof MetaboxField ) {
			return;
		}

		// Render nonce once per post type
		$this->render_nonce_once( $post->post_type );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field->render( null );
	}

	/**
	 * Render default meta box with regular fields
	 *
	 * @param \WP_Post $post Post object.
	 * @param array    $args Metabox arguments.
	 * @return void
	 */
	public function render_default_meta_box( \WP_Post $post, array $args ): void {
		$fields = $args['args']['fields'] ?? array();

		if ( empty( $fields ) ) {
			return;
		}

		// Render nonce
		$this->render_nonce_once( $post->post_type );

		echo '<div class="wp-cmf-fields">';

		foreach ( $fields as $field ) {
			if ( ! $field instanceof FieldInterface ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->render_cpt_field_html( $field, $post->ID );
		}

		echo '</div>';
	}

	/**
	 * Render nonce field once per post type
	 *
	 * @param string $post_type Post type slug.
	 * @return void
	 */
	private function render_nonce_once( string $post_type ): void {
		static $rendered = array();

		if ( isset( $rendered[ $post_type ] ) ) {
			return;
		}

		$this->render_nonce_field(
			'save_' . $post_type . '_fields',
			$post_type . '_fields_nonce'
		);

		$rendered[ $post_type ] = true;
	}

	/**
	 * Save post fields
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post_fields( $post_id ): void {
		$post_id = (int) $post_id;

		// Skip autosaves and revisions
		if ( $this->should_skip_save( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( ! $post_type || ! $this->has_post_type( $post_type ) ) {
			return;
		}

		if ( ! $this->has_fields( $post_type ) ) {
			return;
		}

		// Verify nonce
		$nonce_name = $post_type . '_fields_nonce';
		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) );
		if ( ! $this->verify_nonce( $nonce, 'save_' . $post_type . '_fields' ) ) {
			return;
		}

		// Check permissions
		if ( ! $this->can_edit_post( $post_id, $post_type ) ) {
			return;
		}

		// Save each field
		foreach ( $this->get_fields( $post_type ) as $field ) {
			if ( $field instanceof ContainerFieldInterface ) {
				// Container fields don't store values
				continue;
			}

			$this->save_single_field( $field, $post_id, $post_type );
		}
	}

	/**
	 * Check if save should be skipped
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	private function should_skip_save( int $post_id ): bool {
		if ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) {
			return true;
		}

		if ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if user can edit post
	 *
	 * @param int    $post_id   Post ID.
	 * @param string $post_type Post type.
	 * @return bool
	 */
	private function can_edit_post( int $post_id, string $post_type ): bool {
		if ( ! function_exists( 'current_user_can' ) ) {
			return true;
		}

		$post_type_obj = get_post_type_object( $post_type );
		$capability    = $post_type_obj->cap->edit_post ?? 'edit_post';

		return current_user_can( $capability, $post_id );
	}

	/**
	 * Save a single field value
	 *
	 * @param FieldInterface $field     Field instance.
	 * @param int            $post_id   Post ID.
	 * @param string         $post_type Post type.
	 * @return void
	 */
	private function save_single_field( FieldInterface $field, int $post_id, string $post_type ): void {
		$field_name = $field->get_name();

		// Check if value was submitted
		if ( ! isset( $_POST[ $field_name ] ) ) {
			// Delete meta if it existed
			if ( metadata_exists( 'post', $post_id, $field_name ) ) {
				delete_post_meta( $post_id, $field_name );
			}
			return;
		}

		// Get and sanitize value
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw_value = wp_unslash( $_POST[ $field_name ] );

		// Apply filters
		$value = $this->apply_before_save_filters( $raw_value, $field_name, $post_type );
		if ( null === $value ) {
			return;
		}

		// Sanitize and validate
		$result = $this->sanitize_and_validate( $field, $value );

		if ( $result['valid'] ) {
			update_post_meta( $post_id, $field_name, $result['value'] );
		}
	}

	/**
	 * Enqueue assets for CPT edit screens
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		// Check if we're on a post type edit screen
		foreach ( $this->post_types as $post_type => $cpt ) {
			if ( $screen->post_type === $post_type && $this->has_fields( $post_type ) ) {
				$this->enqueue_field_assets( $post_type );
				$this->enqueue_common_assets();
				break;
			}
		}
	}

	/**
	 * Enqueue assets for fields
	 *
	 * @param string $post_type Post type slug.
	 * @return void
	 */
	private function enqueue_field_assets( string $post_type ): void {
		foreach ( $this->get_fields( $post_type ) as $field ) {
			if ( method_exists( $field, 'enqueue_assets' ) ) {
				$field->enqueue_assets();
			}
		}
	}

	/**
	 * Enqueue common WP-CMF assets
	 *
	 * @return void
	 */
	private function enqueue_common_assets(): void {
		if ( ! function_exists( 'wp_enqueue_style' ) ) {
			return;
		}

		$url     = $this->get_assets_url();
		$version = $this->get_version();

		wp_enqueue_style( 'wp-cmf', $url . 'css/wp-cmf.css', array(), $version );
		wp_enqueue_script( 'wp-cmf', $url . 'js/wp-cmf.js', array( 'jquery', 'wp-color-picker' ), $version, true );
		wp_enqueue_style( 'wp-color-picker' );

		do_action( 'wp_cmf_enqueue_common_assets' );
	}
}
