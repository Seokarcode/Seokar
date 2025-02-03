<?php
/*
 * Plugin Name: Seokar
 * Description: A comprehensive SEO plugin with various functionalities.
 * Version: 1.0.0
 * Author: Sajjadakbari
 * Text Domain: sajjadakbari.ir
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path
define('SEOKAR_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include necessary files
require_once SEOKAR_PLUGIN_PATH . 'includes/seokar-functions.php';
require_once SEOKAR_PLUGIN_PATH . 'admin/seokar-dashboard.php';
require_once SEOKAR_PLUGIN_PATH . 'admin/seokar-settings.php';
require_once SEOKAR_PLUGIN_PATH . 'admin/seokar-image-optimizer.php';

// Add top-level menu and submenus
function seokar_add_admin_menu() {
    add_menu_page(
        'سئوکار',              // Page title
        'سئوکار',              // Menu title
        'manage_options',      // Capability
        'seokar',              // Menu slug
        'seokar_dashboard_page', // Function to display page
        'dashicons-chart-area' // Icon URL
    );

    add_submenu_page(
        'seokar',                     // Parent slug
        'داشبورد',                   // Page title
        'داشبورد',                   // Menu title
        'manage_options',             // Capability
        'seokar-dashboard',           // Menu slug
        'seokar_dashboard_page'       // Function to display page
    );

    add_submenu_page(
        'seokar',                     // Parent slug
        'تنظیمات',                   // Page title
        'تنظیمات',                   // Menu title
        'manage_options',             // Capability
        'seokar-settings',            // Menu slug
        'seokar_settings_page'        // Function to display page
    );

    add_submenu_page(
        'seokar',                     // Parent slug
        'بهینه‌سازی تصاویر',         // Page title
        'بهینه‌سازی تصاویر',         // Menu title
        'manage_options',             // Capability
        'seokar-image-optimizer',     // Menu slug
        'seokar_image_optimizer_page' // Function to display page
    );
}
add_action('admin_menu', 'seokar_add_admin_menu');
