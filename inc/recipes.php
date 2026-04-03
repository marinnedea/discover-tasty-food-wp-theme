<?php
defined( 'ABSPATH' ) || exit;

/* ── Register CPT ───────────────────────────────────────────────── */
function dtf_register_recipe_cpt() {
    $labels = [
        'name'               => __( 'Recipes', 'dtf' ),
        'singular_name'      => __( 'Recipe', 'dtf' ),
        'add_new_item'       => __( 'Add New Recipe', 'dtf' ),
        'edit_item'          => __( 'Edit Recipe', 'dtf' ),
        'view_item'          => __( 'View Recipe', 'dtf' ),
        'all_items'          => __( 'All Recipes', 'dtf' ),
        'not_found'          => __( 'No recipes found.', 'dtf' ),
        'not_found_in_trash' => __( 'No recipes found in trash.', 'dtf' ),
    ];
    register_post_type( 'dtf_recipe', [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'recipes' ],
        'show_in_rest'  => true,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'menu_icon'     => 'dashicons-carrot',
        'menu_position' => 6,
    ] );
}
add_action( 'init', 'dtf_register_recipe_cpt' );

/* ── Admin meta box ─────────────────────────────────────────────── */
function dtf_recipe_add_meta_box() {
    add_meta_box(
        'dtf-recipe-fields',
        __( 'Recipe Details', 'dtf' ),
        'dtf_render_recipe_meta_box',
        'dtf_recipe',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dtf_recipe_add_meta_box' );

function dtf_render_recipe_meta_box( $post ) {
    wp_nonce_field( 'dtf_recipe_save', 'dtf_recipe_nonce' );
    $fields = [
        'dtf_cuisine'    => [ 'label' => 'Cuisine',        'type' => 'text', 'col' => 1 ],
        'dtf_difficulty' => [ 'label' => 'Difficulty',     'type' => 'select', 'col' => 1,
            'options' => [ '' => '— select —', 'easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard' ] ],
        'dtf_prep_time'  => [ 'label' => 'Prep time (min)', 'type' => 'text', 'col' => 1 ],
        'dtf_cook_time'  => [ 'label' => 'Cook time (min)', 'type' => 'text', 'col' => 1 ],
        'dtf_servings'   => [ 'label' => 'Servings',        'type' => 'text', 'col' => 1 ],
        'dtf_calories'   => [ 'label' => 'Calories',        'type' => 'text', 'col' => 1 ],
        'dtf_protein'    => [ 'label' => 'Protein (g)',     'type' => 'text', 'col' => 1 ],
        'dtf_carbs'      => [ 'label' => 'Carbs (g)',       'type' => 'text', 'col' => 1 ],
        'dtf_fat'        => [ 'label' => 'Fat (g)',         'type' => 'text', 'col' => 1 ],
    ];
    $label_style = 'display:block;font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#666;margin-bottom:5px;';
    $input_style = 'width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;';

    echo '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px 22px;margin-bottom:18px;">';
    foreach ( $fields as $key => $cfg ) {
        $val = get_post_meta( $post->ID, $key, true );
        echo '<div>';
        echo '<label for="' . esc_attr( $key ) . '" style="' . $label_style . '">' . esc_html( $cfg['label'] ) . '</label>';
        if ( $cfg['type'] === 'text' ) {
            echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" style="' . $input_style . '">';
        } elseif ( $cfg['type'] === 'select' ) {
            echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" style="' . $input_style . '">';
            foreach ( $cfg['options'] as $opt_val => $opt_label ) {
                echo '<option value="' . esc_attr( $opt_val ) . '"' . selected( $val, $opt_val, false ) . '>' . esc_html( $opt_label ) . '</option>';
            }
            echo '</select>';
        }
        echo '</div>';
    }
    echo '</div>';

    // Ingredients
    $ingredients = get_post_meta( $post->ID, 'dtf_ingredients', true );
    echo '<div style="margin-bottom:16px;">';
    echo '<label for="dtf_ingredients" style="' . $label_style . '">Ingredients <span style="font-weight:400;text-transform:none;letter-spacing:0;">(one per line; prefix with <code>*</code> for section headers)</span></label>';
    echo '<textarea id="dtf_ingredients" name="dtf_ingredients" rows="8" style="' . $input_style . 'resize:vertical;">' . esc_textarea( $ingredients ) . '</textarea>';
    echo '</div>';

    // Steps
    $steps = get_post_meta( $post->ID, 'dtf_steps', true );
    echo '<div>';
    echo '<label for="dtf_steps" style="' . $label_style . '">Steps <span style="font-weight:400;text-transform:none;letter-spacing:0;">(one step per line; auto-numbered)</span></label>';
    echo '<textarea id="dtf_steps" name="dtf_steps" rows="8" style="' . $input_style . 'resize:vertical;">' . esc_textarea( $steps ) . '</textarea>';
    echo '</div>';
}

function dtf_save_recipe_meta( $post_id ) {
    if ( ! isset( $_POST['dtf_recipe_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['dtf_recipe_nonce'] ), 'dtf_recipe_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $text_keys = [ 'dtf_cuisine', 'dtf_difficulty', 'dtf_prep_time', 'dtf_cook_time',
                   'dtf_servings', 'dtf_calories', 'dtf_protein', 'dtf_carbs', 'dtf_fat' ];
    foreach ( $text_keys as $key ) {
        if ( isset( $_POST[ $key ] ) ) update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
    }
    if ( isset( $_POST['dtf_ingredients'] ) ) update_post_meta( $post_id, 'dtf_ingredients', sanitize_textarea_field( wp_unslash( $_POST['dtf_ingredients'] ) ) );
    if ( isset( $_POST['dtf_steps'] ) )       update_post_meta( $post_id, 'dtf_steps',       sanitize_textarea_field( wp_unslash( $_POST['dtf_steps'] ) ) );
}
add_action( 'save_post_dtf_recipe', 'dtf_save_recipe_meta' );

/* ── Render: full recipe card ───────────────────────────────────── */
function dtf_render_recipe_card( $post_id ) {
    $cuisine   = get_post_meta( $post_id, 'dtf_cuisine',    true );
    $prep      = get_post_meta( $post_id, 'dtf_prep_time',  true );
    $cook      = get_post_meta( $post_id, 'dtf_cook_time',  true );
    $servings  = get_post_meta( $post_id, 'dtf_servings',   true );
    $difficulty= get_post_meta( $post_id, 'dtf_difficulty', true );
    $calories  = get_post_meta( $post_id, 'dtf_calories',   true );
    $protein   = get_post_meta( $post_id, 'dtf_protein',    true );
    $carbs     = get_post_meta( $post_id, 'dtf_carbs',      true );
    $fat       = get_post_meta( $post_id, 'dtf_fat',        true );
    $ing_raw   = get_post_meta( $post_id, 'dtf_ingredients',true );
    $steps_raw = get_post_meta( $post_id, 'dtf_steps',      true );

    $total = ( (int) $prep + (int) $cook );

    ob_start(); ?>
    <div class="recipe-card">
        <div class="recipe-header">
            <?php if ( $cuisine ) : ?>
                <span class="recipe-cuisine-tag"><?php echo esc_html( $cuisine ); ?></span>
            <?php endif; ?>
            <h1 class="recipe-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
        </div>

        <div class="recipe-stats">
            <?php if ( $prep ) : ?>
            <div class="recipe-stat">
                <div class="recipe-stat-val"><?php echo esc_html( $prep ); ?><span style="font-size:12px;font-weight:400;">m</span></div>
                <div class="recipe-stat-label"><?php esc_html_e( 'Prep', 'dtf' ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $cook ) : ?>
            <div class="recipe-stat">
                <div class="recipe-stat-val"><?php echo esc_html( $cook ); ?><span style="font-size:12px;font-weight:400;">m</span></div>
                <div class="recipe-stat-label"><?php esc_html_e( 'Cook', 'dtf' ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $total ) : ?>
            <div class="recipe-stat">
                <div class="recipe-stat-val"><?php echo esc_html( $total ); ?><span style="font-size:12px;font-weight:400;">m</span></div>
                <div class="recipe-stat-label"><?php esc_html_e( 'Total', 'dtf' ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $servings ) : ?>
            <div class="recipe-stat">
                <div class="recipe-stat-val"><?php echo esc_html( $servings ); ?></div>
                <div class="recipe-stat-label"><?php esc_html_e( 'Servings', 'dtf' ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $difficulty ) : ?>
            <div class="recipe-stat">
                <div class="recipe-stat-val" style="font-size:14px;">
                    <span class="recipe-difficulty"><?php echo esc_html( ucfirst( $difficulty ) ); ?></span>
                </div>
                <div class="recipe-stat-label"><?php esc_html_e( 'Difficulty', 'dtf' ); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="recipe-body">
            <?php if ( $calories || $protein || $carbs || $fat ) : ?>
            <div class="recipe-nutrition">
                <?php if ( $calories ) : ?>
                <div class="recipe-ntr">
                    <div class="recipe-ntr-val"><?php echo esc_html( $calories ); ?></div>
                    <div class="recipe-ntr-label"><?php esc_html_e( 'Cal', 'dtf' ); ?></div>
                </div>
                <?php endif; ?>
                <?php if ( $protein ) : ?>
                <div class="recipe-ntr">
                    <div class="recipe-ntr-val"><?php echo esc_html( $protein ); ?>g</div>
                    <div class="recipe-ntr-label"><?php esc_html_e( 'Protein', 'dtf' ); ?></div>
                </div>
                <?php endif; ?>
                <?php if ( $carbs ) : ?>
                <div class="recipe-ntr">
                    <div class="recipe-ntr-val"><?php echo esc_html( $carbs ); ?>g</div>
                    <div class="recipe-ntr-label"><?php esc_html_e( 'Carbs', 'dtf' ); ?></div>
                </div>
                <?php endif; ?>
                <?php if ( $fat ) : ?>
                <div class="recipe-ntr">
                    <div class="recipe-ntr-val"><?php echo esc_html( $fat ); ?>g</div>
                    <div class="recipe-ntr-label"><?php esc_html_e( 'Fat', 'dtf' ); ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $ing_raw || $steps_raw ) : ?>
            <div class="recipe-cols">
                <?php if ( $ing_raw ) : ?>
                <div>
                    <div class="recipe-col-label"><?php esc_html_e( 'Ingredients', 'dtf' ); ?></div>
                    <ul class="recipe-ingredients">
                    <?php foreach ( explode( "\n", $ing_raw ) as $line ) :
                        $line = trim( $line );
                        if ( ! $line ) continue;
                        if ( str_starts_with( $line, '*' ) ) :
                            echo '<li class="ing-section">' . esc_html( ltrim( $line, '* ' ) ) . '</li>';
                        else : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php if ( $steps_raw ) : ?>
                <div>
                    <div class="recipe-col-label"><?php esc_html_e( 'Instructions', 'dtf' ); ?></div>
                    <ol class="recipe-steps">
                    <?php foreach ( explode( "\n", $steps_raw ) as $step ) :
                        $step = trim( $step );
                        if ( ! $step ) continue; ?>
                        <li><?php echo esc_html( $step ); ?></li>
                    <?php endforeach; ?>
                    </ol>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* ── Shortcode [dtf_recipe id="123"] ────────────────────────────── */
function dtf_recipe_shortcode( $atts ) {
    $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'dtf_recipe' );
    $id   = (int) $atts['id'];
    if ( ! $id || get_post_type( $id ) !== 'dtf_recipe' ) return '';
    return dtf_render_recipe_card( $id );
}
add_shortcode( 'dtf_recipe', 'dtf_recipe_shortcode' );

/* ── JSON-LD schema ─────────────────────────────────────────────── */
function dtf_recipe_schema() {
    if ( ! is_singular( 'dtf_recipe' ) ) return;
    $post_id   = get_the_ID();
    $prep      = get_post_meta( $post_id, 'dtf_prep_time',  true );
    $cook      = get_post_meta( $post_id, 'dtf_cook_time',  true );
    $servings  = get_post_meta( $post_id, 'dtf_servings',   true );
    $calories  = get_post_meta( $post_id, 'dtf_calories',   true );
    $ing_raw   = get_post_meta( $post_id, 'dtf_ingredients',true );
    $steps_raw = get_post_meta( $post_id, 'dtf_steps',      true );

    $schema = array_filter( [
        '@context'    => 'https://schema.org',
        '@type'       => 'Recipe',
        'name'        => get_the_title( $post_id ),
        'description' => get_the_excerpt( $post_id ),
        'url'         => get_permalink( $post_id ),
        'datePublished' => get_the_date( 'c', $post_id ),
        'image'       => get_the_post_thumbnail_url( $post_id, 'dtf-hero' ) ?: null,
        'prepTime'    => $prep  ? 'PT' . (int)$prep  . 'M' : null,
        'cookTime'    => $cook  ? 'PT' . (int)$cook  . 'M' : null,
        'totalTime'   => ( $prep || $cook ) ? 'PT' . ( (int)$prep + (int)$cook ) . 'M' : null,
        'recipeYield' => $servings ? $servings . ' servings' : null,
        'nutrition'   => $calories ? [ '@type' => 'NutritionInformation', 'calories' => $calories . ' calories' ] : null,
        'recipeIngredient' => $ing_raw ? array_filter( array_map( 'trim', explode( "\n", $ing_raw ) ) ) : null,
        'recipeInstructions' => $steps_raw ? array_map( function( $s ) {
            return [ '@type' => 'HowToStep', 'text' => trim( $s ) ];
        }, array_filter( array_map( 'trim', explode( "\n", $steps_raw ) ) ) ) : null,
    ] );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dtf_recipe_schema' );
