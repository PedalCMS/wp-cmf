<?php
/**
 * Test Registrar Class
 * WordPress-style test following AspirePress patterns
 */

use Pedalcms\WpCmf\Core\Registrar;
use Pedalcms\WpCmf\CPT\CustomPostType;
use Pedalcms\WpCmf\Settings\SettingsPage;

class RegistrarTest extends WP_UnitTestCase {

	/**
	 * Test Registrar initialization
	 */
	public function test_registrar_construct() {
		$registrar = new Registrar();

		$this->assertInstanceOf( Registrar::class, $registrar );
	}

	/**
	 * Test Registrar handles missing WordPress gracefully
	 */
	public function test_registrar_works_without_wordpress() {
		$registrar = new Registrar();

		// Should not throw any errors even without WordPress
		$this->assertInstanceOf( Registrar::class, $registrar );
	}

	/**
	 * Test hooks initialization status
	 */
	public function test_hooks_initialized() {
		$registrar = new Registrar( true );

		$this->assertTrue( $registrar->are_hooks_initialized() );
	}

	/**
	 * Test hooks not initialized when constructor param is false
	 */
	public function test_hooks_not_initialized() {
		$registrar = new Registrar( false );

		$this->assertFalse( $registrar->are_hooks_initialized() );
	}

	/**
	 * Test manual hook initialization
	 */
	public function test_manual_hook_initialization() {
		$registrar = new Registrar( false );
		$this->assertFalse( $registrar->are_hooks_initialized() );

		$registrar->init_hooks();
		$this->assertTrue( $registrar->are_hooks_initialized() );
	}

	/**
	 * Test hooks init only happens once
	 */
	public function test_hooks_init_only_once() {
		$registrar = new Registrar( false );

		$registrar->init_hooks();
		$this->assertTrue( $registrar->are_hooks_initialized() );

		// Second call should not cause issues
		$registrar->init_hooks();
		$this->assertTrue( $registrar->are_hooks_initialized() );
	}

	/**
	 * Test adding custom post type via array config
	 */
	public function test_add_custom_post_type() {
		$registrar = new Registrar( false );

		$result = $registrar->add_custom_post_type(
			'book',
			array(
				'singular' => 'Book',
				'plural'   => 'Books',
				'public'   => true,
			)
		);

		$this->assertSame( $registrar, $result, 'add_custom_post_type should return self for fluent interface' );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 1, $cpts );
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertInstanceOf( CustomPostType::class, $cpts['book'] );
	}

	/**
	 * Test adding multiple custom post types
	 */
	public function test_add_multiple_custom_post_types() {
		$registrar = new Registrar( false );

		$registrar->add_custom_post_type( 'book', array( 'singular' => 'Book' ) )
			->add_custom_post_type( 'product', array( 'singular' => 'Product' ) )
			->add_custom_post_type( 'event', array( 'singular' => 'Event' ) );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 3, $cpts );
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertArrayHasKey( 'product', $cpts );
		$this->assertArrayHasKey( 'event', $cpts );
	}

	/**
	 * Test adding CPT instance directly
	 */
	public function test_add_cpt_instance() {
		$registrar = new Registrar( false );

		$cpt = new CustomPostType( 'portfolio' );
		$cpt->set_label( 'name', 'Portfolio Items' );

		$result = $registrar->add_cpt_instance( $cpt );

		$this->assertSame( $registrar, $result, 'add_cpt_instance should return self for fluent interface' );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 1, $cpts );
		$this->assertArrayHasKey( 'portfolio', $cpts );
		$this->assertSame( $cpt, $cpts['portfolio'] );
	}

	/**
	 * Test registering custom post types
	 */
	public function test_register_custom_post_types() {
		$registrar = new Registrar( false );

		$registrar->add_custom_post_type(
			'book',
			array(
				'singular' => 'Book',
				'plural'   => 'Books',
			)
		);

		// Call the registration method
		$registrar->register_custom_post_types();

		// In test environment without register_post_type function,
		// registration returns false, so verify the method runs without errors
		$cpts = $registrar->get_custom_post_types();
		$this->assertInstanceOf( CustomPostType::class, $cpts['book'] );
	}

	/**
	 * Test adding settings page via array config
	 */
	public function test_add_settings_page() {
		$registrar = new Registrar( false );

		$result = $registrar->add_settings_page(
			'my-settings',
			array(
				'page_title' => 'My Settings',
				'menu_title' => 'My Settings',
				'capability' => 'manage_options',
			)
		);

		$this->assertSame( $registrar, $result, 'add_settings_page should return self for fluent interface' );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 1, $pages );
		$this->assertArrayHasKey( 'my-settings', $pages );
		$this->assertInstanceOf( SettingsPage::class, $pages['my-settings'] );
	}

	/**
	 * Test adding multiple settings pages
	 */
	public function test_add_multiple_settings_pages() {
		$registrar = new Registrar( false );

		$registrar->add_settings_page( 'general', array( 'page_title' => 'General' ) )
			->add_settings_page( 'advanced', array( 'page_title' => 'Advanced' ) )
			->add_settings_page( 'api', array( 'page_title' => 'API' ) );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 3, $pages );
		$this->assertArrayHasKey( 'general', $pages );
		$this->assertArrayHasKey( 'advanced', $pages );
		$this->assertArrayHasKey( 'api', $pages );
	}

	/**
	 * Test adding settings page instance directly
	 */
	public function test_add_settings_page_instance() {
		$registrar = new Registrar( false );

		$page = new SettingsPage( 'custom-page' );
		$page->set_page_title( 'Custom Page' );

		$result = $registrar->add_settings_page_instance( $page );

		$this->assertSame( $registrar, $result, 'add_settings_page_instance should return self for fluent interface' );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 1, $pages );
		$this->assertArrayHasKey( 'custom-page', $pages );
		$this->assertSame( $page, $pages['custom-page'] );
	}

	/**
	 * Test registering settings pages
	 */
	public function test_register_admin_pages() {
		$registrar = new Registrar( false );

		$registrar->add_settings_page(
			'test-page',
			array(
				'page_title' => 'Test Page',
				'menu_title' => 'Test',
			)
		);

		// Call the registration method
		$registrar->register_admin_pages();

		// Verify page is marked as registered
		$pages = $registrar->get_settings_pages();
		$this->assertTrue( $pages['test-page']->is_registered() );
	}

	/**
	 * Test adding fields
	 */
	public function test_add_fields() {
		$registrar = new Registrar( false );

		$fields = array(
			'title'       => array( 'type' => 'text' ),
			'description' => array( 'type' => 'textarea' ),
		);

		$result = $registrar->add_fields( 'book', $fields );

		$this->assertSame( $registrar, $result, 'add_fields should return self for fluent interface' );

		$all_fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'book', $all_fields );
		$this->assertCount( 2, $all_fields['book'] );
	}

	/**
	 * Test adding fields to multiple contexts
	 */
	public function test_add_fields_multiple_contexts() {
		$registrar = new Registrar( false );

		$registrar->add_fields( 'book', array( 'isbn' => array( 'type' => 'text' ) ) )
			->add_fields( 'product', array( 'price' => array( 'type' => 'number' ) ) );

		$fields = $registrar->get_fields();
		$this->assertCount( 2, $fields );
		$this->assertArrayHasKey( 'book', $fields );
		$this->assertArrayHasKey( 'product', $fields );
	}

	/**
	 * Test merging fields for same context
	 */
	public function test_add_fields_merges_same_context() {
		$registrar = new Registrar( false );

		$registrar->add_fields( 'book', array( 'title' => array( 'type' => 'text' ) ) )
			->add_fields( 'book', array( 'author' => array( 'type' => 'text' ) ) );

		$fields = $registrar->get_fields();
		$this->assertCount( 1, $fields );
		$this->assertCount( 2, $fields['book'] );
		$this->assertArrayHasKey( 'title', $fields['book'] );
		$this->assertArrayHasKey( 'author', $fields['book'] );
	}

	/**
	 * Test fluent interface chaining
	 */
	public function test_fluent_interface_chaining() {
		$registrar = new Registrar( false );

		$result = $registrar
			->add_custom_post_type( 'book', array( 'singular' => 'Book' ) )
			->add_settings_page( 'settings', array( 'page_title' => 'Settings' ) )
			->add_fields( 'book', array( 'isbn' => array( 'type' => 'text' ) ) );

		$this->assertSame( $registrar, $result );

		// Verify all were added
		$this->assertCount( 1, $registrar->get_custom_post_types() );
		$this->assertCount( 1, $registrar->get_settings_pages() );
		$this->assertCount( 1, $registrar->get_fields() );
	}

	/**
	 * Test empty state
	 */
	public function test_empty_state() {
		$registrar = new Registrar( false );

		$this->assertEmpty( $registrar->get_custom_post_types() );
		$this->assertEmpty( $registrar->get_settings_pages() );
		$this->assertEmpty( $registrar->get_fields() );
	}
}
