<?php
/**
 * Tests for RepeaterField class
 *
 * @package Pedalcms\WpCmf\Tests\Field
 */

namespace Pedalcms\WpCmf\Tests\Field;

use PHPUnit\Framework\TestCase;
use Pedalcms\WpCmf\Field\Fields\RepeaterField;
use Pedalcms\WpCmf\Field\FieldInterface;
use Pedalcms\WpCmf\Field\FieldFactory;

/**
 * Test class for RepeaterField
 */
class RepeaterFieldTest extends TestCase {

	/**
	 * Reset FieldFactory between tests
	 */
	protected function setUp(): void {
		parent::setUp();
		FieldFactory::reset();
	}

	/**
	 * Helper method to create a RepeaterField
	 *
	 * @param string               $name   Field name.
	 * @param array<string, mixed> $config Field configuration.
	 * @return RepeaterField
	 */
	protected function create_field( string $name, array $config = [] ): RepeaterField {
		return new RepeaterField( $name, 'repeater', $config );
	}

	/**
	 * Test that RepeaterField implements FieldInterface
	 */
	public function test_implements_field_interface(): void {
		$field = $this->create_field( 'test_repeater' );
		$this->assertInstanceOf( FieldInterface::class, $field );
	}

	/**
	 * Test field type is 'repeater'
	 */
	public function test_get_type_returns_repeater(): void {
		$field = $this->create_field( 'test_repeater' );
		$this->assertSame( 'repeater', $field->get_type() );
	}

	/**
	 * Test default configuration
	 */
	public function test_default_configuration(): void {
		$field = $this->create_field( 'test_repeater' );

		$this->assertEmpty( $field->get_config( 'fields', [] ) );
		$this->assertSame( 0, $field->get_config( 'min_rows', 0 ) );
		$this->assertSame( 0, $field->get_config( 'max_rows', 0 ) );
		$this->assertSame( 'Add Row', $field->get_config( 'button_label', 'Add Row' ) );
		$this->assertSame( 'Row {{index}}', $field->get_config( 'row_label', 'Row {{index}}' ) );
		$this->assertTrue( $field->get_config( 'collapsible', true ) );
		$this->assertFalse( $field->get_config( 'collapsed', false ) );
		$this->assertTrue( $field->get_config( 'sortable', true ) );
	}

	/**
	 * Test custom configuration
	 */
	public function test_custom_configuration(): void {
		$field = $this->create_field(
			'team_members',
			[
				'label'        => 'Team Members',
				'min_rows'     => 1,
				'max_rows'     => 10,
				'button_label' => 'Add Member',
				'row_label'    => 'Member {{index}}',
				'collapsible'  => false,
				'sortable'     => false,
				'fields'       => [
					[
						'name' => 'name',
						'type' => 'text',
					],
				],
			]
		);

		$this->assertSame( 'Team Members', $field->get_config( 'label', '' ) );
		$this->assertSame( 1, $field->get_config( 'min_rows', 0 ) );
		$this->assertSame( 10, $field->get_config( 'max_rows', 0 ) );
		$this->assertSame( 'Add Member', $field->get_config( 'button_label', '' ) );
		$this->assertSame( 'Member {{index}}', $field->get_config( 'row_label', '' ) );
		$this->assertFalse( $field->get_config( 'collapsible', true ) );
		$this->assertFalse( $field->get_config( 'sortable', true ) );
		$this->assertCount( 1, $field->get_config( 'fields', [] ) );
	}

	/**
	 * Test get_sub_fields returns fields configuration
	 */
	public function test_get_sub_fields(): void {
		$sub_fields = [
			[
				'name'  => 'name',
				'type'  => 'text',
				'label' => 'Name',
			],
			[
				'name'  => 'email',
				'type'  => 'email',
				'label' => 'Email',
			],
		];

		$field = $this->create_field( 'team', [ 'fields' => $sub_fields ] );

		$this->assertSame( $sub_fields, $field->get_sub_fields() );
	}

	/**
	 * Test render outputs repeater container
	 */
	public function test_render_creates_repeater_container(): void {
		$field = $this->create_field(
			'team_members',
			[
				'label'  => 'Team Members',
				'fields' => [
					[
						'name'  => 'name',
						'type'  => 'text',
						'label' => 'Name',
					],
				],
			]
		);

		$output = $field->render( [] );

		// Check for main container
		$this->assertStringContainsString( 'class="wp-cmf-repeater"', $output );
		$this->assertStringContainsString( 'data-field-name="team_members"', $output );

		// Check for rows container
		$this->assertStringContainsString( 'class="wp-cmf-repeater-rows"', $output );

		// Check for add button
		$this->assertStringContainsString( 'class="button wp-cmf-repeater-add"', $output );
		$this->assertStringContainsString( 'Add Row', $output );

		// Check for template script
		$this->assertStringContainsString( 'class="wp-cmf-repeater-template"', $output );
	}

	/**
	 * Test render with custom button label
	 */
	public function test_render_uses_custom_button_label(): void {
		$field = $this->create_field(
			'faqs',
			[
				'button_label' => 'Add FAQ Item',
				'fields'       => [
					[
						'name' => 'question',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render( [] );

		$this->assertStringContainsString( 'Add FAQ Item', $output );
	}

	/**
	 * Test render with existing rows
	 */
	public function test_render_with_existing_rows(): void {
		$field = $this->create_field(
			'team',
			[
				'fields' => [
					[
						'name'  => 'name',
						'type'  => 'text',
						'label' => 'Name',
					],
				],
			]
		);

		$value = [
			[ 'name' => 'John Doe' ],
			[ 'name' => 'Jane Smith' ],
		];

		$output = $field->render( $value );

		// Should have row containers
		$this->assertStringContainsString( 'class="wp-cmf-repeater-row"', $output );

		// Should contain field values
		$this->assertStringContainsString( 'John Doe', $output );
		$this->assertStringContainsString( 'Jane Smith', $output );
	}

	/**
	 * Test render with min_rows adds empty rows
	 */
	public function test_render_adds_minimum_rows(): void {
		$field = $this->create_field(
			'items',
			[
				'min_rows' => 3,
				'fields'   => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render( [] );

		// Should have 3 rows in the rows container (not counting template)
		// The template has data-row-index="{{INDEX}}" while real rows have numeric indexes
		$this->assertSame( 3, substr_count( $output, 'data-row-index="0"' ) + substr_count( $output, 'data-row-index="1"' ) + substr_count( $output, 'data-row-index="2"' ) );
	}

	/**
	 * Test render includes data attributes
	 */
	public function test_render_includes_data_attributes(): void {
		$field = $this->create_field(
			'test',
			[
				'min_rows'    => 2,
				'max_rows'    => 5,
				'sortable'    => true,
				'collapsible' => true,
				'fields'      => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render( [] );

		$this->assertStringContainsString( 'data-min-rows="2"', $output );
		$this->assertStringContainsString( 'data-max-rows="5"', $output );
		$this->assertStringContainsString( 'data-sortable="true"', $output );
		$this->assertStringContainsString( 'data-collapsible="true"', $output );
	}

	/**
	 * Test sanitize returns empty array for non-array input
	 */
	public function test_sanitize_non_array_returns_empty(): void {
		$field  = $this->create_field( 'test' );
		$result = $field->sanitize( 'not an array' );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	/**
	 * Test sanitize sanitizes each field in each row
	 */
	public function test_sanitize_processes_all_rows(): void {
		$field = $this->create_field(
			'team',
			[
				'fields' => [
					[
						'name' => 'name',
						'type' => 'text',
					],
					[
						'name' => 'email',
						'type' => 'email',
					],
				],
			]
		);

		$input = [
			[
				'name'  => '  John Doe  ',
				'email' => 'john@example.com',
			],
			[
				'name'  => 'Jane Smith',
				'email' => 'invalid-email',
			],
		];

		$result = $field->sanitize( $input );

		$this->assertCount( 2, $result );
		$this->assertSame( 'John Doe', $result[0]['name'] );
		$this->assertSame( 'john@example.com', $result[0]['email'] );
		$this->assertSame( 'Jane Smith', $result[1]['name'] );
	}

	/**
	 * Test sanitize skips invalid row data
	 */
	public function test_sanitize_skips_invalid_rows(): void {
		$field = $this->create_field(
			'test',
			[
				'fields' => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$input = [
			[ 'item' => 'Valid' ],
			'not an array',
			[ 'item' => 'Also Valid' ],
		];

		$result = $field->sanitize( $input );

		$this->assertCount( 2, $result );
		$this->assertSame( 'Valid', $result[0]['item'] );
		$this->assertSame( 'Also Valid', $result[1]['item'] );
	}

	/**
	 * Test validate passes with valid data
	 */
	public function test_validate_passes_with_valid_data(): void {
		$field = $this->create_field(
			'test',
			[
				'fields' => [
					[
						'name'     => 'title',
						'type'     => 'text',
						'required' => true,
					],
				],
			]
		);

		$input = [
			[ 'title' => 'First Item' ],
			[ 'title' => 'Second Item' ],
		];

		$result = $field->validate( $input );

		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	/**
	 * Test validate enforces min_rows
	 */
	public function test_validate_enforces_min_rows(): void {
		$field = $this->create_field(
			'test',
			[
				'min_rows' => 3,
				'fields'   => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$input = [
			[ 'item' => 'Only one' ],
		];

		$result = $field->validate( $input );

		$this->assertFalse( $result['valid'] );
		$this->assertStringContainsString( '3 row(s) required', $result['errors'][0] );
	}

	/**
	 * Test validate enforces max_rows
	 */
	public function test_validate_enforces_max_rows(): void {
		$field = $this->create_field(
			'test',
			[
				'max_rows' => 2,
				'fields'   => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$input = [
			[ 'item' => 'One' ],
			[ 'item' => 'Two' ],
			[ 'item' => 'Three' ],
		];

		$result = $field->validate( $input );

		$this->assertFalse( $result['valid'] );
		$this->assertStringContainsString( '2 row(s) allowed', $result['errors'][0] );
	}

	/**
	 * Test validate validates nested fields
	 */
	public function test_validate_validates_nested_fields(): void {
		$field = $this->create_field(
			'test',
			[
				'fields' => [
					[
						'name'     => 'email',
						'type'     => 'email',
						'required' => true,
					],
				],
			]
		);

		$input = [
			[ 'email' => 'valid@example.com' ],
			[ 'email' => '' ], // Missing required
		];

		$result = $field->validate( $input );

		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
	}

	/**
	 * Test get_schema returns correct schema
	 */
	public function test_get_schema(): void {
		$field = $this->create_field(
			'team',
			[
				'label'       => 'Team Members',
				'description' => 'Add your team',
				'fields'      => [
					[
						'name' => 'name',
						'type' => 'text',
					],
				],
			]
		);

		$schema = $field->get_schema();

		$this->assertSame( 'team', $schema['name'] );
		$this->assertSame( 'repeater', $schema['type'] );
		$this->assertSame( 'Team Members', $schema['label'] );
		$this->assertSame( 'Add your team', $schema['description'] );
		$this->assertArrayHasKey( 'fields', $schema );
	}

	/**
	 * Test field can be created via FieldFactory
	 */
	public function test_field_factory_creates_repeater(): void {
		$field = FieldFactory::create(
			[
				'name'   => 'test_repeater',
				'type'   => 'repeater',
				'label'  => 'Test Repeater',
				'fields' => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$this->assertInstanceOf( RepeaterField::class, $field );
		$this->assertSame( 'test_repeater', $field->get_name() );
		$this->assertSame( 'repeater', $field->get_type() );
	}

	/**
	 * Test render includes row controls (collapse, remove, drag)
	 */
	public function test_render_includes_row_controls(): void {
		$field = $this->create_field(
			'test',
			[
				'collapsible' => true,
				'sortable'    => true,
				'fields'      => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render( [ [ 'item' => 'Test' ] ] );

		// Check for row header with controls
		$this->assertStringContainsString( 'wp-cmf-repeater-row-header', $output );
		$this->assertStringContainsString( 'wp-cmf-repeater-toggle', $output );
		$this->assertStringContainsString( 'wp-cmf-repeater-remove', $output );
		$this->assertStringContainsString( 'wp-cmf-repeater-drag-handle', $output );
	}

	/**
	 * Test render with max_rows disables add button when limit reached
	 */
	public function test_add_button_disabled_at_max_rows(): void {
		$field = $this->create_field(
			'test',
			[
				'max_rows' => 2,
				'fields'   => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render(
			[
				[ 'item' => 'One' ],
				[ 'item' => 'Two' ],
			]
		);

		$this->assertStringContainsString( 'disabled', $output );
	}

	/**
	 * Test complex nested fields
	 */
	public function test_complex_nested_fields(): void {
		$field = $this->create_field(
			'products',
			[
				'fields' => [
					[
						'name'     => 'name',
						'type'     => 'text',
						'required' => true,
					],
					[
						'name'    => 'status',
						'type'    => 'select',
						'options' => [
							'active'   => 'Active',
							'inactive' => 'Inactive',
						],
					],
					[
						'name' => 'price',
						'type' => 'number',
						'min'  => 0,
					],
					[
						'name' => 'description',
						'type' => 'textarea',
					],
				],
			]
		);

		$input = [
			[
				'name'        => 'Product 1',
				'status'      => 'active',
				'price'       => '29.99',
				'description' => 'A great product',
			],
		];

		// Test sanitize
		$sanitized = $field->sanitize( $input );
		$this->assertSame( 'Product 1', $sanitized[0]['name'] );
		$this->assertSame( 'active', $sanitized[0]['status'] );
		$this->assertEquals( 29.99, $sanitized[0]['price'] );

		// Test validate
		$validation = $field->validate( $sanitized );
		$this->assertTrue( $validation['valid'] );
	}

	/**
	 * Test row label template replacement
	 */
	public function test_row_label_uses_index_placeholder(): void {
		$field = $this->create_field(
			'test',
			[
				'row_label' => 'Item #{{index}}',
				'fields'    => [
					[
						'name' => 'item',
						'type' => 'text',
					],
				],
			]
		);

		$output = $field->render(
			[
				[ 'item' => 'First' ],
				[ 'item' => 'Second' ],
			]
		);

		$this->assertStringContainsString( 'Item #1', $output );
		$this->assertStringContainsString( 'Item #2', $output );
	}
}
