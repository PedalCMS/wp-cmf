<?php
/**
 * Tests for SettingsPage class
 *
 * @package Pedalcms\WpCmf
 */

namespace Pedalcms\WpCmf\Tests\Settings;

use Pedalcms\WpCmf\Settings\SettingsPage;
use WP_UnitTestCase;

/**
 * SettingsPage test case
 */
class SettingsPageTest extends WP_UnitTestCase {

	/**
	 * Test constructor with page ID
	 */
	public function test_constructor_with_page_id() {
		$page = new SettingsPage( 'test-page' );

		$this->assertInstanceOf( SettingsPage::class, $page );
		$this->assertEquals( 'test-page', $page->get_page_id() );
	}

	/**
	 * Test constructor with configuration
	 */
	public function test_constructor_with_configuration() {
		$config = [
			'page_title' => 'Test Page',
			'menu_title' => 'Test Menu',
			'capability' => 'edit_posts',
		];

		$page = new SettingsPage( 'test-page', $config );

		$this->assertEquals( 'Test Page', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'Test Menu', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'edit_posts', $page->get_config( 'capability' ) );
	}

	/**
	 * Test fluent interface setters
	 */
	public function test_fluent_interface() {
		$page = new SettingsPage( 'test-page' );

		$result = $page
			->set_page_title( 'My Settings Page' )
			->set_menu_title( 'My Settings' )
			->set_capability( 'manage_options' )
			->set_menu_slug( 'my-settings' )
			->set_icon( 'dashicons-admin-generic' )
			->set_position( 50 );

		// Verify fluent interface returns self
		$this->assertSame( $page, $result );

		// Verify values were set
		$this->assertEquals( 'My Settings Page', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'My Settings', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
		$this->assertEquals( 'my-settings', $page->get_config( 'menu_slug' ) );
		$this->assertEquals( 'dashicons-admin-generic', $page->get_config( 'icon_url' ) );
		$this->assertEquals( 50, $page->get_config( 'position' ) );
	}

	/**
	 * Test setting parent slug for submenu page
	 */
	public function test_set_parent_for_submenu() {
		$page = new SettingsPage( 'test-submenu' );
		$page->set_parent( 'options-general.php' );

		$this->assertEquals( 'options-general.php', $page->get_config( 'parent_slug' ) );
		$this->assertTrue( $page->is_submenu() );
	}

	/**
	 * Test is_submenu returns false for top-level page
	 */
	public function test_is_submenu_false_for_toplevel() {
		$page = new SettingsPage( 'test-toplevel' );

		$this->assertFalse( $page->is_submenu() );
	}

	/**
	 * Test set_defaults generates proper defaults
	 */
	public function test_set_defaults() {
		$page = new SettingsPage( 'test_page_slug' );
		$page->set_defaults();

		$this->assertEquals( 'Test Page Slug', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'Test Page Slug', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
		$this->assertEquals( 'test_page_slug', $page->get_config( 'menu_slug' ) );
		$this->assertIsCallable( $page->get_config( 'callback' ) );
	}

	/**
	 * Test set_defaults preserves existing configuration
	 */
	public function test_set_defaults_preserves_existing() {
		$page = new SettingsPage( 'test-page' );
		$page->set_page_title( 'Custom Title' );
		$page->set_defaults();

		$this->assertEquals( 'Custom Title', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'Test Page', $page->get_config( 'menu_title' ) );
	}

	/**
	 * Test custom callback function
	 */
	public function test_set_callback() {
		$callback = function() {
			echo 'Custom callback';
		};

		$page = new SettingsPage( 'test-page' );
		$page->set_callback( $callback );

		$this->assertSame( $callback, $page->get_config( 'callback' ) );
	}

	/**
	 * Test registration of top-level page
	 */
	public function test_register_toplevel_page() {
		$page = new SettingsPage( 'test-toplevel' );
		$page->set_page_title( 'Test Top Level' )
			->set_menu_title( 'Test Menu' )
			->set_capability( 'manage_options' )
			->set_menu_slug( 'test-toplevel-slug' );

		$result = $page->register();

		$this->assertTrue( $result );
		$this->assertTrue( $page->is_registered() );
		$this->assertNotFalse( $page->get_hook_suffix() );
	}

	/**
	 * Test registration of submenu page
	 */
	public function test_register_submenu_page() {
		$page = new SettingsPage( 'test-submenu' );
		$page->set_page_title( 'Test Submenu' )
			->set_menu_title( 'Test Sub' )
			->set_parent( 'options-general.php' )
			->set_capability( 'manage_options' )
			->set_menu_slug( 'test-submenu-slug' );

		$result = $page->register();

		$this->assertTrue( $result );
		$this->assertTrue( $page->is_registered() );
		$this->assertTrue( $page->is_submenu() );
		$this->assertNotFalse( $page->get_hook_suffix() );
	}

	/**
	 * Test registration only happens once
	 */
	public function test_register_only_once() {
		$page = new SettingsPage( 'test-once' );
		$page->set_defaults();

		$first_result = $page->register();
		$second_result = $page->register();

		$this->assertTrue( $first_result );
		$this->assertTrue( $second_result );
		$this->assertTrue( $page->is_registered() );
	}

	/**
	 * Test from_array factory method
	 */
	public function test_from_array_factory() {
		$config = [
			'page_title' => 'Factory Test',
			'menu_title' => 'Factory Menu',
			'capability' => 'edit_posts',
			'menu_slug'  => 'factory-test',
		];

		$page = SettingsPage::from_array( 'factory-page', $config );

		$this->assertInstanceOf( SettingsPage::class, $page );
		$this->assertEquals( 'factory-page', $page->get_page_id() );
		$this->assertEquals( 'Factory Test', $page->get_config( 'page_title' ) );
		$this->assertEquals( 'Factory Menu', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'edit_posts', $page->get_config( 'capability' ) );
	}

	/**
	 * Test from_array factory sets defaults
	 */
	public function test_from_array_sets_defaults() {
		$config = [
			'page_title' => 'Factory Test',
		];

		$page = SettingsPage::from_array( 'factory-page', $config );

		// Should have defaults set
		$this->assertEquals( 'manage_options', $page->get_config( 'capability' ) );
		$this->assertEquals( 'factory-page', $page->get_config( 'menu_slug' ) );
		$this->assertIsCallable( $page->get_config( 'callback' ) );
	}

	/**
	 * Test get_all_config returns complete configuration
	 */
	public function test_get_all_config() {
		$page = new SettingsPage( 'test-page' );
		$page->set_page_title( 'Test' )
			->set_menu_title( 'Menu' )
			->set_capability( 'edit_posts' );

		$config = $page->get_all_config();

		$this->assertIsArray( $config );
		$this->assertArrayHasKey( 'page_title', $config );
		$this->assertArrayHasKey( 'menu_title', $config );
		$this->assertArrayHasKey( 'capability', $config );
		$this->assertEquals( 'Test', $config['page_title'] );
	}

	/**
	 * Test configure method merges configuration
	 */
	public function test_configure_merges_config() {
		$page = new SettingsPage( 'test-page' );
		$page->set_page_title( 'Initial Title' );

		$page->configure( [
			'menu_title' => 'New Menu',
			'capability' => 'edit_posts',
		] );

		// Original title should remain
		$this->assertEquals( 'Initial Title', $page->get_config( 'page_title' ) );
		// New values should be set
		$this->assertEquals( 'New Menu', $page->get_config( 'menu_title' ) );
		$this->assertEquals( 'edit_posts', $page->get_config( 'capability' ) );
	}

	/**
	 * Test get_menu_slug returns page_id as fallback
	 */
	public function test_get_menu_slug_fallback() {
		$page = new SettingsPage( 'test-page-id' );

		$this->assertEquals( 'test-page-id', $page->get_menu_slug() );
	}

	/**
	 * Test get_menu_slug returns configured slug
	 */
	public function test_get_menu_slug_configured() {
		$page = new SettingsPage( 'test-page-id' );
		$page->set_menu_slug( 'custom-slug' );

		$this->assertEquals( 'custom-slug', $page->get_menu_slug() );
	}

	/**
	 * Test set_config and get_config with custom keys
	 */
	public function test_custom_config_keys() {
		$page = new SettingsPage( 'test-page' );
		$page->set_config( 'custom_key', 'custom_value' );

		$this->assertEquals( 'custom_value', $page->get_config( 'custom_key' ) );
	}

	/**
	 * Test get_config returns default when key not found
	 */
	public function test_get_config_with_default() {
		$page = new SettingsPage( 'test-page' );

		$this->assertEquals( 'default_value', $page->get_config( 'nonexistent_key', 'default_value' ) );
		$this->assertNull( $page->get_config( 'nonexistent_key' ) );
	}
}
