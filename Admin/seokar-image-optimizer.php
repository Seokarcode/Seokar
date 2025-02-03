<?php
// Image optimizer page for Seokar plugin

function seokar_image_optimizer_page() {
    ?>
    <div class="wrap">
        <h1>بهینه‌سازی تصاویر</h1>
        <form method="post" action="admin-post.php">
            <input type="hidden" name="action" value="seokar_optimize_images">
            <label for="convert_format">تبدیل فرمت تصاویر به:</label>
            <select name="convert_format" id="convert_format">
                <option value="webp">WebP</option>
                <option value="avif">AVIF</option>
            </select>
            <br><br>
            <label for="keep_original">
                <input type="checkbox" name="keep_original" id="keep_original" value="1"> حفظ فرمت قبلی
            </label>
            <br><br>
            <label for="optimize_quality">
                <input type="checkbox" name="optimize_quality" id="optimize_quality" value="1"> بهینه‌سازی کیفیت تصویر
            </label>
            <br><br>
            <button type="submit" class="button button-primary">تبدیل و بهینه‌سازی تصاویر</button>
        </form>
        <div id="image_list">
            <h2>تصاویر آپلود شده</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>تصویر</th>
                        <th>نام فایل</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $args = array(
                        'post_type' => 'attachment',
                        'post_status' => 'inherit',
                        'post_mime_type' => 'image',
                        'posts_per_page' => -1,
                    );
                    $images = get_posts($args);
                    foreach ($images as $image) {
                        $image_url = wp_get_attachment_url($image->ID);
                        echo '<tr>';
                        echo '<td><img src="' . esc_url($image_url) . '" style="max-width: 100px; height: auto;"></td>';
                        echo '<td>' . esc_html($image->post_title) . '</td>';
                        echo '<td><button class="button convert_image" data-id="' . esc_attr($image->ID) . '">تبدیل</button></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function seokar_handle_image_optimization() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'seokar_optimize_images') {
        $convert_format = sanitize_text_field($_POST['convert_format']);
        $keep_original = isset($_POST['keep_original']) ? true : false;
        $optimize_quality = isset($_POST['optimize_quality']) ? true : false;

        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
        );
        $images = get_posts($args);
        foreach ($images as $image) {
            $image_path = get_attached_file($image->ID);
            seokar_convert_image_format($image_path, $convert_format, $keep_original, $optimize_quality);
        }
        wp_redirect(admin_url('admin.php?page=seokar-image-optimizer&optimized=1'));
        exit;
    }
}
add_action('admin_post_seokar_optimize_images', 'seokar_handle_image_optimization');

function seokar_convert_image_format($image_path, $format, $keep_original, $optimize_quality) {
    $image_info = pathinfo($image_path);
    $new_image_path = $image_info['dirname'] . '/' . $image_info['filename'] . '.' . $format;

    // Load the image
    $image = wp_get_image_editor($image_path);
    if (!is_wp_error($image)) {
        // Optimize quality if needed
        if ($optimize_quality) {
            $image->set_quality(85); // Adjust the quality as needed
        }

        // Save the image in the new format
        $image->save($new_image_path, $format);

        // If not keeping the original, delete it
        if (!$keep_original) {
            unlink($image_path);
        }
    }
}
