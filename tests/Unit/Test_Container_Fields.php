<?php
/**
 * Container Fields Tests
 *
 * Tests for container field types (GroupField, MetaboxField, TabsField).
 *
 * @package Pedalcms\WpCmf\Tests\Unit
 */

use Pedalcms\WpCmf\Field\FieldFactory;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;

/**
 * Class Test_Container_Fields
 *
 * Tests for container field types.
 */
class Test_Container_Fields extends WP_UnitTestCase {

	/**
	 * Reset FieldFactory between tests.
	 */
	public function set_up(): void {
		parent::set_up();
		FieldFactory::reset();
	}

	/**
	 * Test GroupField creation.
	 */
	public function test_group_field_creation(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_group',
				'type'   => 'group',
				'label'  => 'Test Group',
				'fields' => array(
					array(
						'name'  => 'sub_field_1',
						'type'  => 'text',
						'label' => 'Sub Field 1',
					),
					array(
						'name'  => 'sub_field_2',
						'type'  => 'textarea',
						'label' => 'Sub Field 2',
					),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertSame( 'group', $field->get_type() );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test GroupField nested fields extraction.
	 */
	public function test_group_field_nested_fields(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_group',
				'type'   => 'group',
				'label'  => 'Test Group',
				'fields' => array(
					array(
						'name'  => 'sub_field_1',
						'type'  => 'text',
						'label' => 'Sub Field 1',
					),
					array(
						'name'  => 'sub_field_2',
						'type'  => 'textarea',
						'label' => 'Sub Field 2',
					),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 2, $nested );
	}

	/**
	 * Test MetaboxField creation.
	 */
	public function test_metabox_field_creation(): void {
		$field = FieldFactory::create(
			array(
				'name'     => 'test_metabox',
				'type'     => 'metabox',
				'label'    => 'Test Metabox',
				'context'  => 'side',
				'priority' => 'high',
				'fields'   => array(
					array(
						'name'  => 'meta_field_1',
						'type'  => 'text',
						'label' => 'Meta Field 1',
					),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertSame( 'metabox', $field->get_type() );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test MetaboxField context and priority.
	 */
	public function test_metabox_field_context_priority(): void {
		$field = FieldFactory::create(
			array(
				'name'     => 'test_metabox',
				'type'     => 'metabox',
				'label'    => 'Test Metabox',
				'context'  => 'side',
				'priority' => 'high',
				'fields'   => array(),
			)
		);

		$this->assertSame( 'side', $field->get_context() );
		$this->assertSame( 'high', $field->get_priority() );
	}

	/**
	 * Test TabsField creation.
	 */
	public function test_tabs_field_creation(): void {
		$field = FieldFactory::create(
			array(
				'name'        => 'test_tabs',
				'type'        => 'tabs',
				'label'       => 'Test Tabs',
				'orientation' => 'horizontal',
				'tabs'        => array(
					array(
						'id'     => 'tab1',
						'label'  => 'Tab 1',
						'fields' => array(
							array(
								'name'  => 'tab1_field',
								'type'  => 'text',
								'label' => 'Tab 1 Field',
							),
						),
					),
					array(
						'id'     => 'tab2',
						'label'  => 'Tab 2',
						'fields' => array(
							array(
								'name'  => 'tab2_field',
								'type'  => 'email',
								'label' => 'Tab 2 Field',
							),
						),
					),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertSame( 'tabs', $field->get_type() );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test TabsField nested fields from multiple tabs.
	 */
	public function test_tabs_field_nested_fields(): void {
		$field = FieldFactory::create(
			array(
				'name' => 'test_tabs',
				'type' => 'tabs',
				'tabs' => array(
					array(
						'id'     => 'tab1',
						'label'  => 'Tab 1',
						'fields' => array(
							array(
								'name' => 'field1',
								'type' => 'text',
							),
							array(
								'name' => 'field2',
								'type' => 'text',
							),
						),
					),
					array(
						'id'     => 'tab2',
						'label'  => 'Tab 2',
						'fields' => array(
							array(
								'name' => 'field3',
								'type' => 'text',
							),
						),
					),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 3, $nested );
	}

	/**
	 * Test GroupField renders wrapper.
	 */
	public function test_group_field_renders(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_group',
				'type'   => 'group',
				'label'  => 'Test Group',
				'fields' => array(
					array(
						'name'  => 'sub_field',
						'type'  => 'text',
						'label' => 'Sub Field',
					),
				),
			)
		);

		$html = $field->render( null );

		$this->assertStringContainsString( 'wp-cmf-group', $html );
	}

	/**
	 * Test MetaboxField renders wrapper.
	 */
	public function test_metabox_field_renders(): void {
		$field = FieldFactory::create(
			array(
				'name'   => 'test_metabox',
				'type'   => 'metabox',
				'label'  => 'Test Metabox',
				'fields' => array(
					array(
						'name'  => 'sub_field',
						'type'  => 'text',
						'label' => 'Sub Field',
					),
				),
			)
		);

		$html = $field->render( null );

		$this->assertStringContainsString( 'wp-cmf-metabox', $html );
	}
}
