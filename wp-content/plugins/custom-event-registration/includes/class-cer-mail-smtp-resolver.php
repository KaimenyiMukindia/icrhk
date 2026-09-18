<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CerMailSmtpResolver {
	public static function resolve( string $email, string $default_from_name = '' ): array {
		$email = trim( $email );
		$domain = strtolower( ltrim( strrchr( $email, '@' ) ?: '', '@' ) );
		$site_name = trim( (string) $default_from_name );
		if ( '' === $site_name ) {
			$site_name = get_option( 'blogname', 'ICRHK Events' );
		}
		if ( '' === $site_name ) {
			$site_name = 'ICRHK Events';
		}

		$host = 'mail.' . $domain;
		$port = 587;
		$secure = 'tls';
		$known = self::known_provider_map();
		if ( isset( $known[ $domain ] ) ) {
			$host = $known[ $domain ]['host'];
			$port = $known[ $domain ]['port'];
			$secure = $known[ $domain ]['secure'];
		}

		return array(
			'host' => $host,
			'port' => (int) $port,
			'secure' => $secure,
			'from_email' => $email,
			'from_name' => $site_name,
		); 
	}

	public static function known_provider_map(): array {
		return array(
			'gmail.com' => array( 'host' => 'smtp.gmail.com', 'port' => 587, 'secure' => 'tls' ),
			'googlemail.com' => array( 'host' => 'smtp.gmail.com', 'port' => 587, 'secure' => 'tls' ),
			'outlook.com' => array( 'host' => 'smtp.office365.com', 'port' => 587, 'secure' => 'tls' ),
			'live.com' => array( 'host' => 'smtp.office365.com', 'port' => 587, 'secure' => 'tls' ),
			'microsoft.com' => array( 'host' => 'smtp.office365.com', 'port' => 587, 'secure' => 'tls' ),
			'icloud.com' => array( 'host' => 'smtp.mail.me.com', 'port' => 587, 'secure' => 'tls' ),
			'yahoo.com' => array( 'host' => 'smtp.mail.yahoo.com', 'port' => 587, 'secure' => 'tls' ),
			'yahoo.co.ke' => array( 'host' => 'smtp.mail.yahoo.com', 'port' => 587, 'secure' => 'tls' ),
			'zoho.com' => array( 'host' => 'smtp.zoho.com', 'port' => 587, 'secure' => 'tls' ),
			'zohomail.com' => array( 'host' => 'smtp.zoho.com', 'port' => 587, 'secure' => 'tls' ),
		);
	}
}
