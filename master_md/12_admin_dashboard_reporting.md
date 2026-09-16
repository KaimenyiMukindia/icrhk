# Admin Dashboard & Reporting

## Overview
The event registration plugin now includes a modern admin experience with a dashboard, a richer event management list, and an upgraded registrations reporting screen. These pages are designed to support event operations, revenue tracking, and quick reporting without needing a payment gateway implementation.

## Dashboard
The Dashboard page is available under the plugin’s top-level menu as the first entry, labeled “Dashboard”. It includes:

- Published event count
- Total registration count
- Tickets sold
- Revenue collected
- Sponsorship package count
- Recent registration activity table
- Quick links for event creation and registration review

The metrics are aggregated from the custom WordPress tables and cached for five minutes using WordPress transients to reduce load during repeated admin visits.

## All Events
The All Events screen now includes:

- Search by event title or venue
- Status filter
- Month filter
- Event count metrics such as sold tickets and revenue
- Bulk actions for publish, close, and delete
- Quick links to edit an event or view registrations for a selected event

This page reads from the `evt_events` table and joins with `evt_registrations` to compute totals dynamically for each event.

## Registrations
The Registrations screen includes:

- Event, ticket type, payment method, status, and date filters
- Search by name, email, or phone number
- CSV export of the active filtered result set
- Bulk update actions for paid/cancelled/delete on selected rows
- Pagination and sortable data output

The screen joins `evt_registrations` to both `evt_events` and `evt_ticket_types` so the event names and ticket types can be displayed even when related records are missing.

## Database Notes
The system relies on the following tables:

- `{$wpdb->prefix}evt_events`
- `{$wpdb->prefix}evt_ticket_types`
- `{$wpdb->prefix}evt_speakers`
- `{$wpdb->prefix}evt_sponsorships`
- `{$wpdb->prefix}evt_registrations`

No schema changes were required for the reporting/dashboard implementation beyond the existing fields already used by the plugin. The plugin remains compatible with the Laravel bridge and preserves fields like `full_name`, `email`, `phone`, `ticket_type`, `amount`, and `payment_method`.

## Usage Notes
- Use the filter bar on the Dashboard or registration screens to narrow the view quickly.
- Use the CSV export from the Registrations page to send a report to a stakeholder or use in spreadsheets.
- Use the bulk controls to change registration statuses or manage large event lists efficiently.

## Assumptions / Limitations
- Payment gateway integration remains pending and is intentionally out of scope for this phase.
- The status values used for successful transactions are currently read from the custom registration status field, using `paid` and `confirmed` as successful outcomes where relevant.
- Real-time check-in tracking is not yet implemented and is therefore omitted from the dashboard metrics.
