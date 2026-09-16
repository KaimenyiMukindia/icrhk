# Step 33 - Admin Share Modal

## Changes

- Replaced the direct Facebook Share link in the All Events admin list with an accessible Share button using a WordPress Dashicon.
- Added a reusable modal containing the event title, clean public event URL, copy action, and links for Facebook, X/Twitter, LinkedIn, WhatsApp, and Email.
- Share URLs are generated in the admin JavaScript from the event-specific URL and title passed through escaped data attributes.
- Added close-button, backdrop-click, Escape-key, focus restoration, and copy-to-clipboard behavior.
- Loaded the new admin JavaScript only on the `cer-events` page and kept the modal styling within the existing admin stylesheet.

## Verification

- PHP syntax checks should be run against `events-list.php` and `custom-event-registration.php` after deployment.
- JavaScript syntax can be checked with `node --check assets/js/cer-admin-events.js`.
- Each platform link uses the same clean slug-based URL already shown in the event list.