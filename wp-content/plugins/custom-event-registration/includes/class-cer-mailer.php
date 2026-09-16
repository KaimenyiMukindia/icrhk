<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_configure_smtp( $phpmailer ): void {
	if ( ! defined( 'CER_SMTP_HOST' ) || ! defined( 'CER_SMTP_USERNAME' ) || ! defined( 'CER_SMTP_PASSWORD' ) ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host = CER_SMTP_HOST;
	$phpmailer->Port = defined( 'CER_SMTP_PORT' ) ? (int) CER_SMTP_PORT : 587;
	$phpmailer->SMTPSecure = defined( 'CER_SMTP_SECURE' ) ? CER_SMTP_SECURE : 'tls';
	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = CER_SMTP_USERNAME;
	$phpmailer->Password = str_replace( ' ', '', CER_SMTP_PASSWORD );
	$from = defined( 'CER_SMTP_FROM' ) ? CER_SMTP_FROM : CER_SMTP_USERNAME;
	$from_name = defined( 'CER_SMTP_FROM_NAME' ) ? CER_SMTP_FROM_NAME : 'ICRHK Events';
	$phpmailer->setFrom( $from, $from_name );
}

function cer_mail_from_address( string $from ): string {
	return defined( 'CER_SMTP_FROM' ) ? CER_SMTP_FROM : $from;
}

function cer_mail_from_name( string $from_name ): string {
	return defined( 'CER_SMTP_FROM_NAME' ) ? CER_SMTP_FROM_NAME : $from_name;
}