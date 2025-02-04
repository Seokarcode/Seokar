<?php
// Add meta box to the post edit screen
function seokar_add_meta_box() {
    add_meta_box(
        'seokar_post_checker',
        'چک لیست پیش از انتشار پست',
        'seokar_render_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'seokar_add_meta_box');

// Render the meta box content
function seokar_render_meta_box($post) {
    ?>
    <div id="seokar-checklist">
        <table>
            <tr>
                <td id="seokar-check-h1">وجود حداکثر یک تگ H1</td>
                <td><span id="seokar-h1-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-h2">وجود حداقل پنج تگ H2</td>
                <td><span id="seokar-h2-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-h3">وجود حداقل 7 تگ H3</td>
                <td><span id="seokar-h3-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-h4">وجود حداقل 5 تگ H4</td>
                <td><span id="seokar-h4-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-img">وجود حداقل 4 تصویر در محتوا</td>
                <td><span id="seokar-img-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-alt">وجود alt تصاویر</td>
                <td><span id="seokar-alt-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-ext-link">وجود 2 لینک خارجی در محتوا</td>
                <td><span id="seokar-ext-link-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-int-link">وجود 3 لینک داخلی در محتوا</td>
                <td><span id="seokar-int-link-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-table">وجود جدول در محتوا</td>
                <td><span id="seokar-table-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-bold">وجود متن بلد در محتوا</td>
                <td><span id="seokar-bold-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-video">وجود یک ویدیو در محتوا</td>
                <td><span id="seokar-video-status"></span></td>
            </tr>
            <tr>
                <td id="seokar-check-word-count">متن کلی محتوا حداقل 800 کلمه باشد</td>
                <td><span id="seokar-word-count-status"></span></td>
            </tr>
        </table>
        <p class="seokar-validation-message" style="color: red; display: none;">این پست هنوز واجد الشرایط منتشر شدن نیست. لطفاً شروط را بررسی کنید.</p>
    </div>
    <?php
}

// Enqueue scripts for validation
function seokar_enqueue_post_checker_scripts($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_script('seokar-post-checker', plugin_dir_url(__FILE__) . '../assets/js/seokar-post-checker.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'seokar_enqueue_post_checker_scripts');

// Check conditions before publishing
function seokar_check_post_conditions($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_title']) && isset($_POST['post_content'])) {
        $title = sanitize_text_field($_POST['post_title']);
        $content = sanitize_textarea_field($_POST['post_content']);
        $word_count = str_word_count(strip_tags($content));
        $has_thumbnail = has_post_thumbnail($post_id);

        if (strlen($title) < 50 || $word_count < 800 || !$has_thumbnail) {
            // Prevent the post from being published
            remove_action('save_post', 'seokar_check_post_conditions');
            wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
            add_action('save_post', 'seokar_check_post_conditions');
            // Redirect back to the post edit screen with a message
            wp_redirect(add_query_arg('seokar_validation_error', 'true', get_edit_post_link($post_id, 'url')));
            exit;
        }
    }
}
add_action('save_post', 'seokar_check_post_conditions');

// Show validation error message
function seokar_show_validation_error_notice() {
    if (isset($_GET['seokar_validation_error']) && $_GET['seokar_validation_error'] === 'true') {
        echo '<div class="error notice"><p>این پست واجد الشرایط منتشر شدن نیست. لطفاً شروط را بررسی کنید.</p></div>';
    }
}
add_action('admin_notices', 'seokar_show_validation_error_notice');
?>
