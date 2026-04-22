# Discover Tasty Food — Project Memory

## Repository
- **GitHub:** https://github.com/marinnedea/discover-tasty-food-wp-theme
- **Owner:** marinnedea (personal account, not company)
- **Visibility:** Public
- **License:** CC BY-NC 4.0
- **Latest release:** v1.8.5

## Local paths
- **Theme folder:** `/Users/marin/Desktop/discovertastyfood/`
- **Installable zip:** `/Users/marin/Desktop/discovertastyfood.zip`

## Theme identity
- **Theme name:** Discover Tasty Food
- **Text domain:** `dtf`
- **Requires:** WordPress 6.0+, PHP 7.4+
- **Author credit:** Milestone & Claude

## Design decisions

### Wordmark
- Three spans: `.wm-discover` (dark ink), `.wm-tasty` (basil green), `.wm-food` (hidden desktop, shown mobile)
- Font: `var(--font-wordmark)`, weight 100, tight letter-spacing
- `.wm-discover`: `letter-spacing: -12px`, `margin-left: -15px`, `padding: 0 0 15px 0`
- `.wm-tasty`: `letter-spacing: -12px`, `margin-left: -5px`, `padding: 0 0 15px 0`
- **Do NOT use `margin-top: -15px` on wordmark spans** — causes the `<a>` to bleed into the topbar, breaking nav clicks
- `.site-topbar` has `position: relative; z-index: 101` (above sticky header at 100)

### Header pills
- 4 pills: Food (green), Encounters (orange), Recipes (pink), Guides (cobalt)
- Labels + URLs configurable in Settings → Discover Tasty → Navigation (pill_1–pill_4)
- `.header-pills` padding: `20px 0 8px 0`
- Pills hidden on mobile (≤768px); shown in hamburger drawer instead

### Mobile
- Hamburger drawer nav with Escape key + overlay close + body scroll lock
- `.wm-food` shown on mobile (`display: inline`) so wordmark reads "DISCOVER TASTY FOOD"
- Mobile wordmark: `font-weight: 300`, `letter-spacing: 0` (overrides desktop tight tracking)
- Breakpoints: 960px, 768px, 600px, 400px

### Layout
- Default: boxed (max-width 960px centred)
- Full-width toggle: adds `dtf-full-width` body class, configurable in admin → Layout tab
- CSS injection in layout settings protected by strict regex: `/^\d+(\.\d+)?(px|em|rem|vw|ch|%)$/`

### Dark mode
- CSS custom properties under `[data-theme="dark"]`
- Anti-FOUC script reads `localStorage` before first paint
- Options: Auto / Always light / Always dark (Settings → Technical)

### Email protection (contact page)
- Email address never written to HTML
- Base64-split across two `data-` attributes, assembled in browser JS on user click only

## Custom post types

### Food Encounters (`dtf_encounter`)
- Meta: dtf_enc_date, dtf_enc_location, dtf_enc_rating, dtf_enc_price, dtf_enc_go_back, dtf_enc_availability, dtf_enc_memorable, dtf_enc_best
- Archive slug configurable; flush permalinks after changing

### Recipes (`dtf_recipe`)
- Prep/cook times, nutrition facts, ingredients, step-by-step instructions
- Meta box on edit screen

## Key files

| File | Purpose |
|------|---------|
| `functions.php` | Theme setup, enqueues, helpers, CPT registration |
| `style.css` | All styles + theme header |
| `header.php` | Topbar, wordmark, pills, hamburger, mobile nav drawer |
| `footer.php` | Footer output |
| `front-page.php` | Homepage hero + latest posts grid |
| `archive.php` | Category/tag archives; shows term featured image |
| `single.php` | Single post template |
| `page.php` | Default page template |
| `page-about.php` | About page (slug-based auto-applied) |
| `page-contact.php` | Contact page with bot-protected email + POST-Redirect-GET form |
| `inc/admin-options.php` | All Settings → Discover Tasty tabs incl. Manual |
| `inc/encounters.php` | Food Encounter CPT + meta box |
| `inc/recipes.php` | Recipe CPT + meta box |
| `inc/term-images.php` | Category featured image support (term meta + WP Media Library) |
| `inc/widget-newsletter.php` | Newsletter widget + `[dtf_newsletter]` shortcode |
| `template-parts/post-card.php` | Horizontal post card |
| `template-parts/encounter-card.php` | Encounter card |
| `template-parts/sidebar.php` | Sidebar with widget area |
| `rtl.css` | RTL mirror styles |

## Public PHP helpers

| Function | Returns |
|----------|---------|
| `dtf_opt($key, $fallback)` | Any theme option |
| `dtf_reading_time($post_id)` | "N min read" string |
| `dtf_post_type_label($post)` | Food / Encounter / Recipe / Guide label |
| `dtf_enc_stars($rating)` | ★ string for 1–5 rating |
| `dtf_get_term_image($term, $size, $attr)` | `<img>` HTML for category featured image |
| `dtf_get_term_image_url($term, $size)` | URL of category featured image |
| `dtf_get_term_image_id($term)` | Attachment ID of category featured image |

## Security patterns
- All `$_POST` reads: `wp_unslash()` before `sanitize_*()`
- All nonces: `wp_verify_nonce(wp_unslash($_POST['...nonce']), '...')`
- CSS values in `<style>` tags: regex-validated, never `esc_attr()`
- Contact email: base64-split, never in HTML source
- Contact form: POST-Redirect-GET to prevent double-submit on refresh

## Multilingual
- Polylang: auto-detected, `pll_the_languages()` in topbar
- WPML fallback: `lang-switcher` nav menu location
- Translation files: `/languages/dtf-{locale}.po/.mo`

## Newsletter
- Widget: `DTF_Newsletter_Widget` (drag into Sidebar area)
- Shortcode: `[dtf_newsletter title="" desc="" btn=""]`
- Provider setup documented in admin Manual tab and README

## Git / GitHub workflow
- `gh` CLI binary at `/tmp/gh_cli/gh_2.67.0_macOS_arm64/bin/gh`
- Auth: `marinnedea` via keyring
- Push uses token auth: `https://marinnedea:{TOKEN}@github.com/...`
- To rebuild zip: `cd /Users/marin/Desktop && rm -f discovertastyfood.zip && zip -r discovertastyfood.zip discovertastyfood --exclude "*.DS_Store" --exclude "*__MACOSX*"`
- To create a new release: `gh release create vX.X.X /Users/marin/Desktop/discovertastyfood.zip --repo marinnedea/discover-tasty-food-wp-theme --title "..." --notes-file /tmp/release-notes.md`

## Version history
| Version | Notes |
|---------|-------|
| 1.8.5 | Category featured images, 4th Guides pill, newsletter widget + shortcode, about/contact pages, security hardening, mobile fixes, admin manual tab, full-width layout toggle |
