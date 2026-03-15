<?php
/**
 * Plugin Name: LeKeFu
 * Plugin URI: https://www.laojiang.me/6215.html
 * Description: 一款简单易用的WordPress客服插件，支持自定义客服二维码图片和位置展示。（公众号：老蒋朋友圈）
 * Version: 1.0.0
 * Author: 老蒋和他的小伙伴
 * Author URI: https://www.laojiang.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lekefu  
 */

// 防止直接访问此文件
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('LEKEFU_VERSION', '1.0.0');
define('LEKEFU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LEKEFU_PLUGIN_URL', plugin_dir_url(__FILE__));

// 包含必要的文件
require_once LEKEFU_PLUGIN_DIR . 'includes/admin.php';
require_once LEKEFU_PLUGIN_DIR . 'includes/frontend.php';

// 激活插件时的钩子
register_activation_hook(__FILE__, 'lekefu_activate');
function lekefu_activate() {
    // 设置默认选项
    $default_options = array(
        'enabled' => '1',
        'qr_image' => '',
        'position' => 'right-bottom',
        'description' => '',
        'delay_time' => 3,
        'excluded_categories' => array(),
        'excluded_posts' => array()
    );
    
    add_option('lekefu_options', $default_options);
}

// 删除插件时的钩子
register_deactivation_hook(__FILE__, 'lekefu_deactivate');
function lekefu_deactivate() {
    // 停用插件时的操作
}

// 卸载插件时的钩子
register_uninstall_hook(__FILE__, 'lekefu_uninstall');
function lekefu_uninstall() {
    // 删除插件选项
    delete_option('lekefu_options');
}

// 加载插件文本域
add_action('plugins_loaded', 'lekefu_load_textdomain');
function lekefu_load_textdomain() {
    load_plugin_textdomain('lekefu', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// 加载后台资源
add_action('admin_enqueue_scripts', 'lekefu_admin_scripts');
function lekefu_admin_scripts($hook) {
    if ('settings_page_lekefu' !== $hook) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('lekefu-admin', LEKEFU_PLUGIN_URL . 'assets/css/style.css', array(), LEKEFU_VERSION);
    wp_enqueue_script('lekefu-admin', LEKEFU_PLUGIN_URL . 'assets/js/script.js', array('jquery'), LEKEFU_VERSION, true);
}

// 加载前端资源
add_action('wp_enqueue_scripts', 'lekefu_frontend_scripts');
function lekefu_frontend_scripts() {
    $options = get_option('lekefu_options');
    if (empty($options['enabled'])) {
        return;
    }
    
    wp_enqueue_style('lekefu-frontend', LEKEFU_PLUGIN_URL . 'assets/css/style.css', array(), LEKEFU_VERSION);
    wp_enqueue_script('lekefu-frontend', LEKEFU_PLUGIN_URL . 'assets/js/script.js', array('jquery'), LEKEFU_VERSION, true);
    
    // 传递设置到JavaScript
    wp_localize_script('lekefu-frontend', 'lekefuSettings', array(
        'delay_time' => isset($options['delay_time']) ? intval($options['delay_time']) : 3
    ));
} 