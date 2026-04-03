<?php get_header(); ?>
<?php while(have_posts()):the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('single-wrap'); ?>>
    <?php $cats=get_the_category(); ?>
    <p class="single-eyebrow"><?php if($cats):?><a href="<?php echo esc_url(get_category_link($cats[0]->term_id));?>"><?php echo esc_html($cats[0]->name);?></a><?php endif;?></p>
    <h1 class="single-title"><?php the_title(); ?></h1>
    <div class="single-meta">
        <time datetime="<?php echo esc_attr(get_the_date('c'));?>"><?php echo get_the_date();?></time>
        <span class="sep"></span><span><?php echo dtf_reading_time(get_the_ID());?></span>
    </div>
    <?php if(has_post_thumbnail()):the_post_thumbnail('dtf-hero',['class'=>'single-featured-img']);endif; ?>
    <div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
