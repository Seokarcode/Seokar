<?php
/*
 * Plugin Name: Site Speed Optimizer
 * Description: Adds a dashboard in the admin panel for site speed improvements with options to avoid chaining critical requests and eliminate render-blocking resources.
 * Plugin URI: https://seokar.click/
 * Author: سجاد اکبری
 * Version: 1.0.0
 * Author URI: http://sajjadakbari.ir/
 * Text Domain: seokar.click
 */

// Add submenu to the admin panel
function sso_add_admin_menu() {
    add_menu_page(
        'Site Speed Optimizer', 
        'بهبود سرعت سایت', 
        'manage_options', 
        'site-speed-optimizer', 
        'sso_settings_page', 
        'dashicons-performance',
        30
    );
}
add_action('admin_menu', 'sso_add_admin_menu');

// Render the settings page
function sso_settings_page() {
    ?>
    <div class="wrap">
        <h1>بهبود سرعت سایت</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sso_settings_group'); ?>
            <?php do_settings_sections('site-speed-optimizer'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register and add settings
function sso_settings_init() {
    register_setting('sso_settings_group', 'sso_critical_requests_links');
    register_setting('sso_settings_group', 'sso_render_blocking_resources_links');

    add_settings_section(
        'sso_section_critical_requests',
        'Avoid Chaining Critical Requests',
        'sso_section_critical_requests_cb',
        'site-speed-optimizer'
    );

    add_settings_field(
        'sso_field_critical_requests',
        'لینک‌های درخواست‌های بحرانی',
        'sso_field_critical_requests_cb',
        'site-speed-optimizer',
        'sso_section_critical_requests'
    );

    add_settings_section(
        'sso_section_render_blocking_resources',
        'Eliminate Render-Blocking Resources',
        'sso_section_render_blocking_resources_cb',
        'site-speed-optimizer'
    );

    add_settings_field(
        'sso_field_render_blocking_resources',
        'لینک‌های منابع مسدود کننده رندر',
        'sso_field_render_blocking_resources_cb',
        'site-speed-optimizer',
        'sso_section_render_blocking_resources'
    );
}
add_action('admin_init', 'sso_settings_init');

function sso_section_critical_requests_cb() {
    echo '<p>Consider reducing the length of chains, reducing the download size of resources, or deferring the download of unnecessary resources to improve page load.</p>';
}

function sso_field_critical_requests_cb() {
    $critical_requests_links = get_option('sso_critical_requests_links');
    echo '<textarea name="sso_critical_requests_links" rows="5" cols="50" class="large-text">' . esc_textarea($critical_requests_links) . '</textarea>';
}

function sso_section_render_blocking_resources_cb() {
    echo '<p>Resources are blocking the first paint of your page. Consider delivering critical JS/CSS inline and deferring all non-critical JS/styles.</p>';
}

function sso_field_render_blocking_resources_cb() {
    $render_blocking_resources_links = get_option('sso_render_blocking_resources_links');
    echo '<textarea name="sso_render_blocking_resources_links" rows="5" cols="50" class="large-text">' . esc_textarea($render_blocking_resources_links) . '</textarea>';
}

// Implement optimization based on saved links
function sso_optimize_site_speed() {
    $critical_requests_links = get_option('sso_critical_requests_links');
    $render_blocking_resources_links = get_option('sso_render_blocking_resources_links');

    if ($critical_requests_links) {
        // Implement logic to avoid chaining critical requests
        // Example: defer loading or reduce download size of resources
        $critical_links = explode("\n", $critical_requests_links);
        foreach ($critical_links as $link) {
            $link = trim($link);
            if ($link) {
                echo '<link rel="preload" href="' . esc_url($link) . '" as="fetch" crossorigin="anonymous">';
            }
        }
    }

    if ($render_blocking_resources_links) {
        // Implement logic to eliminate render-blocking resources
        // Example: defer non-critical JS/CSS
        $blocking_links = explode("\n", $render_blocking_resources_links);
        foreach ($blocking_links as $link) {
            $link = trim($link);
            if ($link) {
                echo '<link rel="preload" href="' . esc_url($link) . '" as="style">';
                echo '<link rel="stylesheet" href="' . esc_url($link) . '" media="print" onload="this.media=\'all\'">';
                echo '<noscript><link rel="stylesheet" href="' . esc_url($link) . '"></noscript>';
            }
        }
    }
}
add_action('wp_head', 'sso_optimize_site_speed', 1);
?>
