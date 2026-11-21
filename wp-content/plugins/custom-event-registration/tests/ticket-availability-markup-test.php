<?php

define( 'ABSPATH', __DIR__ );
require dirname( __DIR__ ) . '/includes/registration-functions.php';

function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
function number_format_i18n( $value, $decimals ) { return number_format( $value, $decimals ); }

foreach ( array(
	array( 100, 26, 'Available', '' ),
	array( 100, 25, '25 left', ' - 25 left' ),
	array( 100, 24, '24 left', ' - 24 left' ),
	array( 100, 0, 'Sold out', ' - Sold out' ),
	array( 10, 3, 'Available', '' ),
	array( 10, 2, '2 left', ' - 2 left' ),
	array( null, null, 'Available', '' ),
	array( null, 5, 'Available', '' ),
) as list( $capacity, $remaining, $label, $suffix ) ) {
	$ticket = array( 'capacity' => $capacity, 'remaining' => $remaining, 'maintenance' => false );
	$markup = cer_ticket_availability_markup( $ticket + array( 'tickets' => array( $ticket ) ) );
	if ( ! str_contains( $markup, '>' . $label . '</strong>' ) ||
		cer_ticket_option_availability_suffix( $ticket, 0 === $remaining ) !== $suffix ) {
		throw new RuntimeException( 'Incorrect availability display for ' . json_encode( $ticket ) );
	}
}

$ticket = array( 'capacity' => 100, 'remaining' => 25, 'maintenance' => true );
if ( ! str_contains( cer_ticket_availability_markup( $ticket + array( 'tickets' => array( $ticket ) ) ), 'Scheduled for maintenance' ) ||
	cer_ticket_option_availability_suffix( $ticket, false ) !== ' - Scheduled for maintenance' ) {
	throw new RuntimeException( 'Maintenance display changed.' );
}

echo "Ticket availability markup thresholds passed.\n";
