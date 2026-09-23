# Mail Fix Audit: Implementation and Anti-Patterns

## Purpose
This document captures the actual delivery implementation that exists in the project and records all mail-fix attempts discussed in this thread as an audit trail of what not to do.

The scope is intentionally narrow: SMTP configuration, ticket email dispatch, and the repair path for real live-provider failures. It is not a claim that delivery is complete; it is a record of the implementation and the mistakes that were ruled out.

---

## What the project currently implements

### 1. WordPress mail hook registration
The plugin bootstraps the mail pipeline through the WordPress `phpmailer_init` hook in:

- `wp-content/plugins/custom-event-registration/custom-event-registration.php`

This is the runtime integration point at which PHP mail is converted into the provider-specific SMTP connection configuration.

### 2. Event-specific SMTP configuration
The active implementation resolves the mail sender, encrypted password, host, port, and secure mode from the event row before sending:

- `wp-content/plugins/custom-event-registration/includes/class-cer-mailer.php`

Key logic:
- `cer_get_mail_config_from_event()`
- `cer_set_current_mail_config()`
- `cer_clear_current_mail_config()`
- `cer_configure_smtp()`
- `cer_send_admin_receipt_for_registration()`

The configuration follows this order:
1. read the event record;
2. decrypt the stored password in memory only;
3. resolve host/port/secure from saved values or fallback resolver;
4. use the event sender as the SMTP user and `From` address;
5. fall back to legacy global constants only when the event is not configured.

### 3. Per-event SMTP resolver
The provider inference logic is stored in:

- `wp-content/plugins/custom-event-registration/includes/class-cer-mail-smtp-resolver.php`

It resolves host/port/security from the sender address domain, with specific rules for:
- Gmail / Google Workspace
- Outlook / Microsoft 365
- Yahoo
- Zoho
- cPanel hosting default

This resolver is a fallback mechanism only. It does not change the password; it only picks the transport details.

### 4. Encrypted secret storage and normalization
The admin form stores the event mail password encrypted, not in plaintext, and preserves the current value when the field is left alone:

- `wp-content/plugins/custom-event-registration/admin/event-form.php`

The save path must keep the current password if the field is unchanged; otherwise it should replace the value only when the operator intentionally edits it.

The security layer is in:

- `wp-content/plugins/custom-event-registration/includes/class-cer-security.php`

This layer round-trips encrypted values and uses the current key with a legacy fallback when needed.

### 5. Ticket and admin mail delivery
The send path is driven from the WordPress ticket callback after payment confirmation:

- `wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php`

This file sends:
- attendee ticket email with PDF attachment;
- admin notification email for the registration event;
- the same event-scope SMTP identity used during the callback.

### 6. Runtime SMTP verification controls
The current code includes the PHPMailer `SMTPOptions` configuration as the actual runtime gate for certificate verification behavior:

```php
$smtp_options = array(
    'ssl' => array(
        'verify_peer' => defined( 'CER_SMTP_VERIFY_PEER' ) ? (bool) CER_SMTP_VERIFY_PEER : true,
        'verify_peer_name' => defined( 'CER_SMTP_VERIFY_PEER_NAME' ) ? (bool) CER_SMTP_VERIFY_PEER_NAME : true,
        'allow_self_signed' => defined( 'CER_SMTP_ALLOW_SELF_SIGNED' ) ? (bool) CER_SMTP_ALLOW_SELF_SIGNED : false,
    ),
);
if ( defined( 'CER_SMTP_SSL_OPTIONS' ) && is_array( CER_SMTP_SSL_OPTIONS ) ) {
    $smtp_options = array_replace_recursive( $smtp_options, CER_SMTP_SSL_OPTIONS );
}
$phpmailer->SMTPOptions = $smtp_options;
```

This is the correct hook point for a live TLS verification mismatch. It preserves current behavior by default and allows the operator to override the same source used for the general SMTP settings.

---

## What was attempted and why it failed or was rejected

### Attempt 1: Hardcode SMTP into the global WordPress config
This was a common early path and is deliberately not the preferred implementation.

Why it was rejected:
- it bypassed the event-specific sender configuration;
- it mixed runtime transport defaults with environment-specific overrides;
- it made the send path harder to debug when the live provider rejected TLS verification;
- it created maintenance drift between global config and per-event configuration.

Rule: do not hardcode live SMTP credentials into site-wide constants unless the operator intentionally overrides the default for a live deployment and the same override is still sourced from the normal SMTP config control surface.

### Attempt 2: Trust localhost mailer behavior as proof of production health
The environment showed that local host mail sending and `wp_mail()` on XAMPP does not prove the live provider path works.

Why it was rejected:
- the local stack can succeed or fail independently of the real provider and TLS chain;
- a message accepted on localhost does not imply the event SMTP configuration is valid for Gmail, Microsoft, or another hosted provider;
- the actual failure occurred after real payment flow reached the code path and the SMTP provider rejected the connection or credentials.

Rule: do not treat localhost mail success as evidence of live delivery success.

### Attempt 3: Strip spaces from all passwords without checking provider rules
This was one of the most explicit anti-patterns captured in the thread.

Bad implementation pattern:
- `str_replace( ' ', '', $submitted_mail_password )`
- repeated stripping before assigning PHPMailer credentials

Why it was rejected:
- some provider or user secrets legitimately contain spaces or symbols;
- the wrong normalization changed the credential and produced a false-authentication error;
- the fix was not just cosmetic; it created a real protocol bug.

The correct behavior is to trim only obvious formatting noise, then preserve the actual secret unless a specific provider format requires normalization.

### Attempt 4: Assume the issue is only “bad credentials” without checking the certificate policy
This was another critical mistake in the thread.

Why it was rejected:
- the endpoint, port, username, and TLS mode could resolve correctly while the real provider still rejected the TLS chain or CN verification;
- the failure mode matched a certificate-verification mismatch, not just a wrong username/password;
- the correct fix was in the runtime `SMTPOptions` stack, not in the event schema or deployment structure alone.

Rule: do not assume a provider rejection means the credential is wrong when the socket TLS verification settings are still defaulting to an unsafe or mismatched policy.

### Attempt 5: Adding a new source of truth instead of using the existing SMTP configuration path
This was explicitly prohibited in the task constraints.

Rejected changes:
- new `.env` storage for mail transport;
- new mu-plugin for SMTP override;
- new file or storage path for credentials;
- a new screen or config surface;
- a resolver rewrite unrelated to the existing event settings.

Why it was rejected:
- it would have created a second source of truth;
- it violates the “default must preserve current behavior” rule;
- it makes operator configuration inconsistent with the existing mail fields and event records.

Rule: keep the mail transport override inside the same source as the other SMTP values.

### Attempt 6: Changing WordPress core or treating core files as the place for live configuration
This was disallowed and a lasting cautionary point.

Why it was rejected:
- WordPress core is not the place for project-specific SMTP overrides;
- it creates a fragile deployment and makes upgrades harder;
- it violates the requirement to preserve the existing WordPress installation and not replace core files.

Rule: do not edit core to fix shipping logic; fix the plugin layer and keep the site core intact.

### Attempt 7: Claiming success before runtime proof
This thread repeatedly stressed evidence-first validation.

Why it was rejected:
- a code diff or a lint pass is not proof of successful delivery;
- a successful local callback path to WordPress is not proof of real SMTP completion;
- a payment success cannot be reported as a mail fix without confirming the actual `wp_mail()` result and the ticket email delivery.

Rule: no fix is considered complete until the live mail path is proven in the target environment.

### Attempt 8: Pushing to the wrong branch or treating deployment branch names as minor details
This was a deployment risk and should remain in the audit record.

Why it was rejected:
- branch drift creates confusion between local debugging and live deployment;
- the correct active branch was explicitly required for the live push path.

Rule: always test and validate on the right branch before deployment.

---

## Correct implementation guardrails
The following constraints are the working rules for this project:

1. Keep the default behavior unchanged unless the operator explicitly overrides it.
2. Use the already-existing event SMTP configuration path; do not create new storage or new UI.
3. Keep PHP mailer configuration inside the plugin hook, not in core files.
4. Set `SMTPOptions` for live certificate verification on the actual transport, not by guessing at deployment assumptions.
5. Never strip arbitrary internal spaces from credentials unless a provider-specific format requires it.
6. Do not claim success without a runtime “real SMTP send” proof in the deployed state.
7. Keep the WordPress core and standard install intact; only change the custom plugin data path.

---

## Final audit conclusion
The preferred delivery path is now a narrow transactional API adapter at WordPress's `pre_wp_mail` boundary. It sends the existing HTML and PDF attachment through Resend when `CER_RESEND_API_KEY` and `CER_RESEND_FROM` are configured, and keeps the repaired SMTP path as fallback when they are not. The payment, PDF, encryption, and idempotency paths are unchanged.

The adapter was exercised locally with a stubbed WordPress HTTP client. It produced an accepted Resend payload containing the verified sender, recipient, reply-to address, HTML body, and ticket attachment. This is payload validation only; a live Resend acceptance still requires deployment with a real API key and verified sender domain.

## Root cause found after this audit was written

The resolver contract was broken in the deployed code. `CerMailSmtpResolver::resolve()` returned only `from_email` and `from_name`, while `class-cer-mailer.php` consumed `host`, `port`, and `secure` from the same result. When an event did not already contain saved transport values, the mailer therefore had no host and returned before attempting authentication. A known-good credential could not work because it was never reaching a valid SMTP transport.

The admin event save path had a second related defect: it referenced an undefined `$mail_resolver` and used blank event transport values instead of resolver fallbacks. The repair restores the resolver transport tuple and persists resolver values when the event fields are blank.

Focused validation after the repair resolved:

```text
organizer@gmail.com => smtp.gmail.com:587 tls
hello@outlook.com => smtp.office365.com:587 tls
tickets@mydomain.org => mail.mydomain.org:587 tls
```

PHP lint passed for the resolver, event form, and mailer. This validates configuration resolution, not external mailbox delivery; the deployed live smoke test remains the proof of end-to-end mail acceptance.

The real lesson for this thread is:
- do not assume the provider is the problem without checking the TLS verification policy;
- do not invent a second config surface;
- do not normalize user secrets destructively;
- do not claim success until the live email actually leaves the system.

This document exists as the audit record of those failed and rejected attempts so the team can avoid repeating them.
