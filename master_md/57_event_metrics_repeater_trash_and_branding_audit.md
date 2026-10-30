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
| Five-item pagination | Events, dashboard registrations, both registration-list implementations, child-item Trash views, and all eight event-form repeaters use a five-item page size. Active repeater pages use `LIMIT`/`OFFSET` queries. Page-navigation buttons submit the current form state, save it, and redirect to the requested page. Page offsets are applied to ordering, capacity is calculated across all active tickets, and removing an item shifts later active items so page boundaries remain stable. |
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
| `wp-content/plugins/custom-event-registration/admin/event-form.php` | Server-side five-row queries for eight repeaters, form-state-preserving pagination, global ordering and ticket-capacity calculations, and the selected visibility-control removals. |
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

## Verification

- `php -l` passed for all modified PHP files.
- `node --check` passed for the changed event-form and dashboard JavaScript.
- `git diff --check` passed.
- The local database page-query check returned five rows on each of the first two pages for the event with 12 summit-structure rows; it did not exceed five rows for any tested event-form repeater page.
- No PHP test files were present under the custom event-registration plugin.
- The integrated browser had no authenticated WordPress admin session. Opening the local admin route timed out, and a local public-event HTTP request also timed out. Authenticated admin layouts at desktop/tablet/mobile widths and the rendered public background style therefore remain unverified. A manual `admin_init` dispatch from CLI was also blocked by an unrelated Charitable plugin fatal (`charitable_get_donation_actions()` undefined); the plugin bootstrap and schema migration themselves completed successfully.

## Remaining verification

- Repeat the admin and public-page checks in a working local browser session with authorized admin access.
- Confirm the Branding section and resolved public background appearance after an event has a background attachment selected.
