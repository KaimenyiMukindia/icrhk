# End-to-End Payment, Ticket, and Mail Audit

Date: 2026-08-27

## Implementation

- Added Gmail SMTP configuration through `phpmailer_init` in `includes/class-cer-mailer.php`.
- SMTP values are stored as WordPress configuration constants, not in plugin source. The password is intentionally redacted from this document.
- Configured sender identity, TLS on port 587, SMTP authentication, and Gmail host.
- Added WordPress sender filters so the invalid local `wordpress@localhost` default cannot reach PHPMailer.
- Gmail app-password display spaces are removed before authentication.
- Existing ticket generation remains dynamic and uses DomPDF plus an SVG QR code containing `user_access_key`.
- Existing idempotency remains based on `ticket_sent_at`; generated PDFs can be retried without creating a new ticket.

## Live Checks

- PHP syntax checks passed for the modified mailer, plugin bootstrap, and WordPress configuration.
- Existing paid registration `id=10` was used as real test data for `mukindiakaimenyi@gmail.com`.
- Ticket callback returned HTTP 204.
- PDF generation completed and `ticket_generated_at` was updated.
- Gmail TLS connection reached the SMTP server but returned `SMTP Error: Could not authenticate`.
- Gmail SSL fallback on port 465 was also tested and returned `SMTP Error: Could not authenticate`.
- `ticket_sent_at` remains NULL; no delivery claim is made.
- Apache logged the PHPMailer failure without exposing the password.

## Paystack Migration Status (2026-09-05)

The payment surface has been migrated from PesaPal to Paystack. WordPress now submits one shared registration form containing the attendee name, email, phone, ticket, and selected method. Laravel initializes Paystack transactions in KES subunits, returns an `access_code` and `reference`, and the frontend opens Paystack InlineJS v2 in the same page. Card data and M-Pesa confirmation data remain inside Paystack.

The Paystack webhook endpoint is `POST /laravel-engine/public/api/paystack-webhook`. It validates the `x-paystack-signature` HMAC-SHA512 header, processes `charge.success`, verifies the lower-denomination amount against the registration before fulfillment, writes `payment_logs`, locks the registration and ticket rows, ignores duplicate confirmations, and invokes the existing signed WordPress ticket callback. The browser also sends the reference to `POST /api/payment/verify` for immediate status feedback. InlineJS `resumeTransaction(access_code)` does not expose the `newTransaction` callback options, so the browser uses bounded verification polling while the webhook remains authoritative for ticket delivery.

The official Paystack documentation does not provide a public `secure.js` browser tokenization API for this project. Its custom Card API sends card details to Paystack and is restricted to businesses with current PCI-DSS compliance documentation. Therefore, the implementation does not collect or forward raw card fields through WordPress or Laravel. Card payments use Paystack's compliant InlineJS checkout; M-Pesa uses the direct Charge API with `mobile_money.provider=mpesa` and the normalized `+254` phone number.

Required Laravel environment values are `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY`; credentials are not stored in source. Configure the webhook URL in the Paystack dashboard and use `channels: ['card', 'mobile_money']` for Kenya.

Implementation files:

- `laravel-engine/app/Services/Payment/PaystackService.php`
- `laravel-engine/app/Http/Controllers/PaymentController.php`
- `laravel-engine/routes/api.php`
- `wp-content/plugins/custom-event-registration/includes/registration-functions.php`
- `wp-content/plugins/custom-event-registration/assets/js/cer-registration.js`

No sandbox transaction, webhook delivery, ticket email, or duplicate-webhook result is claimed in this audit yet. Those require real Paystack test credentials, a publicly reachable HTTPS webhook, and user confirmation of both the Card and M-Pesa email receipts. The master checklist therefore remains intentionally unchecked.

## Paystack Sandbox Execution (2026-09-05)

- Paystack test credentials were configured in the ignored Laravel `.env`; the values are intentionally omitted from this audit.
- The public ngrok health endpoint returned HTTP 200.
- Laravel migrations were run and created the `payment_logs` table, which was missing before execution.
- Paystack authentication and transaction initialization succeeded with the supplied test key.
- A real WordPress registration was submitted through the KAMGC form for `mukindiakaimenyi@gmail.com`, Student / Young Professional, KES 2,500, using the documented Paystack M-Pesa sandbox number `+254710000000`.
- Registration `id=3` was created with an encrypted registration payload, a unique `user_access_key`, status `pending`, and gateway reference `61efe06a-1d8b-40d7-8ed4-67cf9c12e06f`.
- Paystack verification returned `success`, KES amount `250000` in subunits, and the same reference.
- A signed webhook replay based on the real Paystack verification payload returned HTTP 200 and changed registration `id=3` to `paid` with `confirmed_amount=2500.00`.
- The existing signed WordPress ticket callback returned HTTP 204 and generated a PDF at `wp-content/uploads/tickets/ticket-0a0046979cb04024a39e0509d8c256190d9208c317a11b0e454443fafa950bb6.pdf`.
- Replaying the same signed webhook returned `Already processed`; `payment_logs` recorded `webhook_duplicate_ignored`, and no second ticket was generated.
- `ticket_sent_at` remains null. WordPress logged `CER wp_mail failed: Invalid address: (From): wordpress@localhost`; no email receipt is claimed.
- The supplied phone `254719763089` was rejected by Paystack test mode as expected for a non-fixture number. The official test fixture `+254710000000` completed successfully.
- The Card browser attempt did not create a registration or transaction, so no Card payment, webhook, ticket email, or receipt is claimed.

The payment and webhook path is partially verified with real Paystack data. The master checklist remains incomplete pending a successful Card sandbox transaction, working SMTP configuration, and user confirmation of both ticket emails.

## Email and Card Completion Run (2026-09-05)

- Gmail SMTP was configured through the existing `phpmailer_init` hook in `wp-config.php` using `smtp.gmail.com`, TLS, port 587, and the supplied sender account. The password is not repeated here.
- Retrying the existing paid M-Pesa registration `id=3` returned HTTP 204 and set `ticket_sent_at`; the ticket email was accepted by PHPMailer for `mukindiakaimenyi@gmail.com`.
- A fresh Card registration was created through the live KAMGC form as registration `id=5`, with encrypted data, a unique access key, and reference `b96d0791-c09e-4941-b18f-8e35ad93a393`.
- Paystack InlineJS opened the hosted Card checkout. The supplied test card `4111 1111 1111 1111`, expiry `01/30`, and CVV `123` produced the Paystack test result `Payment Successful`.
- Paystack verification returned `success` and amount `250000` subunits for the Card reference.
- A signed Card webhook replay returned HTTP 200 and changed registration `id=5` to `paid`. The signed WordPress ticket callback returned HTTP 204; `ticket_generated_at` and `ticket_sent_at` were then set for registration `id=5`.
- Replaying the same Card webhook returned `Already processed` with HTTP 200, confirming duplicate fulfillment protection.

Both payment paths now have real sandbox payment, webhook, ticket PDF, and SMTP acceptance evidence. Email inbox receipt still requires explicit user confirmation before the master checklist can be marked complete.

## UI Separation and Sandbox Amount Update (2026-09-06)

- Added `PAYSTACK_ENV=sandbox` to Laravel configuration. Card initialization and direct M-Pesa charges now send KES 1 (`100` subunits) to Paystack in sandbox while retaining the original ticket price in `wp_evt_registrations`.
- Card selection now initializes the transaction from the already-filled shared fields, hides the main registration form after the bridge responds, and exposes one separate `Pay with Card` action that resumes the preloaded Paystack checkout.
- Card preload failures remain on the page with a retry action. M-Pesa keeps the registration form visible and polls registration status after the direct Charge API response.
- The direct M-Pesa bridge returned `status=pending`, the submitted reference, and `display_text=Approve the payment request on your phone.` for the documented sandbox fixture `254710000000`; the response is accepted as a successful initiation rather than HTTP 422.
- Browser verification confirmed the Card preload surface displays only after the backend returns an access code; the main form computed style changes to `display: none` and the standalone Card action becomes visible.

### M-Pesa "Charge attempted" Bridge Fix (2026-09-06)

Paystack can return a successful top-level Charge API response with the message `Charge attempted` while the nested charge status is not yet final. Laravel now treats any successful response with a usable reference as an initiated payment, normalizes non-final states to `pending`, and returns HTTP 200 to WordPress. A browser retry now displays `Approve the M-Pesa payment request on your phone.` instead of the previous HTTP 422 error.

### Card and M-Pesa Surface Separation (2026-09-06)

The Card view now hides the registration form and M-Pesa phone field after the backend returns the Paystack access code. It exposes a reserved in-flow Card panel with `Pay with Card` and `Back to M-Pesa` actions. The M-Pesa view keeps the phone field and form visible. Paystack's official `resumeTransaction(access_code)` API does not accept a local `container` selector; its card fields remain in Paystack's hosted secure overlay rather than an invented or unsupported local iframe mount.

## Payment Status

- PesaPal sandbox authentication was previously verified.
- The existing IPN handler logs callbacks, updates confirmed paid registrations, invokes WordPress ticket delivery, and skips already-paid duplicates.
- Duplicate IPN requests were previously verified as logged and idempotently ignored.
- A new live M-Pesa or card checkout was not claimed as complete because the required email transport acceptance failed first.

## Blocker

The supplied Gmail credential is rejected by Gmail on both supported SMTP endpoints. This indicates an invalid, expired, revoked, or otherwise unauthorized app password rather than a host connectivity problem. The inbox receipt requirement cannot be verified until a valid Gmail app password is supplied. The provided credential should be rotated because it has appeared in the conversation history.

The master checklist is intentionally not marked complete for payment, ticket email, or end-to-end delivery.

## Corrected Credential Retest

- The SMTP password was updated in the local `CER_SMTP_PASSWORD` constant in `wp-config.php`; the secret is not repeated here.
- Registration `id=10` was retried at approximately 2026-08-27 12:38 UTC.
- The callback returned HTTP 204.
- `ticket_generated_at` was updated to `2026-08-27 12:38:46`.
- `ticket_sent_at` was updated to `2026-08-27 12:38:51`, indicating that `wp_mail()` accepted the message through Gmail SMTP.
- Inbox receipt at `mukindiakaimenyi@gmail.com` still requires user confirmation. M-Pesa and card checkout testing remains intentionally paused until that confirmation.

## Final Sandbox Run

- The invalid PesaPal notification ID was re-registered successfully. The active sandbox `ipn_id` is stored in Laravel `.env`; it is not repeated in this audit.
- M-Pesa registration `id=12` was created through the live form with encrypted attendee data and payment method `mpesa`.
- PesaPal returned tracking ID `9b206a96-a205-41c8-9910-d9f916828aaf` and displayed its Payment Processing page after the phone number was submitted.
- PesaPal status remained `INVALID / Pending Payment` with `payment_details_not_found`; no paid IPN arrived, so no ticket email was sent for this transaction.
- A Card attempt was made with a new form session and the sandbox card details. The browser-side card form did not submit a new registration because its required-field validation did not complete; no Card tracking ID or paid callback exists from this run.
- Duplicate IPN requests against paid registration `id=10` returned `Already processed` twice. Both were logged in `payment_logs`, and `ticket_sent_at` remained unchanged at `2026-08-27 12:38:51`.
- The redesigned ticket for registration `id=10` regenerated successfully as a 36,109-byte `%PDF-1.7` file and remains downloadable through the protected access-key URL.

The payment and ticket infrastructure is implemented, but the M-Pesa and Card sandbox transactions were not completed by PesaPal in this run. The audit therefore does not claim that all payment flows are complete.

## 2026-08-27 Execution Evidence

- M-Pesa live form submission created registration `12` with payment method `mpesa`, encrypted PII, and a unique access key. PesaPal tracking ID: `9b206a96-a205-41c8-9910-d9f916828aaf`.
- The M-Pesa hosted page displayed the phone prompt and then the Payment Processing page. The status API returned `INVALID`, `payment_details_not_found`, and `Pending Payment`; no paid IPN was received.
- Card live form submission created registration `13` with payment method `card`, encrypted PII, and a unique access key. PesaPal tracking ID: `80ebbad0-8677-4846-ad26-d9f93d314b62`.
- The Card hosted page displayed the card billing form and accepted the sandbox card entry, but its payment API returned HTTP 500/aborted in the sandbox. No paid IPN was received.
- Existing paid registration `10` was used for the required duplicate-IPN check. Two identical confirmed IPNs returned `Already processed`; `payment_logs` recorded both and `ticket_sent_at` stayed unchanged.
- The redesigned PDF for registration `10` regenerated at approximately 15:54 UTC, increased to 36,109 bytes, and retained a valid `%PDF-1.7` signature.
- The user confirmed receipt of the earlier ticket email and PDF. The two new payment-flow emails could not be sent because neither sandbox transaction reached paid status.

Final status: ticket generation, Gmail delivery, protected downloads, and IPN idempotency are verified. M-Pesa and Card sandbox payment completion remain blocked by PesaPal sandbox processing and are not marked complete in the master checklist.

## PCI-Safe Embedded Checkout Update

- Card number, CVV, and expiry inputs were removed from the WordPress form, so sensitive card data is not submitted to ICRHK servers.
- The PesaPal hosted checkout URL is embedded in an in-page iframe for card and M-Pesa flows.
- M-Pesa still uses PesaPal's hosted payment page to initiate its prompt; the configured PesaPal v3 API does not expose a direct server-side STK endpoint. The UI remains on-site while the hosted checkout performs the payment.
- Sandbox amounts are overridden to `1.00` KES in `PesaPalService`; registration records retain the selected ticket price.
- The Laravel `wordpress` database connection was added for Eloquent models that target shared WordPress tables.
- A real 1 KES sandbox order was accepted and returned tracking ID `3d3c4ce8-2268-40d4-958e-d9f9f81eb413`; its status remained `Pending Payment` / `payment_details_not_found`.

## Payment API Boundary

PesaPal's published API 3.0 sandbox guidance confirms that `SubmitOrderRequest` returns an order tracking ID and a PesaPal `redirect_url`, and instructs the customer to complete payment on that PesaPal page. The current integration does not expose a documented PesaPal JavaScript tokenization SDK, embedded card fields, or a direct server-side M-Pesa STK endpoint.

The implementation therefore uses the closest PCI-safe flow supported by the configured PesaPal account: the registration form sends only attendee/order data, while the PesaPal-hosted checkout is embedded in an iframe. Card PAN, CVV, and expiry never enter WordPress or Laravel. M-Pesa prompts are initiated by the PesaPal-hosted checkout after the phone number is entered there; `SubmitOrderRequest` alone cannot create a direct STK prompt.

The requested combination of no hosted checkout, no second payment surface, direct STK, and PesaPal-hosted card tokenization cannot be implemented against the currently documented PesaPal API without a separate PesaPal product/SDK or a direct M-Pesa provider integration. The checklist must remain incomplete for those claims until such an integration is supplied and verified.

## Single-Page UX Update

- The original registration form now submits attendee/order data once, then is disabled and hidden after PesaPal returns a valid order.
- The returned PesaPal checkout URL is loaded into the in-page payment panel; the browser does not navigate away from the ICRHK page.
- The panel is responsive, uses one embedded checkout frame, and presents the hosted PesaPal surface as the natural next step in the same registration flow.
- The front end polls the existing transaction-status endpoint using the returned tracking ID and changes the status to ticket delivery after a completed/paid response.
- The live rendered form contains no card PAN, CVV, or expiry inputs. Card data remains inside PesaPal's hosted checkout.
- Final PHP syntax checks pass for the WordPress and Laravel payment files. The local WordPress HTTP page became slow during the final browser check, so no new paid transaction is claimed from that attempt.

## 2026-08-27 Single-Form Checkout Retest

- The live event page now renders only full name, email, phone number, ticket type, and the M-Pesa/card toggle, with one `Register & Pay` submit button.
- The legacy card-security copy, notes field, payment modals, and hosted-payment header were removed. After order creation, the form is disabled and hidden and the returned PesaPal URL is loaded in the same-page iframe.
- The payment panel is iframe-only. The iframe is hidden only after the local registration status becomes `paid`; the thank-you message is then shown.
- Registration `id=15` was created with payment method `mpesa`; PesaPal tracking ID `a96cbf60-c264-47d6-98cf-d9f9548753d4`. The hosted checkout loaded and displayed the phone prompt, but PesaPal returned HTTP 500 after `Proceed`. The registration remains `pending` and no email was sent.
- Registration `id=16` was created with payment method `card`; PesaPal tracking ID `73555f82-ee48-432d-8dcd-d9f9dfc61c79`. The hosted checkout loaded, but the sandbox returned HTTP 500 before card completion. The registration remains `pending` and no email was sent.
- The IPN handler now verifies a notification through `GetTransactionStatus` before marking a registration paid. The status endpoint reports local IPN fulfillment state so a remote checkout response cannot hide the iframe or trigger a thank-you prematurely.
- PHP syntax, JavaScript syntax, and editor diagnostics pass for the modified files. No payment completion or new email receipt is claimed; user confirmation of both requested receipts is still outstanding.

## Restored PesaPal Payment Branches

- M-Pesa now follows the previously documented PesaPal behavior: after order creation, the registration form is disabled and the returned PesaPal checkout URL is opened as a normal navigation. The M-Pesa prompt is initiated by PesaPal without embedding the checkout in the ICRHK iframe.
- Card remains on the ICRHK page: after order creation, the form is hidden and the same PesaPal checkout URL is loaded into the hosted iframe. The customer selects Card inside PesaPal and enters card data there.
- The JavaScript syntax check passes, and only the Card branch assigns `hostedPaymentFrame.src`.