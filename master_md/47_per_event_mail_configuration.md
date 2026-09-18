# Per-Event Mail Configuration Audit

## Summary
This change adds per-event SMTP configuration while keeping the legacy global fallback intact. The schema now stores event-specific sender email, encrypted password, and inferred SMTP details. The event form only asks for an email and app password; the rest is resolved automatically by a dedicated SMTP resolver and saved alongside the encrypted password.

## Schema change and version bump
The event table schema was extended in the plugin migration code and the version was bumped from `6` to `7` in the admin schema bootstrap. The new columns are added idempotently via `cer_add_column_if_missing()`, so existing databases are upgraded without duplication:

- `wp_evt_events.mail_sender_email`
- `wp_evt_events.mail_password_encrypted`
- `wp_evt_events.mail_smtp_host`
- `wp_evt_events.mail_smtp_port`
- `wp_evt_events.mail_smtp_secure`
- `wp_evt_events.mail_from_name`
- `wp_evt_events.mail_notification_email`

This logic lives in the schema bootstrap at `custom-event-registration.php` and runs through `cer_maybe_ensure_event_schema()`. The migration is idempotent and only adds missing columns.

## Domain-to-SMTP resolver
The resolver is implemented in `wp-content/plugins/custom-event-registration/includes/class-cer-mail-smtp-resolver.php` as `CerMailSmtpResolver::resolve()`. It accepts the sender email and defaults the display name to the site name or event name.

Provider table:

- Gmail / Google Workspace: `smtp.gmail.com`, `587`, `tls`
- Outlook / Microsoft 365: `smtp.office365.com`, `587`, `tls`
- Yahoo: `smtp.mail.yahoo.com`, `587`, `tls`
- Zoho: `smtp.zoho.com`, `587`, `tls`
- cPanel / hosting default: `mail.<domain>`, `587`, `tls`

Fallback rule: if the domain is unknown, it resolves to `mail.<domain>` on port `587` with `tls`.

Unit test evidence: `laravel-engine/tests/Unit/PerEventMailResolverTest.php` verifies the supported providers and the unknown-domain fallback. The test confirms the expected host, port, and secure mode for Gmail, Workspace aliasing, Outlook, Yahoo, Zoho, and cPanel-style domains.

## Admin UI
The event admin form adds a dedicated “Event Mail Settings” panel. It shows only:

- Sender Email
- App Password

The password field uses a JavaScript show/hide toggle with the Dashicons visibility states and does not submit the form. When the field is left untouched, the form shows the placeholder `••••••••` and the save logic preserves the existing encrypted password instead of overwriting it.

The implementation sanitizes the email with `sanitize_email()` and strips spaces from the password before encrypting, which avoids the common Gmail app-password failure mode caused by spaces in values like `abcd efgh ijkl mnop`.

Because the password is presented only as a password field and persisted encrypted via the existing security helper, it does not appear in the rendered form source or HTML output as plaintext.

## Mail sending paths
The actual ticket delivery path is the WordPress callback that runs after payment confirmation.

- Ticket email: `wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php`, inside `cer_send_ticket_for_registration()`.
- Admin receipt: the same file now also sends the admin notification via the same per-event mail configuration path. This was not present before, so it was built as part of this task.

The ticket email is triggered from the WordPress ticket callback after the payment confirmation state is accepted. The webhook in `laravel-engine/app/Http/Controllers/PaymentController.php` posts to WordPress with `cer_process_ticket`, then `cer_handle_ticket_request()` calls `cer_send_ticket_for_registration()`. That file sends the PDF attachment and then invokes the admin receipt sender.

## Mailer resolution logic
The mailer resolution is handled by `wp-content/plugins/custom-event-registration/includes/class-cer-mailer.php` and the resolver class above.

At send time, the event-specific configuration is resolved in this order:

1. Look up the event by the registration ID.
2. If `mail_sender_email` and `mail_password_encrypted` are present, decrypt the password in memory only and use the event sender email as the SMTP user and From address.
3. Use the stored `mail_from_name`, `mail_smtp_host`, `mail_smtp_port`, and `mail_smtp_secure` values if set, otherwise derive from the email domain and site name.
4. Fall back to the legacy global `CER_SMTP_*` constants if the event is not configured.

The same logic is used for both the attendee ticket and the admin receipt; both are dispatched through the same `wp_mail()` integration, so they share the same event-specific sender identity.

## Security notes
The encrypted password is stored through the existing `cer_encrypt_pii()` helper and decrypted only at send time. The code never logs the password. Failed sends log the event ID and sender address only, while the raw password stays out of logs and page output.

## Test evidence and status
The resolver unit test is in place and passes under PHPUnit:

- `php artisan test tests/Unit/PerEventMailResolverTest.php`

The codebase does not have a second live SMTP account or two test events configured for real outbound delivery in this environment, so the live-sending integration scenario could not be verified end-to-end without a real Gmail or Office 365 account. The implementation is present and the resolver/integration hooks are wired, but the requested real SMTP verification requires a second mailbox and valid app password.

## Paystack note
Paystack sends its own confirmation email to the customer independently of this implementation. That is a Paystack-side behavior and is outside the scope of this project. This task intentionally does not intercept or redirect Paystack’s customer emails; it only governs the two emails generated by the local event ticket workflow after the webhook confirms payment.
