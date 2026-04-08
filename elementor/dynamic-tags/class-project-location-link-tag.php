<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !class_exists('PP_Project_Location_Link_Tag') &&
    class_exists('PP_Project_Base_Tag') &&
    class_exists('\Elementor\Modules\DynamicTags\Module')
) {
    class PP_Project_Location_Link_Tag extends PP_Project_Base_Tag {
        public function get_name() {
            return 'pp-project-location-link';
        }

        public function get_title() {
            return __('Project Location Link', 'projects-plugin');
        }

        public function get_categories() {
            return [$this->get_dynamic_tag_category('URL_CATEGORY', 'url')];
        }

        public function get_value(array $options = []) {
            $post_id = $this->resolve_post_id();
            if ($post_id <= 0) {
                return [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ];
            }

            $url = $this->get_project_location_link($post_id);
            return [
                'url' => esc_url_raw($url),
                'is_external' => true,
                'nofollow' => false,
            ];
        }
    }
}
