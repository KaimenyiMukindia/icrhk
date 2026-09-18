# Password Normalization Fix

## Symptom

The event ticket callback reached WordPress and generated the PDF, but `wp_mail()` returned false with `SMTP Error: Could not authenticate`. The host, port, username, and TLS mode resolved to Gmail correctly.

Paystack customer receipts are separate and remain outside this implementation's control.

## Research

Google's official app-password documentation says app passwords require 2-Step Verification and are displayed as a 16-character passcode. A displayed grouped value may contain spaces; those display separators are not part of the SMTP secret. Other providers can use passwords of arbitrary length and may legitimately allow spaces or symbols.

Reference: https://support.google.com/accounts/answer/185833

## Audit Findings

The admin save path in `admin/event-form.php` previously used `str_replace( ' ', '', $submitted_mail_password )` for every provider. This was too broad: it changed legitimate custom passwords containing spaces. The mailer also stripped spaces again immediately before assigning `$phpmailer->Password`.

The encryption path in `includes/class-cer-security.php` is a faithful authenticated-encryption round trip. It does not change the plaintext. A compatibility fallback also tries the legacy `AUTH_KEY` when current-key decryption fails.

The SMTP resolver only selects host, port, and security mode. It does not modify passwords.

## Stored Credential Evidence

For event `2` (`kaimenyi`), the credential was present and decrypted successfully. The diagnostic script reported:

```text
length=20
bytes=77,117,116,101,109,98,101,105,77,117,114,105,116,104,105,57,57,56,56,64
normalized_length=20
round_trip_hash=66cb0ba6c15d7a9a93a5a6533b334eb8d2f9d5aefa4c52762f724b023a0359ee
```

The byte sequence contains no whitespace. Encryption/decryption therefore did not corrupt or expand the credential. It is a 20-byte value supplied by the user, not a spaced Gmail display value.

The diagnostic command used was:

```powershell
php -r "require 'wp-load.php'; global `$wpdb; `$raw=`$wpdb->get_var(`$wpdb->prepare('SELECT mail_password_encrypted FROM '.`$wpdb->prefix.'evt_events WHERE id=%d',2)); `$value=cer_decrypt_pii(`$raw); echo 'length='.strlen(`$value).PHP_EOL; echo 'bytes='.implode(',',array_map('ord',str_split(`$value))).PHP_EOL;"
```

The actual password and its reversible base64 form are intentionally not stored in this document or exposed in the response.

## Fix

`cer_normalize_mail_password()` now:

- trims leading and trailing whitespace for all providers;
- removes internal spaces only when the complete value matches four groups of four alphanumeric characters;
- preserves arbitrary internal spaces, symbols, and provider-specific lengths otherwise.

The normalized value is encrypted at save time. The mailer now assigns the decrypted value to PHPMailer without any send-time normalization. The same rule applies to the legacy constant fallback.

Files changed:

- `wp-content/plugins/custom-event-registration/includes/class-cer-security.php`
- `wp-content/plugins/custom-event-registration/admin/event-form.php`
- `wp-content/plugins/custom-event-registration/includes/class-cer-mailer.php`
- `wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php`

No fixed password-length validation remains in the event mail path.

## Diagnostics

When `WP_DEBUG` is enabled, `phpmailer_init` logs the SMTP username, password length, and base64 diagnostic of the value assigned to PHPMailer. This project currently has `WP_DEBUG=false`, so the live retry did not persist a reversible password diagnostic. The failure logger now also records PHPMailer `ErrorInfo` when WordPress supplies it in either supported error-data shape.

## Tests

`CerMailPasswordTest` verifies:

- `abcd efgh ijkl mnop` becomes `abcdefghijklmnop`;
- leading/trailing whitespace is removed from the grouped form;
- an ungrouped value is unchanged;
- `my pass with spaces!` retains its internal spaces;
- normalized and custom passwords survive encrypt/decrypt byte-for-byte.

Focused result: 2 tests passed.

## Live Retry

Registration `126` remains `paid` and its ticket PDF is generated, but `ticket_sent_at` remains null. After the exact stored 20-byte credential was passed unchanged to PHPMailer, Gmail still returned:

```text
SMTP Error: Could not authenticate.
```

This confirms the implementation is no longer changing the credential in transit. The remaining rejection is at Gmail authentication, consistent with the stored value not being accepted as the credential for `emailyanetflix@gmail.com` (for example, it may be an account password rather than an SMTP/app credential, or may be subject to account security policy). No delivery success is claimed.

## Paystack Cardholder Name Audit

The latest successful card payment was registration `128`. Its webhook payload had an empty `authorization.account_name` and empty customer first/last name. A live Paystack transaction verification request was then made using the transaction reference; the verified response also had no cardholder name.

Because Paystack supplied no holder name, `payer_name` was not populated and the ticket correctly used the submitted attendee name. The record was paid and the ticket was sent successfully.

The webhook now performs this verification fallback for future successful card payments when the webhook does not include a holder name. If Paystack returns `authorization.account_name` or a customer first/last name, the existing fulfillment update encrypts that name into both `payer_name` and `full_name`; ticket generation reads the updated `full_name`, so the holder name appears on the ticket. If both Paystack responses omit it, the submitted attendee name remains unchanged.
