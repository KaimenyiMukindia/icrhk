# Backend Logic Fixes for Event Creation and Front-End Display

## Problems Identified
The event system was failing in three core backend areas:

1. The event insert/update flow was not writing to the `evt_events` table reliably, so the admin list was empty even after a “success” alert.
2. The slug field could be left blank or could conflict with an existing event, which caused validation errors and prevented clean front-end URLs.
3. The front-end page was not reading event data using the stored event row, ticket types, speakers, and sponsorship records, so the dynamic registration screen could not populate its sections.

## Fixes Applied
### 1. Event save flow
The event form save logic was corrected to ensure that:

- the event record is written with the correct fields and proper data types;
- a unique `event_uuid` is created for every event;
- slug generation happens automatically when the slug field is blank or left duplicated;
- nested ticket, speaker, and sponsor records are stored with the parent `event_id` after the event is successfully saved;
- deleted child records are removed before re-saving current rows.

### 2. Schema safety and migration
The plugin verifies the current database schema on admin load and adds the missing `slug` column or unique key if the schema is older than the implementation.

This keeps the system working even when the database was created before the slug support was added.

### 3. Front-end dynamic lookup
The registration template now resolves the current event by:

- `event_id` from the query string when available;
- `event_slug` from the query string when available;
- fallback to the latest published event when no selector is supplied.

The template then loads:

- the event row from `evt_events`;
- related ticket types from `evt_ticket_types`;
- speakers from `evt_speakers`;
- sponsors from `evt_sponsorships`.

### 4. Dynamic pricing and ticket selection
The registration form reads ticket name and price values from the saved ticket types and sets them into the hidden fields and summary area so the selected price is always in sync with the chosen ticket.

### 5. Registration persistence
The AJAX registration submission still uses the same security flow and now stores the `event_id` and `ticket_type_id` properly when a registration is submitted.

The Laravel bridge remains intact and continues to receive the registration payload without requiring removal of the older registration fields.

## Front-End URL Structure
The registration page supports unique event URLs such as:

- `/register?event_id=123`
- `/register?event_slug=my-event`

This allows multiple events to coexist while still keeping a clean, shareable registration URL for each one.

## Administrator Workflow
To create and test an event successfully:

1. Open the plugin admin menu and choose “Add New Event.”
2. Enter the event title, venue, dates, and description.
3. Leave the slug blank if you want it to be generated automatically.
4. Add at least one ticket type, one speaker, and one sponsorship package.
5. Set the status to “Published” when the event is ready to appear on the front end.
6. Save the event.
7. Confirm the event appears in the “All Events” list.
8. Open the front-end registration page using the event URL, such as `/register?event_slug=my-event`.
9. Verify that the event details, ticket list, speakers, and sponsors all render correctly.
10. Submit a registration and confirm it appears in the admin registrations list with the correct event and ticket association.

## Outcome
The system now supports:

- event creation and saved records in the admin list;
- unique event URLs for front-end registration pages;
- dynamic display of event details, ticket prices, speakers, and sponsors;
- correct registration saving with event/ticket association;
- working admin metrics and registration reporting.

The payment gateway remains intentionally out of scope and is still the only incomplete feature.
