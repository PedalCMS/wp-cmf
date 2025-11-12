<?php
/**
 * Example 11: Custom Post Type with Submenu Settings Page
 *
 * This example demonstrates how to create a custom post type with its own
 * settings page as a submenu. This is useful for CPT-specific configurations
 * like default values, display options, or integration settings.
 *
 * Use Case: A "Product" CPT with a submenu settings page for configuring
 * product catalog defaults, tax rates, currency, and display options.
 *
 * @package    WP-CMF
 * @subpackage Examples
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize and register CPT with submenu settings page
 *
 * This example demonstrates creating a Product CPT with a settings
 * submenu for CPT-specific configurations like pricing, inventory, and display.
 */
function cpt_with_submenu_settings_init() {
	// Configuration array with CPT and related settings page
	$config = [
	'cpts'           => [
		[
			'id'   => 'product',
			'args' => [
				'label'               => 'Products',
				'labels'              => [
					'name'               => 'Products',
					'singular_name'      => 'Product',
					'menu_name'          => 'Products',
					'add_new'            => 'Add New Product',
					'add_new_item'       => 'Add New Product',
					'edit_item'          => 'Edit Product',
					'new_item'           => 'New Product',
					'view_item'          => 'View Product',
					'search_items'       => 'Search Products',
					'not_found'          => 'No products found',
					'not_found_in_trash' => 'No products found in trash',
					'all_items'          => 'All Products',
				],
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_icon'           => 'dashicons-cart',
				'menu_position'       => 20,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'has_archive'         => true,
				'rewrite'             => [ 'slug' => 'products' ],
				'show_in_rest'        => true,
			],
			'fields' => [
				// Basic Product Information
				[
					'name'        => 'sku',
					'type'        => 'text',
					'label'       => 'SKU',
					'description' => 'Product Stock Keeping Unit',
					'required'    => true,
					'placeholder' => 'e.g., PROD-001',
					'context'     => 'normal',
					'priority'    => 'high',
				],
				[
					'name'        => 'price',
					'type'        => 'number',
					'label'       => 'Price',
					'description' => 'Product price (will use currency from settings)',
					'required'    => true,
					'min'         => 0,
					'step'        => 0.01,
					'placeholder' => '0.00',
					'context'     => 'normal',
					'priority'    => 'high',
				],
				[
					'name'        => 'sale_price',
					'type'        => 'number',
					'label'       => 'Sale Price',
					'description' => 'Optional sale/discount price',
					'min'         => 0,
					'step'        => 0.01,
					'placeholder' => '0.00',
					'context'     => 'normal',
					'priority'    => 'high',
				],

				// Inventory Management
				[
					'name'        => 'stock_quantity',
					'type'        => 'number',
					'label'       => 'Stock Quantity',
					'description' => 'Current inventory count',
					'default'     => 0,
					'min'         => 0,
					'context'     => 'side',
					'priority'    => 'high',
				],
				[
					'name'        => 'stock_status',
					'type'        => 'select',
					'label'       => 'Stock Status',
					'description' => 'Product availability status',
					'options'     => [
						'in_stock'    => 'In Stock',
						'out_of_stock' => 'Out of Stock',
						'backorder'   => 'On Backorder',
						'preorder'    => 'Pre-Order',
					],
					'default'     => 'in_stock',
					'context'     => 'side',
					'priority'    => 'high',
				],
				[
					'name'        => 'track_inventory',
					'type'        => 'checkbox',
					'label'       => 'Track Inventory',
					'description' => 'Enable inventory tracking for this product',
					'default'     => true,
					'context'     => 'side',
					'priority'    => 'default',
				],

				// Product Specifications
				[
					'name'        => 'weight',
					'type'        => 'number',
					'label'       => 'Weight',
					'description' => 'Product weight (use unit from settings)',
					'min'         => 0,
					'step'        => 0.01,
					'context'     => 'normal',
					'priority'    => 'default',
				],
				[
					'name'        => 'dimensions',
					'type'        => 'text',
					'label'       => 'Dimensions (L × W × H)',
					'description' => 'Product dimensions (use unit from settings)',
					'placeholder' => 'e.g., 10 × 5 × 3',
					'context'     => 'normal',
					'priority'    => 'default',
				],
				[
					'name'        => 'color',
					'type'        => 'select',
					'label'       => 'Color',
					'description' => 'Product color variant',
					'options'     => [
						''        => 'Select Color',
						'red'     => 'Red',
						'blue'    => 'Blue',
						'green'   => 'Green',
						'black'   => 'Black',
						'white'   => 'White',
						'yellow'  => 'Yellow',
						'orange'  => 'Orange',
						'purple'  => 'Purple',
					],
					'context'     => 'normal',
					'priority'    => 'default',
				],

				// Product Features
				[
					'name'        => 'featured',
					'type'        => 'checkbox',
					'label'       => 'Featured Product',
					'description' => 'Show this product in featured sections',
					'default'     => false,
					'context'     => 'side',
					'priority'    => 'default',
				],
				[
					'name'        => 'on_sale',
					'type'        => 'checkbox',
					'label'       => 'On Sale',
					'description' => 'Mark product as on sale',
					'default'     => false,
					'context'     => 'side',
					'priority'    => 'default',
				],
				[
					'name'        => 'sale_badge',
					'type'        => 'text',
					'label'       => 'Sale Badge Text',
					'description' => 'Custom text for sale badge (e.g., "50% OFF")',
					'placeholder' => 'e.g., SALE, 50% OFF',
					'context'     => 'side',
					'priority'    => 'default',
				],
			],
		],
	],
	'settings_pages' => [
		[
			'id'           => 'product-settings',
			'page_title'   => 'Product Settings',
			'menu_title'   => 'Settings',
			'capability'   => 'manage_options',
			// This creates a submenu under the "Products" CPT menu
			'parent_slug'  => 'edit.php?post_type=product',
			'menu_slug'    => 'product-settings',
			'fields'       => [
				// Currency & Pricing
				[
					'name'        => 'currency',
					'type'        => 'select',
					'label'       => 'Currency',
					'description' => 'Default currency for product prices',
					'options'     => [
						'USD' => 'US Dollar ($)',
						'EUR' => 'Euro (€)',
						'GBP' => 'British Pound (£)',
						'JPY' => 'Japanese Yen (¥)',
						'AUD' => 'Australian Dollar (A$)',
						'CAD' => 'Canadian Dollar (C$)',
						'INR' => 'Indian Rupee (₹)',
					],
					'default'     => 'USD',
					'required'    => true,
				],
				[
					'name'        => 'currency_position',
					'type'        => 'radio',
					'label'       => 'Currency Position',
					'description' => 'Where to display the currency symbol',
					'options'     => [
						'before' => 'Before amount ($99.00)',
						'after'  => 'After amount (99.00$)',
					],
					'default'     => 'before',
					'layout'      => 'inline',
				],
				[
					'name'        => 'decimal_separator',
					'type'        => 'text',
					'label'       => 'Decimal Separator',
					'description' => 'Character to separate decimals',
					'default'     => '.',
					'maxlength'   => 1,
				],
				[
					'name'        => 'thousand_separator',
					'type'        => 'text',
					'label'       => 'Thousand Separator',
					'description' => 'Character to separate thousands',
					'default'     => ',',
					'maxlength'   => 1,
				],
				[
					'name'        => 'tax_rate',
					'type'        => 'number',
					'label'       => 'Tax Rate (%)',
					'description' => 'Default tax rate percentage',
					'default'     => 0,
					'min'         => 0,
					'max'         => 100,
					'step'        => 0.01,
				],

				// Measurements
				[
					'name'        => 'weight_unit',
					'type'        => 'select',
					'label'       => 'Weight Unit',
					'description' => 'Unit for product weight',
					'options'     => [
						'kg' => 'Kilograms (kg)',
						'g'  => 'Grams (g)',
						'lb' => 'Pounds (lb)',
						'oz' => 'Ounces (oz)',
					],
					'default'     => 'kg',
				],
				[
					'name'        => 'dimension_unit',
					'type'        => 'select',
					'label'       => 'Dimension Unit',
					'description' => 'Unit for product dimensions',
					'options'     => [
						'cm' => 'Centimeters (cm)',
						'm'  => 'Meters (m)',
						'in' => 'Inches (in)',
						'ft' => 'Feet (ft)',
					],
					'default'     => 'cm',
				],

				// Inventory Settings
				[
					'name'        => 'low_stock_threshold',
					'type'        => 'number',
					'label'       => 'Low Stock Threshold',
					'description' => 'Show low stock warning when quantity is below this',
					'default'     => 5,
					'min'         => 0,
				],
				[
					'name'        => 'out_of_stock_visibility',
					'type'        => 'checkbox',
					'label'       => 'Hide Out of Stock Products',
					'description' => 'Hide products from catalog when out of stock',
					'default'     => false,
				],
				[
					'name'        => 'enable_backorders',
					'type'        => 'checkbox',
					'label'       => 'Enable Backorders',
					'description' => 'Allow customers to order out-of-stock products',
					'default'     => false,
				],

				// Display Options
				[
					'name'        => 'products_per_page',
					'type'        => 'number',
					'label'       => 'Products Per Page',
					'description' => 'Number of products to show per page in archive',
					'default'     => 12,
					'min'         => 1,
					'max'         => 100,
				],
				[
					'name'        => 'product_sort_by',
					'type'        => 'select',
					'label'       => 'Default Sort Order',
					'description' => 'Default product sorting on archive pages',
					'options'     => [
						'date_desc'  => 'Newest First',
						'date_asc'   => 'Oldest First',
						'title_asc'  => 'Name (A-Z)',
						'title_desc' => 'Name (Z-A)',
						'price_asc'  => 'Price: Low to High',
						'price_desc' => 'Price: High to Low',
					],
					'default'     => 'date_desc',
				],
				[
					'name'        => 'show_sale_badge',
					'type'        => 'checkbox',
					'label'       => 'Show Sale Badges',
					'description' => 'Display sale badges on products',
					'default'     => true,
				],
				[
					'name'        => 'badge_color',
					'type'        => 'color',
					'label'       => 'Sale Badge Color',
					'description' => 'Color for sale badges',
					'default'     => '#e74c3c',
				],

				// Email Notifications
				[
					'name'        => 'low_stock_email',
					'type'        => 'email',
					'label'       => 'Low Stock Alert Email',
					'description' => 'Email address for low stock notifications',
					'placeholder' => 'admin@example.com',
				],
				[
					'name'        => 'enable_stock_alerts',
					'type'        => 'checkbox',
					'label'       => 'Enable Stock Alerts',
					'description' => 'Send email when stock falls below threshold',
					'default'     => true,
				],

				// Product Archive Page
				[
					'name'        => 'archive_page_title',
					'type'        => 'text',
					'label'       => 'Archive Page Title',
					'description' => 'Custom title for products archive page',
					'default'     => 'Shop',
					'placeholder' => 'Shop',
				],
				[
					'name'        => 'archive_page_description',
					'type'        => 'textarea',
					'label'       => 'Archive Page Description',
					'description' => 'Description text for products archive page',
					'placeholder' => 'Browse our collection of products...',
					'rows'        => 3,
				],
			],
		],
	],
	];

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'cpt_with_submenu_settings_init' );

/**
 * Activation hook
 */
function cpt_with_submenu_settings_activate() {
	cpt_with_submenu_settings_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cpt_with_submenu_settings_activate' );

/**
 * Deactivation hook
 */
function cpt_with_submenu_settings_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cpt_with_submenu_settings_deactivate' );
