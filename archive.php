<?php get_header(); ?>
<div class="site-wrap">
<div class="content-sidebar">
<div class="content-main">
    <header class="archive-hdr">
        <?php
        $dtf_term_img = is_category() || is_tag() || is_tax() ? dtf_get_term_image( null, 'dtf-hero', [ 'class' => 'archive-term-img' ] ) : '';
        if ( $dtf_term_img ) echo $dtf_term_img;
        ?>
        <h1 class="archive-title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<p class="archive-desc">','</p>'); ?>
    </header>
    <?php if(have_posts()):?>
    <div class="post-grid" style="padding-top:24px;">
        <?php while(have_posts()):the_post(); get_template_part('template-parts/post-card'); endwhile; ?>
    </div>
    <?php the_posts_pagination(['mid_size'=>2,'class'=>'pagination']);
    else: echo '<p style="padding:32px 0;color:var(--muted);">' . esc_html__( 'Nothing found.', 'dtf' ) . '</p>'; endif; ?>
</div>
<aside class="content-sidebar-col"><?php get_template_part('template-parts/sidebar'); ?></aside>
</div>
</div>
<?php get_footer(); ?>
