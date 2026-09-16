# Data Persistence Fix for Event Creation, Listing, and Front-End Rendering

## Root Cause
The custom event plugin had been installed but was still inactive in WordPress. Because the plugin was not active, the activation hook never ran, which meant the custom tables such as `wp_evt_events`, `wp_evt_ticket_types`, `wp_evt_speakers`, `wp_evt_sponsorships`, and `wp_evt_registrations` were never created.

This explained the empty admin list and the missing front-end event data:

- there were no rows in `wp_evt_events`;
- the admin event list was querying a table that did not exist;
- the front-end template had no saved event row to render;
- the registration form had no ticket records from which to populate the dropdown or price summary.

## Evidence
The live database check showed:

- `SHOW TABLES LIKE 'wp_evt_%'` returned no event tables while the plugin was inactive.
- `SELECT COUNT(*) FROM wp_evt_events` returned a table-missing error before activation.
- After activating the plugin, the event and registration rows were successfully created and confirmed in the database.

Live proof after fix:

- `EVENT_COUNT=1`
- `REG_COUNT=1`
- `EVENT_ROW={"id":"1","name":"ICRHK Demo Event 2026","slug":"icrhk-demo-event-2026","status":"published"}`

## Actual Schema and Data Model Discovered
The custom plugin uses the WordPress table prefix and stores data in these tables:

- `wp_evt_events`
- `wp_evt_ticket_types`
- `wp_evt_speakers`
- `wp_evt_sponsorships`
- `wp_evt_registrations`

The important columns used by the event workflow are:

- `wp_evt_events`: `id`, `event_uuid`, `name`, `slug`, `description`, `event_date`, `event_end_date`, `venue`, `status`, `max_attendees`, `featured_image_id`, `created_at`, `updated_at`
- `wp_evt_ticket_types`: `id`, `event_id`, `name`, `price`, `currency`, `quantity_available`, `description`, `order_index`
- `wp_evt_speakers`: `id`, `event_id`, `name`, `role`, `bio`, `photo_id`, `order_index`
- `wp_evt_sponsorships`: `id`, `event_id`, `name`, `description`, `benefits`, `cta_url`, `order_index`
- `wp_evt_registrations`: `id`, `registration_uuid`, `event_id`, `ticket_type_id`, `full_name`, `email`, `phone`, `ticket_type`, `payment_method`, `amount`, `notes`, `status`

## Fixes Applied
### 1. Plugin activation and table creation
The plugin was reactivated so the activation routine could install the custom tables.

This was essential because the admin list, front-end loader, and registration form all rely on those tables existing in the database.

### 2. Event save flow validation
The event form save logic uses the correct table name and column names and creates a unique slug when the field is empty.

The save workflow persists the main event row, then stores ticket types, speakers, and sponsorships tied to the generated event ID.

### 3. Front-end event retrieval
The event page loads the event using the URL query parameter:

- `?event_id=123`
- `?event_slug=icrhk-demo-event-2026`

It then fetches the event, speakers, sponsorships, and tickets using that event ID.

### 4. Registration data association
The AJAX registration submission stores both `event_id` and `ticket_type_id` so the registration list and dashboard can show real data linked to the correct event and ticket.

## Working Front-End URL
The event page is accessible at the site’s published registration page:

- `http://localhost/icrhk/event-registration/?event_slug=icrhk-demo-event-2026`

The page successfully rendered:

- event title: “ICRHK Demo Event 2026”
- date: “September 15, 2026”
- venue: “Nairobi Convention Centre”
- a ticket option: “Community Outreach (5000.00 KES)”
- the registration form with the correct hidden event ID and ticket value

## Administrator Instructions
1. Activate the plugin from WordPress admin if it is inactive.
2. Open the Events admin menu and add a new event.
3. Save the event with a title, optional slug, date, venue, and at least one ticket type.
4. Open the front-end route using the event slug or event ID.
5. Confirm the event data is rendered on the page.
6. Submit a registration and verify it appears in the Registrations list.

## Final Outcome
The data persistence issue is resolved because the custom tables now exist, real event rows are written, and the front-end page reads data from the same database source used by the admin screens.
