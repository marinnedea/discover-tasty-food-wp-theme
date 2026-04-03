<?php
$dish      = get_post_meta(get_the_ID(),'dtf_enc_dish',true)     ?: get_the_title();
$city      = get_post_meta(get_the_ID(),'dtf_enc_city',true);
$cuisine   = get_post_meta(get_the_ID(),'dtf_enc_cuisine',true);
$rating    = get_post_meta(get_the_ID(),'dtf_enc_rating',true);
$available = get_post_meta(get_the_ID(),'dtf_enc_available',true);
$memorable = get_post_meta(get_the_ID(),'dtf_enc_memorable',true);
?>
<article id="post-<?php the_ID();?>" class="post-card">
    <div class="post-card-thumb">
        <?php if(has_post_thumbnail()):?><a href="<?php the_permalink();?>"><?php the_post_thumbnail('dtf-card');?></a>
        <?php else:?><a href="<?php the_permalink();?>"><div class="no-thumb" style="background:linear-gradient(145deg,var(--bg-warm),var(--bg-card));"></div></a><?php endif;?>
        <span class="tag-pill tag-encounter"><?php esc_html_e( 'Encounter', 'dtf' ); ?></span>
        <?php if($available==='no'):?><span class="card-location-pill" style="background:var(--rust-l);color:var(--rust-d);"><?php esc_html_e( 'Disappeared', 'dtf' ); ?></span>
        <?php elseif($city):?><span class="card-location-pill"><?php echo esc_html($city);?></span><?php endif;?>
    </div>
    <div class="post-card-body">
        <h3 class="post-card-title"><a href="<?php the_permalink();?>"><?php echo esc_html($dish);?></a></h3>
        <?php if($memorable):?><p class="post-card-excerpt"><?php echo esc_html(wp_trim_words($memorable,16,'…'));?></p><?php endif;?>
        <div class="post-card-meta">
            <?php if($rating):?><span style="color:var(--rust);font-size:11px;letter-spacing:1px;"><?php echo dtf_stars($rating);?></span><span class="sep"></span><?php endif;?>
            <?php if($cuisine):?><span><?php echo esc_html($cuisine);?></span><span class="sep"></span><?php endif;?>
            <time datetime="<?php echo esc_attr(get_the_date('c'));?>"><?php echo get_the_date('M j');?></time>
        </div>
    </div>
</article>
