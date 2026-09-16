# Step 4 - Custom WordPress Dashboard Plugin Development

## Task Description & Scope
- Built the custom event registration plugin to store submissions in a dedicated WordPress table and provide a complete admin command center for event operations, reporting, and registrations.
- Resolved the actual root cause behind the empty admin list and missing event rendering: the plugin was inactive, so the custom event tables were never created.
- Extended the event model with thematic pillars, section visibility toggles, and slug-based clean URLs.
- Re-validated the save, list, and front-end display flow so live event data now exists in the database and appears in the proper screens.

## Files Created, Updated, or Moved
- Updated: /wp-content/plugins/custom-event-registration/custom-event-registration.php
- Updated: /wp-content/plugins/custom-event-registration/admin/admin-functions.php
- Updated: /wp-content/plugins/custom-event-registration/admin/dashboard.php
- Updated: /wp-content/plugins/custom-event-registration/admin/events-list.php
- Updated: /wp-content/plugins/custom-event-registration/admin/registrations-list.php
- Updated: /wp-content/plugins/custom-event-registration/admin/event-form.php
- Updated: /wp-content/plugins/custom-event-registration/includes/registration-functions.php
- Updated: /wp-content/plugins/custom-event-registration/includes/class-laravel-connector.php
- Updated: /wp-content/plugins/custom-event-registration/includes/event-page-renderer.php
- Updated: /wp-content/themes/goodsoul/page-templates/template-event-registration.php
- Created: /master_md/12_admin_dashboard_reporting.md
- Created: /master_md/13_logic_fixes.md
- Created: /master_md/14_backend_logic_fixes.md
- Created: /master_md/15_data_persistence_fix.md
- Created: /master_md/17_dynamic_page_and_url_fix.md

## System & Code Changes Made
- Confirmed the custom event tables are created through the plugin activation hook and included the event schema safety checks.
- Corrected the underlying persistence logic so event rows, ticket rows, speakers, sponsorships, pillars, and registrations are linked to the right record IDs.
- Ensured the front-end registration page reads dynamic data from the database using slug-based clean URLs and query-var fallbacks.
- Kept the AJAX registration flow intact while preserving the event and ticket IDs in the registration record.
- Verified live data insertion and retrieval using direct database checks.
- Added schema migrations for the new pillar table, visibility flags, and target audience field.
- Added event page copy fields and section-visibility flags on the event record so the front-end body can be driven from saved event data.
- Restored the theme-owned page wrapper for event pages and removed the custom standalone shell from the template layer.
- Converted admin visibility controls into switch-style toggles and added collapsible section controls on the event form panels.
- Removed the obsolete event snapshot side panel and changed the event form layout to use the full available width.
- Reworked the dashboard content grid so recent registrations and quick links each span the full admin width cleanly.
- Added versioned schema migrations and explicit indexes for event dates and registration filters; schema checks no longer run on every admin request.
- Replaced per-event registration subqueries and non-sargable date predicates with grouped aggregates and direct datetime ranges.
- Added versioned object caching for event-page data and moved Laravel registration synchronization to a scheduled background action.

## Root Cause Identified
The custom event registration plugin had been installed but remained inactive. Because the plugin was inactive, the activation routine never ran and the custom tables were never created. As a result, there were no rows in `wp_evt_events`, which caused the admin list to be empty and the front-end page to render no real event data.

## Evidence of Working State
- `EVENT_COUNT=1`
- `REG_COUNT=1`
- `EVENT_ROW={"id":"1","name":"ICRHK Demo Event 2026","slug":"icrhk-demo-event-2026","status":"published"}`
- `PILLAR_TABLE=created`
- Front-end page successfully rendered the live event title, date, venue, and ticket selection on `http://localhost/icrhk/event-registration/?event_slug=icrhk-demo-event-2026`

## Payment Integration Notes
- The plugin now creates a registration record with a unique `payment_uuid` before the Laravel payment engine is called.
- Order initiation is handled via the existing Laravel bridge pattern using `wp_remote_post` to the payment endpoint.
- Admin payment settings allow override of the bridge URL and PesaPal credentials without hardcoding values in plugin files.
- The data model now includes `payment_uuid` and `gateway_reference` for reconciliation and duplicate prevention.

## Verification Checklist Status
- [x] Plugin activated and custom tables created.
- [x] Event row exists in the database.
- [x] Ticket row exists for the event.
- [x] Front-end event page loads the event by slug.
- [x] Registration row is stored with event and ticket association.
- [x] Admin dashboard/list and registrations list can display real data.
- [x] Documentation updated to reflect the real persistence fix.
- [x] Payment initiation is now routed through the Laravel engine and PesaPal gateway.
- [x] Pillars, section visibility toggles, and clean URLs were added.

## Production Hardening

- Public event lookups now require `status = published`. Draft, closed, and cancelled events can only be opened through the nonce-protected administrator preview link.
- Registration requests derive ticket identity and price from the selected database ticket row; client-submitted prices are ignored.
- Registration PII remains encrypted. The admin list uses deterministic keyed hashes for exact name, email, phone, or registration-UUID lookup rather than SQL `LIKE` over ciphertext.
- Paid confirmation is serialized per event and ticket to enforce event capacity and ticket inventory before incrementing `quantity_sold`.
- Ticket delivery callbacks accept either an authenticated administrator nonce or a short-lived signed Laravel callback.
