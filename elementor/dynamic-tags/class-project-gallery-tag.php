<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !class_exists('PP_Project_Gallery_Tag') &&
    class_exists('PP_Project_Base_Tag') &&
    class_exists('\Elementor\Modules\DynamicTags\Module')
) {
    class PP_Project_Gallery_Tag extends PP_Project_Base_Tag {
        public function get_name() {
            return 'pp-project-gallery';
        }

        public function get_title() {
            return __('Project Gallery', 'projects-plugin');
        }

        public function get_categories() {
            return [$this->get_dynamic_tag_category('GALLERY_CATEGORY', 'gallery')];
        }

        public function get_value(array $options = []) {
            $post_id = $this->resolve_post_id();
            if ($post_id <= 0) {
                return [];
            }

            $ids = PP_Helpers::get_project_gallery_ids($post_id);
            if (empty($ids)) {
                $thumb = (int) get_post_thumbnail_id($post_id);
                if ($thumb > 0) {
                    $ids = [$thumb];
                }
            }

            $images = [];
            foreach ($ids as $id) {
                $url = wp_get_attachment_image_url($id, 'full');
                if (!$url) {
                    continue;
                }
                $images[] = [
                    'id' => $id,
                    'url' => $url,
                ];
            }

            return $images;
        }
    }
}
