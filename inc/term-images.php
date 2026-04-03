<?php
/**
 * Category / term featured images.
 *
 * Adds an image upload field to the Add and Edit screens of any registered
 * taxonomy. Stores the attachment ID in term meta. Provides two public
 * helpers for templates:
 *
 *   dtf_get_term_image( $term, $size, $attr ) → <img> HTML
 *   dtf_get_term_image_url( $term, $size )   → URL string
 *
 * Works with the standard WP Media Library — no third-party plugins needed.
 */
defined( 'ABSPATH' ) || exit;

/* ── Taxonomies that get image support ────────────────────────────── */
function dtf_image_taxonomies() {
    return (array) apply_filters( 'dtf_image_taxonomies', [ 'category' ] );
}

/* ── Register term meta ───────────────────────────────────────────── */
function dtf_register_term_image_meta() {
    foreach ( dtf_image_taxonomies() as $tax ) {
        register_term_meta( $tax, 'dtf_term_image_id', [
            'type'              => 'integer',
            'single'            => true,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() { return current_user_can( 'manage_categories' ); },
            'show_in_rest'      => true,
        ] );
    }
}
add_action( 'init', 'dtf_register_term_image_meta' );

/* ── Hook field + save into every supported taxonomy ─────────────── */
function dtf_term_image_hooks() {
    foreach ( dtf_image_taxonomies() as $tax ) {
        add_action( "{$tax}_add_form_fields",  'dtf_term_image_add_field' );
        add_action( "{$tax}_edit_form_fields", 'dtf_term_image_edit_field', 10, 2 );
        add_action( "created_{$tax}",          'dtf_term_image_save' );
        add_action( "edited_{$tax}",           'dtf_term_image_save' );
        add_action( "delete_{$tax}",           'dtf_term_image_delete' );
    }
}
add_action( 'init', 'dtf_term_image_hooks' );

/* ── "Add new" form field ─────────────────────────────────────────── */
function dtf_term_image_add_field( $taxonomy ) {
    ?>
    <div class="form-field dtf-term-img-field">
        <label><?php esc_html_e( 'Category image', 'dtf' ); ?></label>
        <?php wp_nonce_field( 'dtf_term_image_save', 'dtf_term_image_nonce' ); ?>
        <div class="dtf-term-img-wrap">
            <img id="dtf-term-img-preview" src="" alt=""
                 style="display:none;max-width:200px;height:auto;margin-bottom:8px;border-radius:4px;">
            <input type="hidden" id="dtf-term-img-id" name="dtf_term_image_id" value="">
            <br>
            <button type="button" class="button dtf-term-img-btn-select">
                <?php esc_html_e( 'Select image', 'dtf' ); ?>
            </button>
            <button type="button" class="button dtf-term-img-btn-remove" style="display:none;margin-left:4px;">
                <?php esc_html_e( 'Remove', 'dtf' ); ?>
            </button>
        </div>
        <p><?php esc_html_e( 'Shown in the category archive header and any template that calls dtf_get_term_image().', 'dtf' ); ?></p>
    </div>
    <?php
}

/* ── Edit form field ──────────────────────────────────────────────── */
function dtf_term_image_edit_field( $term, $taxonomy ) {
    $img_id  = (int) get_term_meta( $term->term_id, 'dtf_term_image_id', true );
    $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';
    ?>
    <tr class="form-field dtf-term-img-field">
        <th><label><?php esc_html_e( 'Category image', 'dtf' ); ?></label></th>
        <td>
            <?php wp_nonce_field( 'dtf_term_image_save', 'dtf_term_image_nonce' ); ?>
            <div class="dtf-term-img-wrap">
                <img id="dtf-term-img-preview"
                     src="<?php echo esc_url( $img_url ); ?>"
                     alt=""
                     style="<?php echo $img_url ? 'display:block;' : 'display:none;'; ?>max-width:200px;height:auto;margin-bottom:8px;border-radius:4px;">
                <input type="hidden" id="dtf-term-img-id" name="dtf_term_image_id"
                       value="<?php echo esc_attr( $img_id ?: '' ); ?>">
                <button type="button" class="button dtf-term-img-btn-select">
                    <?php esc_html_e( 'Select image', 'dtf' ); ?>
                </button>
                <button type="button" class="button dtf-term-img-btn-remove"
                        style="<?php echo $img_id ? '' : 'display:none;'; ?>margin-left:4px;">
                    <?php esc_html_e( 'Remove', 'dtf' ); ?>
                </button>
            </div>
            <p class="description">
                <?php esc_html_e( 'Shown in the category archive header and any template that calls dtf_get_term_image().', 'dtf' ); ?>
            </p>
        </td>
    </tr>
    <?php
}

/* ── Save ─────────────────────────────────────────────────────────── */
function dtf_term_image_save( $term_id ) {
    if (
        ! isset( $_POST['dtf_term_image_nonce'] ) ||
        ! wp_verify_nonce( wp_unslash( $_POST['dtf_term_image_nonce'] ), 'dtf_term_image_save' )
    ) return;

    if ( ! current_user_can( 'manage_categories' ) ) return;

    $img_id = isset( $_POST['dtf_term_image_id'] ) ? absint( $_POST['dtf_term_image_id'] ) : 0;

    if ( $img_id ) {
        update_term_meta( $term_id, 'dtf_term_image_id', $img_id );
    } else {
        delete_term_meta( $term_id, 'dtf_term_image_id' );
    }
}

/* ── Clean up on term delete ──────────────────────────────────────── */
function dtf_term_image_delete( $term_id ) {
    delete_term_meta( $term_id, 'dtf_term_image_id' );
}

/* ── Enqueue WP media uploader + inline JS on term screens ───────── */
function dtf_term_image_admin_scripts() {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->base, [ 'edit-tags', 'term' ], true ) ) return;
    if ( ! in_array( $screen->taxonomy, dtf_image_taxonomies(), true ) ) return;

    wp_enqueue_media();

    $select_title = esc_js( __( 'Select category image', 'dtf' ) );
    $select_btn   = esc_js( __( 'Use this image',        'dtf' ) );

    wp_add_inline_script( 'jquery-core', "
    jQuery(function(\$){
        var frame;

        \$(document).on('click', '.dtf-term-img-btn-select', function(e){
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media({
                title:    '{$select_title}',
                button:   { text: '{$select_btn}' },
                multiple: false,
                library:  { type: 'image' }
            });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                \$('#dtf-term-img-id').val(att.id);
                \$('#dtf-term-img-preview').attr('src', url).show();
                \$('.dtf-term-img-btn-remove').show();
            });
            frame.open();
        });

        \$(document).on('click', '.dtf-term-img-btn-remove', function(e){
            e.preventDefault();
            \$('#dtf-term-img-id').val('');
            \$('#dtf-term-img-preview').attr('src','').hide();
            \$(this).hide();
        });
    });
    " );
}
add_action( 'admin_enqueue_scripts', 'dtf_term_image_admin_scripts' );

/* ═══════════════════════════════════════════════════════════════════
   PUBLIC HELPERS
   ═══════════════════════════════════════════════════════════════════ */

/**
 * Return the <img> tag for a term's featured image.
 *
 * @param int|WP_Term|null $term  Term ID, WP_Term object, or null (= current queried term).
 * @param string           $size  Registered image size. Default 'dtf-hero'.
 * @param array            $attr  Extra attributes passed to wp_get_attachment_image().
 * @return string                 <img> HTML, or empty string if no image is set.
 */
function dtf_get_term_image( $term = null, $size = 'dtf-hero', $attr = [] ) {
    $img_id = dtf_get_term_image_id( $term );
    if ( ! $img_id ) return '';
    return wp_get_attachment_image( $img_id, $size, false, $attr );
}

/**
 * Return the URL of a term's featured image.
 *
 * @param int|WP_Term|null $term  Term ID, WP_Term object, or null (= current queried term).
 * @param string           $size  Registered image size. Default 'dtf-hero'.
 * @return string                 Image URL, or empty string if no image is set.
 */
function dtf_get_term_image_url( $term = null, $size = 'dtf-hero' ) {
    $img_id = dtf_get_term_image_id( $term );
    if ( ! $img_id ) return '';
    $src = wp_get_attachment_image_src( $img_id, $size );
    return $src ? $src[0] : '';
}

/**
 * Internal: resolve term and return its image attachment ID.
 *
 * @param int|WP_Term|null $term
 * @return int Attachment ID, or 0.
 */
function dtf_get_term_image_id( $term = null ) {
    if ( $term === null ) {
        $term = get_queried_object();
    }
    if ( $term instanceof WP_Term ) {
        $term_id = $term->term_id;
    } else {
        $term_id = absint( $term );
    }
    if ( ! $term_id ) return 0;
    return (int) get_term_meta( $term_id, 'dtf_term_image_id', true );
}
