<?php
/**
 * Test Manager Class
 * WordPress-style test following AspirePress patterns
 */

class ManagerTest extends WP_UnitTestCase {

	/**
	 * Test Manager initialization
	 */
	public function test_manager_init_returns_instance() {
		$manager = \Pedalcms\WpCmf\Core\Manager::init();

		$this->assertInstanceOf( \Pedalcms\WpCmf\Core\Manager::class, $manager );
	}

	/**
	 * Test Manager singleton pattern
	 */
	public function test_manager_singleton() {
		$manager1 = \Pedalcms\WpCmf\Core\Manager::init();
		$manager2 = \Pedalcms\WpCmf\Core\Manager::init();

		$this->assertSame( $manager1, $manager2, 'Manager should return the same instance (singleton)' );
	}

	/**
	 * Test Manager provides access to Registrar
	 */
	public function test_manager_provides_registrar() {
		$manager   = \Pedalcms\WpCmf\Core\Manager::init();
		$registrar = $manager->get_registrar();

		$this->assertInstanceOf( \Pedalcms\WpCmf\Core\Registrar::class, $registrar );
	}
}
