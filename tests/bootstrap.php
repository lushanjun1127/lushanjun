<?php
/**
 * PHPUnit bootstrap file
 * 
 * 用于初始化 WordPress 测试环境
 * 
 * @package 陆山君の主题
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
  $_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward all phpunit output to the terminal
remove_filter( 'update_feedback', 'esc_html' );

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
  echo "ERROR: Could not find wp-tests-config.php or wordpress-tests-lib.\n";
  exit( 1 );
}

// Load correct versions of WP functions
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the theme being tested.
 */
function _manually_load_theme() {
  require dirname( __DIR__ ) . '/functions.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_theme' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";

// Load theme constants
if ( ! defined( 'THEME_VERSION' ) ) {
  define( 'THEME_VERSION', '1.0.1' );
}
if ( ! defined( 'THEME_TEXT_DOMAIN' ) ) {
  define( 'THEME_TEXT_DOMAIN', 'lushanjun-theme' );
}
