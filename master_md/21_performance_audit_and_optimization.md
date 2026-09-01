# Performance Audit and Optimization

## Scope

Audited the custom event registration plugin, Goodsoul event template path, Laravel connector, live `wp_evt_*` tables, and local Apache/PHP configuration on 2026-08-20.

## Findings

- `cer_install_event_schema()` was called from `admin_init` on every admin request. It performed repeated `SHOW COLUMNS` checks and schema work even when no migration was pending.
- The live tables initially had only the primary, foreign-key, and uniqueness indexes. `wp_evt_events.event_date` and the registration `created_at`, `status`, and `payment_method` columns were not indexed.
- The All Events query ran three correlated registration aggregates for every event row: total registrations, paid/confirmed tickets, and revenue. This scaled as an N+3 pattern.
- The All Events month filter used `DATE_FORMAT(event_date, ...)`, preventing a normal range lookup on `event_date`.
- The Registrations list used `DATE(created_at)` for date filters, preventing use of a `created_at` index.
- Dashboard metrics used six separate aggregate queries before the recent-registration query. Metrics were transient-cached, but a cache miss still caused unnecessary round trips.
- Front-end related-row loading performed `SHOW COLUMNS` for each related table to discover visibility support. Event and related data were not object-cached.
- Registration AJAX inserted locally and then waited synchronously for the Laravel HTTP bridge, whose timeout was 10 seconds.
- The public route loaded the full Goodsoul/Elementor theme stack and external fonts. These are outside the custom table query path and remain the largest residual local timing risk.

## Changes Applied

### Database and migrations

- Added a version guard (`cer_schema_version = 3`) so schema verification runs only when a migration is pending.
- Added an explicit existing-index migration because `dbDelta()` did not add the new keys to already-created tables.
- Added indexes:
  - `wp_evt_events.event_date`
  - `wp_evt_registrations.created_at`
  - `wp_evt_registrations.status`
  - `wp_evt_registrations.payment_method`
  - `wp_evt_registrations (event_id, status, created_at)`
- The migration was run against the local database and verified with `SHOW INDEX`.

### Admin queries

- Replaced the three per-event correlated aggregates with one grouped registration subquery joined to the event list.
- Replaced the event month `DATE_FORMAT()` predicate with an inclusive start and exclusive next-month datetime range.
- Replaced registration `DATE()` predicates with direct datetime comparisons. End dates use the next day as an exclusive bound, preserving the UI's inclusive date behavior.
- Consolidated dashboard event metrics into one query and registration metrics into one query, reducing six metric queries to three total metric queries including sponsorship count.

### Caching

- Added WordPress object-cache entries for event records and related speakers, sponsorships, and pillars.
- Added a versioned event-cache namespace. Saving an event or related rows increments the namespace, so persistent object caches cannot serve stale event content after an admin save.
- Existing five-minute dashboard transient behavior remains and is still invalidated after event and registration saves.

### Laravel bridge

- Registration AJAX now returns after the local insert and schedules `cer_sync_registration` through WordPress cron.
- The existing connector and error logging are retained in the scheduled callback. A failed remote sync no longer blocks the visitor's registration response for up to 10 seconds.

## Verification Evidence

- Initial live `SHOW INDEX` output showed no `event_date` index and no registration `created_at`, `status`, `payment_method`, or composite index.
- After migration, `SHOW INDEX` confirmed all five new keys.
- `EXPLAIN` for the event month query selected `event_date`; the grouped registration lookup selected `event_id`.
- `EXPLAIN` for filtered registrations selected `event_status_created`.
- PHP lint passed for every modified PHP file.
- The public event URL continued to return HTTP 200, 146,136 bytes, with both the event title and registration form present.
- Repeated public-route timing before the final cache pass was approximately 13.4-17.2 seconds; a later request measured approximately 5.5 seconds. This is a meaningful local improvement, but it is not sub-second.
- Admin timing was not reported as a page-load metric because unauthenticated requests returned the WordPress login response rather than the dashboard/list pages. Authenticated browser timing should be captured separately.
- Query-count before/after was not claimed because `SAVEQUERIES` was disabled and no authenticated admin session was available to instrument. The SQL shape and query-plan improvements are verified directly.

## Maintenance Guide

- Bump `cer_schema_version` when adding a migration and make the migration idempotent. Prefer explicit index checks for existing tables.
- Keep filters on raw indexed columns. Use half-open datetime ranges instead of wrapping columns in `DATE()` or `DATE_FORMAT()`.
- Keep event-list aggregates grouped by `event_id`; do not add per-row subqueries in the render loop.
- Invalidate the dashboard transient after any write that changes event, registration, or sponsorship metrics.
- Increment `cer_event_cache_version` whenever event or related content changes.
- Keep Laravel synchronization asynchronous. Monitor scheduled events and log failed bridge responses.
- For production measurements, enable `SAVEQUERIES` only in a controlled development environment, capture authenticated requests, and use `EXPLAIN` on any new list or report query.
- Re-test the public route with and without a persistent object cache. The remaining 5-second-class timing should be profiled in the theme/Elementor bootstrap before claiming a sub-second target.
