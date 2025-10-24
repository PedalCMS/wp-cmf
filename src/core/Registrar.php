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
	 * @param string               $context Context (post_type slug or settings page ID).
	 * @param array<string, mixed> $fields  Field definitions.
	 * @return self
	 */
	public function add_fields( string $context, array $fields ): self {
		if ( ! isset( $this->fields[ $context ] ) ) {
			$this->fields[ $context ] = array();
		}
		$this->fields[ $context ] = array_merge( $this->fields[ $context ], $fields );
		return $this;
	}

	/**
	 * Register custom post types with WordPress
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
	 * @return void
	 */
	public function register_settings_fields(): void {
		// TODO: Implement settings field registration
		// This will be implemented when field classes are available in Milestone 3
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
	 * @return void
	 */
	protected function enqueue_common_assets(): void {
		// Hook for plugins/themes to add common WP-CMF assets
		if ( function_exists( 'do_action' ) ) {
			do_action( 'wp_cmf_enqueue_common_assets' );
		}
	}

	/**
	 * Register meta boxes for custom post types
	 *
	 * @return void
	 */
	public function register_meta_boxes(): void {
		// TODO: Implement meta box registration
		// This will be implemented when field classes are available in Milestone 3
	}

	/**
	 * Save meta box data
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta_box_data( int $post_id ): void {
		// TODO: Implement meta box data saving
		// This will be implemented when field classes are available in Milestone 3
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
