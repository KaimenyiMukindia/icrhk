# ICRHK Admin Compression Audit

## Scope and boundaries

This audit covers the plugin's WordPress administrator dashboard, event list and form, registration list, and existing registration Trash behavior. It records the form compression, shared admin density, and event Trash implementation added after the earlier dashboard/registration-trash work.

The original UI/UX specification file mentioned in the request was not present in the repository during this pass. Dashboard comparisons below use the concrete requirements in the request: the 3+2 metric groups, compact action header, latest three events, Quick Links immediately before Recent Registrations, all seven table columns, pagination after the table, and a mobile quick-action menu.

No page, field, panel, repeater, setting, or action was removed. Registration-trash behavior is unchanged by this pass. Save payloads and handlers, payment, ticketing, mail delivery, public-facing behavior, Laravel integration, and the Elementor header parity issue were not altered. `master_md/54_event_header_parity_audit.md` and `.vscode/` were left untouched.

## Phase 1 — Inventory

### Registered admin surfaces

The plugin registers four submenu pages under its event-management menu. No standalone settings, reporting, or export submenu is registered; CSV export is an action on the registrations page.

| Surface | Layout and controls | Existing behavior retained |
|---|---|---|
| Dashboard | Header with Create New Event, Manage Events, Refresh; Total Events count; Event Core Metrics (Total Registrations, Tickets Sold, Tickets Available); Financial Metrics (Revenue Collected, Sponsorship Packages); Latest Events (up to three, status and Manage link); Quick Links (Create New Event, View All Registrations, Manage Events); Recent Registrations with Event, Name, Email, Ticket, Amount, Status and Date columns; pagination; mobile radial quick actions (Create Event, Registrations, Manage Events, Refresh). | Metric queries and financial aggregation; active-registration count; list ordering, page size and links; horizontal table scrolling; menu shortcuts. |
| All Events | Active Events/Trash view links; status, month and search filters; select-all; Event Title, Date, Venue, Tickets Sold, Available, Revenue, Registrations, Status and Front-End URL columns, plus Deleted in Trash; pagination; bulk-action selector. | Search/filter behavior; publish/close; edit/open/preview/share/view registrations; single/bulk trash, restore and permanent deletion; nonces, capability checks, server-side confirmation and recovery-window enforcement. |
| Create/Edit Event | One form/save endpoint with an outer tab row; tab panels contain the original sections. Identity and ticket setup is the first tab. Content panels with multiple sections keep one accordion layer within the tab. | Existing content field names and values, server-side nonce/capability checks, validation, slug generation, sanitization, metadata, visibility flags, mail settings, repeater ownership checks, deletion arrays, and save/update implementation. One UI-only hidden key, `cer_event_active_tab`, carries the active tab through a failed submit and is ignored by persistence logic. |
| Event Registrations | Active Registrations/Trash links; filters for event, ticket type, payment method, status, start/end date and search; select-all; ID, Event, Full Name, Email, Phone, Ticket Type, Amount, Payment Method, Status and Date columns, plus Deleted and Restore in Trash; CSV export; bulk actions; pagination. Active bulk actions are Mark as Paid, Mark as Cancelled and Move to Trash; Trash supports per-row and bulk Restore. The registered list markup has bulk (not per-row) active Trash; the legacy list-table implementation supplies row actions. | Registration search hashes, decryption, payment status actions, CSV columns, filters, ordering, page size, nonce/capability checks, and existing 30-day Trash/restore behavior. |

### Event form panel, field, and repeater inventory

All existing form content remains present. The tab names are Event Setup, Page Content, Program, Partners & Sponsors, Registration & FAQ, Branding & Location, and Email.

| Tab | Original panel(s), fields/settings and repeater actions |
|---|---|
| Event Setup | Event Identity & Publishing: Event Title (required), auto-generated Slug (not required), Meta Title, Meta Description, Meta Keywords, event date/end date, status, Max Attendees (derived from ticket quantities), and the informational Event UUID where available. Ticket Types: name, price, quantity available and description; add/remove row. |
| Page Content | Event Display & Marketing Content: Event Information Heading and paragraphs, Target Audience Heading, Registration Heading/Intro, Sponsorship Heading/Intro, Speakers Heading, Pillars Heading/Intro, Summary Heading/Note, primary/secondary CTA labels, hero badge/convened-by/features and target audience; the existing front-end visibility toggle remains. Page Content & Search Preview: event description and search metadata remain in the form. |
| Program | Speakers: name, role/organization, short bio, photo, front-end visibility; add/remove. Thematic Pillars: title, icon/symbol, description, visibility; add/remove. Objectives: title, icon/symbol, description, visibility; add/remove. Summit Structure: title, icon/symbol, description, visibility; add/remove. |
| Partners & Sponsors | Sponsorship Packages: package name, CTA URL, description, benefits, visibility; add/remove. Partners: name, logo, website, telephone, email, address and visibility; add/remove. |
| Registration & FAQ | FAQ heading/intro and FAQ repeater question, answer and visibility; add/remove. Registration headings and introductory copy remain on Page Content. |
| Branding & Location | Branding & Media: featured image, secondary logo, two brand colors, event background image and UUID display. Location / Map: location address, map link, latitude/longitude, map search/preview and use-location action. |
| Email | Event Mail Settings: sender email, notification email, SMTP host, port and secure mode. |

The existing repeater POST keys remain `tickets`, `speakers`, `sponsors`, `pillars`, `objectives`, `summit`, `partners`, and `faqs`; persisted-row removal keys remain `deleted_tickets`, `deleted_speakers`, `deleted_sponsors`, `deleted_pillars`, `deleted_objectives`, `deleted_summit`, `deleted_partners`, and `deleted_faqs`. Media IDs, event metadata, visibility fields, event identity fields, mail settings, and nonce names were not renamed.

The title and event date are required to save. The slug is generated from the title when omitted and uniqueness remains handled by the existing slug helper. Repeater names are validated when a row is submitted. The identity tab includes status selection, so a valid new event can be published from the first tab. Other content is optional and editable later.

For cadence, the event title, status, date, ticket quantities/prices, visibility switches, and registration/marketing copy are expected to be revisited as event details change. The slug is normally generated once and then stable; UUID is read-only. Meta snippets, audience/hero copy, section headings, media, brand colors, location and repeater content are optional and generally set once or edited occasionally. SMTP sender/host/port/security settings are configuration values set once or changed rarely. This is a workflow classification, not a new validation rule.

### Other implementation surfaces and database state

- The legacy `event-registrations` implementation (`page-registrations.php` and `class-cer-registration-list-table.php`) exists but is not registered as one of the four plugin menu pages. Its inherited registration Trash behavior was not changed in this compression pass.
- Admin CSS is loaded from `cer-admin-modern.css` followed by `cer-admin-reporting.css` on the four registered pages. `cer-admin.css` is not enqueued. Dashboard, event list and event form each load their corresponding existing JavaScript; event form also loads WordPress media and Flatpickr.
- Live local database inspection before implementation reported `cer_schema_version=9`, 2 active events and 137 active registrations. Both `evt_events` and `evt_registrations` have indexed nullable `deleted_at` columns, with no trashed rows at inspection time. Event child row counts were tickets 5, speakers 3, sponsorships 3, pillars 4, objectives 5, summit structure 12, partners 3 and FAQs 5. All inspected plugin tables were InnoDB and had no declared foreign keys.
- The versioned migration path is `cer_maybe_ensure_event_schema()`: version 8 adds the nullable `deleted_at` columns and indexes on events and registrations, then advances the option to 9; version 9 exits without repeating that migration. No schema change or version bump was needed. Registration Trash already used a 30-day restore cutoff.

### Already compact and intentionally retained

The inherited dashboard already has compact grouped metrics, latest-event feed, Quick Links placement, condensed seven-column registration table, pagination and the mobile action menu. Event and registration lists already used condensed table cells and horizontal scrolling. These layouts were retained and aligned to the shared density tokens rather than rebuilt.

## Phase 2 — Compression definition

### Shared scale and responsive density

The compact scale is implemented through shared admin tokens in the existing modern stylesheet and applied by the existing reporting stylesheet:

- Spacing increments: 4, 8, 12 and 16 px.
- Compact labels: 12 px; body/control text: 13 px; section headings: 15–16 px; page heading: 20 px on non-dashboard admin pages.
- Desktop controls: 36 px minimum height, 7–9 px internal padding; form labels remain visible at 12 px.
- Desktop form grid: two columns for short fields, 10 px row gap and 12 px column gap; description and other long copy remain full-width.
- List table cells: 6–8 px vertical/horizontal padding and 11–12 px text, with every column retained.
- Tablet keeps compact type and the horizontally scrollable tab row; short fields remain grouped when space allows.
- At 782 px and below, form grids and repeater fields become single-column and controls relax to a 42 px touch target; primary page/filter actions use 44 px. Dashboard metric cards become one column and wide tables remain horizontally scrollable rather than hiding columns.
- High density is used for metrics, configuration, tables and repeater settings. Longer explanatory copy, rich-text descriptions, and expanded editing retain more room for scanning and editing.

### First-paint targets and comparison

These are acceptance targets for a 1366×768 desktop browser content viewport, excluding WordPress admin chrome; heights are measured from the plugin page header to the specified first useful content. The pre-change event-form estimate is based on the single long page and its open panels, not a browser measurement.

| Surface | Target after compression | Before estimate / reference |
|---|---|---|
| Dashboard | Header ≤60 px; both metric groups ≤175 px combined; latest events and Quick Links together ≤150 px; Recent Registrations table begins within 420 px of page content. | The inherited compact dashboard already supplied the required grouped metrics and section order; this pass preserves it, with shared table density. |
| Event list / Trash | Header, view links, filters and table header visible within 260 px; data rows target 34–38 px; no columns removed. | Previously compact list table, with broader controls and inconsistent outer spacing. |
| Registration list / Trash | Header, active/Trash links, filters and table header visible within 300 px; data rows target 34–38 px; all active and Trash columns retained. | Previously compact table, but filters and controls used a taller, less-consistent rhythm. |
| Create/Edit Event | Header and tabs ≤115 px combined; first identity panel visible within 500 px; setup tab exposes required identity/status fields immediately and leaves the ticket panel reachable without a second tab layer. | Single long form with multiple panels/accordion controls and approximately 1,800–2,400 px of stacked content before all sections were reached; this was a code-based estimate. |

The dashboard targets align with the prompt's specified 3+2 metrics, adjacent Quick Links/table, condensed full-column table, pagination and compact latest-events feed. As the original separate UI/UX spec file was unavailable, no additional numerical targets are attributed to it.

## Phase 3 — Design specification

### Navigation and panel model

- Dashboard, All Events, Create/Edit Event and Registrations remain pages. Trash is a view within each corresponding list, not a new page. Export remains an action.
- Create/Edit Event uses seven tabs: Event Setup, Page Content, Program, Partners & Sponsors, Registration & FAQ, Branding & Location, and Email. Event Setup holds identity/publish controls and Ticket Types. Other original panels remain within their logical tabs.
- The identity panel is always visible in Event Setup; when a tab contains more than one original panel, exactly one accordion layer is retained inside that tab. Single-panel tabs have no collapse control. This avoids nesting a new tab inside the old all-page accordion.
- Active tab is submitted in a hidden form field. On validation failure, the server selects the tab corresponding to the first failing field; the field is marked, linked to its error with `aria-describedby`, and focused. Other submitted values continue through the existing save handler and render path.

### Fields, expanded editing, and actions

- Every form field keeps a persistent visible label. The browser-side form enhancer assigns control IDs and label associations for original and dynamically-added text, textarea and select controls; media picker buttons have visible media labels. Required title/date markers remain, while the auto-generated slug is no longer presented as required.
- Desktop fields use compact dimensions and the existing two-column form grid for short values. Mobile fields become full-width with normal touch-height controls.
- One native dialog is reused for expanded editing. It clones the source field or editable repeater-row controls; edits affect only the clone until Apply. Cancel and Escape discard the clone. Apply writes values back to the original controls, including TinyMCE-backed event description. The dialog returns focus to the originating control (TinyMCE editor for description, row-edit trigger for a repeater row). The form submits only its original named controls, so payload ownership and names remain authoritative.
- The dashboard radial action menu is retained only on mobile, where the three desktop header actions would otherwise occupy the compact viewport. Event-list row actions and repeater actions remain plain controls because their labels and direct affordances are clearer than a second radial menu.

### Event Trash and recovery

- Events use the existing indexed `evt_events.deleted_at` column and shared 30-day cutoff convention. Single and bulk Trash require both a valid nonce/capability and an explicit confirmation signal; the message states the 30-day recovery window. Active lists exclude trashed events; Trash shows deletion time and Restore.
- Restore is rejected server-side when the timestamp is outside the 30-day window. Restoring within the window clears only `deleted_at`, retaining event identity/status and existing child rows, registrations, tickets, speakers, sponsorships, pillars, and mail columns.
- Hourly WordPress Cron selects expired events in batches of 25. Permanent event deletion is a transaction: it detaches registration event/ticket links, deletes the event's ticket and content rows, and deletes the event row; registration/payment records remain. Manual permanent deletion is offered only from Trash and requires an explicit confirmation stating the irreversibility and retained registration/payment history.
- WordPress Cron is traffic-triggered on this local setup: the expiration cutoff immediately excludes items from Trash and rejects restore at day 30; the physical purge runs on the next due cron execution, not at an exact wall-clock instant on a site with no traffic.

### Explicit change boundary and estimated footprint

| Added | Changed | Left alone |
|---|---|---|
| Seven form tabs, one reusable expanded editor, inline validation state/focus, explicit associations for dynamically-added media controls, shared compact density tokens, event permanent-delete helper and hourly expiry purge, event row/bulk Trash and permanent-delete controls, audit record. | Form field presentation and navigation only; legacy panel accordions now operate only within multi-panel tabs. Shared admin CSS now shows labels, removes inherited debug borders, and compacts controls/tables. Event Trash adds explicit permanent deletion and automatic expiry purge beyond the inherited reversible-trash behavior. | Save/update handler, field and repeater payload names, sanitization, slug helper, repeater ownership and deletion processing, metadata and visibility behavior, mail settings, payment/ticket/email flows, registration Trash behavior, public renderer, Laravel bridge and header parity work. |

Estimated content footprint after change: dashboard 360–420 px to Recent Registrations; event list 230–260 px to table header; registration list 270–300 px to table header; event-form setup 400–500 px to first identity fields. These estimates are targets only until verified in the authenticated browser at the stated viewport widths.

## Phase 4 — File-level implementation notes

| File | Implementation |
|---|---|
| `wp-content/plugins/custom-event-registration/admin/event-form.php` | Added posted/validated active-tab state, server selection for validation errors, a header save action associated with the existing form, the form ID/class used by the enhancer, accurate slug requirement presentation, and explicit media/map/UUID labels. The existing save/update code is not replaced. |
| `wp-content/plugins/custom-event-registration/assets/js/cer-admin-event-form.js` | Groups existing form fields/panels into tabs; adds keyboard tab navigation, one shared apply/cancel dialog for fields and repeater rows, focus restoration, expanded editor controls on initial and dynamically-added fields, visible inline-error marking/focus, and tab-scoped accordions. Existing repeater creation, deletion keys, date handling, media and map behaviors remain the backing controls. |
| `wp-content/plugins/custom-event-registration/custom-event-registration.php` | Adds translated form labels, hourly event expiry hook and deactivation cleanup, event physical-deletion helper with transactional detachment/child cleanup, per-item event Trash/permanent-delete action handling, nonce/capability/confirmation validation, and cache invalidation. Existing schema version 9 is reused without a new migration. |
| `wp-content/plugins/custom-event-registration/admin/events-list.php` | Adds event per-item Trash and explicit permanent-delete actions, bulk permanent deletion, server-side confirmation signals, and corresponding client prompts; existing filters, list data and columns remain. |
| `wp-content/plugins/custom-event-registration/admin/admin-functions.php` | Adds the permanent-delete success notice. |
| `wp-content/plugins/custom-event-registration/assets/css/cer-admin-modern.css` | Defines shared spacing, type and touch-target tokens. |
| `wp-content/plugins/custom-event-registration/assets/css/cer-admin-reporting.css` | Removes repeated label-hiding/debug declarations and applies compact form/list density, responsive tabs, error/dialog styling, and mobile touch relaxation to the existing pages. No stylesheet or library was added. |
| `master_md/56_icrhk_admin_compression_audit.md` | Records the inventory, measurable compression definition, design, implementation mapping, database observations and verification evidence. |
| `master_md/00_master_checklist.md` | Adds the audit document to the existing master index. |

## Phase 5 — Verification evidence

| Design element/check | Status | Viewport / environment | Evidence |
|---|---|---|---|
| PHP/JavaScript syntax and diff validation | Verified | CLI on local Windows/XAMPP workspace | `php -l` passed on nine changed plugin PHP files; `node --check` passed for event-form, dashboard and event-list scripts; `git diff --check` passed. CSS brace counts balanced (106/106 and 166/166). |
| Schema version and detachment columns | Verified | Local WordPress/PHP CLI and database | Schema option is 9; `deleted_at` columns exist; registration `event_id` and `ticket_type_id` are nullable. No rows were changed. |
| Dashboard desktop layout against the inherited compact targets | Not verified | Requested 1366×768 integrated browser viewport; local authenticated admin | Navigation to the dashboard timed out before the page rendered. |
| Event list, event Trash, and registration list/Trash | Not verified | Desktop, tablet and mobile integrated browser viewports | Authenticated local admin navigation did not complete; no visual conformance is claimed. |
| All seven event-form tabs and panel visibility | Not verified | Desktop/tablet/mobile integrated browser viewports | Browser interaction was blocked by the same admin timeout; PHP/JavaScript syntax checks passed. |
| Failed save selects the right tab, marks error and preserves other values | Not verified end-to-end | Local admin form; no completed browser viewport | Server tab selection and client error mapping were inspected, but a failed-save browser submission could not be completed. |
| Persistent labels and label/control associations on each registered page and dynamic fields | Not verified visually | Desktop/tablet/mobile integrated browser viewports | CSS and enhancer logic are implemented; rendered DOM audit was blocked by admin timeout. |
| Expanded editing: text, textarea, select, repeater row; Apply/Cancel/Escape and focus restoration | Not verified interactively | Desktop/tablet/mobile integrated browser viewports | Implementation is present; modal interactions could not be exercised. |
| Dashboard radial menu open/keyboard/Escape behavior | Not verified in this pass | Mobile integrated browser viewport | Inherited interaction remains; current visual check could not run. |
| Event single/bulk Trash confirmation, Trash timestamp, restore and associations | Not verified behaviorally | Local database/admin browser; no live-record mutation | Server-side paths were reviewed; a safe isolated browser/database behavior test was not run. No event or registration rows were changed. |
| 30-day restore cutoff and automatic expiry purge | Partially verified | Local PHP/database; no browser viewport | Cutoff and cron purge code were reviewed; nullable detachment columns were confirmed. A controlled age-expiry/purge test was not run. |
| Registrations Trash visible row/restore | Not verified | Prior narrow authenticated snapshot; current browser navigation unavailable | Database inspection found no trashed registration row. The prior snapshot was empty, so a restore row is not visually verified. Registration-trash behavior is unchanged by this pass. |
| Public-facing, payment, ticketing, mail delivery and header parity | Not tested; left untouched | Outside this admin-only verification | No such code paths were changed as part of this compression pass. |

## Unverified items and reasons

Post-change browser verification could not be completed: requests to the local authenticated WordPress admin timed out, and the Apache log recorded multiple 120-second PHP execution timeouts in unrelated WooCommerce, Elementor Pro, WPCode and WordPress core admin callbacks. This environment failure is not attributed to the ICRHK code without evidence. The live database had no trashed registrations and no event/registration rows were changed; a safe fixture or controlled local procedure is still needed to verify row-level Trash restore. WordPress Cron's purge is traffic-triggered, so exact automatic expiry timing on an idle production site cannot be guaranteed by this implementation.
