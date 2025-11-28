<?php
/**
 * Example 19: Repeater Field Example - Settings Page Usage
 *
 * This example demonstrates the RepeaterField container type
 * for creating repeatable sets of fields within Settings Pages.
 *
 * @package    Pedalcms\WpCmf
 * @subpackage Examples
 */

declare(strict_types=1);

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize the Repeater Field Settings Example
 */
function wp_cmf_repeater_settings_example_init(): void {
	// Ensure WP-CMF is loaded.
	if ( ! class_exists( Manager::class ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>WP-CMF is required for the Repeater Field Settings Example plugin.</p></div>';
			}
		);
		return;
	}

	$manager = Manager::init();

	// Register configuration using array format.
	$manager->register_from_array( wp_cmf_repeater_settings_get_config() );
}
add_action( 'plugins_loaded', 'wp_cmf_repeater_settings_example_init' );

/**
 * Get the configuration for this example
 *
 * @return array<string, mixed> Configuration array.
 */
function wp_cmf_repeater_settings_get_config(): array {
	return array(
		'settings_pages' => array(
			// =====================================================
			// Settings Page 1: Site Configuration
			// =====================================================
			array(
				'id'         => 'site_options',
				'title'      => 'Site Options',
				'menu_title' => 'Site Options',
				'capability' => 'manage_options',
				'slug'       => 'site-options',
				'icon'       => 'dashicons-admin-settings',
				'position'   => 80,
				'fields'     => array(
					// Social Media Links Repeater
					array(
						'name'         => 'social_links',
						'type'         => 'repeater',
						'label'        => 'Social Media Links',
						'description'  => 'Add your social media profiles.',
						'button_label' => 'Add Social Link',
						'row_label'    => 'Social Profile',
						'collapsible'  => true,
						'sortable'     => true,
						'max_rows'     => 20,
						'fields'       => array(
							array(
								'name'    => 'platform',
								'type'    => 'select',
								'label'   => 'Platform',
								'options' => array(
									'facebook'  => 'Facebook',
									'twitter'   => 'Twitter/X',
									'instagram' => 'Instagram',
									'linkedin'  => 'LinkedIn',
									'youtube'   => 'YouTube',
									'tiktok'    => 'TikTok',
									'pinterest' => 'Pinterest',
									'github'    => 'GitHub',
									'discord'   => 'Discord',
									'other'     => 'Other',
								),
							),
							array(
								'name'        => 'url',
								'type'        => 'url',
								'label'       => 'Profile URL',
								'placeholder' => 'https://',
								'required'    => true,
							),
							array(
								'name'        => 'label',
								'type'        => 'text',
								'label'       => 'Display Label (optional)',
								'placeholder' => 'Follow us on...',
							),
							array(
								'name'    => 'show_in_header',
								'type'    => 'checkbox',
								'label'   => 'Display Options',
								'options' => array(
									'header' => 'Show in Header',
									'footer' => 'Show in Footer',
								),
							),
						),
					),

					// FAQ Repeater
					array(
						'name'         => 'site_faq',
						'type'         => 'repeater',
						'label'        => 'Frequently Asked Questions',
						'description'  => 'Add FAQ items for your site.',
						'button_label' => 'Add FAQ',
						'row_label'    => 'Question',
						'collapsible'  => true,
						'collapsed'    => false,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'        => 'question',
								'type'        => 'text',
								'label'       => 'Question',
								'placeholder' => 'Enter the question',
								'required'    => true,
							),
							array(
								'name'     => 'answer',
								'type'     => 'textarea',
								'label'    => 'Answer',
								'rows'     => 4,
								'required' => true,
							),
							array(
								'name'    => 'category',
								'type'    => 'select',
								'label'   => 'Category',
								'options' => array(
									'general'  => 'General',
									'billing'  => 'Billing',
									'shipping' => 'Shipping',
									'returns'  => 'Returns',
									'support'  => 'Support',
								),
							),
						),
					),

					// Notification Bars Repeater
					array(
						'name'         => 'notification_bars',
						'type'         => 'repeater',
						'label'        => 'Notification Bars',
						'description'  => 'Configure notification bars to display on your site.',
						'button_label' => 'Add Notification',
						'row_label'    => 'Notification',
						'collapsible'  => true,
						'max_rows'     => 5,
						'fields'       => array(
							array(
								'name'     => 'message',
								'type'     => 'text',
								'label'    => 'Message',
								'required' => true,
							),
							array(
								'name'    => 'type',
								'type'    => 'select',
								'label'   => 'Type',
								'options' => array(
									'info'    => 'Info (Blue)',
									'success' => 'Success (Green)',
									'warning' => 'Warning (Yellow)',
									'error'   => 'Error (Red)',
								),
							),
							array(
								'name'        => 'link_url',
								'type'        => 'url',
								'label'       => 'Link URL (optional)',
								'placeholder' => 'https://',
							),
							array(
								'name'  => 'link_text',
								'type'  => 'text',
								'label' => 'Link Text',
							),
							array(
								'name'    => 'is_active',
								'type'    => 'checkbox',
								'label'   => 'Status',
								'options' => array(
									'active' => 'Active',
								),
							),
							array(
								'name'  => 'start_date',
								'type'  => 'date',
								'label' => 'Start Date (optional)',
							),
							array(
								'name'  => 'end_date',
								'type'  => 'date',
								'label' => 'End Date (optional)',
							),
						),
					),
				),
			),

			// =====================================================
			// Settings Page 2: Footer Configuration
			// =====================================================
			array(
				'id'         => 'footer_settings',
				'title'      => 'Footer Settings',
				'menu_title' => 'Footer',
				'capability' => 'manage_options',
				'slug'       => 'footer-settings',
				'parent'     => 'themes.php',
				'fields'     => array(
					// Footer Columns Repeater
					array(
						'name'         => 'footer_columns',
						'type'         => 'repeater',
						'label'        => 'Footer Columns',
						'description'  => 'Configure your footer columns. Each column can contain links or custom content.',
						'button_label' => 'Add Column',
						'row_label'    => 'Column',
						'collapsible'  => true,
						'min_rows'     => 1,
						'max_rows'     => 6,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'  => 'column_title',
								'type'  => 'text',
								'label' => 'Column Title',
							),
							array(
								'name'    => 'column_type',
								'type'    => 'radio',
								'label'   => 'Content Type',
								'options' => array(
									'links' => 'Link List',
									'text'  => 'Custom Text',
								),
								'default' => 'links',
							),
							array(
								'name'  => 'custom_text',
								'type'  => 'textarea',
								'label' => 'Custom Text/HTML',
								'rows'  => 4,
							),
						),
					),

					// Footer Links Repeater
					array(
						'name'         => 'footer_links',
						'type'         => 'repeater',
						'label'        => 'Footer Links',
						'description'  => 'Add links for the footer navigation.',
						'button_label' => 'Add Link',
						'row_label'    => 'Link',
						'sortable'     => true,
						'collapsible'  => false,
						'fields'       => array(
							array(
								'name'     => 'link_text',
								'type'     => 'text',
								'label'    => 'Link Text',
								'required' => true,
							),
							array(
								'name'     => 'link_url',
								'type'     => 'url',
								'label'    => 'URL',
								'required' => true,
							),
							array(
								'name'    => 'open_new_tab',
								'type'    => 'checkbox',
								'label'   => 'Options',
								'options' => array(
									'new_tab' => 'Open in new tab',
								),
							),
						),
					),

					// Partner Logos Repeater
					array(
						'name'         => 'partner_logos',
						'type'         => 'repeater',
						'label'        => 'Partner/Certification Logos',
						'description'  => 'Add partner or certification logos for the footer.',
						'button_label' => 'Add Logo',
						'row_label'    => 'Logo',
						'sortable'     => true,
						'max_rows'     => 10,
						'fields'       => array(
							array(
								'name'  => 'logo_name',
								'type'  => 'text',
								'label' => 'Name/Alt Text',
							),
							array(
								'name'        => 'logo_url',
								'type'        => 'url',
								'label'       => 'Logo Image URL',
								'placeholder' => 'https://example.com/logo.png',
							),
							array(
								'name'  => 'link_url',
								'type'  => 'url',
								'label' => 'Link URL (optional)',
							),
						),
					),
				),
			),

			// =====================================================
			// Settings Page 3: Pricing Tables
			// =====================================================
			array(
				'id'         => 'pricing_settings',
				'title'      => 'Pricing Configuration',
				'menu_title' => 'Pricing',
				'capability' => 'manage_options',
				'slug'       => 'pricing-settings',
				'icon'       => 'dashicons-money-alt',
				'fields'     => array(
					// Pricing Plans Repeater
					array(
						'name'         => 'pricing_plans',
						'type'         => 'repeater',
						'label'        => 'Pricing Plans',
						'description'  => 'Configure your pricing plans.',
						'button_label' => 'Add Plan',
						'row_label'    => 'Plan',
						'collapsible'  => true,
						'sortable'     => true,
						'min_rows'     => 1,
						'max_rows'     => 10,
						'fields'       => array(
							array(
								'name'     => 'plan_name',
								'type'     => 'text',
								'label'    => 'Plan Name',
								'required' => true,
							),
							array(
								'name'  => 'plan_description',
								'type'  => 'text',
								'label' => 'Short Description',
							),
							array(
								'name'     => 'price',
								'type'     => 'number',
								'label'    => 'Price',
								'min'      => 0,
								'step'     => 0.01,
								'required' => true,
							),
							array(
								'name'    => 'billing_period',
								'type'    => 'select',
								'label'   => 'Billing Period',
								'options' => array(
									'monthly'  => 'Monthly',
									'yearly'   => 'Yearly',
									'one_time' => 'One-Time',
								),
							),
							array(
								'name'  => 'features',
								'type'  => 'textarea',
								'label' => 'Features (one per line)',
								'rows'  => 5,
							),
							array(
								'name'        => 'button_text',
								'type'        => 'text',
								'label'       => 'Button Text',
								'default'     => 'Get Started',
								'placeholder' => 'Get Started',
							),
							array(
								'name'  => 'button_url',
								'type'  => 'url',
								'label' => 'Button URL',
							),
							array(
								'name'    => 'is_featured',
								'type'    => 'checkbox',
								'label'   => 'Highlight',
								'options' => array(
									'featured' => 'Mark as featured/recommended',
								),
							),
							array(
								'name'  => 'badge_text',
								'type'  => 'text',
								'label' => 'Badge Text (e.g., "Most Popular")',
							),
						),
					),

					// Currency Settings (regular field for context)
					array(
						'name'    => 'pricing_currency',
						'type'    => 'select',
						'label'   => 'Currency',
						'options' => array(
							'USD' => 'USD ($)',
							'EUR' => 'EUR (€)',
							'GBP' => 'GBP (£)',
							'CAD' => 'CAD ($)',
							'AUD' => 'AUD ($)',
						),
					),
				),
			),
		),
	);
}

/**
 * Helper function to display social links on the frontend
 *
 * @param string $location Display location ('header' or 'footer').
 * @return void
 */
function wp_cmf_display_social_links( string $location = 'footer' ): void {
	$social_links = get_option( 'social_links' );

	if ( empty( $social_links ) || ! is_array( $social_links ) ) {
		return;
	}

	echo '<div class="social-links social-links-' . esc_attr( $location ) . '">';
	foreach ( $social_links as $link ) {
		// Check if this link should display in the requested location.
		$show_in = isset( $link['show_in_header'] ) ? (array) $link['show_in_header'] : array();
		if ( ! in_array( $location, $show_in, true ) ) {
			continue;
		}

		if ( empty( $link['url'] ) ) {
			continue;
		}

		$platform = $link['platform'] ?? 'other';
		$label    = ! empty( $link['label'] ) ? $link['label'] : ucfirst( $platform );

		printf(
			'<a href="%s" class="social-link social-%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( $link['url'] ),
			esc_attr( $platform ),
			esc_html( $label )
		);
	}
	echo '</div>';
}

/**
 * Helper function to display FAQ on the frontend
 *
 * @param string|null $category Optional category filter.
 * @return void
 */
function wp_cmf_display_faq( ?string $category = null ): void {
	$faq_items = get_option( 'site_faq' );

	if ( empty( $faq_items ) || ! is_array( $faq_items ) ) {
		return;
	}

	echo '<div class="faq-section">';
	foreach ( $faq_items as $faq ) {
		// Filter by category if specified.
		if ( null !== $category && ( $faq['category'] ?? '' ) !== $category ) {
			continue;
		}

		if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
			continue;
		}

		echo '<div class="faq-item">';
		echo '<h3 class="faq-question">' . esc_html( $faq['question'] ) . '</h3>';
		echo '<div class="faq-answer">' . wp_kses_post( $faq['answer'] ) . '</div>';
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Helper function to display pricing table on the frontend
 *
 * @return void
 */
function wp_cmf_display_pricing_table(): void {
	$plans    = get_option( 'pricing_plans' );
	$currency = get_option( 'pricing_currency', 'USD' );

	if ( empty( $plans ) || ! is_array( $plans ) ) {
		return;
	}

	$currency_symbols = array(
		'USD' => '$',
		'EUR' => '€',
		'GBP' => '£',
		'CAD' => '$',
		'AUD' => '$',
	);
	$symbol           = $currency_symbols[ $currency ] ?? '$';

	echo '<div class="pricing-table">';
	foreach ( $plans as $plan ) {
		$is_featured = ! empty( $plan['is_featured'] ) && in_array( 'featured', (array) $plan['is_featured'], true );
		$classes     = 'pricing-plan' . ( $is_featured ? ' featured' : '' );

		echo '<div class="' . esc_attr( $classes ) . '">';

		// Badge.
		if ( ! empty( $plan['badge_text'] ) ) {
			echo '<span class="plan-badge">' . esc_html( $plan['badge_text'] ) . '</span>';
		}

		// Plan name.
		echo '<h3 class="plan-name">' . esc_html( $plan['plan_name'] ?? '' ) . '</h3>';

		// Description.
		if ( ! empty( $plan['plan_description'] ) ) {
			echo '<p class="plan-description">' . esc_html( $plan['plan_description'] ) . '</p>';
		}

		// Price.
		echo '<div class="plan-price">';
		echo '<span class="price">' . esc_html( $symbol . number_format( (float) ( $plan['price'] ?? 0 ), 2 ) ) . '</span>';
		if ( ! empty( $plan['billing_period'] ) && 'one_time' !== $plan['billing_period'] ) {
			echo '<span class="period">/' . esc_html( $plan['billing_period'] === 'monthly' ? 'mo' : 'yr' ) . '</span>';
		}
		echo '</div>';

		// Features.
		if ( ! empty( $plan['features'] ) ) {
			$features = array_filter( array_map( 'trim', explode( "\n", $plan['features'] ) ) );
			if ( ! empty( $features ) ) {
				echo '<ul class="plan-features">';
				foreach ( $features as $feature ) {
					echo '<li>' . esc_html( $feature ) . '</li>';
				}
				echo '</ul>';
			}
		}

		// Button.
		if ( ! empty( $plan['button_url'] ) ) {
			printf(
				'<a href="%s" class="plan-button">%s</a>',
				esc_url( $plan['button_url'] ),
				esc_html( $plan['button_text'] ?? 'Get Started' )
			);
		}

		echo '</div>';
	}
	echo '</div>';
}
