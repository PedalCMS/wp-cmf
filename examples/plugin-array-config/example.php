<?php
/**
 * Plugin Name: WP-CMF Array Configuration Example
 * Description: Complete example of array-based configuration for CPTs, settings pages, and fields
 * Version: 1.0.0
 * Author: PedalCMS
 *
 * This example demonstrates the most streamlined way to configure WP-CMF:
 * - Register CPTs with fields
 * - Register settings pages with fields
 * - All configuration in a single array
 *
 * @package Pedalcms\WpCmf\Examples
 */

use Pedalcms\WpCmf\Core\Manager;

// Require Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Initialize WP-CMF with array configuration
 */
add_action(
	'init',
	function () {
		$config = array(
			/**
			 * Custom Post Types
			 *
			 * Each CPT entry defines:
			 * - id: Post type slug (required)
			 * - args: Arguments for register_post_type()
			 * - fields: Array of field configurations for metaboxes
			 */
			'cpts'           => array(
				/**
				 * Book Custom Post Type
				 */
				array(
					'id'     => 'book',
					'args'   => array(
						'singular'        => 'Book',
						'plural'          => 'Books',
						'public'          => true,
						'has_archive'     => true,
						'show_in_rest'    => true,
						'menu_icon'       => 'dashicons-book',
						'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
						'rewrite'         => array( 'slug' => 'books' ),
						'show_in_menu'    => true,
						'menu_position'   => 5,
					),
					'fields' => array(
						// ISBN
						array(
							'name'        => 'book_isbn',
							'type'        => 'text',
							'label'       => 'ISBN',
							'description' => 'International Standard Book Number',
							'required'    => true,
							'placeholder' => 'e.g., 978-3-16-148410-0',
						),
						// Author
						array(
							'name'        => 'book_author',
							'type'        => 'text',
							'label'       => 'Author',
							'description' => 'Primary author name',
							'required'    => true,
						),
						// Co-Authors
						array(
							'name'        => 'book_co_authors',
							'type'        => 'textarea',
							'label'       => 'Co-Authors',
							'description' => 'Additional authors (one per line)',
							'rows'        => 3,
						),
						// Genre
						array(
							'name'        => 'book_genre',
							'type'        => 'select',
							'label'       => 'Genre',
							'description' => 'Book genre/category',
							'options'     => array(
								'fiction'     => 'Fiction',
								'non-fiction' => 'Non-Fiction',
								'mystery'     => 'Mystery',
								'scifi'       => 'Science Fiction',
								'romance'     => 'Romance',
								'thriller'    => 'Thriller',
								'fantasy'     => 'Fantasy',
								'biography'   => 'Biography',
								'history'     => 'History',
								'other'       => 'Other',
							),
						),
						// Page Count
						array(
							'name'        => 'book_pages',
							'type'        => 'number',
							'label'       => 'Number of Pages',
							'description' => 'Total page count',
							'min'         => 1,
							'max'         => 10000,
						),
						// Publication Date
						array(
							'name'        => 'book_pub_date',
							'type'        => 'date',
							'label'       => 'Publication Date',
							'description' => 'Original publication date',
						),
						// Publisher
						array(
							'name'        => 'book_publisher',
							'type'        => 'text',
							'label'       => 'Publisher',
							'description' => 'Publishing company name',
						),
						// Price
						array(
							'name'        => 'book_price',
							'type'        => 'number',
							'label'       => 'Price ($)',
							'description' => 'Retail price in USD',
							'min'         => 0,
							'step'        => 0.01,
						),
						// In Stock
						array(
							'name'        => 'book_in_stock',
							'type'        => 'checkbox',
							'label'       => 'In Stock',
							'description' => 'Is this book currently available?',
						),
						// Featured
						array(
							'name'        => 'book_featured',
							'type'        => 'checkbox',
							'label'       => 'Featured Book',
							'description' => 'Display on homepage',
						),
					),
				),

				/**
				 * Movie Custom Post Type
				 */
				array(
					'id'     => 'movie',
					'args'   => array(
						'singular'        => 'Movie',
						'plural'          => 'Movies',
						'public'          => true,
						'has_archive'     => true,
						'show_in_rest'    => true,
						'menu_icon'       => 'dashicons-video-alt2',
						'supports'        => array( 'title', 'editor', 'thumbnail' ),
						'rewrite'         => array( 'slug' => 'movies' ),
					),
					'fields' => array(
						// Director
						array(
							'name'        => 'movie_director',
							'type'        => 'text',
							'label'       => 'Director',
							'required'    => true,
						),
						// Release Year
						array(
							'name'        => 'movie_year',
							'type'        => 'number',
							'label'       => 'Release Year',
							'min'         => 1900,
							'max'         => 2100,
						),
						// Rating
						array(
							'name'        => 'movie_rating',
							'type'        => 'select',
							'label'       => 'MPAA Rating',
							'options'     => array(
								'G'     => 'G - General Audiences',
								'PG'    => 'PG - Parental Guidance',
								'PG-13' => 'PG-13 - Parents Strongly Cautioned',
								'R'     => 'R - Restricted',
								'NC-17' => 'NC-17 - Adults Only',
							),
						),
						// Runtime
						array(
							'name'        => 'movie_runtime',
							'type'        => 'number',
							'label'       => 'Runtime (minutes)',
							'min'         => 1,
							'max'         => 500,
						),
						// Genre (multiple)
						array(
							'name'        => 'movie_genres',
							'type'        => 'checkbox',
							'label'       => 'Genres',
							'description' => 'Select all that apply',
							'multiple'    => true,
							'options'     => array(
								'action'    => 'Action',
								'comedy'    => 'Comedy',
								'drama'     => 'Drama',
								'horror'    => 'Horror',
								'scifi'     => 'Sci-Fi',
								'thriller'  => 'Thriller',
								'romance'   => 'Romance',
								'animation' => 'Animation',
							),
						),
						// Streaming URL
						array(
							'name'        => 'movie_stream_url',
							'type'        => 'url',
							'label'       => 'Streaming URL',
							'description' => 'Link to watch the movie',
						),
					),
				),
			),

			/**
			 * Settings Pages
			 *
			 * Each settings page entry defines:
			 * - id: Page identifier (required)
			 * - page_title, menu_title, capability, slug, etc.
			 * - fields: Array of field configurations
			 */
			'settings_pages' => array(
				/**
				 * Library Settings Page
				 */
				array(
					'id'         => 'library-settings',
					'page_title' => 'Library Settings',
					'menu_title' => 'Library',
					'capability' => 'manage_options',
					'menu_slug'  => 'library-settings',
					'icon_url'   => 'dashicons-book-alt',
					'position'   => 60,
					'fields'     => array(
						// Library Name
						array(
							'name'        => 'library_name',
							'type'        => 'text',
							'label'       => 'Library Name',
							'description' => 'Name of your library or bookstore',
							'required'    => true,
						),
						// Contact Email
						array(
							'name'        => 'library_email',
							'type'        => 'email',
							'label'       => 'Contact Email',
							'description' => 'Email address for customer inquiries',
							'required'    => true,
						),
						// Website URL
						array(
							'name'        => 'library_website',
							'type'        => 'url',
							'label'       => 'Website URL',
							'description' => 'Your library\'s website',
						),
						// Theme Color
						array(
							'name'        => 'library_theme_color',
							'type'        => 'color',
							'label'       => 'Theme Color',
							'description' => 'Primary color for your library theme',
							'default'     => '#0073aa',
						),
						// Enable Book Reviews
						array(
							'name'        => 'library_enable_reviews',
							'type'        => 'checkbox',
							'label'       => 'Enable Book Reviews',
							'description' => 'Allow visitors to leave reviews',
							'default'     => true,
						),
						// Books Per Page
						array(
							'name'        => 'library_books_per_page',
							'type'        => 'number',
							'label'       => 'Books Per Page',
							'description' => 'Number of books to display per page',
							'default'     => 12,
							'min'         => 1,
							'max'         => 100,
						),
						// Default Sort Order
						array(
							'name'        => 'library_sort_order',
							'type'        => 'radio',
							'label'       => 'Default Sort Order',
							'description' => 'How books should be sorted by default',
							'options'     => array(
								'title'  => 'Title (A-Z)',
								'author' => 'Author',
								'date'   => 'Publication Date',
								'price'  => 'Price (Low to High)',
							),
							'default'     => 'title',
						),
						// Featured Categories
						array(
							'name'        => 'library_featured_cats',
							'type'        => 'checkbox',
							'label'       => 'Featured Categories',
							'description' => 'Show these categories on the homepage',
							'multiple'    => true,
							'options'     => array(
								'bestsellers' => 'Bestsellers',
								'new'         => 'New Releases',
								'classics'    => 'Classics',
								'staff-picks' => 'Staff Picks',
							),
						),
						// Opening Hours
						array(
							'name'        => 'library_hours',
							'type'        => 'textarea',
							'label'       => 'Opening Hours',
							'description' => 'Your library opening hours (one per line)',
							'rows'        => 7,
							'placeholder' => "Monday: 9am - 6pm\nTuesday: 9am - 6pm\n...",
						),
					),
				),

				/**
				 * Movie Catalog Settings
				 */
				array(
					'id'         => 'movie-settings',
					'page_title' => 'Movie Catalog Settings',
					'menu_title' => 'Movies',
					'capability' => 'manage_options',
					'menu_slug'  => 'movie-settings',
					'icon_url'   => 'dashicons-video-alt2',
					'position'   => 61,
					'fields'     => array(
						// Enable Movie Catalog
						array(
							'name'        => 'movies_enabled',
							'type'        => 'checkbox',
							'label'       => 'Enable Movie Catalog',
							'description' => 'Show movies alongside books',
							'default'     => true,
						),
						// Movies Per Page
						array(
							'name'        => 'movies_per_page',
							'type'        => 'number',
							'label'       => 'Movies Per Page',
							'default'     => 12,
							'min'         => 1,
							'max'         => 100,
						),
						// Embed Player
						array(
							'name'        => 'movies_embed_player',
							'type'        => 'checkbox',
							'label'       => 'Embed Video Player',
							'description' => 'Embed video player on movie pages',
						),
						// Poster Dimensions
						array(
							'name'        => 'movies_poster_height',
							'type'        => 'number',
							'label'       => 'Poster Height (px)',
							'description' => 'Height for movie posters',
							'default'     => 400,
							'min'         => 100,
							'max'         => 1000,
						),
					),
				),
			),
		);

		// Initialize WP-CMF with the configuration
		Manager::init()->register_from_array( $config );
	}
);

