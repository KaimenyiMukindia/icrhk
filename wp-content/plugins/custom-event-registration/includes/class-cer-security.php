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

function cer_normalize_mail_password( $value ): string {
	$value = trim( (string) $value );
	if ( preg_match( '/^[A-Za-z0-9]{4}(?: [A-Za-z0-9]{4}){3}$/', $value ) ) {
		return str_replace( ' ', '', $value );
	}

	return $value;
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

	$ciphertext = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$nonce = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$keys = array( cer_get_encryption_key() );
	if ( defined( 'AUTH_KEY' ) && AUTH_KEY !== $keys[0] ) {
		$keys[] = AUTH_KEY;
	}

	foreach ( $keys as $encryption_key ) {
		$key = hash_hkdf( 'sha256', $encryption_key, SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'cer-registration-pii' );
		$plaintext = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );
		if ( false !== $plaintext ) {
			return $plaintext;
		}
	}

	return '';
}

function cer_registration_search_hash( $value ): string {
	$value = strtolower( trim( (string) $value ) );
	$value = preg_replace( '/\s+/', ' ', $value );
	if ( '' === $value ) {
		return '';
	}

	return hash_hmac( 'sha256', $value, hash_hkdf( 'sha256', cer_get_encryption_key(), 32, 'cer-registration-search' ) );
}

function cer_prepare_registration_row( array $row ): array {
	foreach ( array( 'full_name', 'email', 'phone', 'notes' ) as $field ) {
		if ( isset( $row[ $field ] ) && '' !== (string) $row[ $field ] ) {
			if ( in_array( $field, array( 'full_name', 'email', 'phone' ), true ) ) {
				$row[ $field . '_search_hash' ] = cer_registration_search_hash( $row[ $field ] );
			}
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