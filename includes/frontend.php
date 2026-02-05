<?php
// 防止直接访问此文件
if (!defined('ABSPATH')) {
    exit;
}

// 添加前端显示
add_action('wp_footer', 'lekefu_display_frontend');
function lekefu_display_frontend() {
    // 获取插件选项
    $options = get_option('lekefu_options');
    
    // 检查是否启用插件
    if (empty($options['enabled'])) {
        return;
    }
    
    // 检查是否在排除的分类或文章中
    if (is_single() || is_page()) {
        global $post;
        
        // 检查文章是否被排除
        if (isset($options['excluded_posts']) && in_array($post->ID, (array)$options['excluded_posts'])) {
            return;
        }
        
        // 检查分类是否被排除
        $post_categories = wp_get_post_categories($post->ID);
        $excluded_categories = isset($options['excluded_categories']) ? (array)$options['excluded_categories'] : array();
        if (!empty(array_intersect($post_categories, $excluded_categories))) {
            return;
        }
    }
    
    // 获取位置类
    $position_class = isset($options['position']) ? esc_attr($options['position']) : 'right-bottom';
    
    // 获取图片URL
    $image_url = isset($options['qr_image']) ? esc_url($options['qr_image']) : '';
    if (empty($image_url)) {
        return;
    }
    
    // 获取描述文字
    $description = isset($options['description']) ? esc_html($options['description']) : '';
    
    // 输出HTML
    ?>
    <div id="lekefu-container" class="lekefu-container <?php echo $position_class; ?>" style="display: none;">
        <div class="lekefu-content">
            <span class="lekefu-close">&times;</span>
            <img src="<?php echo $image_url; ?>" alt="客服二维码" class="lekefu-qr-image">
            <?php if (!empty($description)): ?>
                <p class="lekefu-description"><?php echo $description; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
} 