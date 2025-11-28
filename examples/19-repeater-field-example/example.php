<?php
/**
 * Example 19: Repeater Field Example - CPT Usage
 *
 * This example demonstrates the RepeaterField container type
 * for creating repeatable sets of fields within Custom Post Types.
 *
 * @package    Pedalcms\WpCmf
 * @subpackage Examples
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use Pedalcms\WpCmf\Core\Manager;

/**
 * Initialize the Repeater Field CPT Example
 */
function wp_cmf_repeater_cpt_example_init(): void {
	// Ensure WP-CMF is loaded.
	if ( ! class_exists( Manager::class ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>WP-CMF is required for the Repeater Field Example plugin.</p></div>';
			}
		);
		return;
	}

	$manager = Manager::init();

	// Register configuration using array format.
	$manager->register_from_array( wp_cmf_repeater_cpt_get_config() );
}
add_action( 'plugins_loaded', 'wp_cmf_repeater_cpt_example_init' );

/**
 * Get the configuration for this example
 *
 * @return array<string, mixed> Configuration array.
 */
function wp_cmf_repeater_cpt_get_config(): array {
	return array(
		'cpts' => array(
			// =====================================================
			// CPT 1: Team - Using repeaters for team member info
			// =====================================================
			array(
				'id'   => 'team',
				'args' => array(
					'label'        => 'Teams',
					'labels'       => array(
						'name'               => 'Teams',
						'singular_name'      => 'Team',
						'add_new'            => 'Add New Team',
						'add_new_item'       => 'Add New Team',
						'edit_item'          => 'Edit Team',
						'new_item'           => 'New Team',
						'view_item'          => 'View Team',
						'search_items'       => 'Search Teams',
						'not_found'          => 'No teams found',
						'not_found_in_trash' => 'No teams found in trash',
					),
					'public'       => true,
					'has_archive'  => true,
					'supports'     => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'    => 'dashicons-groups',
					'show_in_rest' => true,
				),
				'fields' => array(
					// Team Members Repeater
					array(
						'name'         => 'team_members',
						'type'         => 'repeater',
						'label'        => 'Team Members',
						'description'  => 'Add team members to this team. Drag to reorder.',
						'context'      => 'normal',
						'priority'     => 'high',
						'min_rows'     => 1,
						'max_rows'     => 50,
						'button_label' => 'Add Team Member',
						'row_label'    => 'Team Member',
						'collapsible'  => true,
						'collapsed'    => false,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'        => 'name',
								'type'        => 'text',
								'label'       => 'Full Name',
								'placeholder' => 'Enter full name',
							),
							array(
								'name'        => 'title',
								'type'        => 'text',
								'label'       => 'Job Title',
								'placeholder' => 'e.g., Senior Developer',
							),
							array(
								'name'    => 'department',
								'type'    => 'select',
								'label'   => 'Department',
								'options' => array(
									''            => '-- Select Department --',
									'engineering' => 'Engineering',
									'design'      => 'Design',
									'marketing'   => 'Marketing',
									'sales'       => 'Sales',
									'hr'          => 'Human Resources',
									'operations'  => 'Operations',
								),
							),
							array(
								'name'  => 'bio',
								'type'  => 'textarea',
								'label' => 'Biography',
								'rows'  => 3,
							),
							array(
								'name'        => 'email',
								'type'        => 'email',
								'label'       => 'Email Address',
								'placeholder' => 'email@example.com',
							),
							array(
								'name'        => 'phone',
								'type'        => 'text',
								'label'       => 'Phone Number',
								'placeholder' => '+1 (555) 123-4567',
							),
							array(
								'name'    => 'is_lead',
								'type'    => 'checkbox',
								'label'   => 'Team Lead',
								'options' => array(
									'yes' => 'This person is a team lead',
								),
							),
						),
					),

					// Achievements Repeater (simpler example)
					array(
						'name'         => 'team_achievements',
						'type'         => 'repeater',
						'label'        => 'Team Achievements',
						'description'  => 'Notable achievements for this team.',
						'context'      => 'normal',
						'priority'     => 'default',
						'button_label' => 'Add Achievement',
						'row_label'    => 'Achievement',
						'collapsible'  => true,
						'collapsed'    => true,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'     => 'achievement_title',
								'type'     => 'text',
								'label'    => 'Achievement',
								'required' => true,
							),
							array(
								'name'  => 'achievement_date',
								'type'  => 'date',
								'label' => 'Date Achieved',
							),
							array(
								'name'  => 'achievement_description',
								'type'  => 'textarea',
								'label' => 'Description',
								'rows'  => 2,
							),
						),
					),
				),
			),

			// =====================================================
			// CPT 2: Event - Using repeaters for schedule/speakers
			// =====================================================
			array(
				'id'   => 'event',
				'args' => array(
					'label'        => 'Events',
					'labels'       => array(
						'name'               => 'Events',
						'singular_name'      => 'Event',
						'add_new'            => 'Add New Event',
						'add_new_item'       => 'Add New Event',
						'edit_item'          => 'Edit Event',
						'new_item'           => 'New Event',
						'view_item'          => 'View Event',
						'search_items'       => 'Search Events',
						'not_found'          => 'No events found',
						'not_found_in_trash' => 'No events found in trash',
					),
					'public'       => true,
					'has_archive'  => true,
					'supports'     => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'    => 'dashicons-calendar-alt',
					'show_in_rest' => true,
				),
				'fields' => array(
					// Event Details (regular fields)
					array(
						'name'     => 'event_date',
						'type'     => 'date',
						'label'    => 'Event Date',
						'context'  => 'side',
						'priority' => 'high',
						'required' => true,
					),
					array(
						'name'    => 'event_location',
						'type'    => 'text',
						'label'   => 'Location',
						'context' => 'side',
					),

					// Schedule Repeater
					array(
						'name'         => 'event_schedule',
						'type'         => 'repeater',
						'label'        => 'Event Schedule',
						'description'  => 'Add schedule items for this event.',
						'context'      => 'normal',
						'priority'     => 'high',
						'min_rows'     => 0,
						'max_rows'     => 100,
						'button_label' => 'Add Schedule Item',
						'row_label'    => 'Session',
						'collapsible'  => true,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'        => 'start_time',
								'type'        => 'text',
								'label'       => 'Start Time',
								'placeholder' => '9:00 AM',
								'required'    => true,
							),
							array(
								'name'        => 'end_time',
								'type'        => 'text',
								'label'       => 'End Time',
								'placeholder' => '10:00 AM',
							),
							array(
								'name'     => 'session_title',
								'type'     => 'text',
								'label'    => 'Session Title',
								'required' => true,
							),
							array(
								'name'  => 'session_description',
								'type'  => 'textarea',
								'label' => 'Description',
								'rows'  => 2,
							),
							array(
								'name'        => 'speaker',
								'type'        => 'text',
								'label'       => 'Speaker',
								'placeholder' => 'Speaker name',
							),
							array(
								'name'    => 'session_type',
								'type'    => 'select',
								'label'   => 'Session Type',
								'options' => array(
									'presentation' => 'Presentation',
									'workshop'     => 'Workshop',
									'panel'        => 'Panel Discussion',
									'break'        => 'Break',
									'networking'   => 'Networking',
									'keynote'      => 'Keynote',
								),
							),
							array(
								'name'  => 'room',
								'type'  => 'text',
								'label' => 'Room/Location',
							),
						),
					),

					// Speakers Repeater
					array(
						'name'         => 'event_speakers',
						'type'         => 'repeater',
						'label'        => 'Featured Speakers',
						'description'  => 'Add featured speakers for this event.',
						'context'      => 'normal',
						'priority'     => 'default',
						'button_label' => 'Add Speaker',
						'row_label'    => 'Speaker',
						'collapsible'  => true,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'     => 'speaker_name',
								'type'     => 'text',
								'label'    => 'Name',
								'required' => true,
							),
							array(
								'name'  => 'speaker_title',
								'type'  => 'text',
								'label' => 'Title/Company',
							),
							array(
								'name'  => 'speaker_bio',
								'type'  => 'textarea',
								'label' => 'Biography',
								'rows'  => 3,
							),
							array(
								'name'  => 'speaker_website',
								'type'  => 'url',
								'label' => 'Website',
							),
							array(
								'name'        => 'speaker_twitter',
								'type'        => 'text',
								'label'       => 'Twitter Handle',
								'placeholder' => '@username',
							),
						),
					),

					// Sponsors Repeater
					array(
						'name'         => 'event_sponsors',
						'type'         => 'repeater',
						'label'        => 'Event Sponsors',
						'description'  => 'Add sponsors for this event.',
						'context'      => 'normal',
						'priority'     => 'low',
						'button_label' => 'Add Sponsor',
						'row_label'    => 'Sponsor',
						'collapsible'  => true,
						'collapsed'    => true,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'     => 'sponsor_name',
								'type'     => 'text',
								'label'    => 'Sponsor Name',
								'required' => true,
							),
							array(
								'name'    => 'sponsor_level',
								'type'    => 'select',
								'label'   => 'Sponsorship Level',
								'options' => array(
									'platinum' => 'Platinum',
									'gold'     => 'Gold',
									'silver'   => 'Silver',
									'bronze'   => 'Bronze',
									'partner'  => 'Partner',
								),
							),
							array(
								'name'  => 'sponsor_url',
								'type'  => 'url',
								'label' => 'Website URL',
							),
						),
					),
				),
			),

			// =====================================================
			// CPT 3: Product - Gallery and specs repeaters
			// =====================================================
			array(
				'id'   => 'product_item',
				'args' => array(
					'label'        => 'Products',
					'labels'       => array(
						'name'          => 'Products',
						'singular_name' => 'Product',
					),
					'public'       => true,
					'has_archive'  => true,
					'supports'     => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'    => 'dashicons-cart',
					'show_in_rest' => true,
				),
				'fields' => array(
					// Price info
					array(
						'name'     => 'product_price',
						'type'     => 'number',
						'label'    => 'Price ($)',
						'context'  => 'side',
						'priority' => 'high',
						'min'      => 0,
						'step'     => 0.01,
					),

					// Specifications Repeater
					array(
						'name'         => 'product_specs',
						'type'         => 'repeater',
						'label'        => 'Product Specifications',
						'description'  => 'Add technical specifications.',
						'context'      => 'normal',
						'priority'     => 'high',
						'button_label' => 'Add Specification',
						'row_label'    => 'Spec',
						'collapsible'  => false,
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'        => 'spec_name',
								'type'        => 'text',
								'label'       => 'Specification',
								'placeholder' => 'e.g., Weight, Dimensions, Material',
								'required'    => true,
							),
							array(
								'name'        => 'spec_value',
								'type'        => 'text',
								'label'       => 'Value',
								'placeholder' => 'e.g., 2.5 kg, 10x20x5 cm, Aluminum',
								'required'    => true,
							),
						),
					),

					// Features Repeater
					array(
						'name'         => 'product_features',
						'type'         => 'repeater',
						'label'        => 'Key Features',
						'description'  => 'List the key features of this product.',
						'context'      => 'normal',
						'priority'     => 'default',
						'button_label' => 'Add Feature',
						'row_label'    => 'Feature',
						'sortable'     => true,
						'fields'       => array(
							array(
								'name'     => 'feature_title',
								'type'     => 'text',
								'label'    => 'Feature',
								'required' => true,
							),
							array(
								'name'  => 'feature_description',
								'type'  => 'textarea',
								'label' => 'Description',
								'rows'  => 2,
							),
						),
					),

					// Related Products (minimal repeater)
					array(
						'name'         => 'related_products',
						'type'         => 'repeater',
						'label'        => 'Related Products',
						'context'      => 'side',
						'priority'     => 'low',
						'max_rows'     => 5,
						'button_label' => 'Add Related',
						'row_label'    => 'Product',
						'collapsible'  => false,
						'sortable'     => false,
						'fields'       => array(
							array(
								'name'        => 'related_product_id',
								'type'        => 'number',
								'label'       => 'Product ID',
								'placeholder' => 'Enter product post ID',
							),
						),
					),
				),
			),
		),
	);
}

/**
 * Helper function to display team members on the frontend
 *
 * @param int $post_id The post ID.
 * @return void
 */
function wp_cmf_display_team_members( int $post_id ): void {
	$team_members = get_post_meta( $post_id, 'team_members', true );

	if ( empty( $team_members ) || ! is_array( $team_members ) ) {
		return;
	}

	echo '<div class="team-members-grid">';
	foreach ( $team_members as $member ) {
		echo '<div class="team-member">';

		if ( ! empty( $member['name'] ) ) {
			echo '<h3 class="member-name">' . esc_html( $member['name'] ) . '</h3>';
		}

		if ( ! empty( $member['title'] ) ) {
			echo '<p class="member-title">' . esc_html( $member['title'] ) . '</p>';
		}

		if ( ! empty( $member['department'] ) ) {
			echo '<span class="member-department">' . esc_html( ucfirst( $member['department'] ) ) . '</span>';
		}

		if ( ! empty( $member['bio'] ) ) {
			echo '<p class="member-bio">' . esc_html( $member['bio'] ) . '</p>';
		}

		if ( ! empty( $member['email'] ) ) {
			echo '<p class="member-email"><a href="mailto:' . esc_attr( $member['email'] ) . '">' . esc_html( $member['email'] ) . '</a></p>';
		}

		if ( ! empty( $member['is_lead'] ) && in_array( 'yes', (array) $member['is_lead'], true ) ) {
			echo '<span class="team-lead-badge">Team Lead</span>';
		}

		echo '</div>';
	}
	echo '</div>';
}

/**
 * Helper function to display event schedule on the frontend
 *
 * @param int $post_id The post ID.
 * @return void
 */
function wp_cmf_display_event_schedule( int $post_id ): void {
	$schedule = get_post_meta( $post_id, 'event_schedule', true );

	if ( empty( $schedule ) || ! is_array( $schedule ) ) {
		return;
	}

	echo '<div class="event-schedule">';
	echo '<h3>Schedule</h3>';
	echo '<table class="schedule-table">';
	echo '<thead><tr><th>Time</th><th>Session</th><th>Speaker</th><th>Room</th></tr></thead>';
	echo '<tbody>';

	foreach ( $schedule as $session ) {
		echo '<tr>';

		// Time
		echo '<td class="time">';
		if ( ! empty( $session['start_time'] ) ) {
			echo esc_html( $session['start_time'] );
			if ( ! empty( $session['end_time'] ) ) {
				echo ' - ' . esc_html( $session['end_time'] );
			}
		}
		echo '</td>';

		// Session
		echo '<td class="session">';
		if ( ! empty( $session['session_title'] ) ) {
			echo '<strong>' . esc_html( $session['session_title'] ) . '</strong>';
		}
		if ( ! empty( $session['session_description'] ) ) {
			echo '<p>' . esc_html( $session['session_description'] ) . '</p>';
		}
		if ( ! empty( $session['session_type'] ) ) {
			echo '<span class="session-type type-' . esc_attr( $session['session_type'] ) . '">' . esc_html( ucfirst( $session['session_type'] ) ) . '</span>';
		}
		echo '</td>';

		// Speaker
		echo '<td class="speaker">' . esc_html( $session['speaker'] ?? '' ) . '</td>';

		// Room
		echo '<td class="room">' . esc_html( $session['room'] ?? '' ) . '</td>';

		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}
