<?php get_header(); ?>
<?php while(have_posts()):the_post(); ?>
<div class="single-wrap"><?php dtf_render_encounter(get_the_ID()); ?></div>
<?php endwhile; ?>
<?php get_footer(); ?>
