<?php

/**
 * 模板标签和助手函数
 * 
 * 包含主题常用的模板标签、显示函数和工具方法
 * 
 * @package 陆山君の主题
 * @since 1.0.1
 */

// ==================== 文章相关函数 ====================

/**
 * 获取文章第一张图片作为封面
 * 
 * @param string|false $content 文章内容，false 则自动获取
 * @return string|null 封面图片 URL 或 null
 */
function first_post_cover($content = false) {
  if ($content === false) {
    $content = get_the_content();
  }
  
  if (empty($content)) {
    return null;
  }
  
  // 匹配图片标签（优化正则性能）
  if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
    return esc_url_raw($matches[1]);
  }
  
  return null;
}

/**
 * 获取文章封面（优先特色图，其次第一张图，最后默认图）
 * 
 * @return string 封面图片 URL
 */
function get_post_cover() {
  global $post;
  
  // 1. 尝试获取特色图
  $thumbnail_src = get_the_post_thumbnail_url($post->ID, 'full');
  if($thumbnail_src) {
    return esc_url_raw($thumbnail_src);
  }
  
  // 2. 尝试获取第一张图
  $first_img = first_post_cover(false);
  if($first_img) {
    return $first_img;
  }
  
  // 3. 使用默认封面
  return default_post_cover();
}

/**
 * 获取文章阅读时间（估算）
 * 
 * @param int|null $post_id 文章 ID
 * @return string 阅读时间描述
 */
function get_reading_time($post_id = null) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  
  if (!$post_id) {
    return __('1 分钟以内', THEME_TEXT_DOMAIN);
  }
  
  $content = get_post_field('post_content', $post_id);
  if (empty($content)) {
    return __('1 分钟以内', THEME_TEXT_DOMAIN);
  }
  
  // 使用 WordPress 内置函数计算字数（更准确）
  $word_count = str_word_count(strip_tags($content));
  $reading_speed = apply_filters('theme_reading_speed', 200); // 每分钟 200 字，可通过过滤器调整
  
  $reading_time = ceil($word_count / $reading_speed);
  
  if ($reading_time < 1) {
    return __('不到 1 分钟', THEME_TEXT_DOMAIN);
  } elseif ($reading_time < 60) {
    /* translators: %d: 分钟数 */
    return sprintf(_n('%d 分钟', '%d 分钟', $reading_time, THEME_TEXT_DOMAIN), $reading_time);
  } else {
    $hours = floor($reading_time / 60);
    $minutes = $reading_time % 60;
    /* translators: %1$d: 小时数，%2$d: 分钟数 */
    return sprintf(__('%1$d 小时 %2$d 分钟', THEME_TEXT_DOMAIN), $hours, $minutes);
  }
}

// ==================== 用户相关函数 ====================

/**
 * 获取作者头像
 * 
 * @param int|object|string $user 用户 ID、用户对象或邮箱
 * @param int $size 头像大小
 * @return string 头像 HTML
 */
function the_avatar_author($user = null, $size = 80) {
  if(!$user) {
    global $post;
    $user = $post->post_author;
  }
  
  return get_avatar($user, $size, '', '', array('class' => 'avatar'));
}

/**
 * 获取评论者等级图标
 * 
 * @param string $email 评论者邮箱
 * @return string 等级图标 HTML
 */
function get_comment_level_icon($email) {
  // 根据评论数量判断等级（带缓存）
  $cache_key = 'comment_level_' . md5($email);
  $comment_count = get_transient($cache_key);
  
  if ($comment_count === false) {
    $comment_count = get_comments(array(
      'author_email' => $email,
      'count' => true,
      'status' => 'approve'
    ));
    set_transient($cache_key, $comment_count, THEME_TRANSIENT_EXPIRE);
  }
  
  $level_class = '';
  $level_text = '';
  
  // 使用常量定义等级阈值
  if ($comment_count >= 100) {
    $level_class = 'level-5';
    $level_text = __('资深粉丝', THEME_TEXT_DOMAIN);
  } elseif ($comment_count >= 50) {
    $level_class = 'level-4';
    $level_text = __('忠实粉丝', THEME_TEXT_DOMAIN);
  } elseif ($comment_count >= 20) {
    $level_class = 'level-3';
    $level_text = __('活跃粉丝', THEME_TEXT_DOMAIN);
  } elseif ($comment_count >= 5) {
    $level_class = 'level-2';
    $level_text = __('普通粉丝', THEME_TEXT_DOMAIN);
  } else {
    $level_class = 'level-1';
    $level_text = __('新粉丝', THEME_TEXT_DOMAIN);
  }
  
  return sprintf(
    '<span class="comment-level %s" title="%s">%s</span>',
    esc_attr($level_class),
    esc_attr($level_text),
    esc_html($level_text)
  );
}

// ==================== 日期和时间格式化 ====================

/**
 * 格式化日期为相对时间
 * 
 * @param string|int $date 日期字符串或时间戳
 * @return string 相对时间描述
 */
function format_relative_time($date) {
  if (is_numeric($date)) {
    $timestamp = intval($date);
  } else {
    $timestamp = strtotime($date);
  }
  
  if (!$timestamp) {
    return __('未知时间', THEME_TEXT_DOMAIN);
  }
  
  $diff = current_time('timestamp') - $timestamp;
  
  if ($diff < 0) {
    return __('刚刚', THEME_TEXT_DOMAIN);
  } elseif ($diff < MINUTE_IN_SECONDS) {
    return __('刚刚', THEME_TEXT_DOMAIN);
  } elseif ($diff < HOUR_IN_SECONDS) {
    /* translators: %d: 分钟前 */
    return sprintf(_n('%d 分钟前', '%d 分钟前', floor($diff / MINUTE_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / MINUTE_IN_SECONDS));
  } elseif ($diff < DAY_IN_SECONDS) {
    /* translators: %d: 小时前 */
    return sprintf(_n('%d 小时前', '%d 小时前', floor($diff / HOUR_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / HOUR_IN_SECONDS));
  } elseif ($diff < WEEK_IN_SECONDS) {
    /* translators: %d: 天前 */
    return sprintf(_n('%d 天前', '%d 天前', floor($diff / DAY_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / DAY_IN_SECONDS));
  } elseif ($diff < MONTH_IN_SECONDS) {
    /* translators: %d: 周前 */
    return sprintf(_n('%d 周前', '%d 周前', floor($diff / WEEK_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / WEEK_IN_SECONDS));
  } elseif ($diff < YEAR_IN_SECONDS) {
    /* translators: %d: 月前 */
    return sprintf(_n('%d 月前', '%d 月前', floor($diff / MONTH_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / MONTH_IN_SECONDS));
  } else {
    /* translators: %d: 年前 */
    return sprintf(_n('%d 年前', '%d 年前', floor($diff / YEAR_IN_SECONDS), THEME_TEXT_DOMAIN), floor($diff / YEAR_IN_SECONDS));
  }
}

/**
 * 获取格式化的日期显示
 * 
 * @param string|int $date 日期
 * @param string $format 格式类型：relative, short, long
 * @return string 格式化后的日期
 */
function get_formatted_date($date, $format = 'relative') {
  switch($format) {
    case 'short':
      return date_i18n(get_option('date_format'), strtotime($date));
    
    case 'long':
      return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($date));
    
    case 'relative':
    default:
      return format_relative_time($date);
  }
}

// ==================== 导航和菜单 ====================

/**
 * 检查是否有子菜单
 * 
 * @param array $children 子菜单数组
 * @return bool 是否有子菜单
 */
function has_submenu($children) {
  return !empty($children) && is_array($children);
}

/**
 * 生成面包屑导航
 * 
 * @return string 面包屑 HTML
 */
function get_breadcrumb() {
  $breadcrumb = array();
  
  // 首页
  $breadcrumb[] = sprintf(
    '<a href="%s">%s</a>',
    esc_url(home_url()),
    __('首页', THEME_TEXT_DOMAIN)
  );
  
  // 分类页
  if(is_category()) {
    $category = get_queried_object();
    $breadcrumb[] = esc_html(single_cat_title('', false));
  }
  
  // 标签页
  elseif(is_tag()) {
    $breadcrumb[] = single_tag_title('', false);
  }
  
  // 文章页
  elseif(is_single()) {
    $categories = get_the_category();
    if($categories) {
      $category = $categories[0];
      $breadcrumb[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url(get_category_link($category->term_id)),
        esc_html($category->name)
      );
    }
    $breadcrumb[] = get_the_title();
  }
  
  // 页面
  elseif(is_page()) {
    $breadcrumb[] = get_the_title();
  }
  
  // 归档页
  elseif(is_archive()) {
    $breadcrumb[] = get_the_archive_title();
  }
  
  // 搜索页
  elseif(is_search()) {
    /* translators: %s: 搜索关键词 */
    $breadcrumb[] = sprintf(__('搜索：%s', THEME_TEXT_DOMAIN), get_search_query());
  }
  
  // 404 页
  elseif(is_404()) {
    $breadcrumb[] = __('页面未找到', THEME_TEXT_DOMAIN);
  }
  
  return '<div class="breadcrumb">' . implode(' > ', $breadcrumb) . '</div>';
}

// ==================== 分页和加载更多 ====================

/**
 * 生成分页链接
 * 
 * @param WP_Query $query 查询对象
 * @return string 分页 HTML
 */
function get_pagination($query = null) {
  if(!$query) {
    global $wp_query;
    $query = $wp_query;
  }
  
  $big = 999999999;
  
  $pagination = paginate_links(array(
    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
    'format' => '?paged=%#%',
    'current' => max(1, get_query_var('paged')),
    'total' => $query->max_num_pages,
    'prev_text' => __('&laquo; 上一页', THEME_TEXT_DOMAIN),
    'next_text' => __('下一页 &raquo;', THEME_TEXT_DOMAIN),
    'type' => 'list'
  ));
  
  if($pagination) {
    return '<nav class="pagination">' . $pagination . '</nav>';
  }
  
  return '';
}

/**
 * 检查是否还有更多文章
 * 
 * @param WP_Query $query 查询对象
 * @return bool 是否还有更多
 */
function has_more_posts($query = null) {
  if(!$query) {
    global $wp_query;
    $query = $wp_query;
  }
  
  return $query->max_num_pages > $query->get('paged');
}

// ==================== 社交分享 ====================

/**
 * 生成社交分享链接
 * 
 * @param string $platform 平台名称：wechat, weibo, qq
 * @param string $title 分享标题
 * @param string $url 分享链接
 * @return string 分享链接 HTML
 */
function get_social_share_link($platform, $title = '', $url = '') {
  if(!$url) {
    $url = get_permalink();
  }
  if(!$title) {
    $title = get_the_title();
  }
  
  $icon_class = '';
  $share_url = '';
  $title_attr = '';
  
  switch($platform) {
    case 'wechat':
      $icon_class = 'icon-wechat';
      $share_url = $url;
      break;
    
    case 'weibo':
      $icon_class = 'icon-weibo';
      $share_url = 'http://service.weibo.com/share/share.php?title=' . urlencode($title) . '&url=' . urlencode($url);
      break;
    
    case 'qq':
      $icon_class = 'icon-qq';
      $share_url = 'http://connect.qq.com/widget/shareqq/index.html?url=' . urlencode($url) . '&title=' . urlencode($title);
      break;
  }
  
  return sprintf(
    '<a href="%s" class="social-share %s" target="_blank" rel="noopener noreferrer" title="%s">%s</a>',
    esc_url($share_url),
    esc_attr($icon_class),
    esc_attr($title),
    '<i class="iconfont '.esc_attr($icon_class).'"></i>'
  );
}
