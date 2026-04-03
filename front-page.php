<?php get_header(); ?>
<?php
$sticky = get_option('sticky_posts');
$hero_q = new WP_Query($sticky ? ['post__in'=>$sticky,'posts_per_page'=>1,'ignore_sticky_posts'=>1] : ['posts_per_page'=>1]);
?>
<div class="site-wrap">

<?php if($hero_q->have_posts()):$hero_q->the_post(); ?>
<section class="featured-hero">
    <div class="featured-hero-text">
        <?php $cats=get_the_category(); ?>
        <p class="featured-eyebrow"><?php echo $cats?esc_html($cats[0]->name):esc_html__('Featured','dtf'); ?><?php $loc=get_post_meta(get_the_ID(),'dtf_enc_city',true);if($loc) echo ' &middot; '.esc_html($loc); ?></p>
        <h2 class="featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php if(has_excerpt()):?><p class="featured-excerpt"><?php the_excerpt(); ?></p><?php endif; ?>
        <div class="featured-meta">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
            <span class="sep"></span><span><?php echo dtf_reading_time(get_the_ID()); ?></span>
        </div>
        <a href="<?php the_permalink(); ?>" class="featured-cta"><?php esc_html_e( 'Read the full story', 'dtf' ); ?> &rarr;</a>
    </div>
    <div class="featured-hero-image">
        <?php if(has_post_thumbnail()):?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('dtf-hero'); ?></a>
        <?php else:?><a href="<?php the_permalink(); ?>"><div class="no-img"><?php if($loc):?><span class="location-pill"><?php echo esc_html($loc);?></span><?php endif;?></div></a><?php endif;?>
    </div>
</section>
<?php wp_reset_postdata();endif; ?>

<div class="content-sidebar">
<div class="content-main">

<?php $posts_q=new WP_Query(['posts_per_page'=>6,'ignore_sticky_posts'=>1,'post__not_in'=>get_option('sticky_posts')]); ?>
<?php if($posts_q->have_posts()):?>
<div class="section-hdr">
    <span class="section-label"><?php esc_html_e( 'Latest posts', 'dtf' ); ?></span>
    <a class="section-more" href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><?php esc_html_e( 'View all', 'dtf' ); ?> &rarr;</a>
</div>
<div class="post-grid">
    <?php while($posts_q->have_posts()):$posts_q->the_post(); get_template_part('template-parts/post-card'); endwhile; wp_reset_postdata(); ?>
</div>
<?php endif; ?>

<?php $enc_q=new WP_Query(['post_type'=>'dtf_encounter','posts_per_page'=>3]); ?>
<?php if($enc_q->have_posts()):?>
<div class="section-hdr">
    <span class="section-label"><?php esc_html_e( 'Recent food encounters', 'dtf' ); ?></span>
    <a class="section-more" href="<?php echo esc_url(home_url('/food-encounters/')); ?>"><?php esc_html_e( 'All encounters', 'dtf' ); ?> &rarr;</a>
</div>
<div class="post-grid">
    <?php while($enc_q->have_posts()):$enc_q->the_post(); get_template_part('template-parts/encounter-card'); endwhile; wp_reset_postdata(); ?>
</div>
<?php endif; ?>

</div>
<aside class="content-sidebar-col">
    <?php get_template_part('template-parts/sidebar'); ?>
</aside>
</div>

</div><!-- .site-wrap -->
<?php get_footer(); ?>
