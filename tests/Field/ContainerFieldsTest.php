<?php
/**
 * Container Fields Tests
 *
 * Tests for container field types (GroupField, MetaboxField, TabsField).
 *
 * @package Pedalcms\WpCmf\Tests\Field
 */

namespace Pedalcms\WpCmf\Tests\Field;

use Pedalcms\WpCmf\Tests\WpCmfTestCase;
use Pedalcms\WpCmf\Field\ContainerFieldInterface;
use Pedalcms\WpCmf\Field\fields\GroupField;
use Pedalcms\WpCmf\Field\fields\MetaboxField;
use Pedalcms\WpCmf\Field\fields\TabsField;

/**
 * Class ContainerFieldsTest
 */
class ContainerFieldsTest extends WpCmfTestCase {

	/**
	 * Test GroupField implements ContainerFieldInterface
	 */
	public function test_group_field_is_container(): void {
		$field = new GroupField(
			'test_group',
			array(
				'label'  => 'Test Group',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test MetaboxField implements ContainerFieldInterface
	 */
	public function test_metabox_field_is_container(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'  => 'Test Metabox',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test TabsField implements ContainerFieldInterface
	 */
	public function test_tabs_field_is_container(): void {
		$field = new TabsField(
			'test_tabs',
			array(
				'label' => 'Test Tabs',
				'tabs'  => array(
					array(
						'id'     => 'tab1',
						'title'  => 'Tab 1',
						'fields' => array(
							array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
						),
					),
				),
			)
		);

		$this->assertInstanceOf( ContainerFieldInterface::class, $field );
		$this->assertTrue( $field->is_container() );
	}

	/**
	 * Test GroupField get_nested_fields
	 */
	public function test_group_field_get_nested_fields(): void {
		$field = new GroupField(
			'test_group',
			array(
				'label'  => 'Test Group',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
					array( 'name' => 'field2', 'type' => 'textarea', 'label' => 'Field 2' ),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 2, $nested );
		$this->assertSame( 'field1', $nested[0]['name'] );
		$this->assertSame( 'field2', $nested[1]['name'] );
	}

	/**
	 * Test MetaboxField get_nested_fields
	 */
	public function test_metabox_field_get_nested_fields(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'  => 'Test Metabox',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
					array( 'name' => 'field2', 'type' => 'number', 'label' => 'Field 2' ),
					array( 'name' => 'field3', 'type' => 'checkbox', 'label' => 'Field 3' ),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 3, $nested );
	}

	/**
	 * Test TabsField get_nested_fields
	 */
	public function test_tabs_field_get_nested_fields(): void {
		$field = new TabsField(
			'test_tabs',
			array(
				'label' => 'Test Tabs',
				'tabs'  => array(
					array(
						'id'     => 'tab1',
						'title'  => 'Tab 1',
						'fields' => array(
							array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
							array( 'name' => 'field2', 'type' => 'text', 'label' => 'Field 2' ),
						),
					),
					array(
						'id'     => 'tab2',
						'title'  => 'Tab 2',
						'fields' => array(
							array( 'name' => 'field3', 'type' => 'text', 'label' => 'Field 3' ),
						),
					),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 3, $nested );
	}

	/**
	 * Test GroupField render
	 */
	public function test_group_field_render(): void {
		$field = new GroupField(
			'test_group',
			array(
				'label'  => 'Test Group',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		// Render with context (settings page ID).
		$html = $field->render( 'my-settings' );

		$this->assertStringContainsString( 'wp-cmf-group', $html );
		$this->assertStringContainsString( 'Test Group', $html );
	}

	/**
	 * Test MetaboxField render
	 */
	public function test_metabox_field_render(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'  => 'Test Metabox',
				'fields' => array(
					array( 'name' => 'field1', 'type' => 'text', 'label' => 'Field 1' ),
				),
			)
		);

		// Render with post ID context.
		$html = $field->render( 123 );

		$this->assertStringContainsString( 'wp-cmf-metabox', $html );
	}

	/**
	 * Test MetaboxField get_context
	 */
	public function test_metabox_field_get_context(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'   => 'Test Metabox',
				'context' => 'side',
				'fields'  => array(),
			)
		);

		$this->assertSame( 'side', $field->get_context() );
	}

	/**
	 * Test MetaboxField get_priority
	 */
	public function test_metabox_field_get_priority(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'    => 'Test Metabox',
				'priority' => 'high',
				'fields'   => array(),
			)
		);

		$this->assertSame( 'high', $field->get_priority() );
	}

	/**
	 * Test TabsField with horizontal orientation
	 */
	public function test_tabs_field_horizontal(): void {
		$field = new TabsField(
			'test_tabs',
			array(
				'label'       => 'Test Tabs',
				'orientation' => 'horizontal',
				'tabs'        => array(
					array(
						'id'     => 'tab1',
						'title'  => 'Tab 1',
						'fields' => array(),
					),
				),
			)
		);

		$html = $field->render( 'my-settings' );

		$this->assertStringContainsString( 'horizontal', $html );
	}

	/**
	 * Test TabsField with vertical orientation
	 */
	public function test_tabs_field_vertical(): void {
		$field = new TabsField(
			'test_tabs',
			array(
				'label'       => 'Test Tabs',
				'orientation' => 'vertical',
				'tabs'        => array(
					array(
						'id'     => 'tab1',
						'title'  => 'Tab 1',
						'fields' => array(),
					),
				),
			)
		);

		$html = $field->render( 'my-settings' );

		$this->assertStringContainsString( 'vertical', $html );
	}

	/**
	 * Test nested containers
	 */
	public function test_nested_containers(): void {
		$field = new GroupField(
			'outer_group',
			array(
				'label'  => 'Outer Group',
				'fields' => array(
					array(
						'name'   => 'inner_group',
						'type'   => 'group',
						'label'  => 'Inner Group',
						'fields' => array(
							array( 'name' => 'nested_field', 'type' => 'text', 'label' => 'Nested' ),
						),
					),
				),
			)
		);

		$nested = $field->get_nested_fields();

		$this->assertCount( 1, $nested );
		$this->assertSame( 'inner_group', $nested[0]['name'] );
		$this->assertSame( 'group', $nested[0]['type'] );
	}

	/**
	 * Test GroupField with description
	 */
	public function test_group_field_with_description(): void {
		$field = new GroupField(
			'test_group',
			array(
				'label'       => 'Test Group',
				'description' => 'This is a description',
				'fields'      => array(),
			)
		);

		$html = $field->render( 'my-settings' );

		$this->assertStringContainsString( 'This is a description', $html );
	}

	/**
	 * Test MetaboxField default context and priority
	 */
	public function test_metabox_field_defaults(): void {
		$field = new MetaboxField(
			'test_metabox',
			array(
				'label'  => 'Test Metabox',
				'fields' => array(),
			)
		);

		$this->assertSame( 'normal', $field->get_context() );
		$this->assertSame( 'default', $field->get_priority() );
	}
}
