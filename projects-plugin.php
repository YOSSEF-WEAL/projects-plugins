<?php
/**
 * Plugin Name: Projects Plugin for Elementor
 * Description: Manage and display projects with Elementor widgets, filtering, pagination, and location support.
 * Version: 1.0.2
 * Author: Malka
 * Author URI: https://portfolio-yossef-weal.netlify.app/
 * Update URI: https://github.com/YOSSEF-WEAL/projects-plugins
 * Text Domain: projects-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_VERSION', '1.0.2');
define('PP_PATH', plugin_dir_path(__FILE__));
define('PP_URL', plugin_dir_url(__FILE__));
define('PP_PLUGIN_FILE', __FILE__);
define('PP_GITHUB_REPOSITORY', 'YOSSEF-WEAL/projects-plugins');
define('PP_GITHUB_REPOSITORY_URL', 'https://github.com/' . PP_GITHUB_REPOSITORY);
define('PP_GITHUB_LATEST_RELEASE_API', 'https://api.github.com/repos/' . PP_GITHUB_REPOSITORY . '/releases/latest');

require_once PP_PATH . 'includes/class-helpers.php';
require_once PP_PATH . 'includes/class-post-types.php';
require_once PP_PATH . 'includes/class-meta-boxes.php';
require_once PP_PATH . 'includes/class-settings.php';
require_once PP_PATH . 'includes/class-updater.php';
require_once PP_PATH . 'elementor/class-elementor.php';

final class Projects_Plugin {
    public function __construct() {
        add_action('init', ['PP_Post_Types', 'register']);
        add_action('init', ['PP_Meta_Boxes', 'register']);
        add_action('init', ['PP_Helpers', 'register_rest']);
        add_action('admin_init', ['PP_Settings', 'register']);
        add_action('admin_menu', ['PP_Settings', 'menu']);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_filter('template_include', [$this, 'template_loader']);
        add_action('pre_get_posts', [$this, 'adjust_project_queries']);

        new PP_Elementor();
    }

    public function enqueue_frontend() {
        $frontend_css_ver = file_exists(PP_PATH . 'assets/css/frontend.css') ? (string) filemtime(PP_PATH . 'assets/css/frontend.css') : PP_VERSION;
        $gallery_css_ver = file_exists(PP_PATH . 'assets/css/gallery-lightbox.css') ? (string) filemtime(PP_PATH . 'assets/css/gallery-lightbox.css') : PP_VERSION;
        $frontend_js_ver = file_exists(PP_PATH . 'assets/js/frontend.js') ? (string) filemtime(PP_PATH . 'assets/js/frontend.js') : PP_VERSION;
        $gallery_js_ver = file_exists(PP_PATH . 'assets/js/gallery-lightbox.js') ? (string) filemtime(PP_PATH . 'assets/js/gallery-lightbox.js') : PP_VERSION;

        wp_enqueue_style('pp-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.2.6');
        wp_enqueue_style('pp-frontend', PP_URL . 'assets/css/frontend.css', ['pp-swiper'], $frontend_css_ver);
        wp_enqueue_style('pp-gallery-lightbox', PP_URL . 'assets/css/gallery-lightbox.css', ['pp-swiper'], $gallery_css_ver);

        wp_enqueue_script('pp-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.2.6', true);
        wp_enqueue_script('pp-frontend', PP_URL . 'assets/js/frontend.js', ['jquery', 'pp-swiper'], $frontend_js_ver, true);
        wp_enqueue_script('pp-gallery-lightbox', PP_URL . 'assets/js/gallery-lightbox.js', ['pp-swiper'], $gallery_js_ver, true);

        if (is_singular('project')) {
            $single_style = sanitize_key((string) PP_Helpers::get_setting('single_project_style', 'style-01'));
            $single_style_path = PP_PATH . 'assets/css/single-project-styles/' . $single_style . '.css';
            if (file_exists($single_style_path)) {
                wp_enqueue_style(
                    'pp-single-project-style',
                    PP_URL . 'assets/css/single-project-styles/' . $single_style . '.css',
                    ['pp-frontend'],
                    (string) filemtime($single_style_path)
                );
            }
        }

        wp_localize_script('pp-frontend', 'PP_DATA', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(rest_url('projects-plugin/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'default_per_page' => (int) PP_Helpers::get_setting('pagination_per_page', 9),
            'icons' => [
                'zoom_in' => PP_URL . 'public/arrows-out.svg',
                'zoom_out' => PP_URL . 'public/arrows-in.svg',
                'caret_left' => PP_URL . 'public/caret-left.svg',
                'caret_right' => PP_URL . 'public/caret-right.svg',
                'close' => PP_URL . 'public/x.svg',
            ],
        ]);
    }

    public function enqueue_admin($hook) {
        $admin_css_ver = file_exists(PP_PATH . 'assets/css/admin.css') ? (string) filemtime(PP_PATH . 'assets/css/admin.css') : PP_VERSION;
        $admin_js_ver = file_exists(PP_PATH . 'assets/js/admin.js') ? (string) filemtime(PP_PATH . 'assets/js/admin.js') : PP_VERSION;

        wp_enqueue_style('pp-admin', PP_URL . 'assets/css/admin.css', [], $admin_css_ver);
        wp_enqueue_media();
        wp_enqueue_script('pp-admin', PP_URL . 'assets/js/admin.js', ['jquery'], $admin_js_ver, true);

        wp_localize_script('pp-admin', 'PP_ADMIN', [
            'select_image' => __('Select Image', 'projects-plugin'),
            'use_image' => __('Use Image', 'projects-plugin'),
            'remove_image' => __('Remove', 'projects-plugin'),
            'select_gallery' => __('Select Gallery Images', 'projects-plugin'),
            'use_gallery' => __('Use Gallery', 'projects-plugin'),
        ]);
    }

    public function template_loader($template) {
        if (is_singular('project')) {
            $custom = PP_PATH . 'templates/single-project.php';
            if (file_exists($custom)) {
                return $custom;
            }
        }

        if (is_post_type_archive('project') || is_tax('project_category')) {
            $custom = PP_PATH . 'templates/archive-project.php';
            if (file_exists($custom)) {
                return $custom;
            }
        }

        return $template;
    }

    public function adjust_project_queries($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->is_post_type_archive('project') || $query->is_tax('project_category')) {
            $query->set('posts_per_page', (int) PP_Helpers::get_setting('projects_per_page', 9));
        }
    }
}

register_activation_hook(__FILE__, function () {
    PP_Post_Types::register();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

add_action('plugins_loaded', function () {
    new Projects_Plugin();
    new PP_Updater(PP_PLUGIN_FILE);
});
