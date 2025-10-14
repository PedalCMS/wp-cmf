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
	private array $custom_post_types = [];

	/**
	 * Registered settings pages
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $settings_pages = [];

	/**
	 * Registered field definitions
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $fields = [];

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
		add_action( 'init', [ $this, 'register_custom_post_types' ] );

		// Register admin pages and settings
		add_action( 'admin_menu', [ $this, 'register_admin_pages' ] );
		add_action( 'admin_init', [ $this, 'register_settings_fields' ] );

		// Register meta boxes for CPTs
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );

		// Handle form submissions
		add_action( 'save_post', [ $this, 'save_meta_box_data' ] );

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
		$cpt = CustomPostType::from_array( $post_type, $args );
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
		$this->settings_pages[ $page_id ] = $args;
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
			$this->fields[ $context ] = [];
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
		foreach ( $this->settings_pages as $page_id => $page_args ) {
			$this->register_single_admin_page( $page_id, $page_args );
		}
	}

	/**
	 * Register a single admin page
	 *
	 * @param string               $page_id   Page identifier.
	 * @param array<string, mixed> $page_args Page arguments.
	 * @return void
	 */
	private function register_single_admin_page( string $page_id, array $page_args ): void {
		$defaults = [
			'page_title' => '',
			'menu_title' => '',
			'capability' => 'manage_options',
			'menu_slug'  => $page_id,
			'callback'   => [ $this, 'render_settings_page' ],
			'icon_url'   => '',
			'position'   => null,
		];

		$args = array_merge( $defaults, $page_args );

		// Determine registration method based on whether it's a top-level or sub-menu page
		if ( isset( $args['parent_slug'] ) ) {
			add_submenu_page(
				$args['parent_slug'],
				$args['page_title'],
				$args['menu_title'],
				$args['capability'],
				$args['menu_slug'],
				$args['callback']
			);
		} else {
			add_menu_page(
				$args['page_title'],
				$args['menu_title'],
				$args['capability'],
				$args['menu_slug'],
				$args['callback'],
				$args['icon_url'],
				$args['position']
			);
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
	 * @return array<string, array<string, mixed>>
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
