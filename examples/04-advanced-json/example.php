<?php
/**
 * Plugin Name: WP-CMF Advanced Example (JSON)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Advanced example demonstrating ALL WP-CMF capabilities using JSON configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * License: GPL v2 or later
 *
 * @package WpCmfAdvancedJson
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * =============================================================================
 * ADVANCED JSON EXAMPLE
 * =============================================================================
 *
 * This comprehensive example demonstrates ALL WP-CMF capabilities using
 * external JSON configuration files.
 *
 * Features demonstrated:
 * 1. Creating a new Custom Post Type (Property) with all field types
 * 2. Creating a new Settings Page with tabs and groups
 * 3. Adding fields to existing post types (post, page)
 * 4. Adding fields to existing settings pages
 * 5. All 16 field types in realistic scenarios
 * 6. Multiple JSON configuration files
 * 7. Before-save filters (PHP-only feature)
 *
 * JSON Benefits:
 * - Easier for non-developers to modify
 * - Can be validated against schema
 * - Great for multi-environment configs
 * - Easy to share/export
 * =============================================================================
 */
function wp_cmf_advanced_json_init() {
	$manager = Manager::init();

	// Load all JSON configurations
	$config_files = [
		__DIR__ . '/config/cpt-property.json',
		__DIR__ . '/config/settings-agency.json',
		__DIR__ . '/config/extend-posts.json',
		__DIR__ . '/config/extend-settings.json',
	];

	foreach ( $config_files as $file ) {
		if ( file_exists( $file ) ) {
			$manager->register_from_json( $file );
		}
	}
}
add_action( 'init', 'wp_cmf_advanced_json_init' );

/**
 * =============================================================================
 * BEFORE-SAVE FILTERS (PHP-only feature)
 * =============================================================================
 * These cannot be defined in JSON, demonstrating that some features
 * require PHP even when using JSON configuration.
 */

// Format phone number
add_filter(
	'wp_cmf_before_save_field_agent_phone',
	function ( $value ) {
		// Remove non-numeric characters
		$numbers = preg_replace( '/[^0-9]/', '', $value );
		// Format as (XXX) XXX-XXXX
		if ( strlen( $numbers ) === 10 ) {
			return sprintf(
				'(%s) %s-%s',
				substr( $numbers, 0, 3 ),
				substr( $numbers, 3, 3 ),
				substr( $numbers, 6 )
			);
		}
		return $value;
	}
);

// Ensure property price is rounded to 2 decimal places
add_filter(
	'wp_cmf_before_save_field_property_price',
	function ( $value ) {
		return round( (float) $value, 2 );
	}
);

// Auto-generate listing ID if empty
add_filter(
	'wp_cmf_before_save_field_listing_id',
	function ( $value, $post_id ) {
		if ( empty( $value ) ) {
			return 'PROP-' . str_pad( $post_id, 6, '0', STR_PAD_LEFT );
		}
		return strtoupper( $value );
	},
	10,
	2
);

/**
 * =============================================================================
 * RETRIEVING SAVED VALUES
 * =============================================================================
 */

/**
 * Get property field value
 */
function get_property_field( $post_id, $field ) {
	return get_post_meta( $post_id, $field, true );
}

/**
 * Get agency setting
 */
function get_agency_setting( $field, $default = '' ) {
	return get_option( 'agency-settings_' . $field, $default );
}

/**
 * Get extended post option
 */
function get_extended_post_field( $post_id, $field ) {
	return get_post_meta( $post_id, $field, true );
}

/**
 * =============================================================================
 * EXAMPLE: DISPLAY PROPERTY DETAILS
 * =============================================================================
 */
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! is_singular( 'property' ) ) {
			return $content;
		}

		$post_id = get_the_ID();

		// Get property values
		$price        = get_property_field( $post_id, 'property_price' );
		$bedrooms     = get_property_field( $post_id, 'bedrooms' );
		$bathrooms    = get_property_field( $post_id, 'bathrooms' );
		$sqft         = get_property_field( $post_id, 'square_feet' );
		$listing_id   = get_property_field( $post_id, 'listing_id' );
		$status       = get_property_field( $post_id, 'property_status' );
		$type         = get_property_field( $post_id, 'property_type' );
		$amenities    = get_property_field( $post_id, 'amenities' );
		$virtual_tour = get_property_field( $post_id, 'virtual_tour_url' );

		$output = '<div class="property-details">';

		// Status badge
		$status_labels = [
			'active'  => 'Active',
			'pending' => 'Pending',
			'sold'    => 'Sold',
			'rented'  => 'Rented',
		];
		if ( $status ) {
			$output .= '<span class="status-badge status-' . esc_attr( $status ) . '">';
			$output .= esc_html( $status_labels[ $status ] ?? $status );
			$output .= '</span>';
		}

		// Listing ID
		if ( $listing_id ) {
			$output .= '<p class="listing-id">Listing #' . esc_html( $listing_id ) . '</p>';
		}

		// Price
		if ( $price ) {
			$output .= '<p class="price">$' . esc_html( number_format( $price ) ) . '</p>';
		}

		// Key details
		$output .= '<div class="key-details">';
		if ( $bedrooms ) {
			$output .= '<span class="detail">' . esc_html( $bedrooms ) . ' Beds</span>';
		}
		if ( $bathrooms ) {
			$output .= '<span class="detail">' . esc_html( $bathrooms ) . ' Baths</span>';
		}
		if ( $sqft ) {
			$output .= '<span class="detail">' . esc_html( number_format( $sqft ) ) . ' sqft</span>';
		}
		$output .= '</div>';

		// Property type
		$type_labels = [
			'house'     => 'House',
			'condo'     => 'Condo',
			'townhouse' => 'Townhouse',
			'apartment' => 'Apartment',
			'land'      => 'Land',
		];
		if ( $type ) {
			$output .= '<p><strong>Type:</strong> ' . esc_html( $type_labels[ $type ] ?? $type ) . '</p>';
		}

		// Amenities
		if ( ! empty( $amenities ) && is_array( $amenities ) ) {
			$output .= '<div class="amenities"><strong>Amenities:</strong> ';
			$output .= esc_html( implode( ', ', array_map( 'ucfirst', $amenities ) ) );
			$output .= '</div>';
		}

		// Virtual tour
		if ( $virtual_tour ) {
			$output .= '<p><a href="' . esc_url( $virtual_tour ) . '" target="_blank">View Virtual Tour →</a></p>';
		}

		$output .= '</div>';

		return $output . $content;
	}
);

/**
 * =============================================================================
 * EXAMPLE: PROPERTY ARCHIVE MODIFICATIONS
 * =============================================================================
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'property' ) ) {
			// Get default sort from settings
			$default_sort = get_agency_setting( 'default_sort', 'date' );

			switch ( $default_sort ) {
				case 'price_asc':
					$query->set( 'meta_key', 'property_price' );
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'ASC' );
					break;
				case 'price_desc':
					$query->set( 'meta_key', 'property_price' );
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
					break;
			}
		}
	}
);
