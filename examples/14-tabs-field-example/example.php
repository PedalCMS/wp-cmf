<?php
/**
 * Example 14: Tabs Field (Container Field)
 *
 * Demonstrates the new Tabs container field with both horizontal and vertical orientations.
 * Shows usage in both Custom Post Types and Settings Pages.
 *
 * Container fields like tabs don't store their own values - they only organize other fields.
 * Nested fields within tabs save and load exactly like regular fields using their field names.
 *
 * @package Pedalcms\WpCmf
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

// Initialize WP-CMF Manager
$manager = Manager::init();

// ============================================================================
// Example 1: Custom Post Type with Horizontal Tabs
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
				// Product metabox with horizontal tabs
				'fields' => [
					[
						'name'        => 'product_details_tabs',
						'type'        => 'tabs',
						'label'       => 'Product Details',
						'orientation' => 'horizontal', // Browser-style tabs
						'default_tab' => 'basic',
						'tabs'        => [
							[
								'id'          => 'basic',
								'label'       => 'Basic Info',
								'icon'        => 'dashicons-info',
								'description' => 'Basic product information and pricing',
								'fields'      => [
									[
										'name'        => 'product_sku',
										'type'        => 'text',
										'label'       => 'SKU',
										'placeholder' => 'PROD-001',
										'required'    => true,
									],
									[
										'name'        => 'product_price',
										'type'        => 'number',
										'label'       => 'Price ($)',
										'placeholder' => '99.99',
										'validation'  => [
											'min' => 0,
										],
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
										'default' => 'in_stock',
									],
								],
							],
							[
								'id'          => 'details',
								'label'       => 'Details',
								'icon'        => 'dashicons-list-view',
								'description' => 'Additional product details and specifications',
								'fields'      => [
									[
										'name'        => 'product_brand',
										'type'        => 'text',
										'label'       => 'Brand',
										'placeholder' => 'Brand name',
									],
									[
										'name'        => 'product_weight',
										'type'        => 'number',
										'label'       => 'Weight (kg)',
										'placeholder' => '1.5',
										'validation'  => [
											'min'  => 0,
											'step' => 0.1,
										],
									],
									[
										'name'        => 'product_dimensions',
										'type'        => 'text',
										'label'       => 'Dimensions',
										'placeholder' => '10 x 5 x 3 cm',
									],
								],
							],
							[
								'id'          => 'shipping',
								'label'       => 'Shipping',
								'icon'        => 'dashicons-cart',
								'description' => 'Shipping options and settings',
								'fields'      => [
									[
										'name'    => 'product_free_shipping',
										'type'    => 'checkbox',
										'label'   => 'Free Shipping',
										'options' => [
											'yes' => 'Enable free shipping for this product',
										],
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
										'placeholder' => 'Select shipping class',
									],
								],
							],
						],
					],
				],
			],
		],
	]
);

// ============================================================================
// Example 2: Custom Post Type with Vertical Tabs
// ============================================================================

$manager->register_from_array(
	[
		'cpts' => [
			[
				'id'   => 'event',
				'args' => [
					'label'       => 'Events',
					'public'      => true,
					'has_archive' => true,
					'menu_icon'   => 'dashicons-calendar-alt',
					'supports'    => [ 'title', 'editor', 'thumbnail' ],
				],
				// Event metabox with vertical tabs (sidebar style)
				'fields' => [
					[
						'name'        => 'event_settings_tabs',
						'type'        => 'tabs',
						'label'       => 'Event Settings',
						'orientation' => 'vertical', // Sidebar navigation
						'default_tab' => 'datetime',
						'tabs'        => [
							[
								'id'          => 'datetime',
								'label'       => 'Date & Time',
								'icon'        => 'dashicons-clock',
								'description' => 'Event schedule information',
								'fields'      => [
									[
										'name'  => 'event_date',
										'type'  => 'date',
										'label' => 'Event Date',
									],
									[
										'name'        => 'event_start_time',
										'type'        => 'text',
										'label'       => 'Start Time',
										'placeholder' => '09:00 AM',
									],
									[
										'name'        => 'event_end_time',
										'type'        => 'text',
										'label'       => 'End Time',
										'placeholder' => '05:00 PM',
									],
								],
							],
							[
								'id'          => 'location',
								'label'       => 'Location',
								'icon'        => 'dashicons-location',
								'description' => 'Event location details',
								'fields'      => [
									[
										'name'        => 'event_venue',
										'type'        => 'text',
										'label'       => 'Venue Name',
										'placeholder' => 'Convention Center',
									],
									[
										'name'        => 'event_address',
										'type'        => 'textarea',
										'label'       => 'Address',
										'placeholder' => 'Enter full address',
										'rows'        => 3,
									],
									[
										'name'        => 'event_map_url',
										'type'        => 'url',
										'label'       => 'Google Maps URL',
										'placeholder' => 'https://maps.google.com/...',
									],
								],
							],
							[
								'id'          => 'tickets',
								'label'       => 'Tickets',
								'icon'        => 'dashicons-tickets-alt',
								'description' => 'Ticketing information',
								'fields'      => [
									[
										'name'        => 'event_ticket_price',
										'type'        => 'number',
										'label'       => 'Ticket Price ($)',
										'placeholder' => '50',
										'validation'  => [
											'min' => 0,
										],
									],
									[
										'name'        => 'event_capacity',
										'type'        => 'number',
										'label'       => 'Capacity',
										'placeholder' => '100',
										'validation'  => [
											'min' => 1,
										],
									],
									[
										'name'    => 'event_registration_url',
										'type'    => 'url',
										'label'   => 'Registration URL',
										'placeholder' => 'https://...',
									],
								],
							],
							[
								'id'          => 'organizer',
								'label'       => 'Organizer',
								'icon'        => 'dashicons-groups',
								'description' => 'Event organizer information',
								'fields'      => [
									[
										'name'        => 'event_organizer_name',
										'type'        => 'text',
										'label'       => 'Organizer Name',
										'placeholder' => 'John Doe',
									],
									[
										'name'        => 'event_organizer_email',
										'type'        => 'email',
										'label'       => 'Contact Email',
										'placeholder' => 'contact@example.com',
									],
									[
										'name'        => 'event_organizer_phone',
										'type'        => 'text',
										'label'       => 'Contact Phone',
										'placeholder' => '+1 (555) 123-4567',
									],
								],
							],
						],
					],
				],
			],
		],
	]
);

// ============================================================================
// Example 3: Settings Page with Horizontal Tabs
// ============================================================================

$manager->register_from_array(
	[
		'settings_pages' => [
			[
				'id'         => 'store-settings',
				'title'      => 'Store Settings',
				'menu_title' => 'Store Settings',
				'capability' => 'manage_options',
				'slug'       => 'store-settings',
				'icon'       => 'dashicons-store',
				'fields'     => [
					[
						'name'        => 'store_tabs',
						'type'        => 'tabs',
						'label'       => 'Store Configuration',
						'orientation' => 'horizontal',
						'default_tab' => 'general',
						'tabs'        => [
							[
								'id'          => 'general',
								'label'       => 'General',
								'icon'        => 'dashicons-admin-generic',
								'description' => 'General store settings',
								'fields'      => [
									[
										'name'        => 'store_name',
										'type'        => 'text',
										'label'       => 'Store Name',
										'placeholder' => 'My Online Store',
									],
									[
										'name'        => 'store_email',
										'type'        => 'email',
										'label'       => 'Store Email',
										'placeholder' => 'store@example.com',
									],
									[
										'name'    => 'store_currency',
										'type'    => 'select',
										'label'   => 'Currency',
										'options' => [
											'USD' => 'US Dollar',
											'EUR' => 'Euro',
											'GBP' => 'British Pound',
											'CAD' => 'Canadian Dollar',
										],
										'default' => 'USD',
									],
								],
							],
							[
								'id'          => 'checkout',
								'label'       => 'Checkout',
								'icon'        => 'dashicons-cart',
								'description' => 'Checkout page settings',
								'fields'      => [
									[
										'name'    => 'store_guest_checkout',
										'type'    => 'checkbox',
										'label'   => 'Guest Checkout',
										'options' => [
											'enabled' => 'Allow customers to checkout without an account',
										],
									],
									[
										'name'    => 'store_terms_page',
										'type'    => 'text',
										'label'   => 'Terms & Conditions Page',
										'placeholder' => 'URL to terms page',
									],
								],
							],
							[
								'id'          => 'payments',
								'label'       => 'Payments',
								'icon'        => 'dashicons-money-alt',
								'description' => 'Payment gateway configuration',
								'fields'      => [
									[
										'name'    => 'store_payment_methods',
										'type'    => 'checkbox',
										'label'   => 'Enabled Payment Methods',
										'options' => [
											'stripe'  => 'Stripe',
											'paypal'  => 'PayPal',
											'cash'    => 'Cash on Delivery',
										],
									],
									[
										'name'    => 'store_test_mode',
										'type'    => 'checkbox',
										'label'   => 'Test Mode',
										'options' => [
											'enabled' => 'Enable test mode for payments',
										],
									],
								],
							],
						],
					],
				],
			],
		],
	]
);

// ============================================================================
// Example 4: Settings Page with Vertical Tabs
// ============================================================================

$manager->register_from_array(
	[
		'settings_pages' => [
			[
				'id'         => 'app-config',
				'title'      => 'App Configuration',
				'menu_title' => 'App Config',
				'capability' => 'manage_options',
				'slug'       => 'app-config',
				'icon'       => 'dashicons-admin-tools',
				'fields'     => [
					[
						'name'        => 'app_config_tabs',
						'type'        => 'tabs',
						'label'       => 'Application Settings',
						'orientation' => 'vertical',
						'default_tab' => 'api',
						'tabs'        => [
							[
								'id'          => 'api',
								'label'       => 'API Settings',
								'icon'        => 'dashicons-admin-plugins',
								'description' => 'Configure API keys and endpoints',
								'fields'      => [
									[
										'name'        => 'app_api_key',
										'type'        => 'password',
										'label'       => 'API Key',
										'placeholder' => 'Enter your API key',
									],
									[
										'name'        => 'app_api_secret',
										'type'        => 'password',
										'label'       => 'API Secret',
										'placeholder' => 'Enter your API secret',
									],
									[
										'name'        => 'app_api_endpoint',
										'type'        => 'url',
										'label'       => 'API Endpoint',
										'placeholder' => 'https://api.example.com',
									],
								],
							],
							[
								'id'          => 'email',
								'label'       => 'Email Settings',
								'icon'        => 'dashicons-email',
								'description' => 'Configure email notifications',
								'fields'      => [
									[
										'name'        => 'app_email_from',
										'type'        => 'email',
										'label'       => 'From Email',
										'placeholder' => 'noreply@example.com',
									],
									[
										'name'        => 'app_email_name',
										'type'        => 'text',
										'label'       => 'From Name',
										'placeholder' => 'My App',
									],
									[
										'name'    => 'app_email_notifications',
										'type'    => 'checkbox',
										'label'   => 'Enable Notifications',
										'options' => [
											'new_user'     => 'New user registration',
											'new_order'    => 'New order placed',
											'low_stock'    => 'Low stock alerts',
										],
									],
								],
							],
							[
								'id'          => 'advanced',
								'label'       => 'Advanced',
								'icon'        => 'dashicons-admin-settings',
								'description' => 'Advanced configuration options',
								'fields'      => [
									[
										'name'    => 'app_debug_mode',
										'type'    => 'checkbox',
										'label'   => 'Debug Mode',
										'options' => [
											'enabled' => 'Enable debug mode (not recommended for production)',
										],
									],
									[
										'name'        => 'app_cache_duration',
										'type'        => 'number',
										'label'       => 'Cache Duration (seconds)',
										'placeholder' => '3600',
										'validation'  => [
											'min' => 0,
										],
									],
								],
							],
						],
					],
				],
			],
		],
	]
);
