# Event Dashboard Compaction and Soft Delete Audit

## Scope

Implemented the administrator dashboard UI and recovery workflow described in the dashboard compaction and soft-delete request. The public event page, registration form, Laravel engine, payment bridge, payment callbacks, ticket delivery, and email paths were not reworked.

## File-level changes

| File | Rationale and prompt sections | Acceptance criteria |
|---|---|---|
| `wp-content/plugins/custom-event-registration/admin/dashboard.php` | Condensed the dashboard into labelled Event Core Metrics and Financial Metrics groups. Kept Total Events visible in the header, added the three-event latest feed, moved Quick Links directly above Recent Registrations, and added pagination below the table. The dashboard registration count and table use active registrations; paid ticket and revenue totals continue to include trashed registration rows to preserve financial history. Added the mobile action trigger and its shortcuts. Sections 1–6. | 1–7, 11 |
| `wp-content/plugins/custom-event-registration/assets/css/cer-admin-reporting.css` | Applied one compact spacing/type/radius system, distinguished the two metric groups, retained horizontal scrolling for the full registrations table, and added responsive header and mobile arc-menu layouts. Sections 1–6. | 1–7, 11 |
| `wp-content/plugins/custom-event-registration/assets/js/cer-admin-dashboard.js` | Implements the mobile-only quick-action menu, its expanded state, Escape/outside-click dismissal, focus handling, and trigger icon/label updates. Section 6. | 7 |
| `wp-content/plugins/custom-event-registration/admin/events-list.php` | Added an active/Trash view, deleted-time display, individual and bulk restore, and confirmation before moving events to Trash. Active event queries exclude trashed events. Section 7. | 8–11 |
| `wp-content/plugins/custom-event-registration/admin/registrations-list.php` | Added an active/Trash view, deleted-time display, individual and bulk restore, and confirmation before moving registrations to Trash. Existing columns and filters remain available. Section 7. | 8–11 |
| `wp-content/plugins/custom-event-registration/admin/admin-functions.php` | Added the shared status notice used after successful trash and restore actions. Sections 7 and interaction requirements. | 8, 11 |
| `wp-content/plugins/custom-event-registration/custom-event-registration.php` | Added schema version 9 with indexed nullable `deleted_at` columns; shared allowlisted trash/restore helpers; 30-day restoration checks; cache invalidation; and replacement of the legacy single-registration physical delete. Public event routing and new-registration validation exclude trashed events. Sections 7 and out-of-scope boundary. | 8–10, 12 |
| `wp-content/plugins/custom-event-registration/includes/event-page-renderer.php` and `admin/event-form.php` | Exclude trashed events from public and preview rendering and from direct event editing. Event status and child rows are not changed when trashed, so restoring the parent retains its original state and relationships. Section 7. | 8–9, 12 |
| `wp-content/plugins/custom-event-registration/admin/class-cer-registration-list-table.php` and `admin/page-registrations.php` | Replaced the legacy list-table physical delete with the shared trash/restore helpers and exposed the recovery view there as well. Single and bulk delete actions require an explicit confirmation signal server-side, with a client confirmation prompt and the 30-day recovery period stated. Sections 4 and 7. | 8–11 |

## Trash and retention behavior

- Moving an event or registration to Trash only sets `deleted_at` and `updated_at`; the existing status, registration/payment fields, IDs, and associations are retained.
- Active list queries exclude records with `deleted_at`. Trash queries show only records deleted within the preceding 30 days and include the deletion timestamp and Restore action.
- Individual and bulk restore use the same helper. It rejects records older than 30 days with an explicit error. The cutoff is checked when listing and restoring, so expiration does not depend on manually emptying Trash.
- No permanent-delete control or automatic physical purge was added. Records remain stored after they expire from recovery, preserving payment and financial history under the existing retention policy. The 30-day period is the recovery window.
- A trashed event is excluded from public lookup and new registration initiation. Its child records and registrations are not deleted or detached, so restoring it reactivates the original event record and its existing associations.
- Trashed paid/confirmed registrations remain included in ticket-sold and revenue aggregates; moving one to Trash does not erase its financial contribution or alter inventory.

## Verification

- PHP syntax validation passed for the changed plugin, admin, and event-renderer PHP files.
- `node --check` passed for `cer-admin-dashboard.js`; `git diff --check` reported no whitespace errors.
- Re-ran PHP syntax checks across all changed PHP files, the dashboard JavaScript syntax check, and `git diff --check` after adding server-side confirmation enforcement to the remaining registration delete paths; all passed.
- Local WordPress bootstrap completed the migration and reported `cer_schema_version=9`, with `deleted_at` present on both `evt_events` and `evt_registrations`.
- Authenticated local dashboard inspection found both metric group labels and the Latest Events, Quick Links, and Recent Registrations sections.
- Playwright interaction at a narrow viewport opened the mobile action menu, exposed Create Event, Registrations, Manage Events, and Refresh, and closed it with Escape. The table container remained horizontally scrollable without document overflow. The browser reported an effective CSS viewport width different from the requested device width due to its scale factor.
- The local Events Trash view rendered Active Events/Trash navigation, a Deleted column, and the Restore action column. Existing trashed data was inspected only; no records were changed by the interaction test.
- `http://localhost/icrhk/event/kamgc-2026/` returned HTTP 200 after the schema migration.

## Acceptance evidence checklist

| # | Acceptance criterion | Evidence/status |
|---|---|---|
| 1 | Five specified metrics visible in the first desktop content band | The 3+2 grid and metric DOM were inspected; desktop visual layout should still be confirmed in the target administrator viewport. |
| 2 | Title, primary Create action, and a secondary action share the header row | Header markup and desktop styles provide Create, Manage Events, and Refresh in one action row; narrow layouts move quick actions to the radial control. |
| 3 | Core and financial metrics have independent labels and framing | Both labelled groups were found in the rendered dashboard DOM and have separate borders/backgrounds. |
| 4 | Quick Links immediately precede Recent Registrations; pagination follows the table | Verified in dashboard markup and rendered section order; pagination is rendered directly after the table. |
| 5 | Registrations table retains all columns and scrolls horizontally | All seven dashboard columns remain in markup; Playwright confirmed the table container scrolls horizontally without expanding the document. |
| 6 | Latest Events shows up to three events with title, status, and management link | Dashboard query limits active events to three; rendered local dashboard showed available entries with status and Manage links. |
| 7 | Mobile radial menu opens/closes and layout does not overflow | Playwright confirmed open, four actions, Escape close, and no document-width overflow at the configured narrow viewport. |
| 8 | Delete moves to Trash; Trash shows deletion time and Restore | Shared helper sets `deleted_at`; rendered Events Trash view showed the Deleted and Actions columns. Single and bulk event and registration actions, including the legacy registration list, require an explicit confirmation signal server-side and retain the client confirmation prompt and status feedback. |
| 9 | Restore preserves prior lifecycle state and associations | Restore clears only `deleted_at`; no child, event status, registration, or payment fields are rewritten. |
| 10 | Records older than 30 days cannot be restored without manual intervention | The shared restore helper enforces the cutoff, and both Trash queries exclude expired rows. No manual emptying is required. |
| 11 | Existing dashboard information remains reachable | All original KPI values remain (Total Events is now in the header); all seven registration columns remain; existing quick links remain; latest events links to All Events. |
| 12 | Public event, registration, payment, Laravel, ticket, and email paths remain unchanged | No Laravel, payment, ticket, or email implementation files were changed; the local published event route returned HTTP 200. |
