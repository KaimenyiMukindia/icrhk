<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cer_render_event_registration_page' ) ) {
	function cer_render_event_registration_page() {
		global $wpdb;

		if ( ! function_exists( 'cer_get_registration_form_markup' ) ) {
			echo '<div class="auto-container" style="padding:48px 20px;">Registration form is unavailable.</div>';
			return;
		}

		$current_event = cer_event_page_get_current_event();
		if ( ! $current_event && ! empty( cer_event_page_get_requested_slug() ) ) {
			status_header( 404 );
			nocache_headers();
		}
		$event_description = $current_event ? wp_kses_post( $current_event->description ) : '';

		$hero_badge_text = cer_event_page_copy_value( $current_event, 'hero_badge_text', 'Event Registration' );
		$hero_convened_by = cer_event_page_copy_value( $current_event, 'hero_convened_by', '' );
		$hero_features = cer_event_page_split_lines( cer_event_page_copy_value( $current_event, 'hero_features', '' ) );
		$event_info_heading = cer_event_page_copy_value( $current_event, 'event_info_heading', 'Event Information' );
		$event_info_paragraph_1 = cer_event_page_copy_value( $current_event, 'event_info_paragraph_1', $event_description );
		$event_info_paragraph_2 = cer_event_page_copy_value( $current_event, 'event_info_paragraph_2', '' );
		$target_audience_heading = cer_event_page_copy_value( $current_event, 'target_audience_heading', 'Target Audience' );
		$registration_heading = cer_event_page_copy_value( $current_event, 'registration_heading', 'Secure Your Place' );
		$registration_intro = cer_event_page_copy_value( $current_event, 'registration_intro', 'Complete your registration and payment details below.' );
		$sponsorship_heading = cer_event_page_copy_value( $current_event, 'sponsorship_heading', 'Partner With Us' );
		$sponsorship_intro = cer_event_page_copy_value( $current_event, 'sponsorship_intro', 'Explore sponsorship opportunities for the event.' );
		$speakers_heading = cer_event_page_copy_value( $current_event, 'speakers_heading', 'Keynote Speakers' );
		$pillars_heading = cer_event_page_copy_value( $current_event, 'pillars_heading', 'Thematic Pillars' );
		$pillars_intro = cer_event_page_copy_value( $current_event, 'pillars_intro', '' );
		$summary_heading = cer_event_page_copy_value( $current_event, 'summary_heading', 'Payment Summary' );
		$summary_note = cer_event_page_copy_value( $current_event, 'summary_note', 'Payment verification and receipt delivery are handled after registration using the secure gateway workflow.' );
		$primary_cta_text = cer_event_page_copy_value( $current_event, 'primary_cta_text', 'Register Now' );
		$secondary_cta_text = cer_event_page_copy_value( $current_event, 'secondary_cta_text', 'Learn More' );
		if ( $current_event ) {
			$event_id = (int) $current_event->id;
			$show_event_information = isset( $current_event->show_event_information ) ? (int) $current_event->show_event_information : 1;
			$show_speakers = isset( $current_event->show_speakers ) ? (int) $current_event->show_speakers : 1;
			$show_sponsors = isset( $current_event->show_sponsors ) ? (int) $current_event->show_sponsors : 1;
			$show_pillars  = isset( $current_event->show_pillars ) ? (int) $current_event->show_pillars : 1;
			$event_speakers = $show_speakers ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_speakers', true ) : array();
			$event_sponsors = $show_sponsors ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_sponsorships', true ) : array();
			$event_pillars  = $show_pillars ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_pillars', true ) : array();
		} else {
			$event_id = 0;
			$show_event_information = 0;
			$show_speakers = 0;
			$show_sponsors = 0;
			$show_pillars  = 0;
			$event_speakers = array();
			$event_sponsors = array();
			$event_pillars = array();
		}
		?>
		<div class="cer-event-page">
			<div class="cer-top-glass-backdrop" aria-hidden="true"></div>
			<div class="cer-event-shell">
				<?php if ( ! $current_event ) : ?>
					<section class="cer-empty-state">
						<h1>Event not found</h1>
						<p>The requested event could not be located. Please return to the latest published event.</p>
					</section>
				<?php else : ?>
					<?php
					$event_title = trim( (string) $current_event->name );
					$event_title_prefix = $event_title;
					$event_title_highlight = '';
					if ( false !== strpos( $event_title, ':' ) ) {
						$parts = explode( ':', $event_title, 2 );
						$event_title_prefix = trim( $parts[0] );
						$event_title_highlight = trim( $parts[1] );
					}
					$event_dates = cer_event_page_format_range( $current_event->event_date, $current_event->event_end_date );
					$event_venue = $current_event->venue ? $current_event->venue : 'To be confirmed';
					$event_attendees = number_format_i18n( max( 0, (int) $current_event->max_attendees ) ?: 500 ) . ' delegates';
					$hero_features = cer_event_page_split_lines( cer_event_page_copy_value( $current_event, 'hero_features', '' ) );
					$hero_badge_text = cer_event_page_copy_value( $current_event, 'hero_badge_text', 'Event Registration' );
					$hero_convened_by = cer_event_page_copy_value( $current_event, 'hero_convened_by', '' );
					$registration_intro = cer_event_page_copy_value( $current_event, 'registration_intro', 'Complete your registration and payment details below.' );
					$summary_note = cer_event_page_copy_value( $current_event, 'summary_note', 'Payment verification and receipt delivery are handled after registration using the secure gateway workflow.' );
					$target_audience_text = ! empty( $current_event->target_audience ) ? wp_strip_all_tags( $current_event->target_audience ) : wp_strip_all_tags( $current_event->description );
					?>
					<div class="cer-event-page cer-kamgc-page">
						<div class="cer-event-shell cer-kamgc-shell">
							<section class="cer-kamgc-hero" id="home">
								<div class="cer-kamgc-badge">
									<span class="material-symbols-outlined">campaign</span>
									<span><?php echo esc_html( $hero_badge_text ); ?></span>
								</div>
								<div class="cer-kamgc-hero-inner">
									<div class="cer-kamgc-hero-copy">
										<h1>
											<?php echo esc_html( $event_title_prefix ); ?>
											<?php if ( '' !== $event_title_highlight ) : ?>
												<span><?php echo esc_html( ': ' . $event_title_highlight ); ?></span>
											<?php endif; ?>
										</h1>
										
										<div class="cer-kamgc-meta-row">
<div><span class="material-symbols-outlined">calendar_month</span><span>Starts: <?php echo esc_html( cer_event_page_format_date_only( $current_event->event_date ) ); ?></span></div>
													<?php if ( ! empty( $current_event->event_end_date ) ) : ?>
														<div class="cer-kamgc-divider"></div>
														<div><span class="material-symbols-outlined">event</span><span>Ends: <?php echo esc_html( cer_event_page_format_date_only( $current_event->event_end_date ) ); ?></span></div>
													<?php endif; ?>
											<div class="cer-kamgc-divider"></div>
											<div><span class="material-symbols-outlined">location_on</span><span><?php echo esc_html( $event_venue ); ?></span></div>
											<div class="cer-kamgc-divider"></div>
											<div><span class="material-symbols-outlined">groups</span><span><?php echo esc_html( $event_attendees ); ?></span></div>
										</div>
										<?php if ( ! empty( $hero_convened_by ) ) : ?>
											<div class="cer-kamgc-convened-by"><span>Convened by:</span> <?php echo esc_html( $hero_convened_by ); ?></div>
										<?php endif; ?>
										<?php if ( ! empty( $hero_features ) ) : ?>
											<ul class="cer-kamgc-feature-list">
												<?php foreach ( $hero_features as $feature ) : ?>
													<li><span class="material-symbols-outlined text-success">check_circle</span><?php echo esc_html( $feature ); ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
										<div class="cer-kamgc-actions">
											<a class="cer-kamgc-button cer-kamgc-button-primary" href="#registration"><?php echo esc_html( $primary_cta_text ); ?> <span class="material-symbols-outlined">arrow_forward</span></a>
											<a class="cer-kamgc-button cer-kamgc-button-secondary" href="#pillars"><?php echo esc_html( $secondary_cta_text ); ?></a>
										</div>
													<?php $share_links = cer_get_event_share_links( $current_event ); if ( ! empty( $share_links ) ) : ?>
													<div class="cer-share-bar" aria-label="Share event">
														<span class="cer-share-label"><?php esc_html_e( 'Share event:', 'custom-event-registration' ); ?></span>
														<a href="<?php echo esc_url( $share_links['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook" title="<?php esc_attr_e( 'Share on Facebook', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-facebook-alt" aria-hidden="true"></span></a>
														<a href="<?php echo esc_url( $share_links['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X" title="<?php esc_attr_e( 'Share on X', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-twitter" aria-hidden="true"></span></a>
														<a href="<?php echo esc_url( $share_links['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn" title="<?php esc_attr_e( 'Share on LinkedIn', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-linkedin" aria-hidden="true"></span></a>
														<a href="<?php echo esc_url( $share_links['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp" title="<?php esc_attr_e( 'Share on WhatsApp', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-format-chat" aria-hidden="true"></span></a>
													</div>
													<?php endif; ?>
									</div>
								</div>
							</section>

							<section class="cer-kamgc-main" id="registration">
								<div class="cer-kamgc-two-col">
									<div class="cer-kamgc-main-column">
										<?php if ( $show_event_information ) : ?>
											<div class="cer-kamgc-card">
												<h2><?php echo esc_html( $event_info_heading ); ?></h2>
												<div class="cer-kamgc-copy-stack">
													<?php if ( ! empty( $event_info_paragraph_1 ) ) : ?><p><?php echo wp_kses_post( $event_info_paragraph_1 ); ?></p><?php endif; ?>
													<?php if ( ! empty( $event_info_paragraph_2 ) ) : ?><p><?php echo wp_kses_post( $event_info_paragraph_2 ); ?></p><?php endif; ?>
												</div>
												<?php if ( ! empty( $current_event->target_audience ) ) : ?>
													<div class="cer-kamgc-callout">
														<div class="cer-kamgc-callout-icon"><span class="material-symbols-outlined">info</span></div>
														<div>
															<h4><?php echo esc_html( $target_audience_heading ); ?></h4>
															<p><?php echo wp_kses_post( $current_event->target_audience ); ?></p>
														</div>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<div class="cer-kamgc-card">
											<h2><?php echo esc_html( $registration_heading ); ?></h2>
											<p class="cer-kamgc-form-intro"><?php echo wp_kses_post( $registration_intro ); ?></p>
											<?php echo cer_get_registration_form_markup( $event_id ); ?>
											<div id="cer-form-message" class="cer-form-message"></div>
										</div>
									</div>
									<div class="cer-kamgc-sidebar-column">
										<?php if ( ! empty( $event_sponsors ) ) : ?>
											<div class="cer-kamgc-sponsor-box">
												<h3><?php echo esc_html( $sponsorship_heading ); ?></h3>
												<p><?php echo wp_kses_post( $sponsorship_intro ); ?></p>
												<ul class="cer-kamgc-sponsor-list">
													<?php foreach ( $event_sponsors as $sponsor ) : ?>
														<li>
															<div class="cer-kamgc-sponsor-badge"><?php echo esc_html( strtoupper( substr( $sponsor->name, 0, 1 ) ) ); ?></div>
															<span><?php echo esc_html( $sponsor->name ); ?></span>
														</li>
													<?php endforeach; ?>
												</ul>
												<?php if ( ! empty( $event_sponsors[0]->cta_url ) ) : ?>
													<a class="cer-kamgc-inline-button" href="<?php echo esc_url( $event_sponsors[0]->cta_url ); ?>"><?php echo esc_html__( 'Enquire About Sponsorship', 'custom-event-registration' ); ?></a>
												<?php endif; ?>
											</div>
										<?php endif; ?>
										<div class="cer-kamgc-summary-box">
											<h3><?php echo esc_html( $summary_heading ); ?></h3>
											<div class="cer-kamgc-summary-row"><span>Selected Ticket</span><strong id="cer-summary-ticket">Select a ticket</strong></div>
											<div class="cer-kamgc-summary-row"><span>Payment Method</span><strong id="cer-summary-method">M-Pesa</strong></div>
											<div class="cer-kamgc-summary-total"><span>Total</span><strong id="cer-summary-amount">KES 0.00</strong></div>
											<p><?php echo wp_kses_post( $summary_note ); ?></p>
											<div class="cer-kamgc-security"><span class="material-symbols-outlined">lock</span><span>Secure Payment</span></div>
										</div>
									</div>
								</div>
							</section>

							<?php if ( $show_speakers && ! empty( $event_speakers ) ) : ?>
								<section class="cer-kamgc-speakers">
									<div class="cer-kamgc-card cer-kamgc-speakers-card">
										<div class="cer-kamgc-section-title">
											<h2><?php echo esc_html( $speakers_heading ); ?></h2>
											<div class="cer-kamgc-divider-center"></div>
										</div>
										<div class="cer-kamgc-speaker-grid">
											<?php foreach ( $event_speakers as $speaker ) : ?>
												<div class="cer-kamgc-speaker-card">
													<div class="cer-kamgc-speaker-avatar"><?php echo $speaker->photo_id ? wp_get_attachment_image( $speaker->photo_id, 'medium' ) : '<span class="material-symbols-outlined">person</span>'; ?></div>
													<h3><?php echo esc_html( $speaker->name ); ?></h3>
													<p class="cer-kamgc-speaker-role"><?php echo esc_html( $speaker->role ); ?></p>
													<p><?php echo wp_kses_post( $speaker->bio ); ?></p>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>

							<?php if ( $show_pillars && ! empty( $event_pillars ) ) : ?>
								<section class="cer-kamgc-pillars" id="pillars">
									<div class="cer-kamgc-card cer-kamgc-pillars-card-wrapper">
										<div class="cer-kamgc-pillar-header">
											<h2><?php echo esc_html( $pillars_heading ); ?></h2>
												<?php if ( ! empty( $pillars_intro ) ) : ?>
													<p><?php echo wp_kses_post( $pillars_intro ); ?></p>
												<?php endif; ?>
										</div>
										<div class="cer-kamgc-pillar-grid">
											<?php foreach ( $event_pillars as $index => $pillar ) : ?>
												<div class="cer-kamgc-pillars-card<?php echo ( $index === 6 ) ? ' cer-kamgc-pillars-card-large' : ''; ?>">
													<div class="cer-kamgc-pillars-front"><div class="cer-kamgc-pillars-icon"><span class="material-symbols-outlined"><?php echo esc_html( $pillar->icon ? $pillar->icon : 'insights' ); ?></span></div><h3><?php echo esc_html( $pillar->title ); ?></h3></div>
													<div class="cer-kamgc-pillars-back"><p><?php echo wp_kses_post( $pillar->description ); ?></p></div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			
		</div>
		<?php
	}
}

if ( ! function_exists( 'cer_event_page_copy_value' ) ) {
	function cer_event_page_copy_value( $event, $field, $fallback = '' ) {
		if ( $event && isset( $event->{$field} ) ) {
			$value = trim( (string) $event->{$field} );
			if ( '' !== $value ) {
				return $value;
			}
		}

		return $fallback;
	}
}

if ( ! function_exists( 'cer_event_page_get_requested_slug' ) ) {
	function cer_event_page_get_requested_slug() {
		$event_slug = sanitize_title( (string) get_query_var( 'event_slug' ) );
		if ( ! empty( $event_slug ) ) {
			return $event_slug;
		}

		if ( ! empty( $_GET['event_slug'] ) ) {
			return sanitize_title( wp_unslash( $_GET['event_slug'] ) );
		}

		if ( ! empty( $_GET['event_id'] ) ) {
			return absint( wp_unslash( $_GET['event_id'] ) );
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
		if ( '' === $path ) {
			return '';
		}

		$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
			$path = substr( $path, strlen( $home_path ) + 1 );
		}

		$segments = array_values( array_filter( explode( '/', $path ) ) );
		if ( count( $segments ) < 2 ) {
			return '';
		}

		if ( 'event' !== sanitize_title( (string) $segments[0] ) ) {
			return '';
		}

		return sanitize_title( (string) $segments[1] );
	}
}

if ( ! function_exists( 'cer_event_page_get_current_event' ) ) {
	function cer_event_page_get_current_event() {
		global $wpdb;
		$events_table = $wpdb->prefix . 'evt_events';
		$identifier = cer_event_page_get_requested_slug();
			$cache_key = 'event:' . (int) get_option( 'cer_event_cache_version', 1 ) . ':' . md5( (string) $identifier );
			$cached_event = wp_cache_get( $cache_key, 'cer_events' );
			if ( false !== $cached_event ) {
				return $cached_event;
			}

		if ( is_numeric( $identifier ) && (int) $identifier > 0 ) {
				$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$events_table} WHERE id = %d LIMIT 1", (int) $identifier ) );
				wp_cache_set( $cache_key, $event, 'cer_events', HOUR_IN_SECONDS );
				return $event;
		}

		if ( ! empty( $identifier ) ) {
			$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$events_table} WHERE slug = %s LIMIT 1", $identifier ) );
				wp_cache_set( $cache_key, $event, 'cer_events', HOUR_IN_SECONDS );
				return $event;
		}

			$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$events_table} WHERE status = %s ORDER BY event_date DESC LIMIT 1", 'published' ) );
			wp_cache_set( $cache_key, $event, 'cer_events', HOUR_IN_SECONDS );
			return $event;
	}
}

if ( ! function_exists( 'cer_event_page_get_related_rows' ) ) {
	function cer_event_page_get_related_rows( $event_id, $table, $respect_visibility = true ) {
		global $wpdb;
			$cache_key = (int) get_option( 'cer_event_cache_version', 1 ) . ':' . md5( $table . ':' . (int) $event_id . ':' . (int) $respect_visibility );
			$cached_rows = wp_cache_get( $cache_key, 'cer_event_related' );
			if ( false !== $cached_rows ) {
				return $cached_rows;
			}

			$visibility_sql = $respect_visibility ? ' AND is_visible = 1' : '';
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE event_id = %d{$visibility_sql} ORDER BY order_index ASC, id ASC", $event_id ) );
			wp_cache_set( $cache_key, $rows, 'cer_event_related', HOUR_IN_SECONDS );
			return $rows;
	}
}

if ( ! function_exists( 'cer_event_page_table_has_visibility' ) ) {
	function cer_event_page_table_has_visibility( $table ) {
		global $wpdb;
		return ! empty( $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'is_visible' ) ) );
	}
}

if ( ! function_exists( 'cer_event_page_format_datetime' ) ) {
	function cer_event_page_format_datetime( $value ) {
		return ! empty( $value ) ? date_i18n( 'M j, Y \a\t g:i a', strtotime( $value ) ) : 'To be confirmed';
	}
}

if ( ! function_exists( 'cer_event_page_format_date_only' ) ) {
	function cer_event_page_format_date_only( $value ) {
		return ! empty( $value ) ? date_i18n( 'M j, Y', strtotime( $value ) ) : 'To be confirmed';
	}
}

if ( ! function_exists( 'cer_event_page_format_range' ) ) {
	function cer_event_page_format_range( $start, $end ) {
		if ( empty( $start ) ) {
			return 'Date to be confirmed';
		}

		$start_label = date_i18n( 'M j, Y', strtotime( $start ) );
		if ( ! empty( $end ) ) {
			$end_label = date_i18n( 'M j, Y', strtotime( $end ) );
			if ( $end_label !== $start_label ) {
				return $start_label . ' - ' . $end_label;
			}
		}

		return $start_label;
	}
}

if ( ! function_exists( 'cer_event_page_split_lines' ) ) {
	function cer_event_page_split_lines( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return array();
		}

		$decoded = json_decode( $value, true );
		if ( is_array( $decoded ) ) {
			return array_values( array_filter( array_map( 'sanitize_text_field', $decoded ) ) );
		}

		$lines = preg_split( '/\r\n|\r|\n/', $value );
		return array_values( array_filter( array_map( 'sanitize_text_field', $lines ) ) );
	}
}
