<?php

require dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! function_exists( 'cer_unwrap_event_header_container' ) ) {
	fwrite( STDERR, "Custom Event Registration is not loaded.\n" );
	exit( 1 );
}

function cer_test_expect( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$header_id = 900001;
$footer_id = 900002;
$meta_filter = function ( $value, $post_id, $meta_key ) use ( $header_id, $footer_id ) {
	if ( '_elementor_template_type' === $meta_key ) {
		if ( $header_id === $post_id ) {
			return array( 'header' );
		}
		if ( $footer_id === $post_id ) {
			return array( 'footer' );
		}
	}
	return $value;
};
add_filter( 'get_post_metadata', $meta_filter, 10, 3 );

$inner = array(
	'id'       => '6423679',
	'elType'   => 'container',
	'elements' => array(),
);
$outer = array(
	'id'       => '48909e04',
	'elType'   => 'container',
	'elements' => array( $inner ),
);
$data = array( $outer );
$cache_ttl = get_option( 'elementor_element_cache_ttl', '' );

try {
	cer_test_expect( array( $inner ) === cer_unwrap_event_header_container( $data, $header_id ), 'The event header was not unwrapped.' );
	cer_test_expect( 'disable' === get_option( 'elementor_element_cache_ttl', '' ), 'The shared element cache was not bypassed.' );
	cer_test_expect( 'content' === cer_restore_element_cache( 'content' ), 'Restoring the cache changed the rendered content.' );
	cer_test_expect( $cache_ttl === get_option( 'elementor_element_cache_ttl', '' ), 'The element cache was not restored.' );

	$other_outer = $data;
	$other_outer[0]['id'] = 'another';
	$other_inner = $data;
	$other_inner[0]['elements'][0]['id'] = 'another';
	$extra_child = $data;
	$extra_child[0]['elements'][] = $inner;

	foreach ( array( $other_outer, $other_inner, $extra_child ) as $unrelated ) {
		cer_test_expect( $unrelated === cer_unwrap_event_header_container( $unrelated, $header_id ), 'An unrelated header was changed.' );
		cer_test_expect( false === has_filter( 'pre_option_elementor_element_cache_ttl', 'cer_disable_element_cache' ), 'The cache was bypassed for an unrelated header.' );
	}
	cer_test_expect( $data === cer_unwrap_event_header_container( $data, $footer_id ), 'A non-header template was changed.' );
	cer_test_expect( 'invalid' === cer_unwrap_event_header_container( 'invalid', $header_id ), 'Invalid data was changed.' );
	echo "Event header tests passed.\n";
} finally {
	cer_restore_element_cache( '' );
	remove_filter( 'get_post_metadata', $meta_filter, 10 );
}
