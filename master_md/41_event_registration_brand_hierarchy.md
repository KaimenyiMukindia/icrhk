# Event Registration Brand Hierarchy

## Scope

Updated the event-registration hero to establish the requested institutional and conference branding hierarchy using the existing database-backed event and partner records.

## Implementation

- Co-convenor logos now render first inside the hero in an ordered, responsive logo rail sourced from `wp_evt_partners`; institution titles are intentionally omitted from this top rail.
- Partner logo attachment IDs remain database-controlled; no event-specific logo IDs were hard-coded.
- The conference mark from `secondary_logo_id` is now visually larger and remains the primary conference emblem.
- Institution names in the lower Partners section are formatted for display with initial capitalization while preserving uppercase acronyms such as `NSDCC`.
- The existing Partners section remains available later on the page, so the footer presentation and partner links are preserved.

## Verified Live Data

The published KAMGC 2026 event uses partner rows for Ministry of Gender, Culture and Children Services, National Syndemic Diseases Control Council, and Council of Governors. Their existing attachment IDs are used by both the hero group and the lower partner section.

## Validation

- `php -l wp-content/plugins/custom-event-registration/includes/event-page-renderer.php`
- `git diff --check`
- Live route checked: `/event-registration/?event_slug=kamgc-2026`
- Browser DOM confirmed the co-convenor group is the first hero content block, contains three logo images, and leaves the lower partner titles intact.