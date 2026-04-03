<?php
defined( 'ABSPATH' ) || exit;

/* ─────────────────────────────────────────────────────────────────
   DTF Newsletter Widget
   ─────────────────────────────────────────────────────────────────
   Renders a styled signup form inside any sidebar / widget area.
   All service-level settings (form action URL, field names, hidden
   fields) live in Settings → Discover Tasty → Newsletter so you
   only configure them once even if you drop the widget in multiple
   locations.  Per-widget overrides: title, description, button text.
   ───────────────────────────────────────────────────────────────── */

class DTF_Newsletter_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'dtf_newsletter',
            __( 'DTF — Newsletter Signup', 'dtf' ),
            [ 'description' => __( 'Email newsletter signup form styled to match the theme sidebar.', 'dtf' ) ]
        );
    }

    /* ── Front-end output ───────────────────────────────────────── */
    public function widget( $args, $instance ) {
        $action      = dtf_opt( 'nl_form_action', '' );
        $email_field = dtf_opt( 'nl_email_field', 'EMAIL' );
        $hidden_raw  = dtf_opt( 'nl_hidden_fields', '' );
        $success_msg = dtf_opt( 'nl_success_msg', __( '✓ Thank you! Check your inbox.', 'dtf' ) );

        // Per-widget overrides fall back to admin defaults
        $title = ! empty( $instance['title'] ) ? $instance['title'] : dtf_opt( 'nl_title', __( 'Sign Up for Updates', 'dtf' ) );
        $desc  = ! empty( $instance['desc'] )  ? $instance['desc']  : dtf_opt( 'nl_desc',  __( 'Get the latest food writing straight to your inbox.', 'dtf' ) );
        $btn   = ! empty( $instance['btn'] )   ? $instance['btn']   : dtf_opt( 'nl_btn',   __( 'Sign Up', 'dtf' ) );

        // Warn admins if the form action hasn't been set yet
        if ( ! $action ) {
            if ( current_user_can( 'manage_options' ) ) {
                echo $args['before_widget'];
                echo '<div class="sb-section"><p style="font-size:12px;color:var(--tomato);padding:10px 0;">';
                esc_html_e( 'Newsletter widget: please set your form action URL in Settings → Discover Tasty → Newsletter.', 'dtf' );
                echo '</p></div>';
                echo $args['after_widget'];
            }
            return;
        }

        // Build a unique ID for ARIA / JS targeting
        $uid = 'dtf-nl-' . $this->id;

        echo $args['before_widget'];
        ?>
        <span class="sb-label"><?php echo esc_html( $title ); ?></span>
        <?php if ( $desc ) : ?>
        <p class="dtf-nl-desc"><?php echo esc_html( $desc ); ?></p>
        <?php endif; ?>
        <form class="dtf-nl-form"
              id="<?php echo esc_attr( $uid ); ?>"
              action="<?php echo esc_url( $action ); ?>"
              method="post"
              target="_blank"
              novalidate>

            <?php /* Hidden fields — one per line: name=value */ ?>
            <?php foreach ( dtf_nl_parse_hidden( $hidden_raw ) as $fname => $fval ) : ?>
            <input type="hidden" name="<?php echo esc_attr( $fname ); ?>" value="<?php echo esc_attr( $fval ); ?>">
            <?php endforeach; ?>

            <div class="dtf-nl-row">
                <input type="email"
                       name="<?php echo esc_attr( $email_field ); ?>"
                       placeholder="<?php esc_attr_e( 'Email address', 'dtf' ); ?>"
                       required
                       autocomplete="email"
                       aria-label="<?php esc_attr_e( 'Email address', 'dtf' ); ?>"
                       class="dtf-nl-input">
                <button type="submit" class="dtf-nl-btn">
                    <?php echo esc_html( $btn ); ?>
                </button>
            </div>

            <p class="dtf-nl-success" role="status" aria-live="polite"></p>
        </form>

        <script>
        (function(){
            var form = document.getElementById(<?php echo wp_json_encode( $uid ); ?>);
            if (!form) return;
            var row     = form.querySelector('.dtf-nl-row');
            var success = form.querySelector('.dtf-nl-success');
            var msg     = <?php echo wp_json_encode( $success_msg ); ?>;
            form.addEventListener('submit', function(e){
                var email = form.querySelector('[type="email"]');
                if (!email || !email.value || !email.checkValidity()) return;
                // Form submits to the service in a new tab; show inline confirmation
                setTimeout(function(){
                    row.style.display     = 'none';
                    success.textContent   = msg;
                    success.style.display = 'block';
                }, 150);
            });
        })();
        </script>
        <?php
        echo $args['after_widget'];
    }

    /* ── Admin widget form ──────────────────────────────────────── */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $desc  = isset( $instance['desc'] )  ? $instance['desc']  : '';
        $btn   = isset( $instance['btn'] )   ? $instance['btn']   : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">
                <?php esc_html_e( 'Title', 'dtf' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id('title') ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name('title') ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>"
                   placeholder="<?php esc_attr_e( 'Sign Up for Updates', 'dtf' ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('desc') ); ?>">
                <?php esc_html_e( 'Description', 'dtf' ); ?>
            </label>
            <textarea class="widefat"
                      id="<?php echo esc_attr( $this->get_field_id('desc') ); ?>"
                      name="<?php echo esc_attr( $this->get_field_name('desc') ); ?>"
                      rows="3"><?php echo esc_textarea( $desc ); ?></textarea>
            <em style="font-size:11px;color:#888;"><?php esc_html_e( 'Leave blank to use the default set in Settings → Discover Tasty → Newsletter.', 'dtf' ); ?></em>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('btn') ); ?>">
                <?php esc_html_e( 'Button label', 'dtf' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id('btn') ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name('btn') ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $btn ); ?>"
                   placeholder="Sign Up">
        </p>
        <?php
    }

    /* ── Save widget instance ───────────────────────────────────── */
    public function update( $new_instance, $old_instance ) {
        return [
            'title' => sanitize_text_field( $new_instance['title'] ),
            'desc'  => sanitize_textarea_field( $new_instance['desc'] ),
            'btn'   => sanitize_text_field( $new_instance['btn'] ),
        ];
    }
}

/* ── Register widget ─────────────────────────────────────────────── */
function dtf_register_newsletter_widget() {
    register_widget( 'DTF_Newsletter_Widget' );
}
add_action( 'widgets_init', 'dtf_register_newsletter_widget' );

/* ── Helper: parse hidden fields textarea ────────────────────────── */
function dtf_nl_parse_hidden( $raw ) {
    $fields = [];
    if ( ! $raw ) return $fields;
    foreach ( explode( "\n", $raw ) as $line ) {
        $line = trim( $line );
        if ( ! $line || strpos( $line, '=' ) === false ) continue;
        [ $k, $v ] = explode( '=', $line, 2 );
        $fields[ trim( $k ) ] = trim( $v );
    }
    return $fields;
}

/* ── Shortcode: [dtf_newsletter title="" desc="" btn=""] ─────────── */
function dtf_newsletter_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'title' => '',
        'desc'  => '',
        'btn'   => '',
    ], $atts, 'dtf_newsletter' );

    // Reuse widget output via a fake widget instance
    $widget   = new DTF_Newsletter_Widget();
    $instance = [
        'title' => $atts['title'],
        'desc'  => $atts['desc'],
        'btn'   => $atts['btn'],
    ];
    $widget_args = [
        'before_widget' => '<div class="dtf-nl-shortcode sb-section">',
        'after_widget'  => '</div>',
        'before_title'  => '',
        'after_title'   => '',
    ];

    ob_start();
    $widget->widget( $widget_args, $instance );
    return ob_get_clean();
}
add_shortcode( 'dtf_newsletter', 'dtf_newsletter_shortcode' );
