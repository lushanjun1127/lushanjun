<div class="post-part new active">
  <ul>
    <?php 
      $sticky = get_option( 'sticky_posts' );
      $args = array(
      	'ignore_sticky_posts' => 1,
        'post_status' => 'publish',
        'posts_per_page' => get_option('posts_per_page')
      );
      $new_query = new WP_Query($args);
      if ($new_query->have_posts()) : while ($new_query->have_posts()) : $new_query->the_post(); 
    ?> 
      <li>
        <div class="left">
          <a class="cover" href="<?php the_permalink(); ?>">
            <?php
              if (has_post_thumbnail()) {
                the_post_thumbnail('large', array('loading' => 'lazy'));
              } else { ?>
                <img src="<?php if(first_post_cover(get_the_content())){echo first_post_cover(get_the_content());}else{echo default_post_cover();} ?>?<?=the_ID()?>" alt="" loading="lazy">
              <?php }
            ?>
          </a>
          <div class="cate-view">
            <?php echo the_category(' <span>/</span> '); ?>
          </div>
        </div>
        <div class="right">
          <div class="text">
            <div class="title">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </div>
            <p><?php the_excerpt(); ?></p>
          </div>
          <div class="post-info">
            <div class="time">
              <i class="iconfont icon-clock"></i>
              <span><?php echo get_the_date(); ?></span>
            </div>
            <div class="read-more">
              <a href="<?php the_permalink(); ?>">
                <span>阅读更多</span>
                <i class="iconfont icon-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </li>
    <?php 
      endwhile; else: endif;
      wp_reset_postdata();
    ?>
  </ul>
  <?php require 'ajax/ajax-home.php'; ?>
</div>