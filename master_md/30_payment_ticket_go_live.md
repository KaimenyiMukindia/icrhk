# Payment, Ticket, and Email Go-Live Audit

Date: 2026-08-27

## Configuration

- Laravel environment is set to `sandbox`.
- PesaPal base URL is `https://cybqa.pesapal.com/pesapalv3/api`.
- The documented sandbox consumer key and secret are configured locally in `laravel-engine/.env`; secrets are intentionally not repeated here.
- The callback URL is `https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn`.
- A fresh 32-byte base64 `CER_ENCRYPTION_KEY` was generated and configured identically in `wp-config.php` and `laravel-engine/.env`.
- `WORDPRESS_URL=http://localhost/icrhk` is used by Laravel to call the local WordPress ticket handler.
- The existing PesaPal notification ID remains configured. Automatic registration was not repeated because the existing ID is present.

## Schema

Live database: `icrhk`, prefix `wp_`.

Existing event tables: `wp_evt_events`, `wp_evt_pillars`, `wp_evt_registrations`, `wp_evt_speakers`, `wp_evt_sponsorships`, and `wp_evt_ticket_types`.

`wp_evt_registrations` already contained unique non-null `user_access_key`, nullable unique `payment_uuid`, `gateway_reference`, encrypted PII fields, and status/filter indexes. The following additive delivery fields were applied to the live table:

- `ticket_generated_at DATETIME NULL`
- `ticket_sent_at DATETIME NULL`

Laravel migration `2026_08_27_000001_create_payment_logs_table.php` was run successfully and created `payment_logs`.

## Implementation

- `includes/class-cer-ticket-service.php` generates a dynamic DomPDF ticket with attendee name, event title/date/venue, ticket type, access key, and an SVG QR code containing the access key.
- Tickets are stored in `wp-content/uploads/tickets/`.
- `?cer_ticket={user_access_key}` serves a paid ticket inline as a PDF.
- `wp_mail()` sends an HTML confirmation with the PDF attachment and download link.
- `ticket_generated_at` is recorded after PDF generation. `ticket_sent_at` is recorded only after `wp_mail()` returns success, allowing failed delivery to be retried without creating a new ticket.
- Laravel `PaymentController::ipn()` logs each IPN, recognizes confirmed PesaPal statuses, updates the shared registration, skips already-paid rows, and invokes the WordPress ticket callback.
- The WordPress admin top-level label is now `ICRHK Events`; the slug remains `cer-dashboard`.

## Verification Results

- Live MySQL schema inspection passed.
- Shared encryption key presence and length passed: Laravel sees 44 base64 characters and WordPress has the constant configured.
- PHP syntax checks passed for all modified PHP files.
- Composer installed `dompdf/dompdf` 3.1.6 and `endroid/qr-code` 5.0.9; Composer reported no security advisories.
- Apache served WordPress successfully at `http://localhost/icrhk/`.
- PesaPal sandbox authentication passed with the configured demo credentials.
- A controlled paid registration was inserted as live test record `id=10` using the published KAMGC event and `mukindiakaimenyi@gmail.com`.
- The ticket callback initially exposed missing GD support in XAMPP. The QR writer was changed from PNG to SVG, after which the callback returned HTTP 204.
- The generated ticket is a valid 22,483-byte PDF beginning with `%PDF-1.7`.
- The protected ticket URL returned HTTP 200 with `application/pdf`.
- Duplicate confirmed IPN requests returned `Already processed`; both requests were written to `payment_logs`, and no second ticket timestamp was created.

## Outstanding External Verification

`wp_mail()` did not report success on this XAMPP host, so `ticket_sent_at` remains null and receipt at `mukindiakaimenyi@gmail.com` has not been proven. The PDF generation and download path work. Actual email delivery requires a functioning local PHP mail/sendmail configuration or SMTP/transactional mail setup; no SMTP credentials were available or requested under the task constraints.

The Laravel PHPUnit executable is missing from `vendor/phpunit/phpunit`, so the Laravel test suite could not run even after `composer install`. This is a local dependency/vendor issue, separate from the runtime checks above.

M-Pesa and card checkout completion through the hosted PesaPal sandbox, including real sandbox tracking IDs and callbacks, were not claimed as complete because the email transport prerequisite failed and the supplied sandbox notification ID/checkout simulation could not be independently verified as a completed transaction in this run.
