# Event Registration Hardening Fixes

Date: 2026-09-02

## Scope

This hardening pass protects public event visibility, server-side registration integrity, capacity confirmation, encrypted registration search, ticket processing, and the PesaPal order configuration. WordPress remains the source of truth for events, registrations, inventory, and ticket delivery; Laravel confirms payments against the same database.

## Publication Gating

Files:

- `wp-content/plugins/custom-event-registration/custom-event-registration.php`
- `wp-content/plugins/custom-event-registration/includes/event-page-renderer.php`
- `wp-content/plugins/custom-event-registration/admin/events-list.php`

Public slug and renderer lookups now require `evt_events.status = published`. Unpublished clean URLs are marked as WordPress 404 responses and do not render the registration form.

The Events list provides a separate Preview action for draft, closed, and cancelled events. The preview URL targets the existing registration page with `event_id` and an event-specific `cer_event_preview` nonce. It is accepted only for authenticated users with `manage_options`. Preview responses have a separate object-cache key, preventing a private preview from being reused for a public request.

## Server-Side Ticket Validation

File:

- `wp-content/plugins/custom-event-registration/custom-event-registration.php`

The AJAX handler now loads the selected event and ticket using both `event_id` and `ticket_type_id`. It rejects unavailable/unpublished events and tickets that do not belong to the event. Ticket name and amount are derived from the stored ticket record; the submitted ticket name and amount are no longer trusted.

## Capacity and Inventory

Files:

- `wp-content/plugins/custom-event-registration/custom-event-registration.php`
- `laravel-engine/app/Http/Controllers/PaymentController.php`

Registration initiation performs an early availability check using the event paid-registration count and ticket `quantity_sold`.

The authoritative check occurs at paid confirmation. WordPress manual confirmation and Laravel IPN confirmation lock the registration, event, and ticket rows in a database transaction. The transition rejects a full event or sold-out ticket before it changes the registration status. On a successful first transition to `paid`, `evt_ticket_types.quantity_sold` is incremented exactly once. Duplicate confirmations detect the already-paid registration and do not increment inventory again.

If a gateway has already confirmed payment but capacity cannot be reserved, Laravel logs the condition for manual review and does not issue a ticket automatically.

## Encrypted Registration Search

Files:

- `wp-content/plugins/custom-event-registration/includes/class-cer-security.php`
- `wp-content/plugins/custom-event-registration/custom-event-registration.php`
- `wp-content/plugins/custom-event-registration/admin/registrations-list.php`

The selected approach is an exact-match keyed search index. PII remains encrypted with libsodium. New fields store HMAC-SHA256 hashes derived through a separate HKDF context:

- `full_name_search_hash CHAR(64)`
- `email_search_hash CHAR(64)`
- `phone_search_hash CHAR(64)`

Each field has a database index. Existing rows are backfilled by the idempotent schema migration, which decrypts the stored value and creates the hash. New registrations create hashes before encryption. The admin screen can search an exact name, email address, phone number, or `registration_uuid`; partial matching is intentionally unavailable so plaintext PII is never stored for search.

Schema version is now `5`.

## Ticket Processing Authorization

File:

- `wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php`

The `cer_process_ticket` action now requires one of two authorization paths:

- An authenticated administrator with `manage_options` and an event-specific `cer_process_ticket_{registration_id}` nonce.
- A signed Laravel callback. Laravel sends the registration ID, a timestamp, and an HMAC-SHA256 signature in `X-CER-Ticket-Timestamp` and `X-CER-Ticket-Signature`. WordPress accepts signatures only within five minutes.

Set the same high-entropy `CER_TICKET_CALLBACK_SECRET` in WordPress configuration and Laravel's untracked `.env`. Without this secret, Laravel ticket callbacks are rejected rather than accepted insecurely.

The attendee ticket download URL remains separate: it requires the registration's random `user_access_key` and a paid registration, and does not grant processing capability.

## PesaPal Configuration

Files:

- `laravel-engine/app/Services/Payment/PesaPalService.php`
- `laravel-engine/.env.example`

The implementation retains the verified integration contract:

- Token requests send consumer credentials as JSON, not HTTP Basic authentication.
- Order requests use bearer authorization, nested `billing_address`, `notification_id`, and normalized Kenyan mobile numbers.
- The PesaPal base URL, credentials, IPN ID, callback URL, SSL policy, and shared WordPress URL come from environment variables only.
- Sandbox orders use `PESAPAL_TEST_AMOUNT`; production orders use the validated server-side ticket amount and are no longer silently reduced to a test value.

Required untracked production values include `PESAPAL_ENV`, `PESAPAL_BASE_URL`, `PESAPAL_CONSUMER_KEY`, `PESAPAL_CONSUMER_SECRET`, `PESAPAL_IPN_NOTIFICATION_ID`, `PESAPAL_CALLBACK_URL`, `CER_ENCRYPTION_KEY`, and `CER_TICKET_CALLBACK_SECRET`.

The 2026-08-24 live audit proved PesaPal authentication, IPN registration, and hosted order creation. It did not prove delivery of an STK handset prompt. No new live payment was created in this hardening pass, because a test would initiate a real merchant transaction and requires an explicitly approved target number.

## Verification

- PHP syntax validation passed for all changed WordPress and Laravel PHP files.
- The schema migration was executed through WordPress and reports `cer_schema_version = 5`.
- The known published event URL `http://localhost/icrhk/event/kamgc-2026/` returned HTTP `200` and contained `cer-registration-form`.
- A request to `?cer_process_ticket=1` without administrator credentials, nonce, or signed callback was rejected with HTTP `403`.
- Local phone normalization converted `0712 345 678` to `254712345678`.
- A controlled draft-route check confirmed that the registration form is absent for a draft event. The event was immediately restored to `published`; environment-level connection handling prevented recording a definitive HTTP 404 response in that second request, so it should be rechecked in the deployed web-server environment.

## Deployment Checks

1. Set identical `CER_ENCRYPTION_KEY` and `CER_TICKET_CALLBACK_SECRET` values in WordPress configuration and Laravel `.env`.
2. Visit WordPress admin once after deployment so the idempotent version-5 migration completes and backfills search hashes.
3. Verify a draft event returns HTTP 404 at `/event/{slug}/` and that its authenticated Preview action still works.
4. Submit mismatched `event_id` and `ticket_type_id` values to `cer_submit_registration`; confirm rejection.
5. Confirm a full event and a sold-out ticket reject new payment initiation and paid confirmation.
6. Confirm an exact email, phone, name, and registration UUID lookup works in Registrations; confirm partial PII search does not return results.
7. Confirm unsigned ticket processing returns HTTP 403, then confirm a configured signed Laravel callback creates and sends a ticket.
8. Use an explicitly approved non-production payment test path to validate the merchant's current PesaPal mobile-payment entitlement and callback delivery.
