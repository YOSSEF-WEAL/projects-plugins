<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !class_exists('PP_Project_Base_Tag') &&
    class_exists('\Elementor\Core\DynamicTags\Data_Tag')
) {
    abstract class PP_Project_Base_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
        public function get_group() {
            return 'pp-project';
        }

        protected function is_project_post($post_id) {
            return $post_id > 0 && get_post_type($post_id) === 'project';
        }

        protected function resolve_post_id() {
            $candidates = [
                (int) get_the_ID(),
                (int) get_queried_object_id(),
            ];

            foreach (['preview_id', 'post_id', 'post', 'elementor-preview'] as $key) {
                if (!empty($_GET[$key])) {
                    $candidates[] = absint(wp_unslash($_GET[$key]));
                }
            }

            if (
                did_action('elementor/loaded') &&
                class_exists('\Elementor\Plugin')
            ) {
                $elementor = \Elementor\Plugin::$instance;
                if (
                    $elementor &&
                    !empty($elementor->preview) &&
                    method_exists($elementor->preview, 'get_preview_id')
                ) {
                    $candidates[] = (int) $elementor->preview->get_preview_id();
                }
            }

            $candidates = array_values(array_unique(array_filter(array_map('intval', $candidates))));
            foreach ($candidates as $candidate_id) {
                if ($this->is_project_post($candidate_id)) {
                    return $candidate_id;
                }
            }

            return 0;
        }

        protected function get_primary_category_name($post_id) {
            $terms = get_the_terms($post_id, 'project_category');
            if (!empty($terms) && !is_wp_error($terms) && isset($terms[0]->name)) {
                return (string) $terms[0]->name;
            }
            return '';
        }

        protected function get_project_location_label($post_id) {
            return (string) get_post_meta($post_id, '_pp_location_label', true);
        }

        protected function get_project_location_link($post_id) {
            $lat = get_post_meta($post_id, '_pp_location_lat', true);
            $lng = get_post_meta($post_id, '_pp_location_lng', true);
            $label = get_post_meta($post_id, '_pp_location_label', true);
            $shortlink = get_post_meta($post_id, '_pp_location_shortlink', true);

            if (class_exists('PP_Helpers') && method_exists('PP_Helpers', 'get_google_maps_link')) {
                return (string) PP_Helpers::get_google_maps_link($lat, $lng, $label, $shortlink);
            }

            return '';
        }

        protected function get_project_excerpt($post_id) {
            $excerpt = get_the_excerpt($post_id);
            if (trim((string) $excerpt) !== '') {
                return (string) wp_strip_all_tags($excerpt);
            }

            if (class_exists('PP_Helpers') && method_exists('PP_Helpers', 'get_excerpt')) {
                return (string) PP_Helpers::get_excerpt($post_id, 22);
            }

            $content = (string) get_post_field('post_content', $post_id);
            return (string) wp_trim_words(wp_strip_all_tags($content), 22);
        }

        protected function get_project_content($post_id) {
            $content = (string) get_post_field('post_content', $post_id);
            if ($content !== '') {
                $content = strip_shortcodes($content);
                $content = trim((string) wp_strip_all_tags($content));
                if ($content !== '') {
                    return $content;
                }
            }

            return $this->get_project_excerpt($post_id);
        }

        protected function get_dynamic_tag_category($constant_name, $fallback) {
            $module_class = '\Elementor\Modules\DynamicTags\Module';
            $constant = $module_class . '::' . $constant_name;

            if (defined($constant)) {
                $value = constant($constant);
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }

            return (string) $fallback;
        }
    }
}
