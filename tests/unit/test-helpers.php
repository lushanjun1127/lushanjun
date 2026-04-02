<?php
/**
 * 测试助手函数
 * 
 * @package 陆山君の主题
 */

class HelpersTest extends WP_UnitTestCase {
  
  /**
   * 测试 fileUri 函数
   */
  function test_fileUri() {
    $uri = fileUri();
    
    $this->assertNotEmpty( $uri );
    $this->assertStringContainsString( 'http', $uri );
    $this->assertStringContainsString( 'wp-content', $uri );
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
   * 测试 excerpt_length 过滤器
   */
  function test_excerpt_length() {
    // WordPress 默认摘要长度通常是 55
    $default_length = apply_filters( 'excerpt_length', 55 );
    
    // 我们的主题应该返回 300
    $this->assertEquals( 300, $default_length );
  }
  
  /**
   * 测试 has_submenu 函数
   */
  function test_has_submenu() {
    $this->assertTrue( has_submenu( array( 'item1', 'item2' ) ) );
    $this->assertFalse( has_submenu( array() ) );
    $this->assertFalse( has_submenu( null ) );
    $this->assertFalse( has_submenu( false ) );
  }
  
  /**
   * 测试 default_post_cover 函数
   */
  function test_default_post_cover() {
    $cover = default_post_cover();
    
    $this->assertNotEmpty( $cover );
    $this->assertStringContainsString( 'http', $cover );
    $this->assertStringContainsString( '.jpg', $cover );
  }
  
  /**
   * 测试 show_wp_title 函数输出
   */
  function test_show_wp_title_output() {
    // 创建一个测试文章并访问它
    $post_id = $this->factory->post->create(array(
      'post_title' => '测试文章'
    ));
    
    $this->go_to( get_permalink( $post_id ) );
    
    // 捕获输出
    ob_start();
    show_wp_title();
    $output = ob_get_clean();
    
    $this->assertNotEmpty( $output );
    $this->assertStringContainsString( '测试文章', $output );
  }
}
