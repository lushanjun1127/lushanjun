<?php

/**
 * Author: 陆山君
 * E-mail: lushanjun@proton.me
 * 
 * 主题核心功能文件
 * 包含主题配置、模板标签、助手函数等
 */

// ==================== 常量定义 ====================

// 主题版本号（用于资源版本控制和国际化）
define('THEME_VERSION', '1.0.1');
define('THEME_TEXT_DOMAIN', 'lushanjun-theme');

// 主题目录路径和 URI 常量
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// 缓存控制常量
define('THEME_CACHE_TIME', 3600); // 浏览器缓存时间（秒）
define('THEME_TRANSIENT_EXPIRE', DAY_IN_SECONDS); // 临时数据过期时间

// ==================== 后台管理菜单 ====================

/**
 * 注册主题选项菜单
 */
add_action('admin_menu', 'add_theme_options_menu');
function add_theme_options_menu() {
  add_menu_page(
    __('陆山君の主题设置', THEME_TEXT_DOMAIN),
    __('陆山君の主题设置', THEME_TEXT_DOMAIN),
    'edit_themes',
    'iemo_option',
    'iemo_option_admin'
  );
}

/**
 * 加载主题选项页面
 */
function iemo_option_admin() {
  require get_template_directory()."/admin/option.php";
}

// ==================== 模板加载函数 ====================

/**
 * 加载头部模板
 */
function get_head() {
  require 'inc/head.php';
}

/**
 * 加载底部模板
 */
function get_foot() {
  require 'inc/foot.php';
}

/**
 * 加载导航栏模板
 */
function get_nav() {
  require 'inc/nav.php';
}

/**
 * 加载侧边栏模板
 */
function get_aside() {
  require 'inc/aside.php';
}

// ==================== 助手函数 ====================

/**
 * 获取主题静态资源 URI
 * 
 * @return string 主题目录 URI
 */
function fileUri() {
  return get_template_directory_uri();
}

// 加载模板标签和助手函数
require get_template_directory() . '/inc/template-tags.php';


// ==================== 主题配置和过滤器 ====================

/**
 * 显示网站标题（用于 SEO）
 */
function show_wp_title() {
  global $page, $paged;
  wp_title( '&#8211;', true, 'right' );
  bloginfo( 'name' );
  $site_description = get_bloginfo( 'description', 'display' );
  if($site_description && (is_home() || is_front_page()))
    echo ' &#8211; ' . esc_html($site_description);
  if ( $paged >= 2 || $page >= 2 )
    echo ' &#8211; ' . sprintf(__('第%s页', THEME_TEXT_DOMAIN), max( $paged, $page ));
}

/**
 * 设置摘要长度
 * 
 * @param int $length 原始长度
 * @return int 新长度
 */
function excerpt_length($length) {
  return 300;
}
add_filter('excerpt_length', 'excerpt_length');

/**
 * 开启文章特色图支持
 */
if(function_exists('add_theme_support')) {
  add_theme_support('post-thumbnails', array('post', 'page'));
}

/**
 * 获取默认文章封面
 * 
 * @return string 封面图片 URL
 */
function default_post_cover() {
  if(get_option("iemo_cover_post")) {
    return esc_url_raw(get_option("iemo_cover_post"));
  } else {
    return esc_url_raw(fileUri().'/assets/images/random/cover-post-'.rand(1,2).'.jpg');
  }
}


/**
 * 获取随机图片 API
 * 包含完整的错误处理和nonce验证
 */
add_action('wp_ajax_random_img', 'random_img');
add_action('wp_ajax_nopriv_random_img', 'random_img');

function random_img() {
  // 验证 nonce（允许未登录用户访问，但需要验证 nonce）
  if (!check_ajax_referer('random_img_nonce', 'nonce', false)) {
    wp_send_json_error(array(
      'code' => 403,
      'msg' => 'Security check failed'
    ), 403);
  }
  
  header('Content-Type: application/json');
  
  try {
    $cover_url = get_option('iemo_cover_post');
    if (empty($cover_url)) {
      throw new \Exception('Cover URL not configured');
    }
    
    // 创建安全的流上下文
    $context = stream_context_create(array(
      'ssl' => array(
        'verify_peer' => true,
        'verify_peer_name' => true,
      ),
      'http' => array(
        'timeout' => 5,
        'ignore_errors' => true,
      ),
    ));
    
    $headers = @get_headers(esc_url_raw($cover_url), true, $context);
    
    if (!$headers || !isset($headers[0]) || strpos($headers[0], '200') === false) {
      throw new \Exception('Failed to fetch image headers');
    }
    
    $location = isset($headers['Location']) ? $headers['Location'] : 
                (isset($headers['location']) ? $headers['location'] : '');
    
    if (empty($location)) {
      throw new \Exception('No redirect location found');
    }
    
    wp_send_json_success(array(
      'url' => esc_url_raw($location),
    ));
    
  } catch (\Throwable $e) {
    // 记录错误日志（可选）
    error_log('Random image API error: ' . $e->getMessage());
    
    wp_send_json_error(array(
      'code' => 500,
      'msg' => 'Get image failed',
      'debug' => defined('WP_DEBUG') && WP_DEBUG ? $e->getMessage() : null
    ), 500);
  }
}


// 获取随机图API重定向地址
// function img_redirect_url($url, $ua=0) {
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//   $httpheader[] = "Accept:*/*";
//   $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
//   $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
//   $httpheader[] = "Connection:close";
//   curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
//   curl_setopt($ch, CURLOPT_HEADER, true);
//   if ($ua) {
//     curl_setopt($ch, CURLOPT_USERAGENT, $ua);
//   } else {
//     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
//   }
//   curl_setopt($ch, CURLOPT_NOBODY, 1);
//   curl_setopt($ch, CURLOPT_ENCODING, "gzip");
//   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//   $ret = curl_exec($ch);
//   curl_close($ch);
//   preg_match("/Location: (.*?)\r\n/iU",$ret,$location);
//   return $location[1];
// }


//获取用户信息
function get_user_role($id) {
  $user = new WP_User($id);
  return $user->data;
}


// 头像
require_once 'plugins/simple-local-avatars/simple-local-avatars.php';

if ( ! function_exists( 'dr_filter_get_avatar' ) ) {
  function dr_filter_get_avatar( $avatar ) {
      // 新 Gravatar 头像源，可自行修改
      $new_gravatar_sever = 'cravatar.cn';

      $sources = array(
          'www.gravatar.com/avatar/',
          '0.gravatar.com/avatar/',
          '1.gravatar.com/avatar/',
          '2.gravatar.com/avatar/',
          'secure.gravatar.com/avatar/',
          'cn.gravatar.com/avatar/'
      );

      return str_replace( $sources, $new_gravatar_sever.'/avatar/', $avatar );
  }
  add_filter( 'get_avatar', 'dr_filter_get_avatar' );
}


// 添加媒体外链
require_once 'plugins/external-media-without-import/external-media-without-import.php';


// 个人背景图片
// function user_profile( $userProfile ) {
//   $userProfile['bgi'] = '背景图片';
//   return $userProfile;
// }
// add_filter('user_contactmethods','user_profile');


// 注册菜单
register_nav_menus( array(        
  'menu' => '左侧主菜单',
  'social' => '侧边栏主页社交链接',
  'menu_sidebar' => '侧边栏附页菜单(需开启附页)',
) );

// 返回函数
function menu_fallback() {
  if(is_admin()) {
    if(is_home()) {
      echo '<div class="menu">
        <ul class="menu">
          <li class="current-menu-item"><a href="' . esc_url(home_url()) . '" title="首页"><i class="iconfont icon-home"></i></a></li>
          <li><a href="' . esc_url(home_url('/wp-admin/nav-menus.php')) . '" title="前往设置菜单"><i class="iconfont icon-settings"></i></a></li>
        </ul>
      </div>';
    } else {
      echo '<div class="menu">
        <ul class="menu">
          <li><a href="' . esc_url(home_url()) . '" title="首页"><i class="iconfont icon-home"></i></a></li>
          <li><a href="' . esc_url(home_url('/wp-admin/nav-menus.php')) . '" title="前往设置菜单"><i class="iconfont icon-settings"></i></a></li>
        </ul>
      </div>';
    }
  } else {
    if(is_home()) {
      echo '<div class="menu">
        <ul class="menu">
          <li class="current-menu-item"><a href="' . esc_url(home_url()) . '" title="首页"><i class="iconfont icon-home"></i></a></li>
        </ul>
      </div>';
    } else {
      echo '<div class="menu">
        <ul class="menu">
          <li><a href="' . esc_url(home_url()) . '" title="首页"><i class="iconfont icon-home"></i></a></li>
        </ul>
      </div>';
    }
  }
}


// 说说
function note_init() { 
  $labels = [ 
    'name' => '说说',
    'singular_name' => '说说', 
    'all_items' => '所有说说',
    'add_new' => '发表说说', 
    'add_new_item' => '撰写新说说',
    'edit_item' => '编辑说说', 
    'new_item' => '新说说', 
    'view_item' => '查看说说', 
    'search_items' => '搜索说说', 
    'not_found' => '暂无说说', 
    'not_found_in_trash' => '没有已遗弃的说说', 
    'parent_item_colon' => '',
    'menu_name' => '说说'
  ]; 
  $args = [ 
    'labels' => $labels, 
    'public' => true, 
    'publicly_queryable' => true, 
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true, 
    'rewrite' => true, 
    'capability_type' => 'post', 
    'has_archive' => true, 
    'hierarchical' => false, 
    'menu_position' => null, 
    'supports' => array('title','editor','author','comments'),
  ]; 
  register_post_type('note', $args); 
}
add_action('init', 'note_init');


// 链接
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
function friend_links($output){
  if(!is_home()|| is_paged()) {
    $output = '';
  }
  return $output;
}
add_filter('wp_list_bookmarks','friend_links');


// 归档页
function iemo_archives_list() {
  if( !$output = get_option('iemo_archives_list') ) {
    $the_query = new WP_Query( 'posts_per_page=-1&ignore_sticky_posts=1&showposts=-1' );
    $year=0; $mon=0; $i=0; $j=0;
    while ( $the_query->have_posts() ) : $the_query->the_post();
      $year_tmp = get_the_time('Y');
      $mon_tmp = get_the_time('m');
      if ($year != $year_tmp || $mon != $mon_tmp) {
        $year = $year_tmp;
        $mon = $mon_tmp;
        $output .= '<h3>' . esc_html($year) .'年'. esc_html($mon) .'月</h3>';
      }
      $output .= '<li><span>〔'. esc_html(get_the_time('d 日')) .'〕</span><a href="' . esc_url(get_permalink()) .'">'. esc_html(get_the_title()) .'</a></li>';
    endwhile;
    wp_reset_postdata();
    update_option('iemo_archives_list', $output);
  }
  echo $output;
}

// 清除归档缓存时同时清理其他临时选项
function iemo_clear_cache() {
  delete_option('iemo_archives_list');
  // 可以添加更多缓存清理逻辑
}
add_action('save_post', 'iemo_clear_cache');


// 自动添加页面模板
function ashu_add_page($title,$slug,$page_template=''){   
  $allPages = get_pages();
  $exists = false;   
  foreach( $allPages as $page ){   
    if( strtolower( $page->post_name ) == strtolower( $slug ) ){   
      $exists = true;   
    }   
  }  

  if( $exists == false ) {   
    $new_page_id = wp_insert_post(   
      array(   
        'post_title' => sanitize_text_field($title),   
        'post_type'     => 'page',   
        'post_name'  => sanitize_title($slug),   
        'comment_status' => 'closed',   
        'ping_status' => 'closed',   
        'post_content' => '',   
        'post_status' => 'publish',   
        'post_author' => 1,   
        'menu_order' => 0   
      )   
    );   
    if($new_page_id && $page_template!=''){   
      update_post_meta($new_page_id, '_wp_page_template',  $page_template);   
    }   
  }   
}

function ashu_add_pages() {   
	global $pagenow;
	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){
		ashu_add_page('分类','category','template/template-cate.php');
		ashu_add_page('标签','tag','template/template-tag.php');
		ashu_add_page('归档','archive','template/template-archive.php');
    ashu_add_page('说说','note','archive-note.php');
    ashu_add_page('友人帐','link','template/template-link.php');
	}   
}   

add_action( 'load-themes.php', 'ashu_add_pages' ); 


// 搜索排除页面
add_filter('pre_get_posts', function($wp_query){
  if($wp_query->is_search){
    $wp_query->set('post_type', 'post');
  }
  return $wp_query;
});


// 登出账户后重定向
// add_action('wp_logout','redirect_after_logout');
// function redirect_after_logout(){
//   wp_safe_redirect(home_url());
//   exit();
// }


// 评论区同步昵称
add_filter('get_comment_author', function ($author, $comment_ID, $comment) {
  if (!$comment->user_id) {
    return $author;
  }
  $newuser = get_userdata($comment->user_id);
  return $newuser->display_name ?: $author;
}, 10, 3);


// 修正评论用户IP不准确问题
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $list = explode(',', sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']));
  $_SERVER['REMOTE_ADDR'] = $list[0];
}


//禁止引号半角/全角切换
add_filter('run_wptexturize', '__return_false');

// 性能优化：Gzip 压缩（仅在服务器支持时启用）
if(!function_exists('enable_gzip_compression')) {
  function enable_gzip_compression() {
    // 检查是否已启用 gzip
    if (ini_get('zlib.output_compression')) {
      return;
    }
    
    // 检查服务器是否支持 gzip
    if (!function_exists('ob_gzhandler')) {
      return;
    }
    
    // 检查 Accept-Encoding 头
    $encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? sanitize_text_field($_SERVER['HTTP_ACCEPT_ENCODING']) : '';
    if (strpos($encoding, 'gzip') !== false && !headers_sent() && ob_get_level() === 0) {
      ob_start('ob_gzhandler');
    }
  }
  add_action('init', 'enable_gzip_compression', 1);
}

// 性能优化：添加资源预加载
if(!function_exists('add_resource_hints')) {
  function add_resource_hints($hints, $relation_type) {
    if ('preconnect' === $relation_type) {
      $hints[] = 'https://cravatar.cn';
      $hints[] = 'https://i.pinimg.com';
    }
    return $hints;
  }
  add_filter('wp_resource_hints', 'add_resource_hints', 10, 2);
}

// 性能优化：延迟加载非关键 CSS（保留原有功能）
if(!function_exists('defer_non_critical_css')) {
  function defer_non_critical_css($html, $handle) {
    // 只对非关键 CSS 进行延迟加载
    if (false !== strpos($handle, 'fancybox') || false !== strpos($handle, 'highlight')) {
      $html = str_replace('media=\'all\'', 'media=\'print\' onload="this.media=\'all\'"', $html);
    }
    return $html;
  }
  add_filter('style_loader_tag', 'defer_non_critical_css', 10, 2);
}

// 安全性增强：移除WordPress版本信息
if(!function_exists('remove_version_info')) {
  function remove_version_info() {
    return '';
  }
  add_filter('the_generator', 'remove_version_info');
}

// 安全性增强：限制 XML-RPC 访问（保留必要功能）
if(!function_exists('disable_xmlrpc_for_specific_ua')) {
  function disable_xmlrpc_for_specific_ua($methods) {
    // 禁用危险的 XML-RPC 方法
    unset($methods['system.multicall']);
    unset($methods['system.listMethods']);
    return $methods;
  }
  add_filter('xmlrpc_methods', 'disable_xmlrpc_for_specific_ua');
}

// 安全性增强：移除不必要的 WordPress 头部信息
if(!function_exists('remove_header_info')) {
  function remove_header_info() {
    // 移除不影响功能的头部信息
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'adjacent_posts_rel_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('template_redirect', 'wp_shortlink_header', 11);
  }
  add_action('after_setup_theme', 'remove_header_info', 100);
}

// 性能优化：延迟加载Google Fonts等外部资源
if(!function_exists('async_fonts_loading')) {
  function async_fonts_loading($html, $handle) {
    if (false === strpos($handle, 'googleapis') && false === strpos($handle, 'gstatic')) {
      return $html;
    }
    return str_replace("'>", "' async>", $html);
  }
  add_filter('style_loader_tag', 'async_fonts_loading', 10, 2);
}

// 性能优化：优化浏览器缓存策略
if(!function_exists('add_header_cache')) {
  function add_header_cache() {
    // 管理员、Feed、搜索、404 页面不缓存
    if (is_admin() || is_feed() || is_search() || is_404()) {
      return;
    }
    
    // 设置更合理的缓存时间
    header('Cache-Control: public, max-age=' . THEME_CACHE_TIME . ', must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + THEME_CACHE_TIME) . ' GMT');
  }
  add_action('send_headers', 'add_header_cache', 20);
}

// 性能优化：使用本地 jQuery 或稳定的 CDN
if(!function_exists('replace_jquery')) {
  function replace_jquery() {
    if (!is_admin()) {
      wp_deregister_script('jquery');
      
      // 优先使用本地 jQuery，避免 CDN 不稳定
      $jquery_url = fileUri() . '/assets/js/jquery.min.js';
      
      // 如果本地文件不存在，使用可靠的 CDN
      if (!file_exists(THEME_DIR . '/assets/js/jquery.min.js')) {
        $jquery_url = 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js';
      }
      
      wp_register_script(
        'jquery',
        $jquery_url,
        array(),
        '3.7.1',
        true // 在页脚加载
      );
      wp_enqueue_script('jquery');
    }
  }
  add_action('wp_enqueue_scripts', 'replace_jquery', 1);
}

// 性能优化：添加WebP图片支持检测
if(!function_exists('add_webp_support')) {
  function add_webp_support() {
    echo '<script>
      function supportsWebP() {
        var canvas = document.createElement("canvas");
        canvas.width = 1;
        canvas.height = 1;
        var ctx = canvas.getContext("2d");
        if (!ctx) return false;
        ctx.fillStyle = "#000000";
        ctx.fillRect(0, 0, 1, 1);
        var imageData = ctx.getImageData(0, 0, 1, 1);
        return imageData.data[3] === 255;
      }
      if (supportsWebP()) {
        document.documentElement.classList.add("webp");
      } else {
        document.documentElement.classList.add("no-webp");
      }
    </script>';
  }
  add_action('wp_head', 'add_webp_support');
}

// 修复评论功能中的潜在安全问题
if(!function_exists('fix_comment_security')){
  function fix_comment_security($comment_data) {
    $comment_data['comment_content'] = esc_html($comment_data['comment_content']);
    return $comment_data;
  }
  add_filter('preprocess_comment', 'fix_comment_security');
}

// 修复搜索功能的安全问题
if(!function_exists('fix_search_security')){
  function fix_search_security($query) {
    if (!is_admin() && $query->is_main_query()) {
      if ($query->is_search) {
        $query->set('s', esc_attr($query->get('s')));
      }
    }
  }
  add_action('pre_get_posts', 'fix_search_security');
}