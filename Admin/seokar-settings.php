<?php
// Settings page for Seokar plugin

function seokar_settings_page() {
    ?>
    <div class="wrap">
        <h1>تنظیمات سئوکار</h1>
        <form method="post" action="options.php">
            <?php settings_fields('seokar-settings-group'); ?>
            <?php do_settings_sections('seokar-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function seokar_settings_init() {
    register_setting('seokar-settings-group', 'seokar_option');

    add_settings_section(
        'seokar_settings_section',
        'تنظیمات پایه',
        'seokar_settings_section_cb',
        'seokar-settings'
    );

    add_settings_field(
        'seokar_option',
        'مثال تنظیمات',
        'seokar_option_cb',
        'seokar-settings',
        'seokar_settings_section'
    );
}
add_action('admin_init', 'seokar_settings_init');

function seokar_settings_section_cb() {
    echo '<p>تنظیمات مربوط به سئوکار را در اینجا تنظیم کنید.</p>';
}

function seokar_option_cb() {
    $value = get_option('seokar_option');
    echo '<input type="text" name="seokar_option" value="' . esc_attr($value) . '">';
}
?>
