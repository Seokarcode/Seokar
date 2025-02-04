<?php
/*
 * External Link Annotator
 * Adds numbered markers for external links and creates references section with improved design and features.
 */

// Function to fetch the title of a webpage
function ela_fetch_page_title($url, $fallback) {
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return $fallback;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code == 403) {
        return $fallback;
    }

    $body = wp_remote_retrieve_body($response);
    preg_match('/<title>(.*?)<\/title>/is', $body, $matches);

    if (!empty($matches[1])) {
        return trim($matches[1]);
    }

    return $fallback;
}

function ela_add_markers_to_content($content) {
    if (!is_single()) return $content;

    global $ela_links;
    $ela_links = array();
    $counter = 1;

    // Find and process external links
    $content = preg_replace_callback('/<a(.*?)href=["\'](.*?)["\'](.*?)>(.*?)<\/a>/i',
        function ($matches) use (&$counter, &$ela_links) {
            $url = $matches[2];
            $site_url = site_url();

            // Check if the URL points to a video file hosted on the same site
            $video_extensions = array('mp4', 'webm', 'ogg');
            $parsed_url = parse_url($url);
            $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if (strpos($url, $site_url) === false && filter_var($url, FILTER_VALIDATE_URL) && !in_array($extension, $video_extensions)) {
                // Use the link text as fallback
                $fallback_title = strip_tags($matches[4]);
                // Fetch the title of the external page
                $title = ela_fetch_page_title($url, $fallback_title);

                $ela_links[] = array(
                    'number' => $counter,
                    'url' => $url,
                    'title' => $title
                );

                $marker = '<sup class="ela-marker" data-number="' . $counter . '">' . $counter . '</sup>';
                $counter++;
                // Modified to disable the link and add marker
                return '<span class="ela-link" data-number="' . $counter . '" data-tippy-content="' . esc_attr($title
