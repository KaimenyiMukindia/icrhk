# ICRHK Replication Baseline

This repository is the source-code baseline for the current ICRHK WordPress and Laravel implementation. It does not store production secrets, database dumps, uploaded media, caches, logs, or third-party plugin backup archives.

## Git Scope

Track the custom implementation and the information needed to rebuild it:

- `wp-content/plugins/custom-event-registration/`
- `wp-content/themes/goodsoul/`
- `laravel-engine/`, excluding `vendor`, storage, and local environment files
- `master_md/` technical audits and verified behavior notes
- `wp-config-sample.php`, `laravel-engine/.env.example`, and documented configuration templates

Do not add `wp-config.php`, `.env`, `laravel-engine/.env`, database dumps, or `wp-content/uploads/`. These contain credentials, personal data, operational data, or large binary files and must be handled as controlled release artifacts.

## Current-State Artifacts

The current implementation cannot be reproduced from source alone. Create one timestamped release bundle outside Git containing:

1. A MySQL/MariaDB dump with schema, data, triggers, routines, events, and the `utf8mb4` character set.
2. A compressed archive of `wp-content/uploads/` preserving relative paths.
3. A copy of third-party plugin/theme packages that are required but not reconstructed from Composer or npm lock files.
4. An environment manifest recording PHP, MySQL/MariaDB, WordPress, Node, Composer, active plugin, cron, queue, payment callback, and web-server versions/settings. Do not include secrets.
5. SHA-256 checksums for every artifact and the Git commit SHA that produced the bundle.

Store the bundle in access-controlled backup storage. Record its storage location and access process in a private operations system, not in this repository.

The existing `db-backups/` SQL files are historical local evidence. They must be verified against the running database before being treated as the replication baseline.

## Restore Order

1. Check out the tagged Git revision.
2. Install PHP, Composer, Node, WordPress, and database versions from the environment manifest.
3. Restore Composer and npm dependencies from their lock files.
4. Install the exact third-party plugin/theme packages from the release bundle and activate the versions in the environment manifest.
5. Restore uploads before importing the database so attachment references resolve.
6. Import the database dump, configure local secrets from secure storage, and update environment-specific URLs through a serialized-data-safe WordPress migration process.
7. Configure web-server rewrite rules, WordPress cron, Laravel workers, payment webhook URLs, and provider credentials.
8. Run the functional checks below before declaring the replica operational.

## Functional Acceptance Checks

- **Event Setup** uses two section tabs: **Event Identity & Publishing** and **Ticket Types**. **Page Content** uses three: **Page Content & Search Preview**, **Registration & Payment**, and **Page Content**. Only the selected section is displayed; selection survives saving through the existing tab-state fields. Left/Right/Home/End navigation and automatic revelation of invalid fields must work through the nested tabs. Other admin sections retain their existing accordion behavior.
- Hero fields use the available desktop columns and stack on mobile. Hero detail and Page Content rows retain their existing item tabs, Add/Remove and expanded editing; adding a row must select and focus it without duplicate expand buttons on the surrounding repeater field. Verify save/reload with both selected and hidden sections intact.
- In event content management, **Page Content & Search Preview** precedes **Registration & Payment**. The existing event title (also the hero title), description, badge, repeatable label/value details, feature bullets, CTA labels, emblem and hero summary title are together under **Hero content**; SEO fields remain under **Search preview**. Each program, sponsorship, partner and FAQ panel owns its existing section title/intro plus its editable eyebrow and visibility controls. Blank eyebrows hide the label; existing labels remain the defaults for older events.
- Add **Page Content** rows with header, subtitle and safe HTML body, save, reload, edit and remove rows. One section with the registration/payment background renders these cards after payment and before FAQs, including the existing CSS visual ordering. The grid uses four desktop columns, two tablet columns and one mobile column. Its **Visible** switch hides/shows the section without deleting rows. The former Event Information card and editing subtab are removed, but their old database copy is retained. Existing `hero_convened_by` becomes the first hero detail until the repeater is saved; explicitly removing all rows must not resurrect it. The additive `cer_event_content_schema` migration stores these event-owned repeaters and visibility on the existing event record, without changing payment, tickets or mail.
- Check the existing share links alongside hero CTAs on desktop, with the CTA group and share group justified apart, and wrapping without overflow on mobile; do not introduce a second share control or change the share targets.
- Payment summary keeps Total and Secure Payment on one compact, wrapping row. The existing notification below payment remains the single system-generated status region for idle, validation, pending, success and failure messages. Editable summary intro is no longer displayed or offered as a status-message control; its legacy database value remains intact.
- The nested **Page Content** editor exposes the same **Section Link** and **Visible** action group as other frontend sections. Section Link uses the existing copy handler with the event URL's `#page-content` anchor. Switching Visible off and saving omits the whole section, background, grid and cards, while retaining the saved content.
- Public event route resolves at `/event/{slug}/` and loads registration settings.
- Purchase 1–20 tickets using the plus/minus controls inside each existing ticket-type choice. Compact prices omit the currency prefix and unnecessary decimal zeros in the choices; the existing total retains currency. Confirm server-stored unit price times each type's quantity is charged, live stock limits the combined selection, and reservation expiry releases each type's quantity exactly once. Admin assignment prices are also per ticket, not per group.
- Selecting multiple types creates one existing registration per type with its own registration UUID, payment-attempt UUID, line amount, admission quantity and PDF. Roots share an indexed `checkout_uuid`; allocated individual children remain linked only to their own type's root. The earliest root coordinates one payment for the sum of all line amounts. Gateway confirmation and manual confirmation are atomic across the checkout; verified retries from any line reserve and charge the whole checkout, not that line alone. Existing single-type purchases retain their original flow.
- Reservation deadlines and expiry comparisons use UTC. Inventory reconciliation preserves pending held quantities until the existing expiry worker releases them; expired timestamps alone must not reopen those seats.
- Event registration validates its nonce and stores `event_id` and `ticket_type_id`.
- Event administration manages events, ticket types, speakers, sponsors, pillars, registrations, and dashboard reporting.
- Laravel payment routes accept a test request, persist payment state/logs, and return the expected callback response.
- A payment-provider sandbox test and callback test complete without exposing live credentials.
- In Event Registrations, select an active paid or free confirmed registration with an unsent ticket and use **Send Missing Tickets**. Confirm that delivery is recorded and that repeating the action reports an already-sent ticket without sending another copy. Pending and trashed registrations must not receive tickets.
- Create an admin-assigned ticket with a custom title, color, price, and reason. Select an event and check that only its tickets and current availability are shown; a single available ticket is selected automatically. Confirm that a free ticket or explicitly confirmed offline payment uses the existing ticket delivery and inventory flow. For card and M-Pesa, verify the discounted amount with the provider sandbox; a card checkout link can be copied or emailed. Existing registrations can be assigned only when they are unconfirmed, have no payment/reservation history, and have not received a ticket. Other existing registrations retain their payment history and can receive an already-confirmed ticket through the mail composer or **Send Missing Tickets**.
- Assignment inventory is preloaded in three shared queries, so event selection displays current per-type stock and stored prices immediately. A capability/nonce-protected refresh updates it in the background every 30 seconds. Quantity/price edits require no page or network reload. Set a quantity and discounted per-seat price for each required type (1–20 seats total); the existing assignment transaction validates fresh stock, creates distinct per-type roots linked by the existing checkout UUID, and initiates one aggregate gateway charge or delivers each confirmed offline/free ticket. Existing contact-record conversion remains single-type; use a new assignment for multi-type purchases.
- Inspect the payment error on a failed registration. **Verify payment** checks the provider; **Verify and retry** initiates a replacement only after a verified failed or abandoned outcome. Never retry a pending, unknown, or unverified outcome. A late successful callback from an earlier attempt is reconciled, and a second successful reference is flagged for manual review.
- In the event's **Email** tab, save a valid Sender Email, compose formatted content, search for registrations and add or remove them from the selected-recipient list, or choose all active registrations, and queue individual messages. Confirm each message copies the saved Sender Email; an optional generated ticket belongs to that recipient and is attached only for paid or free-confirmed admissions. The notification address remains separate.
- Bulk purchases initially issue one group ticket showing its remaining admission count and asking the purchaser to reply with individual details. In **Event Registrations**, use **Allocate individual tickets** and enter one `Name,email` per line (quote names containing commas). An omitted individual email uses the purchaser's address. Partial allocation rotates the group access code, removes its old PDF, and sends individual tickets plus a replacement group for the remaining seats; full allocation invalidates the group and notifies the purchaser. Mail failures do not undo issued seats: recover missing child/replacement tickets with **Send Missing Tickets**.
- Allocated children retain the original event and ticket type; the root retains the original quantity, amount and payment reference. Children do not add inventory or revenue. Trashing/cancelling the root invalidates its children; restoring it restores eligible children. Do not delete purchase/allocation history to release seats. Allocation is not a refund or recorded check-in.
- **Validate the current QR code online before manual admission.** Downloaded obsolete PDFs cannot be recalled from an attendee's device. Staff must reject invalid/replaced codes, admit only the displayed remaining group count, and keep their existing manual admission records. Allocate individual seats before admission; do not split seats already admitted.
- The composer places existing-registration search and bulk-address entry side by side on desktop. Both feed one case-insensitively deduplicated email-chip list with accessible × removal buttons, three columns and a three-row scroll area. Invalid addresses reject the input; duplicates of selected registrations are removed by the existing queue. Normal added addresses receive the composed message without a ticket attachment. To attach an attendee's ticket, select their existing registration. Fully allocated roots and children of invalid roots cannot supply ticket attachments. All composer delivery still uses the ticketing mail context, configured Resend/no-reply sender, event Reply-To and Sender Email CC.
- Compose paragraphs, line breaks, bullet/numbered/nested lists, links and inline formatting in Visual or Text mode. Submission synchronizes only the mail editor; the queue restores WordPress editor paragraph breaks with `wpautop` once, then both registered and added-address deliveries pass that HTML unchanged to Resend. Existing capability-based HTML sanitization remains. No separate email archive is added; the existing background/retry queue still retains its message payload. Check both send-now and 03:05 delivery, with and without a ticket attachment.
- To replace a group in the email composer, expand **Issue individual tickets from an existing group purchase**, choose an eligible event-matched group, select/add exactly one unique email for every remaining seat, and confirm replacement. Existing selected registrations supply names; email-only entries use the email as the ticket name. The existing locked allocator creates fresh child UUIDs/codes, retains the root's purchase totals and stock, and invalidates its previous group code. Individual tickets with the composed message and the purchaser replacement notice use the chosen send-now/03:05 queues, not immediate synchronous mail. If queue persistence fails after allocation, the error identifies existing child IDs for mailing recovery; never repeat allocation. Daily queues retain the existing ten-message worker budget and five-minute cron cadence.
- Confirm WordPress cron is running: email batches process up to ten recipients per run. **Send now** schedules background processing; the 03:05 option waits for the next Nairobi 03:05 window and then uses the existing recurring notification worker (actual execution depends on WordPress cron). Multiple messages for an event are kept as separate batches. Review each batch's failed and uncertain entries before resending. An interrupted `sending` entry may already have been delivered and is not automatically resent. Use **Resume queued batch** if scheduling or cron stopped.
- Desktop and mobile visual checks match the release screenshots for the event page and administrator screens.
- The payment summary and M-Pesa/Card selector stay visible before selecting tickets and for free registrations. The ticket-picker label stays compact ("Select ticket"); selected types and quantities appear in the existing summary, not twice. Form controls use the same font family and compact 36px heights (30px quantity controls). The form reserves space for both steps and feedback; hover expansion, payment-method changes and accessibility details must not move the footer or resize the parent columns. The ticket dropdown opens explicitly, overlays rather than stretches its parent, and closes when another field is used, on outside click, Escape or viewport resize. Check long selected-ticket names and narrow screens for overflow.

The plugin requires database privileges for its registration-column upgrades and MySQL transactional tables/advisory locks for ticket assignment and mail processing. Existing Laravel bridge configuration is reused; `CER_LARAVEL_VERIFY_URL` can override the default verification URL under the WordPress site's `/laravel-engine/public/api/payment/verify` path. Provider and mail credentials must remain in secure environment configuration. Acceptance checks must use synthetic attendees and sandbox services, never real charges or unsolicited mail.

Deploy the WordPress upgrade before enabling bulk checkout alongside the updated Laravel callback handler. Verify registration columns `ticket_quantity`, `admission_quantity`, `parent_registration_id`, and the parent lookup index exist; pre-upgrade records default to one seat. The upgrade also widens legacy profile columns for the existing encrypted values (including accessibility answers); plaintext-width columns must not truncate encrypted attendee data. Database upgrade failures are logged and must be resolved before accepting registrations.

## Git Release Procedure

1. Confirm `git status` contains only intended source, documentation, and configuration-template changes.
2. Run the relevant Laravel test suite and PHP syntax checks.
3. Commit the source baseline and create an annotated tag such as `replication-2026-09-01`.
4. Create the controlled data/media artifact bundle and its checksum manifest referencing that tag.
5. Add a remote repository, then push the commit and tag. This workspace currently has no Git remote, so a push cannot occur until one is configured.

## Evidence Available Here

- `master_md/` contains implementation, payment, audit, and live-test notes.
- The custom event plugin owns the `wp_evt_events`, `wp_evt_ticket_types`, `wp_evt_speakers`, `wp_evt_sponsorships`, `wp_evt_registrations`, and `wp_evt_pillars` tables.
- `laravel-engine/database/migrations/` records Laravel-owned schema changes; the WordPress custom-table schema must be verified from the current database dump and plugin install/upgrade routines.