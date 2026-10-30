# Event Header Parity Audit

## Scope and evidence

Audited the repository implementation and the public standard and event pages in a browser on 2026-10-29:

- Event: `https://icrhk.org/event/kamgc-2026/`
- Standard baseline: `https://www.icrhk.org/`
- Browser CSS viewport measurements: desktop 1440px, tablet 768px, mobile 390px.
- Browser evidence: DOM header counts, rendered Elementor template IDs, computed styles, loaded stylesheet order, responsive logo/nav state, and sticky element settings/classes.

The initial audit preceded code changes. A follow-up local plugin correction is documented under **Fix Status** below. No production Elementor condition was changed.

## 1. Header source inventory

**Found:** The ordinary Goodsoul page template [page.php](../wp-content/themes/goodsoul/page.php) calls `get_header()`. The theme [header.php](../wp-content/themes/goodsoul/header.php) emits `wp_head()`, `wp_body_open()`, and then `get_template_part( 'components/header/header' )`. The native Goodsoul header selector [components/header/header.php](../wp-content/themes/goodsoul/components/header/header.php) chooses among `components/header/header-list/header-one.php` through `header-six.php` using the per-page header meta or the global `header_tob_bar_style` option.

**Live mechanism:** The public browser DOM does not contain Goodsoul's `.main-header` markup. It contains exactly one `<header class="elementor ... elementor-location-header">`. The standard page header is Elementor Theme Builder template `8603` (`elementor-8603`), and its generated stylesheet is `uploads/elementor/css/post-8603.css`. This is evidence that Elementor Pro's Theme Builder location is supplying/replacing the theme header on the live site; the Goodsoul header list is its fallback implementation, not the rendered header here.

**Defect:** Not a duplicate implementation in the rendered page. The two pages select different Elementor location templates.

## 2. Event page header path

**Found:** [template-event-registration.php](../wp-content/themes/goodsoul/page-templates/template-event-registration.php) calls `get_header()` once, then calls `cer_render_event_registration_page()`, then `get_footer()`. The renderer [event-page-renderer.php](../wp-content/plugins/custom-event-registration/includes/event-page-renderer.php) starts the event body at `<div class="cer-event-page">`; it does not emit a header.

The live event body is `elementor-page-9788`. Its only rendered header is Elementor template `10256` (`elementor-10256`); its generated stylesheet is `uploads/elementor/css/post-10256.css`. Browser counts: one `<header>`, one `.elementor-location-header`, zero `.main-header`.

**Defect:** Event page uses a distinct Elementor header template (`10256`) instead of the standard page header (`8603`). The custom event renderer does not duplicate the header.

## 3. Stylesheet load order and enqueue

The plugin enqueues assets in `cer_enqueue_frontend_assets()` in [custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php). It loads the critical inline CSS, event fonts/material symbols, `cer-event.css`, AOS, payment JS, and registration JS only on event pages. The live browser stylesheet order for the event route, recorded by DOM order and shortened to URL basenames, was:

```text
00 fonts.googleapis.com/css
01 charitable-elementor-editor.css
02 style.min.css (block-library)
03 design-tokens.min.css
04 components/style.min.css
05 preferences/style.min.css
06 block-editor/style.min.css
07 reusable-blocks/style.min.css
08 patterns/style.min.css
09 media-utils/style.min.css
10 editor/style.min.css
11 blocks.style.build.css
12 font-awesome.min.css
13 bootstrap-front.css
14 animate.css
15 icon-picker.css
16 glyphicon.css
17 dashicons.css
18 jquery.mCustomScrollbar.css
19 styles.css
20 front.min.css
21 wpdm-icons.css
22 front.min.css
23 front-dark.min.css
24 wpdm-modal.min.css
25 sr7.css
26 font-awesome.min.css
27 glyphicon.css
28 dashicons.css
29 bootstrap.css
30 owl.carousel.min.css
31 owl.theme.default.min.css
32 service-box-columns-layout.css
33 fonts.googleapis.com/css
34 glyphicon.css
35 dashicons.css
36 font-awesome.min.css
37 bootstrap-front.css
38 animate.css
39 jquery.mCustomScrollbar.css
40 woocommerce-layout.css
41 woocommerce-smallscreen.css
42 woocommerce.css
43 tribe-events-single-skeleton.css
44 tribe-events-single-full.css
45 widget-base.css
46 charitable.min.css
47 dashicons.min.css
48 eae.min.css
49 peel.css
50 v4-shims.min.css
51 all.min.css
52 vegas.min.css
53 yith-quick-view.css
54 fonts.googleapis.com/css
55 fonts.googleapis.com/css2
56 cer-event.css
57 aos.css
58 goodsoul/dist/output.css
59 dflip.min.css
60 elementor-pro/modules/sticky.min.css
61 elementor slideInDown.min.css
62 elementor/frontend.min.css
63 elementor/widget-image.min.css
64 elementor-pro/widget-nav-menu.min.css
65 elementor-pro/modules/motion-fx.min.css
66 elementor/widget-icon-box.min.css
67 elementor/widget-heading.min.css
68 elementor/widget-icon-list.min.css
69 elementor/widget-social-icons.min.css
70 elementor/apple-webkit.min.css
71 elementor/widget-posts.min.css
72 elementor post-9.css
73 sticky-header-effects/she-header-style.css
74 elementor post-10256.css
75 elementor post-7867.css
76 elementor post-9788.css
77 recent-posts-widget-with-thumbnails/public.css
78 goodsoul/bootstrap.css
79 goodsoul/bootstrap-select.min.css
80 goodsoul/animate.css
81 goodsoul/custom-animate.css
82 goodsoul/font-awesome.css
83 goodsoul/flaticon.css
84 goodsoul/jquery.fancybox.min.css
85 goodsoul/jquery-ui.css
86 goodsoul/owl.css
87 goodsoul/swiper.min.css
88 goodsoul/style.css
89 goodsoul-base-style.css
90 goodsoul/responsive.css
91 widget_base.css
```

The event-only additions relevant to this audit are `cer-event.css`, its Google font styles, AOS, and Elementor post styles `post-10256.css` and `post-9788.css`. The standard page instead loads Elementor `post-8603.css` and `post-26.css`, and does not load `cer-event.css`.

Event inline style blocks occur in this order: `wp-img-auto-sizes-inline-css`, `wp-emoji-styles-inline-css`, `classic-theme-styles-inline-css`, the create-block and charitable block styles, `global-styles-inline-css`, WooCommerce inline CSS, Dashicons inline CSS, YITH inline CSS, `cer-event-critical-inline-css`, Font Awesome inline CSS, `goodsoul-style-inline-css`, charitable highlight styles, two anonymous style blocks, `wp-custom-css`, `goodsoul_options-dynamic-css`, and two additional anonymous style blocks. None of the observed inline style blocks had selectors matching the Elementor header. `#wpadminbar` and its stylesheet were absent because the browser session was logged out.

**Cascade result:** Event CSS is enqueued before Elementor's frontend and per-template CSS. More importantly, CSSOM matching found no selector in `cer-event.css` that matches the actual Elementor header or its descendants. The later Elementor template CSS supplies the winning header rules. The event page does not load header styles after Elementor that override it.

## 4. Cascade analysis

**Header layout winners:** The active Elementor template root and its settings drive header height, sticky position, top offset, and z-index. At desktop, event container data-id `48909e04` computes to `min-height: 130px`, `padding: 30px 100px 0`, `position: fixed`, `top: 0`, `z-index: 99`. Standard template data-id `1c342af` computes to `min-height: 0`, `padding: 0`, `position: fixed`, `top: 0`, `z-index: 10`.

**Navigation winners:** Both templates use Roboto, 15px, weight 600, and `13px 20px` link padding on desktop. Standard links compute to `line-height: 22.5px`; event links compute to `line-height: normal`, producing a 44px nav box instead of 48.5px. This is a per-template generated Elementor rule difference, not `cer-event.css`.

**Container/logo:** Both use a 1140px maximum-width inner container at desktop, the same logo attachment `wp-image-10387`, and 208.0 × 58.3px desktop rendering. The logo and container sizing are substantially the same; the extra event header space comes from the outer Elementor template root.

**Defect:** Header-specific Elementor template settings differ; no event-body wrapper or event stylesheet wins the header cascade.

## 5. Override origin

**Found:** The `.cer-event-page` wrapper is emitted after the header, so its descendant selectors cannot affect the preceding header. The apparent header rule in [cer-event.css](../wp-content/plugins/custom-event-registration/assets/css/cer-event.css), `.cer-event-page .site-header, .cer-event-page .site-header nav, .cer-event-page .main-navigation, .cer-event-page .navbar`, is inert on the live markup: the Elementor header is not a descendant of `.cer-event-page` and has none of those Goodsoul classes.

`body:has(.cer-event-page)` in the event stylesheet sets only background-image/repeat/position/size/attachment. It does not set header typography or sizing. Event typography selectors are rooted in `.cer-event-page`/`.cer-kamgc-page` content classes.

**Defect:** The live divergence is not caused by broad event-body typography leaking into the header. The defect is selection of header template `10256` for the event page.

## 6. Duplication

**Found:** The custom event template calls `get_header()` exactly once; the renderer contains no header markup. Live DOM has one header, one Elementor location header, one logo image, and no Goodsoul `.main-header` markup.

**Defect:** No duplicate header markup or duplicate logo output was found. There is a stale, non-matching event CSS selector for a hypothetical `.site-header`/`.navbar`; it does not affect the current header. It is not the cause of the parity failure.

## 7. Colour boundary

The custom event code owns event-body brand variables (`--cer-brand-color-1`, `--cer-brand-color-2`), body/event background treatment, and event-section colors. Those declarations are scoped to the body/event content and were not changed. The inert `.cer-event-page .site-header...` rule sets only position/z-index, not color, and does not match the live header. No Elementor color values were copied or introduced by this audit.

**Defect:** No color defect assessed or changed; color remains out of scope.

## 8. Standard-page baseline

Computed browser comparison (CSS viewport; measurements are rounded to 0.1 CSS px):

| Property | Viewport | Standard page (`8603`) | Event page (`10256`) |
|---|---:|---|---|
| Header rendered height | Desktop 1440 | 98.3px | 130.0px |
| Header rendered height | Tablet 768 | 76.1px | 130.0px (sticky spacer min-height 130px) |
| Header rendered height | Mobile 390 | 89.5px | 130.0px (sticky spacer min-height 130px) |
| Inner container max-width | Desktop 1440 | 1140px | 1140px |
| Inner container width | Tablet 768 | 745.2px | 745.2px |
| Inner container width | Mobile 390 | 367.2px | 367.2px |
| Desktop nav link | Desktop 1440 | Roboto, 15px, 600, line-height 22.5px; padding 13px 20px | Roboto, 15px, 600, line-height normal; padding 13px 20px |
| Desktop nav box height | Desktop 1440 | 48.5px | 44.0px |
| Collapsed menu toggle | Tablet 768 | 33 × 33px | 33 × 33px |
| Collapsed menu toggle | Mobile 390 | 49.5 × 49.5px | 49.5 × 49.5px |
| Collapsed menu toggle visibility | Mobile 390 | visible | hidden (`elementor-invisible` on nav widget) |
| Logo | Desktop 1440 | 208.0 × 58.3px | 208.0 × 58.3px |
| Logo | Tablet 768 | 129.0 × 36.1px | 129.0 × 36.2px |
| Logo | Mobile 390 | 114.9 × 32.2px | 114.9 × 32.2px |
| Logo source | All | WordPress attachment `wp-image-10387`, same `src`/`srcset` | WordPress attachment `wp-image-10387`, same `src`/`srcset` |
| Sticky position/top | Desktop, tablet, mobile | fixed, top 0px; sticky enabled on all devices, offset 0 | fixed, top 0px; sticky enabled on all devices, offset 0 |
| Sticky z-index | All widths | 10 | 99 |


Homepage sticky state was verified both at `scrollY=0` and `scrollY≈500`: fixed at `top: 0`, `z-index: 10`. The event header was verified at `scrollY=0`, `500`, and `600` on desktop/tablet/mobile samples: fixed at `top: 0`, `z-index: 99`, height 130px. Both templates declare sticky enabled on all devices with zero offset.

## Fix decision and status

### Local implementation correction

The event route was missing Elementor's existing active-kit body class even though it loads the active kit stylesheet (`post-9.css`). Without that class, its generated generic nav rule has an unresolved `--e-global-typography-primary-line-height`, so the nav link computes to `normal`; adding the existing active class in a browser check changes it to `22.5px`, without changing the header height.

The plugin now registers `cer_add_active_elementor_kit_body_class()` on `body_class` only for event routes when Elementor is loaded. It reads the active kit through Elementor's `kits_manager->get_kit_for_frontend()` API and adds the kit's existing `elementor-kit-{id}` class. Local browser verification after the change shows event and standard pages both use header template `8603`, with 22.5px nav link line-height at desktop, tablet, and mobile. A computed-style comparison across 444 event-body descendants found no differences in font, spacing, color, background, layout, or stacking properties when the active kit class was toggled. No CSS, header markup, or event content was added or changed.

The PHP syntax check passed for [custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php).

Local post-fix measurements are separate from production. Both local pages render `elementor-8603`; the local event now receives the same active kit class as the standard page. The measured values match:

| Local property | Desktop 1440 | Tablet 768 | Mobile 390 |
|---|---|---|---|
| Header height, standard/event | 98.3 / 98.3px | 76.1 / 76.1px | 89.5 / 89.5px |
| Inner container max-width, standard/event | 1140 / 1140px | 1024 / 1024px | 767 / 767px |
| Nav link, standard/event | Roboto 15px/600, 22.5px line-height | Roboto 15px/600, 22.5px computed (collapsed) | Roboto 15px/600, 22.5px computed (collapsed) |
| Logo, standard/event | 208.0×58.3 / 208.0×58.3px | 129.0×36.1 / 129.0×36.1px | 114.9×32.2 / 114.9×32.2px |
| Sticky, top, z-index | fixed / 0 / 10 | fixed / 0 / 10 | fixed / 0 / 10 |

The local pages share the same logo attachment and responsive template. The local theme uses its local `2026/09` logo file; production uses the live `2026/10` upload. Those are different environment assets with matching measured dimensions, not a code-path parity claim about the image bytes.

### Production template assignment and live defects

The live page still renders header template `10256`; changing that assignment requires the production Elementor Theme Builder condition data. Local database inspection found a different site state: there is no template `10256`, local post `9788` is an archive template, and templates `8603` and `7726` both have `include/general`. This local database must not be used to overwrite production conditions.

Production browser checks additionally found:

- **Anchor obstruction observed:** clicking the live header's `#registration` link navigated to `https://www.icrhk.org/event/kamgc-2026/#registration`. The target section then computed to `y: 0.37px`, `scroll-margin-top: 0px`, while the fixed header occupied `y=0..129.99px`; the section heading is obscured by the sticky header. At the sampled scroll position the sticky header remained `position: fixed; top: 0; z-index: 99`.
- **Mobile menu visibility defect observed:** at 390px, the event header's Elementor nav widget retains `elementor-invisible`; its menu toggle computes to `visibility: hidden`, and the dropdown is hidden with zero height. A physical click left `aria-expanded=false`. The standard header template `8603` on the same live domain has a visible menu toggle at 390px. This is a template/Elementor animation-state difference, not a z-index inference.
- **Dropdown under-content defect not observed:** desktop dropdown panels were `display:none`; on mobile the event dropdown panels were `visibility:hidden` and zero-height. No open dropdown was seen underneath event content.
- **Top-of-page overlap test:** the event body begins at y≈130px, equal to the header's reserved outer height. The decorative `.cer-top-glass-backdrop` covers y=0..160px at z-index 1 behind the header's z-index 99 container. It covers only the top decorative area of the hero; no interactive hero control intersected the tested top band. No click interception by this backdrop was observed.


The smallest correct production correction remains: remove the event route's assignment to header template `10256` and let standard template `8603` match, after confirming the conditions of both templates. The anchor-offset defect should then be retested; if it persists with template `8603`, set Elementor's native sticky anchor-link offset in the owning header template rather than adding CSS. The hidden mobile toggle should also be retested after the swap; template `8603` did not show this state on the same live site.

The display-condition metadata is stored in the live WordPress/Elementor database, not in this repository. Exhaustive local inspection found no production database dump or Elementor export; the production public REST endpoints return `401 rest_forbidden` for Elementor template metadata, `/wp-login.php` and the Theme Builder admin route are hidden as 404, and no authenticated admin session is available. The exact access required to finish the production swap is an authenticated WordPress administrator account with Elementor Theme Builder access, or a controlled read/write connection to the production WordPress database plus a backup. Do not copy the local condition data to production.

Parity is **not complete**. Live template height, nav line-height, z-index, the anchor obstruction, and mobile toggle visibility remain different from the standard template. After an authorized production condition change, rerun computed-value comparisons at 1440px, 768px, and 390px, click the event anchors, test the mobile menu and dropdown stacking, and verify sticky state after scroll before marking the relevant acceptance criteria complete.
