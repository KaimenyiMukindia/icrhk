# Dynamic Event Page, Clean URLs, and Pillars

## Summary
This update finished the event-registration rebuild around the live WordPress tables and the new front-end design.

## What Was Fixed
- Events now persist through the admin form and remain visible in the All Events list.
- Slugs are auto-generated when blank and kept unique.
- The front-end page now loads event data, tickets, speakers, sponsors, and thematic pillars from the database.
- The registration form keeps the AJAX flow intact while sending `event_id` and `ticket_type_id` to `evt_registrations`.
- Clean URLs now resolve by slug and load the registration template.

## Schema Changes
- Added `wp_evt_pillars` for thematic pillars.
- Added `target_audience` to `wp_evt_events`.
- Added visibility flags on event-level sections and related rows so speakers, sponsors, and pillars can be hidden from the front end.

## Clean URL Flow
- WordPress rewrite rules map `/{event-slug}` to the event registration template.
- The template resolves the slug from the query var first, then falls back to the request path.
- If no matching event exists, the page returns a 404 state.

## Front-End Structure
- Hero section with title, date, venue, and actions.
- Two-column content layout with event details, registration form, sponsorship cards, and payment summary.
- Keynote speakers grid.
- Thematic pillars grid with hover transitions.
- M-Pesa/Card toggle and modal-driven payment simulation.

## How To Use It
1. Create or edit an event in the admin Events screen.
2. Add ticket types, speakers, sponsors, and pillars.
3. Publish the event.
4. Open the slug-based URL for the event to view the registration page.
5. Submit a registration to confirm the row appears in Registrations.

## Limitations
- Payment gateway integration is still simulated and remains the final pending step.
- The new clean URL route depends on flushed rewrite rules after activation or deactivation.