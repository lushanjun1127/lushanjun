<?php
/**
 * 测试模板标签函数
 * 
 * @package 陆山君の主题
 */

class TemplateTagsTest extends WP_UnitTestCase {
  
  /**
   * 测试 get_post_cover 函数（有特色图的情况）
   */
  function test_get_post_cover_with_featured_image() {
    // 创建测试文章
    $post_id = $this->factory->post->create();
    
    // 设置特色图
    $attachment_id = $this->factory->attachment->create_upload_object(
      dirname( __FILE__ ) . '/data/test-image.jpg',
      $post_id
    );
    set_post_thumbnail( $post_id, $attachment_id );
    
    global $post;
    $post = get_post( $post_id );
    setup_postdata( $post );
    
    // 测试
    $cover = get_post_cover();
    
    $this->assertNotEmpty( $cover );
    $this->assertStringContainsString( 'test-image', $cover );
    
    wp_reset_postdata();
  }
  
  /**
   * 测试 get_reading_time 函数
   */
  function test_get_reading_time() {
    global $post;
    
    // 创建短文章（约 100 字）
    $post_id = $this->factory->post->create(array(
      'post_content' => str_repeat('这是一个测试句子。', 20)
    ));
    
    $post = get_post( $post_id );
    setup_postdata( $post );
    
    $reading_time = get_reading_time();
    
    $this->assertNotEmpty( $reading_time );
    $this->assertStringContainsString( '分钟', $reading_time );
    
    wp_reset_postdata();
  }
  
  /**
   * 测试 format_relative_time 函数
   */
  function test_format_relative_time() {
    // 测试"刚刚"
    $now = current_time( 'timestamp' );
    $relative = format_relative_time( $now );
    $this->assertEquals( '刚刚', $relative );
    
    // 测试"X 分钟前"
    $five_min_ago = $now - ( 5 * MINUTE_IN_SECONDS );
    $relative = format_relative_time( $five_min_ago );
    $this->assertStringContainsString( '分钟前', $relative );
    
    // 测试"X 小时前"
    $two_hour_ago = $now - ( 2 * HOUR_IN_SECONDS );
    $relative = format_relative_time( $two_hour_ago );
    $this->assertStringContainsString( '小时前', $relative );
    
    // 测试"X 天前"
    $three_days_ago = $now - ( 3 * DAY_IN_SECONDS );
    $relative = format_relative_time( $three_days_ago );
    $this->assertStringContainsString( '天前', $relative );
  }
  
  /**
   * 测试 get_comment_level_icon 函数
   */
  function test_get_comment_level_icon_newbie() {
    // 创建测试用户（0-4 条评论 - 新粉丝）
    $user_id = $this->factory->user->create(array(
      'user_email' => 'newbie@example.com'
    ));
    
    // 创建 2 条评论
    for ( $i = 0; $i < 2; $i++ ) {
      $this->factory->comment->create(array(
        'comment_author_email' => 'newbie@example.com',
        'comment_approved' => 1
      ));
    }
    
    $icon = get_comment_level_icon( 'newbie@example.com' );
    
    $this->assertStringContainsString( 'level-1', $icon );
    $this->assertStringContainsString( '新粉丝', $icon );
  }
  
  function test_get_comment_level_icon_active() {
    // 创建测试用户（20-49 条评论 - 活跃粉丝）
    $user_id = $this->factory->user->create(array(
      'user_email' => 'active@example.com'
    ));
    
    // 创建 25 条评论
    for ( $i = 0; $i < 25; $i++ ) {
      $this->factory->comment->create(array(
        'comment_author_email' => 'active@example.com',
        'comment_approved' => 1
      ));
    }
    
    $icon = get_comment_level_icon( 'active@example.com' );
    
    $this->assertStringContainsString( 'level-3', $icon );
    $this->assertStringContainsString( '活跃粉丝', $icon );
  }
  
  function test_get_comment_level_icon_veteran() {
    // 创建测试用户（≥100 条评论 - 资深粉丝）
    $user_id = $this->factory->user->create(array(
      'user_email' => 'veteran@example.com'
    ));
    
    // 创建 100 条评论
    for ( $i = 0; $i < 100; $i++ ) {
      $this->factory->comment->create(array(
        'comment_author_email' => 'veteran@example.com',
        'comment_approved' => 1
      ));
    }
    
    $icon = get_comment_level_icon( 'veteran@example.com' );
    
    $this->assertStringContainsString( 'level-5', $icon );
    $this->assertStringContainsString( '资深粉丝', $icon );
  }
  
  /**
   * 测试 first_post_cover 函数（有图片）
   */
  function test_first_post_cover_with_image() {
    $content = '<p>这是一些文本</p><img src="http://example.com/image.jpg" alt="测试" /><p>更多文本</p>';
    
    $cover = first_post_cover( $content );
    
    $this->assertEquals( 'http://example.com/image.jpg', $cover );
  }
  
  /**
   * 测试 first_post_cover 函数（无图片）
   */
  function test_first_post_cover_without_image() {
    $content = '<p>这是一些纯文本内容，没有图片</p>';
    
    $cover = first_post_cover( $content );
    
    $this->assertNull( $cover );
  }
  
  /**
   * 测试 get_breadcrumb 函数（首页）
   */
  function test_get_breadcrumb_home() {
    $this->go_to( home_url( '/' ) );
    
    $breadcrumb = get_breadcrumb();
    
    $this->assertStringContainsString( '首页', $breadcrumb );
  }
  
  /**
   * 测试 get_breadcrumb 函数（文章页）
   */
  function test_get_breadcrumb_single() {
    $post_id = $this->factory->post->create(array(
      'post_title' => '测试文章标题'
    ));
    
    $this->go_to( get_permalink( $post_id ) );
    
    $breadcrumb = get_breadcrumb();
    
    $this->assertStringContainsString( '首页', $breadcrumb );
    $this->assertStringContainsString( '测试文章标题', $breadcrumb );
  }
  
  /**
   * 测试 get_breadcrumb 函数（搜索页）
   */
  function test_get_breadcrumb_search() {
    $this->go_to( home_url( '/?s=测试关键词' ) );
    
    $breadcrumb = get_breadcrumb();
    
    $this->assertStringContainsString( '搜索', $breadcrumb );
    $this->assertStringContainsString( '测试关键词', $breadcrumb );
  }
  
  /**
   * 测试 get_breadcrumb 函数（404 页）
   */
  function test_get_breadcrumb_404() {
    $this->go_to( home_url( '/non-existent-page' ) );
    
    $breadcrumb = get_breadcrumb();
    
    $this->assertStringContainsString( '页面未找到', $breadcrumb );
  }
}
