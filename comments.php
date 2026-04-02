<?php
  if(comments_open()) {
    $comments = get_comments(); ?>

    <link rel="stylesheet" href="<?php echo fileUri(); ?>/assets/css/comments.css?v=<?=THEME_VERSION?>">
      
    <div class="comments" id="<?=get_the_ID()?>">
    <?php //var_dump(get_comments()); ?>
    <?php //var_dump(get_comment_author('29')); ?>
      
      <div class="take-part-in-comment">
        <p>参与讨论</p>
        <p>(Participate in the discussion)</p>
      </div>
      <div class="response" id="response">
        <?php //var_dump(wp_get_current_commenter()); ?>
        <?php require 'comment-response.php'; ?>
      </div>

      <?php
        // IP 匿名化处理（提高安全性）
        function ip_encryption(string $ip) {
          if(strlen($ip) <= 15) {
            // 使用 WordPress 内置的 IP 匿名化函数进行部分隐藏
            $anonymized_ip = wp_privacy_anonymize_ip($ip);
            // 返回简化的地区信息（这里仅作演示，实际可接入地理位置 API）
            return substr(md5($anonymized_ip), 0, 8);
          } else {
            return false;
          }
        }
      ?>

      <?php
        $comment_count = get_comment_count(get_the_ID())['approved'];
      ?>
      <div class="comments-body">
        <?php
          if($comment_count >= 1) { ?>
            <h3>发现<?=$comment_count?>条评论</h3>
            <div class="comments-item">
              <ul>
                <?php 
                $commentIndexs = [];
                foreach ($comments as $item){
                  if (!isset($commentIndexs[$item->comment_parent])) {
                    $commentIndexs[$item->comment_parent] = [];
                  }
                  $commentIndexs[$item->comment_parent][$item->comment_ID] = $item;
                  // var_dump($item->comment_parent.'='.$item->comment_content);
                }
                // var_dump($commentIndexs);
                foreach($commentIndexs['0'] as $value) {
                  if($value -> comment_post_ID == get_the_ID() && $value -> comment_parent == '0' && $value -> comment_approved == '1') { //var_dump($value -> comment_ID);?>
                    <li>
                      <div class="parent comment-card" id="comment-<?=$value -> comment_ID?>">
                        <div class="user-avatar">
                          <div class="avatar">
                            <?php
                              if($value -> user_id == 1) {
                                the_avatar_author();
                              } else {
                                echo get_avatar($value -> comment_author_email);
                              }
                            ?>
                          </div>
                          <?php
                            if(is_user_logged_in() && current_user_can('level_7')) { ?>
                              <div class="change-comment">
                                <a href="<?=bloginfo('url').'/wp-admin/comment.php?action=editcomment&c='.$value -> comment_ID?>">编辑</a>
                              </div>
                            <? }
                          ?>
                        </div>
                        <div class="comment-info">
                          <div class="info">
                            <div class="user">
                              <?php
                                $user_name = get_comment_author($value -> comment_ID);
                                //var_dump($user_name);
                                //var_dump(ip_encryption($value -> comment_author_IP));
                              ?>
                              <div class="user-name">
                                <h4 class="<?=$value -> user_id ? 'master-name' : 'comment-user-name' ?>"><?php echo $value -> comment_author_url ? '<a href="'.esc_url($value -> comment_author_url).'">'.esc_html($user_name).'</a>' : esc_html($user_name) ?></h4>
                                <?=$value -> user_id == 1 ? '<span class="master">博主</span>' : ''?>
                              </div>
                              <p><i class="iconfont icon-clock"></i><?=date('Y 年 m 月 d 日 H:i', strtotime($value -> comment_date)); ?><span class="user-ip" ip="<?=esc_attr(ip_encryption($value -> comment_author_IP))?>"><i class="iconfont icon-map-pin"></i>获取中...</span></p>
                            </div>
                            <div class="reply-btn"><a href="?replytocom=<?=esc_attr($value -> comment_ID); ?>#respond" id="<?=esc_attr($value -> comment_ID); ?>">回复</a></div>
                          </div>
                          <div class="comment-content">
                            <p><?=esc_html($value -> comment_content)?></p>
                          </div>
                        </div>
                      </div>
                        <?php 
                          $parentComment = $value;
                          // var_dump($parentId);
                          if (isset($commentIndexs[$parentComment->comment_ID])) {
                            require 'comment-child.php'; 
                          }
                        ?>
                    </li>
                  <?php }
                } ?>
              </ul>
            </div>
          <? } else { ?>
            <h3 class="no-comment-title">没有发现评论</h3>
            <div class="no-comments">
              <div class="no-comment-img">
                <img src="<?=fileUri(); ?>/assets/images/no-comment.png" alt="">
                <p>暂无评论</p>
              </div>
            </div>
          <?php }
        ?>
        
      </div>
    </div>  
    <script src="<?php echo fileUri(); ?>/assets/js/comments.js?v=<?=THEME_VERSION?>"></script>
<?php } ?>
</```