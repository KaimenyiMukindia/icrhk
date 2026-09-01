# ICRHK Replication Baseline

This repository is the source-code baseline for the current ICRHK WordPress and Laravel implementation. It does not store production secrets, database dumps, uploaded media, caches, logs, or third-party plugin backup archives.

## Git Scope

Track the custom implementation and the information needed to rebuild it:

- `wp-content/plugins/custom-event-registration/`
- `wp-content/themes/goodsoul/`
- `laravel-engine/`, excluding `vendor`, storage, and local environment files
- `master_md/` technical audits and verified behavior notes
- `wp-config-sample.php`, `laravel-engine/.env.example`, and documented configuration templates

Do not add `wp-config.php`, `.env`, `laravel-engine/.env`, database dumps, or `wp-content/uploads/`. These contain credentials, personal data, operational data, or large binary files and must be handled as controlled release artifacts.

## Current-State Artifacts

The current implementation cannot be reproduced from source alone. Create one timestamped release bundle outside Git containing:

1. A MySQL/MariaDB dump with schema, data, triggers, routines, events, and the `utf8mb4` character set.
2. A compressed archive of `wp-content/uploads/` preserving relative paths.
3. A copy of third-party plugin/theme packages that are required but not reconstructed from Composer or npm lock files.
4. An environment manifest recording PHP, MySQL/MariaDB, WordPress, Node, Composer, active plugin, cron, queue, payment callback, and web-server versions/settings. Do not include secrets.
5. SHA-256 checksums for every artifact and the Git commit SHA that produced the bundle.

Store the bundle in access-controlled backup storage. Record its storage location and access process in a private operations system, not in this repository.

The existing `db-backups/` SQL files are historical local evidence. They must be verified against the running database before being treated as the replication baseline.

## Restore Order

1. Check out the tagged Git revision.
2. Install PHP, Composer, Node, WordPress, and database versions from the environment manifest.
3. Restore Composer and npm dependencies from their lock files.
4. Install the exact third-party plugin/theme packages from the release bundle and activate the versions in the environment manifest.
5. Restore uploads before importing the database so attachment references resolve.
6. Import the database dump, configure local secrets from secure storage, and update environment-specific URLs through a serialized-data-safe WordPress migration process.
7. Configure web-server rewrite rules, WordPress cron, Laravel workers, payment webhook URLs, and provider credentials.
8. Run the functional checks below before declaring the replica operational.

## Functional Acceptance Checks

- Public event route resolves at `/event/{slug}/` and loads registration settings.
- Event registration validates its nonce and stores `event_id` and `ticket_type_id`.
- Event administration manages events, ticket types, speakers, sponsors, pillars, registrations, and dashboard reporting.
- Laravel payment routes accept a test request, persist payment state/logs, and return the expected callback response.
- A payment-provider sandbox test and callback test complete without exposing live credentials.
- Desktop and mobile visual checks match the release screenshots for the event page and administrator screens.

## Git Release Procedure

1. Confirm `git status` contains only intended source, documentation, and configuration-template changes.
2. Run the relevant Laravel test suite and PHP syntax checks.
3. Commit the source baseline and create an annotated tag such as `replication-2026-09-01`.
4. Create the controlled data/media artifact bundle and its checksum manifest referencing that tag.
5. Add a remote repository, then push the commit and tag. This workspace currently has no Git remote, so a push cannot occur until one is configured.

## Evidence Available Here

- `master_md/` contains implementation, payment, audit, and live-test notes.
- The custom event plugin owns the `wp_evt_events`, `wp_evt_ticket_types`, `wp_evt_speakers`, `wp_evt_sponsorships`, `wp_evt_registrations`, and `wp_evt_pillars` tables.
- `laravel-engine/database/migrations/` records Laravel-owned schema changes; the WordPress custom-table schema must be verified from the current database dump and plugin install/upgrade routines.