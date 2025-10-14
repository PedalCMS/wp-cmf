<?php
/**
 * Integration Tests for WP-CMF
 * Tests config validation and full registration workflows
 *
 * @package Pedalcms\WpCmf
 */

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Core\Registrar;
use Pedalcms\WpCmf\CPT\CustomPostType;
use Pedalcms\WpCmf\Settings\SettingsPage;

class IntegrationTest extends WP_UnitTestCase {

	/**
	 * Test Manager and Registrar integration
	 */
	public function test_manager_registrar_integration() {
		$manager = Manager::init();
		$registrar = $manager->get_registrar();

		$this->assertInstanceOf( Registrar::class, $registrar );
		$this->assertTrue( $registrar->are_hooks_initialized() );
	}

	/**
	 * Test complete CPT registration workflow
	 */
	public function test_complete_cpt_workflow() {
		$manager = Manager::init();
		$registrar = $manager->get_registrar();

		// Add CPT via array config
		$registrar->add_custom_post_type( 'book', [
			'singular' => 'Book',
			'plural'   => 'Books',
			'public'   => true,
			'supports' => [ 'title', 'editor', 'thumbnail' ],
		] );

		// Verify CPT was added
		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 1, $cpts );
		$this->assertArrayHasKey( 'book', $cpts );

		// Verify configuration
		$book_cpt = $cpts['book'];
		$this->assertEquals( 'book', $book_cpt->get_post_type() );
		$this->assertTrue( $book_cpt->get_args()['public'] );
		$this->assertEquals( [ 'title', 'editor', 'thumbnail' ], $book_cpt->get_supports() );
	}

	/**
	 * Test complete settings page workflow
	 */
	public function test_complete_settings_page_workflow() {
		$manager = Manager::init();
		$registrar = $manager->get_registrar();

		// Add settings page via array config
		$registrar->add_settings_page( 'plugin-settings', [
			'page_title' => 'Plugin Settings',
			'menu_title' => 'Plugin',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-generic',
		] );

		// Verify page was added
		$pages = $registrar->get_settings_pages();
		$this->assertCount( 1, $pages );
		$this->assertArrayHasKey( 'plugin-settings', $pages );

		// Verify configuration
		$settings_page = $pages['plugin-settings'];
		$this->assertEquals( 'plugin-settings', $settings_page->get_page_id() );
		$this->assertEquals( 'Plugin Settings', $settings_page->get_config( 'page_title' ) );
		$this->assertEquals( 'manage_options', $settings_page->get_config( 'capability' ) );
	}

	/**
	 * Test CPT with minimal config
	 */
	public function test_cpt_minimal_config() {
		$registrar = new Registrar( false );

		// Add CPT with just post type slug
		$registrar->add_custom_post_type( 'product', [] );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 1, $cpts );

		// Should still create valid CPT instance
		$product_cpt = $cpts['product'];
		$this->assertEquals( 'product', $product_cpt->get_post_type() );
	}

	/**
	 * Test settings page with minimal config
	 */
	public function test_settings_page_minimal_config() {
		$registrar = new Registrar( false );

		// Add page with minimal config
		$registrar->add_settings_page( 'minimal-page', [] );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 1, $pages );

		// Defaults should be applied
		$page = $pages['minimal-page'];
		$page->set_defaults();
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
	}

	/**
	 * Test CPT config validation with full options
	 */
	public function test_cpt_full_config_validation() {
		$config = [
			'singular'        => 'Portfolio Item',
			'plural'          => 'Portfolio Items',
			'public'          => true,
			'show_ui'         => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-portfolio',
			'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
			'has_archive'     => true,
			'rewrite'         => [ 'slug' => 'portfolio' ],
			'capability_type' => 'post',
		];

		$cpt = CustomPostType::from_array( 'portfolio', $config );

		// Verify all configs are applied
		$this->assertEquals( 'portfolio', $cpt->get_post_type() );
		$this->assertEquals( 'Portfolio Items', $cpt->get_labels()['name'] );
		$this->assertEquals( 'Portfolio Item', $cpt->get_labels()['singular_name'] );
		$this->assertTrue( $cpt->get_args()['public'] );
		$this->assertTrue( $cpt->get_args()['show_ui'] );
		$this->assertTrue( $cpt->get_args()['show_in_rest'] );
		$this->assertEquals( 'dashicons-portfolio', $cpt->get_args()['menu_icon'] );
		$this->assertEquals( [ 'title', 'editor', 'thumbnail', 'excerpt' ], $cpt->get_supports() );
		$this->assertTrue( $cpt->get_args()['has_archive'] );
	}

	/**
	 * Test settings page config validation with full options
	 */
	public function test_settings_page_full_config_validation() {
		$config = [
			'page_title' => 'Advanced Settings',
			'menu_title' => 'Advanced',
			'capability' => 'manage_options',
			'menu_slug'  => 'advanced-settings',
			'icon_url'   => 'dashicons-admin-settings',
			'position'   => 85,
		];

		$page = SettingsPage::from_array( 'advanced', $config );

		// Verify all configs are applied
		$this->assertEquals( 'advanced', $page->get_page_id() );
		$this->assertEquals( 'Advanced Settings', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'Advanced', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
		$this->assertEquals( 'advanced-settings', $page->get_config( 'menu_slug' ) );
		$this->assertEquals( 'dashicons-admin-settings', $page->get_config( 'icon_url' ) );
		$this->assertEquals( 85, $page->get_config( 'position' ) );
	}

	/**
	 * Test submenu page config validation
	 */
	public function test_submenu_page_config_validation() {
		$config = [
			'page_title'  => 'Plugin Tools',
			'menu_title'  => 'Tools',
			'parent_slug' => 'options-general.php',
			'capability'  => 'manage_options',
		];

		$page = SettingsPage::from_array( 'plugin-tools', $config );

		$this->assertEquals( 'plugin-tools', $page->get_page_id() );
		$this->assertEquals( 'options-general.php', $page->get_config( 'parent_slug' ) );
		$this->assertTrue( $page->is_submenu() );
	}

	/**
	 * Test multiple CPTs registration
	 */
	public function test_multiple_cpts_registration() {
		$registrar = new Registrar( false );

		// Add multiple CPTs
		$registrar
			->add_custom_post_type( 'book', [ 'singular' => 'Book' ] )
			->add_custom_post_type( 'product', [ 'singular' => 'Product' ] )
			->add_custom_post_type( 'event', [ 'singular' => 'Event' ] );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 3, $cpts );

		// Each should have correct post type
		$this->assertEquals( 'book', $cpts['book']->get_post_type() );
		$this->assertEquals( 'product', $cpts['product']->get_post_type() );
		$this->assertEquals( 'event', $cpts['event']->get_post_type() );
	}

	/**
	 * Test multiple settings pages registration
	 */
	public function test_multiple_settings_pages_registration() {
		$registrar = new Registrar( false );

		// Add multiple pages
		$registrar
			->add_settings_page( 'general', [ 'page_title' => 'General' ] )
			->add_settings_page( 'advanced', [ 'page_title' => 'Advanced' ] )
			->add_settings_page( 'api', [ 'page_title' => 'API Settings' ] );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 3, $pages );

		// Each should have correct configuration
		$this->assertEquals( 'General', $pages['general']->get_config( 'page_title' ) );
		$this->assertEquals( 'Advanced', $pages['advanced']->get_config( 'page_title' ) );
		$this->assertEquals( 'API Settings', $pages['api']->get_config( 'page_title' ) );
	}

	/**
	 * Test mixed registration (CPTs and settings pages)
	 */
	public function test_mixed_registration() {
		$registrar = new Registrar( false );

		// Add CPTs
		$registrar->add_custom_post_type( 'book', [ 'singular' => 'Book' ] );
		$registrar->add_custom_post_type( 'product', [ 'singular' => 'Product' ] );

		// Add settings pages
		$registrar->add_settings_page( 'general', [ 'page_title' => 'General' ] );
		$registrar->add_settings_page( 'api', [ 'page_title' => 'API' ] );

		// Add fields
		$registrar->add_fields( 'book', [ 'isbn' => [ 'type' => 'text' ] ] );

		// Verify all were added
		$this->assertCount( 2, $registrar->get_custom_post_types() );
		$this->assertCount( 2, $registrar->get_settings_pages() );
		$this->assertCount( 1, $registrar->get_fields() );
	}

	/**
	 * Test CPT label generation validation
	 */
	public function test_cpt_label_generation_validation() {
		$cpt = new CustomPostType( 'event' );
		$cpt->generate_labels( 'Event', 'Events' );

		$labels = $cpt->get_labels();

		// Verify key labels are generated correctly
		$this->assertEquals( 'Events', $labels['name'] );
		$this->assertEquals( 'Event', $labels['singular_name'] );
		$this->assertEquals( 'Events', $labels['menu_name'] );
		$this->assertEquals( 'Add New Event', $labels['add_new_item'] );
		$this->assertEquals( 'Edit Event', $labels['edit_item'] );
		$this->assertEquals( 'View Event', $labels['view_item'] );
		$this->assertEquals( 'Search Events', $labels['search_items'] );
	}

	/**
	 * Test settings page default generation validation
	 */
	public function test_settings_page_default_generation() {
		$page = new SettingsPage( 'my_custom_page' );
		$page->set_defaults();

		// Verify defaults are properly generated
		$this->assertEquals( 'My Custom Page', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'My Custom Page', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
		$this->assertEquals( 'my_custom_page', $page->get_config( 'menu_slug' ) );
		$this->assertIsCallable( $page->get_config( 'callback' ) );
	}

	/**
	 * Test CPT with custom capabilities
	 */
	public function test_cpt_custom_capabilities() {
		$config = [
			'singular'        => 'Book',
			'plural'          => 'Books',
			'capability_type' => 'book',
			'map_meta_cap'    => true,
		];

		$cpt = CustomPostType::from_array( 'book', $config );

		$this->assertEquals( 'book', $cpt->get_args()['capability_type'] );
		$this->assertTrue( $cpt->get_args()['map_meta_cap'] );
	}

	/**
	 * Test settings page with custom callback
	 */
	public function test_settings_page_custom_callback() {
		$callback_executed = false;

		$callback = function() use ( &$callback_executed ) {
			$callback_executed = true;
			echo 'Custom callback executed';
		};

		$page = new SettingsPage( 'custom-callback-page' );
		$page->set_callback( $callback );

		// Get the callback and execute it
		$stored_callback = $page->get_config( 'callback' );
		$this->assertIsCallable( $stored_callback );

		ob_start();
		call_user_func( $stored_callback );
		$output = ob_get_clean();

		$this->assertTrue( $callback_executed );
		$this->assertStringContainsString( 'Custom callback executed', $output );
	}

	/**
	 * Test field addition to multiple contexts
	 */
	public function test_field_addition_validation() {
		$registrar = new Registrar( false );

		// Add fields to CPT
		$registrar->add_fields( 'book', [
			'isbn'   => [ 'type' => 'text', 'label' => 'ISBN' ],
			'author' => [ 'type' => 'text', 'label' => 'Author' ],
		] );

		// Add fields to settings page
		$registrar->add_fields( 'general-settings', [
			'site_name' => [ 'type' => 'text', 'label' => 'Site Name' ],
			'api_key'   => [ 'type' => 'text', 'label' => 'API Key' ],
		] );

		$fields = $registrar->get_fields();

		$this->assertCount( 2, $fields );
		$this->assertArrayHasKey( 'book', $fields );
		$this->assertArrayHasKey( 'general-settings', $fields );
		$this->assertCount( 2, $fields['book'] );
		$this->assertCount( 2, $fields['general-settings'] );
	}
}
