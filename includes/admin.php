<?php
// 防止直接访问此文件
if (!defined('ABSPATH')) {
    exit;
}

// 添加管理菜单
add_action('admin_menu', 'lekefu_add_admin_menu');
function lekefu_add_admin_menu() {
    add_options_page(
        'LeKeFu设置', // 页面标题
        'LeKeFu插件', // 菜单标题
        'manage_options', // 权限
        'lekefu', // 菜单slug
        'lekefu_options_page' // 回调函数
    );
}

// 注册设置
add_action('admin_init', 'lekefu_register_settings');
function lekefu_register_settings() {
    register_setting('lekefu_options', 'lekefu_options', 'lekefu_validate_options');
    
    // 添加设置区块
    add_settings_section(
        'lekefu_main_section',
        '基本设置',
        'lekefu_main_section_callback',
        'lekefu'
    );
    
    // 添加设置字段
    add_settings_field(
        'lekefu_enabled',
        '启用插件',
        'lekefu_enabled_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_qr_image',
        '客服二维码图片',
        'lekefu_qr_image_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_position',
        '显示位置',
        'lekefu_position_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_description',
        '提示文字',
        'lekefu_description_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_delay_time',
        '显示延迟时间',
        'lekefu_delay_time_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_excluded_categories',
        '排除分类',
        'lekefu_excluded_categories_callback',
        'lekefu',
        'lekefu_main_section'
    );
    
    add_settings_field(
        'lekefu_excluded_posts',
        '排除文章',
        'lekefu_excluded_posts_callback',
        'lekefu',
        'lekefu_main_section'
    );
}

// 设置页面回调函数
function lekefu_options_page() {
    ?>
    <div class="wrap">
        <h1>插件设置</h1>
        <p>WordPress 主页客服二维码设置插件。<a href="https://www.laojiang.me/6215.html" target="_blank">插件介绍</a>（公众号：<span style="color: red;">老蒋朋友圈</span>）</p>

        <form method="post" action="options.php">
            <?php
            settings_fields('lekefu_options');
            do_settings_sections('lekefu');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 主设置区块描述
function lekefu_main_section_callback() {
    echo '<p>在这里，配置我们的客服插件的信息参数。</p>';
}

// 启用插件选项回调
function lekefu_enabled_callback() {
    $options = get_option('lekefu_options');
    $enabled = isset($options['enabled']) ? $options['enabled'] : '1';
    ?>
    <input type="checkbox" name="lekefu_options[enabled]" value="1" <?php checked('1', $enabled); ?>>
    <?php
}

// 二维码图片选项回调
function lekefu_qr_image_callback() {
    $options = get_option('lekefu_options');
    $image = isset($options['qr_image']) ? $options['qr_image'] : '';
    ?>
    <div class="lekefu-image-upload">
        <input type="hidden" name="lekefu_options[qr_image]" id="lekefu_qr_image" value="<?php echo esc_attr($image); ?>">
        <button type="button" class="button" id="lekefu_upload_image_button">选择图片</button>
        <div id="lekefu_image_preview">
            <?php if ($image): ?>
                <img src="<?php echo esc_url($image); ?>" style="max-width: 200px;">
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// 显示位置选项回调
function lekefu_position_callback() {
    $options = get_option('lekefu_options');
    $position = isset($options['position']) ? $options['position'] : 'right-bottom';
    ?>
    <select name="lekefu_options[position]">
        <option value="left-bottom" <?php selected($position, 'left-bottom'); ?>>左下角</option>
        <option value="center-bottom" <?php selected($position, 'center-bottom'); ?>>底部中间</option>
        <option value="center-center" <?php selected($position, 'center-center'); ?>>页面中间</option>
        <option value="right-bottom" <?php selected($position, 'right-bottom'); ?>>右下角</option>
    </select>
    <?php
}

// 描述文字选项回调
function lekefu_description_callback() {
    $options = get_option('lekefu_options');
    $description = isset($options['description']) ? $options['description'] : '';
    ?>
    <input type="text" name="lekefu_options[description]" value="<?php echo esc_attr($description); ?>" class="regular-text">
    <p class="description">可选，显示在二维码图片下方的提示文字</p>
    <?php
}

// 延迟时间选项回调
function lekefu_delay_time_callback() {
    $options = get_option('lekefu_options');
    $delay_time = isset($options['delay_time']) ? intval($options['delay_time']) : 3;
    ?>
    <input type="number" name="lekefu_options[delay_time]" value="<?php echo esc_attr($delay_time); ?>" min="0" step="1">
    <p class="description">访客进入页面后多少秒显示客服图片（0表示立即显示）</p>
    <?php
}

// 排除分类选项回调
function lekefu_excluded_categories_callback() {
    $options = get_option('lekefu_options');
    $excluded_categories = isset($options['excluded_categories']) ? implode(',', (array)$options['excluded_categories']) : '';
    ?>
    <input type="text" name="lekefu_options[excluded_categories]" value="<?php echo esc_attr($excluded_categories); ?>" class="regular-text">
    <p class="description">输入要排除的分类ID，多个ID请用英文逗号分隔，例如：1,2,3</p>
    <?php
}

// 排除文章选项回调
function lekefu_excluded_posts_callback() {
    $options = get_option('lekefu_options');
    $excluded_posts = isset($options['excluded_posts']) ? implode(',', (array)$options['excluded_posts']) : '';
    ?>
    <input type="text" name="lekefu_options[excluded_posts]" value="<?php echo esc_attr($excluded_posts); ?>" class="regular-text">
    <p class="description">输入要排除的文章ID，多个ID请用英文逗号分隔，例如：1,2,3</p>
    <?php
}

// 验证选项
function lekefu_validate_options($input) {
    $output = array();
    
    $output['enabled'] = isset($input['enabled']) ? '1' : '0';
    $output['qr_image'] = esc_url_raw($input['qr_image']);
    $output['position'] = sanitize_text_field($input['position']);
    $output['description'] = sanitize_text_field($input['description']);
    $output['delay_time'] = intval($input['delay_time']);
    
    // 处理分类ID
    $excluded_categories = sanitize_text_field($input['excluded_categories']);
    $output['excluded_categories'] = array_filter(array_map('intval', explode(',', $excluded_categories)));
    
    // 处理文章ID
    $excluded_posts = sanitize_text_field($input['excluded_posts']);
    $output['excluded_posts'] = array_filter(array_map('intval', explode(',', $excluded_posts)));
    
    return $output;
} 