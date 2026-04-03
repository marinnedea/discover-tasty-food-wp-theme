<?php get_header(); ?>
<?php while(have_posts()):the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('single-wrap'); ?>>
    <h1 class="single-title"><?php the_title(); ?></h1>
    <?php if(has_post_thumbnail()):the_post_thumbnail('dtf-hero',['class'=>'single-featured-img']);endif; ?>
    <div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
