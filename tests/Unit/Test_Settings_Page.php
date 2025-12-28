<?php
/**
 * Settings Page Registration Tests
 *
 * Tests for registering settings pages with WP-CMF.
 *
 * @package Pedalcms\WpCmf\Tests\Unit
 */

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Settings\SettingsPage;

/**
 * Class Test_Settings_Page
 *
 * Tests for settings page registration.
 */
class Test_Settings_Page extends WP_UnitTestCase {

	/**
	 * Reset Manager between tests.
	 */
	public function set_up(): void {
		parent::set_up();

		// Reset the Manager singleton.
		$reflection = new ReflectionClass( Manager::class );
		$instance = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );

		// Set current user as admin for capability checks.
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );
	}

	/**
	 * Test creating a SettingsPage instance.
	 */
	public function test_settings_page_creation(): void {
		$page = new SettingsPage( 'test_settings' );

		$this->assertInstanceOf( SettingsPage::class, $page );
		$this->assertSame( 'test_settings', $page->get_config( 'menu_slug', 'test_settings' ) );
	}

	/**
	 * Test SettingsPage with custom title.
	 */
	public function test_settings_page_with_title(): void {
		$page = new SettingsPage( 'test_settings' );
		$page->set_page_title( 'Test Settings' );
		$page->set_menu_title( 'Test' );

		$this->assertSame( 'Test Settings', $page->get_config( 'page_title' ) );
		$this->assertSame( 'Test', $page->get_config( 'menu_title' ) );
	}

	/**
	 * Test registering settings page via Manager.
	 */
	public function test_register_settings_page_via_manager(): void {
		$manager = Manager::init();

		$page = new SettingsPage( 'test_settings' );
		$page->set_page_title( 'Test Settings' );
		$page->set_menu_title( 'Test' );
		$page->set_capability( 'manage_options' );

		$manager->get_new_settings_handler()->add_page_instance( $page );

		// Trigger admin_menu action.
		do_action( 'admin_menu' );

		// Verify the page was added.
		$handler = $manager->get_new_settings_handler();
		$this->assertTrue( $handler->has_page( 'test_settings' ) );
	}

	/**
	 * Test registering settings page from array config.
	 */
	public function test_register_settings_page_from_array(): void {
		$manager = Manager::init();

		$manager->register_from_array(
			array(
				'settings_pages' => array(
					array(
						'id'         => 'test_settings',
						'page_title' => 'Test Settings',
						'menu_title' => 'Test',
						'capability' => 'manage_options',
					),
				),
			)
		);

		$handler = $manager->get_new_settings_handler();
		$this->assertTrue( $handler->has_page( 'test_settings' ) );
	}

	/**
	 * Test settings page with fields.
	 */
	public function test_settings_page_with_fields(): void {
		$manager = Manager::init();

		$manager->register_from_array(
			array(
				'settings_pages' => array(
					array(
						'id'         => 'test_settings',
						'page_title' => 'Test Settings',
						'menu_title' => 'Test',
						'capability' => 'manage_options',
						'fields'     => array(
							array(
								'name'  => 'test_field',
								'type'  => 'text',
								'label' => 'Test Field',
							),
							array(
								'name'  => 'test_email',
								'type'  => 'email',
								'label' => 'Test Email',
							),
						),
					),
				),
			)
		);

		$handler = $manager->get_new_settings_handler();
		$fields = $handler->get_fields( 'test_settings' );

		$this->assertCount( 2, $fields );
	}

	/**
	 * Test adding fields to existing WordPress settings page.
	 */
	public function test_add_fields_to_existing_settings_page(): void {
		$manager = Manager::init();

		$handler = $manager->get_existing_settings_handler();

		// Add a field to the General settings page.
		$handler->add_fields(
			'options-general.php',
			array(
				array(
					'name'  => 'custom_site_tagline',
					'type'  => 'text',
					'label' => 'Custom Tagline',
				),
			)
		);

		$fields = $handler->get_fields( 'options-general.php' );

		$this->assertCount( 1, $fields );
		$this->assertArrayHasKey( 'custom_site_tagline', $fields );
	}

	/**
	 * Test is_wordpress_page for General settings.
	 */
	public function test_is_wordpress_page_general(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'general' ) );
	}

	/**
	 * Test is_wordpress_page for Writing settings.
	 */
	public function test_is_wordpress_page_writing(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'writing' ) );
	}

	/**
	 * Test is_wordpress_page for Reading settings.
	 */
	public function test_is_wordpress_page_reading(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'reading' ) );
	}

	/**
	 * Test is_wordpress_page for Discussion settings.
	 */
	public function test_is_wordpress_page_discussion(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'discussion' ) );
	}

	/**
	 * Test is_wordpress_page for Media settings.
	 */
	public function test_is_wordpress_page_media(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'media' ) );
	}

	/**
	 * Test is_wordpress_page for Permalinks settings.
	 */
	public function test_is_wordpress_page_permalinks(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertTrue( $handler->is_wordpress_page( 'permalink' ) );
	}

	/**
	 * Test is_wordpress_page returns false for custom pages.
	 */
	public function test_is_wordpress_page_returns_false_for_custom(): void {
		$handler = Manager::init()->get_existing_settings_handler();

		$this->assertFalse( $handler->is_wordpress_page( 'my-custom-page' ) );
	}
}
