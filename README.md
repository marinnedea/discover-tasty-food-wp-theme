# Discover Tasty Food — WordPress Theme

![Discover Tasty Food theme screenshot](https://raw.githubusercontent.com/marinnedea/discover-tasty-food-wp-theme/main/screenshot.png)

A dish-first WordPress food blog theme with **Food Encounter** and **Recipe** custom post types. Editorial layout, dark mode, mobile-first responsive design, newsletter widget, category featured images, and a full built-in admin manual.

**License:** [CC BY-NC 4.0](https://creativecommons.org/licenses/by-nc/4.0/) — free for non-commercial use
**Requires:** WordPress 6.0+, PHP 7.4+
**Version:** 1.8.5

---

## Install

1. Download `discovertastyfood.zip` from [Releases](../../releases)
2. WordPress Admin → **Appearance → Themes → Add New → Upload Theme**
3. Upload the zip and click **Activate**

---

## 1 · Quick Start Checklist

| Step | Where |
|------|-------|
| 1. Set a tagline and footer copyright | Settings → Discover Tasty → Identity |
| 2. Add your social profile URLs | Settings → Discover Tasty → Social |
| 3. Choose a hero post (or leave blank for sticky post) | Settings → Discover Tasty → Homepage |
| 4. Customise the four header navigation pills (label + URL) | Settings → Discover Tasty → Navigation |
| 5. Set the Food Encounters archive slug; flush Permalinks after | Settings → Discover Tasty → Encounters + Settings → Permalinks |
| 6. Choose site layout: Boxed or Full Width | Settings → Discover Tasty → Layout |
| 7. Paste your newsletter service embed URL | Settings → Discover Tasty → Newsletter |
| 8. Add widgets to the sidebar | Appearance → Widgets |
| 9. Assign your primary menu | Appearance → Menus |
| 10. Set dark mode preference | Settings → Discover Tasty → Technical |

---

## 2 · Shortcodes

### `[dtf_newsletter]`

Embeds the newsletter signup form in any post, page, or text widget. All attributes are optional — omitting them uses the defaults set in the Newsletter tab.

```
[dtf_newsletter]
[dtf_newsletter title="Stay in the loop" desc="Weekly food writing, no spam." btn="Subscribe"]
```

| Attribute | Default | Description |
|-----------|---------|-------------|
| `title` | Sign Up for Updates | Widget heading |
| `desc` | Get the latest food writing… | Short description shown above the email field |
| `btn` | Sign Up | Submit button label |

---

## 3 · Sidebar Widgets

Go to **Appearance → Widgets** and drag widgets into the "Sidebar" area. All standard WordPress widgets (Search, Recent Posts, Archives, Categories, Recent Comments) are automatically styled to match the theme's sidebar card design — green top border, unified title size, green dot list items.

### DTF — Newsletter Signup

A custom widget that renders the newsletter form. Requires the form action URL to be configured first (Settings → Discover Tasty → Newsletter). You can override the title, description, and button label per widget instance.

> **Note:** If the form action URL is not set, the widget shows a warning only to logged-in admins and is invisible to regular visitors.

---

## 4 · Layout Options

### Boxed (default)

Content is centred at a maximum width (default 960 px). You can change this in the Layout tab — enter any CSS width value such as `1200px` or `1440px`.

### Full Width

Adds the body class `dtf-full-width`, which removes the max-width constraint and lets content span the full viewport with 32 px side padding (collapses to 16 px on mobile).

```css
/* Resulting CSS when Full Width is active */
.dtf-full-width .site-wrap {
  max-width: 100%;
  padding-left:  32px;
  padding-right: 32px;
}
```

---

## 5 · Dark Mode

Three options are available in **Settings → Discover Tasty → Technical**:

| Value | Behaviour |
|-------|-----------|
| `Auto` | Follows the visitor's OS preference (`prefers-color-scheme`). The moon/sun toggle in the topbar lets them override it per-session. |
| `Always light` | Forces light mode even on devices set to dark. Injects a small CSS block that overrides the OS media query. |
| `Always dark` | Forces dark mode site-wide regardless of OS setting. |

Dark mode uses a separate CSS token set under `[data-theme="dark"]` so all colours are controlled by CSS custom properties — no inline styles or JavaScript colour overrides.

---

## 6 · Multilingual Support

### Polylang (recommended, free)

Install the Polylang plugin. The theme detects it automatically and calls `pll_the_languages()` to render a language switcher in the topbar showing flags and language names for all configured languages, including the current one.

### WPML / manual fallback

If Polylang is not active, the theme falls back to a WordPress nav menu assigned to the "Language Switcher" location (Appearance → Menus). Create a menu with your language links and assign it there.

### Translation files

All user-facing strings use the text domain `dtf`. Place `.po` / `.mo` files in the `/languages/` folder inside the theme directory.

```
discovertastyfood/
└── languages/
    ├── dtf-ro_RO.po
    └── dtf-ro_RO.mo
```

Use Poedit or Loco Translate to generate the files. The `.po` source strings are in English.

---

## 7 · Newsletter Setup by Provider

### Mailchimp

In Mailchimp go to **Audience → Signup forms → Embedded forms**. Copy the action URL from the `<form>` tag and the `u=` and `id=` hidden field values.

```
Form action URL:
https://yourdomain.us1.list-manage.com/subscribe/post?u=XXXXXXXX&id=YYYYYYYY

Email field name:  EMAIL

Hidden fields (one per line):
u=XXXXXXXXXXXXXXXXXXXXXXXX
id=YYYYYYYY
```

> **Tip:** You can put the `u=` and `id=` values either in the action URL query string or as hidden fields — both work.

### MailerLite

```
Form action URL:
https://assets.mailerlite.com/jsonp/XXXXXX/forms/YYYYYY/subscribe

Email field name:  fields[email]
```

### Brevo (Sendinblue)

```
Form action URL:
https://sibforms.com/serve/MUIEEXXXXXXXXXX

Email field name:  EMAIL

Hidden fields:
locale=en
emailValidation=true
```

---

## 8 · Custom Post Types

### Food Encounters (`dtf_encounter`)

Represents a restaurant or food experience visit. Each encounter has:

| Meta field | Description |
|------------|-------------|
| `dtf_enc_date` | Visit date (YYYY-MM-DD) |
| `dtf_enc_location` | Restaurant / place name |
| `dtf_enc_rating` | Star rating (1–5) |
| `dtf_enc_price` | Price indicator |
| `dtf_enc_go_back` | Would go back? (yes/no) |
| `dtf_enc_availability` | Still available? (yes / no / seasonal / unknown) |
| `dtf_enc_memorable` | Memorable moment (free text) |
| `dtf_enc_best` | What made it special (free text) |

Archive slug is configurable in **Settings → Discover Tasty → Encounters**. Flush permalinks (Settings → Permalinks → Save) after changing it.

### Recipes (`dtf_recipe`)

A structured recipe post with prep/cook times, nutrition, ingredients and step-by-step instructions. Meta fields are entered via the Recipe Details meta box on the edit screen.

---

## 9 · Template Parts & PHP Helpers

| File / Function | Purpose |
|----------------|---------|
| `template-parts/post-card.php` | Horizontal post card used in the homepage grid and archive |
| `template-parts/encounter-card.php` | Card used in the Food Encounters archive |
| `template-parts/sidebar.php` | Right-hand sidebar with widget area |
| `dtf_opt( $key, $fallback )` | Read any theme option; returns `$fallback` if not set |
| `dtf_reading_time( $post_id )` | Returns localised "N min read" string based on word count |
| `dtf_post_type_label( $post )` | Returns the display label for a post's type (Food, Encounter, Recipe, Guide) |
| `dtf_enc_stars( $rating )` | Returns a ★ string for a 1–5 rating |
| `dtf_get_term_image( $term, $size, $attr )` | Returns `<img>` HTML for a category's featured image |
| `dtf_get_term_image_url( $term, $size )` | Returns the URL of a category's featured image |

---

## 10 · Responsive Breakpoints

| Breakpoint | Key changes |
|------------|-------------|
| `≤ 960px` | Sidebar narrows to 220 px; header pills tighten; layout stays 2-column |
| `≤ 768px` | Topbar and desktop pills hidden; hamburger appears; hero stacks; layout goes 1-column; post cards go vertical; footer stacks |
| `≤ 600px` | Recipe stats wrap 2-across; encounter meta goes 1-column |
| `≤ 400px` | Wordmark and padding compressed further for small phones |

---

## 11 · RTL (Right-to-Left) Support

The theme includes `rtl.css` which WordPress loads automatically when the active language is right-to-left (Arabic, Hebrew, etc.). It mirrors flex directions, text alignments, paddings and border positions.

---

## 12 · Google Analytics

Enter your Measurement ID (`G-XXXXXXXXXX`) in **Settings → Discover Tasty → Technical**. The theme injects the `gtag.js` snippet in `wp_head` at priority 99, after all other head scripts.

---

## License

[Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)](LICENSE)

Free to use, share, and adapt for non-commercial purposes with attribution.
For commercial licensing, contact the author.

---

*Discover Tasty Food — Theme by Milestone & Claude · v1.8.5*
