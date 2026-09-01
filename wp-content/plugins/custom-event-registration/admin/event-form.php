<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ) );
}

global $wpdb;

$default_event = array(
	'event_id' => 0,
	'title' => '',
	'slug' => '',
	'meta_title' => '',
	'meta_description' => '',
	'meta_keywords' => '',
	'target_audience' => '',
	'hero_badge_text' => '',
	'hero_convened_by' => '',
	'hero_features' => '',
	'event_info_heading' => '',
	'event_info_paragraph_1' => '',
	'event_info_paragraph_2' => '',
	'target_audience_heading' => '',
	'registration_heading' => '',
	'registration_intro' => '',
	'sponsorship_heading' => '',
	'sponsorship_intro' => '',
	'speakers_heading' => '',
	'pillars_heading' => '',
	'pillars_intro' => '',
	'summary_heading' => '',
	'summary_note' => '',
	'primary_cta_text' => '',
	'secondary_cta_text' => '',
	'description' => '',
	'event_date' => '',
	'event_end_date' => '',
	'venue' => '',
	'status' => 'draft',
	'show_event_information' => 1,
	'show_speakers' => 1,
	'show_sponsors' => 1,
	'show_pillars' => 1,
	'max_attendees' => '',
	'featured_image_id' => '',
	'uuid' => '',
);

$event_id = isset( $_GET['event_id'] ) ? absint( wp_unslash( $_GET['event_id'] ) ) : 0;
$event = null;
$errors = array();
$ticket_rows = array();
$speaker_rows = array();
$sponsor_rows = array();
$pillar_rows = array();

if ( $event_id ) {
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_events WHERE id = %d", $event_id ) );
	if ( ! $event ) {
		wp_die( esc_html__( 'Event not found.', 'custom-event-registration' ) );
	}

	$default_event['event_id'] = $event_id;
	$default_event['title'] = $event->name;
	$default_event['slug'] = $event->slug;
	$default_event['meta_title'] = isset( $event->meta_title ) ? $event->meta_title : '';
	$default_event['meta_description'] = isset( $event->meta_description ) ? $event->meta_description : '';
	$default_event['meta_keywords'] = isset( $event->meta_keywords ) ? $event->meta_keywords : '';
	$default_event['target_audience'] = isset( $event->target_audience ) ? $event->target_audience : '';
	$default_event['hero_badge_text'] = isset( $event->hero_badge_text ) ? $event->hero_badge_text : '';
	$default_event['hero_convened_by'] = isset( $event->hero_convened_by ) ? $event->hero_convened_by : '';
	$default_event['hero_features'] = isset( $event->hero_features ) ? $event->hero_features : '';
	$default_event['event_info_heading'] = isset( $event->event_info_heading ) ? $event->event_info_heading : '';
	$default_event['event_info_paragraph_1'] = isset( $event->event_info_paragraph_1 ) ? $event->event_info_paragraph_1 : '';
	$default_event['event_info_paragraph_2'] = isset( $event->event_info_paragraph_2 ) ? $event->event_info_paragraph_2 : '';
	$default_event['target_audience_heading'] = isset( $event->target_audience_heading ) ? $event->target_audience_heading : '';
	$default_event['registration_heading'] = isset( $event->registration_heading ) ? $event->registration_heading : '';
	$default_event['registration_intro'] = isset( $event->registration_intro ) ? $event->registration_intro : '';
	$default_event['sponsorship_heading'] = isset( $event->sponsorship_heading ) ? $event->sponsorship_heading : '';
	$default_event['sponsorship_intro'] = isset( $event->sponsorship_intro ) ? $event->sponsorship_intro : '';
	$default_event['speakers_heading'] = isset( $event->speakers_heading ) ? $event->speakers_heading : '';
	$default_event['pillars_heading'] = isset( $event->pillars_heading ) ? $event->pillars_heading : '';
	$default_event['pillars_intro'] = isset( $event->pillars_intro ) ? $event->pillars_intro : '';
	$default_event['summary_heading'] = isset( $event->summary_heading ) ? $event->summary_heading : '';
	$default_event['summary_note'] = isset( $event->summary_note ) ? $event->summary_note : '';
	$default_event['primary_cta_text'] = isset( $event->primary_cta_text ) ? $event->primary_cta_text : '';
	$default_event['secondary_cta_text'] = isset( $event->secondary_cta_text ) ? $event->secondary_cta_text : '';
	$default_event['description'] = $event->description;
	$default_event['event_date'] = $event->event_date;
	$default_event['event_end_date'] = $event->event_end_date;
	$default_event['venue'] = $event->venue;
	$default_event['status'] = $event->status;
	$default_event['show_event_information'] = isset( $event->show_event_information ) ? (int) $event->show_event_information : 1;
	$default_event['show_speakers'] = isset( $event->show_speakers ) ? (int) $event->show_speakers : 1;
	$default_event['show_sponsors'] = isset( $event->show_sponsors ) ? (int) $event->show_sponsors : 1;
	$default_event['show_pillars'] = isset( $event->show_pillars ) ? (int) $event->show_pillars : 1;
	$default_event['max_attendees'] = $event->max_attendees;
	$default_event['featured_image_id'] = $event->featured_image_id;
	$default_event['uuid'] = $event->event_uuid;

	$ticket_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_ticket_types WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$speaker_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_speakers WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$sponsor_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_sponsorships WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$pillar_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_pillars WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
}

if ( isset( $_POST['cer_event_save'] ) && check_admin_referer( 'cer_event_form', 'cer_event_nonce' ) ) {
	$event_id = absint( $_POST['event_id'] ?? 0 );
	$default_event['event_id'] = $event_id;
	$default_event['title'] = sanitize_text_field( wp_unslash( $_POST['event_title'] ?? '' ) );
	$submitted_slug = isset( $_POST['event_slug'] ) ? sanitize_title( wp_unslash( $_POST['event_slug'] ) ) : '';
	$default_event['slug'] = $submitted_slug ? $submitted_slug : cer_generate_unique_event_slug( $default_event['title'], $event_id );
	$default_event['meta_title'] = sanitize_text_field( wp_unslash( $_POST['meta_title'] ?? '' ) );
	$default_event['meta_description'] = sanitize_textarea_field( wp_unslash( $_POST['meta_description'] ?? '' ) );
	$default_event['meta_keywords'] = sanitize_text_field( wp_unslash( $_POST['meta_keywords'] ?? '' ) );
	$default_event['hero_badge_text'] = sanitize_text_field( wp_unslash( $_POST['hero_badge_text'] ?? '' ) );
	$default_event['hero_convened_by'] = sanitize_text_field( wp_unslash( $_POST['hero_convened_by'] ?? '' ) );
	$default_event['hero_features'] = sanitize_textarea_field( wp_unslash( $_POST['hero_features'] ?? '' ) );
	$default_event['event_info_heading'] = sanitize_text_field( wp_unslash( $_POST['event_info_heading'] ?? '' ) );
	$default_event['event_info_paragraph_1'] = wp_kses_post( wp_unslash( $_POST['event_info_paragraph_1'] ?? '' ) );
	$default_event['event_info_paragraph_2'] = wp_kses_post( wp_unslash( $_POST['event_info_paragraph_2'] ?? '' ) );
	$default_event['target_audience_heading'] = sanitize_text_field( wp_unslash( $_POST['target_audience_heading'] ?? '' ) );
	$default_event['registration_heading'] = sanitize_text_field( wp_unslash( $_POST['registration_heading'] ?? '' ) );
	$default_event['registration_intro'] = wp_kses_post( wp_unslash( $_POST['registration_intro'] ?? '' ) );
	$default_event['sponsorship_heading'] = sanitize_text_field( wp_unslash( $_POST['sponsorship_heading'] ?? '' ) );
	$default_event['sponsorship_intro'] = wp_kses_post( wp_unslash( $_POST['sponsorship_intro'] ?? '' ) );
	$default_event['speakers_heading'] = sanitize_text_field( wp_unslash( $_POST['speakers_heading'] ?? '' ) );
	$default_event['pillars_heading'] = sanitize_text_field( wp_unslash( $_POST['pillars_heading'] ?? '' ) );
	$default_event['pillars_intro'] = wp_kses_post( wp_unslash( $_POST['pillars_intro'] ?? '' ) );
	$default_event['summary_heading'] = sanitize_text_field( wp_unslash( $_POST['summary_heading'] ?? '' ) );
	$default_event['summary_note'] = wp_kses_post( wp_unslash( $_POST['summary_note'] ?? '' ) );
	$default_event['primary_cta_text'] = sanitize_text_field( wp_unslash( $_POST['primary_cta_text'] ?? '' ) );
	$default_event['secondary_cta_text'] = sanitize_text_field( wp_unslash( $_POST['secondary_cta_text'] ?? '' ) );
	$default_event['description'] = wp_kses_post( wp_unslash( $_POST['event_description'] ?? '' ) );
	$default_event['target_audience'] = wp_kses_post( wp_unslash( $_POST['target_audience'] ?? '' ) );
	$default_event['event_date'] = cer_normalize_datetime_value( wp_unslash( $_POST['event_date'] ?? '' ) );
	$default_event['event_end_date'] = cer_normalize_datetime_value( wp_unslash( $_POST['event_end_date'] ?? '' ) );
	$default_event['venue'] = sanitize_text_field( wp_unslash( $_POST['event_venue'] ?? '' ) );
	$default_event['status'] = sanitize_key( wp_unslash( $_POST['status'] ?? 'draft' ) );
	$default_event['show_event_information'] = empty( $_POST['show_event_information'] ) ? 0 : 1;
	$default_event['show_speakers'] = empty( $_POST['show_speakers'] ) ? 0 : 1;
	$default_event['show_sponsors'] = empty( $_POST['show_sponsors'] ) ? 0 : 1;
	$default_event['show_pillars'] = empty( $_POST['show_pillars'] ) ? 0 : 1;
	$default_event['max_attendees'] = absint( wp_unslash( $_POST['max_attendees'] ?? 0 ) );
	$default_event['featured_image_id'] = absint( wp_unslash( $_POST['featured_image_id'] ?? 0 ) );

	if ( empty( $default_event['title'] ) ) {
		$errors['event_title'] = __( 'Please enter an event title.', 'custom-event-registration' );
	}
	if ( empty( $default_event['slug'] ) ) {
		$default_event['slug'] = cer_generate_unique_event_slug( $default_event['title'], $event_id );
	}
	if ( empty( $default_event['event_date'] ) ) {
		$errors['event_date'] = __( 'Please select a start date and time.', 'custom-event-registration' );
	}

	$ticket_rows = isset( $_POST['tickets'] ) && is_array( $_POST['tickets'] ) ? array_values( $_POST['tickets'] ) : array();
	$speaker_rows = isset( $_POST['speakers'] ) && is_array( $_POST['speakers'] ) ? array_values( $_POST['speakers'] ) : array();
	$sponsor_rows = isset( $_POST['sponsors'] ) && is_array( $_POST['sponsors'] ) ? array_values( $_POST['sponsors'] ) : array();
	$pillar_rows = isset( $_POST['pillars'] ) && is_array( $_POST['pillars'] ) ? array_values( $_POST['pillars'] ) : array();

	foreach ( $ticket_rows as $key => $ticket ) {
		$ticket_name = sanitize_text_field( wp_unslash( $ticket['name'] ?? '' ) );
		if ( empty( $ticket_name ) && ( ! empty( $ticket['price'] ) || ! empty( $ticket['quantity_available'] ) || ! empty( $ticket['description'] ) ) ) {
			$errors[ 'ticket_name_' . $key ] = __( 'Ticket name is required.', 'custom-event-registration' );
		}
	}
	foreach ( $speaker_rows as $key => $speaker ) {
		$speaker_name = sanitize_text_field( wp_unslash( $speaker['name'] ?? '' ) );
		if ( empty( $speaker_name ) && ( ! empty( $speaker['role'] ) || ! empty( $speaker['bio'] ) || ! empty( $speaker['photo_id'] ) ) ) {
			$errors[ 'speaker_name_' . $key ] = __( 'Speaker name is required.', 'custom-event-registration' );
		}
	}
	foreach ( $sponsor_rows as $key => $sponsor ) {
		$sponsor_name = sanitize_text_field( wp_unslash( $sponsor['name'] ?? '' ) );
		if ( empty( $sponsor_name ) && ( ! empty( $sponsor['description'] ) || ! empty( $sponsor['benefits'] ) || ! empty( $sponsor['cta_url'] ) ) ) {
			$errors[ 'sponsor_name_' . $key ] = __( 'Sponsor name is required.', 'custom-event-registration' );
		}
	}
	foreach ( $pillar_rows as $key => $pillar ) {
		$pillar_title = sanitize_text_field( wp_unslash( $pillar['title'] ?? '' ) );
		if ( empty( $pillar_title ) && ( ! empty( $pillar['description'] ) || ! empty( $pillar['icon'] ) ) ) {
			$errors[ 'pillar_title_' . $key ] = __( 'Pillar title is required.', 'custom-event-registration' );
		}
	}

	if ( empty( $errors ) ) {
		$event_data = array(
			'name' => $default_event['title'],
			'slug' => $default_event['slug'],
			'meta_title' => $default_event['meta_title'],
			'meta_description' => $default_event['meta_description'],
			'meta_keywords' => $default_event['meta_keywords'],
			'target_audience' => $default_event['target_audience'],
			'hero_badge_text' => $default_event['hero_badge_text'],
			'hero_convened_by' => $default_event['hero_convened_by'],
			'hero_features' => $default_event['hero_features'],
			'event_info_heading' => $default_event['event_info_heading'],
			'event_info_paragraph_1' => $default_event['event_info_paragraph_1'],
			'event_info_paragraph_2' => $default_event['event_info_paragraph_2'],
			'target_audience_heading' => $default_event['target_audience_heading'],
			'registration_heading' => $default_event['registration_heading'],
			'registration_intro' => $default_event['registration_intro'],
			'sponsorship_heading' => $default_event['sponsorship_heading'],
			'sponsorship_intro' => $default_event['sponsorship_intro'],
			'speakers_heading' => $default_event['speakers_heading'],
			'pillars_heading' => $default_event['pillars_heading'],
			'pillars_intro' => $default_event['pillars_intro'],
			'summary_heading' => $default_event['summary_heading'],
			'summary_note' => $default_event['summary_note'],
			'primary_cta_text' => $default_event['primary_cta_text'],
			'secondary_cta_text' => $default_event['secondary_cta_text'],
			'description' => $default_event['description'],
			'event_date' => $default_event['event_date'],
			'event_end_date' => $default_event['event_end_date'],
			'venue' => $default_event['venue'],
			'status' => $default_event['status'],
			'show_event_information' => $default_event['show_event_information'],
			'show_speakers' => $default_event['show_speakers'],
			'show_sponsors' => $default_event['show_sponsors'],
			'show_pillars' => $default_event['show_pillars'],
			'max_attendees' => $default_event['max_attendees'],
			'featured_image_id' => $default_event['featured_image_id'],
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $event_id ) {
			$event_update_formats = array();
			foreach ( array_keys( $event_data ) as $field_name ) {
				if ( in_array( $field_name, array( 'show_event_information', 'show_speakers', 'show_sponsors', 'show_pillars', 'max_attendees', 'featured_image_id' ), true ) ) {
					$event_update_formats[] = '%d';
				} else {
					$event_update_formats[] = '%s';
				}
			}

			$result = $wpdb->update(
				$wpdb->prefix . 'evt_events',
				$event_data,
				array( 'id' => $event_id ),
				$event_update_formats,
				array( '%d' )
			);
			if ( false === $result ) {
				error_log( 'CER event update failed: ' . $wpdb->last_error );
			}
		} else {
			$event_uuid = wp_generate_uuid4();
			$wpdb->insert(
				$wpdb->prefix . 'evt_events',
				array_merge(
					array(
						'event_uuid' => $event_uuid,
						'created_at' => current_time( 'mysql' ),
					),
					$event_data
				),
			);
			$event_id = (int) $wpdb->insert_id;
		}

		$deleted_ticket_ids = array_map( 'absint', (array) ( $_POST['deleted_tickets'] ?? array() ) );
		foreach ( $deleted_ticket_ids as $deleted_ticket_id ) {
			if ( $deleted_ticket_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_ticket_types', array( 'id' => $deleted_ticket_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_speaker_ids = array_map( 'absint', (array) ( $_POST['deleted_speakers'] ?? array() ) );
		foreach ( $deleted_speaker_ids as $deleted_speaker_id ) {
			if ( $deleted_speaker_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_speakers', array( 'id' => $deleted_speaker_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_sponsor_ids = array_map( 'absint', (array) ( $_POST['deleted_sponsors'] ?? array() ) );
		foreach ( $deleted_sponsor_ids as $deleted_sponsor_id ) {
			if ( $deleted_sponsor_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_sponsorships', array( 'id' => $deleted_sponsor_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_pillar_ids = array_map( 'absint', (array) ( $_POST['deleted_pillars'] ?? array() ) );
		foreach ( $deleted_pillar_ids as $deleted_pillar_id ) {
			if ( $deleted_pillar_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_pillars', array( 'id' => $deleted_pillar_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}

		$ticket_insert = array();
		foreach ( $ticket_rows as $index => $ticket ) {
			$ticket_name = sanitize_text_field( wp_unslash( $ticket['name'] ?? '' ) );
			$ticket_price = floatval( $ticket['price'] ?? 0 );
			$ticket_quantity = absint( $ticket['quantity_available'] ?? 0 );
			$ticket_description = sanitize_textarea_field( wp_unslash( $ticket['description'] ?? '' ) );
			$ticket_id = absint( $ticket['id'] ?? 0 );
			if ( $ticket_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_ticket_types WHERE id = %d AND event_id = %d", $ticket_id, $event_id ) ) ) {
				$ticket_id = 0;
			}
			if ( empty( $ticket_name ) ) {
				continue;
			}
			$ticket_data = array(
				'event_id' => $event_id,
				'name' => $ticket_name,
				'price' => $ticket_price,
				'currency' => 'KES',
				'quantity_available' => $ticket_quantity,
				'description' => $ticket_description,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $ticket_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_ticket_types', $ticket_data, array( 'id' => $ticket_id ), array( '%d', '%s', '%f', '%s', '%d', '%s', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_ticket_types', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $ticket_data ), array( '%s', '%d', '%s', '%f', '%s', '%d', '%s', '%d', '%s' ) );
			}
		}

		foreach ( $speaker_rows as $index => $speaker ) {
			$speaker_name = sanitize_text_field( wp_unslash( $speaker['name'] ?? '' ) );
			if ( empty( $speaker_name ) ) {
				continue;
			}
			$speaker_id = absint( $speaker['id'] ?? 0 );
			if ( $speaker_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_speakers WHERE id = %d AND event_id = %d", $speaker_id, $event_id ) ) ) {
				$speaker_id = 0;
			}
			$speaker_data = array(
				'event_id' => $event_id,
				'name' => $speaker_name,
				'role' => sanitize_text_field( wp_unslash( $speaker['role'] ?? '' ) ),
				'bio' => sanitize_textarea_field( wp_unslash( $speaker['bio'] ?? '' ) ),
				'photo_id' => absint( $speaker['photo_id'] ?? 0 ),
				'is_visible' => empty( $speaker['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $speaker_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_speakers', $speaker_data, array( 'id' => $speaker_id ), array( '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_speakers', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $speaker_data ), array( '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%s' ) );
			}
		}

		foreach ( $sponsor_rows as $index => $sponsor ) {
			$sponsor_name = sanitize_text_field( wp_unslash( $sponsor['name'] ?? '' ) );
			if ( empty( $sponsor_name ) ) {
				continue;
			}
			$sponsor_id = absint( $sponsor['id'] ?? 0 );
			if ( $sponsor_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_sponsorships WHERE id = %d AND event_id = %d", $sponsor_id, $event_id ) ) ) {
				$sponsor_id = 0;
			}
			$sponsor_data = array(
				'event_id' => $event_id,
				'name' => $sponsor_name,
				'description' => sanitize_textarea_field( wp_unslash( $sponsor['description'] ?? '' ) ),
				'benefits' => sanitize_textarea_field( wp_unslash( $sponsor['benefits'] ?? '' ) ),
				'cta_url' => esc_url_raw( wp_unslash( $sponsor['cta_url'] ?? '' ) ),
				'is_visible' => empty( $sponsor['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $sponsor_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_sponsorships', $sponsor_data, array( 'id' => $sponsor_id ), array( '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_sponsorships', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $sponsor_data ), array( '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ) );
			}
		}

		foreach ( $pillar_rows as $index => $pillar ) {
			$pillar_title = sanitize_text_field( wp_unslash( $pillar['title'] ?? '' ) );
			if ( empty( $pillar_title ) ) {
				continue;
			}
			$pillar_id = absint( $pillar['id'] ?? 0 );
			if ( $pillar_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_pillars WHERE id = %d AND event_id = %d", $pillar_id, $event_id ) ) ) {
				$pillar_id = 0;
			}
			$pillar_data = array(
				'event_id' => $event_id,
				'title' => $pillar_title,
				'description' => wp_kses_post( wp_unslash( $pillar['description'] ?? '' ) ),
				'icon' => sanitize_text_field( wp_unslash( $pillar['icon'] ?? '' ) ),
				'is_visible' => empty( $pillar['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $pillar_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_pillars', $pillar_data, array( 'id' => $pillar_id ), array( '%d', '%s', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_pillars', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $pillar_data ), array( '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s' ) );
			}
		}

		if ( $wpdb->last_error ) {
			error_log( 'CER event save failed: ' . $wpdb->last_error );
		}
		delete_transient( 'cer_dashboard_metrics' );
		update_option( 'cer_event_cache_version', (int) get_option( 'cer_event_cache_version', 1 ) + 1, false );

		$redirect_url = add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event_id, 'updated' => 1 ), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
}

if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) {
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Event saved successfully.', 'custom-event-registration' ) . '</p></div>';
}

$event_data = array(
	'event_id' => $default_event['event_id'],
	'event_title' => $default_event['title'],
	'event_slug' => $default_event['slug'],
	'meta_title' => $default_event['meta_title'] ?? '',
	'meta_description' => $default_event['meta_description'] ?? '',
	'meta_keywords' => $default_event['meta_keywords'] ?? '',
	'target_audience' => $default_event['target_audience'],
	'hero_badge_text' => $default_event['hero_badge_text'],
	'hero_convened_by' => $default_event['hero_convened_by'],
	'hero_features' => $default_event['hero_features'],
	'event_info_heading' => $default_event['event_info_heading'],
	'event_info_paragraph_1' => $default_event['event_info_paragraph_1'],
	'event_info_paragraph_2' => $default_event['event_info_paragraph_2'],
	'target_audience_heading' => $default_event['target_audience_heading'],
	'registration_heading' => $default_event['registration_heading'],
	'registration_intro' => $default_event['registration_intro'],
	'sponsorship_heading' => $default_event['sponsorship_heading'],
	'sponsorship_intro' => $default_event['sponsorship_intro'],
	'speakers_heading' => $default_event['speakers_heading'],
	'pillars_heading' => $default_event['pillars_heading'],
	'pillars_intro' => $default_event['pillars_intro'],
	'summary_heading' => $default_event['summary_heading'],
	'summary_note' => $default_event['summary_note'],
	'primary_cta_text' => $default_event['primary_cta_text'],
	'secondary_cta_text' => $default_event['secondary_cta_text'],
	'event_description' => $default_event['description'],
	'event_date' => $default_event['event_date'],
	'event_end_date' => $default_event['event_end_date'],
	'event_venue' => $default_event['venue'],
	'status' => $default_event['status'],
	'show_event_information' => $default_event['show_event_information'],
	'show_speakers' => $default_event['show_speakers'],
	'show_sponsors' => $default_event['show_sponsors'],
	'show_pillars' => $default_event['show_pillars'],
	'max_attendees' => $default_event['max_attendees'],
	'featured_image_id' => $default_event['featured_image_id'],
	'event_uuid' => $default_event['uuid'],
);

if ( empty( $ticket_rows ) ) {
	$ticket_rows = array( array( 'id' => '', 'name' => '', 'price' => '', 'quantity_available' => '', 'description' => '' ) );
}
if ( empty( $speaker_rows ) ) {
	$speaker_rows = array( array( 'id' => '', 'name' => '', 'role' => '', 'bio' => '', 'photo_id' => '' ) );
}
if ( empty( $sponsor_rows ) ) {
	$sponsor_rows = array( array( 'id' => '', 'name' => '', 'description' => '', 'benefits' => '', 'cta_url' => '' ) );
}
if ( empty( $pillar_rows ) ) {
	$pillar_rows = array( array( 'id' => '', 'title' => '', 'description' => '', 'icon' => '', 'is_visible' => 1 ) );
}

?>
<div class="wrap cer-admin-shell">
	<div class="cer-admin-header">
		<h1><?php echo $event_id ? esc_html__( 'Edit Event', 'custom-event-registration' ) : esc_html__( 'Create Event', 'custom-event-registration' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Back to Events', 'custom-event-registration' ); ?></a>
	</div>
	<script>
	jQuery(function($){
		var $title = $('#event-title');
		var $slug = $('#event-slug');
		if ( $title.length && $slug.length ) {
			var syncSlug = function(){
				if ( $slug.val().trim() !== '' && $slug.data('manual') ) {
					return;
				}
				$slug.data('manual', false);
				if ( $slug.val().trim() === '' || !$slug.data('has-value') ) {
					$slug.val( $title.val().trim().toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-') );
				}
			};
			$title.on('input', function(){
				$slug.data('has-value', $slug.val().trim() !== '');
				if ( $slug.val().trim() === '' ) {
					syncSlug();
				}
			});
			$slug.on('input', function(){
				$slug.data('manual', $(this).val().trim() !== '');
			});
		}
	});
	</script>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>">
		<?php wp_nonce_field( 'cer_event_form', 'cer_event_nonce' ); ?>
		<input type="hidden" name="cer_event_save" value="1" />
		<input type="hidden" name="event_id" value="<?php echo esc_attr( $event_data['event_id'] ); ?>" />
		<div class="cer-admin-layout">
			<div class="cer-panel">
				<div class="cer-panel-header">
					<h2><?php esc_html_e( 'Basic Event Info', 'custom-event-registration' ); ?></h2>
					<div class="cer-panel-header-actions"></div>
				</div>
				<div class="cer-panel-body">
					<div class="cer-field-grid">
						<div class="cer-field">
							<label for="event-title"><?php esc_html_e( 'Event Title', 'custom-event-registration' ); ?> <span class="required">*</span></label>
							<input type="text" id="event-title" name="event_title" value="<?php echo esc_attr( $event_data['event_title'] ); ?>" placeholder="Enter the event title (e.g., ICRHK Impact Forum 2026)" />
							<?php if ( ! empty( $errors['event_title'] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors['event_title'] ); ?></span><?php endif; ?>
						</div>
						<div class="cer-field">
							<label for="event-slug"><?php esc_html_e( 'Slug', 'custom-event-registration' ); ?> <span class="required">*</span></label>
							<input type="text" id="event-slug" name="event_slug" value="<?php echo esc_attr( $event_data['event_slug'] ); ?>" placeholder="e.g. icrhk-impact-forum-2026" />
							<?php if ( ! empty( $errors['event_slug'] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors['event_slug'] ); ?></span><?php endif; ?>
						</div>
						<div class="cer-field cer-full">
							<label for="meta-title"><?php esc_html_e( 'Meta Title', 'custom-event-registration' ); ?></label>
							<input type="text" id="meta-title" name="meta_title" value="<?php echo esc_attr( $event_data['meta_title'] ); ?>" placeholder="Optional custom browser title" />
						</div>
						<div class="cer-field cer-full">
							<label for="meta-description"><?php esc_html_e( 'Meta Description', 'custom-event-registration' ); ?></label>
							<textarea id="meta-description" name="meta_description" rows="3" placeholder="Write a concise event summary for search engine previews."><?php echo esc_textarea( $event_data['meta_description'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="meta-keywords"><?php esc_html_e( 'Meta Keywords', 'custom-event-registration' ); ?></label>
							<input type="text" id="meta-keywords" name="meta_keywords" value="<?php echo esc_attr( $event_data['meta_keywords'] ); ?>" placeholder="comma, separated, keywords" />
						</div>
						<div class="cer-field cer-full">
							<label for="event-description"><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
							<?php wp_editor( $event_data['event_description'], 'event_description', array( 'textarea_name' => 'event_description', 'media_buttons' => true, 'teeny' => false, 'textarea_rows' => 9 ) ); ?>
						</div>
						<div class="cer-field cer-full">
							<label for="target-audience"><?php esc_html_e( 'Target Audience', 'custom-event-registration' ); ?></label>
							<textarea id="target-audience" name="target_audience" rows="4" placeholder="Describe the intended participants and audience."><?php echo esc_textarea( $event_data['target_audience'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="hero-badge-text"><?php esc_html_e( 'Hero Badge Text', 'custom-event-registration' ); ?></label>
							<input type="text" id="hero-badge-text" name="hero_badge_text" value="<?php echo esc_attr( $event_data['hero_badge_text'] ); ?>" placeholder="16 Days of Activism Flag-off" />
						</div>
						<div class="cer-field cer-full">
							<label for="hero-convened-by"><?php esc_html_e( 'Convened By', 'custom-event-registration' ); ?></label>
							<input type="text" id="hero-convened-by" name="hero_convened_by" value="<?php echo esc_attr( $event_data['hero_convened_by'] ); ?>" placeholder="ICRHK & Council of Governors" />
						</div>
						<div class="cer-field cer-full">
							<label for="hero-features"><?php esc_html_e( 'Hero Feature Bullets', 'custom-event-registration' ); ?></label>
							<textarea id="hero-features" name="hero_features" rows="3" placeholder="One bullet per line."><?php echo esc_textarea( $event_data['hero_features'] ); ?></textarea>
						</div>
						<div class="cer-field">
							<label for="event-date"><?php esc_html_e( 'Start Date & Time', 'custom-event-registration' ); ?> <span class="required">*</span></label>
							<input type="text" id="event-date" class="cer-datetime-picker" name="event_date" value="<?php echo esc_attr( cer_format_datetime_for_picker( $event_data['event_date'] ) ); ?>" placeholder="YYYY-MM-DD HH:MM" />
							<?php if ( ! empty( $errors['event_date'] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors['event_date'] ); ?></span><?php endif; ?>
						</div>
						<div class="cer-field">
							<label for="event-end-date"><?php esc_html_e( 'End Date & Time', 'custom-event-registration' ); ?></label>
							<input type="text" id="event-end-date" class="cer-datetime-picker" name="event_end_date" value="<?php echo esc_attr( cer_format_datetime_for_picker( $event_data['event_end_date'] ) ); ?>" placeholder="YYYY-MM-DD HH:MM" />
						</div>
						<div class="cer-field">
							<label for="event-venue"><?php esc_html_e( 'Venue', 'custom-event-registration' ); ?></label>
							<input type="text" id="event-venue" name="event_venue" value="<?php echo esc_attr( $event_data['event_venue'] ); ?>" placeholder="Enter the event venue" />
						</div>
						<div class="cer-field">
							<label for="status"><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></label>
							<select id="status" name="status">
								<option value="draft" <?php selected( $event_data['status'], 'draft' ); ?>><?php esc_html_e( 'Draft', 'custom-event-registration' ); ?></option>
								<option value="published" <?php selected( $event_data['status'], 'published' ); ?>><?php esc_html_e( 'Published', 'custom-event-registration' ); ?></option>
								<option value="closed" <?php selected( $event_data['status'], 'closed' ); ?>><?php esc_html_e( 'Closed', 'custom-event-registration' ); ?></option>
								<option value="cancelled" <?php selected( $event_data['status'], 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'custom-event-registration' ); ?></option>
							</select>
						</div>
						<div class="cer-field">
							<label for="max-attendees"><?php esc_html_e( 'Max Attendees', 'custom-event-registration' ); ?></label>
							<input type="number" id="max-attendees" name="max_attendees" value="<?php echo esc_attr( $event_data['max_attendees'] ); ?>" placeholder="250" min="0" />
						</div>
						<div class="cer-field">
							<label><?php esc_html_e( 'Featured Image', 'custom-event-registration' ); ?></label>
							<input type="hidden" id="featured-image-id" name="featured_image_id" value="<?php echo esc_attr( $event_data['featured_image_id'] ); ?>" />
							<div id="featured-image-preview" class="cer-inline-help" style="margin-bottom:8px; min-height: 44px;">
								<?php if ( $event_data['featured_image_id'] ) : ?>
									<?php echo wp_get_attachment_image( $event_data['featured_image_id'], 'thumbnail' ); ?>
								<?php else : ?>
									<?php esc_html_e( 'No featured image selected.', 'custom-event-registration' ); ?>
								<?php endif; ?>
							</div>
							<button type="button" class="button cer-media-select" data-target="featured-image-id" data-preview="featured-image-preview"><?php esc_html_e( 'Select Image', 'custom-event-registration' ); ?></button>
							<button type="button" class="button secondary cer-media-clear" data-target="featured-image-id" data-preview="featured-image-preview" style="margin-left:8px;"><?php esc_html_e( 'Clear', 'custom-event-registration' ); ?></button>
						</div>
						<?php if ( ! empty( $event_data['event_uuid'] ) ) : ?>
						<div class="cer-field">
							<label><?php esc_html_e( 'Event UUID', 'custom-event-registration' ); ?></label>
							<input type="text" value="<?php echo esc_attr( $event_data['event_uuid'] ); ?>" readonly />
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="cer-panel is-collapsed" style="margin-top: 24px;">
				<div class="cer-panel-header">
					<h2><?php esc_html_e( 'Event Display & Marketing Content', 'custom-event-registration' ); ?></h2>
					<div class="cer-panel-header-actions">
						<label class="cer-toggle cer-toggle-compact" for="show-event-information"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="show-event-information" type="checkbox" name="show_event_information" value="1" <?php checked( (int) $event_data['show_event_information'], 1 ); ?> /></label>
					</div>
				</div>
				<div class="cer-panel-body">
					<div class="cer-field-grid">
						<div class="cer-field cer-full">
							<label for="event-info-heading"><?php esc_html_e( 'Event Information Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="event-info-heading" name="event_info_heading" value="<?php echo esc_attr( $event_data['event_info_heading'] ); ?>" placeholder="Event Information" />
						</div>
						<div class="cer-field cer-full">
							<label for="event-info-paragraph-1"><?php esc_html_e( 'Event Information Paragraph 1', 'custom-event-registration' ); ?></label>
							<textarea id="event-info-paragraph-1" name="event_info_paragraph_1" rows="4" placeholder="First supporting paragraph."><?php echo esc_textarea( $event_data['event_info_paragraph_1'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="event-info-paragraph-2"><?php esc_html_e( 'Event Information Paragraph 2', 'custom-event-registration' ); ?></label>
							<textarea id="event-info-paragraph-2" name="event_info_paragraph_2" rows="4" placeholder="Second supporting paragraph."><?php echo esc_textarea( $event_data['event_info_paragraph_2'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="target-audience-heading"><?php esc_html_e( 'Target Audience Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="target-audience-heading" name="target_audience_heading" value="<?php echo esc_attr( $event_data['target_audience_heading'] ); ?>" placeholder="Target Audience" />
						</div>
						<div class="cer-field cer-full">
							<label for="registration-heading"><?php esc_html_e( 'Registration Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="registration-heading" name="registration_heading" value="<?php echo esc_attr( $event_data['registration_heading'] ); ?>" placeholder="Secure Your Place" />
						</div>
						<div class="cer-field cer-full">
							<label for="registration-intro"><?php esc_html_e( 'Registration Intro', 'custom-event-registration' ); ?></label>
							<textarea id="registration-intro" name="registration_intro" rows="3" placeholder="Intro copy above the form."><?php echo esc_textarea( $event_data['registration_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="sponsorship-heading"><?php esc_html_e( 'Sponsorship Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="sponsorship-heading" name="sponsorship_heading" value="<?php echo esc_attr( $event_data['sponsorship_heading'] ); ?>" placeholder="Partner With Us" />
						</div>
						<div class="cer-field cer-full">
							<label for="sponsorship-intro"><?php esc_html_e( 'Sponsorship Intro', 'custom-event-registration' ); ?></label>
							<textarea id="sponsorship-intro" name="sponsorship_intro" rows="3" placeholder="Intro copy for sponsor cards."><?php echo esc_textarea( $event_data['sponsorship_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="speakers-heading"><?php esc_html_e( 'Speakers Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="speakers-heading" name="speakers_heading" value="<?php echo esc_attr( $event_data['speakers_heading'] ); ?>" placeholder="Keynote Speakers" />
						</div>
						<div class="cer-field cer-full">
							<label for="pillars-heading"><?php esc_html_e( 'Pillars Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="pillars-heading" name="pillars_heading" value="<?php echo esc_attr( $event_data['pillars_heading'] ); ?>" placeholder="Thematic Pillars" />
						</div>
						<div class="cer-field cer-full">
							<label for="pillars-intro"><?php esc_html_e( 'Pillars Intro', 'custom-event-registration' ); ?></label>
							<textarea id="pillars-intro" name="pillars_intro" rows="3" placeholder="Intro copy for the pillars grid."><?php echo esc_textarea( $event_data['pillars_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="summary-heading"><?php esc_html_e( 'Summary Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="summary-heading" name="summary_heading" value="<?php echo esc_attr( $event_data['summary_heading'] ); ?>" placeholder="Payment Summary" />
						</div>
						<div class="cer-field cer-full">
							<label for="summary-note"><?php esc_html_e( 'Summary Note', 'custom-event-registration' ); ?></label>
							<textarea id="summary-note" name="summary_note" rows="3" placeholder="Copy shown under the payment summary."><?php echo esc_textarea( $event_data['summary_note'] ); ?></textarea>
						</div>
						<div class="cer-field">
							<label for="primary-cta-text"><?php esc_html_e( 'Primary CTA Text', 'custom-event-registration' ); ?></label>
							<input type="text" id="primary-cta-text" name="primary_cta_text" value="<?php echo esc_attr( $event_data['primary_cta_text'] ); ?>" placeholder="Register Now" />
						</div>
						<div class="cer-field">
							<label for="secondary-cta-text"><?php esc_html_e( 'Secondary CTA Text', 'custom-event-registration' ); ?></label>
							<input type="text" id="secondary-cta-text" name="secondary_cta_text" value="<?php echo esc_attr( $event_data['secondary_cta_text'] ); ?>" placeholder="Learn More" />
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Ticket Types', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions"></div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Add as many tickets as needed for this event.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="tickets">
					<?php foreach ( $ticket_rows as $index => $ticket ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="tickets[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $ticket['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Ticket Name', 'custom-event-registration' ); ?></label>
								<input type="text" name="tickets[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $ticket['name'] ?? '' ); ?>" placeholder="Community Outreach" />
								<?php if ( ! empty( $errors[ 'ticket_name_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'ticket_name_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Price', 'custom-event-registration' ); ?></label>
								<input type="number" name="tickets[<?php echo esc_attr( $index ); ?>][price]" value="<?php echo esc_attr( $ticket['price'] ?? '' ); ?>" step="0.01" min="0" placeholder="5000" />
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Quantity Available', 'custom-event-registration' ); ?></label>
								<input type="number" name="tickets[<?php echo esc_attr( $index ); ?>][quantity_available]" value="<?php echo esc_attr( $ticket['quantity_available'] ?? '' ); ?>" min="0" placeholder="200" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
								<textarea name="tickets[<?php echo esc_attr( $index ); ?>][description]" placeholder="Describe what is included in this ticket type."><?php echo esc_textarea( $ticket['description'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Ticket', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="tickets"><?php esc_html_e( '+ Add Ticket', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Speakers', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<label class="cer-toggle cer-toggle-compact" for="show-speakers"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-speakers" type="checkbox" name="show_speakers" value="1" <?php checked( (int) $event_data['show_speakers'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Add speakers and their profiles for the event program.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="speakers">
					<?php foreach ( $speaker_rows as $index => $speaker ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="speakers[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $speaker['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Speaker Name', 'custom-event-registration' ); ?></label>
								<input type="text" name="speakers[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $speaker['name'] ?? '' ); ?>" placeholder="Enter speaker's full name" />
								<?php if ( ! empty( $errors[ 'speaker_name_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'speaker_name_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Role / Organization', 'custom-event-registration' ); ?></label>
								<input type="text" name="speakers[<?php echo esc_attr( $index ); ?>][role]" value="<?php echo esc_attr( $speaker['role'] ?? '' ); ?>" placeholder="e.g. Human Rights Advocate" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Short Bio', 'custom-event-registration' ); ?></label>
								<textarea name="speakers[<?php echo esc_attr( $index ); ?>][bio]" placeholder="Short bio or profile summary"><?php echo esc_textarea( $speaker['bio'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Photo', 'custom-event-registration' ); ?></label>
								<input type="hidden" name="speakers[<?php echo esc_attr( $index ); ?>][photo_id]" id="speaker-photo-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $speaker['photo_id'] ?? '' ); ?>" />
								<div id="speaker-photo-preview-<?php echo esc_attr( $index ); ?>" style="margin-bottom: 8px; min-height: 30px;">
									<?php if ( ! empty( $speaker['photo_id'] ) ) : ?><?php echo wp_get_attachment_image( $speaker['photo_id'], 'thumbnail' ); ?><?php endif; ?>
								</div>
								<button type="button" class="button cer-media-select" data-target="speaker-photo-<?php echo esc_attr( $index ); ?>" data-preview="speaker-photo-preview-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Select Photo', 'custom-event-registration' ); ?></button>
							</div>
								<div class="cer-field cer-full">
										<label class="cer-toggle" for="speaker-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="speaker-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="speakers[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $speaker['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
								</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Speaker', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="speakers"><?php esc_html_e( '+ Add Speaker', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Sponsorship Packages', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<label class="cer-toggle cer-toggle-compact" for="show-sponsors"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-sponsors" type="checkbox" name="show_sponsors" value="1" <?php checked( (int) $event_data['show_sponsors'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Add sponsor packages and benefits for supporters of the event.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="sponsors">
					<?php foreach ( $sponsor_rows as $index => $sponsor ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="sponsors[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $sponsor['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Package Name', 'custom-event-registration' ); ?></label>
								<input type="text" name="sponsors[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $sponsor['name'] ?? '' ); ?>" placeholder="Bronze" />
								<?php if ( ! empty( $errors[ 'sponsor_name_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'sponsor_name_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'CTA URL', 'custom-event-registration' ); ?></label>
								<input type="url" name="sponsors[<?php echo esc_attr( $index ); ?>][cta_url]" value="<?php echo esc_attr( $sponsor['cta_url'] ?? '' ); ?>" placeholder="https://example.com/contact" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
								<textarea name="sponsors[<?php echo esc_attr( $index ); ?>][description]" placeholder="Write a brief description of the package."><?php echo esc_textarea( $sponsor['description'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Benefits', 'custom-event-registration' ); ?></label>
								<textarea name="sponsors[<?php echo esc_attr( $index ); ?>][benefits]" placeholder="One benefit per line or a short paragraph."><?php echo esc_textarea( $sponsor['benefits'] ?? '' ); ?></textarea>
							</div>
								<div class="cer-field cer-full">
										<label class="cer-toggle" for="sponsor-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="sponsor-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="sponsors[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $sponsor['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
								</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Package', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="sponsors"><?php esc_html_e( '+ Add Sponsorship Package', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Thematic Pillars', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<label class="cer-toggle cer-toggle-compact" for="show-pillars"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-pillars" type="checkbox" name="show_pillars" value="1" <?php checked( (int) $event_data['show_pillars'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Add the thematic pillars that will appear in the front-end grid.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="pillars">
					<?php foreach ( $pillar_rows as $index => $pillar ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="pillars[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $pillar['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Pillar Title', 'custom-event-registration' ); ?></label>
								<input type="text" name="pillars[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $pillar['title'] ?? '' ); ?>" placeholder="Policy & Governance" />
								<?php if ( ! empty( $errors[ 'pillar_title_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'pillar_title_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Icon / Symbol', 'custom-event-registration' ); ?></label>
								<input type="text" name="pillars[<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( $pillar['icon'] ?? '' ); ?>" placeholder="policy" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
								<textarea name="pillars[<?php echo esc_attr( $index ); ?>][description]" placeholder="Describe what this pillar covers."><?php echo esc_textarea( $pillar['description'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
									<label class="cer-toggle" for="pillar-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="pillar-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="pillars[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $pillar['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
							</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Pillar', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="pillars"><?php esc_html_e( '+ Add Pillar', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-admin-actions">
			<?php submit_button( $event_id ? __( 'Save Event', 'custom-event-registration' ) : __( 'Create Event', 'custom-event-registration' ), 'primary', 'cer_submit', false ); ?>
		</div>
		<div class="cer-deleted-inputs" style="display:none;"></div>
	</form>
</div>
