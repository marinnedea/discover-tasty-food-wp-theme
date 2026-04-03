<?php
$rev_q=new WP_Query(['post_type'=>'dtf_encounter','posts_per_page'=>4,'meta_key'=>'dtf_enc_rating','orderby'=>'meta_value_num','order'=>'DESC']);
if($rev_q->have_posts()):?>
<div class="sb-section">
    <span class="sb-label sb-label-rust"><?php esc_html_e( 'Top encounters', 'dtf' ); ?></span>
    <?php while($rev_q->have_posts()):$rev_q->the_post();
        $rating=get_post_meta(get_the_ID(),'dtf_enc_rating',true);
        $city=get_post_meta(get_the_ID(),'dtf_enc_city',true);
        $dish=get_post_meta(get_the_ID(),'dtf_enc_dish',true)?:get_the_title();?>
    <div class="sb-review">
        <div><div class="sb-rname"><a href="<?php the_permalink();?>"><?php echo esc_html($dish);?></a></div><?php if($city):?><div class="sb-rloc"><?php echo esc_html($city);?></div><?php endif;?></div>
        <?php if($rating):?><span class="sb-stars"><?php echo dtf_stars($rating);?></span><?php endif;?>
    </div>
    <?php endwhile;wp_reset_postdata();?>
</div>
<?php endif;
$gone_q=new WP_Query(['post_type'=>'dtf_encounter','posts_per_page'=>4,'meta_query'=>[['key'=>'dtf_enc_available','value'=>'no']]]);
if($gone_q->have_posts()):?>
<div class="sb-section">
    <span class="sb-label sb-label-rust"><?php esc_html_e( 'Dishes that disappeared', 'dtf' ); ?></span>
    <?php while($gone_q->have_posts()):$gone_q->the_post();
        $dish=get_post_meta(get_the_ID(),'dtf_enc_dish',true)?:get_the_title();
        $city=get_post_meta(get_the_ID(),'dtf_enc_city',true);?>
    <div class="sb-post">
        <div class="sb-dot sb-dot-rust"></div>
        <div><div class="sb-ptitle"><a href="<?php the_permalink();?>"><?php echo esc_html($dish);?></a><?php if($city):?><span style="color:var(--muted);"> &middot; <?php echo esc_html($city);?></span><?php endif;?></div></div>
    </div>
    <?php endwhile;wp_reset_postdata();?>
</div>
<?php endif;
$side_q=new WP_Query(['posts_per_page'=>4,'ignore_sticky_posts'=>1]);
if($side_q->have_posts()):?>
<div class="sb-section">
    <span class="sb-label"><?php esc_html_e( 'Latest posts', 'dtf' ); ?></span>
    <?php while($side_q->have_posts()):$side_q->the_post();?>
    <div class="sb-post">
        <div class="sb-dot"></div>
        <div><div class="sb-ptitle"><a href="<?php the_permalink();?>"><?php echo esc_html( get_the_title() );?></a></div><div class="sb-pmeta"><?php echo esc_html( get_the_date('M j') );?> &middot; <?php echo esc_html( dtf_reading_time( get_the_ID() ) );?></div></div>
    </div>
    <?php endwhile;wp_reset_postdata();?>
</div>
<?php endif;
dynamic_sidebar('sidebar-main');?>
