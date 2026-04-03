# Discover Tasty Food — Changelog

## [1.8.1] — 2026-04-03
### Security hardening (full audit)
- **`header.php`** — `$_SERVER['REQUEST_URI']` now sanitized with `esc_url_raw( wp_unslash() )` before passing to `home_url()` (was unsanitized superglobal)
- **`inc/recipes.php`** — Added `wp_unslash()` before all `$_POST` reads in `dtf_save_recipe_meta()`; nonce verified with `wp_unslash()` applied first
- **`inc/encounters.php`** — Same `wp_unslash()` treatment on nonce and all `$_POST` fields in `dtf_save_encounter_meta()`; loop counter `$i` now wrapped in `esc_attr()` in star rating radio inputs
- **`functions.php`** — CSS injection hardened: custom max-width value validated by strict regex (`/^\d+(\.\d+)?(px|em|rem|vw|ch|%)$/`) before injecting into `<style>` block; `esc_attr()` in CSS context removed in favour of format validation
- **`template-parts/post-card.php`** — `the_title()` replaced with `echo esc_html( get_the_title() )` to prevent XSS via post title
- **`template-parts/sidebar.php`** — Same `esc_html( get_the_title() )` fix; `get_the_date()` and `dtf_reading_time()` output now escaped with `esc_html()`
- **`page-contact.php`** — Nonce verification now applies `wp_unslash()` first; all re-displayed `$_POST` values (name, email, subject, message) wrapped with `wp_unslash()` before `esc_attr()` / `esc_textarea()`

## [1.7.3] — 2026-04-03
- **Newsletter signup widget** (`inc/widget-newsletter.php`):
  - New `DTF_Newsletter_Widget` class — drag into any sidebar from Appearance → Widgets
  - Per-widget overrides for title, description, button label; falls back to admin defaults
  - Submits to any newsletter service (Mailchimp, MailerLite, Brevo, etc.) via a configurable form action URL
  - Hidden fields textarea for Mailchimp `u=` / `id=` parameters (one `name=value` per line)
  - Inline JS shows a success message after click without navigating away
  - Warns admins (only) if the form action URL is not yet set
- **`[dtf_newsletter]`** shortcode — embed the form in any post or page; accepts `title`, `desc`, `btn` attributes
- **Settings → Discover Tasty → Newsletter** tab: form action URL, email field name, hidden fields, default title/description/button/success message
- CSS: `.dtf-nl-*` rules matching existing sidebar card design (green top border, unified font sizes, dark-mode compatible)

## [1.7.1] — 2026-04-03
- Mobile wordmark: added `.wm-food` span (`display:none` on desktop, `display:inline` on ≤768 px) so the header reads "DISCOVER TASTY **FOOD**" as a complete standalone logo when category pills are hidden
- All three spans share the same font, weight and scaled size at every mobile breakpoint
- Mobile nav drawer brand also shows the full three-word mark

## [1.7.0] — 2026-04-03
### Responsive / Mobile
- **Mobile navigation drawer**: hamburger button appears on ≤768 px; tapping it slides down a full-width panel with all nav links, category pills, language switcher, RSS link and dark-mode toggle. Closes on overlay tap, close button, or Escape key. Body scroll is locked while open.
- **Hamburger animation**: three bars animate into an ✕ when the drawer is open.
- **Tablet wide (≤960 px)**: sidebar narrows to 220 px, header pills shrink slightly; layout stays 2-column.
- **Tablet portrait / mobile (≤768 px)**: topbar and desktop pills hidden; header collapses to `wordmark | hamburger`; hero image stacks below text; content+sidebar becomes single column; post cards go vertical; footer stacks copyright above links.
- **Small mobile (≤600 px)**: recipe stats wrap to 2-across, encounter meta goes single column, pagination tightens.
- **Mini phones (≤400 px)**: extra wordmark/padding compression.
- Dark-mode toggle in the mobile drawer is synced with the desktop toggle via shared `applyTheme()` helper.

## [1.6.7] — 2026-04-03
- Full-width layout mode added:
  - New **Layout** tab in admin options (Settings → Discover Tasty → Layout)
  - `layout_mode` option: `boxed` (default, centred at `--max-w`) or `full-width` (edge-to-edge)
  - `layout_max_width` option: override the default 960 px max-width for boxed mode (e.g. `1200px`)
  - `dtf-full-width` body class injected when full-width mode is active
  - CSS: `.dtf-full-width .site-wrap` expands to 100% width with 32 px side padding; collapses to 16 px on mobile
  - `dtf_layout_inline_css()` injects a `--max-w` override for custom boxed widths

## [1.6.6] — 2026-04-03
- Dark mode CTA colour changed from `#B39CD0` to `#6FB241`
- Sidebar list items: green dot restored via CSS `::before` pseudo-element (5px circle, `var(--basil)`)
- Recent Posts block: post title links forced to 12px matching other sidebar lists; post date styled as 10px muted text
- List item padding adjusted to `5px 0 5px 14px` to accommodate the absolute-positioned dot

## [1.6.5] — 2026-04-03
- Dark mode accent colours adjusted:
  - `--basil` (Food pill): changed to `#6FB241` (green)
  - `--tomato` (Encounters pill): swapped to `#F4A340` amber/orange
  - `--saffron` (Recipes pill): swapped to `#E8587A` rose/pink

## [1.6.4] — 2026-04-03
- Sidebar: extended widget title/list rules to also target `.sb-section` headings and Gutenberg block widget selectors (`.wp-block-heading`, `.wp-block-latest-posts`, `.wp-block-archives`, `.wp-block-categories`, `.wp-block-search`, `.wp-block-latest-comments`) for fully uniform design
- Dark mode pill colours updated — 3 distinct vivid colours:
  - `--basil` (Food pill): `#3DBDC4` vivid teal
  - `--tomato` (Encounters pill): `#E8587A` vivid rose
  - `--saffron` (Recipes pill): `#F4A340` warm amber (was identical to tomato)

## [1.6.3] — 2026-04-03
- `.site-wrap`: replaced hardcoded `background: #fff` with `background: var(--bg-card)` so dark mode slate background applies correctly across the full content area

## [1.6.2] — 2026-04-03
- Dark mode palette updated:
  - Background: `#2C2C2C` (slate gray) / cards `#383838` / deep `#242424`
  - Primary text: `#E4E4E4`, soft `#C0C0C0`, muted `#909090`
  - Accent 1 (basil/cyan): `#A8DADC` — borders, links, section accents
  - Accent 2 (tomato/pink): `#FFC1CC` — encounter tags, highlights
  - Button/CTA: `#B39CD0` (lavender) — Read More, Search submit, active filters
- `--cta` token introduced for dark mode button colour

## [1.6.1] — 2026-04-03
- Sidebar widget titles (Recent Posts, Recent Comments, Search, Archives, Categories): increased CSS specificity by targeting `h1`–`h4` inside `.widget` alongside `.widget-title`/`.widgettitle`, with `!important` overrides to match the "Latest Posts" small uppercase style

## [1.6.0] — 2026-04-03
- `.content-sidebar`: added `border-top: 1px solid var(--basil)`

## [1.5.9] — 2026-04-03
- `.featured-hero`: removed `border` and `border-top` (green accent and outline)

## [1.5.8] — 2026-04-03
- `.site-wrap`: margin restored to `0 auto` (was `-10px auto`)

## [1.5.7] — 2026-04-03
- Topbar border: switched to `2px solid rgba(0,0,0,.10)` so it remains visible above the white `.site-wrap` (removed box-shadow)
- Post cards: `gap: 16px` added to `.post-grid` for spacing between cards

## [1.5.6] — 2026-04-02
- `.site-wrap`: margin `-10px auto`, padding `0 10px`, background `#fff`
- `.content-sidebar`: added `margin: 0 0 -35px 0`

## [1.5.5] — 2026-04-02
- `.content-sidebar-col` top padding increased to `84px` (was `24px`)

## [1.5.4] — 2026-04-02
- Fix language switcher: use `pll_the_languages()` directly when Polylang is active (`hide_current: 0`) so both EN and RO always show on every page; falls back to nav menu for WPML or manual menus

## [1.5.3] — 2026-04-02
- Fix language switcher display: `.lang-switcher-nav` now renders as inline flex, no bullet, flag and text sit correctly in the topbar-right row

## [1.5.2] — 2026-04-02
### Multilingual support
- All user-facing strings in PHP templates wrapped with `__()` / `esc_html__()` / `esc_html_e()` using the `dtf` text domain
- `load_theme_textdomain()` already in place — `.po`/`.mo` files go in `/languages/`
- Registered `lang-switcher` nav menu location; Polylang/WPML will inject language links automatically
- `rtl.css` added for Right-to-Left language support (Arabic, Hebrew, Farsi, etc.)
- Theme description updated to note Polylang/WPML compatibility

## [1.5.1] — 2026-04-02
- Theme header: Author set to "Milestone & Claude", added `Requires at least: 6.0` and `Requires PHP: 7.4`

## [1.5.0] — 2026-04-02
- Unified sidebar widget title style: 11px bold uppercase, `var(--ink)` colour, 2px letter-spacing, bottom border
- Applied same title style to custom `.sb-label` (Latest Posts widget) for consistent look across all sidebar sections
- screenshot.png added (renamed from Screenshot.png for Linux server compatibility)
- CHANGELOG.md introduced

## [1.4.8] — 2026-04-02
- Fix page-shift on navigation: added `scrollbar-gutter: stable` to `html` so scrollbar track space is always reserved

## [1.4.7] — 2026-04-02
- Topbar redesigned: white background, subtle bottom border + shadow
- Topbar height increased to 48px
- Topbar nav font updated to match header pills (var(--font), 16px, weight 500, letter-spacing 4px, uppercase)
- Active/hover state changed to basil-coloured text on faint tint

## [1.4.6] — 2026-04-02
- Topbar nav constrained to site content width (inherits `.site-wrap` max-width) instead of spanning full viewport

## [1.4.5] — 2026-04-02
- Topbar nav items centred (reverted in 1.4.6)

## [1.4.4] — 2026-04-02
- Wordmark font-size clamp max reduced from 220px to 150px to prevent disproportionate scaling at wide viewports

## [1.4.3] — 2026-04-01
- Removed `overflow: hidden` from `.site-wordmark`
- Wordmark font-size set to `clamp(20px, 10.3vw, 220px)` on both `.wm-discover` and `.wm-tasty`
- `.header-pills` padding set to `28px 0 8px 0`
- Added `margin-top: 10px` to `.featured-hero`

## [1.4.2] — 2026-04-01
- Switched wordmark font to Barlow Condensed weight 100 (ultra-thin condensed, matching Apollo original)
- Updated Google Fonts import to include Barlow Condensed 100/200/300

## [1.4.1] — 2026-04-01
- Header layout converted to CSS Grid (`1fr 280px`) matching content-sidebar grid for pixel-perfect alignment
- Wrapped front-page, archive, and index content in `.site-wrap` for consistent padding context
- Pills column now aligns precisely with the sidebar below

## [1.4.0] — 2026-04-01
- Content area redesigned to match Apollo theme: beige page background (`#F5F1EA`), white cards
- Post list changed to horizontal cards (thumbnail left, text right) with green top accent border
- Sidebar widgets styled with top border and card background
- Featured hero added as full-width split card at the top of the front page

## [1.3.4] — prior session
- Topbar full-width fix: `.site-topbar .site-wrap` set to `max-width: 100%; margin: 0; padding: 0 28px`
- Active menu item highlight added to topbar nav
