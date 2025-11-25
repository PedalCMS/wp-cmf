<?php
/**
 * Example 15: Multiple Metaboxes for Custom Post Type (Array Configuration)
 *
 * Demonstrates how to create multiple meta boxes per post type using the new MetaboxField.
 * Each metabox is a container field that groups related fields together.
 *
 * The metabox field:
 * - Creates separate WordPress meta boxes with individual titles and positions
 * - Checks if metabox exists, creates it if not, adds fields to existing ones
 * - Works like the tabs field - it's a container with nested fields
 * - Doesn't store values itself - nested fields handle their own data
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

// Initialize WP-CMF Manager
$manager = Manager::init();

// ============================================================================
// Example: Product CPT with Multiple Metaboxes
// ============================================================================

$manager->register_from_array(
	[
		'cpts' => [
			[
				'id'   => 'product',
				'args' => [
					'label'       => 'Products',
					'public'      => true,
					'has_archive' => true,
					'menu_icon'   => 'dashicons-products',
					'supports'    => [ 'title', 'editor', 'thumbnail' ],
				],

				// Define multiple metaboxes with their own fields
				'fields' => [
					// Metabox 1: Basic Product Information (Normal position, High priority)
					[
						'type'          => 'metabox',
						'name'          => 'basic_info_metabox',
						'metabox_id'    => 'product_basic_info',
						'metabox_title' => 'Basic Product Information',
						'context'       => 'normal',   // 'normal', 'side', 'advanced'
						'priority'      => 'high',      // 'high', 'core', 'default', 'low'
						'fields'        => [
							[
								'name'        => 'product_sku',
								'type'        => 'text',
								'label'       => 'SKU',
								'placeholder' => 'PROD-001',
								'required'    => true,
								'description' => 'Unique product identifier',
							],
							[
								'name'        => 'product_price',
								'type'        => 'number',
								'label'       => 'Price ($)',
								'placeholder' => '99.99',
								'validation'  => [
									'min'  => 0,
									'step' => 0.01,
								],
								'description' => 'Product price in USD',
							],
							[
								'name'    => 'product_stock',
								'type'    => 'select',
								'label'   => 'Stock Status',
								'options' => [
									'in_stock'     => 'In Stock',
									'out_of_stock' => 'Out of Stock',
									'on_backorder' => 'On Backorder',
								],
								'default'     => 'in_stock',
								'description' => 'Current stock availability',
							],
							[
								'name'        => 'product_brand',
								'type'        => 'text',
								'label'       => 'Brand',
								'placeholder' => 'Brand name',
								'description' => 'Product brand or manufacturer',
							],
						],
					],

					// Metabox 2: Product Specifications (Normal position, Default priority)
					[
						'type'          => 'metabox',
						'name'          => 'specifications_metabox',
						'metabox_id'    => 'product_specifications',
						'metabox_title' => 'Product Specifications',
						'context'       => 'normal',
						'priority'      => 'default',
						'fields'        => [
							[
								'name'        => 'product_weight',
								'type'        => 'number',
								'label'       => 'Weight (kg)',
								'placeholder' => '1.5',
								'validation'  => [
									'min'  => 0,
									'step' => 0.1,
								],
								'description' => 'Product weight in kilograms',
							],
							[
								'name'        => 'product_dimensions',
								'type'        => 'text',
								'label'       => 'Dimensions',
								'placeholder' => '10 x 5 x 3 cm',
								'description' => 'Product dimensions (L x W x H)',
							],
							[
								'name'        => 'product_color',
								'type'        => 'color',
								'label'       => 'Primary Color',
								'default'     => '#000000',
								'description' => 'Primary product color',
							],
							[
								'name'        => 'product_material',
								'type'        => 'text',
								'label'       => 'Material',
								'placeholder' => 'Cotton, Polyester, etc.',
								'description' => 'Product material composition',
							],
						],
					],

					// Metabox 3: Shipping & Delivery (Side position)
					[
						'type'          => 'metabox',
						'name'          => 'shipping_metabox',
						'metabox_id'    => 'product_shipping',
						'metabox_title' => 'Shipping & Delivery',
						'context'       => 'side',     // Appears in sidebar
						'priority'      => 'default',
						'fields'        => [
							[
								'name'    => 'product_free_shipping',
								'type'    => 'checkbox',
								'label'   => 'Free Shipping',
								'options' => [
									'yes' => 'Enable free shipping',
								],
								'description' => 'Check to offer free shipping',
							],
							[
								'name'        => 'product_shipping_class',
								'type'        => 'select',
								'label'       => 'Shipping Class',
								'options'     => [
									'standard' => 'Standard',
									'express'  => 'Express',
									'heavy'    => 'Heavy Item',
								],
								'placeholder' => 'Select class',
								'description' => 'Shipping method classification',
							],
							[
								'name'        => 'product_delivery_days',
								'type'        => 'number',
								'label'       => 'Delivery Time (days)',
								'placeholder' => '5',
								'validation'  => [
									'min' => 1,
									'max' => 90,
								],
								'default'     => 5,
								'description' => 'Estimated delivery time',
							],
						],
					],

					// Metabox 4: Product Features (Normal position, Low priority)
					[
						'type'          => 'metabox',
						'name'          => 'features_metabox',
						'metabox_id'    => 'product_features',
						'metabox_title' => 'Product Features',
						'context'       => 'normal',
						'priority'      => 'low',
						'fields'        => [
							[
								'name'        => 'product_features',
								'type'        => 'textarea',
								'label'       => 'Key Features',
								'rows'        => 5,
								'placeholder' => 'List key features, one per line...',
								'description' => 'List the main features of this product',
							],
							[
								'name'    => 'product_warranty',
								'type'    => 'checkbox',
								'label'   => 'Warranty',
								'options' => [
									'1_year'  => '1 Year Warranty',
									'2_years' => '2 Years Warranty',
									'3_years' => '3 Years Warranty',
									'lifetime' => 'Lifetime Warranty',
								],
								'description' => 'Select applicable warranties',
							],
							[
								'name'    => 'product_eco_friendly',
								'type'    => 'radio',
								'label'   => 'Eco-Friendly',
								'options' => [
									'yes'     => 'Yes',
									'no'      => 'No',
									'partial' => 'Partially',
								],
								'default'     => 'no',
								'description' => 'Is this product eco-friendly?',
							],
						],
					],

					// Metabox 5: SEO & Marketing (Advanced position)
					[
						'type'          => 'metabox',
						'name'          => 'seo_metabox',
						'metabox_id'    => 'product_seo',
						'metabox_title' => 'SEO & Marketing',
						'context'       => 'advanced',
						'priority'      => 'default',
						'fields'        => [
							[
								'name'        => 'product_meta_title',
								'type'        => 'text',
								'label'       => 'Meta Title',
								'placeholder' => 'SEO title for this product',
								'validation'  => [
									'max_length' => 60,
								],
								'description' => 'SEO meta title (max 60 characters)',
							],
							[
								'name'        => 'product_meta_description',
								'type'        => 'textarea',
								'label'       => 'Meta Description',
								'rows'        => 3,
								'placeholder' => 'Brief description for search engines...',
								'validation'  => [
									'max_length' => 160,
								],
								'description' => 'SEO meta description (max 160 characters)',
							],
							[
								'name'    => 'product_featured',
								'type'    => 'checkbox',
								'label'   => 'Featured Product',
								'options' => [
									'yes' => 'Mark as featured on homepage',
								],
								'description' => 'Feature this product in promotions',
							],
						],
					],
				],
			],
		],
	]
);
