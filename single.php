<?php get_head(); ?>
  <div class="container single">
    <?php get_header(); ?>
    <main>
      <?php get_nav(); ?>
      <div class="content">
        <article>
          <div class="post-cover">
            <div class="cover">
              <?php 
                if (has_post_thumbnail()) { ?>
                <img class="thumbnail_loading" src="<?php echo esc_url(fileUri()) ?>/assets/images/loading.gif" alt="" loading="lazy" width="800" height="450">
                <?php the_post_thumbnail('large', array('loading' => 'lazy', 'decoding' => 'async', 'fetchpriority' => 'high')); ?>
                <img class="color-thief" src="" alt="" crossorigin="anonymous" style="display:none" loading="lazy">

                <script>
                  const imgELem1 = $('.single .post-cover .cover img:eq(1)');
                  if (imgELem1[0].complete) {
                    $('.single .post-cover .cover img:first').remove();
                    <?php
                      if(get_option("iemo_page_animation")) { ?>
                        imgELem1.css('animation','FadeIn-<?php echo esc_js(get_option("iemo_page_animation")); ?> .5s forwards');
                      <?php }
                    ?>
                  } else {
                    imgELem1.css('opacity','0');
                    imgELem1.on('load',function (){
                      $('.single .post-cover .cover img:first').remove();
                      <?php
                        if(get_option("iemo_page_animation")) { ?>
                          $(this).css('animation','FadeIn-<?php echo esc_js(get_option("iemo_page_animation")); ?> .5s forwards');
                        <?php }
                      ?>
                      $(this).css('opacity','1');
                    });
                  }
                  $('.single .post-cover .cover .color-thief').attr('src',imgELem1.attr('src'));
                </script>
                
              <?php } else {
                $imgUrl = first_post_cover(get_the_content());
                if($imgUrl){ ?>  
                  <img class="thumbnail_loading" src="<?php echo fileUri() ?>/assets/images/loading.gif" alt="" loading="lazy" width="800" height="450">
                  <img src="<?php echo esc_url($imgUrl); ?>" alt="" loading="lazy" decoding="async">
                  <img class="color-thief" src="<?php echo esc_url($imgUrl); ?>" alt="" crossorigin="anonymous" style="display:none" loading="lazy">

                  <script>
                    const imgELem2 = $('.single .post-cover .cover img:eq(1)');
                    if (imgELem2[0].complete) {
                      $('.single .post-cover .cover img:first').remove();
                      <?php
                        if(get_option("iemo_page_animation")) { ?>
                          imgELem2.css('animation','FadeIn-<?php echo esc_js(get_option("iemo_page_animation")); ?> .5s forwards');
                        <?php }
                      ?>
                    } else {
                      imgELem2.css('opacity','0');   
                        imgELem2.on('load',function (){
                        $('.single .post-cover .cover img:first').remove();
                        <?php
                          if(get_option("iemo_page_animation")) { ?>
                            $(this).css('animation','FadeIn-<?php echo esc_js(get_option("iemo_page_animation")); ?> .5s forwards');
                          <?php }
                        ?>
                        $(this).css('opacity','1');
                      });
                    }
                  </script>

                <?php }else{ ?>  
                  <img class="get_img_url" src="<?php echo fileUri() ?>/assets/images/loading.gif" alt="" loading="lazy" width="800" height="450">
                  <img class="color-thief" src="" alt="" crossorigin="anonymous" style="display:none" loading="lazy">
                <?php } ?>
              <?php } ?>
            </div>

            <div class="shortcuts">
              <?php
                if(get_edit_post_link()) { ?>
                  <a href="<?php echo esc_url(get_edit_post_link()); ?>"><i class="iconfont icon-edit"></i>编辑</a>
                <?php }
              ?>

              <?php
                if(get_option("iemo_comments") == 'true') {
                  if(comments_open()) { ?>
                    <a class="to-comment"><i class="iconfont icon-message-circle"></i>参与讨论</a>
                  <?php }
                }
              ?>
            </div>

            <div class="post-info">
              <div class="title">
                <h2 title="<?php echo esc_attr(get_the_title()); ?>"><?php the_title(); ?></h2>
              </div>
              <div class="more">
                <div class="time">
                  <i class="iconfont icon-clock"></i>
                  <span><?php echo get_the_date(); ?> <?php the_time(); ?></span>
                </div>
                <div class="cate">
                  <?php echo the_category(' ', 'single') ?>
                </div>
                <?php if(get_the_tag_list()){ ?><div class="tag"><?php echo get_the_tag_list('',' ',''); ?></div><?php } ?>
              </div>
            </div>
          </div>
          <div class="post-content">
            <?php the_content(); ?>
          </div>
          <?php
            if(get_option("iemo_comments") == 'true') {
              require 'comments.php';
            }
          ?>
        </article>
        <?php get_aside(); ?>
      </div>
    </main>
  </div>
<?php get_foot(); ?>
<?php require 'inc/single-color.php'; ?>

<script>
  let postImg = document.querySelectorAll('.post-content img');
  if(postImg) {
    let postImgUrl = [];
    $(postImg).each(function(i) {
      postImgUrl[i] = $('<a data-fancybox="gallery"></a>').attr('href',$(postImg[i]).attr('src'));
      postImg[i].parentNode.replaceChild($(postImgUrl[i])[0], postImg[i]);
      $(postImg[i]).appendTo($(postImgUrl[i])[0]);
    })
  }
</script>

<script src="<?php echo esc_url(fileUri()); ?>/assets/static/fancybox/fancybox.umd.js" async></script>
<script src="<?php echo esc_url(fileUri()); ?>/assets/static/highlight/highlight.min.js" async></script>
<script>
  // 延迟初始化代码高亮和标题处理
  document.addEventListener('DOMContentLoaded', function() {
    // 代码高亮初始化
    if (typeof hljs !== 'undefined') {
      hljs.highlightAll();
    }
    
    // 为文章标题添加包装 span
    document.querySelectorAll('.post-content h1, .post-content h2, .post-content h3').forEach(function(el) {
      const span = document.createElement('span');
      span.textContent = el.textContent;
      el.innerHTML = '';
      el.appendChild(span);
    });
  });
</script>