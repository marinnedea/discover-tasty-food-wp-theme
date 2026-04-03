<?php
defined( 'ABSPATH' ) || exit;

/* ── Helper ─────────────────────────────────────────────────────── */
function dtf_opt( $key, $fallback = '' ) {
    $opts = get_option( 'dtf_options', [] );
    return isset( $opts[ $key ] ) && $opts[ $key ] !== '' ? $opts[ $key ] : $fallback;
}

/* ── Register settings ──────────────────────────────────────────── */
function dtf_register_options() {
    register_setting( 'dtf_options_group', 'dtf_options', [ 'sanitize_callback' => 'dtf_sanitize_options' ] );
}
add_action( 'admin_init', 'dtf_register_options' );

function dtf_sanitize_options( $input ) {
    $clean = [];
    $text_keys = [
        'tagline','about_blurb','footer_copyright',
        'social_instagram','social_facebook','social_twitter','social_youtube','social_tiktok','rss_url',
        'hero_post_id','grid_posts','sidebar_items',
        'pill_1_label','pill_1_url','pill_2_label','pill_2_url',
        'pill_3_label','pill_3_url','pill_4_label','pill_4_url',
        'enc_rating_label','enc_price_label','enc_goback_label','enc_available_label',
        'enc_memorable_label','enc_best_label','enc_gone_text','enc_goback_options',
        'enc_price_options','enc_archive_slug','enc_posts_per_page',
        'ga_id','pwa_url',
        'layout_max_width',
        'nl_form_action','nl_email_field','nl_title','nl_desc','nl_btn','nl_success_msg',
    ];
    foreach ( $text_keys as $key ) {
        if ( isset( $input[ $key ] ) ) $clean[ $key ] = sanitize_text_field( $input[ $key ] );
    }
    if ( isset( $input['enc_show_disappeared'] ) ) $clean['enc_show_disappeared'] = '1';
    if ( isset( $input['dark_mode'] ) && in_array( $input['dark_mode'], [ 'auto', 'light', 'dark' ], true ) ) {
        $clean['dark_mode'] = $input['dark_mode'];
    }
    if ( isset( $input['layout_mode'] ) && in_array( $input['layout_mode'], [ 'boxed', 'full-width' ], true ) ) {
        $clean['layout_mode'] = $input['layout_mode'];
    }
    if ( isset( $input['nl_hidden_fields'] ) ) {
        $clean['nl_hidden_fields'] = sanitize_textarea_field( $input['nl_hidden_fields'] );
    }
    return $clean;
}

/* ── Admin menu ─────────────────────────────────────────────────── */
function dtf_admin_menu() {
    add_options_page(
        __( 'Discover Tasty Food', 'dtf' ),
        __( 'Discover Tasty', 'dtf' ),
        'manage_options',
        'dtf-options',
        'dtf_options_page'
    );
}
add_action( 'admin_menu', 'dtf_admin_menu' );

/* ── Options page ───────────────────────────────────────────────── */
function dtf_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $tab  = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'identity';
    $tabs = [
        'identity'   => 'Identity',
        'social'     => 'Social',
        'homepage'   => 'Homepage',
        'navigation' => 'Navigation',
        'encounters' => 'Encounters',
        'layout'     => 'Layout',
        'newsletter' => 'Newsletter',
        'technical'  => 'Technical',
        'manual'     => '📖 Manual',
    ];
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:20px;">🍽</span> <?php esc_html_e( 'Discover Tasty Food', 'dtf' ); ?>
        </h1>
        <nav class="nav-tab-wrapper" style="margin-bottom:0;">
            <?php foreach ( $tabs as $slug => $label ) :
                $url = add_query_arg( [ 'page' => 'dtf-options', 'tab' => $slug ], admin_url( 'options-general.php' ) ); ?>
                <a href="<?php echo esc_url( $url ); ?>" class="nav-tab <?php echo $tab === $slug ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div style="background:#fff;border:1px solid #ccd0d4;border-top:none;padding:24px 28px;max-width:<?php echo $tab === 'manual' ? '860px' : '780px'; ?>;">
        <?php if ( $tab === 'manual' ) : ?>
            <?php dtf_options_tab( $tab ); ?>
        <?php else : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'dtf_options_group' ); ?>
            <?php dtf_options_tab( $tab ); ?>
            <?php submit_button( __( 'Save settings', 'dtf' ) ); ?>
        </form>
        <?php endif; ?>
        </div>
    </div>
    <?php
}

function dtf_options_tab( $tab ) {
    $lbl = 'display:block;font-weight:600;margin-bottom:4px;';
    $inp = 'width:100%;max-width:480px;padding:7px 10px;border:1px solid #ccd0d4;border-radius:4px;font-size:13px;';
    $row = 'margin-bottom:18px;';
    $hint = 'color:#666;font-size:12px;margin-top:4px;';

    switch ( $tab ) {

        case 'identity':
            echo '<h2>' . esc_html__( 'Site Identity', 'dtf' ) . '</h2>';
            dtf_opt_text( 'tagline',          'Tagline',         'Honest food writing.', $lbl, $inp, $row, $hint );
            dtf_opt_textarea( 'about_blurb',  'About blurb',     'A short bio shown in the footer or about widget.', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'footer_copyright', 'Footer copyright','&copy; ' . date('Y') . ' Discover Tasty Food', $lbl, $inp, $row, $hint );
            break;

        case 'social':
            echo '<h2>' . esc_html__( 'Social Links', 'dtf' ) . '</h2>';
            foreach ( [
                'social_instagram' => 'Instagram URL',
                'social_facebook'  => 'Facebook URL',
                'social_twitter'   => 'Twitter / X URL',
                'social_youtube'   => 'YouTube URL',
                'social_tiktok'    => 'TikTok URL',
                'rss_url'          => 'RSS URL',
            ] as $key => $label ) {
                dtf_opt_text( $key, $label, '', $lbl, $inp, $row, $hint );
            }
            break;

        case 'homepage':
            echo '<h2>' . esc_html__( 'Homepage', 'dtf' ) . '</h2>';
            dtf_opt_text( 'hero_post_id', 'Hero post ID', '', $lbl, $inp, $row, $hint, 'Leave blank to use the sticky post, or enter a specific post ID.' );
            dtf_opt_text( 'grid_posts',   'Posts in grid',  '6', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'sidebar_items','Sidebar items per section', '4', $lbl, $inp, $row, $hint );
            break;

        case 'navigation':
            echo '<h2>' . esc_html__( 'Header Navigation Pills', 'dtf' ) . '</h2>';
            echo '<p style="color:#666;margin-bottom:20px;font-size:13px;">These four pills appear in the sticky header. Leave blank to use the defaults.</p>';
            for ( $i = 1; $i <= 4; $i++ ) {
                $colors = [ 1 => 'olive', 2 => 'rust', 3 => 'stone', 4 => 'teal' ];
                echo '<div style="display:flex;gap:16px;align-items:flex-end;margin-bottom:14px;padding:14px 16px;background:#f9f9f9;border-radius:4px;">';
                echo '<div style="width:12px;height:12px;border-radius:50%;background:var(--' . ( $i === 4 ? 'dteal' : $colors[$i] ) . ');margin-bottom:6px;flex-shrink:0;"></div>';
                dtf_opt_text( 'pill_' . $i . '_label', 'Pill ' . $i . ' label', '', $lbl, 'padding:7px 10px;border:1px solid #ccd0d4;border-radius:4px;font-size:13px;', 'margin-bottom:0;margin-right:8px;', $hint );
                dtf_opt_text( 'pill_' . $i . '_url',   'URL', '', $lbl, 'padding:7px 10px;border:1px solid #ccd0d4;border-radius:4px;font-size:13px;width:260px;', 'margin-bottom:0;', $hint );
                echo '</div>';
            }
            break;

        case 'encounters':
            echo '<h2>' . esc_html__( 'Food Encounters', 'dtf' ) . '</h2>';
            dtf_opt_text( 'enc_archive_slug',    'Archive slug',          'food-encounters', $lbl, $inp, $row, $hint, 'Save Permalinks after changing.' );
            dtf_opt_text( 'enc_posts_per_page',  'Encounters per page',   '9',  $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_rating_label',    'Rating label',          'Rating',    $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_price_label',     'Price label',           'Price',     $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_goback_label',    'Go back label',         'Would go back', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_available_label', 'Available label',       'Still available', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_memorable_label', 'Memorable label',       'Memorable moment', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_best_label',      'Best label',            'What made it special', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'enc_gone_text',       '"Disappeared" badge text', 'Disappeared', $lbl, $inp, $row, $hint );
            echo '<div style="' . $row . '">';
            echo '<label style="' . $lbl . '">';
            echo '<input type="checkbox" name="dtf_options[enc_show_disappeared]" value="1"' . checked( dtf_opt('enc_show_disappeared'), '1', false ) . '> ';
            echo esc_html__( 'Show "Disappeared" filter tab on encounter archive', 'dtf' );
            echo '</label></div>';
            break;

        case 'layout':
            echo '<h2>' . esc_html__( 'Layout', 'dtf' ) . '</h2>';
            echo '<div style="' . $row . '">';
            echo '<label style="' . $lbl . '">' . esc_html__( 'Site layout', 'dtf' ) . '</label>';
            echo '<select name="dtf_options[layout_mode]" style="' . $inp . '">';
            foreach ( [ 'boxed' => 'Boxed (default — centred, max-width)', 'full-width' => 'Full width (edge to edge)' ] as $val => $label ) {
                echo '<option value="' . esc_attr( $val ) . '"' . selected( dtf_opt( 'layout_mode', 'boxed' ), $val, false ) . '>' . esc_html( $label ) . '</option>';
            }
            echo '</select>';
            echo '<p style="' . $hint . '">' . esc_html__( 'Full width removes the centred container and lets content span the full viewport.', 'dtf' ) . '</p>';
            echo '</div>';
            dtf_opt_text( 'layout_max_width', 'Custom max-width (boxed mode)', '', $lbl, $inp, $row, $hint, 'Override the default 960 px. Examples: 1200px, 1440px. Leave blank for default.' );
            break;

        case 'newsletter':
            echo '<h2>' . esc_html__( 'Newsletter Signup', 'dtf' ) . '</h2>';
            echo '<p style="color:#666;margin-bottom:20px;font-size:13px;">' . esc_html__( 'Works with any service (Mailchimp, MailerLite, Brevo, etc.). Paste the form action URL from your provider\'s embed code. Then add the widget from Appearance → Widgets, or use the shortcode [dtf_newsletter] in any post or page.', 'dtf' ) . '</p>';

            dtf_opt_text( 'nl_form_action', 'Form action URL', '', $lbl, $inp, $row, $hint, 'Paste the full POST URL from your newsletter provider\'s embed code.' );
            dtf_opt_text( 'nl_email_field', 'Email field name', 'EMAIL', $lbl, $inp, $row, $hint, 'Mailchimp uses EMAIL. MailerLite uses fields[email]. Check your provider\'s embed code.' );

            echo '<div style="' . $row . '">';
            echo '<label for="dtf_opt_nl_hidden_fields" style="' . $lbl . '">' . esc_html__( 'Hidden fields', 'dtf' ) . '</label>';
            echo '<textarea id="dtf_opt_nl_hidden_fields" name="dtf_options[nl_hidden_fields]" rows="4" style="' . $inp . 'resize:vertical;font-family:monospace;">' . esc_textarea( dtf_opt('nl_hidden_fields') ) . '</textarea>';
            echo '<p style="' . $hint . '">' . esc_html__( 'One per line: name=value. Mailchimp needs u=XXXXXXXX and id=XXXXXXXX (copy from their embed code).', 'dtf' ) . '</p>';
            echo '</div>';

            echo '<hr style="margin:24px 0;border:none;border-top:1px solid #eee;">';
            echo '<h3 style="font-size:13px;margin-bottom:16px;">' . esc_html__( 'Default content (overridable per widget)', 'dtf' ) . '</h3>';
            dtf_opt_text( 'nl_title',       'Widget title',       'Sign Up for Updates',                             $lbl, $inp, $row, $hint );
            dtf_opt_text( 'nl_desc',        'Description text',   'Get the latest food writing straight to your inbox.', $lbl, $inp, $row, $hint );
            dtf_opt_text( 'nl_btn',         'Button label',       'Sign Up',                                         $lbl, $inp, $row, $hint );
            dtf_opt_text( 'nl_success_msg', 'Success message',    '✓ Thank you! Check your inbox.',                  $lbl, $inp, $row, $hint, 'Shown inline after the user clicks Sign Up.' );
            break;

        case 'manual':
            $h2  = 'font-size:15px;font-weight:600;color:#1d2327;margin:32px 0 8px;padding-bottom:6px;border-bottom:2px solid #6FB241;';
            $h3  = 'font-size:13px;font-weight:600;color:#1d2327;margin:20px 0 6px;';
            $p   = 'font-size:13px;color:#444;line-height:1.7;margin-bottom:10px;';
            $pre = 'background:#f0f0f1;border:1px solid #ddd;border-left:3px solid #6FB241;border-radius:3px;padding:10px 14px;font-size:12px;font-family:monospace;overflow-x:auto;margin:8px 0 14px;white-space:pre;';
            $td  = 'padding:7px 12px;font-size:12px;border-bottom:1px solid #eee;vertical-align:top;';
            $thl = 'padding:7px 12px;font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;background:#f6f7f7;color:#555;border-bottom:2px solid #ddd;text-align:left;';
            $note = 'background:#fff8e5;border-left:3px solid #F4A340;padding:10px 14px;font-size:12px;color:#555;margin:10px 0 16px;border-radius:0 3px 3px 0;';
            ?>
            <style>
            .dtf-manual a { color:#6FB241; }
            .dtf-manual table { width:100%; border-collapse:collapse; margin-bottom:18px; }
            .dtf-manual code { background:#f0f0f1; padding:1px 5px; border-radius:3px; font-size:12px; font-family:monospace; }
            </style>
            <div class="dtf-manual">
                <p style="font-size:13px;color:#666;margin-bottom:0;">
                    <?php esc_html_e( 'Theme version', 'dtf' ); ?>: <strong><?php echo esc_html( wp_get_theme()->get('Version') ); ?></strong>
                    &nbsp;·&nbsp; <?php esc_html_e( 'Text domain', 'dtf' ); ?>: <code>dtf</code>
                    &nbsp;·&nbsp; <a href="<?php echo esc_url( add_query_arg(['page'=>'dtf-options','tab'=>'technical'], admin_url('options-general.php')) ); ?>"><?php esc_html_e( 'Go to settings', 'dtf' ); ?></a>
                </p>

                <?php /* ── 1. QUICK START ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '1 · Quick Start Checklist', 'dtf' ); ?></h2>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Step', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Where', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $steps = [
                        [ '1. Set a tagline and footer copyright', 'Settings → Discover Tasty → Identity' ],
                        [ '2. Add your social profile URLs', 'Settings → Discover Tasty → Social' ],
                        [ '3. Choose a hero post (or leave blank for sticky post)', 'Settings → Discover Tasty → Homepage' ],
                        [ '4. Customise the four header navigation pills (label + URL)', 'Settings → Discover Tasty → Navigation' ],
                        [ '5. Set the Food Encounters archive slug; flush Permalinks after', 'Settings → Discover Tasty → Encounters + Settings → Permalinks' ],
                        [ '6. Choose site layout: Boxed or Full Width', 'Settings → Discover Tasty → Layout' ],
                        [ '7. Paste your newsletter service embed URL', 'Settings → Discover Tasty → Newsletter' ],
                        [ '8. Add widgets to the sidebar', 'Appearance → Widgets' ],
                        [ '9. Assign your primary menu', 'Appearance → Menus' ],
                        [ '10. Set dark mode preference', 'Settings → Discover Tasty → Technical' ],
                    ];
                    foreach ( $steps as $s ) :
                    ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><?php echo esc_html( $s[0] ); ?></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo esc_html( $s[1] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php /* ── 2. SHORTCODES ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '2 · Shortcodes', 'dtf' ); ?></h2>

                <h3 style="<?php echo $h3; ?>">[dtf_newsletter]</h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Embeds the newsletter signup form in any post, page, or text widget. All attributes are optional — omitting them uses the defaults set in the Newsletter tab.', 'dtf' ); ?></p>
                <pre style="<?php echo $pre; ?>">[dtf_newsletter]
[dtf_newsletter title="Stay in the loop" desc="Weekly food writing, no spam." btn="Subscribe"]</pre>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Attribute', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Default', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Description', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $sc_attrs = [
                        [ 'title', esc_html__( 'Sign Up for Updates', 'dtf' ), esc_html__( 'Widget heading', 'dtf' ) ],
                        [ 'desc',  esc_html__( 'Get the latest food writing…', 'dtf' ), esc_html__( 'Short description shown above the email field', 'dtf' ) ],
                        [ 'btn',   esc_html__( 'Sign Up', 'dtf' ), esc_html__( 'Submit button label', 'dtf' ) ],
                    ];
                    foreach ( $sc_attrs as $a ) :
                    ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><code><?php echo esc_html($a[0]); ?></code></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo $a[1]; ?></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo $a[2]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php /* ── 3. WIDGETS ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '3 · Sidebar Widgets', 'dtf' ); ?></h2>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Go to Appearance → Widgets and drag widgets into the "Sidebar" area. All standard WordPress widgets (Search, Recent Posts, Archives, Categories, Recent Comments) are automatically styled to match the theme\'s sidebar card design — green top border, unified title size, green dot list items.', 'dtf' ); ?></p>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'DTF — Newsletter Signup', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'A custom widget that renders the newsletter form. Requires the form action URL to be configured first (Settings → Discover Tasty → Newsletter). You can override the title, description, and button label per widget instance.', 'dtf' ); ?></p>
                <div style="<?php echo $note; ?>"><?php esc_html_e( 'If the form action URL is not set, the widget shows a warning only to logged-in admins and is invisible to regular visitors.', 'dtf' ); ?></div>

                <?php /* ── 4. LAYOUT ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '4 · Layout Options', 'dtf' ); ?></h2>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Boxed (default)', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Content is centred at a maximum width (default 960 px). You can change this in the Layout tab — enter any CSS width value such as 1200px or 1440px.', 'dtf' ); ?></p>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Full Width', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Adds the body class dtf-full-width, which removes the max-width constraint and lets content span the full viewport with 32 px side padding (collapses to 16 px on mobile).', 'dtf' ); ?></p>
                <pre style="<?php echo $pre; ?>"><?php echo esc_html( "/* Resulting CSS when Full Width is active */\n.dtf-full-width .site-wrap {\n  max-width: 100%;\n  padding-left:  32px;\n  padding-right: 32px;\n}" ); ?></pre>

                <?php /* ── 5. DARK MODE ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '5 · Dark Mode', 'dtf' ); ?></h2>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Three options are available in Settings → Discover Tasty → Technical:', 'dtf' ); ?></p>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Value', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Behaviour', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <tr><td style="<?php echo $td; ?>"><code>Auto</code></td><td style="<?php echo $td; ?>color:#666;"><?php esc_html_e( 'Follows the visitor\'s OS preference (prefers-color-scheme). The moon/sun toggle in the topbar lets them override it per-session.', 'dtf' ); ?></td></tr>
                    <tr><td style="<?php echo $td; ?>"><code>Always light</code></td><td style="<?php echo $td; ?>color:#666;"><?php esc_html_e( 'Forces light mode even on devices set to dark. Injects a small CSS block that overrides the OS media query.', 'dtf' ); ?></td></tr>
                    <tr><td style="<?php echo $td; ?>"><code>Always dark</code></td><td style="<?php echo $td; ?>color:#666;"><?php esc_html_e( 'Forces dark mode site-wide regardless of OS setting.', 'dtf' ); ?></td></tr>
                    </tbody>
                </table>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Dark mode uses a separate CSS token set under [data-theme="dark"] so all colours are controlled by CSS custom properties — no inline styles or JavaScript colour overrides.', 'dtf' ); ?></p>

                <?php /* ── 6. MULTILINGUAL ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '6 · Multilingual Support', 'dtf' ); ?></h2>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Polylang (recommended, free)', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Install the Polylang plugin. The theme detects it automatically and calls pll_the_languages() to render a language switcher in the topbar showing flags and language names for all configured languages, including the current one.', 'dtf' ); ?></p>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'WPML / manual fallback', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'If Polylang is not active, the theme falls back to a WordPress nav menu assigned to the "Language Switcher" location (Appearance → Menus). Create a menu with your language links and assign it there.', 'dtf' ); ?></p>
                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Translation files', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'All user-facing strings use the text domain dtf. Place .po / .mo files in the /languages/ folder inside the theme directory.', 'dtf' ); ?></p>
                <pre style="<?php echo $pre; ?>"><?php echo esc_html( "discovertastyfood/\n└── languages/\n    ├── dtf-ro_RO.po\n    └── dtf-ro_RO.mo" ); ?></pre>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Use Poedit or Loco Translate to generate the files. The .po source strings are in English.', 'dtf' ); ?></p>

                <?php /* ── 7. NEWSLETTER ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '7 · Newsletter Setup by Provider', 'dtf' ); ?></h2>
                <h3 style="<?php echo $h3; ?>">Mailchimp</h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'In Mailchimp go to Audience → Signup forms → Embedded forms. Copy the action URL from the <form> tag and the u= and id= hidden field values.', 'dtf' ); ?></p>
                <pre style="<?php echo $pre; ?>"><?php echo esc_html(
                    "Form action URL:\nhttps://yourdomain.us1.list-manage.com/subscribe/post?u=XXXXXXXX&id=YYYYYYYY\n\nEmail field name:  EMAIL\n\nHidden fields (one per line):\nu=XXXXXXXXXXXXXXXXXXXXXXXX\nid=YYYYYYYY"
                ); ?></pre>
                <div style="<?php echo $note; ?>"><?php esc_html_e( 'Tip: you can put the u= and id= values either in the action URL query string or as hidden fields — both work.', 'dtf' ); ?></div>

                <h3 style="<?php echo $h3; ?>">MailerLite</h3>
                <pre style="<?php echo $pre; ?>"><?php echo esc_html(
                    "Form action URL:\nhttps://assets.mailerlite.com/jsonp/XXXXXX/forms/YYYYYY/subscribe\n\nEmail field name:  fields[email]"
                ); ?></pre>

                <h3 style="<?php echo $h3; ?>">Brevo (Sendinblue)</h3>
                <pre style="<?php echo $pre; ?>"><?php echo esc_html(
                    "Form action URL:\nhttps://sibforms.com/serve/MUIEEXXXXXXXXXX\n\nEmail field name:  EMAIL\n\nHidden fields:\nlocale=en\nemailValidation=true"
                ); ?></pre>

                <?php /* ── 8. CUSTOM POST TYPES ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '8 · Custom Post Types', 'dtf' ); ?></h2>

                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Food Encounters (dtf_encounter)', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Represents a restaurant or food experience visit. Each encounter has:', 'dtf' ); ?></p>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Meta field', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Description', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $enc_fields = [
                        [ 'dtf_enc_date',        esc_html__( 'Visit date (YYYY-MM-DD)', 'dtf' ) ],
                        [ 'dtf_enc_location',     esc_html__( 'Restaurant / place name', 'dtf' ) ],
                        [ 'dtf_enc_rating',       esc_html__( 'Star rating (1–5)', 'dtf' ) ],
                        [ 'dtf_enc_price',        esc_html__( 'Price indicator', 'dtf' ) ],
                        [ 'dtf_enc_go_back',      esc_html__( 'Would go back? (yes/no)', 'dtf' ) ],
                        [ 'dtf_enc_availability', esc_html__( 'Still available? (yes / no / seasonal / unknown)', 'dtf' ) ],
                        [ 'dtf_enc_memorable',    esc_html__( 'Memorable moment (free text)', 'dtf' ) ],
                        [ 'dtf_enc_best',         esc_html__( 'What made it special (free text)', 'dtf' ) ],
                    ];
                    foreach ( $enc_fields as $f ) :
                    ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><code><?php echo esc_html($f[0]); ?></code></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo $f[1]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Archive slug is configurable in Settings → Discover Tasty → Encounters. Flush permalinks (Settings → Permalinks → Save) after changing it.', 'dtf' ); ?></p>

                <h3 style="<?php echo $h3; ?>"><?php esc_html_e( 'Recipes (dtf_recipe)', 'dtf' ); ?></h3>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'A structured recipe post with prep/cook times, nutrition, ingredients and step-by-step instructions. Meta fields are entered via the Recipe Details meta box on the edit screen.', 'dtf' ); ?></p>

                <?php /* ── 9. TEMPLATE PARTS ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '9 · Template Parts & PHP Helpers', 'dtf' ); ?></h2>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'File / Function', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Purpose', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $helpers = [
                        [ 'template-parts/post-card.php',      esc_html__( 'Horizontal post card used in the homepage grid and archive', 'dtf' ) ],
                        [ 'template-parts/encounter-card.php', esc_html__( 'Card used in the Food Encounters archive', 'dtf' ) ],
                        [ 'template-parts/sidebar.php',        esc_html__( 'Right-hand sidebar with widget area', 'dtf' ) ],
                        [ 'dtf_opt( $key, $fallback )',         esc_html__( 'Read any theme option; returns $fallback if not set', 'dtf' ) ],
                        [ 'dtf_reading_time( $post_id )',       esc_html__( 'Returns localised "N min read" string based on word count', 'dtf' ) ],
                        [ 'dtf_post_type_label( $post )',       esc_html__( 'Returns the display label for a post\'s type (Food, Encounter, Recipe, Guide)', 'dtf' ) ],
                        [ 'dtf_enc_stars( $rating )',           esc_html__( 'Returns a ★ string for a 1–5 rating', 'dtf' ) ],
                    ];
                    foreach ( $helpers as $h ) :
                    ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><code><?php echo esc_html($h[0]); ?></code></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo $h[1]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php /* ── 10. RESPONSIVE ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '10 · Responsive Breakpoints', 'dtf' ); ?></h2>
                <table>
                    <thead><tr>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Breakpoint', 'dtf' ); ?></th>
                        <th style="<?php echo $thl; ?>"><?php esc_html_e( 'Key changes', 'dtf' ); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $bps = [
                        [ '≤ 960 px',  esc_html__( 'Sidebar narrows to 220 px; header pills tighten; layout stays 2-column', 'dtf' ) ],
                        [ '≤ 768 px',  esc_html__( 'Topbar and desktop pills hidden; hamburger appears; hero stacks; layout goes 1-column; post cards go vertical; footer stacks', 'dtf' ) ],
                        [ '≤ 600 px',  esc_html__( 'Recipe stats wrap 2-across; encounter meta goes 1-column', 'dtf' ) ],
                        [ '≤ 400 px',  esc_html__( 'Wordmark and padding compressed further for small phones', 'dtf' ) ],
                    ];
                    foreach ( $bps as $b ) :
                    ?>
                    <tr>
                        <td style="<?php echo $td; ?>font-family:monospace;"><?php echo esc_html($b[0]); ?></td>
                        <td style="<?php echo $td; ?>color:#666;"><?php echo $b[1]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php /* ── 11. RTL ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '11 · RTL (Right-to-Left) Support', 'dtf' ); ?></h2>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'The theme includes rtl.css which WordPress loads automatically when the active language is right-to-left (Arabic, Hebrew, etc.). It mirrors flex directions, text alignments, paddings and border positions.', 'dtf' ); ?></p>

                <?php /* ── 12. GOOGLE ANALYTICS ── */ ?>
                <h2 style="<?php echo $h2; ?>"><?php esc_html_e( '12 · Google Analytics', 'dtf' ); ?></h2>
                <p style="<?php echo $p; ?>"><?php esc_html_e( 'Enter your Measurement ID (G-XXXXXXXXXX) in Settings → Discover Tasty → Technical. The theme injects the gtag.js snippet in wp_head at priority 99, after all other head scripts.', 'dtf' ); ?></p>

                <p style="margin-top:32px;font-size:11px;color:#aaa;">
                    Discover Tasty Food &mdash; <?php esc_html_e( 'Theme by Milestone &amp; Claude', 'dtf' ); ?>
                    &nbsp;·&nbsp; v<?php echo esc_html( wp_get_theme()->get('Version') ); ?>
                </p>
            </div>
            <?php
            break;

        case 'technical':
            echo '<h2>' . esc_html__( 'Technical', 'dtf' ) . '</h2>';
            dtf_opt_text( 'ga_id',  'Google Analytics ID', '', $lbl, $inp, $row, $hint, 'e.g. G-XXXXXXXXXX' );
            dtf_opt_text( 'pwa_url','PWA / Field Notes URL', '', $lbl, $inp, $row, $hint, 'Full URL to your PWA app (e.g. https://notes.yourdomain.com)' );
            echo '<div style="' . $row . '">';
            echo '<label style="' . $lbl . '">' . esc_html__( 'Dark mode', 'dtf' ) . '</label>';
            echo '<select name="dtf_options[dark_mode]" style="' . $inp . '">';
            foreach ( [ 'auto' => 'Auto (follows OS)', 'light' => 'Always light', 'dark' => 'Always dark' ] as $val => $label ) {
                echo '<option value="' . esc_attr( $val ) . '"' . selected( dtf_opt('dark_mode','auto'), $val, false ) . '>' . esc_html( $label ) . '</option>';
            }
            echo '</select></div>';
            break;
    }
}

/* ── Field helpers ──────────────────────────────────────────────── */
function dtf_opt_text( $key, $label, $placeholder, $lbl, $inp, $row, $hint, $note = '' ) {
    $val = dtf_opt( $key );
    echo '<div style="' . $row . '">';
    echo '<label for="dtf_opt_' . esc_attr( $key ) . '" style="' . $lbl . '">' . esc_html( $label ) . '</label>';
    echo '<input type="text" id="dtf_opt_' . esc_attr( $key ) . '" name="dtf_options[' . esc_attr( $key ) . ']" value="' . esc_attr( $val ) . '" placeholder="' . esc_attr( $placeholder ) . '" style="' . $inp . '">';
    if ( $note ) echo '<p style="' . $hint . '">' . esc_html( $note ) . '</p>';
    echo '</div>';
}

function dtf_opt_textarea( $key, $label, $note, $lbl, $inp, $row, $hint ) {
    $val = dtf_opt( $key );
    echo '<div style="' . $row . '">';
    echo '<label for="dtf_opt_' . esc_attr( $key ) . '" style="' . $lbl . '">' . esc_html( $label ) . '</label>';
    echo '<textarea id="dtf_opt_' . esc_attr( $key ) . '" name="dtf_options[' . esc_attr( $key ) . ']" rows="3" style="' . $inp . 'resize:vertical;">' . esc_textarea( $val ) . '</textarea>';
    if ( $note ) echo '<p style="' . $hint . '">' . esc_html( $note ) . '</p>';
    echo '</div>';
}

/* ── Inject GA ──────────────────────────────────────────────────── */
function dtf_inject_ga() {
    $ga = dtf_opt( 'ga_id' );
    if ( ! $ga || is_admin() ) return;
    echo "<!-- Google Analytics -->\n";
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga ) . '"></script>' . "\n";
    echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga ) . '");</script>' . "\n";
}
add_action( 'wp_head', 'dtf_inject_ga', 99 );

/* ── Inject dark mode override ──────────────────────────────────── */
function dtf_inject_dark_mode_css() {
    $mode = dtf_opt( 'dark_mode', 'auto' );
    if ( $mode === 'auto' ) return;
    if ( $mode === 'light' ) {
        echo '<style>@media(prefers-color-scheme:dark){:root{--bg:#F5F0E8;--bg-card:#FDFAF4;--bg-warm:#EDE8D8;--ink:#1E1B16;--ink-soft:#3d3a34;--muted:#8a8478;--border:rgba(44,40,32,.09);--border-mid:rgba(44,40,32,.16);}}</style>' . "\n";
    } elseif ( $mode === 'dark' ) {
        echo '<style>:root{--bg:#191714;--bg-card:#221f1b;--bg-warm:#131210;--ink:#F2EBD8;--ink-soft:#C8BFA8;--muted:#756e62;--border:rgba(240,234,216,.07);--border-mid:rgba(240,234,216,.13);}</style>' . "\n";
    }
}
add_action( 'wp_head', 'dtf_inject_dark_mode_css', 5 );

/* ── PWA link in admin toolbar ──────────────────────────────────── */
function dtf_admin_bar_pwa( $wp_admin_bar ) {
    $pwa = dtf_opt( 'pwa_url' );
    if ( ! $pwa ) return;
    $wp_admin_bar->add_node( [
        'id'    => 'dtf-pwa',
        'title' => '📱 Field Notes',
        'href'  => esc_url( $pwa ),
        'meta'  => [ 'target' => '_blank' ],
    ] );
}
add_action( 'admin_bar_menu', 'dtf_admin_bar_pwa', 100 );
