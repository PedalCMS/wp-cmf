<?php
/**
 * Custom Post Type with Metabox Fields Example
 *
 * This example demonstrates a production-ready custom post type with:
 * - Custom post type registration (Book CPT)
 * - Multiple metaboxes with organized fields
 * - Field validation and sanitization
 * - Data saving and retrieval
 * - Different metabox contexts (normal, side, advanced)
 *
 * @package Pedalcms\WpCmf\Examples
 */

namespace Pedalcms\WpCmf\Examples\CptWithMetaboxFields;

use Pedalcms\WpCmf\Core\Manager;
use Pedalcms\WpCmf\Field\FieldFactory;

// Initialize WP-CMF
$manager   = Manager::init();
$registrar = $manager->get_registrar();

/**
 * Register the Book custom post type
 */
add_action(
	'init',
	function () use ( $registrar ) {
		$registrar->add_custom_post_type(
			[
				'id'   => 'book',
				'args' => [
					'labels'       => [
						'name'               => 'Books',
						'singular_name'      => 'Book',
						'add_new'            => 'Add New',
						'add_new_item'       => 'Add New Book',
						'edit_item'          => 'Edit Book',
						'new_item'           => 'New Book',
						'view_item'          => 'View Book',
						'search_items'       => 'Search Books',
						'not_found'          => 'No books found',
						'not_found_in_trash' => 'No books found in trash',
					],
					'public'       => true,
					'has_archive'  => true,
					'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
					'menu_icon'    => 'dashicons-book',
					'menu_position' => 20,
					'rewrite'      => [
						'slug'       => 'books',
						'with_front' => false,
					],
					'show_in_rest' => true,
				],
			]
		);
	}
);

/**
 * Register metaboxes and fields
 */
add_action(
	'add_meta_boxes',
	function () {
		register_book_details_metabox();
		register_pricing_metabox();
		register_publication_metabox();
		register_additional_info_metabox();
	}
);

/**
 * Save metabox data
 */
add_action(
	'save_post_book',
	function ( $post_id ) {
		// Verify nonce
		if ( ! isset( $_POST['book_meta_nonce'] ) ||
			! wp_verify_nonce( $_POST['book_meta_nonce'], 'save_book_meta' ) ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save all book metadata
		save_book_metadata( $post_id );
	},
	10,
	1
);

/**
 * Register Book Details metabox (main content area)
 */
function register_book_details_metabox() {
	add_meta_box(
		'book_details',
		'Book Details',
		function ( $post ) {
			render_book_details_fields( $post );
		},
		'book',
		'normal',
		'high'
	);
}

/**
 * Render book details fields
 */
function render_book_details_fields( $post ) {
	// Create fields
	$fields = FieldFactory::create_multiple(
		[
			'isbn'           => [
				'type'        => 'text',
				'label'       => 'ISBN',
				'description' => 'International Standard Book Number',
				'placeholder' => '978-3-16-148410-0',
				'pattern'     => '[\d\-]+',
			],
			'author'         => [
				'type'        => 'text',
				'label'       => 'Author Name',
				'description' => 'Primary author of the book',
				'required'    => true,
				'placeholder' => 'John Doe',
			],
			'co_authors'     => [
				'type'        => 'textarea',
				'label'       => 'Co-Authors',
				'description' => 'Additional authors (one per line)',
				'rows'        => 3,
			],
			'genre'          => [
				'type'        => 'select',
				'label'       => 'Genre',
				'description' => 'Primary genre classification',
				'options'     => [
					'fiction'    => 'Fiction',
					'non-fiction' => 'Non-Fiction',
					'mystery'    => 'Mystery',
					'sci-fi'     => 'Science Fiction',
					'fantasy'    => 'Fantasy',
					'biography'  => 'Biography',
					'history'    => 'History',
					'children'   => 'Children',
					'young-adult' => 'Young Adult',
				],
			],
			'pages'          => [
				'type'        => 'number',
				'label'       => 'Number of Pages',
				'description' => 'Total page count',
				'min'         => 1,
				'max'         => 10000,
				'step'        => 1,
			],
			'language'       => [
				'type'        => 'select',
				'label'       => 'Language',
				'description' => 'Primary language of the book',
				'options'     => [
					'en' => 'English',
					'es' => 'Spanish',
					'fr' => 'French',
					'de' => 'German',
					'it' => 'Italian',
					'pt' => 'Portuguese',
					'ja' => 'Japanese',
					'zh' => 'Chinese',
				],
				'default'     => 'en',
			],
			'series'         => [
				'type'        => 'text',
				'label'       => 'Series Name',
				'description' => 'If part of a series',
				'placeholder' => 'e.g., Harry Potter',
			],
			'series_number'  => [
				'type'        => 'number',
				'label'       => 'Series Number',
				'description' => 'Position in the series',
				'min'         => 1,
				'max'         => 100,
			],
		]
	);

	// Output nonce field
	wp_nonce_field( 'save_book_meta', 'book_meta_nonce' );

	// Render each field
	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $field ) {
		$field_name = $field->get_name();
		$value      = get_post_meta( $post->ID, '_book_' . $field_name, true );

		echo '<tr>';
		echo '<th scope="row">' . esc_html( $field->get_label() ) . '</th>';
		echo '<td>';
		// Replace the field name with meta key for proper form submission
		$html = $field->render( $value );
		$html = str_replace( 'name="' . $field_name . '"', 'name="book_meta[' . $field_name . ']"', $html );
		echo $html;
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

/**
 * Register Pricing metabox (sidebar)
 */
function register_pricing_metabox() {
	add_meta_box(
		'book_pricing',
		'Pricing & Availability',
		function ( $post ) {
			render_pricing_fields( $post );
		},
		'book',
		'side',
		'default'
	);
}

/**
 * Render pricing fields
 */
function render_pricing_fields( $post ) {
	$fields = FieldFactory::create_multiple(
		[
			'price'       => [
				'type'        => 'number',
				'label'       => 'Price ($)',
				'description' => 'Regular price',
				'min'         => 0,
				'step'        => 0.01,
				'placeholder' => '29.99',
			],
			'sale_price'  => [
				'type'        => 'number',
				'label'       => 'Sale Price ($)',
				'description' => 'Sale price (if on sale)',
				'min'         => 0,
				'step'        => 0.01,
			],
			'in_stock'    => [
				'type'        => 'checkbox',
				'label'       => 'In Stock',
				'description' => 'Check if currently available',
			],
			'stock_count' => [
				'type'        => 'number',
				'label'       => 'Stock Count',
				'description' => 'Number of copies available',
				'min'         => 0,
			],
		]
	);

	foreach ( $fields as $field ) {
		$field_name = $field->get_name();
		$value      = get_post_meta( $post->ID, '_book_' . $field_name, true );

		echo '<div style="margin-bottom: 15px;">';
		echo '<strong>' . esc_html( $field->get_label() ) . '</strong><br>';
		$html = $field->render( $value );
		$html = str_replace( 'name="' . $field_name . '"', 'name="book_meta[' . $field_name . ']"', $html );
		echo $html;
		echo '</div>';
	}
}

/**
 * Register Publication metabox (normal context)
 */
function register_publication_metabox() {
	add_meta_box(
		'book_publication',
		'Publication Information',
		function ( $post ) {
			render_publication_fields( $post );
		},
		'book',
		'normal',
		'default'
	);
}

/**
 * Render publication fields
 */
function render_publication_fields( $post ) {
	$fields = FieldFactory::create_multiple(
		[
			'publisher'        => [
				'type'        => 'text',
				'label'       => 'Publisher',
				'description' => 'Publishing company name',
				'placeholder' => 'Penguin Random House',
			],
			'publication_date' => [
				'type'        => 'date',
				'label'       => 'Publication Date',
				'description' => 'Original publication date',
			],
			'edition'          => [
				'type'        => 'text',
				'label'       => 'Edition',
				'description' => 'e.g., 1st Edition, Revised',
				'placeholder' => '1st Edition',
			],
			'format'           => [
				'type'        => 'checkbox',
				'label'       => 'Available Formats',
				'description' => 'Select all available formats',
				'options'     => [
					'hardcover'  => 'Hardcover',
					'paperback'  => 'Paperback',
					'ebook'      => 'E-Book',
					'audiobook'  => 'Audiobook',
				],
				'layout'      => 'stacked',
			],
			'awards'           => [
				'type'        => 'textarea',
				'label'       => 'Awards & Recognition',
				'description' => 'List any awards or recognition',
				'rows'        => 3,
				'placeholder' => 'e.g., Pulitzer Prize, Best Seller',
			],
		]
	);

	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $field ) {
		$field_name = $field->get_name();
		$value      = get_post_meta( $post->ID, '_book_' . $field_name, true );

		echo '<tr>';
		echo '<th scope="row">' . esc_html( $field->get_label() ) . '</th>';
		echo '<td>';
		$html = $field->render( $value );
		$html = str_replace( 'name="' . $field_name . '"', 'name="book_meta[' . $field_name . ']"', $html );
		echo $html;
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

/**
 * Register Additional Info metabox (advanced context)
 */
function register_additional_info_metabox() {
	add_meta_box(
		'book_additional',
		'Additional Information',
		function ( $post ) {
			render_additional_fields( $post );
		},
		'book',
		'advanced',
		'low'
	);
}

/**
 * Render additional information fields
 */
function render_additional_fields( $post ) {
	$fields = FieldFactory::create_multiple(
		[
			'target_audience' => [
				'type'        => 'radio',
				'label'       => 'Target Audience',
				'description' => 'Primary target age group',
				'options'     => [
					'children'    => 'Children (0-12)',
					'teen'        => 'Teen (13-17)',
					'young-adult' => 'Young Adult (18-24)',
					'adult'       => 'Adult (25+)',
					'all-ages'    => 'All Ages',
				],
				'layout'      => 'stacked',
			],
			'content_rating'  => [
				'type'        => 'select',
				'label'       => 'Content Rating',
				'description' => 'Age appropriateness rating',
				'options'     => [
					'g'     => 'G - General Audiences',
					'pg'    => 'PG - Parental Guidance',
					'pg-13' => 'PG-13 - Parents Strongly Cautioned',
					'r'     => 'R - Restricted',
				],
			],
			'featured'        => [
				'type'        => 'checkbox',
				'label'       => 'Featured Book',
				'description' => 'Display prominently on site',
			],
			'bestseller'      => [
				'type'        => 'checkbox',
				'label'       => 'Bestseller',
				'description' => 'Mark as bestseller',
			],
			'keywords'        => [
				'type'        => 'text',
				'label'       => 'Keywords',
				'description' => 'Search keywords (comma-separated)',
				'placeholder' => 'fiction, mystery, thriller',
			],
			'website'         => [
				'type'        => 'url',
				'label'       => 'Book Website',
				'description' => 'Official website for this book',
				'placeholder' => 'https://example.com',
			],
		]
	);

	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $field ) {
		$field_name = $field->get_name();
		$value      = get_post_meta( $post->ID, '_book_' . $field_name, true );

		echo '<tr>';
		echo '<th scope="row">' . esc_html( $field->get_label() ) . '</th>';
		echo '<td>';
		$html = $field->render( $value );
		$html = str_replace( 'name="' . $field_name . '"', 'name="book_meta[' . $field_name . ']"', $html );
		echo $html;
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

/**
 * Save book metadata
 */
function save_book_metadata( $post_id ) {
	if ( ! isset( $_POST['book_meta'] ) || ! is_array( $_POST['book_meta'] ) ) {
		return;
	}

	// Create all fields to use their sanitization
	$all_field_configs = array_merge(
		get_book_details_fields_config(),
		get_pricing_fields_config(),
		get_publication_fields_config(),
		get_additional_fields_config()
	);

	$fields = FieldFactory::create_multiple( $all_field_configs );

	// Save each field
	foreach ( $_POST['book_meta'] as $field_name => $value ) {
		if ( ! isset( $fields[ $field_name ] ) ) {
			continue;
		}

		$field = $fields[ $field_name ];

		// Sanitize the value
		$sanitized_value = $field->sanitize( $value );

		// Validate the value
		$validation = $field->validate( $sanitized_value );

		// Save if valid
		if ( $validation['valid'] ) {
			update_post_meta( $post_id, '_book_' . $field_name, $sanitized_value );
		}
	}
}

/**
 * Get book details field configurations
 */
function get_book_details_fields_config() {
	return [
		'isbn'          => [ 'type' => 'text' ],
		'author'        => [ 'type' => 'text', 'required' => true ],
		'co_authors'    => [ 'type' => 'textarea' ],
		'genre'         => [ 'type' => 'select' ],
		'pages'         => [ 'type' => 'number' ],
		'language'      => [ 'type' => 'select' ],
		'series'        => [ 'type' => 'text' ],
		'series_number' => [ 'type' => 'number' ],
	];
}

/**
 * Get pricing field configurations
 */
function get_pricing_fields_config() {
	return [
		'price'       => [ 'type' => 'number' ],
		'sale_price'  => [ 'type' => 'number' ],
		'in_stock'    => [ 'type' => 'checkbox' ],
		'stock_count' => [ 'type' => 'number' ],
	];
}

/**
 * Get publication field configurations
 */
function get_publication_fields_config() {
	return [
		'publisher'        => [ 'type' => 'text' ],
		'publication_date' => [ 'type' => 'date' ],
		'edition'          => [ 'type' => 'text' ],
		'format'           => [ 'type' => 'checkbox' ],
		'awards'           => [ 'type' => 'textarea' ],
	];
}

/**
 * Get additional information field configurations
 */
function get_additional_fields_config() {
	return [
		'target_audience' => [ 'type' => 'radio' ],
		'content_rating'  => [ 'type' => 'select' ],
		'featured'        => [ 'type' => 'checkbox' ],
		'bestseller'      => [ 'type' => 'checkbox' ],
		'keywords'        => [ 'type' => 'text' ],
		'website'         => [ 'type' => 'url' ],
	];
}

/**
 * Display book metadata in the frontend
 * Usage: add this to your theme's single-book.php template
 */
function display_book_metadata( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Get all metadata
	$author = get_post_meta( $post_id, '_book_author', true );
	$isbn   = get_post_meta( $post_id, '_book_isbn', true );
	$price  = get_post_meta( $post_id, '_book_price', true );
	$genre  = get_post_meta( $post_id, '_book_genre', true );
	$pages  = get_post_meta( $post_id, '_book_pages', true );

	?>
	<div class="book-metadata">
		<?php if ( $author ) : ?>
			<p><strong>Author:</strong> <?php echo esc_html( $author ); ?></p>
		<?php endif; ?>
		
		<?php if ( $isbn ) : ?>
			<p><strong>ISBN:</strong> <?php echo esc_html( $isbn ); ?></p>
		<?php endif; ?>
		
		<?php if ( $price ) : ?>
			<p><strong>Price:</strong> $<?php echo esc_html( number_format( $price, 2 ) ); ?></p>
		<?php endif; ?>
		
		<?php if ( $genre ) : ?>
			<p><strong>Genre:</strong> <?php echo esc_html( ucfirst( $genre ) ); ?></p>
		<?php endif; ?>
		
		<?php if ( $pages ) : ?>
			<p><strong>Pages:</strong> <?php echo esc_html( $pages ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
