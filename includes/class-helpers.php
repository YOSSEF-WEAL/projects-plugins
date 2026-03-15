<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Helpers {
    public static function get_settings() {
        $defaults = [
            'location_display_default' => 'link',
            'google_maps_api_key' => '',
            'projects_per_page' => 9,
            'pagination_per_page' => 9,
            'enabled_views' => ['grid', 'masonry', 'slider', 'list'],
            'single_project_style' => 'style-01',
        ];

        $saved = get_option('pp_settings', []);
        if (!is_array($saved)) {
            $saved = [];
        }

        return wp_parse_args($saved, $defaults);
    }

    public static function get_setting($key, $default = null) {
        $settings = self::get_settings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    public static function get_project_gallery_ids($post_id) {
        $raw = get_post_meta($post_id, '_pp_gallery_ids', true);
        if (empty($raw)) {
            return [];
        }
        $ids = array_filter(array_map('absint', explode(',', (string) $raw)));
        return $ids;
    }

    public static function get_project_location_display($post_id) {
        $override = get_post_meta($post_id, '_pp_location_display', true);
        if (in_array($override, ['link', 'map'], true)) {
            return $override;
        }

        $default = self::get_setting('location_display_default', 'link');
        return in_array($default, ['link', 'map'], true) ? $default : 'link';
    }

    public static function get_google_maps_link($lat, $lng, $label = '') {
        $query = $label ? rawurlencode($label) : $lat . ',' . $lng;
        return 'https://www.google.com/maps/search/?api=1&query=' . $query;
    }

    public static function get_excerpt($post_id, $length = 20) {
        $text = get_the_excerpt($post_id);
        if (!$text) {
            $text = wp_strip_all_tags(get_post_field('post_content', $post_id));
        }
        return wp_trim_words($text, $length);
    }

    public static function register_rest() {
        register_rest_route('projects-plugin/v1', '/projects', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [__CLASS__, 'rest_projects'],
            'args' => [
                'page' => ['sanitize_callback' => 'absint', 'default' => 1],
                'per_page' => ['sanitize_callback' => 'absint', 'default' => 9],
                'category' => ['sanitize_callback' => 'sanitize_text_field'],
                'latest' => ['sanitize_callback' => 'absint'],
                'image_size' => ['sanitize_callback' => 'sanitize_key', 'default' => 'large'],
                'show_image' => ['sanitize_callback' => 'absint', 'default' => 1],
                'show_title' => ['sanitize_callback' => 'absint', 'default' => 1],
                'show_excerpt' => ['sanitize_callback' => 'absint', 'default' => 1],
                'show_category' => ['sanitize_callback' => 'absint', 'default' => 1],
                'show_button' => ['sanitize_callback' => 'absint', 'default' => 1],
                'view_text' => ['sanitize_callback' => 'sanitize_text_field', 'default' => 'View Project'],
            ],
        ]);
    }

    public static function rest_projects(WP_REST_Request $request) {
        $page = max(1, (int) $request->get_param('page'));
        $per_page = max(1, (int) $request->get_param('per_page'));
        $latest = (int) $request->get_param('latest');
        $category = $request->get_param('category');
        $image_size = sanitize_key((string) $request->get_param('image_size'));
        $show_image = (int) $request->get_param('show_image') === 1;
        $show_title = (int) $request->get_param('show_title') === 1;
        $show_excerpt = (int) $request->get_param('show_excerpt') === 1;
        $show_category = (int) $request->get_param('show_category') === 1;
        $show_button = (int) $request->get_param('show_button') === 1;
        $view_text = sanitize_text_field((string) $request->get_param('view_text'));
        if ($view_text === '') {
            $view_text = __('View Project', 'projects-plugin');
        }
        $allowed_sizes = get_intermediate_image_sizes();
        $allowed_sizes[] = 'full';
        if (!in_array($image_size, $allowed_sizes, true)) {
            $image_size = 'large';
        }

        if ($latest > 0) {
            $per_page = $latest;
            $page = 1;
        }

        $args = [
            'post_type' => 'project',
            'post_status' => 'publish',
            'paged' => $page,
            'posts_per_page' => $per_page,
        ];

        if (!empty($category)) {
            $args['tax_query'] = [[
                'taxonomy' => 'project_category',
                'field' => is_numeric($category) ? 'term_id' : 'slug',
                'terms' => $category,
            ]];
        }

        $query = new WP_Query($args);
        $items = [];

        foreach ($query->posts as $post) {
            $items[] = [
                'id' => $post->ID,
                'title' => get_the_title($post->ID),
                'link' => get_permalink($post->ID),
                'excerpt' => self::get_excerpt($post->ID),
                'image' => get_the_post_thumbnail_url($post->ID, $image_size),
                'category' => wp_get_post_terms($post->ID, 'project_category', ['fields' => 'names']),
                'html' => self::render_project_card_html($post->ID, [
                    'show_image' => $show_image,
                    'image_size' => $image_size,
                    'show_title' => $show_title,
                    'show_excerpt' => $show_excerpt,
                    'show_category' => $show_category,
                    'show_button' => $show_button,
                    'view_text' => $view_text,
                ]),
            ];
        }

        return new WP_REST_Response([
            'items' => $items,
            'total' => (int) $query->found_posts,
            'max_pages' => (int) $query->max_num_pages,
            'page' => $page,
        ]);
    }

    private static function render_project_card_html($post_id, array $opts) {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return '';
        }

        $pp_card_context = [
            'post_id' => $post_id,
            'show_image' => !empty($opts['show_image']),
            'image_size' => !empty($opts['image_size']) ? $opts['image_size'] : 'large',
            'show_title' => !empty($opts['show_title']),
            'show_excerpt' => !empty($opts['show_excerpt']),
            'show_category' => !empty($opts['show_category']),
            'show_button' => !empty($opts['show_button']),
            'view_text' => !empty($opts['view_text']) ? $opts['view_text'] : __('View Project', 'projects-plugin'),
        ];

        ob_start();
        include PP_PATH . 'templates/parts/project-card.php';
        $html = (string) ob_get_clean();
        unset($pp_card_context);

        return $html;
    }
}
