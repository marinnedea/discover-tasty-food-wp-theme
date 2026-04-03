<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php /* Anti-FOUC: set data-theme before first paint */ ?>
    <script>
    (function(){
        try {
            var s = localStorage.getItem('dtf-theme');
            var d = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-theme', s || (d ? 'dark' : 'light'));
        } catch(e){}
    })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page">

<div class="site-topbar">
    <div class="site-wrap">
        <nav class="topbar-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'dtf' ); ?>">
            <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>function(){
                $links=[
                    home_url('/')                => __( 'Home',       'dtf' ),
                    home_url('/food/')            => __( 'Food',       'dtf' ),
                    home_url('/food-encounters/') => __( 'Encounters', 'dtf' ),
                    home_url('/recipes/')         => __( 'Recipes',    'dtf' ),
                    home_url('/guides/')          => __( 'Guides',     'dtf' ),
                    home_url('/about/')           => __( 'About',      'dtf' ),
                ];
                echo '<ul>';
                foreach($links as $url=>$label) echo '<li><a href="'.esc_url($url).'">'.esc_html($label).'</a></li>';
                echo '</ul>';
            }]); ?>
        </nav>
        <div class="topbar-right">
            <?php
            if ( function_exists( 'pll_the_languages' ) ) :
                // Polylang: show all languages including current, hide_current=0
                echo '<ul class="lang-switcher-nav">';
                pll_the_languages( [
                    'show_flags'   => 1,
                    'show_names'   => 1,
                    'hide_current' => 0,
                    'hide_if_no_translation' => 0,
                ] );
                echo '</ul>';
            elseif ( has_nav_menu( 'lang-switcher' ) ) :
                // WPML or manual menu fallback
                wp_nav_menu( [
                    'theme_location' => 'lang-switcher',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => false,
                    'menu_class'     => 'lang-switcher-nav',
                ] );
            endif; ?>
            <a href="<?php bloginfo('rss2_url'); ?>" aria-label="<?php esc_attr_e( 'RSS feed', 'dtf' ); ?>">RSS</a>
            <button class="theme-toggle" id="dtf-theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'dtf' ); ?>">
                <span class="icon-sun" aria-hidden="true">&#9728;</span>
                <span class="icon-moon" aria-hidden="true">&#9790;</span>
            </button>
        </div>
    </div>
</div>

<header class="site-header" role="banner">
    <div class="site-wrap">
        <div class="site-wordmark">
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr( get_bloginfo('name') ); ?>">
                <span class="wm-discover">Discover</span> <span class="wm-tasty">Tasty</span><span class="wm-food">Food</span>
            </a>
        </div>
        <nav class="header-pills" aria-label="<?php esc_attr_e( 'Content categories', 'dtf' ); ?>">
            <?php
            $pills = [
                ['pill-food',       dtf_opt('pill_1_url', home_url('/food/')),            dtf_opt('pill_1_label', __( 'Food',       'dtf' ))],
                ['pill-encounters', dtf_opt('pill_2_url', home_url('/food-encounters/')), dtf_opt('pill_2_label', __( 'Encounters', 'dtf' ))],
                ['pill-recipes',    dtf_opt('pill_3_url', home_url('/recipes/')),         dtf_opt('pill_3_label', __( 'Recipes',    'dtf' ))],
                ['pill-guides',     dtf_opt('pill_4_url', home_url('/guides/')),          dtf_opt('pill_4_label', __( 'Guides',     'dtf' ))],
            ];
            foreach($pills as [$cls,$url,$label]):?>
            <a class="header-pill <?php echo esc_attr($cls); ?>" href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a>
            <?php endforeach;?>
        </nav>
        <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'dtf' ); ?>" aria-expanded="false" aria-controls="dtf-mobile-nav">
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>
    </div>
    <div class="header-stripe"><div class="hs-1"></div><div class="hs-2"></div><div class="hs-3"></div></div>
</header>

<nav id="dtf-mobile-nav" class="mobile-nav" aria-hidden="true" aria-label="<?php esc_attr_e( 'Mobile navigation', 'dtf' ); ?>">
    <div class="mobile-nav-inner">
        <div class="mobile-nav-header">
            <span class="mobile-nav-brand">
                <span class="wm-discover" style="font-size:clamp(22px,8vw,36px);letter-spacing:-4px;">Discover</span>
                <span class="wm-tasty"    style="font-size:clamp(22px,8vw,36px);letter-spacing:-4px;">Tasty</span>
                <span class="wm-food"     style="font-size:clamp(22px,8vw,36px);letter-spacing:-4px;display:inline;">Food</span>
            </span>
            <button class="mobile-nav-close" aria-label="<?php esc_attr_e( 'Close menu', 'dtf' ); ?>">&#x2715;</button>
        </div>
        <ul class="mobile-nav-links">
            <?php
            $nav_links = [
                home_url('/')                => __( 'Home',       'dtf' ),
                home_url('/food/')            => __( 'Food',       'dtf' ),
                home_url('/food-encounters/') => __( 'Encounters', 'dtf' ),
                home_url('/recipes/')         => __( 'Recipes',    'dtf' ),
                home_url('/guides/')          => __( 'Guides',     'dtf' ),
                home_url('/about/')           => __( 'About',      'dtf' ),
            ];
            foreach ( $nav_links as $url => $label ) :
                $is_current = trailingslashit( home_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) === trailingslashit( $url );
            ?>
            <li<?php echo $is_current ? ' class="current"' : ''; ?>>
                <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="mobile-nav-pills">
            <?php foreach ( $pills as [ $cls, $url, $label ] ) : ?>
            <a class="header-pill <?php echo esc_attr( $cls ); ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </div>
        <div class="mobile-nav-utils">
            <?php if ( function_exists( 'pll_the_languages' ) ) :
                echo '<ul class="lang-switcher-nav">';
                pll_the_languages( [ 'show_flags' => 1, 'show_names' => 1, 'hide_current' => 0, 'hide_if_no_translation' => 0 ] );
                echo '</ul>';
            endif; ?>
            <a href="<?php bloginfo( 'rss2_url' ); ?>" aria-label="<?php esc_attr_e( 'RSS feed', 'dtf' ); ?>">RSS</a>
            <button class="theme-toggle" id="dtf-theme-toggle-mobile" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'dtf' ); ?>">
                <span class="icon-sun" aria-hidden="true">&#9728;</span>
                <span class="icon-moon" aria-hidden="true">&#9790;</span>
            </button>
        </div>
    </div>
    <button class="mobile-nav-overlay" aria-label="<?php esc_attr_e( 'Close menu', 'dtf' ); ?>"></button>
</nav>

<script>
(function(){
    /* ── Dark mode toggle ── */
    function applyTheme(next) {
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('dtf-theme', next); } catch(e){}
    }
    function bindToggle(id) {
        var btn = document.getElementById(id);
        if (!btn) return;
        btn.addEventListener('click', function(){
            var cur = document.documentElement.getAttribute('data-theme') || 'light';
            applyTheme(cur === 'dark' ? 'light' : 'dark');
        });
    }
    bindToggle('dtf-theme-toggle');
    bindToggle('dtf-theme-toggle-mobile');

    /* ── Mobile nav ── */
    var toggleBtn = document.querySelector('.mobile-menu-toggle');
    var mobileNav = document.getElementById('dtf-mobile-nav');
    if (!toggleBtn || !mobileNav) return;
    var overlay   = mobileNav.querySelector('.mobile-nav-overlay');
    var closeBtn  = mobileNav.querySelector('.mobile-nav-close');

    function openNav() {
        mobileNav.classList.add('is-open');
        mobileNav.setAttribute('aria-hidden', 'false');
        toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.classList.add('mobile-nav-open');
    }
    function closeNav() {
        mobileNav.classList.remove('is-open');
        mobileNav.setAttribute('aria-hidden', 'true');
        toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('mobile-nav-open');
    }

    toggleBtn.addEventListener('click', function(){
        mobileNav.classList.contains('is-open') ? closeNav() : openNav();
    });
    if (overlay)  overlay.addEventListener('click', closeNav);
    if (closeBtn) closeBtn.addEventListener('click', closeNav);

    /* Close on Escape key */
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && mobileNav.classList.contains('is-open')) closeNav();
    });
})();
</script>

<div id="content">
