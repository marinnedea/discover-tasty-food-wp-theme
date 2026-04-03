<article id="post-<?php the_ID(); ?>" class="post-card">
    <div class="post-card-thumb">
        <?php if(has_post_thumbnail()):?><a href="<?php the_permalink();?>"><?php the_post_thumbnail('dtf-card');?></a>
        <?php else:?><a href="<?php the_permalink();?>"><div class="no-thumb"></div></a><?php endif;?>
        <?php echo dtf_post_type_label(get_the_ID()); ?>
        <?php $loc=get_post_meta(get_the_ID(),'dtf_enc_city',true);if($loc):?><span class="card-location-pill"><?php echo esc_html($loc);?></span><?php endif;?>
    </div>
    <div class="post-card-body">
        <h3 class="post-card-title"><a href="<?php the_permalink();?>"><?php echo esc_html( get_the_title() );?></a></h3>
        <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(),18,'…');?></p>
        <div class="post-card-meta">
            <time datetime="<?php echo esc_attr(get_the_date('c'));?>"><?php echo get_the_date('M j');?></time>
            <span class="sep"></span><span><?php echo dtf_reading_time(get_the_ID());?></span>
        </div>
        <a href="<?php the_permalink();?>" class="read-more-btn"><?php esc_html_e( 'Read more', 'dtf' ); ?> &rarr;</a>
    </div>
</article>
