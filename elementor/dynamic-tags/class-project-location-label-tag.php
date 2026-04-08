<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !class_exists('PP_Project_Location_Label_Tag') &&
    class_exists('PP_Project_Base_Tag') &&
    class_exists('\Elementor\Modules\DynamicTags\Module')
) {
    class PP_Project_Location_Label_Tag extends PP_Project_Base_Tag {
        public function get_name() {
            return 'pp-project-location-label';
        }

        public function get_title() {
            return __('Project Location Label', 'projects-plugin');
        }

        public function get_categories() {
            return [$this->get_dynamic_tag_category('TEXT_CATEGORY', 'text')];
        }

        public function get_value(array $options = []) {
            $post_id = $this->resolve_post_id();
            if ($post_id <= 0) {
                return '';
            }

            return $this->get_project_location_label($post_id);
        }
    }
}
