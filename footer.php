</div><!-- #content -->
<footer class="site-footer" role="contentinfo">
    <div class="site-wrap">
        <p class="footer-copy">&copy; <?php echo date('Y'); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a> &mdash; <?php esc_html_e( 'Honest food writing.', 'dtf' ); ?></p>
        <nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'dtf' ); ?>">
            <?php wp_nav_menu(['theme_location'=>'footer','container'=>false,'depth'=>1,'fallback_cb'=>function(){
                echo '<ul>';
                foreach([
                    home_url('/about/')         => __( 'About',   'dtf' ),
                    home_url('/contact/')        => __( 'Contact', 'dtf' ),
                    home_url('/privacy-policy/') => __( 'Privacy', 'dtf' ),
                ] as $url=>$label)
                    echo '<li><a href="'.esc_url($url).'">'.esc_html($label).'</a></li>';
                echo '</ul>';
            }]); ?>
        </nav>
    </div>
</footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
