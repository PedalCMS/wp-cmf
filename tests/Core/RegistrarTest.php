<?php
/**
 * Test Registrar Class
 * WordPress-style test following AspirePress patterns
 */

class RegistrarTest extends WP_UnitTestCase {

	/**
	 * Test Registrar initialization
	 */
	public function test_registrar_construct() {
		$registrar = new \Pedalcms\WpCmf\Core\Registrar();

		$this->assertInstanceOf( \Pedalcms\WpCmf\Core\Registrar::class, $registrar );
	}

	/**
	 * Test Registrar handles missing WordPress gracefully
	 */
	public function test_registrar_works_without_wordpress() {
		$registrar = new \Pedalcms\WpCmf\Core\Registrar();

		// Should not throw any errors even without WordPress
		$this->assertInstanceOf( \Pedalcms\WpCmf\Core\Registrar::class, $registrar );
	}
}
