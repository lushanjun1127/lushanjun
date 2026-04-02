<?php 
// 禁止直接访问文件
if (!defined('ABSPATH')) {
    exit;
}

get_head(); 
?>
  <div class="container home">
    <?php get_header(); ?>
    <main>
      <?php get_nav(); ?>
      <div class="content">
        <article>
          <h2>首页 <span>Home.</span></h2>
          <?php 
            // 验证并过滤用户输入
            $recommend_show = get_option("iemo_recommend_show");
            if ($recommend_show === 'regular') {
              require 'inc/home-recommend-regular.php';
            } elseif ($recommend_show === 'swiper') {
              require 'inc/home-recommend-swiper.php';
            }
          ?>
          <div class="bottom">
            <div class="recommend-bar">
              <ul>
                <li><a class="active"><i class="iconfont icon-bookmark"></i> 最新发布</a></li>
                <li><a><i class="iconfont icon-flag"></i> 为你推荐</a></li>
                <div class="slider"></div>
              </ul>
            </div>
            <?php 
            // 防止直接访问 inc 目录中的文件
            if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
                exit;
            }
            require 'inc/home-new.php'; 
            require 'inc/home-sticky.php'; 
            ?>
          </div>
        </article>
        <?php get_aside(); ?>
      </div>
    </main>
  </div>
<?php get_foot(); ?>