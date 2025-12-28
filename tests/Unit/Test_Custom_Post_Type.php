<?php
/**
 * Custom Post Type Registration Tests
 *
 * Tests for registering custom post types with WP-CMF.
 *
 * @package Pedalcms\WpCmf\Tests\Unit
 */

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\CPT\CustomPostType;

/**
 * Class Test_Custom_Post_Type
 *
 * Tests for CPT registration.
 */
class Test_Custom_Post_Type extends WP_UnitTestCase {

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
	}

	/**
	 * Clean up registered post types.
	 */
	public function tear_down(): void {
		// Unregister test post types.
		unregister_post_type( 'test_book' );
		unregister_post_type( 'test_movie' );

		parent::tear_down();
	}

	/**
	 * Test registering a CPT via Manager.
	 *
	 * @expectedIncorrectUsage WP_Block_Type_Registry::register
	 * @expectedIncorrectUsage WP_Block_Bindings_Registry::register
	 */
	public function test_register_cpt_via_manager(): void {
		$manager = Manager::init();

		// Register a custom post type.
		$cpt = new CustomPostType( 'test_book' );
		$cpt->set_labels(
			array(
				'name'          => 'Books',
				'singular_name' => 'Book',
			)
		);
		$cpt->set_args(
			array(
				'public'       => true,
				'show_ui'      => true,
				'supports'     => array( 'title', 'editor' ),
				'show_in_rest' => true,
			)
		);

		$manager->get_new_cpt_handler()->add_post_type_instance( $cpt );

		// Manually trigger registration for the test.
		do_action( 'init' );

		// Verify the post type is registered.
		$this->assertTrue(
			post_type_exists( 'test_book' ),
			'The "test_book" post type should be registered.'
		);
	}

	/**
	 * Test CPT is public.
	 *
	 * @expectedIncorrectUsage WP_Block_Type_Registry::register
	 * @expectedIncorrectUsage WP_Block_Bindings_Registry::register
	 */
	public function test_cpt_is_public(): void {
		$manager = Manager::init();

		$cpt = new CustomPostType( 'test_book' );
		$cpt->set_args(
			array(
				'public'  => true,
				'show_ui' => true,
			)
		);

		$manager->get_new_cpt_handler()->add_post_type_instance( $cpt );

		do_action( 'init' );

		$post_type_obj = get_post_type_object( 'test_book' );

		$this->assertNotNull( $post_type_obj );
		$this->assertTrue( $post_type_obj->public );
		$this->assertTrue( $post_type_obj->show_ui );
	}

	/**
	 * Test creating a post of the CPT.
	 *
	 * @expectedIncorrectUsage WP_Block_Type_Registry::register
	 * @expectedIncorrectUsage WP_Block_Bindings_Registry::register
	 */
	public function test_can_create_cpt_post(): void {
		$manager = Manager::init();

		$cpt = new CustomPostType( 'test_book' );
		$cpt->set_args(
			array(
				'public'   => true,
				'supports' => array( 'title', 'editor' ),
			)
		);

		$manager->get_new_cpt_handler()->add_post_type_instance( $cpt );

		do_action( 'init' );

		// Create a post using the factory.
		$post_id = self::factory()->post->create(
			array(
				'post_type'  => 'test_book',
				'post_title' => 'Test Book Title',
			)
		);

		$this->assertIsInt( $post_id );
		$this->assertSame( 'test_book', get_post_type( $post_id ) );
		$this->assertSame( 'Test Book Title', get_the_title( $post_id ) );
	}

	/**
	 * Test CPT with custom supports.
	 *
	 * @expectedIncorrectUsage WP_Block_Type_Registry::register
	 * @expectedIncorrectUsage WP_Block_Bindings_Registry::register
	 */
	public function test_cpt_with_custom_supports(): void {
		$manager = Manager::init();

		$cpt = new CustomPostType( 'test_movie' );
		$cpt->set_args(
			array(
				'public'   => true,
				'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			)
		);

		$manager->get_new_cpt_handler()->add_post_type_instance( $cpt );

		do_action( 'init' );

		$this->assertTrue( post_type_supports( 'test_movie', 'title' ) );
		$this->assertTrue( post_type_supports( 'test_movie', 'editor' ) );
		$this->assertTrue( post_type_supports( 'test_movie', 'thumbnail' ) );
		$this->assertTrue( post_type_supports( 'test_movie', 'excerpt' ) );
		$this->assertFalse( post_type_supports( 'test_movie', 'comments' ) );
	}

	/**
	 * Test CPT registration from array config.
	 *
	 * @expectedIncorrectUsage WP_Block_Type_Registry::register
	 * @expectedIncorrectUsage WP_Block_Bindings_Registry::register
	 */
	public function test_register_cpt_from_array(): void {
		$manager = Manager::init();

		$manager->register_from_array(
			array(
				'cpts' => array(
					array(
						'id'   => 'test_book',
						'args' => array(
							'label'   => 'Books',
							'public'  => true,
							'show_ui' => true,
						),
					),
				),
			)
		);

		do_action( 'init' );

		$this->assertTrue(
			post_type_exists( 'test_book' ),
			'The "test_book" post type should be registered from array config.'
		);
	}
}
