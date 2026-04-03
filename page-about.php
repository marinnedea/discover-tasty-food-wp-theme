<?php
/**
 * Template for the About page.
 * Loaded automatically when a page has the slug "about".
 */
get_header();

$tagline   = dtf_opt( 'tagline',     __( 'Honest food writing.', 'dtf' ) );
$blurb     = dtf_opt( 'about_blurb', '' );

$socials = array_filter( [
    'Instagram' => dtf_opt('social_instagram'),
    'Facebook'  => dtf_opt('social_facebook'),
    'Twitter'   => dtf_opt('social_twitter'),
    'YouTube'   => dtf_opt('social_youtube'),
    'TikTok'    => dtf_opt('social_tiktok'),
    'RSS'       => dtf_opt('rss_url') ?: get_bloginfo('rss2_url'),
] );

$pills = [
    [ 'pill-food',       dtf_opt('pill_1_url', home_url('/food/')),            dtf_opt('pill_1_label', __('Food','dtf')),       __('Our day-to-day food writing — what we ate, where we found it, what it meant.','dtf') ],
    [ 'pill-encounters', dtf_opt('pill_2_url', home_url('/food-encounters/')), dtf_opt('pill_2_label', __('Encounters','dtf')), __('Documented restaurant visits with ratings, availability notes, and honest reflections.','dtf') ],
    [ 'pill-recipes',    dtf_opt('pill_3_url', home_url('/recipes/')),         dtf_opt('pill_3_label', __('Recipes','dtf')),    __('Recipes tested in a real kitchen — with nutrition facts and step-by-step instructions.','dtf') ],
    [ 'pill-guides',     dtf_opt('pill_4_url', home_url('/guides/')),          dtf_opt('pill_4_label', __('Guides','dtf')),     __('Longer reads on diet, nutrition science, and navigating food choices.','dtf') ],
];
?>

<div id="about-page">
<div class="site-wrap">

    <?php /* ── Page header ── */ ?>
    <header class="about-page-hdr">
        <p class="single-eyebrow"><?php esc_html_e( 'About', 'dtf' ); ?></p>
        <?php while ( have_posts() ) : the_post(); ?>
        <h1 class="single-title"><?php echo esc_html( get_the_title() ); ?></h1>
        <?php endwhile; ?>
        <p class="about-tagline"><?php echo esc_html( $tagline ); ?></p>
    </header>

    <?php /* ── Featured image ── */ ?>
    <?php rewind_posts(); while ( have_posts() ) : the_post(); ?>
    <?php if ( has_post_thumbnail() ) : ?>
    <div class="about-featured-img">
        <?php the_post_thumbnail( 'dtf-hero', [ 'alt' => esc_attr( get_the_title() ) ] ); ?>
    </div>
    <?php endif; ?>
    <?php endwhile; ?>

    <?php /* ── Two-column body ── */ ?>
    <div class="about-body">

        <main class="about-main">

            <?php if ( $blurb ) : ?>
            <div class="about-blurb">
                <?php echo wp_kses_post( wpautop( $blurb ) ); ?>
            </div>
            <?php endif; ?>

            <?php /* Any content written in the page editor appears here */ ?>
            <?php rewind_posts(); while ( have_posts() ) : the_post(); ?>
            <?php if ( get_the_content() ) : ?>
            <div class="entry-content"><?php the_content(); ?></div>
            <?php endif; ?>
            <?php endwhile; ?>

            <?php if ( ! $blurb && ! get_the_content() ) : ?>
            <?php if ( current_user_can('edit_pages') ) : ?>
            <div class="about-empty-notice">
                <p><?php printf(
                    /* translators: 1: settings link  2: edit link */
                    esc_html__( 'Add your About blurb in %1$s, or write content directly in the %2$s.', 'dtf' ),
                    '<a href="' . esc_url( admin_url('options-general.php?page=dtf-options&tab=identity') ) . '">' . esc_html__( 'Settings → Identity', 'dtf' ) . '</a>',
                    '<a href="' . esc_url( get_edit_post_link() ) . '">' . esc_html__( 'page editor', 'dtf' ) . '</a>'
                ); ?></p>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php /* ── What we cover ── */ ?>
            <section class="about-covers">
                <h2 class="about-covers-title"><?php esc_html_e( 'What you\'ll find here', 'dtf' ); ?></h2>
                <div class="about-covers-grid">
                    <?php foreach ( $pills as [ $cls, $url, $label, $desc ] ) : ?>
                    <a href="<?php echo esc_url( $url ); ?>" class="about-cover-card">
                        <span class="about-cover-pill <?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $label ); ?></span>
                        <p class="about-cover-desc"><?php echo esc_html( $desc ); ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

        </main>

        <?php /* ── Aside ── */ ?>
        <aside class="about-aside">

            <?php if ( $socials ) : ?>
            <div class="about-card">
                <span class="about-card-label"><?php esc_html_e( 'Find us here', 'dtf' ); ?></span>
                <ul class="about-socials">
                    <?php foreach ( $socials as $name => $url ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( $name ); ?>
                            <span class="about-social-arrow" aria-hidden="true">→</span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="about-card about-card-cta">
                <span class="about-card-label" style="border-bottom-color:var(--tomato);"><?php esc_html_e( 'Get in touch', 'dtf' ); ?></span>
                <p class="about-card-note"><?php esc_html_e( 'Collaborations, tips, corrections — we read every message.', 'dtf' ); ?></p>
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="read-more-btn">
                    <?php esc_html_e( 'Contact us', 'dtf' ); ?>
                </a>
            </div>

        </aside>
    </div><!-- .about-body -->

</div><!-- .site-wrap -->
</div><!-- #about-page -->

<?php get_footer(); ?>
