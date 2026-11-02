# Event Metrics, Repeater Trash, Pagination, and Branding Audit

## Scope and pre-change findings

This pass extends the event-admin work recorded in `55_event_dashboard_compaction_and_soft_delete.md` and `56_icrhk_admin_compression_audit.md`.

- Dashboard registration, ticket, revenue, and sponsorship figures were not scoped to a selected event.
- The legacy `CER_Registration_List_Table` still requested 20 rows per page.
- Event-form repeater content was rendered as one large list. Child-item tables did not have a Trash timestamp.
- Speakers, sponsorship packages, and thematic pillars had individual `is_visible` controls in addition to their section-level visibility controls.
- The event editor had one `background_image_id` control and the event schema had one corresponding field. No duplicate control or column was found.

## Implementation

| Area | Changes |
|---|---|
| Dashboard | Core and financial metrics now use the selected event. The Latest Events list has five entries per server-rendered page and selecting an event keeps its row in view. Active registration count and ticket availability exclude trashed records; paid/confirmed sales and revenue continue to include trashed registration history. Sponsorship counts exclude trashed packages. |
| List pagination and item navigation | Events, dashboard registrations, both registration-list implementations, and child-item Trash views retain five-item pages. In the follow-up, all eight active event-form repeaters load every item in order, without pagination. Their existing title bars scroll horizontally; selecting an item shows its editor without a request or save. Save Event remains the explicit save action. |
| Child-item Trash | Ticket types, speakers, sponsorship packages, and pillars can be moved to Trash, restored within 30 days, or permanently deleted from Trash. Restoration requires an active parent event. Ticket types with registration history cannot be manually deleted; the scheduled expiry cleanup also retains those ticket rows. Expired child items without retained ticket history are physically purged in bounded batches. |
| Visibility | Per-item visibility controls and writes were removed for speakers, sponsorship packages, and pillars. Their section-level toggles remain. Existing `is_visible` columns and values are retained; the public renderer no longer filters those three categories by the obsolete per-item flag. Visibility behavior for objectives, summit structure, partners, and FAQs remains unchanged. |
| Branding | The existing single background-image picker and `background_image_id` field were retained. No duplicate field was found to remove. Branding styles remain compact. The current database rows have no selected background attachment, so an effective custom background URL was not available to verify. |
| Schema | Schema version 10 adds nullable, indexed `deleted_at` columns to ticket types, speakers, sponsorship packages, and pillars. The migration advances to version 10 only after all four columns are confirmed. The schema initializer now exits for versions 9 and 10, preventing the legacy fallback migration from running after the new migration. Fresh-install table definitions include the same columns and indexes. |

## File-level changes

| File | Responsibility |
|---|---|
| `wp-content/plugins/custom-event-registration/admin/dashboard.php` | Selected-event metrics, latest-event pagination, and five-row recent-registration pagination. |
| `wp-content/plugins/custom-event-registration/admin/events-list.php` | Five-event pages with page numbers and previous/next navigation while retaining event filters and Trash context. |
| `wp-content/plugins/custom-event-registration/admin/registrations-list.php`, `admin/class-cer-registration-list-table.php`, and `admin/page-registrations.php` | Five-row pages on both registration-list surfaces and preservation of the legacy Trash/filter workflows. |
| `wp-content/plugins/custom-event-registration/admin/event-form.php` | One ordered active-item query per repeater, full-event saves, global ordering and ticket-capacity calculations, and the selected visibility-control removals. |
| `wp-content/plugins/custom-event-registration/custom-event-registration.php` | Schema version 10, item Trash/restore/permanent-delete actions, event-item Trash paging and search, public schema definitions, and bounded expired-item cleanup. |
| `wp-content/plugins/custom-event-registration/includes/event-page-renderer.php` and `includes/registration-functions.php` | Exclude trashed child content and ticket types from public rendering and registration initiation; stop honoring the removed speaker/sponsor/pillar item flags. |
| `wp-content/plugins/custom-event-registration/assets/js/cer-admin-event-form.js` | Accessible item tabs, row creation/removal, expansion, confirmation behavior, and local navigation for newly added unsaved rows. |
| `wp-content/plugins/custom-event-registration/assets/js/cer-admin-dashboard.js` | Dashboard quick-action interaction for compact/mobile layouts. |
| `wp-content/plugins/custom-event-registration/assets/css/cer-admin-reporting.css` and `assets/css/cer-admin-modern.css` | Dashboard, Trash, pagination, item-tab, responsive, and compact Branding presentation. |
| `wp-content/plugins/custom-event-registration/admin/admin-functions.php` | Shared admin feedback for Trash and restore actions. |

## Data and behavior safeguards

- The schema migration did not rewrite or delete existing content. Before and after counts remained: 2 events, 137 registrations, 5 ticket types, 3 speakers, 3 sponsorship packages, and 4 pillars.
- A local WordPress bootstrap reported schema version 10 and confirmed `deleted_at` plus its index on each of the four child tables.
- Ticket inventory history (`quantity_sold`) is not changed by trash or restore. Trashed ticket types are excluded from public registration and available-inventory queries.
- Registration records and payment history remain intact. Historical paid/confirmed registrations continue contributing to sales and revenue, including when a registration is in Trash.
- Child-item expiry cleanup retains ticket rows linked to registration history; manual permanent deletion has the same history protection. The pre-existing parent-event expiry path still detaches registration links and removes all event content when the parent event itself expires.

## Initial verification (before the local scrolling-item follow-up)

- `php -l` passed for all modified PHP files.
- `node --check` passed for the changed event-form and dashboard JavaScript.
- `git diff --check` passed.
- The local database page-query check returned five rows on each of the first two pages for the event with 12 summit-structure rows; it did not exceed five rows for any tested event-form repeater page.
- No PHP test files were present under the custom event-registration plugin.
- The integrated browser had no authenticated WordPress admin session. Opening the local admin route timed out, and a local public-event HTTP request also timed out. Authenticated admin layouts at desktop/tablet/mobile widths and the rendered public background style therefore remain unverified. A manual `admin_init` dispatch from CLI was also blocked by an unrelated Charitable plugin fatal (`charitable_get_donation_actions()` undefined); the plugin bootstrap and schema migration themselves completed successfully.

## Remaining verification

- Repeat the admin and public-page checks in a working local browser session with authorized admin access.
- Confirm the Branding section and resolved public background appearance after an event has a background attachment selected.

## Dashboard follow-up (local verification)

- Selecting an event updates its metrics through a capability- and nonce-checked request. Figures are read from the database, and ordinary links remain available if JavaScript cannot run. Existing definitions, including sales and revenue from trashed registration history, are unchanged.
- Inner item pagination has been removed from both PHP and JavaScript. All active items appear in the existing scrollable title bars; keyboard arrows, Home, and End select items without requests, saves, or loss of unsaved edits. The normal Save Event submission and redirect remain unchanged. The obsolete item-pagination AJAX handler, page state, response-reconciliation code, and related strings/styles have been removed.
- Loading active items now uses eight list queries instead of eight count queries plus eight paginated list queries. The off-page ticket-capacity display query is no longer needed. This trades the old five-row limit for rendering all active items; very large events may require more initial HTML and browser work, but item switching itself requires no database round trip.
- A final hidden form marker rejects truncated submissions before any writes, with a visible error, so exceeding PHP input limits cannot silently apply a partial save.
- The existing event form, per-section save processing, and database model remain in use. A shared item-save helper reuses loaded rows to avoid repeated existence checks and unchanged writes. Saves verify transactional storage and the database connection, roll back on confirmed failures, and prevent another save when the result cannot be confirmed. The existing live and local event tables were found to use InnoDB; nontransactional or unverifiable storage is rejected before saving.
- The three branding-column checks are grouped, with missing-column recovery retained, and Flatpickr is pinned to version `4.6.13`. No schema migration is introduced.
- `tests/dashboard-integration-test.php` exercises synthetic data in a temporary local database, including the live foreign-key actions, validation, rollback, storage checks, simulated connection changes, cache invalidation, item ordering, and unchanged saves. The loopback-only browser fixture is used to check the WordPress editor and JavaScript/non-JavaScript paths.
- The isolated fixture excludes the rest of the site's plugin and theme bootstrap. Its responsiveness does not establish a production page-load improvement; the previously observed live server wait still needs authenticated profiling and verification after any separately authorized deployment.

### Scrolling-item verification

- The isolated integration suite passed with 12 or more items in every repeater. It verifies all rows load, edits beyond the former five-item limit persist, ordering/capacity/deletions remain correct, and unchanged saves perform no event/item writes or per-item existence lookups. Native permission/nonce checks, validation, incomplete-form rejection, rollback, storage checks, and simulated connection-change tests also passed.
- Browser checks verified all eight title bars scroll and expose the last item through Home/End navigation, preserve unsaved edits, and make zero item-navigation requests. Adding/removing rows and saving new IDs worked; all eight sections' final-item edits and WordPress editor paragraphs survived a normal save and reload.
- At CSS viewport widths of 390px and 1280px, the title bar scrolled without overflowing the page. With JavaScript resources blocked, all items remained in the form and an edit to the thirteenth ticket saved successfully.
- PHP/JavaScript syntax checks, nine existing header-offset tests, and `git diff --check` passed. The task's temporary browser server was stopped and its marker-validated synthetic database removed. Production was not edited during this local verification.

### Environment comparison

- Before the local dashboard changes, all 24 deployed custom-plugin PHP, JavaScript, and CSS files matched the local working copy after normalizing line endings. GitHub's default branch, `replication-baseline` at `412413bc6a9f8edd2de0afdeb76c938bbe041cb3`, matched 20 of those files; the other four lacked the previously deployed navbar and schema-hook fixes.
- The live and local event databases are not identical: live has 10 event tables and 181 columns, while local has 11 tables and 199 columns. Local has an extra `evt_summit_steps` table, nine extra legacy event columns, `evt_faqs.question` at `varchar(500)` instead of live's `varchar(255)`, and no foreign keys; live has ten. Both use InnoDB and the same event-table collation.
- GitHub contains schema code but no running database or matching SQL snapshot to compare. The isolated test database reproduces the live foreign-key actions and FAQ length and omits the nine local-only event columns; it is not an exact production schema clone.

### Real-time filtering follow-up

- All currently reachable filter controls use the existing authenticated GET routes and server-side queries. Events retain status, month, and name/venue search; Registrations retain event, ticket, payment method, status, date range, and exact name/email/phone/registration-UUID search. Ticket, speaker, sponsor, and pillar Trash searches retain the current event/type and 30-day recovery scope. Dormant legacy registration pages are not activated or changed.
- Text searches update after a 250ms typing pause; selections, dates, Enter, and the existing Filter/Search buttons update immediately without document navigation. Only the results are replaced. Matching counts cover the full filtered dataset, not just visible rows. New filters reset list pagination; list pagination and browser Back continue using the selected filters. Reset clears editable filters while retaining the current Active/Trash scope.
- Registration search keeps the existing exact, indexed search-hash behavior for encrypted personal data; it is not substring PII search. Searching for the string `0` is no longer treated as an absent search. No schema changes, new filtering API, client-only dataset, or duplicate query implementation were added.
- Pending requests are cancelled when controls change, and stale responses cannot replace newer results. Network, HTTP, redirect, and malformed-results failures are reported explicitly. Previous results remain inert, and bulk submissions/export clicks remain blocked until a successful retry or reset. Database count/results failures log an error and return HTTP 500 rather than misleading empty results.
- Registration export URLs refresh from the same server response as the rows. Existing nonce/capability enforcement remains in place. Select-all, bulk enablement/confirmation, event action dialogs, and the shared event-share modal are reinitialized or delegated after replacement. Child Trash filtering replaces neither the event form nor its editors, preserving unsaved data.
- Integration coverage includes combined filters, full counts, pagination beyond page one, empty results, each encrypted exact-search field, Active/Trash scope, all four child types, permissions, numeric-zero searches, and injected count/results failures. Local browser verification covered live updates without navigation, filtered registration export counts, pagination and Back, rapid filter changes, error recovery, bulk selection, event action dialogs, and preservation of unsaved editor data.
- This is an enhancement to the existing lists, not an event-editor replacement. Active inner items still use scrolling title bars without pagination. Filter requests still incur WordPress bootstrap and server-query time; the isolated results do not prove the previously measured live 8–10-second initial response delay is resolved.
