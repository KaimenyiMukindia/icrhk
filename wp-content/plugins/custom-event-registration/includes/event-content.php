<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_event_content_sections(): array {
	return array(
		'objectives' => array( 'heading' => 'Our Objectives', 'eyebrow' => 'What we aim to achieve' ),
		'pillars' => array( 'heading' => 'Thematic Pillars', 'eyebrow' => 'Conference framework' ),
		'summit_structure' => array( 'heading' => 'Summit Structure', 'eyebrow' => 'How the three days run' ),
		'speakers' => array( 'heading' => 'Keynote Speakers', 'eyebrow' => 'Featured leaders' ),
		'registration' => array( 'heading' => 'Secure Your Place', 'eyebrow' => 'Registration portal' ),
		'sponsorship' => array( 'heading' => 'Partner With Us', 'eyebrow' => '' ),
		'summary' => array( 'heading' => 'Payment Summary', 'eyebrow' => '' ),
		'partners' => array( 'heading' => 'Our Partners', 'eyebrow' => 'Convened with' ),
		'faq' => array( 'heading' => 'Frequently Asked Questions', 'eyebrow' => 'Before you register' ),
	);
}

function cer_event_content_columns(): array {
	$columns = array(
		'hero_details' => 'LONGTEXT NULL',
		'page_content' => 'LONGTEXT NULL',
		'show_page_content' => 'TINYINT(1) NOT NULL DEFAULT 1',
		'hero_summary_heading' => 'VARCHAR(255) NULL',
		'speakers_intro' => 'LONGTEXT NULL',
	);
	foreach ( cer_event_content_sections() as $section => $defaults ) {
		$columns[ $section . '_eyebrow' ] = 'VARCHAR(255) NULL';
	}
	return $columns;
}

function cer_event_section_eyebrow( $event, string $section ): string {
	$field = $section . '_eyebrow';
	return isset( $event->{$field} ) ? (string) $event->{$field} : cer_event_content_sections()[ $section ]['eyebrow'];
}

function cer_ensure_event_content_schema(): void {
	if ( '2' === get_option( 'cer_event_content_schema', '' ) ) {
		return;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'evt_events';
	foreach ( cer_event_content_columns() as $column => $definition ) {
		cer_add_column_if_missing( $table, $column, $definition );
		if ( ! cer_column_exists( $table, $column ) ) {
			error_log( 'CER event content schema unavailable: ' . $column . ' ' . $wpdb->last_error );
			return;
		}
	}
	update_option( 'cer_event_content_schema', '2', false );
}

function cer_parse_event_content_rows( $rows, bool $hero = false ) {
	if ( ! is_array( $rows ) ) {
		return new WP_Error( 'cer_content_rows', __( 'The content rows are invalid. Reload the page and try again.', 'custom-event-registration' ) );
	}
	$fields = $hero ? array( 'label', 'value' ) : array( 'header', 'subtitle', 'body' );
	$clean = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			return new WP_Error( 'cer_content_row', __( 'A content row is invalid.', 'custom-event-registration' ) );
		}
		$item = array();
		foreach ( $fields as $field ) {
			$value = $row[ $field ] ?? '';
			if ( ! is_string( $value ) ) {
				return new WP_Error( 'cer_content_field', __( 'Content fields must contain text.', 'custom-event-registration' ) );
			}
			$item[ $field ] = 'body' === $field ? wp_kses_post( $value ) : sanitize_text_field( $value );
			if ( 'body' !== $field && mb_strlen( $item[ $field ] ) > 255 ) {
				return new WP_Error( 'cer_content_length', __( 'Content headings, subtitles and hero details must be 255 characters or fewer.', 'custom-event-registration' ) );
			}
		}
		if ( ! array_filter( $item, static function ( $value ) { return '' !== trim( $value ); } ) ) {
			continue;
		}
		if ( '' === trim( $item[ $hero ? 'value' : 'header' ] ) ) {
			return new WP_Error( 'cer_content_required', $hero
				? __( 'Enter a value for each hero detail, or remove the empty row.', 'custom-event-registration' )
				: __( 'Enter a header for each page content row, or remove the empty row.', 'custom-event-registration' ) );
		}
		$clean[] = $item;
	}
	return $clean;
}

function cer_event_content_rows( $event, string $field ): array {
	$value = $event->{$field} ?? null;
	if ( null === $value || '' === $value ) {
		if ( 'hero_details' === $field && ! empty( $event->hero_convened_by ) ) {
			return array( array( 'label' => __( 'Convened by', 'custom-event-registration' ), 'value' => $event->hero_convened_by ) );
		}
		return array();
	}
	$rows = cer_parse_event_content_rows( json_decode( $value, true ), 'hero_details' === $field );
	if ( is_wp_error( $rows ) ) {
		error_log( 'CER invalid stored event content for event #' . (int) ( $event->id ?? 0 ) . ' field ' . $field . ': ' . $rows->get_error_message() );
		return array();
	}
	return $rows;
}

function cer_render_section_copy_fields( string $section, array $event_data ): void {
	$defaults = cer_event_content_sections()[ $section ];
	echo '<div class="cer-field-grid cer-section-copy-fields">';
	foreach ( array( 'eyebrow' => __( 'Eyebrow', 'custom-event-registration' ), 'heading' => __( 'Section title', 'custom-event-registration' ), 'intro' => __( 'Intro', 'custom-event-registration' ) ) as $part => $label ) {
		if ( 'summary' === $section && 'intro' === $part ) {
			continue;
		}
		$name = $section . '_' . $part;
		$id = str_replace( '_', '-', $name );
		$value = $event_data[ $name ] ?? '';
		$placeholder = $defaults[ $part ] ?? '';
		echo '<div class="cer-field cer-full"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		if ( 'intro' === $part ) {
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="3">' . esc_textarea( $value ) . '</textarea>';
		} else {
			echo '<input type="text" maxlength="255" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
		}
		echo '</div>';
	}
	echo '</div>';
}

function cer_render_content_repeater_row( string $type, $index, array $row ): void {
	$fields = 'hero_details' === $type
		? array( 'label' => __( 'Label', 'custom-event-registration' ), 'value' => __( 'Value', 'custom-event-registration' ) )
		: array( 'header' => __( 'Header', 'custom-event-registration' ), 'subtitle' => __( 'Subtitle', 'custom-event-registration' ), 'body' => __( 'Body', 'custom-event-registration' ) );
	echo '<div class="cer-repeater-row" data-row-index="' . esc_attr( $index ) . '">';
	foreach ( $fields as $field => $label ) {
		$name = $type . '[' . $index . '][' . $field . ']';
		$id = $type . '-' . $index . '-' . $field;
		echo '<div class="cer-field cer-full"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		if ( 'body' === $field ) {
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="6">' . esc_textarea( $row[ $field ] ?? '' ) . '</textarea>';
		} else {
			echo '<input id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" type="text" maxlength="255" value="' . esc_attr( $row[ $field ] ?? '' ) . '" />';
		}
		echo '</div>';
	}
	echo '<div class="cer-repeater-actions"><button type="button" class="button cer-remove-row">' . esc_html__( 'Remove', 'custom-event-registration' ) . '</button></div></div>';
}

function cer_render_content_repeater( string $type, array $rows ): void {
	echo '<input type="hidden" name="' . esc_attr( $type . '_present' ) . '" value="1" />';
	echo '<div class="cer-repeater" data-repeater="' . esc_attr( $type ) . '">';
	foreach ( $rows ?: array( array() ) as $index => $row ) {
		cer_render_content_repeater_row( $type, $index, $row );
	}
	echo '</div><template id="cer-template-' . esc_attr( $type ) . '">';
	cer_render_content_repeater_row( $type, '__INDEX__', array() );
	echo '</template><button type="button" class="button cer-add-row" data-repeater="' . esc_attr( $type ) . '">' . esc_html__( '+ Add row', 'custom-event-registration' ) . '</button>';
}
