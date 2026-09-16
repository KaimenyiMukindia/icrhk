# Go-Live Phase 1 Audit

Date: 2026-08-27

## Registration Data Model

- `wp_evt_registrations.user_access_key` is `VARCHAR(64) NOT NULL` with a unique BTREE index. New keys use 32 cryptographically secure random bytes encoded as 64 hexadecimal characters.
- `user_id` was added as a nullable WordPress-user link.
- Existing `payment_uuid` and `gateway_reference` columns were added to the live table because the registration flow already writes them.
- Existing `ticket_type` was retained for backward compatibility. New filtering and display should use `ticket_type_id` and its ticket-type relationship; old values remain available for legacy rows.
- The registration-to-event foreign key is now `cer_registrations_event_fk` with `ON DELETE RESTRICT`. Ticket types retain `ON DELETE SET NULL` to preserve registration history.

## Encryption

Registration `full_name`, `email`, `phone`, and `notes` use versioned libsodium `secretbox` encryption. The stored value begins with `cer:v1:` and contains a base64-encoded nonce and authenticated ciphertext. The key is derived with HKDF-SHA256 using the `CER_ENCRYPTION_KEY` environment/config value and the context `cer-registration-pii`; WordPress falls back to `AUTH_KEY` for backward-compatible local operation.

Laravel uses the same algorithm and context through `RegistrationCrypto`. Production must set the same high-entropy `CER_ENCRYPTION_KEY` in Laravel `.env` and WordPress configuration. The key must not be committed to source control or stored in WordPress options.

Plaintext legacy values remain readable by the compatibility decryptor, and the schema migration encrypted all current rows. Encrypted columns were widened to avoid truncating ciphertext.

## Payment Settings

The WordPress Payment Settings submenu, renderer, option reads, and option writes were removed. The legacy `cer_payment_settings` option was deleted from `wp_options`. PesaPal credentials continue to be read only from Laravel environment variables (`PESAPAL_CONSUMER_KEY`, `PESAPAL_CONSUMER_SECRET`, `PESAPAL_BASE_URL`, `PESAPAL_CALLBACK_URL`, and related settings).

## Migration Applied

The plugin schema hook advanced `cer_schema_version` to `4`. It added access keys, the nullable user link, payment columns, widened encrypted fields, backfilled existing keys and PII, added the unique key index, and replaced the event foreign key.

## Validation Results

- Live database: `wp_` prefix and `icrhk` database confirmed from `wp-config.php`.
- Existing registrations: 8 rows inspected; all 8 have unique 64-character access keys.
- Existing PII: all 8 names, emails, and phones have the `cer:v1:` ciphertext marker.
- Encryption round trip: a real temporary registration insert stored ciphertext and a 64-character key; the raw row decrypted to `roundtrip@example.com` and `+254711111111`; the test row was deleted afterward.
- Foreign key: live `SHOW CREATE TABLE` confirms `cer_registrations_event_fk` references `wp_evt_events(id)` without a delete action, which is MySQL `RESTRICT` behavior.
- Payment option: `cer_payment_settings` returned no row after cleanup.
- PHP syntax: all touched WordPress and Laravel PHP files passed `php -l`.

## Remaining Go-Live Checks

The external PesaPal request was not executed during this local validation because it requires production credentials and callback configuration. Before deployment, set and verify the shared encryption key in both runtimes, then exercise the front-end payment flow and confirm the Laravel endpoint returns its normal redirect response.