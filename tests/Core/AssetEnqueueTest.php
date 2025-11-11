<?php
/**
 * Asset Enqueue Test
 *
 * Tests for WP-CMF asset enqueuing functionality.
 *
 * @package Pedalcms\WpCmf\Tests\Core
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Tests\Core;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Core\Registrar;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * Test asset enqueuing functionality
 */
class AssetEnqueueTest extends TestCase {

	/**
	 * Test that enqueue_field_assets method exists
	 */
	public function test_enqueue_field_assets_method_exists(): void {
		$registrar = new Registrar( false );
		$this->assertTrue( method_exists( $registrar, 'enqueue_field_assets' ) );
	}

	/**
	 * Test that get_assets_url method exists
	 */
	public function test_get_assets_url_method_exists(): void {
		$registrar  = new Registrar( false );
		$reflection = new \ReflectionClass( $registrar );
		$this->assertTrue( $reflection->hasMethod( 'get_assets_url' ) );
	}

	/**
	 * Test that get_version method exists
	 */
	public function test_get_version_method_exists(): void {
		$registrar  = new Registrar( false );
		$reflection = new \ReflectionClass( $registrar );
		$this->assertTrue( $reflection->hasMethod( 'get_version' ) );
	}

	/**
	 * Test that enqueue_common_assets method exists
	 */
	public function test_enqueue_common_assets_method_exists(): void {
		$registrar  = new Registrar( false );
		$reflection = new \ReflectionClass( $registrar );
		$this->assertTrue( $reflection->hasMethod( 'enqueue_common_assets' ) );
	}

	/**
	 * Test get_version returns a string
	 */
	public function test_get_version_returns_string(): void {
		$registrar  = new Registrar( false );
		$reflection = new \ReflectionClass( $registrar );
		$method     = $reflection->getMethod( 'get_version' );
		$method->setAccessible( true );

		$version = $method->invoke( $registrar );
		$this->assertIsString( $version );
		$this->assertNotEmpty( $version );
	}

	/**
	 * Test get_assets_url returns a string
	 */
	public function test_get_assets_url_returns_string(): void {
		$registrar  = new Registrar( false );
		$reflection = new \ReflectionClass( $registrar );
		$method     = $reflection->getMethod( 'get_assets_url' );
		$method->setAccessible( true );

		$url = $method->invoke( $registrar );
		$this->assertIsString( $url );
	}

	/**
	 * Test that asset files exist
	 */
	public function test_asset_files_exist(): void {
		$base_dir = dirname( dirname( __DIR__ ) ) . '/src/assets/';

		$this->assertDirectoryExists( $base_dir, 'Assets directory should exist' );
		$this->assertDirectoryExists( $base_dir . 'css/', 'CSS directory should exist' );
		$this->assertDirectoryExists( $base_dir . 'js/', 'JS directory should exist' );
		$this->assertFileExists( $base_dir . 'css/wp-cmf.css', 'CSS file should exist' );
		$this->assertFileExists( $base_dir . 'js/wp-cmf.js', 'JS file should exist' );
	}

	/**
	 * Test that CSS file has content
	 */
	public function test_css_file_has_content(): void {
		$css_file = dirname( dirname( __DIR__ ) ) . '/src/assets/css/wp-cmf.css';
		$this->assertFileExists( $css_file );

		$content = file_get_contents( $css_file );
		$this->assertNotEmpty( $content, 'CSS file should have content' );
		$this->assertStringContainsString( '.wp-cmf-field', $content, 'CSS should contain field class' );
	}

	/**
	 * Test that JS file has content
	 */
	public function test_js_file_has_content(): void {
		$js_file = dirname( dirname( __DIR__ ) ) . '/src/assets/js/wp-cmf.js';
		$this->assertFileExists( $js_file );

		$content = file_get_contents( $js_file );
		$this->assertNotEmpty( $content, 'JS file should have content' );
		$this->assertStringContainsString( 'WpCmfFields', $content, 'JS should contain WpCmfFields object' );
	}

	/**
	 * Test enqueue_field_assets can be called without errors
	 */
	public function test_enqueue_field_assets_callable(): void {
		$registrar = new Registrar( false );

		// Add some fields to trigger asset enqueuing
		$registrar->add_fields(
			'test_context',
			array(
				array(
					'name'  => 'test_field',
					'type'  => 'text',
					'label' => 'Test Field',
				),
			)
		);

		// This should not throw any errors even without WordPress functions
		$this->assertNull( $registrar->enqueue_field_assets() );
	}

	/**
	 * Test that field instances can enqueue their own assets
	 */
	public function test_field_enqueue_assets_method(): void {
		$field = FieldFactory::create(
			array(
				'name'  => 'test_color',
				'type'  => 'color',
				'label' => 'Test Color',
			)
		);

		$this->assertTrue( method_exists( $field, 'enqueue_assets' ) );
		$this->assertNull( $field->enqueue_assets() );
	}
}
