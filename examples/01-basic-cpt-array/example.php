<?php
/**
 * Plugin Name: Basic CPT with Array Configuration
 * Plugin URI: https://github.com/pedalcms/wp-cmf
 * Description: Simple example of registering a Custom Post Type with fields using array-based configuration
 * Version: 1.0.0
 * Author: PedalCMS
 * Author URI: https://github.com/pedalcms
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: basic-cpt-array
 *
 * @package BasicCptArray
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloader (adjust path as needed).
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize the plugin and register the Custom Post Type with fields.
 */
function basic_cpt_array_init() {
    // Configuration array for a simple Book CPT
    $config = [
        'cpts' => [
            [
                // CPT identifier (lowercase, max 20 characters)
                'id' => 'book',

                // WordPress CPT registration arguments
                'args' => [
                    'label' => 'Books',
                    'labels' => [
                        'name' => 'Books',
                        'singular_name' => 'Book',
                        'add_new' => 'Add New Book',
                        'add_new_item' => 'Add New Book',
                        'edit_item' => 'Edit Book',
                        'new_item' => 'New Book',
                        'view_item' => 'View Book',
                        'search_items' => 'Search Books',
                        'not_found' => 'No books found',
                        'not_found_in_trash' => 'No books found in trash',
                    ],
                    'public' => true,
                    'menu_icon' => 'dashicons-book',
                    'supports' => ['title', 'editor', 'thumbnail'],
                    'has_archive' => true,
                    'show_in_rest' => true, // Enable Gutenberg editor
                ],

                // Fields to display in the book edit screen
                'fields' => [
                    [
                        'name' => 'isbn',
                        'type' => 'text',
                        'label' => 'ISBN',
                        'description' => 'International Standard Book Number (e.g., 978-3-16-148410-0)',
                        'placeholder' => '978-3-16-148410-0',
                        'required' => true,
                        'validation' => [
                            'pattern' => '/^[0-9-]+$/',
                            'min_length' => 10,
                            'max_length' => 17,
                        ],
                    ],
                    [
                        'name' => 'author',
                        'type' => 'text',
                        'label' => 'Author',
                        'description' => 'Primary author of the book',
                        'placeholder' => 'Jane Doe',
                        'required' => true,
                    ],
                    [
                        'name' => 'publication_year',
                        'type' => 'number',
                        'label' => 'Publication Year',
                        'description' => 'Year the book was published',
                        'min' => 1900,
                        'max' => 2100,
                        'default' => 2024,
                    ],
                    [
                        'name' => 'genre',
                        'type' => 'select',
                        'label' => 'Genre',
                        'description' => 'Book genre or category',
                        'options' => [
                            'fiction' => 'Fiction',
                            'non_fiction' => 'Non-Fiction',
                            'mystery' => 'Mystery',
                            'sci_fi' => 'Science Fiction',
                            'fantasy' => 'Fantasy',
                            'biography' => 'Biography',
                            'history' => 'History',
                            'self_help' => 'Self-Help',
                        ],
                        'default' => 'fiction',
                    ],
                    [
                        'name' => 'in_stock',
                        'type' => 'checkbox',
                        'label' => 'In Stock',
                        'description' => 'Check if the book is currently available',
                        'default' => true,
                    ],
                ],
            ],
        ],
    ];

    // Register the configuration with WP-CMF
    Manager::init()->register_from_array($config);
}

// Hook into WordPress init action
add_action('init', 'basic_cpt_array_init');

/**
 * Flush rewrite rules on plugin activation.
 */
function basic_cpt_array_activate() {
    basic_cpt_array_init();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'basic_cpt_array_activate');

/**
 * Flush rewrite rules on plugin deactivation.
 */
function basic_cpt_array_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'basic_cpt_array_deactivate');
