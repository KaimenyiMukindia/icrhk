<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_get_encryption_key(): string {
	$key = defined( 'CER_ENCRYPTION_KEY' ) ? CER_ENCRYPTION_KEY : getenv( 'CER_ENCRYPTION_KEY' );
	if ( ! is_string( $key ) || '' === $key ) {
		$key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
	}

	return $key;
}

function cer_encrypt_pii( $value ): string {
	$value = (string) $value;
	if ( '' === $value || ! function_exists( 'sodium_crypto_secretbox' ) ) {
		return $value;
	}

	$key = hash_hkdf( 'sha256', cer_get_encryption_key(), SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'cer-registration-pii' );
	$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$ciphertext = sodium_crypto_secretbox( $value, $nonce, $key );

	return 'cer:v1:' . base64_encode( $nonce . $ciphertext );
}

function cer_decrypt_pii( $value ): string {
	$value = (string) $value;
	if ( 0 !== strpos( $value, 'cer:v1:' ) || ! function_exists( 'sodium_crypto_secretbox_open' ) ) {
		return $value;
	}

	$decoded = base64_decode( substr( $value, 7 ), true );
	if ( false === $decoded || strlen( $decoded ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
		return '';
	}

	$key = hash_hkdf( 'sha256', cer_get_encryption_key(), SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'cer-registration-pii' );
	$plaintext = sodium_crypto_secretbox_open( substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ), substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ), $key );

	return false === $plaintext ? '' : $plaintext;
}

function cer_prepare_registration_row( array $row ): array {
	foreach ( array( 'full_name', 'email', 'phone', 'notes' ) as $field ) {
		if ( isset( $row[ $field ] ) && '' !== (string) $row[ $field ] ) {
			$row[ $field ] = cer_encrypt_pii( $row[ $field ] );
		}
	}

	if ( empty( $row['user_access_key'] ) ) {
		$row['user_access_key'] = bin2hex( random_bytes( 32 ) );
	}

	return $row;
}

function cer_decrypt_registration_row( $row ) {
	if ( is_object( $row ) ) {
		foreach ( array( 'full_name', 'email', 'phone', 'notes' ) as $field ) {
			if ( isset( $row->$field ) ) {
				$row->$field = cer_decrypt_pii( $row->$field );
			}
		}
	} elseif ( is_array( $row ) ) {
		foreach ( array( 'full_name', 'email', 'phone', 'notes' ) as $field ) {
			if ( isset( $row[ $field ] ) ) {
				$row[ $field ] = cer_decrypt_pii( $row[ $field ] );
			}
		}
	}

	return $row;
}