<?php
/**
 * Template for the Contact page.
 * Loaded automatically when a page has the slug "contact".
 *
 * Built-in form — no plugin required.
 * Uses wp_mail() → admin email, nonce CSRF, honeypot anti-spam.
 * POST → redirect (PRG pattern) prevents double-submission on refresh.
 */

/* ── Process form submission BEFORE any output ─────────────────── */
$dtf_contact_sent  = false;
$dtf_contact_error = '';

if (
    'POST' === $_SERVER['REQUEST_METHOD'] &&
    isset( $_POST['dtf_contact_nonce'] ) &&
    wp_verify_nonce( wp_unslash( $_POST['dtf_contact_nonce'] ), 'dtf_contact_form' )
) {
    // Honeypot — bots fill this hidden field; humans leave it empty
    if ( ! empty( $_POST['dtf_website'] ) ) {
        wp_redirect( add_query_arg( 'sent', '1', get_permalink() ) );
        exit;
    }

    $sender_name    = sanitize_text_field( wp_unslash( $_POST['contact_name']    ?? '' ) );
    $sender_email   = sanitize_email(      wp_unslash( $_POST['contact_email']   ?? '' ) );
    $subject        = sanitize_text_field( wp_unslash( $_POST['contact_subject'] ?? '' ) );
    $message_raw    = sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ?? '' ) );

    // Basic validation
    if ( ! $sender_name || ! $sender_email || ! $message_raw ) {
        $dtf_contact_error = __( 'Please fill in all required fields.', 'dtf' );
    } elseif ( ! is_email( $sender_email ) ) {
        $dtf_contact_error = __( 'Please enter a valid email address.', 'dtf' );
    } else {
        $to       = get_option( 'admin_email' );
        $subject  = $subject ?: sprintf( __( 'Message from %s via Discover Tasty Food', 'dtf' ), $sender_name );
        $body     = sprintf(
            /* translators: 1: sender name  2: sender email  3: message */
            __( "Name: %1\$s\nEmail: %2\$s\n\n%3\$s", 'dtf' ),
            $sender_name, $sender_email, $message_raw
        );
        $headers  = [
            'Content-Type: text/plain; charset=UTF-8',
            sprintf( 'Reply-To: %s <%s>', $sender_name, $sender_email ),
        ];

        if ( wp_mail( $to, $subject, $body, $headers ) ) {
            wp_redirect( add_query_arg( 'sent', '1', get_permalink() ) );
            exit;
        } else {
            $dtf_contact_error = __( 'Sorry, the message could not be sent. Please try again or email us directly.', 'dtf' );
        }
    }
}

$dtf_contact_sent = isset( $_GET['sent'] ) && '1' === $_GET['sent'];

/* ── Now render ─────────────────────────────────────────────────── */
get_header();

$socials = array_filter([
    'Instagram' => dtf_opt('social_instagram'),
    'Facebook'  => dtf_opt('social_facebook'),
    'Twitter'   => dtf_opt('social_twitter'),
    'YouTube'   => dtf_opt('social_youtube'),
    'TikTok'    => dtf_opt('social_tiktok'),
]);
?>

<div id="contact-page">
    <div class="site-wrap">
        <div class="contact-wrap">

            <?php /* ── Left: form ── */ ?>
            <main class="contact-main">

                <?php while ( have_posts() ) : the_post(); ?>
                <div class="contact-hdr">
                    <p class="single-eyebrow"><?php esc_html_e( 'Get in touch', 'dtf' ); ?></p>
                    <h1 class="single-title"><?php the_title(); ?></h1>
                    <?php if ( get_the_content() ) : ?>
                    <div class="contact-intro entry-content"><?php the_content(); ?></div>
                    <?php else : ?>
                    <p class="contact-intro-default"><?php esc_html_e( 'Collaborations, tips, story ideas, corrections — we read every message and reply within a few days.', 'dtf' ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>

                <?php if ( $dtf_contact_sent ) : ?>
                <div class="contact-success" role="status">
                    <span class="contact-success-icon" aria-hidden="true">✓</span>
                    <div>
                        <strong><?php esc_html_e( 'Message sent!', 'dtf' ); ?></strong>
                        <p><?php esc_html_e( 'Thanks for reaching out. We\'ll get back to you within a few days.', 'dtf' ); ?></p>
                    </div>
                </div>

                <?php else : ?>

                <?php if ( $dtf_contact_error ) : ?>
                <div class="contact-error" role="alert">
                    <?php echo esc_html( $dtf_contact_error ); ?>
                </div>
                <?php endif; ?>

                <form class="contact-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
                    <?php wp_nonce_field( 'dtf_contact_form', 'dtf_contact_nonce' ); ?>

                    <?php /* Honeypot — hidden from humans, bots fill it */ ?>
                    <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
                        <label for="dtf_website"><?php esc_html_e( 'Leave this blank', 'dtf' ); ?></label>
                        <input type="text" id="dtf_website" name="dtf_website" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="contact-row contact-row-2col">
                        <div class="contact-field">
                            <label for="contact_name" class="contact-label">
                                <?php esc_html_e( 'Your name', 'dtf' ); ?> <span class="contact-req" aria-label="required">*</span>
                            </label>
                            <input type="text"
                                   id="contact_name"
                                   name="contact_name"
                                   class="contact-input"
                                   required
                                   autocomplete="name"
                                   value="<?php echo esc_attr( wp_unslash( $_POST['contact_name'] ?? '' ) ); ?>">
                        </div>
                        <div class="contact-field">
                            <label for="contact_email" class="contact-label">
                                <?php esc_html_e( 'Email address', 'dtf' ); ?> <span class="contact-req" aria-label="required">*</span>
                            </label>
                            <input type="email"
                                   id="contact_email"
                                   name="contact_email"
                                   class="contact-input"
                                   required
                                   autocomplete="email"
                                   value="<?php echo esc_attr( wp_unslash( $_POST['contact_email'] ?? '' ) ); ?>">
                        </div>
                    </div>

                    <div class="contact-field">
                        <label for="contact_subject" class="contact-label">
                            <?php esc_html_e( 'Subject', 'dtf' ); ?>
                        </label>
                        <input type="text"
                               id="contact_subject"
                               name="contact_subject"
                               class="contact-input"
                               value="<?php echo esc_attr( wp_unslash( $_POST['contact_subject'] ?? '' ) ); ?>">
                    </div>

                    <div class="contact-field">
                        <label for="contact_message" class="contact-label">
                            <?php esc_html_e( 'Message', 'dtf' ); ?> <span class="contact-req" aria-label="required">*</span>
                        </label>
                        <textarea id="contact_message"
                                  name="contact_message"
                                  class="contact-input contact-textarea"
                                  rows="7"
                                  required><?php echo esc_textarea( wp_unslash( $_POST['contact_message'] ?? '' ) ); ?></textarea>
                    </div>

                    <div class="contact-submit-row">
                        <button type="submit" class="contact-submit">
                            <?php esc_html_e( 'Send message', 'dtf' ); ?>
                            <span aria-hidden="true">→</span>
                        </button>
                        <p class="contact-privacy">
                            <?php printf(
                                /* translators: %s: privacy policy link */
                                esc_html__( 'Your details are used only to respond to your message. See our %s.', 'dtf' ),
                                '<a href="' . esc_url( home_url('/privacy-policy/') ) . '">' . esc_html__( 'privacy policy', 'dtf' ) . '</a>'
                            ); ?>
                        </p>
                    </div>
                </form>

                <?php endif; /* sent */ ?>
            </main>

            <?php /* ── Right: sidebar info ── */ ?>
            <aside class="contact-sidebar">

                <div class="sb-section">
                    <span class="sb-label"><?php esc_html_e( 'Direct email', 'dtf' ); ?></span>
                    <p class="contact-aside-text">
                        <?php
                        /*
                         * Email protection — address is never written to the HTML source.
                         * Split into two base64-encoded data attributes; only assembled
                         * client-side when the visitor clicks the reveal button.
                         * Bots scraping the DOM or raw HTML source see no address at all.
                         */
                        $raw   = get_option( 'admin_email' );
                        $local = base64_encode( strstr( $raw, '@', true ) );         // everything before @
                        $host  = base64_encode( ltrim( strstr( $raw, '@' ), '@' ) ); // everything after @
                        ?>
                        <span class="dtf-email-protect"
                              data-a="<?php echo esc_attr( $local ); ?>"
                              data-b="<?php echo esc_attr( $host ); ?>">
                            <button type="button" class="dtf-reveal-email">
                                <?php esc_html_e( 'Show email address', 'dtf' ); ?>
                            </button>
                        </span>
                        <script>
                        (function(){
                            var wrap = document.currentScript.previousElementSibling;
                            var btn  = wrap ? wrap.querySelector('.dtf-reveal-email') : null;
                            if (!btn) return;
                            btn.addEventListener('click', function(){
                                var a = atob(wrap.dataset.a);
                                var b = atob(wrap.dataset.b);
                                var email = a + '\u0040' + b; // \u0040 = @ — keeps literal out of source
                                var link  = document.createElement('a');
                                link.href        = 'mailto:' + email;
                                link.textContent = email;
                                link.className   = 'contact-aside-text';
                                wrap.replaceWith(link);
                            });
                        })();
                        </script>
                    </p>
                </div>

                <?php if ( $socials ) : ?>
                <div class="sb-section">
                    <span class="sb-label"><?php esc_html_e( 'Follow along', 'dtf' ); ?></span>
                    <ul class="contact-socials">
                        <?php foreach ( $socials as $name => $url ) : ?>
                        <li>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html( $name ); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="sb-section">
                    <span class="sb-label"><?php esc_html_e( 'Response time', 'dtf' ); ?></span>
                    <p class="contact-aside-text"><?php esc_html_e( 'We aim to reply within 2–3 business days. For press or collaboration enquiries, please include your brief in the message.', 'dtf' ); ?></p>
                </div>

            </aside>

        </div><!-- .contact-wrap -->
    </div><!-- .site-wrap -->
</div><!-- #contact-page -->

<?php get_footer(); ?>
