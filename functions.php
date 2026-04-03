<?php
defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/encounters.php';
require_once get_template_directory() . '/inc/recipes.php';
require_once get_template_directory() . '/inc/admin-options.php';
require_once get_template_directory() . '/inc/widget-newsletter.php';
require_once get_template_directory() . '/inc/term-images.php';

function dtf_setup() {
    load_theme_textdomain( 'dtf', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-logo', [ 'height'=>80, 'width'=>300, 'flex-width'=>true, 'flex-height'=>true ] );
    add_image_size( 'dtf-hero',   960, 500, true );
    add_image_size( 'dtf-card',   480, 320, true );
    add_image_size( 'dtf-thumb',  120, 90,  true );
    add_image_size( 'dtf-review', 120, 120, true );
    register_nav_menus( [
        'primary'      => __( 'Primary Menu (topbar)', 'dtf' ),
        'footer'       => __( 'Footer Menu', 'dtf' ),
        'lang-switcher'=> __( 'Language Switcher', 'dtf' ),
    ] );
}
add_action( 'after_setup_theme', 'dtf_setup' );

function dtf_scripts() {
    wp_enqueue_style( 'dtf-style', get_stylesheet_uri(), [], '1.8.5' );
    add_editor_style( 'style.css' );
}
add_action( 'wp_enqueue_scripts', 'dtf_scripts' );

function dtf_widgets() {
    register_sidebar( [
        'name'          => __( 'Main Sidebar', 'dtf' ),
        'id'            => 'sidebar-main',
        'before_widget' => '<div class="sb-section">',
        'after_widget'  => '</div>',
        'before_title'  => '<span class="sb-label">',
        'after_title'   => '</span>',
    ] );
}
add_action( 'widgets_init', 'dtf_widgets' );

add_filter( 'excerpt_length', fn() => 20 );
add_filter( 'excerpt_more',   fn() => '…' );

function dtf_reading_time( $post_id = null ) {
    $words = str_word_count( strip_tags( get_post_field( 'post_content', $post_id ) ) );
    $mins  = max( 1, (int) round( $words / 200 ) );
    /* translators: %d: number of minutes */
    return sprintf( __( '%d min read', 'dtf' ), $mins );
}

function dtf_post_type_label( $post_id = null ) {
    $type = get_post_type( $post_id );
    $cats = get_the_category( $post_id );
    if ( $type === 'dtf_encounter' ) return '<span class="tag-pill tag-encounter">' . esc_html__( 'Encounter', 'dtf' ) . '</span>';
    if ( $type === 'dtf_recipe' )    return '<span class="tag-pill tag-recipe">'    . esc_html__( 'Recipe', 'dtf' )    . '</span>';
    if ( $cats ) {
        $slug = $cats[0]->slug;
        $map  = [ 'food'=>'tag-food', 'reviews'=>'tag-review', 'recipes'=>'tag-recipe', 'guides'=>'tag-guide' ];
        $cls  = $map[$slug] ?? 'tag-food';
        return '<span class="tag-pill ' . $cls . '">' . esc_html( $cats[0]->name ) . '</span>';
    }
    return '';
}

function dtf_stars( $rating ) {
    $r = max( 1, min( 5, (int) $rating ) );
    return str_repeat( '★', $r ) . str_repeat( '☆', 5 - $r );
}

function dtf_layout_body_class( $classes ) {
    $mode = dtf_opt( 'layout_mode', 'boxed' );
    if ( $mode === 'full-width' ) {
        $classes[] = 'dtf-full-width';
    }
    $custom_max_w = dtf_opt( 'layout_max_width' );
    if ( $custom_max_w && $mode === 'boxed' ) {
        $classes[] = 'dtf-custom-width';
    }
    return $classes;
}
add_filter( 'body_class', 'dtf_layout_body_class' );

function dtf_layout_inline_css() {
    $mode         = dtf_opt( 'layout_mode', 'boxed' );
    $custom_max_w = dtf_opt( 'layout_max_width' );
    if ( $mode === 'boxed' && $custom_max_w ) {
        $w = sanitize_text_field( $custom_max_w );
        // Validate strictly: only allow a positive number followed by a CSS length unit
        if ( preg_match( '/^\d+(\.\d+)?(px|em|rem|vw|ch|%)$/', $w ) ) {
            echo '<style>:root{--max-w:' . $w . ';}</style>' . "\n";
        }
    }
}
add_action( 'wp_head', 'dtf_layout_inline_css', 6 );

function dtf_register_encounter_meta_rest() {
    $keys = [ 'dtf_enc_dish','dtf_enc_place','dtf_enc_city','dtf_enc_cuisine',
              'dtf_enc_date','dtf_enc_price','dtf_enc_rating','dtf_enc_goback',
              'dtf_enc_available','dtf_enc_memorable','dtf_enc_best' ];
    foreach ( $keys as $key ) {
        register_post_meta( 'dtf_encounter', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => fn() => current_user_can( 'edit_posts' ),
        ] );
    }
}
add_action( 'init', 'dtf_register_encounter_meta_rest' );
