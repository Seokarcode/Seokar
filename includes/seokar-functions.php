<?php
// General functions for the Seokar plugin

// Example function to enqueue scripts and styles
function seokar_enqueue_assets() {
    wp_enqueue_style('seokar-styles', plugin_dir_url(__FILE__) . '../assets/css/seokar-styles.css');
    wp_enqueue_script('seokar-scripts', plugin_dir_url(__FILE__) . '../assets/js/seokar-scripts.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'seokar_enqueue_assets');
