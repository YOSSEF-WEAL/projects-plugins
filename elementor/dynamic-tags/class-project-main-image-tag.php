<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !class_exists('PP_Project_Main_Image_Tag') &&
    class_exists('PP_Project_Base_Tag') &&
    class_exists('\Elementor\Modules\DynamicTags\Module')
) {
    class PP_Project_Main_Image_Tag extends PP_Project_Base_Tag {
        public function get_name() {
            return 'pp-project-main-image';
        }

        public function get_title() {
            return __('Project Main Image', 'projects-plugin');
        }

        public function get_categories() {
            return [$this->get_dynamic_tag_category('IMAGE_CATEGORY', 'image')];
        }

        public function get_value(array $options = []) {
            $post_id = $this->resolve_post_id();
            if ($post_id <= 0) {
                return [
                    'id' => 0,
                    'url' => '',
                ];
            }

            $attachment_id = $this->resolve_main_image_id($post_id);
            if ($attachment_id <= 0) {
                return [
                    'id' => 0,
                    'url' => '',
                ];
            }

            $url = wp_get_attachment_image_url($attachment_id, 'full');
            return [
                'id' => $attachment_id,
                'url' => $url ? $url : '',
            ];
        }

        private function resolve_main_image_id($post_id) {
            $thumbnail_id = (int) get_post_thumbnail_id($post_id);
            if ($thumbnail_id > 0) {
                return $thumbnail_id;
            }

            $gallery_raw = get_post_meta($post_id, '_pp_gallery_ids', true);
            if (empty($gallery_raw)) {
                return 0;
            }

            $ids = array_filter(array_map('absint', explode(',', (string) $gallery_raw)));
            if (empty($ids)) {
                return 0;
            }

            return (int) reset($ids);
        }
    }
}
