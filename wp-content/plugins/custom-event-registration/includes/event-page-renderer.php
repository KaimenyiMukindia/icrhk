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
		$objectives_heading = cer_event_page_copy_value( $current_event, 'objectives_heading', 'Our Objectives' );
		$objectives_intro = cer_event_page_copy_value( $current_event, 'objectives_intro', '' );
		$summit_structure_heading = cer_event_page_copy_value( $current_event, 'summit_structure_heading', 'Summit Structure' );
		$summit_structure_intro = cer_event_page_copy_value( $current_event, 'summit_structure_intro', '' );
		$partners_heading = cer_event_page_copy_value( $current_event, 'partners_heading', 'Our Partners' );
		$partners_intro = cer_event_page_copy_value( $current_event, 'partners_intro', '' );
		$faq_heading = cer_event_page_copy_value( $current_event, 'faq_heading', 'Frequently Asked Questions' );
		$faq_intro = cer_event_page_copy_value( $current_event, 'faq_intro', '' );
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
			$show_objectives = isset( $current_event->show_objectives ) ? (int) $current_event->show_objectives : 1;
			$show_summit_structure = isset( $current_event->show_summit_structure ) ? (int) $current_event->show_summit_structure : 1;
			$show_partners = isset( $current_event->show_partners ) ? (int) $current_event->show_partners : 1;
			$show_faq = isset( $current_event->show_faq ) ? (int) $current_event->show_faq : 1;
			$event_speakers = $show_speakers ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_speakers', true ) : array();
			$event_sponsors = $show_sponsors ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_sponsorships', true ) : array();
			$event_pillars  = $show_pillars ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_pillars', true ) : array();
			$event_objectives = $show_objectives ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_objectives', true ) : array();
			$event_summit_structure = $show_summit_structure ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_summit_structure', true ) : array();
			$event_partners = $show_partners ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_partners', true ) : array();
			$event_faqs = $show_faq ? cer_event_page_get_related_rows( $event_id, $wpdb->prefix . 'evt_faqs', true ) : array();
			$secondary_logo_id = isset( $current_event->secondary_logo_id ) ? (int) $current_event->secondary_logo_id : 0;
			$location_link = isset( $current_event->location_link ) ? trim( (string) $current_event->location_link ) : '';
			$location_lat = isset( $current_event->location_lat ) ? trim( (string) $current_event->location_lat ) : '';
			$location_lng = isset( $current_event->location_lng ) ? trim( (string) $current_event->location_lng ) : '';
			$location_address = isset( $current_event->location_address ) ? trim( (string) $current_event->location_address ) : '';
		} else {
			$event_id = 0;
			$show_event_information = 0;
			$show_speakers = 0;
			$show_sponsors = 0;
			$show_pillars  = 0;
			$show_objectives = 0;
			$show_summit_structure = 0;
			$show_partners = 0;
			$show_faq = 0;
			$event_speakers = array();
			$event_sponsors = array();
			$event_pillars = array();
			$event_objectives = array();
			$event_summit_structure = array();
			$event_partners = array();
			$event_faqs = array();
			$secondary_logo_id = 0;
			$location_link = '';
			$location_lat = '';
			$location_lng = '';
			$location_address = '';
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
					// Venue is derived from the Location / Map section (display address) when
					// no explicit venue string exists, so the hero never goes blank.
					$event_venue = $current_event->venue ? $current_event->venue : ( $location_address ? $location_address : 'To be confirmed' );
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
								<div class="cer-kamgc-hero-inner">
									<div class="cer-kamgc-hero-copy">
										<div class="cer-kamgc-hero-badges">
											<?php
											/*
											 * Conference emblem.
											 *
											 * Prefer an image chosen in the admin (Secondary Logo), but fall
											 * back to the copy bundled with the plugin. The admin choice is a
											 * media-library attachment ID, which is database state and does
											 * not travel with the code — so on a fresh checkout the bundled
											 * file is what makes the emblem appear.
											 */
											$cer_emblem_markup = '';
											if ( $secondary_logo_id ) {
												$cer_emblem_markup = wp_get_attachment_image( $secondary_logo_id, 'medium' );
											}
											if ( '' === $cer_emblem_markup && defined( 'CER_PLUGIN_DIR' ) && file_exists( CER_PLUGIN_DIR . 'assets/img/kamgc-emblem.png' ) ) {
												$cer_emblem_markup = sprintf(
													'<img src="%s" width="512" height="512" alt="%s" loading="lazy" decoding="async" />',
													esc_url( CER_PLUGIN_URL . 'assets/img/kamgc-emblem.png' ),
													esc_attr__( 'KAMGC — Kenya Annual Multi-Sectoral GBV Conference official emblem', 'custom-event-registration' )
												);
											}
											?>
											<?php if ( $cer_emblem_markup ) : ?>
												<div class="cer-kamgc-emblem-badge">
													<?php echo $cer_emblem_markup; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped ?>
													<div><strong><?php echo esc_html( $hero_badge_text ); ?></strong><span>Official Conference Emblem</span><small>KAMGC Kenya 2026</small></div>
												</div>
											<?php else : ?>
												<div class="cer-kamgc-badge"><span class="material-symbols-outlined">campaign</span><span>National Summit</span></div>
											<?php endif; ?>
											<div class="cer-kamgc-convening-badge"><span class="material-symbols-outlined">workspace_premium</span><span>National Convening • Kenya 2026</span></div>
										</div>
										<div class="cer-kamgc-activism-badge"><span class="material-symbols-outlined">campaign</span><span>16 Days of Activism Flag-off</span></div>
										<h1>
											<?php echo esc_html( $event_title_prefix ); ?>
											<?php if ( '' !== $event_title_highlight ) : ?>
												<span><?php echo esc_html( ': ' . $event_title_highlight ); ?></span>
											<?php endif; ?>
										</h1>
										<p class="cer-kamgc-hero-description"><?php echo wp_kses_post( $event_description ); ?></p>
										
										<?php if ( ! empty( $hero_convened_by ) ) : ?>
											<div class="cer-kamgc-convened-by"><span>Convened by:</span> <?php echo esc_html( $hero_convened_by ); ?></div>
										<?php endif; ?>

										<div class="cer-kamgc-actions">
											<a class="cer-kamgc-button cer-kamgc-button-primary" href="#registration"><?php echo esc_html( $primary_cta_text ); ?> <span class="material-symbols-outlined">arrow_forward</span></a>
											<a class="cer-kamgc-button cer-kamgc-button-secondary" href="#pillars"><?php echo esc_html( $secondary_cta_text ); ?></a>
										</div>
													<?php $share_links = cer_get_event_share_links( $current_event ); if ( ! empty( $share_links ) ) : ?>
													<div class="cer-share-bar" aria-label="Share event">
														<span class="cer-share-label"><?php esc_html_e( 'Share event:', 'custom-event-registration' ); ?></span>
														<a href="<?php echo esc_url( $share_links['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook" title="<?php esc_attr_e( 'Share on Facebook', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-facebook-alt" aria-hidden="true"></span></a>
																<a href="<?php echo esc_url( $share_links['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X" title="<?php esc_attr_e( 'Share on X', 'custom-event-registration' ); ?>"><svg class="cer-share-brand-icon cer-share-x-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817-5.963 6.817H1.684l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z" fill="currentColor"></path></svg></a>
														<a href="<?php echo esc_url( $share_links['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn" title="<?php esc_attr_e( 'Share on LinkedIn', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-linkedin" aria-hidden="true"></span></a>
																<a href="<?php echo esc_url( $share_links['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp" title="<?php esc_attr_e( 'Share on WhatsApp', 'custom-event-registration' ); ?>"><span class="fa fa-whatsapp cer-share-brand-icon" aria-hidden="true"></span></a>
													</div>
													<?php endif; ?>
									</div>
									<div class="cer-kamgc-summary-box cer-kamgc-hero-summary">
										<h3>Conference Summary</h3>
										<div class="cer-kamgc-summary-metadata">
											<div><span class="cer-kamgc-metadata-icon material-symbols-outlined">calendar_month</span><span><small>Starts</small><strong><?php echo esc_html( cer_event_page_format_datetime( $current_event->event_date ) ); ?></strong></span></div>
											<div><span class="cer-kamgc-metadata-icon material-symbols-outlined">event</span><span><small>Ends</small><strong><?php echo esc_html( cer_event_page_format_datetime( $current_event->event_end_date ) ); ?></strong></span></div>
											<div><span class="cer-kamgc-metadata-icon material-symbols-outlined">location_on</span><span><small>Location</small><strong><?php if ( $location_link ) : ?><a href="<?php echo esc_url( $location_link ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $event_venue ); ?></a><?php else : ?><?php echo esc_html( $event_venue ); ?><?php endif; ?></strong></span></div>
											<div><span class="cer-kamgc-metadata-icon material-symbols-outlined">groups</span><span><small>Capacity</small><strong><?php echo esc_html( $event_attendees ); ?></strong></span></div>
										</div>
									</div>
									<?php if ( ! empty( $hero_features ) ) : ?>
										<ul class="cer-kamgc-feature-list">
											<?php foreach ( $hero_features as $feature ) : ?>
												<li><span class="material-symbols-outlined text-success">check_circle</span><?php echo esc_html( $feature ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</section>

							<?php if ( $show_objectives && ! empty( $event_objectives ) ) : ?>
								<section class="cer-kamgc-objectives" id="objectives">
									<div class="cer-kamgc-card cer-kamgc-objectives-card">
										<div class="cer-kamgc-section-title">
											<h2><?php echo esc_html( $objectives_heading ); ?></h2>
											<?php if ( ! empty( $objectives_intro ) ) : ?><p><?php echo wp_kses_post( $objectives_intro ); ?></p><?php endif; ?>
											<div class="cer-kamgc-divider-center"></div>
										</div>
										<div class="cer-kamgc-compact-grid">
											<?php foreach ( $event_objectives as $objective ) : ?>
												<div class="cer-kamgc-compact-card">
													<div class="cer-kamgc-compact-icon"><span class="material-symbols-outlined"><?php echo esc_html( $objective->icon ? $objective->icon : 'flag' ); ?></span></div>
													<h3><?php echo esc_html( $objective->title ); ?></h3>
													<p><?php echo wp_kses_post( $objective->description ); ?></p>
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
												<?php $pillar_panel_id = 'cer-pillar-panel-' . (int) $index; ?>
												<div class="cer-kamgc-pillars-card cer-reveal" style="--cer-reveal-delay: <?php echo (int) ( $index * 60 ); ?>ms;">
													<button type="button" class="cer-kamgc-pillars-front" aria-expanded="false" aria-controls="<?php echo esc_attr( $pillar_panel_id ); ?>"><div class="cer-kamgc-pillars-icon"><span class="material-symbols-outlined"><?php echo esc_html( $pillar->icon ? $pillar->icon : 'insights' ); ?></span></div><h3><?php echo esc_html( $pillar->title ); ?></h3></button>
												<div class="cer-kamgc-pillars-back" id="<?php echo esc_attr( $pillar_panel_id ); ?>"><button type="button" class="cer-kamgc-pillars-close" aria-label="<?php esc_attr_e( 'Close pillar details', 'custom-event-registration' ); ?>"><span class="material-symbols-outlined">close</span></button><p><?php echo wp_kses_post( $pillar->description ); ?></p></div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>

							<?php if ( $show_summit_structure && ! empty( $event_summit_structure ) ) : ?>
								<section class="cer-kamgc-summit-structure" id="summit-structure">
									<div class="cer-kamgc-card cer-kamgc-summit-structure-card">
										<div class="cer-kamgc-section-title">
											<h2><?php echo esc_html( $summit_structure_heading ); ?></h2>
											<?php if ( ! empty( $summit_structure_intro ) ) : ?><p><?php echo wp_kses_post( $summit_structure_intro ); ?></p><?php endif; ?>
											<div class="cer-kamgc-divider-center"></div>
										</div>
										<div class="cer-kamgc-compact-grid">
											<?php foreach ( $event_summit_structure as $summit_item ) : ?>
												<div class="cer-kamgc-compact-card">
													<div class="cer-kamgc-compact-icon"><span class="material-symbols-outlined"><?php echo esc_html( $summit_item->icon ? $summit_item->icon : 'event_note' ); ?></span></div>
													<h3><?php echo esc_html( $summit_item->title ); ?></h3>
													<p><?php echo wp_kses_post( $summit_item->description ); ?></p>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>

							<?php if ( $show_partners && ! empty( $event_partners ) ) : ?>
								<section class="cer-kamgc-partners" id="partners">
									<div class="cer-kamgc-card cer-kamgc-partners-card">
										<div class="cer-kamgc-section-title">
											<h2><?php echo esc_html( $partners_heading ); ?></h2>
											<?php if ( ! empty( $partners_intro ) ) : ?><p><?php echo wp_kses_post( $partners_intro ); ?></p><?php endif; ?>
											<div class="cer-kamgc-divider-center"></div>
										</div>
										<div class="cer-kamgc-partner-grid">
											<?php foreach ( $event_partners as $partner ) : ?>
												<?php if ( ! empty( $partner->link_url ) ) : ?>
													<a class="cer-kamgc-partner-card" href="<?php echo esc_url( $partner->link_url ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $partner->name ); ?>">
												<?php else : ?>
													<div class="cer-kamgc-partner-card" title="<?php echo esc_attr( $partner->name ); ?>">
												<?php endif; ?>
													<?php if ( $partner->logo_id ) : ?>
														<?php echo wp_get_attachment_image( $partner->logo_id, 'medium' ); ?>
													<?php else : ?>
														<span class="material-symbols-outlined">handshake</span><span><?php echo esc_html( $partner->name ); ?></span>
													<?php endif; ?>
												<?php if ( ! empty( $partner->link_url ) ) : ?>
													</a>
												<?php else : ?>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>

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

							<section class="cer-kamgc-main" id="registration">
								<div class="cer-kamgc-two-col">
									<div class="cer-kamgc-main-column">
										<?php if ( $show_event_information ) : ?>
											<div class="cer-kamgc-card">
												<div class="cer-kamgc-heading-with-icon"><span class="cer-kamgc-heading-icon material-symbols-outlined">info</span><h2><?php echo esc_html( $event_info_heading ); ?></h2></div>
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
												<div class="cer-kamgc-stats-grid">
													<div><strong>3 Days</strong><span>Plenary keynotes, panels &amp; workshops</span></div>
													<div><strong>47 Counties</strong><span>Multi-sectoral devolution representation</span></div>
													<div><strong>500+</strong><span>Delegates, experts and global partners</span></div>
												</div>
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

							<?php if ( $show_faq && ! empty( $event_faqs ) ) : ?>
								<section class="cer-kamgc-faq" id="faq">
									<div class="cer-kamgc-card cer-kamgc-faq-card">
										<div class="cer-kamgc-section-title">
											<h2><?php echo esc_html( $faq_heading ); ?></h2>
											<?php if ( ! empty( $faq_intro ) ) : ?><p><?php echo wp_kses_post( $faq_intro ); ?></p><?php endif; ?>
											<div class="cer-kamgc-divider-center"></div>
										</div>
										<div class="cer-kamgc-faq-accordion">
											<?php foreach ( $event_faqs as $faq ) : ?>
												<details class="cer-kamgc-faq-item">
													<summary><span><?php echo esc_html( $faq->question ); ?></span><span class="material-symbols-outlined cer-kamgc-faq-caret">expand_more</span></summary>
													<div class="cer-kamgc-faq-answer"><?php echo wp_kses_post( $faq->answer ); ?></div>
												</details>
											<?php endforeach; ?>
										</div>
									</div>
								</section>
							<?php endif; ?>

							<?php
							$map_embed_url = cer_event_page_get_map_embed_url( $location_link, $location_lat, $location_lng, $location_address, $event_venue );
							?>
							<?php if ( '' !== $map_embed_url ) : ?>
								<div class="cer-kamgc-map-widget" id="cer-map-widget">
									<button type="button" class="cer-kamgc-map-toggle" id="cer-map-toggle" aria-label="<?php esc_attr_e( 'Show event location', 'custom-event-registration' ); ?>">
										<span class="material-symbols-outlined">location_on</span>
									</button>
									<div class="cer-kamgc-map-panel" id="cer-map-panel" hidden>
										<button type="button" class="cer-kamgc-map-close" id="cer-map-close" aria-label="<?php esc_attr_e( 'Close map', 'custom-event-registration' ); ?>"><span class="material-symbols-outlined">close</span></button>
										<iframe class="cer-kamgc-map-iframe" src="<?php echo esc_url( $map_embed_url ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php esc_attr_e( 'Event location map', 'custom-event-registration' ); ?>"></iframe>
									</div>
								</div>
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
		$preview_allowed = current_user_can( 'manage_options' ) && is_numeric( $identifier ) && isset( $_GET['cer_event_preview'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['cer_event_preview'] ) ), 'cer_event_preview_' . (int) $identifier );
			$cache_key = 'event:' . (int) get_option( 'cer_event_cache_version', 1 ) . ':' . ( $preview_allowed ? 'preview:' : 'public:' ) . md5( (string) $identifier );
			$cached_event = wp_cache_get( $cache_key, 'cer_events' );
			if ( false !== $cached_event ) {
				return $cached_event;
			}

		$status_sql = $preview_allowed ? '' : $wpdb->prepare( ' AND status = %s', 'published' );

		if ( is_numeric( $identifier ) && (int) $identifier > 0 ) {
				$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$events_table} WHERE id = %d{$status_sql} LIMIT 1", (int) $identifier ) );
				wp_cache_set( $cache_key, $event, 'cer_events', HOUR_IN_SECONDS );
				return $event;
		}

		if ( ! empty( $identifier ) ) {
			$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$events_table} WHERE slug = %s{$status_sql} LIMIT 1", $identifier ) );
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

if ( ! function_exists( 'cer_event_page_get_map_embed_url' ) ) {
	function cer_event_page_get_map_embed_url( $location_link, $lat, $lng, $address, $fallback_query = '' ) {
		if ( $lat && $lng ) {
			return 'https://www.google.com/maps?q=' . rawurlencode( $lat . ',' . $lng ) . '&output=embed';
		}

		if ( $address ) {
			return 'https://www.google.com/maps?q=' . rawurlencode( $address ) . '&output=embed';
		}

		if ( $location_link ) {
			$query = wp_parse_url( $location_link, PHP_URL_QUERY );
			if ( $query ) {
				parse_str( $query, $params );
				if ( ! empty( $params['q'] ) ) {
					return 'https://www.google.com/maps?q=' . rawurlencode( $params['q'] ) . '&output=embed';
				}
			}
		}

		// Short links (e.g. maps.app.goo.gl) carry no readable query — fall back to
		// the venue/label text so the embed still renders something meaningful.
		if ( $fallback_query && 'To be confirmed' !== $fallback_query ) {
			return 'https://www.google.com/maps?q=' . rawurlencode( $fallback_query ) . '&output=embed';
		}

		return '';
	}
}
