<?php
/**
 * Plugin Name: Complete Array Configuration Example
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Comprehensive example demonstrating all 11 field types with CPTs and Settings Pages using array configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: complete-array-example
 *
 * @package CompleteArrayExample
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

function complete_array_example_init() {
	$config = [
		'cpts'            => [
			// Product Custom Post Type
			[
				'id'     => 'product',
				'args'   => [
					'label'       => 'Products',
					'labels'      => [
						'name'          => 'Products',
						'singular_name' => 'Product',
						'add_new'       => 'Add New Product',
						'add_new_item'  => 'Add New Product',
						'edit_item'     => 'Edit Product',
					],
					'public'      => true,
					'menu_icon'   => 'dashicons-cart',
					'supports'    => [ 'title', 'editor', 'thumbnail' ],
					'has_archive' => true,
					'show_in_rest' => true,
				],
				'fields' => [
					// Text Field
					[
						'name'        => 'sku',
						'type'        => 'text',
						'label'       => 'Product SKU',
						'description' => 'Unique product identifier',
						'placeholder' => 'PROD-001',
						'required'    => true,
						'context'     => 'normal',
						'priority'    => 'high',
					],
					// Textarea Field
					[
						'name'        => 'description',
						'type'        => 'textarea',
						'label'       => 'Detailed Description',
						'description' => 'Full product description',
						'rows'        => 8,
						'cols'        => 50,
						'placeholder' => 'Enter detailed product information',
						'context'     => 'normal',
					],
					// Number Field
					[
						'name'        => 'price',
						'type'        => 'number',
						'label'       => 'Price',
						'description' => 'Product price in USD',
						'min'         => 0,
						'max'         => 99999,
						'step'        => 0.01,
						'default'     => 0,
						'context'     => 'side',
					],
					// Select Field
					[
						'name'        => 'category',
						'type'        => 'select',
						'label'       => 'Product Category',
						'description' => 'Choose product category',
						'options'     => [
							'electronics' => 'Electronics',
							'clothing'    => 'Clothing',
							'books'       => 'Books',
							'home'        => 'Home & Garden',
							'toys'        => 'Toys & Games',
							'sports'      => 'Sports & Outdoors',
						],
						'default'     => 'electronics',
						'context'     => 'side',
					],
					// Radio Field
					[
						'name'        => 'condition',
						'type'        => 'radio',
						'label'       => 'Product Condition',
						'description' => 'Select product condition',
						'options'     => [
							'new'         => 'Brand New',
							'like_new'    => 'Like New',
							'used'        => 'Used',
							'refurbished' => 'Refurbished',
						],
						'default'     => 'new',
						'context'     => 'side',
					],
					// Checkbox Field
					[
						'name'        => 'in_stock',
						'type'        => 'checkbox',
						'label'       => 'In Stock',
						'description' => 'Check if product is available',
						'default'     => true,
						'context'     => 'side',
					],
					// Email Field
					[
						'name'        => 'supplier_email',
						'type'        => 'email',
						'label'       => 'Supplier Email',
						'description' => 'Email address of the supplier',
						'placeholder' => 'supplier@example.com',
						'context'     => 'advanced',
					],
					// URL Field
					[
						'name'        => 'product_url',
						'type'        => 'url',
						'label'       => 'External Product URL',
						'description' => 'Link to external product page',
						'placeholder' => 'https://example.com/product',
						'context'     => 'advanced',
					],
					// Date Field
					[
						'name'        => 'release_date',
						'type'        => 'date',
						'label'       => 'Release Date',
						'description' => 'Product release date',
						'min'         => '2020-01-01',
						'max'         => '2030-12-31',
						'context'     => 'normal',
					],
					// Password Field
					[
						'name'        => 'admin_access_code',
						'type'        => 'password',
						'label'       => 'Admin Access Code',
						'description' => 'Secure access code for product management',
						'context'     => 'advanced',
					],
					// Color Field
					[
						'name'        => 'primary_color',
						'type'        => 'color',
						'label'       => 'Primary Color',
						'description' => 'Main product color',
						'default'     => '#FF5733',
						'context'     => 'side',
					],
				],
			],

			// Event Custom Post Type
			[
				'id'     => 'event',
				'args'   => [
					'label'       => 'Events',
					'labels'      => [
						'name'          => 'Events',
						'singular_name' => 'Event',
						'add_new'       => 'Add New Event',
					],
					'public'      => true,
					'menu_icon'   => 'dashicons-calendar-alt',
					'supports'    => [ 'title', 'editor', 'thumbnail' ],
					'has_archive' => true,
					'show_in_rest' => true,
				],
				'fields' => [
					[
						'name'        => 'event_date',
						'type'        => 'date',
						'label'       => 'Event Date',
						'description' => 'When the event takes place',
						'required'    => true,
					],
					[
						'name'        => 'event_location',
						'type'        => 'text',
						'label'       => 'Location',
						'description' => 'Event venue or location',
						'placeholder' => 'Convention Center',
						'required'    => true,
					],
					[
						'name'        => 'max_attendees',
						'type'        => 'number',
						'label'       => 'Maximum Attendees',
						'description' => 'Maximum number of participants',
						'min'         => 1,
						'max'         => 10000,
						'default'     => 100,
					],
					[
						'name'        => 'registration_url',
						'type'        => 'url',
						'label'       => 'Registration URL',
						'description' => 'Link to event registration page',
						'placeholder' => 'https://example.com/register',
					],
					[
						'name'        => 'contact_email',
						'type'        => 'email',
						'label'       => 'Contact Email',
						'description' => 'Email for event inquiries',
						'placeholder' => 'events@example.com',
					],
				],
			],
		],

		'settings_pages' => [
			// Shop Settings Page
			[
				'id'         => 'shop_settings',
				'title'      => 'Shop Settings',
				'menu_title' => 'Shop Settings',
				'capability' => 'manage_options',
				'slug'       => 'shop-settings',
				'icon'       => 'dashicons-store',
				'position'   => 82,
				'fields'     => [
					[
						'name'        => 'store_name',
						'type'        => 'text',
						'label'       => 'Store Name',
						'description' => 'Your online store name',
						'placeholder' => 'My Awesome Store',
						'required'    => true,
					],
					[
						'name'        => 'store_description',
						'type'        => 'textarea',
						'label'       => 'Store Description',
						'description' => 'Brief description of your store',
						'rows'        => 5,
						'placeholder' => 'We sell amazing products...',
					],
					[
						'name'        => 'enable_cart',
						'type'        => 'checkbox',
						'label'       => 'Enable Shopping Cart',
						'description' => 'Enable cart functionality',
						'default'     => true,
					],
					[
						'name'        => 'currency',
						'type'        => 'select',
						'label'       => 'Currency',
						'description' => 'Store currency',
						'options'     => [
							'USD' => 'US Dollar',
							'EUR' => 'Euro',
							'GBP' => 'British Pound',
							'JPY' => 'Japanese Yen',
							'CAD' => 'Canadian Dollar',
						],
						'default'     => 'USD',
					],
					[
						'name'        => 'payment_method',
						'type'        => 'radio',
						'label'       => 'Primary Payment Method',
						'description' => 'Default payment option',
						'options'     => [
							'stripe'  => 'Stripe',
							'paypal'  => 'PayPal',
							'square'  => 'Square',
							'offline' => 'Offline Payment',
						],
						'default'     => 'stripe',
					],
					[
						'name'        => 'support_email',
						'type'        => 'email',
						'label'       => 'Support Email',
						'description' => 'Customer support email',
						'placeholder' => 'support@example.com',
						'required'    => true,
					],
					[
						'name'        => 'store_url',
						'type'        => 'url',
						'label'       => 'Store URL',
						'description' => 'Your main store URL',
						'placeholder' => 'https://mystore.com',
					],
					[
						'name'        => 'brand_color',
						'type'        => 'color',
						'label'       => 'Brand Color',
						'description' => 'Primary brand color',
						'default'     => '#E74C3C',
					],
					[
						'name'        => 'api_key',
						'type'        => 'password',
						'label'       => 'API Key',
						'description' => 'Secure API key for integrations',
					],
					[
						'name'        => 'max_order_amount',
						'type'        => 'number',
						'label'       => 'Maximum Order Amount',
						'description' => 'Maximum single order value',
						'min'         => 0,
						'max'         => 999999,
						'step'        => 1,
						'default'     => 10000,
					],
					[
						'name'        => 'sale_start_date',
						'type'        => 'date',
						'label'       => 'Next Sale Start Date',
						'description' => 'When the next sale begins',
					],
				],
			],

			// Event Management Settings
			[
				'id'         => 'event_settings',
				'title'      => 'Event Management',
				'menu_title' => 'Events Config',
				'capability' => 'manage_options',
				'slug'       => 'event-settings',
				'icon'       => 'dashicons-tickets-alt',
				'position'   => 83,
				'fields'     => [
					[
						'name'        => 'events_enabled',
						'type'        => 'checkbox',
						'label'       => 'Enable Events',
						'description' => 'Turn on event management',
						'default'     => true,
					],
					[
						'name'        => 'default_duration',
						'type'        => 'number',
						'label'       => 'Default Event Duration (hours)',
						'description' => 'Default length of events',
						'min'         => 1,
						'max'         => 24,
						'default'     => 2,
					],
					[
						'name'        => 'event_notification_email',
						'type'        => 'email',
						'label'       => 'Notification Email',
						'description' => 'Email for event notifications',
						'placeholder' => 'events@example.com',
					],
					[
						'name'        => 'event_page_color',
						'type'        => 'color',
						'label'       => 'Event Page Color',
						'description' => 'Color scheme for event pages',
						'default'     => '#3498DB',
					],
				],
			],
		],
	];

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'complete_array_example_init' );

function complete_array_example_activate() {
	complete_array_example_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'complete_array_example_activate' );

function complete_array_example_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'complete_array_example_deactivate' );
