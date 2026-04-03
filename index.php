<?php get_header(); ?>
<div class="site-wrap">
<div class="content-sidebar">
<div class="content-main">
    <div class="section-hdr"><span class="section-label"><?php esc_html_e( 'All posts', 'dtf' ); ?></span></div>
    <?php if(have_posts()):?>
    <div class="post-grid">
        <?php while(have_posts()):the_post(); get_template_part('template-parts/post-card'); endwhile; ?>
    </div>
    <?php the_posts_pagination(['mid_size'=>2,'class'=>'pagination']);
    else: echo '<p style="padding:32px 0;color:var(--muted);">' . esc_html__( 'Nothing here yet.', 'dtf' ) . '</p>'; endif; ?>
</div>
<aside class="content-sidebar-col"><?php get_template_part('template-parts/sidebar'); ?></aside>
</div>
</div>
<?php get_footer(); ?>
