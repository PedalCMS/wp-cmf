<?php
/**
 * WP-CMF Test Bootstrap
 * WordPress-style test bootstrap following AspirePress patterns
 */

// Set test mode flag
define( 'WP_CMF_TESTING', true );

// Load Composer autoloader
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Mock WP_UnitTestCase if not available
if ( ! class_exists( 'WP_UnitTestCase' ) ) {
	class WP_UnitTestCase extends \PHPUnit\Framework\TestCase {
		public function setUp(): void {
			parent::setUp();
		}

		public function tearDown(): void {
			parent::tearDown();
		}
	}
}

// Mock WordPress functions if needed
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		// Mock implementation
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		// Mock implementation
		return true;
	}
}

// Initialize WP-CMF for testing
if ( class_exists( 'Pedalcms\WpCmf\Core\Manager' ) ) {
	\Pedalcms\WpCmf\Core\Manager::init();
}

echo "WP-CMF test environment initialized\n";
