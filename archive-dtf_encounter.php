<?php get_header(); ?>
<?php $filter=isset($_GET['filter'])?sanitize_key($_GET['filter']):''; ?>
<div class="site-wrap">
<div class="content-sidebar">
<div class="content-main">
    <header class="archive-hdr">
        <h1 class="archive-title"><?php echo $filter==='disappeared'?esc_html__('Dishes that disappeared','dtf'):esc_html__('Food encounters','dtf'); ?></h1>
        <p class="archive-desc"><?php echo $filter==='disappeared'?esc_html__('Dishes I loved that no longer exist.','dtf'):esc_html__('Field notes from the table. Honest, dish-first.','dtf'); ?></p>
    </header>
    <div class="archive-filters">
        <a href="<?php echo esc_url(get_post_type_archive_link('dtf_encounter')); ?>" class="archive-filter-btn<?php echo !$filter?' active':''; ?>"><?php esc_html_e( 'All', 'dtf' ); ?></a>
        <a href="?filter=disappeared" class="archive-filter-btn<?php echo $filter==='disappeared'?' active':''; ?>"><?php esc_html_e( 'Disappeared', 'dtf' ); ?></a>
    </div>
    <?php if(have_posts()):?>
    <div class="post-grid">
        <?php while(have_posts()):the_post(); get_template_part('template-parts/encounter-card'); endwhile; ?>
    </div>
    <?php the_posts_pagination(['mid_size'=>2,'class'=>'pagination']);
    else: echo '<p style="padding:32px 0;color:var(--muted);">' . esc_html__( 'No encounters found.', 'dtf' ) . '</p>'; endif; ?>
</div>
<aside class="content-sidebar-col"><?php get_template_part('template-parts/sidebar'); ?></aside>
</div>
</div>
<?php get_footer(); ?>
