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
	'secondary_logo_id' => '',
	'objectives_heading' => '',
	'objectives_intro' => '',
	'show_objectives' => 1,
	'summit_structure_heading' => '',
	'summit_structure_intro' => '',
	'show_summit_structure' => 1,
	'partners_heading' => '',
	'partners_intro' => '',
	'show_partners' => 1,
	'faq_heading' => '',
	'faq_intro' => '',
	'show_faq' => 1,
	'location_link' => '',
	'location_lat' => '',
	'location_lng' => '',
	'location_address' => '',
	'mail_sender_email' => '',
	'mail_smtp_host' => '',
	'mail_smtp_port' => '',
	'mail_smtp_secure' => '',
	'uuid' => '',
);

$event_id = isset( $_GET['event_id'] ) ? absint( wp_unslash( $_GET['event_id'] ) ) : 0;
$event = null;
$errors = array();
$ticket_rows = array();
$speaker_rows = array();
$sponsor_rows = array();
$pillar_rows = array();
$objective_rows = array();
$summit_rows = array();
$partner_rows = array();
$faq_rows = array();

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
	$default_event['mail_sender_email'] = isset( $event->mail_sender_email ) ? $event->mail_sender_email : '';
	$default_event['mail_smtp_host'] = isset( $event->mail_smtp_host ) ? $event->mail_smtp_host : '';
	$default_event['mail_smtp_port'] = isset( $event->mail_smtp_port ) ? $event->mail_smtp_port : '';
	$default_event['mail_smtp_secure'] = isset( $event->mail_smtp_secure ) ? $event->mail_smtp_secure : '';
	$default_event['mail_password_placeholder'] = '••••••••';
	$default_event['status'] = $event->status;
	$default_event['show_event_information'] = isset( $event->show_event_information ) ? (int) $event->show_event_information : 1;
	$default_event['show_speakers'] = isset( $event->show_speakers ) ? (int) $event->show_speakers : 1;
	$default_event['show_sponsors'] = isset( $event->show_sponsors ) ? (int) $event->show_sponsors : 1;
	$default_event['show_pillars'] = isset( $event->show_pillars ) ? (int) $event->show_pillars : 1;
	$default_event['max_attendees'] = $event->max_attendees;
	$default_event['featured_image_id'] = $event->featured_image_id;
	$default_event['secondary_logo_id'] = isset( $event->secondary_logo_id ) ? $event->secondary_logo_id : '';
	$default_event['objectives_heading'] = isset( $event->objectives_heading ) ? $event->objectives_heading : '';
	$default_event['objectives_intro'] = isset( $event->objectives_intro ) ? $event->objectives_intro : '';
	$default_event['show_objectives'] = isset( $event->show_objectives ) ? (int) $event->show_objectives : 1;
	$default_event['summit_structure_heading'] = isset( $event->summit_structure_heading ) ? $event->summit_structure_heading : '';
	$default_event['summit_structure_intro'] = isset( $event->summit_structure_intro ) ? $event->summit_structure_intro : '';
	$default_event['show_summit_structure'] = isset( $event->show_summit_structure ) ? (int) $event->show_summit_structure : 1;
	$default_event['partners_heading'] = isset( $event->partners_heading ) ? $event->partners_heading : '';
	$default_event['partners_intro'] = isset( $event->partners_intro ) ? $event->partners_intro : '';
	$default_event['show_partners'] = isset( $event->show_partners ) ? (int) $event->show_partners : 1;
	$default_event['faq_heading'] = isset( $event->faq_heading ) ? $event->faq_heading : '';
	$default_event['faq_intro'] = isset( $event->faq_intro ) ? $event->faq_intro : '';
	$default_event['show_faq'] = isset( $event->show_faq ) ? (int) $event->show_faq : 1;
	$default_event['location_link'] = isset( $event->location_link ) ? $event->location_link : '';
	$default_event['location_lat'] = isset( $event->location_lat ) ? $event->location_lat : '';
	$default_event['location_lng'] = isset( $event->location_lng ) ? $event->location_lng : '';
	$default_event['location_address'] = isset( $event->location_address ) ? $event->location_address : '';
	$default_event['uuid'] = $event->event_uuid;

	$ticket_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_ticket_types WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$speaker_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_speakers WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$sponsor_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_sponsorships WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$pillar_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_pillars WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$objective_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_objectives WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$summit_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_summit_structure WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$partner_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_partners WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
	$faq_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_faqs WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ), ARRAY_A );
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
	// The standalone venue field was removed from the form (it duplicated the
	// Location / Map section). Accept a posted value for back-compat, otherwise
	// keep the stored one; the location address below takes final precedence.
	$posted_venue = sanitize_text_field( wp_unslash( $_POST['event_venue'] ?? '' ) );
	if ( '' !== $posted_venue ) {
		$default_event['venue'] = $posted_venue;
	}
	$default_event['status'] = sanitize_key( wp_unslash( $_POST['status'] ?? 'draft' ) );
	$default_event['show_event_information'] = empty( $_POST['show_event_information'] ) ? 0 : 1;
	$default_event['show_speakers'] = empty( $_POST['show_speakers'] ) ? 0 : 1;
	$default_event['show_sponsors'] = empty( $_POST['show_sponsors'] ) ? 0 : 1;
	$default_event['show_pillars'] = empty( $_POST['show_pillars'] ) ? 0 : 1;
	$default_event['max_attendees'] = absint( wp_unslash( $_POST['max_attendees'] ?? 0 ) );
	$default_event['featured_image_id'] = absint( wp_unslash( $_POST['featured_image_id'] ?? 0 ) );
	$default_event['secondary_logo_id'] = absint( wp_unslash( $_POST['secondary_logo_id'] ?? 0 ) );
	$default_event['objectives_heading'] = sanitize_text_field( wp_unslash( $_POST['objectives_heading'] ?? '' ) );
	$default_event['objectives_intro'] = wp_kses_post( wp_unslash( $_POST['objectives_intro'] ?? '' ) );
	$default_event['show_objectives'] = empty( $_POST['show_objectives'] ) ? 0 : 1;
	$default_event['summit_structure_heading'] = sanitize_text_field( wp_unslash( $_POST['summit_structure_heading'] ?? '' ) );
	$default_event['summit_structure_intro'] = wp_kses_post( wp_unslash( $_POST['summit_structure_intro'] ?? '' ) );
	$default_event['show_summit_structure'] = empty( $_POST['show_summit_structure'] ) ? 0 : 1;
	$default_event['partners_heading'] = sanitize_text_field( wp_unslash( $_POST['partners_heading'] ?? '' ) );
	$default_event['partners_intro'] = wp_kses_post( wp_unslash( $_POST['partners_intro'] ?? '' ) );
	$default_event['show_partners'] = empty( $_POST['show_partners'] ) ? 0 : 1;
	$default_event['faq_heading'] = sanitize_text_field( wp_unslash( $_POST['faq_heading'] ?? '' ) );
	$default_event['faq_intro'] = wp_kses_post( wp_unslash( $_POST['faq_intro'] ?? '' ) );
	$default_event['show_faq'] = empty( $_POST['show_faq'] ) ? 0 : 1;
	$default_event['location_link'] = esc_url_raw( wp_unslash( $_POST['location_link'] ?? '' ) );
	$default_event['location_lat'] = sanitize_text_field( wp_unslash( $_POST['location_lat'] ?? '' ) );
	$default_event['location_lng'] = sanitize_text_field( wp_unslash( $_POST['location_lng'] ?? '' ) );
	$default_event['location_address'] = sanitize_text_field( wp_unslash( $_POST['location_address'] ?? '' ) );
	$default_event['mail_sender_email'] = sanitize_email( wp_unslash( $_POST['mail_sender_email'] ?? '' ) );
	$submitted_mail_password = isset( $_POST['mail_password'] ) ? wp_unslash( $_POST['mail_password'] ) : '';
	$default_event['mail_password_placeholder'] = '••••••••';
	$existing_encrypted_password = $event_id ? $wpdb->get_var( $wpdb->prepare( "SELECT mail_password_encrypted FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ) ) : '';
	if ( is_string( $existing_encrypted_password ) && '' !== $existing_encrypted_password && ( '' === $submitted_mail_password || '••••••••' === $submitted_mail_password ) ) {
		$default_event['mail_password_encrypted'] = $existing_encrypted_password;
	} elseif ( '' !== $submitted_mail_password && '••••••••' !== $submitted_mail_password ) {
		$normalized_password = cer_normalize_mail_password( $submitted_mail_password );
		$encryption_key = cer_get_encryption_key();
		if ( '' !== trim( (string) $encryption_key ) ) {
			$default_event['mail_password_encrypted'] = cer_encrypt_pii( $normalized_password );
		} else {
			wp_die( esc_html__( 'Mail encryption key is missing. Configure CER_ENCRYPTION_KEY or ensure AUTH_KEY is defined before saving event email settings.', 'custom-event-registration' ) );
		}
	}

	// Single source of truth for the venue: the Location / Map display address.
	// The front end reads `venue`, so deriving it here keeps every existing
	// consumer (hero badge, summary card, ticket PDF) working unchanged.
	if ( '' !== $default_event['location_address'] ) {
		$default_event['venue'] = $default_event['location_address'];
	}

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
	$objective_rows = isset( $_POST['objectives'] ) && is_array( $_POST['objectives'] ) ? array_values( $_POST['objectives'] ) : array();
	$summit_rows = isset( $_POST['summit'] ) && is_array( $_POST['summit'] ) ? array_values( $_POST['summit'] ) : array();
	$partner_rows = isset( $_POST['partners'] ) && is_array( $_POST['partners'] ) ? array_values( $_POST['partners'] ) : array();
	$faq_rows = isset( $_POST['faqs'] ) && is_array( $_POST['faqs'] ) ? array_values( $_POST['faqs'] ) : array();

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
	foreach ( $objective_rows as $key => $objective ) {
		$objective_title = sanitize_text_field( wp_unslash( $objective['title'] ?? '' ) );
		if ( empty( $objective_title ) && ( ! empty( $objective['description'] ) || ! empty( $objective['icon'] ) ) ) {
			$errors[ 'objective_title_' . $key ] = __( 'Objective title is required.', 'custom-event-registration' );
		}
	}
	foreach ( $summit_rows as $key => $summit_item ) {
		$summit_title = sanitize_text_field( wp_unslash( $summit_item['title'] ?? '' ) );
		if ( empty( $summit_title ) && ( ! empty( $summit_item['description'] ) || ! empty( $summit_item['icon'] ) ) ) {
			$errors[ 'summit_title_' . $key ] = __( 'Summit structure title is required.', 'custom-event-registration' );
		}
	}
	foreach ( $partner_rows as $key => $partner ) {
		$partner_name = sanitize_text_field( wp_unslash( $partner['name'] ?? '' ) );
		$partner_url = isset( $partner['url'] ) ? $partner['url'] : ( isset( $partner['link_url'] ) ? $partner['link_url'] : '' );
		if ( empty( $partner_name ) && ( ! empty( $partner['logo_id'] ) || ! empty( $partner_url ) ) ) {
			$errors[ 'partner_name_' . $key ] = __( 'Partner name is required.', 'custom-event-registration' );
		}
	}
	foreach ( $faq_rows as $key => $faq ) {
		$faq_question = sanitize_text_field( wp_unslash( $faq['question'] ?? '' ) );
		if ( empty( $faq_question ) && ! empty( $faq['answer'] ) ) {
			$errors[ 'faq_question_' . $key ] = __( 'FAQ question is required.', 'custom-event-registration' );
		}
	}

	if ( empty( $errors ) ) {
		// Total capacity is the sum of the individual ticket quantities. The
		// dashboard "Max Attendees" field is derived from this and read-only.
		$computed_max_attendees = 0;
		foreach ( $ticket_rows as $ticket ) {
			if ( '' === sanitize_text_field( wp_unslash( $ticket['name'] ?? '' ) ) ) {
				continue;
			}
			$computed_max_attendees += absint( $ticket['quantity_available'] ?? 0 );
		}
		if ( $computed_max_attendees > 0 ) {
			$default_event['max_attendees'] = $computed_max_attendees;
		} elseif ( ! $default_event['max_attendees'] && $event_id ) {
			// No quantities entered anywhere — keep whatever capacity the event
			// already had instead of zeroing it out.
			$existing_max = $wpdb->get_var( $wpdb->prepare( "SELECT max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d", $event_id ) );
			if ( null !== $existing_max ) {
				$default_event['max_attendees'] = (int) $existing_max;
			}
		}

		$mail_sender_email = sanitize_email( wp_unslash( $_POST['mail_sender_email'] ?? '' ) );
		$mail_from_name = trim( (string) get_option( 'blogname', 'ICRHK Events' ) );
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
			'secondary_logo_id' => $default_event['secondary_logo_id'],
			'objectives_heading' => $default_event['objectives_heading'],
			'objectives_intro' => $default_event['objectives_intro'],
			'show_objectives' => $default_event['show_objectives'],
			'summit_structure_heading' => $default_event['summit_structure_heading'],
			'summit_structure_intro' => $default_event['summit_structure_intro'],
			'show_summit_structure' => $default_event['show_summit_structure'],
			'partners_heading' => $default_event['partners_heading'],
			'partners_intro' => $default_event['partners_intro'],
			'show_partners' => $default_event['show_partners'],
			'faq_heading' => $default_event['faq_heading'],
			'faq_intro' => $default_event['faq_intro'],
			'show_faq' => $default_event['show_faq'],
			'location_link' => $default_event['location_link'],
			'location_lat' => $default_event['location_lat'],
			'location_lng' => $default_event['location_lng'],
			'location_address' => $default_event['location_address'],
			'mail_sender_email' => $mail_sender_email,
			'mail_password_encrypted' => $default_event['mail_password_encrypted'] ?? '',
			'mail_smtp_host' => $default_event['mail_smtp_host'] ?? '',
			'mail_smtp_port' => $default_event['mail_smtp_port'] ?? '',
			'mail_smtp_secure' => $default_event['mail_smtp_secure'] ?? '',
			'mail_from_name' => ! empty( $mail_resolver['from_name'] ) ? $mail_resolver['from_name'] : ( $default_event['mail_from_name'] ?? '' ),
			'mail_notification_email' => $mail_sender_email,
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $event_id ) {
			$event_update_formats = array();
			foreach ( array_keys( $event_data ) as $field_name ) {
				if ( in_array( $field_name, array( 'show_event_information', 'show_speakers', 'show_sponsors', 'show_pillars', 'max_attendees', 'featured_image_id', 'secondary_logo_id', 'show_objectives', 'show_summit_structure', 'show_partners', 'show_faq' ), true ) ) {
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
		$deleted_objective_ids = array_map( 'absint', (array) ( $_POST['deleted_objectives'] ?? array() ) );
		foreach ( $deleted_objective_ids as $deleted_objective_id ) {
			if ( $deleted_objective_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_objectives', array( 'id' => $deleted_objective_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_summit_ids = array_map( 'absint', (array) ( $_POST['deleted_summit'] ?? array() ) );
		foreach ( $deleted_summit_ids as $deleted_summit_id ) {
			if ( $deleted_summit_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_summit_structure', array( 'id' => $deleted_summit_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_partner_ids = array_map( 'absint', (array) ( $_POST['deleted_partners'] ?? array() ) );
		foreach ( $deleted_partner_ids as $deleted_partner_id ) {
			if ( $deleted_partner_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_partners', array( 'id' => $deleted_partner_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
			}
		}
		$deleted_faq_ids = array_map( 'absint', (array) ( $_POST['deleted_faqs'] ?? array() ) );
		foreach ( $deleted_faq_ids as $deleted_faq_id ) {
			if ( $deleted_faq_id ) {
				$wpdb->delete( $wpdb->prefix . 'evt_faqs', array( 'id' => $deleted_faq_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
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

		foreach ( $objective_rows as $index => $objective ) {
			$objective_title = sanitize_text_field( wp_unslash( $objective['title'] ?? '' ) );
			if ( empty( $objective_title ) ) {
				continue;
			}
			$objective_id = absint( $objective['id'] ?? 0 );
			if ( $objective_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_objectives WHERE id = %d AND event_id = %d", $objective_id, $event_id ) ) ) {
				$objective_id = 0;
			}
			$objective_data = array(
				'event_id' => $event_id,
				'title' => $objective_title,
				'description' => wp_kses_post( wp_unslash( $objective['description'] ?? '' ) ),
				'icon' => sanitize_text_field( wp_unslash( $objective['icon'] ?? '' ) ),
				'is_visible' => empty( $objective['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $objective_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_objectives', $objective_data, array( 'id' => $objective_id ), array( '%d', '%s', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_objectives', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $objective_data ), array( '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s' ) );
			}
		}

		foreach ( $summit_rows as $index => $summit_item ) {
			$summit_title = sanitize_text_field( wp_unslash( $summit_item['title'] ?? '' ) );
			if ( empty( $summit_title ) ) {
				continue;
			}
			$summit_id = absint( $summit_item['id'] ?? 0 );
			if ( $summit_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_summit_structure WHERE id = %d AND event_id = %d", $summit_id, $event_id ) ) ) {
				$summit_id = 0;
			}
			$summit_data = array(
				'event_id' => $event_id,
				'title' => $summit_title,
				'description' => wp_kses_post( wp_unslash( $summit_item['description'] ?? '' ) ),
				'icon' => sanitize_text_field( wp_unslash( $summit_item['icon'] ?? '' ) ),
				'is_visible' => empty( $summit_item['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $summit_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_summit_structure', $summit_data, array( 'id' => $summit_id ), array( '%d', '%s', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_summit_structure', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $summit_data ), array( '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s' ) );
			}
		}

		foreach ( $partner_rows as $index => $partner ) {
			$partner_name = sanitize_text_field( wp_unslash( $partner['name'] ?? '' ) );
			if ( empty( $partner_name ) ) {
				continue;
			}
			$partner_id = absint( $partner['id'] ?? 0 );
			if ( $partner_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_partners WHERE id = %d AND event_id = %d", $partner_id, $event_id ) ) ) {
				$partner_id = 0;
			}
			$partner_url = isset( $partner['url'] ) ? $partner['url'] : ( isset( $partner['link_url'] ) ? $partner['link_url'] : '' );
			$partner_data = array(
				'event_id' => $event_id,
				'name' => $partner_name,
				'logo_id' => absint( $partner['logo_id'] ?? 0 ),
				'url' => esc_url_raw( wp_unslash( $partner_url ) ),
				'tel_no' => sanitize_text_field( wp_unslash( $partner['tel_no'] ?? '' ) ),
				'email' => sanitize_email( wp_unslash( $partner['email'] ?? '' ) ),
				'address' => sanitize_text_field( wp_unslash( $partner['address'] ?? '' ) ),
				'is_visible' => empty( $partner['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $partner_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_partners', $partner_data, array( 'id' => $partner_id ), array( '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_partners', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $partner_data ), array( '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ) );
			}
		}

		foreach ( $faq_rows as $index => $faq ) {
			$faq_question = sanitize_text_field( wp_unslash( $faq['question'] ?? '' ) );
			if ( empty( $faq_question ) ) {
				continue;
			}
			$faq_id = absint( $faq['id'] ?? 0 );
			if ( $faq_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_faqs WHERE id = %d AND event_id = %d", $faq_id, $event_id ) ) ) {
				$faq_id = 0;
			}
			$faq_data = array(
				'event_id' => $event_id,
				'question' => $faq_question,
				'answer' => wp_kses_post( wp_unslash( $faq['answer'] ?? '' ) ),
				'is_visible' => empty( $faq['is_visible'] ) ? 0 : 1,
				'order_index' => $index,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $faq_id ) {
				$wpdb->update( $wpdb->prefix . 'evt_faqs', $faq_data, array( 'id' => $faq_id ), array( '%d', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
			} else {
				$wpdb->insert( $wpdb->prefix . 'evt_faqs', array_merge( array( 'created_at' => current_time( 'mysql' ) ), $faq_data ), array( '%s', '%d', '%s', '%s', '%d', '%d', '%s' ) );
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
	'secondary_logo_id' => $default_event['secondary_logo_id'],
	'objectives_heading' => $default_event['objectives_heading'],
	'objectives_intro' => $default_event['objectives_intro'],
	'show_objectives' => $default_event['show_objectives'],
	'summit_structure_heading' => $default_event['summit_structure_heading'],
	'summit_structure_intro' => $default_event['summit_structure_intro'],
	'show_summit_structure' => $default_event['show_summit_structure'],
	'partners_heading' => $default_event['partners_heading'],
	'partners_intro' => $default_event['partners_intro'],
	'show_partners' => $default_event['show_partners'],
	'faq_heading' => $default_event['faq_heading'],
	'faq_intro' => $default_event['faq_intro'],
	'show_faq' => $default_event['show_faq'],
	'location_link' => $default_event['location_link'],
	'location_lat' => $default_event['location_lat'],
	'location_lng' => $default_event['location_lng'],
	'location_address' => $default_event['location_address'],
	'mail_sender_email' => $default_event['mail_sender_email'] ?? '',
	'mail_password_placeholder' => '••••••••',
	'event_uuid' => $default_event['uuid'],
);

if ( empty( $ticket_rows ) ) {
	$ticket_rows = array( array( 'id' => '', 'name' => '', 'price' => '', 'quantity_available' => '', 'description' => '' ) );
}
if ( empty( $speaker_rows ) ) {
	$speaker_rows = array( array( 'id' => '', 'name' => '', 'role' => '', 'bio' => '', 'photo_id' => '', 'is_visible' => 1 ) );
}
if ( empty( $sponsor_rows ) ) {
	$sponsor_rows = array( array( 'id' => '', 'name' => '', 'description' => '', 'benefits' => '', 'cta_url' => '', 'is_visible' => 1 ) );
}
if ( empty( $pillar_rows ) ) {
	$pillar_rows = array( array( 'id' => '', 'title' => '', 'description' => '', 'icon' => '', 'is_visible' => 1 ) );
}
if ( empty( $objective_rows ) ) {
	$objective_rows = array( array( 'id' => '', 'title' => '', 'description' => '', 'icon' => '', 'is_visible' => 1 ) );
}
if ( empty( $summit_rows ) ) {
	$summit_rows = array( array( 'id' => '', 'title' => '', 'description' => '', 'icon' => '', 'is_visible' => 1 ) );
}
if ( empty( $partner_rows ) ) {
	$partner_rows = array( array( 'id' => '', 'name' => '', 'logo_id' => '', 'url' => '', 'tel_no' => '', 'email' => '', 'address' => '', 'is_visible' => 1 ) );
}
if ( empty( $faq_rows ) ) {
	$faq_rows = array( array( 'id' => '', 'question' => '', 'answer' => '', 'is_visible' => 1 ) );
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

		$('.cer-toggle-password').on('click', function(){
			var $button = $(this);
			var targetId = $button.data('target');
			var $input = $('#' + targetId);
			if ( !$input.length ) {
				return;
			}
			var isPassword = $input.attr('type') === 'password';
			$input.attr('type', isPassword ? 'text' : 'password');
			$button.find('.dashicons').toggleClass('dashicons-visibility', ! isPassword).toggleClass('dashicons-hidden', isPassword);
		});

		$('.cer-section-link-btn').on('click', function(){
			var $button = $(this);
			var sectionId = $button.data('sectionId');
			var slug = $('#event-slug').val().trim();
			var baseUrl = '<?php echo esc_js( home_url( '/' ) ); ?>';
			var routeBase = '<?php echo esc_js( cer_get_event_route_base() ); ?>';
			var url = baseUrl.replace(/\/$/, '') + '/' + routeBase + '/' + slug + '/#' + sectionId;

			if ( ! slug ) {
				alert('<?php echo esc_js( __( 'Set a slug before copying the section link.', 'custom-event-registration' ) ); ?>');
				return;
			}

			var fallbackCopy = function(text){
				var $helper = $('<textarea>', {
					readonly: 'readonly',
					style: 'position:fixed;left:-9999px;top:-9999px'
				}).val(text).appendTo('body');
				$helper[0].focus();
				$helper[0].select();
				try {
					document.execCommand('copy');
				} catch (err) {
					console.warn('CER section link copy failed', err);
				}
				$helper.remove();
			};

			if ( navigator.clipboard && window.isSecureContext ) {
				navigator.clipboard.writeText(url).then(function(){
					var originalText = $button.text();
					$button.text('<?php echo esc_js( __( 'Copied!', 'custom-event-registration' ) ); ?>');
					setTimeout(function(){ $button.text(originalText); }, 1200);
				}).catch(function(){ fallbackCopy(url); });
			} else {
				fallbackCopy(url);
				var originalText = $button.text();
				$button.text('<?php echo esc_js( __( 'Copied!', 'custom-event-registration' ) ); ?>');
				setTimeout(function(){ $button.text(originalText); }, 1200);
			}
		});
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
							<input type="number" id="max-attendees" name="max_attendees" value="<?php echo esc_attr( $event_data['max_attendees'] ); ?>" placeholder="0" min="0" readonly />
							<p class="cer-help-text"><?php esc_html_e( 'Auto-calculated as the total of all ticket quantities added below.', 'custom-event-registration' ); ?></p>
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
						<div class="cer-field">
							<label><?php esc_html_e( 'Secondary Logo', 'custom-event-registration' ); ?></label>
							<p class="cer-help-text"><?php esc_html_e( 'Displayed on the right side of the hero section, outside the main navigation.', 'custom-event-registration' ); ?></p>
							<input type="hidden" id="secondary-logo-id" name="secondary_logo_id" value="<?php echo esc_attr( $event_data['secondary_logo_id'] ); ?>" />
							<div id="secondary-logo-preview" class="cer-inline-help" style="margin-bottom:8px; min-height: 44px;">
								<?php if ( $event_data['secondary_logo_id'] ) : ?>
									<?php echo wp_get_attachment_image( $event_data['secondary_logo_id'], 'thumbnail' ); ?>
								<?php else : ?>
									<?php esc_html_e( 'No secondary logo selected.', 'custom-event-registration' ); ?>
								<?php endif; ?>
							</div>
							<button type="button" class="button cer-media-select" data-target="secondary-logo-id" data-preview="secondary-logo-preview"><?php esc_html_e( 'Select Logo', 'custom-event-registration' ); ?></button>
							<button type="button" class="button secondary cer-media-clear" data-target="secondary-logo-id" data-preview="secondary-logo-preview" style="margin-left:8px;"><?php esc_html_e( 'Clear', 'custom-event-registration' ); ?></button>
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
							<label for="objectives-heading"><?php esc_html_e( 'Objectives Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="objectives-heading" name="objectives_heading" value="<?php echo esc_attr( $event_data['objectives_heading'] ); ?>" placeholder="Our Objectives" />
						</div>
						<div class="cer-field cer-full">
							<label for="objectives-intro"><?php esc_html_e( 'Objectives Intro', 'custom-event-registration' ); ?></label>
							<textarea id="objectives-intro" name="objectives_intro" rows="3" placeholder="Intro copy for the objectives grid."><?php echo esc_textarea( $event_data['objectives_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="summit-structure-heading"><?php esc_html_e( 'Summit Structure Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="summit-structure-heading" name="summit_structure_heading" value="<?php echo esc_attr( $event_data['summit_structure_heading'] ); ?>" placeholder="Summit Structure" />
						</div>
						<div class="cer-field cer-full">
							<label for="summit-structure-intro"><?php esc_html_e( 'Summit Structure Intro', 'custom-event-registration' ); ?></label>
							<textarea id="summit-structure-intro" name="summit_structure_intro" rows="3" placeholder="Intro copy for the summit structure grid."><?php echo esc_textarea( $event_data['summit_structure_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="partners-heading"><?php esc_html_e( 'Partners Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="partners-heading" name="partners_heading" value="<?php echo esc_attr( $event_data['partners_heading'] ); ?>" placeholder="Our Partners" />
						</div>
						<div class="cer-field cer-full">
							<label for="partners-intro"><?php esc_html_e( 'Partners Intro', 'custom-event-registration' ); ?></label>
							<textarea id="partners-intro" name="partners_intro" rows="3" placeholder="Intro copy for the partner logos."><?php echo esc_textarea( $event_data['partners_intro'] ); ?></textarea>
						</div>
						<div class="cer-field cer-full">
							<label for="faq-heading"><?php esc_html_e( 'FAQ Heading', 'custom-event-registration' ); ?></label>
							<input type="text" id="faq-heading" name="faq_heading" value="<?php echo esc_attr( $event_data['faq_heading'] ); ?>" placeholder="Frequently Asked Questions" />
						</div>
						<div class="cer-field cer-full">
							<label for="faq-intro"><?php esc_html_e( 'FAQ Intro', 'custom-event-registration' ); ?></label>
							<textarea id="faq-intro" name="faq_intro" rows="3" placeholder="Intro copy shown above the FAQ accordion."><?php echo esc_textarea( $event_data['faq_intro'] ); ?></textarea>
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
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="speakers"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
					<label class="cer-toggle cer-toggle-compact" for="show-speakers"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-speakers" type="checkbox" name="show_speakers" value="1" <?php checked( (int) $event_data['show_speakers'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Add speakers and their profiles for the event program.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="speakers">
					<?php foreach ( $speaker_rows as $index => $speaker ) : ?>
						<div class="cer-repeater-row cer-speaker-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="speakers[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $speaker['id'] ?? '' ); ?>" />
							<div class="cer-speaker-photo-col">
								<input type="hidden" name="speakers[<?php echo esc_attr( $index ); ?>][photo_id]" id="speaker-photo-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $speaker['photo_id'] ?? '' ); ?>" />
								<div id="speaker-photo-preview-<?php echo esc_attr( $index ); ?>" class="cer-speaker-photo-thumb">
									<?php if ( ! empty( $speaker['photo_id'] ) ) : ?><?php echo wp_get_attachment_image( $speaker['photo_id'], 'thumbnail' ); ?><?php else : ?><span class="dashicons dashicons-admin-users"></span><?php endif; ?>
								</div>
							</div>
							<div class="cer-speaker-fields-col">
								<div class="cer-field">
									<label><?php esc_html_e( 'Speaker Name', 'custom-event-registration' ); ?></label>
									<input type="text" name="speakers[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $speaker['name'] ?? '' ); ?>" placeholder="Enter speaker's full name" />
									<?php if ( ! empty( $errors[ 'speaker_name_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'speaker_name_' . $index ] ); ?></span><?php endif; ?>
								</div>
								<div class="cer-field">
									<label><?php esc_html_e( 'Role / Organization', 'custom-event-registration' ); ?></label>
									<input type="text" name="speakers[<?php echo esc_attr( $index ); ?>][role]" value="<?php echo esc_attr( $speaker['role'] ?? '' ); ?>" placeholder="e.g. Human Rights Advocate" />
								</div>
								<div class="cer-field cer-speaker-field-full">
									<label><?php esc_html_e( 'Short Bio', 'custom-event-registration' ); ?></label>
									<textarea name="speakers[<?php echo esc_attr( $index ); ?>][bio]" placeholder="Short bio or profile summary"><?php echo esc_textarea( $speaker['bio'] ?? '' ); ?></textarea>
								</div>
								<div class="cer-field cer-speaker-field-full">
									<label class="cer-toggle" for="speaker-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="speaker-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="speakers[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $speaker['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
								</div>
							</div>
							<div class="cer-speaker-actions-col">
								<span class="cer-row-handle"><?php esc_html_e( 'Speaker', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button button-small cer-media-select" data-target="speaker-photo-<?php echo esc_attr( $index ); ?>" data-preview="speaker-photo-preview-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Select Photo', 'custom-event-registration' ); ?></button>
								<button type="button" class="button button-small cer-media-clear" data-target="speaker-photo-<?php echo esc_attr( $index ); ?>" data-preview="speaker-photo-preview-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Remove Photo', 'custom-event-registration' ); ?></button>
								<button type="button" class="button button-small cer-remove-row"><?php esc_html_e( 'Remove Speaker', 'custom-event-registration' ); ?></button>
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
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="registration"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
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
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="pillars"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
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

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Objectives', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="objectives"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
					<label class="cer-toggle cer-toggle-compact" for="show-objectives"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-objectives" type="checkbox" name="show_objectives" value="1" <?php checked( (int) $event_data['show_objectives'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Compact objective cards shown after the hero section.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="objectives">
					<?php foreach ( $objective_rows as $index => $objective ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="objectives[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $objective['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Objective Title', 'custom-event-registration' ); ?></label>
								<input type="text" name="objectives[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $objective['title'] ?? '' ); ?>" placeholder="Strengthen Coordination" />
								<?php if ( ! empty( $errors[ 'objective_title_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'objective_title_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Icon / Symbol', 'custom-event-registration' ); ?></label>
								<input type="text" name="objectives[<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( $objective['icon'] ?? '' ); ?>" placeholder="flag" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
								<textarea name="objectives[<?php echo esc_attr( $index ); ?>][description]" placeholder="Describe this objective."><?php echo esc_textarea( $objective['description'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
								<label class="cer-toggle" for="objective-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="objective-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="objectives[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $objective['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
							</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Objective', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="objectives"><?php esc_html_e( '+ Add Objective', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Summit Structure', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="summit-structure"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
					<label class="cer-toggle cer-toggle-compact" for="show-summit-structure"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-summit-structure" type="checkbox" name="show_summit_structure" value="1" <?php checked( (int) $event_data['show_summit_structure'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Compact cards describing how the summit is structured (tracks, days, formats).', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="summit">
					<?php foreach ( $summit_rows as $index => $summit_item ) : ?>
						<div class="cer-repeater-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="summit[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $summit_item['id'] ?? '' ); ?>" />
							<div class="cer-field">
								<label><?php esc_html_e( 'Structure Title', 'custom-event-registration' ); ?></label>
								<input type="text" name="summit[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $summit_item['title'] ?? '' ); ?>" placeholder="Day 1: Plenary Sessions" />
								<?php if ( ! empty( $errors[ 'summit_title_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'summit_title_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field">
								<label><?php esc_html_e( 'Icon / Symbol', 'custom-event-registration' ); ?></label>
								<input type="text" name="summit[<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( $summit_item['icon'] ?? '' ); ?>" placeholder="event_note" />
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Description', 'custom-event-registration' ); ?></label>
								<textarea name="summit[<?php echo esc_attr( $index ); ?>][description]" placeholder="Describe this part of the summit structure."><?php echo esc_textarea( $summit_item['description'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
								<label class="cer-toggle" for="summit-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="summit-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="summit[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $summit_item['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
							</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Item', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="summit"><?php esc_html_e( '+ Add Structure Item', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Partners', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="partners"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
					<label class="cer-toggle cer-toggle-compact" for="show-partners"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-partners" type="checkbox" name="show_partners" value="1" <?php checked( (int) $event_data['show_partners'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Partner logos linking out to their websites (opens in a new tab).', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="partners">
					<?php foreach ( $partner_rows as $index => $partner ) : ?>
						<div class="cer-repeater-row cer-speaker-row" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="partners[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $partner['id'] ?? '' ); ?>" />
							<div class="cer-speaker-photo-col">
								<input type="hidden" name="partners[<?php echo esc_attr( $index ); ?>][logo_id]" id="partner-logo-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $partner['logo_id'] ?? '' ); ?>" />
								<div id="partner-logo-preview-<?php echo esc_attr( $index ); ?>" class="cer-speaker-photo-thumb">
									<?php if ( ! empty( $partner['logo_id'] ) ) : ?><?php echo wp_get_attachment_image( $partner['logo_id'], 'thumbnail' ); ?><?php else : ?><span class="dashicons dashicons-format-image"></span><?php endif; ?>
								</div>
							</div>
							<div class="cer-speaker-fields-col cer-partner-fields-col">
								<div class="cer-field">
									<label><?php esc_html_e( 'Partner Name', 'custom-event-registration' ); ?></label>
									<input type="text" name="partners[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $partner['name'] ?? '' ); ?>" placeholder="Ministry of Health" />
									<?php if ( ! empty( $errors[ 'partner_name_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'partner_name_' . $index ] ); ?></span><?php endif; ?>
								</div>
								<div class="cer-field">
									<label><?php esc_html_e( 'Website Link', 'custom-event-registration' ); ?></label>
									<input type="url" name="partners[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( isset( $partner['url'] ) ? $partner['url'] : ( isset( $partner['link_url'] ) ? $partner['link_url'] : '' ) ); ?>" placeholder="https://example.org" />
								</div>
								<div class="cer-partner-contact-group">
									<div class="cer-field">
										<label><?php esc_html_e( 'Tel No', 'custom-event-registration' ); ?></label>
										<input type="tel" name="partners[<?php echo esc_attr( $index ); ?>][tel_no]" value="<?php echo esc_attr( $partner['tel_no'] ?? '' ); ?>" placeholder="+254 20 123 4567" />
									</div>
									<div class="cer-field">
										<label><?php esc_html_e( 'Email', 'custom-event-registration' ); ?></label>
										<input type="email" name="partners[<?php echo esc_attr( $index ); ?>][email]" value="<?php echo esc_attr( $partner['email'] ?? '' ); ?>" placeholder="hello@example.org" />
									</div>
									<div class="cer-field cer-partner-address-field">
										<label><?php esc_html_e( 'Address', 'custom-event-registration' ); ?></label>
										<input type="text" name="partners[<?php echo esc_attr( $index ); ?>][address]" value="<?php echo esc_attr( $partner['address'] ?? '' ); ?>" placeholder="Nairobi, Kenya" />
									</div>
								</div>
								<div class="cer-field cer-speaker-field-full">
									<label class="cer-toggle" for="partner-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="partner-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="partners[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $partner['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
								</div>
							</div>
							<div class="cer-speaker-actions-col">
								<span class="cer-row-handle"><?php esc_html_e( 'Partner', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button button-small cer-media-select" data-target="partner-logo-<?php echo esc_attr( $index ); ?>" data-preview="partner-logo-preview-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Select Logo', 'custom-event-registration' ); ?></button>
								<button type="button" class="button button-small cer-media-clear" data-target="partner-logo-<?php echo esc_attr( $index ); ?>" data-preview="partner-logo-preview-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Remove Logo', 'custom-event-registration' ); ?></button>
								<button type="button" class="button button-small cer-remove-row"><?php esc_html_e( 'Remove Partner', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="partners"><?php esc_html_e( '+ Add Partner', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'FAQ', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions">
					<button type="button" class="button button-small cer-section-link-btn" data-section-id="faq"><?php esc_html_e( 'Section Link', 'custom-event-registration' ); ?></button>
					<label class="cer-toggle cer-toggle-compact" for="show-faq"><span><?php esc_html_e( 'Visible', 'custom-event-registration' ); ?></span><input id="show-faq" type="checkbox" name="show_faq" value="1" <?php checked( (int) $event_data['show_faq'], 1 ); ?> /></label>
				</div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Rendered as an accordion, right before the FAQ closes out the page.', 'custom-event-registration' ); ?></p>
				<div class="cer-repeater" data-repeater="faqs">
					<?php foreach ( $faq_rows as $index => $faq ) : ?>
						<div class="cer-repeater-row cer-single-col" data-row-index="<?php echo esc_attr( $index ); ?>">
							<input type="hidden" name="faqs[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $faq['id'] ?? '' ); ?>" />
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Question', 'custom-event-registration' ); ?></label>
								<input type="text" name="faqs[<?php echo esc_attr( $index ); ?>][question]" value="<?php echo esc_attr( $faq['question'] ?? '' ); ?>" placeholder="How do I register?" />
								<?php if ( ! empty( $errors[ 'faq_question_' . $index ] ) ) : ?><span class="cer-inline-error"><?php echo esc_html( $errors[ 'faq_question_' . $index ] ); ?></span><?php endif; ?>
							</div>
							<div class="cer-field cer-full">
								<label><?php esc_html_e( 'Answer', 'custom-event-registration' ); ?></label>
								<textarea name="faqs[<?php echo esc_attr( $index ); ?>][answer]" placeholder="Provide the answer to this question."><?php echo esc_textarea( $faq['answer'] ?? '' ); ?></textarea>
							</div>
							<div class="cer-field cer-full">
								<label class="cer-toggle" for="faq-visible-<?php echo esc_attr( $index ); ?>"><span><?php esc_html_e( 'Visible on front-end', 'custom-event-registration' ); ?></span><input id="faq-visible-<?php echo esc_attr( $index ); ?>" type="checkbox" name="faqs[<?php echo esc_attr( $index ); ?>][is_visible]" value="1" <?php checked( empty( $faq['is_visible'] ) ? 0 : 1, 1 ); ?> /></label>
							</div>
							<div class="cer-repeater-actions">
								<span class="cer-row-handle"><?php esc_html_e( 'Question', 'custom-event-registration' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
								<button type="button" class="button cer-remove-row"><?php esc_html_e( 'Remove', 'custom-event-registration' ); ?></button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cer-repeater-actions" style="margin-top: 12px; justify-content: flex-start;">
					<button type="button" class="button cer-add-row" data-repeater="faqs"><?php esc_html_e( '+ Add Question', 'custom-event-registration' ); ?></button>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Location / Map', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions"></div>
			</div>
			<div class="cer-panel-body">
				<p class="cer-inline-help"><?php esc_html_e( 'Set the venue location once — it feeds the venue display, the hero location badge and the front-end floating map widget. Search for a place or paste coordinates (lat, lng) in the same map picker.', 'custom-event-registration' ); ?></p>
				<div class="cer-field-grid">
					<div class="cer-field cer-full">
						<label for="location-link"><?php esc_html_e( 'Location Link (Google Maps URL)', 'custom-event-registration' ); ?></label>
						<input type="url" id="location-link" name="location_link" value="<?php echo esc_attr( $event_data['location_link'] ); ?>" placeholder="https://www.google.com/maps?q=..." />
					</div>
					<div class="cer-field">
						<label for="location-lat"><?php esc_html_e( 'Latitude', 'custom-event-registration' ); ?></label>
						<input type="text" id="location-lat" name="location_lat" value="<?php echo esc_attr( $event_data['location_lat'] ); ?>" placeholder="-1.2921" />
					</div>
					<div class="cer-field">
						<label for="location-lng"><?php esc_html_e( 'Longitude', 'custom-event-registration' ); ?></label>
						<input type="text" id="location-lng" name="location_lng" value="<?php echo esc_attr( $event_data['location_lng'] ); ?>" placeholder="36.8219" />
					</div>
					<div class="cer-field cer-full">
						<label for="location-address"><?php esc_html_e( 'Display Address', 'custom-event-registration' ); ?></label>
						<input type="text" id="location-address" name="location_address" value="<?php echo esc_attr( $event_data['location_address'] ); ?>" placeholder="KICC, Nairobi, Kenya" />
						<p class="cer-help-text"><?php esc_html_e( 'Also used as the event venue label on the front end and tickets.', 'custom-event-registration' ); ?></p>
					</div>
					<div class="cer-field cer-full">
						<button type="button" class="button" id="cer-open-map-search"><?php esc_html_e( 'Search on Map', 'custom-event-registration' ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<div class="cer-panel" style="margin-top: 24px;">
			<div class="cer-panel-header">
				<h2><?php esc_html_e( 'Event Mail Settings', 'custom-event-registration' ); ?></h2>
				<div class="cer-panel-header-actions"></div>
			</div>
			<div class="cer-panel-body">
				<div class="cer-field-grid">
					<div class="cer-field">
						<label for="mail-sender-email"><?php esc_html_e( 'Sender Email', 'custom-event-registration' ); ?></label>
						<input type="email" id="mail-sender-email" name="mail_sender_email" value="<?php echo esc_attr( $event_data['mail_sender_email'] ); ?>" placeholder="organizer@example.com" />
					</div>
					<div class="cer-field">
						<label for="mail-password"><?php esc_html_e( 'App Password', 'custom-event-registration' ); ?></label>
						<div class="cer-password-wrap" style="position:relative;">
							<input type="password" id="mail-password" name="mail_password" value="" placeholder="••••••••" autocomplete="new-password" style="padding-right: 42px;" />
							<button type="button" class="button button-small cer-toggle-password" data-target="mail-password" aria-label="<?php esc_attr_e( 'Show or hide app password', 'custom-event-registration' ); ?>" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);padding:0 8px;line-height:28px;height:28px;">
								<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
							</button>
						</div>
						<p class="cer-help-text"><?php esc_html_e( 'Leave blank to keep the existing encrypted password.', 'custom-event-registration' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<div id="cer-map-modal" class="cer-map-modal" hidden>
			<div class="cer-map-modal-backdrop"></div>
			<div class="cer-map-modal-dialog">
				<button type="button" class="cer-map-modal-close" id="cer-close-map-search" aria-label="<?php esc_attr_e( 'Close', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-no-alt"></span></button>
				<h2><?php esc_html_e( 'Search Location', 'custom-event-registration' ); ?></h2>
				<div class="cer-map-search-row">
					<input type="text" id="cer-map-search-input" placeholder="<?php esc_attr_e( 'Type an address, place name, or coordinates (lat, lng)…', 'custom-event-registration' ); ?>" />
					<button type="button" class="button button-primary" id="cer-map-search-go"><?php esc_html_e( 'Preview', 'custom-event-registration' ); ?></button>
				</div>
				<div class="cer-map-embed-wrap">
					<iframe id="cer-map-embed" src="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
				</div>
				<button type="button" class="button button-primary" id="cer-map-use-location"><?php esc_html_e( 'Use This Location', 'custom-event-registration' ); ?></button>
			</div>
		</div>

		<div class="cer-admin-actions">
			<?php submit_button( $event_id ? __( 'Save Event', 'custom-event-registration' ) : __( 'Create Event', 'custom-event-registration' ), 'primary', 'cer_submit', false ); ?>
		</div>
		<div class="cer-deleted-inputs" style="display:none;"></div>
	</form>
</div>

