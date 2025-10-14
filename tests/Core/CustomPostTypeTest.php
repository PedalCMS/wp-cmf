<?php
/**
 * Test CustomPostType Class
 * WordPress-style test following AspirePress patterns
 */

class CustomPostTypeTest extends WP_UnitTestCase {

	/**
	 * Test CustomPostType instantiation
	 */
	public function test_custom_post_type_construct() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'book' );

		$this->assertInstanceOf( \Pedalcms\WpCmf\CPT\CustomPostType::class, $cpt );
		$this->assertEquals( 'book', $cpt->get_post_type() );
	}

	/**
	 * Test CustomPostType configuration
	 */
	public function test_custom_post_type_configure() {
		$config = [
			'labels' => [
				'name' => 'Books',
				'singular_name' => 'Book',
			],
			'supports' => [ 'title', 'editor' ],
			'public' => true,
		];

		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'book', $config );

		$labels = $cpt->get_labels();
		$supports = $cpt->get_supports();
		$args = $cpt->get_args();

		$this->assertEquals( 'Books', $labels['name'] );
		$this->assertEquals( 'Book', $labels['singular_name'] );
		$this->assertEquals( [ 'title', 'editor' ], $supports );
		$this->assertTrue( $args['public'] );
	}

	/**
	 * Test CustomPostType fluent interface
	 */
	public function test_custom_post_type_fluent_interface() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'product' );
		
		$result = $cpt->set_label( 'name', 'Products' )
			         ->set_arg( 'public', true )
			         ->add_support( 'thumbnail' );

		$this->assertInstanceOf( \Pedalcms\WpCmf\CPT\CustomPostType::class, $result );
		$this->assertEquals( 'Products', $cpt->get_labels()['name'] );
		$this->assertTrue( $cpt->get_args()['public'] );
		$this->assertContains( 'thumbnail', $cpt->get_supports() );
	}

	/**
	 * Test CustomPostType label generation
	 */
	public function test_custom_post_type_generate_labels() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'event' );
		$cpt->generate_labels( 'Event', 'Events' );

		$labels = $cpt->get_labels();

		$this->assertEquals( 'Events', $labels['name'] );
		$this->assertEquals( 'Event', $labels['singular_name'] );
		$this->assertEquals( 'Events', $labels['menu_name'] );
		$this->assertEquals( 'Add New Event', $labels['add_new_item'] );
	}

	/**
	 * Test CustomPostType defaults
	 */
	public function test_custom_post_type_defaults() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'testimonial' );
		$cpt->set_defaults();

		$args = $cpt->get_args();
		$supports = $cpt->get_supports();

		$this->assertTrue( $args['public'] );
		$this->assertTrue( $args['show_ui'] );
		$this->assertTrue( $args['show_in_rest'] );
		$this->assertEquals( [ 'title', 'editor', 'thumbnail' ], $supports );
	}

	/**
	 * Test CustomPostType from_array factory method
	 */
	public function test_custom_post_type_from_array() {
		$config = [
			'singular' => 'Portfolio Item',
			'plural' => 'Portfolio Items',
			'public' => true,
			'supports' => [ 'title', 'editor', 'thumbnail' ],
		];

		$cpt = \Pedalcms\WpCmf\CPT\CustomPostType::from_array( 'portfolio', $config );

		$this->assertEquals( 'portfolio', $cpt->get_post_type() );
		$this->assertEquals( 'Portfolio Items', $cpt->get_labels()['name'] );
		$this->assertEquals( 'Portfolio Item', $cpt->get_labels()['singular_name'] );
		$this->assertTrue( $cpt->get_args()['public'] );
	}

	/**
	 * Test CustomPostType support management
	 */
	public function test_custom_post_type_support_management() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'project' );
		
		$cpt->add_support( 'title' )
		    ->add_support( 'editor' )
		    ->add_support( 'thumbnail' );

		$supports = $cpt->get_supports();
		$this->assertContains( 'title', $supports );
		$this->assertContains( 'editor', $supports );
		$this->assertContains( 'thumbnail', $supports );

		$cpt->remove_support( 'editor' );
		$supports = $cpt->get_supports();
		$this->assertNotContains( 'editor', $supports );
		$this->assertContains( 'title', $supports );
	}

	/**
	 * Test CustomPostType registration status
	 */
	public function test_custom_post_type_registration_status() {
		$cpt = new \Pedalcms\WpCmf\CPT\CustomPostType( 'news' );

		$this->assertFalse( $cpt->is_registered() );

		// Since register_post_type function is mocked in test environment,
		// registration will return false
		$result = $cpt->register();
		$this->assertFalse( $result );
	}
}