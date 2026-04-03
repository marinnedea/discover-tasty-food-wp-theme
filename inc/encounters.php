<?php
defined( 'ABSPATH' ) || exit;

/* ── Register CPT ───────────────────────────────────────────────── */
function dtf_register_encounter_cpt() {
    $labels = [
        'name'               => __( 'Food Encounters', 'dtf' ),
        'singular_name'      => __( 'Food Encounter', 'dtf' ),
        'add_new_item'       => __( 'Add New Encounter', 'dtf' ),
        'edit_item'          => __( 'Edit Encounter', 'dtf' ),
        'view_item'          => __( 'View Encounter', 'dtf' ),
        'all_items'          => __( 'All Encounters', 'dtf' ),
        'search_items'       => __( 'Search Encounters', 'dtf' ),
        'not_found'          => __( 'No encounters found.', 'dtf' ),
        'not_found_in_trash' => __( 'No encounters found in trash.', 'dtf' ),
    ];
    register_post_type( 'dtf_encounter', [
        'labels'      => $labels,
        'public'      => true,
        'has_archive' => true,
        'rewrite'     => [ 'slug' => 'food-encounters' ],
        'show_in_rest'=> true,
        'supports'    => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'menu_icon'   => 'dashicons-location-alt',
        'menu_position' => 5,
    ] );
}
add_action( 'init', 'dtf_register_encounter_cpt' );

/* ── Meta fields ────────────────────────────────────────────────── */
$dtf_enc_fields = [
    'dtf_enc_dish'      => [ 'label' => 'Dish name',        'type' => 'text' ],
    'dtf_enc_place'     => [ 'label' => 'Restaurant / place','type' => 'text' ],
    'dtf_enc_city'      => [ 'label' => 'City',             'type' => 'text' ],
    'dtf_enc_cuisine'   => [ 'label' => 'Cuisine',          'type' => 'text' ],
    'dtf_enc_date'      => [ 'label' => 'Date visited',     'type' => 'text' ],
    'dtf_enc_price'     => [ 'label' => 'Price range',      'type' => 'select', 'options' => [ '' => '— select —', '$' => '$ (cheap)', '$$' => '$$ (moderate)', '$$$' => '$$$ (pricey)', '$$$$' => '$$$$ (splurge)' ] ],
    'dtf_enc_rating'    => [ 'label' => 'Rating (1–5)',     'type' => 'stars' ],
    'dtf_enc_goback'    => [ 'label' => 'Would go back?',   'type' => 'select', 'options' => [ '' => '— select —', 'yes' => 'Yes', 'no' => 'No', 'maybe' => 'Maybe' ] ],
    'dtf_enc_available' => [ 'label' => 'Still available?', 'type' => 'select', 'options' => [ '' => '— select —', 'yes' => 'Yes', 'no' => 'No (disappeared)', 'seasonal' => 'Seasonal', 'unknown' => 'Unknown' ] ],
    'dtf_enc_memorable' => [ 'label' => 'One memorable sentence', 'type' => 'textarea' ],
    'dtf_enc_best'      => [ 'label' => 'What made it special',   'type' => 'textarea' ],
];

/* ── Admin meta box ─────────────────────────────────────────────── */
function dtf_encounter_add_meta_box() {
    add_meta_box(
        'dtf-encounter-fields',
        __( 'Encounter Details', 'dtf' ),
        'dtf_render_encounter_meta_box',
        'dtf_encounter',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dtf_encounter_add_meta_box' );

function dtf_render_encounter_meta_box( $post ) {
    global $dtf_enc_fields;
    wp_nonce_field( 'dtf_enc_save', 'dtf_enc_nonce' );
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px 24px;padding:4px 0 8px;">';
    foreach ( $dtf_enc_fields as $key => $cfg ) {
        $val = get_post_meta( $post->ID, $key, true );
        echo '<div' . ( in_array( $cfg['type'], [ 'textarea', 'stars' ] ) ? ' style="grid-column:1/-1;"' : '' ) . '>';
        echo '<label for="' . esc_attr( $key ) . '" style="display:block;font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#666;margin-bottom:5px;">' . esc_html( $cfg['label'] ) . '</label>';
        if ( $cfg['type'] === 'text' ) {
            echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" style="width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
        } elseif ( $cfg['type'] === 'select' ) {
            echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" style="width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
            foreach ( $cfg['options'] as $opt_val => $opt_label ) {
                echo '<option value="' . esc_attr( $opt_val ) . '"' . selected( $val, $opt_val, false ) . '>' . esc_html( $opt_label ) . '</option>';
            }
            echo '</select>';
        } elseif ( $cfg['type'] === 'textarea' ) {
            echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" style="width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;resize:vertical;">' . esc_textarea( $val ) . '</textarea>';
        } elseif ( $cfg['type'] === 'stars' ) {
            echo '<div class="dtf-star-rating" style="display:flex;gap:6px;align-items:center;">';
            for ( $i = 1; $i <= 5; $i++ ) {
                $checked = ( (int) $val === $i ) ? 'checked' : '';
                echo '<label style="cursor:pointer;font-size:26px;color:' . ( (int)$val >= $i ? '#A85540' : '#ddd' ) . ';" title="' . $i . ' stars">';
                echo '<input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $i ) . '" ' . $checked . ' style="display:none;">';
                echo '★</label>';
            }
            echo '</div>';
            echo '<script>
(function(){
    var labels=document.querySelectorAll(".dtf-star-rating label");
    labels.forEach(function(lbl,idx){
        lbl.addEventListener("mouseenter",function(){
            labels.forEach(function(l,i){ l.style.color = i<=idx ? "#A85540" : "#ddd"; });
        });
        lbl.addEventListener("mouseleave",function(){
            var checked=document.querySelector("[name=dtf_enc_rating]:checked");
            var v=checked?parseInt(checked.value):0;
            labels.forEach(function(l,i){ l.style.color = i<v ? "#A85540" : "#ddd"; });
        });
        lbl.querySelector("input").addEventListener("change",function(){
            var v=parseInt(this.value);
            labels.forEach(function(l,i){ l.style.color = i<v ? "#A85540" : "#ddd"; });
        });
    });
})();
</script>';
        }
        echo '</div>';
    }
    echo '</div>';
}

function dtf_save_encounter_meta( $post_id ) {
    global $dtf_enc_fields;
    if ( ! isset( $_POST['dtf_enc_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['dtf_enc_nonce'] ), 'dtf_enc_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    foreach ( array_keys( $dtf_enc_fields ) as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }
}
add_action( 'save_post_dtf_encounter', 'dtf_save_encounter_meta' );

/* ── Archive filter ─────────────────────────────────────────────── */
function dtf_encounter_pre_get_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'dtf_encounter' ) ) {
        if ( isset( $_GET['filter'] ) && $_GET['filter'] === 'disappeared' ) {
            $query->set( 'meta_query', [ [
                'key'   => 'dtf_enc_available',
                'value' => 'no',
            ] ] );
        }
    }
}
add_action( 'pre_get_posts', 'dtf_encounter_pre_get_posts' );

/* ── Helper: star string ────────────────────────────────────────── */
function dtf_enc_stars( $rating ) {
    $r = max( 1, min( 5, (int) $rating ) );
    return str_repeat( '★', $r ) . str_repeat( '☆', 5 - $r );
}

/* ── Helper: availability badge ─────────────────────────────────── */
function dtf_enc_availability( $status ) {
    $map = [
        'yes'      => [ 'class' => 'enc-avail-yes',      'label' => __( 'Still available', 'dtf' ) ],
        'no'       => [ 'class' => 'enc-avail-no',       'label' => __( 'Disappeared',     'dtf' ) ],
        'seasonal' => [ 'class' => 'enc-avail-seasonal', 'label' => __( 'Seasonal',        'dtf' ) ],
        'unknown'  => [ 'class' => 'enc-avail-unknown',  'label' => __( 'Unknown',         'dtf' ) ],
    ];
    if ( ! $status || ! isset( $map[ $status ] ) ) return '';
    $d = $map[ $status ];
    return '<span class="enc-availability-badge ' . esc_attr( $d['class'] ) . '">' . esc_html( $d['label'] ) . '</span>';
}

/* ── Render: full single encounter ──────────────────────────────── */
function dtf_render_encounter( $post_id ) {
    $dish      = get_post_meta( $post_id, 'dtf_enc_dish',      true ) ?: get_the_title( $post_id );
    $place     = get_post_meta( $post_id, 'dtf_enc_place',     true );
    $city      = get_post_meta( $post_id, 'dtf_enc_city',      true );
    $cuisine   = get_post_meta( $post_id, 'dtf_enc_cuisine',   true );
    $date      = get_post_meta( $post_id, 'dtf_enc_date',      true );
    $price     = get_post_meta( $post_id, 'dtf_enc_price',     true );
    $rating    = get_post_meta( $post_id, 'dtf_enc_rating',    true );
    $goback    = get_post_meta( $post_id, 'dtf_enc_goback',    true );
    $available = get_post_meta( $post_id, 'dtf_enc_available', true );
    $memorable = get_post_meta( $post_id, 'dtf_enc_memorable', true );
    $best      = get_post_meta( $post_id, 'dtf_enc_best',      true );

    ob_start(); ?>
    <div class="enc-single">
        <div class="enc-header">
            <?php if ( $cuisine ) : ?>
                <span class="enc-cuisine-tag"><?php echo esc_html( $cuisine ); ?></span>
            <?php endif; ?>
            <h1 class="enc-dish-title"><?php echo esc_html( $dish ); ?></h1>
            <?php if ( $place || $city ) : ?>
                <p class="enc-place">
                    <?php if ( $place ) echo esc_html( $place ); ?>
                    <?php if ( $place && $city ) echo ' &middot; '; ?>
                    <?php if ( $city ) echo esc_html( $city ); ?>
                </p>
            <?php endif; ?>
            <?php if ( $rating ) : ?>
                <div class="enc-stars-row">
                    <span class="enc-stars-big"><?php echo dtf_enc_stars( $rating ); ?></span>
                    <span class="enc-rating-num"><?php echo esc_html( $rating ); ?> / <?php esc_html_e( '5', 'dtf' ); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <?php echo get_the_post_thumbnail( $post_id, 'dtf-hero', [ 'class' => 'enc-featured-img' ] ); ?>
        <?php endif; ?>

        <?php if ( $date || $price || $goback || $available ) : ?>
        <div class="enc-meta-grid">
            <?php if ( $date ) : ?>
            <div class="enc-meta-item">
                <div class="enc-meta-label"><?php esc_html_e( 'Date', 'dtf' ); ?></div>
                <div class="enc-meta-value"><?php echo esc_html( $date ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $price ) : ?>
            <div class="enc-meta-item">
                <div class="enc-meta-label"><?php esc_html_e( 'Price', 'dtf' ); ?></div>
                <div class="enc-meta-value"><?php echo esc_html( $price ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $goback ) : ?>
            <div class="enc-meta-item">
                <div class="enc-meta-label"><?php esc_html_e( 'Would go back', 'dtf' ); ?></div>
                <div class="enc-meta-value"><?php echo esc_html( ucfirst( $goback ) ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $available ) : ?>
            <div class="enc-meta-item">
                <div class="enc-meta-label"><?php esc_html_e( 'Still available', 'dtf' ); ?></div>
                <div class="enc-meta-value"><?php echo dtf_enc_availability( $available ); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $memorable ) : ?>
            <blockquote class="enc-memorable"><?php echo esc_html( $memorable ); ?></blockquote>
        <?php endif; ?>

        <?php if ( $best ) : ?>
            <p class="enc-best"><?php echo esc_html( $best ); ?></p>
        <?php endif; ?>

        <div class="enc-content">
            <?php echo apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* ── JSON-LD schema ─────────────────────────────────────────────── */
function dtf_encounter_schema() {
    if ( ! is_singular( 'dtf_encounter' ) ) return;
    $post_id  = get_the_ID();
    $dish     = get_post_meta( $post_id, 'dtf_enc_dish',    true ) ?: get_the_title( $post_id );
    $place    = get_post_meta( $post_id, 'dtf_enc_place',   true );
    $city     = get_post_meta( $post_id, 'dtf_enc_city',    true );
    $rating   = get_post_meta( $post_id, 'dtf_enc_rating',  true );
    $schema = [
        '@context'     => 'https://schema.org',
        '@type'        => 'Review',
        'name'         => get_the_title( $post_id ),
        'description'  => get_the_excerpt( $post_id ),
        'itemReviewed' => array_filter( [
            '@type'  => 'FoodEstablishment',
            'name'   => $place ?: $dish,
            'address'=> array_filter( [ '@type' => 'PostalAddress', 'addressLocality' => $city ] ),
        ] ),
        'url'          => get_permalink( $post_id ),
        'datePublished'=> get_the_date( 'c', $post_id ),
    ];
    if ( $rating ) {
        $schema['reviewRating'] = [
            '@type'       => 'Rating',
            'ratingValue' => (int) $rating,
            'bestRating'  => 5,
            'worstRating' => 1,
        ];
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dtf_encounter_schema' );
