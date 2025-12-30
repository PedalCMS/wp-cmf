<?php
/**
 * Plugin Name: WP-CMF Simple Example (Array)
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Simple example demonstrating WP-CMF basics using PHP array configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * License: GPL v2 or later
 *
 * @package WpCmfSimpleArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * =============================================================================
 * SIMPLE ARRAY EXAMPLE
 * =============================================================================
 *
 * This example demonstrates:
 * 1. Creating a simple custom post type (Book) with basic fields
 * 2. Creating a simple taxonomy (Genre) with custom fields
 * 3. Creating a simple settings page with common field types
 *
 * For advanced features (all field types, tabs, repeaters, groups, metaboxes,
 * adding to existing post types/settings), see the advanced examples.
 * =============================================================================
 */
function wp_cmf_simple_array_init() {
	$config = [
		// =====================================================================
		// CUSTOM POST TYPE: Book
		// =====================================================================
		'cpts'           => [
			[
				'id'     => 'book',
				'args'   => [
					'label'        => 'Books',
					'public'       => true,
					'has_archive'  => true,
					'show_in_rest' => true,
					'supports'     => [ 'title', 'editor', 'thumbnail' ],
					'menu_icon'    => 'dashicons-book',
				],
				'fields' => [
					// Text field - for ISBN
					[
						'name'        => 'isbn',
						'type'        => 'text',
						'label'       => 'ISBN',
						'description' => 'International Standard Book Number',
						'placeholder' => '978-3-16-148410-0',
					],
					// Text field - for Author
					[
						'name'     => 'author_name',
						'type'     => 'text',
						'label'    => 'Author',
						'required' => true,
					],
					// Number field - for Pages
					[
						'name'  => 'page_count',
						'type'  => 'number',
						'label' => 'Number of Pages',
						'min'   => 1,
						'max'   => 10000,
					],
					// Date field - for Publication Date
					[
						'name'  => 'publication_date',
						'type'  => 'date',
						'label' => 'Publication Date',
					],
					// Checkbox - for Availability
					[
						'name'        => 'in_stock',
						'type'        => 'checkbox',
						'label'       => 'In Stock',
						'description' => 'Check if this book is currently in stock',
					],
					// Textarea - for Synopsis
					[
						'name'        => 'synopsis',
						'type'        => 'textarea',
						'label'       => 'Synopsis',
						'description' => 'Brief description of the book',
						'rows'        => 4,
					],
				],
			],
		],

		// =====================================================================
		// TAXONOMY: Genre
		// =====================================================================
		'taxonomies'     => [
			[
				'id'          => 'book_genre',
				'object_type' => [ 'book' ],
				'args'        => [
					'label'             => 'Genres',
					'hierarchical'      => true,
					'public'            => true,
					'show_in_rest'      => true,
					'show_admin_column' => true,
				],
				'fields'      => [
					// Color field - for genre badge color
					[
						'name'        => 'genre_color',
						'type'        => 'color',
						'label'       => 'Genre Color',
						'description' => 'Color used for genre badges and labels',
						'default'     => '#2271b1',
					],
					// Text field - for icon class
					[
						'name'        => 'genre_icon',
						'type'        => 'text',
						'label'       => 'Icon Class',
						'description' => 'Dashicons class (e.g., dashicons-book)',
						'placeholder' => 'dashicons-book',
					],
					// Checkbox - for featured genre
					[
						'name'        => 'is_featured',
						'type'        => 'checkbox',
						'label'       => 'Featured Genre',
						'description' => 'Display this genre prominently on the site',
					],
				],
			],
		],

		// =====================================================================
		// SETTINGS PAGE: Library Settings
		// =====================================================================
		'settings_pages' => [
			[
				'id'         => 'library-settings',
				'title'      => 'Library Settings',
				'menu_title' => 'Library',
				'capability' => 'manage_options',
				'icon'       => 'dashicons-book-alt',
				'position'   => 80,
				'fields'     => [
					// Text - Library Name
					[
						'name'        => 'library_name',
						'type'        => 'text',
						'label'       => 'Library Name',
						'placeholder' => 'My Library',
					],
					// Email - Contact Email
					[
						'name'  => 'contact_email',
						'type'  => 'email',
						'label' => 'Contact Email',
					],
					// URL - Website
					[
						'name'  => 'website_url',
						'type'  => 'url',
						'label' => 'Website URL',
					],
					// Number - Max Borrowing Days
					[
						'name'    => 'max_borrow_days',
						'type'    => 'number',
						'label'   => 'Max Borrowing Days',
						'default' => 14,
						'min'     => 1,
						'max'     => 90,
					],
					// Checkbox - Enable Notifications
					[
						'name'        => 'enable_notifications',
						'type'        => 'checkbox',
						'label'       => 'Enable Email Notifications',
						'description' => 'Send email reminders for due books',
					],
					// Radio - Theme
					[
						'name'    => 'display_theme',
						'type'    => 'radio',
						'label'   => 'Display Theme',
						'options' => [
							'light' => 'Light',
							'dark'  => 'Dark',
							'auto'  => 'Auto (System)',
						],
						'default' => 'auto',
					],
					// Color - Accent Color
					[
						'name'    => 'accent_color',
						'type'    => 'color',
						'label'   => 'Accent Color',
						'default' => '#2271b1',
					],
				],
			],
		],
	];

	Manager::init()->register_from_array( $config );
}
add_action( 'init', 'wp_cmf_simple_array_init' );

/**
 * =============================================================================
 * RETRIEVING SAVED VALUES
 * =============================================================================
 */

/**
 * Get book meta value
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return mixed
 */
function get_book_field( $post_id, $field ) {
	return get_post_meta( $post_id, $field, true );
}

/**
 * Get library setting
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function get_library_setting( $field, $default = '' ) {
	return get_option( 'library-settings_' . $field, $default );
}

/**
 * Get genre term meta value
 *
 * @param int    $term_id Term ID.
 * @param string $field   Field name.
 * @return mixed
 */
function get_genre_field( $term_id, $field ) {
	return get_term_meta( $term_id, $field, true );
}

/**
 * Example: Display book details in content
 */
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! is_singular( 'book' ) ) {
			return $content;
		}

		$post_id = get_the_ID();
		$author  = get_book_field( $post_id, 'author_name' );
		$isbn    = get_book_field( $post_id, 'isbn' );
		$pages   = get_book_field( $post_id, 'page_count' );

		$details = '<div class="book-details">';
		if ( $author ) {
			$details .= '<p><strong>Author:</strong> ' . esc_html( $author ) . '</p>';
		}
		if ( $isbn ) {
			$details .= '<p><strong>ISBN:</strong> ' . esc_html( $isbn ) . '</p>';
		}
		if ( $pages ) {
			$details .= '<p><strong>Pages:</strong> ' . esc_html( $pages ) . '</p>';
		}

		// Get genres from taxonomy with their custom color
		$genres = get_the_terms( $post_id, 'book_genre' );
		if ( $genres && ! is_wp_error( $genres ) ) {
			$details .= '<p><strong>Genres:</strong> ';
			$genre_links = [];
			foreach ( $genres as $genre ) {
				$color        = get_genre_field( $genre->term_id, 'genre_color' );
				$style        = $color ? ' style="color: ' . esc_attr( $color ) . ';"' : '';
				$genre_links[] = '<span' . $style . '>' . esc_html( $genre->name ) . '</span>';
			}
			$details .= implode( ', ', $genre_links );
			$details .= '</p>';
		}

		$details .= '</div>';

		return $details . $content;
	}
);
