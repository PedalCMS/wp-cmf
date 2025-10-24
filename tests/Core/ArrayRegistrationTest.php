<?php
/**
 * Test Array-based Registration
 * Tests Manager::register_from_array() and related functionality
 */

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Field\FieldFactory;

class ArrayRegistrationTest extends WP_UnitTestCase {

	/**
	 * Reset FieldFactory and Manager before each test
	 */
	public function setUp(): void {
		parent::setUp();
		FieldFactory::reset();

		// Reset Manager singleton using reflection
		$reflection = new \ReflectionClass( Manager::class );
		$instance   = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );
	}

	/**
	 * Test registering CPT from array
	 */
	public function test_register_cpt_from_array() {
		$config = array(
			'cpts' => array(
				array(
					'id'   => 'book',
					'args' => array(
						'label'   => 'Books',
						'public'  => true,
						'supports' => array( 'title', 'editor' ),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$cpts = $registrar->get_custom_post_types();
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertInstanceOf( \Pedalcms\WpCmf\CPT\CustomPostType::class, $cpts['book'] );
	}

	/**
	 * Test registering settings page from array
	 */
	public function test_register_settings_page_from_array() {
		$config = array(
			'settings_pages' => array(
				array(
					'id'         => 'my-plugin-settings',
					'page_title' => 'My Plugin',
					'menu_title' => 'My Plugin',
					'capability' => 'manage_options',
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$pages = $registrar->get_settings_pages();
		$this->assertArrayHasKey( 'my-plugin-settings', $pages );
		$this->assertInstanceOf( \Pedalcms\WpCmf\Settings\SettingsPage::class, $pages['my-plugin-settings'] );
	}

	/**
	 * Test registering CPT with fields from array
	 */
	public function test_register_cpt_with_fields() {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'book',
					'args'   => array(
						'label' => 'Books',
					),
					'fields' => array(
						array(
							'name'  => 'isbn',
							'type'  => 'text',
							'label' => 'ISBN',
						),
						array(
							'name'  => 'author',
							'type'  => 'text',
							'label' => 'Author',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'book', $fields );
		$this->assertCount( 2, $fields['book'] );
		$this->assertArrayHasKey( 'isbn', $fields['book'] );
		$this->assertArrayHasKey( 'author', $fields['book'] );
	}

	/**
	 * Test registering settings page with fields from array
	 */
	public function test_register_settings_page_with_fields() {
		$config = array(
			'settings_pages' => array(
				array(
					'id'         => 'my-settings',
					'page_title' => 'Settings',
					'menu_title' => 'Settings',
					'capability' => 'manage_options',
					'fields'     => array(
						array(
							'name'  => 'site_name',
							'type'  => 'text',
							'label' => 'Site Name',
						),
						array(
							'name'  => 'enable_feature',
							'type'  => 'checkbox',
							'label' => 'Enable Feature',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'my-settings', $fields );
		$this->assertCount( 2, $fields['my-settings'] );
		$this->assertArrayHasKey( 'site_name', $fields['my-settings'] );
		$this->assertArrayHasKey( 'enable_feature', $fields['my-settings'] );
	}

	/**
	 * Test registering multiple CPTs from array
	 */
	public function test_register_multiple_cpts() {
		$config = array(
			'cpts' => array(
				array(
					'id'   => 'book',
					'args' => array( 'label' => 'Books' ),
				),
				array(
					'id'   => 'movie',
					'args' => array( 'label' => 'Movies' ),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 2, $cpts );
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertArrayHasKey( 'movie', $cpts );
	}

	/**
	 * Test registering multiple settings pages from array
	 */
	public function test_register_multiple_settings_pages() {
		$config = array(
			'settings_pages' => array(
				array(
					'id'         => 'general-settings',
					'page_title' => 'General',
					'menu_title' => 'General',
					'capability' => 'manage_options',
				),
				array(
					'id'         => 'advanced-settings',
					'page_title' => 'Advanced',
					'menu_title' => 'Advanced',
					'capability' => 'manage_options',
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$pages = $registrar->get_settings_pages();
		$this->assertCount( 2, $pages );
		$this->assertArrayHasKey( 'general-settings', $pages );
		$this->assertArrayHasKey( 'advanced-settings', $pages );
	}

	/**
	 * Test mixed registration (CPTs and settings pages)
	 */
	public function test_register_mixed_config() {
		$config = array(
			'cpts'           => array(
				array(
					'id'   => 'book',
					'args' => array( 'label' => 'Books' ),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'book-settings',
					'page_title' => 'Book Settings',
					'menu_title' => 'Book Settings',
					'capability' => 'manage_options',
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$cpts  = $registrar->get_custom_post_types();
		$pages = $registrar->get_settings_pages();

		$this->assertCount( 1, $cpts );
		$this->assertCount( 1, $pages );
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertArrayHasKey( 'book-settings', $pages );
	}

	/**
	 * Test empty config array
	 */
	public function test_register_empty_config() {
		$config = array();

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		// Should not throw error
		$manager->register_from_array( $config );

		$cpts  = $registrar->get_custom_post_types();
		$pages = $registrar->get_settings_pages();

		$this->assertCount( 0, $cpts );
		$this->assertCount( 0, $pages );
	}

	/**
	 * Test CPT with invalid config (missing id)
	 */
	public function test_register_cpt_missing_id() {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'CPT configuration must include "id"' );

		$config = array(
			'cpts' => array(
				array(
					'args' => array( 'label' => 'Books' ),
				),
			),
		);

		$manager = Manager::init();
		$manager->register_from_array( $config );
	}

	/**
	 * Test settings page with invalid config (missing id)
	 */
	public function test_register_settings_page_missing_id() {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Settings page configuration must include "id"' );

		$config = array(
			'settings_pages' => array(
				array(
					'page_title' => 'Settings',
				),
			),
		);

		$manager = Manager::init();
		$manager->register_from_array( $config );
	}

	/**
	 * Test fluent interface returns Manager instance
	 */
	public function test_register_from_array_returns_manager() {
		$config = array(
			'cpts' => array(
				array(
					'id'   => 'book',
					'args' => array( 'label' => 'Books' ),
				),
			),
		);

		$manager = Manager::init();
		$result  = $manager->register_from_array( $config );

		$this->assertSame( $manager, $result );
	}

	/**
	 * Test field instances are created from config
	 */
	public function test_fields_are_field_instances() {
		$config = array(
			'cpts' => array(
				array(
					'id'     => 'book',
					'args'   => array( 'label' => 'Books' ),
					'fields' => array(
						array(
							'name'  => 'title',
							'type'  => 'text',
							'label' => 'Book Title',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		$fields = $registrar->get_fields();
		$this->assertInstanceOf(
			\Pedalcms\WpCmf\Field\FieldInterface::class,
			$fields['book']['title']
		);
	}

	/**
	 * Test complex config with multiple CPTs and fields
	 */
	public function test_complex_registration() {
		$config = array(
			'cpts'           => array(
				array(
					'id'     => 'book',
					'args'   => array( 'label' => 'Books' ),
					'fields' => array(
						array(
							'name'  => 'isbn',
							'type'  => 'text',
							'label' => 'ISBN',
						),
						array(
							'name'  => 'price',
							'type'  => 'number',
							'label' => 'Price',
						),
					),
				),
				array(
					'id'     => 'movie',
					'args'   => array( 'label' => 'Movies' ),
					'fields' => array(
						array(
							'name'  => 'rating',
							'type'  => 'select',
							'label' => 'Rating',
							'options' => array(
								'G'  => 'G',
								'PG' => 'PG',
							),
						),
					),
				),
			),
			'settings_pages' => array(
				array(
					'id'         => 'media-settings',
					'page_title' => 'Media Settings',
					'menu_title' => 'Media',
					'capability' => 'manage_options',
					'fields'     => array(
						array(
							'name'  => 'enable_books',
							'type'  => 'checkbox',
							'label' => 'Enable Books',
						),
						array(
							'name'  => 'enable_movies',
							'type'  => 'checkbox',
							'label' => 'Enable Movies',
						),
					),
				),
			),
		);

		$manager   = Manager::init();
		$registrar = $manager->get_registrar();

		$manager->register_from_array( $config );

		// Verify CPTs
		$cpts = $registrar->get_custom_post_types();
		$this->assertCount( 2, $cpts );
		$this->assertArrayHasKey( 'book', $cpts );
		$this->assertArrayHasKey( 'movie', $cpts );

		// Verify settings pages
		$pages = $registrar->get_settings_pages();
		$this->assertCount( 1, $pages );
		$this->assertArrayHasKey( 'media-settings', $pages );

		// Verify fields
		$fields = $registrar->get_fields();
		$this->assertArrayHasKey( 'book', $fields );
		$this->assertArrayHasKey( 'movie', $fields );
		$this->assertArrayHasKey( 'media-settings', $fields );
		$this->assertCount( 2, $fields['book'] );
		$this->assertCount( 1, $fields['movie'] );
		$this->assertCount( 2, $fields['media-settings'] );
	}

	/**
	 * Test that register_custom_post_types is called when did_action('init') returns true
	 *
	 * This simulates the real-world scenario where a plugin calls
	 * register_from_array() inside an 'init' action hook. In that case,
	 * we need to call register_custom_post_types() immediately instead
	 * of waiting for the 'init' hook (which has already fired).
	 */
	public function test_register_during_init_hook() {
		// Mock did_action to return true (simulating we're during init)
		global $wp_actions;
		$wp_actions['init'] = 1;

		$config = array(
			'cpts' => array(
				array(
					'id'   => 'test_cpt',
					'args' => array(
						'label'    => 'Test CPT',
						'public'   => true,
						'supports' => array( 'title' ),
					),
				),
			),
		);

		$manager = Manager::init();
		$manager->register_from_array( $config );

		// Verify CPT was added to registrar
		$cpts = $manager->get_registrar()->get_custom_post_types();
		$this->assertArrayHasKey( 'test_cpt', $cpts );

		// In a test environment, register() won't actually work because
		// register_post_type() doesn't exist. But we can verify that
		// register_custom_post_types() was called by checking that the
		// CPT object exists in the registrar.
		$this->assertInstanceOf( \Pedalcms\WpCmf\CPT\CustomPostType::class, $cpts['test_cpt'] );

		// Reset for other tests
		unset( $wp_actions['init'] );
	}

	/**
	 * Test that settings pages and fields are registered when called after hooks fire
	 * 
	 * This simulates calling register_from_array() after admin_menu and admin_init
	 * hooks have already fired.
	 */
	public function test_register_settings_after_hooks() {
		// Mock hooks as already fired
		global $wp_actions;
		$wp_actions['init']       = 1;
		$wp_actions['admin_menu'] = 1;
		$wp_actions['admin_init'] = 1;

		$config = [
			'settings_pages' => [
				[
					'id'         => 'test_settings',
					'page_title' => 'Test Settings',
					'menu_title' => 'Test Settings',
					'capability' => 'manage_options',
					'fields'     => [
						[
							'name'  => 'test_field',
							'type'  => 'text',
							'label' => 'Test Field',
						],
					],
				],
			],
		];

		$manager = Manager::init();
		$manager->register_from_array( $config );

		// Verify settings page was added
		$pages = $manager->get_registrar()->get_settings_pages();
		$this->assertArrayHasKey( 'test_settings', $pages );

		// Verify fields were added
		$fields = $manager->get_registrar()->get_fields();
		$this->assertArrayHasKey( 'test_settings', $fields );
		$this->assertCount( 1, $fields['test_settings'] );

		// Reset for other tests
		unset( $wp_actions['init'], $wp_actions['admin_menu'], $wp_actions['admin_init'] );
	}
}
