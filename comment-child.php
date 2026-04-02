<?php 
  foreach($commentIndexs[$parentComment->comment_ID] as $child) {
  // var_dump($child);
  if($child -> comment_approved == '1'){
?>
  <div class="child comment-card" id="comment-<?=$child -> comment_ID?>">
    <div class="user-avatar">
      <div class="avatar">
        <?php
          if($child -> user_id == 1) {
            the_avatar_author();
          } else {
            echo get_avatar($child -> comment_author_email);
          }
        ?>
      </div>
      <?php
        if(is_user_logged_in() && current_user_can('level_7')) { ?>
          <div class="change-comment">
            <a href="<?=bloginfo('url').'/wp-admin/comment.php?action=editcomment&c='.$child -> comment_ID?>">编辑</a>
          </div>
        <? }
      ?>
    </div>
    <div class="comment-info">
      <div class="info">
        <div class="user">
          <?php
            $user_name = get_comment_author($child -> comment_ID);
            $reply_user_name = get_comment_author($parentComment -> comment_ID);
            //var_dump($parentComment -> user_id)
          ?>
          <div class="user-name">
          <h4 class="<?=$child -> user_id ? 'master-name' : 'comment-user-name' ?>"><?php echo $child -> comment_author_url ? '<a href="'.esc_url($child -> comment_author_url).'">'.esc_html($user_name).'</a>' : esc_html($user_name) ?></h4>
            <?=$child -> user_id == 1 ? '<span class="master">博主</span>' : ''?>
          </div>
          <p><i class="iconfont icon-clock"></i><?=date('Y 年 m 月 d 日 H:i', strtotime($child -> comment_date))?><span class="user-ip" ip="<?=esc_attr(ip_encryption($child -> comment_author_IP))?>"><i class="iconfont icon-map-pin"></i>获取中...</span></p>
        </div>
        <div class="reply-btn"><a href="?replytocom=<?=esc_attr($child -> comment_ID); ?>#respond" id="<?=esc_attr($child -> comment_ID); ?>">回复</a></div>
      </div>
      <div class="comment-content">
        <p><span class="at">@<?=esc_html($reply_user_name)?></span><?=esc_html($child->comment_content)?></p>
      </div>
    </div>
  </div>
  <?php
  $oldParentComment = $parentComment; 
  $parentComment = $child;
  if (isset($commentIndexs[$parentComment->comment_ID])) {
    require 'comment-child.php';
  }
  $parentComment = $oldParentComment;
  ?>
<?php }
  } 
?>