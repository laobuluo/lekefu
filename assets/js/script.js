jQuery(document).ready(function($) {
    // 前端功能
    if ($('#lekefu-container').length) {
        // 延迟显示客服窗口
        setTimeout(function() {
            $('#lekefu-container').fadeIn();
        }, lekefuSettings.delay_time * 1000);

        // 关闭按钮点击事件
        $('.lekefu-close').on('click', function() {
            $('#lekefu-container').fadeOut();
        });
    }

    // 后台功能
    if ($('#lekefu_upload_image_button').length) {
        var mediaUploader;

        $('#lekefu_upload_image_button').on('click', function(e) {
            e.preventDefault();

            // 如果上传框已经存在，则打开它
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // 创建媒体上传框
            mediaUploader = wp.media({
                title: '选择客服二维码图片',
                button: {
                    text: '使用此图片'
                },
                multiple: false
            });

            // 当选择图片时
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#lekefu_qr_image').val(attachment.url);
                $('#lekefu_image_preview').html('<img src="' + attachment.url + '" style="max-width: 200px;">');
            });

            // 打开媒体上传框
            mediaUploader.open();
        });
    }
}); 