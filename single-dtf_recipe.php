<?php get_header(); ?>
<?php while(have_posts()):the_post(); ?>
<div class="single-wrap">
    <?php echo dtf_render_recipe_card(get_the_ID()); ?>
    <div class="entry-content" style="margin-top:2em;"><?php the_content(); ?></div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
