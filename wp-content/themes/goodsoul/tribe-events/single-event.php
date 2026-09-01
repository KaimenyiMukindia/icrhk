<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package goodsoul
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$time_format          = get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT );
$time_range_separator = tribe_get_option( 'timeRangeSeparator', ' - ' );
$start_datetime       = tribe_get_start_date();
$start_date           = tribe_get_start_date( null, false );
$start_time           = tribe_get_start_date( null, false, $time_format );
$start_ts             = tribe_get_start_date( null, false, Tribe__Date_Utils::DBDATEFORMAT );
$end_datetime         = tribe_get_end_date();
$end_date             = tribe_get_display_end_date( null, false );
$end_time             = tribe_get_end_date( null, false, $time_format );
$end_ts               = tribe_get_end_date( null, false, Tribe__Date_Utils::DBDATEFORMAT );

$time_formatted = null;
if ( $start_time == $end_time ) {
	$time_formatted = esc_html( $start_time );
} else {
	$time_formatted = esc_html( $start_time . $time_range_separator . $end_time );
}
$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();
$event_id              = get_queried_object_id();



$goodsoul_metabox_date_event_counter = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_date_event_counter', true );
$goodsoul_metabox_event_where        = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_where', true );
$goodsoul_metabox_event_when         = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_when', true );
$goodsoul_metabox_event_description  = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_description', true );
$goodsoul_metabox_event_organizer    = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_organizer', true );
$goodsoul_metabox_event_organizer_t  = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_organizer_t', true );
$goodsoul_metabox_event_phone        = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_phone', true );
$goodsoul_metabox_event_phone_t      = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_phone_t', true );
$goodsoul_metabox_event_email        = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_email', true );
$goodsoul_metabox_event_email_t      = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_email_t', true );




?>
<section class="upcoming-events-section-two style-two events-section-bg">
	<div class="auto-container">
		<div class="sec-title text-center light">
			<h1><?php the_title(); ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="countdown-timer-two">
				<div class="default-coundown">
					<div class="box">
						<div class="countdown time-countdown-three"
							data-countdown-time="<?php echo esc_attr( $goodsoul_metabox_date_event_counter ); ?>"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="event-time-place">
	<div class="auto-container">
		<div class="row">
			<div class="col-lg-6">
				<div class="content">
					<h2><?php echo wp_kses_post( $goodsoul_metabox_event_where ); ?></h2>
					<div class="text"><?php echo tribe_get_full_address(); ?></div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="content">
					<h2><?php echo wp_kses_post( $goodsoul_metabox_event_when ); ?></h2>
					<div class="text"><?php esc_html_e( 'Block your calendar on -', 'goodsoul' ); ?>
						<?php echo wp_kses( $start_ts, 'code_contxt' ); ?> <br>
						<?php echo wp_kses( $end_ts, 'code_contxt' ); ?><?php esc_html_e( ' @', 'goodsoul' ); ?>
						<?php echo esc_html__( $start_time . ' - ' . $end_time, 'goodsoul' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<section class="about-event">
	<div class="auto-container">
		<div class="row">
			<div class="col-lg-6">
				<div class="image mb-30"><?php echo tribe_event_featured_image( $event_id, 'full', false ); ?></div>
			</div>
			<div class="col-lg-6">
				<div class="content">
				<?php if ( $goodsoul_metabox_event_description ) { ?>
					<h2><?php echo esc_html__( 'Event description', 'goodsoul' ); ?></h2>
					<div class="text">
					<?php echo wp_kses_post( $goodsoul_metabox_event_description ); ?>
					</div>
				<?php } ?>
				<?php if ( $goodsoul_metabox_event_organizer ) { ?>
					<div class="info-box">
						<h5><?php echo esc_html__( 'Organizer', 'goodsoul' ); ?></h5>
						<a href="#"><?php echo wp_kses_post( $goodsoul_metabox_event_organizer ); ?></a>
					</div>
				<?php } ?>
				<?php if ( $goodsoul_metabox_event_phone ) { ?>
					<div class="info-box">
						<h5><?php echo esc_html__( 'Phone', 'goodsoul' ); ?></h5>
						<a href="tel:<?php echo wp_kses_post( $goodsoul_metabox_event_phone ); ?>"><?php echo wp_kses_post( $goodsoul_metabox_event_phone ); ?></a>
					</div>
				<?php } ?>  
				<?php if ( $goodsoul_metabox_event_email ) { ?>
					<div class="info-box">
						<h5><?php echo esc_html__( 'Email', 'goodsoul' ); ?></h5>
						<a href="mailto:<?php echo wp_kses_post( $goodsoul_metabox_event_email ); ?>"><?php echo wp_kses_post( $goodsoul_metabox_event_email ); ?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
<?php the_content(); ?>
<div class="google-map">
	<?php
	$goodsoul_map = tribe_get_embedded_map();
	if ( empty( $goodsoul_map ) ) {
		return;
	}
	echo sprintf( '%s', $goodsoul_map );
	?>
</div>
