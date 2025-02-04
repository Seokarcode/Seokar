<?php
/*
 * Plugin Name: References link seo
 * Description: Adds numbered markers for external links and creates references section with improved design and features.
 * Plugin URI: https://seokar.click/
 * Author: سجاد اکبری
 * Version: 1.2.4
 * Author URI: http://sajjadakbari.ir/
 * Text Domain: seokar.click
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
                return '<span class="ela-link" data-number="' . $counter . '" data-tippy-content="' . esc_attr($title) . '">' . $matches[4] . '</span>' . $marker;
            }
            return $matches[0];
        }, $content);

    // Add references section
    if (!empty($ela_links)) {
        $references = '<div id="ela-references"><h3>منابع و لینک‌های مرتبط</h3><ol>';
        foreach ($ela_links as $link) {
            $references .= '<li id="ref-' . $link['number'] . '"><a href="' . $link['url'] . '" target="_blank" rel="noopener noreferrer">' . $link['title'] . '</a></li>';
        }
        $references .= '</ol></div>';
        $content .= $references;
    }
    return $content;
}
add_filter('the_content', 'ela_add_markers_to_content', 20);

function ela_enqueue_scripts() {
    ?>
    <style>
        /* Stylish markers */
        .ela-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ff3838);
            color: white;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid white;
            transition: all 0.3s ease;
        }
        .ela-marker:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        /* References section */
        #ela-references {
            margin-top: 50px;
            padding: 25px;
            border-top: 2px solid #eee;
            background-color: #f9f9f9;
            border-radius: 10px;
        }
        #ela-references h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        #ela-references ol {
            padding-left: 20px;
        }
        #ela-references li {
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        #ela-references li:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        #ela-references li a {
            text-decoration: none;
            color: #0073aa;
            font-weight: 500;
        }
        #ela-references li a:hover {
            text-decoration: underline;
        }

        /* Highlight effect */
        .highlight {
            animation: highlightFade 1.5s ease;
            border: 2px solid #ff6b6b;
            padding: 8px;
            border-radius: 5px;
        }
        @keyframes highlightFade {
            0% { background-color: #fff9e6; }
            100% { background-color: white; }
        }

        /* Prevent default link behavior for external links */
        .ela-link {
            cursor: pointer;
            color: #0073aa;
            text-decoration: underline;
        }
        .ela-link:hover {
            text-decoration: none;
        }
    </style>

    <!-- Include tippy.js and popper.js from CDN -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.ela-marker, .ela-link').forEach(element => {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const refNumber = this.getAttribute('data-number');
                const target = document.querySelector(`#ref-${refNumber}`);
                if(target) {
                    const offsetTop = target.getBoundingClientRect().top + window.scrollY - 50; // Adjust offset for better scroll position
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });

                    // Remove previous highlights
                    document.querySelectorAll('#ela-references li').forEach(li => {
                        li.classList.remove('highlight');
                    });

                    // Add highlight effect
                    target.classList.add('highlight');
                }
            });
        });

        // Initialize tippy.js for tooltips
        tippy('.ela-link', {
            content: (reference) => reference.getAttribute('data-tippy-content'),
            theme: 'light',
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'ela_enqueue_scripts');

// Shortcode for placing references
function ela_references_shortcode() {
    return '<div id="ela-references"></div>';
}
add_shortcode('references', 'ela_references_shortcode');
?>
