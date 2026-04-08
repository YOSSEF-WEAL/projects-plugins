<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Elementor {
    public function __construct() {
        add_action('init', [$this, 'enable_project_elementor_support'], 20);
        add_filter('elementor_cpt_support', [$this, 'add_project_post_type_to_supported_cpts']);
        add_action('elementor/dynamic_tags/register', [$this, 'register_dynamic_tags']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('save_post_project', [$this, 'maybe_set_project_page_template'], 20, 3);
    }

    public function enable_project_elementor_support() {
        if (post_type_exists('project')) {
            add_post_type_support('project', 'elementor');
        }
    }

    public function add_project_post_type_to_supported_cpts($post_types) {
        if (!is_array($post_types)) {
            $post_types = [];
        }

        if (!in_array('project', $post_types, true)) {
            $post_types[] = 'project';
        }

        return $post_types;
    }

    public function register_widgets($widgets_manager) {
        if (!did_action('elementor/loaded')) {
            return;
        }

        require_once PP_PATH . 'elementor/widgets/categories-widget.php';
        require_once PP_PATH . 'elementor/widgets/projects-widget.php';
        require_once PP_PATH . 'elementor/widgets/latest-projects-widget.php';

        $widgets_manager->register(new PP_Categories_Widget());
        $widgets_manager->register(new PP_Projects_Widget());
        $widgets_manager->register(new PP_Latest_Projects_Widget());
    }

    public function register_dynamic_tags($dynamic_tags) {
        if (!did_action('elementor/loaded')) {
            return;
        }

        if (!is_object($dynamic_tags)) {
            return;
        }

        if (!class_exists('\Elementor\Core\DynamicTags\Data_Tag') || !class_exists('\Elementor\Modules\DynamicTags\Module')) {
            return;
        }

        require_once PP_PATH . 'elementor/dynamic-tags/class-project-base-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-main-image-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-title-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-excerpt-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-content-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-category-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-location-label-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-location-link-tag.php';
        require_once PP_PATH . 'elementor/dynamic-tags/class-project-gallery-tag.php';

        if (method_exists($dynamic_tags, 'register_group')) {
            try {
                $dynamic_tags->register_group('pp-project', [
                    'title' => __('Projects Plugin', 'projects-plugin'),
                ]);
            } catch (\Throwable $e) {
                // Continue registering tags even if the group already exists.
            }
        }

        $tag_classes = [
            'PP_Project_Main_Image_Tag',
            'PP_Project_Title_Tag',
            'PP_Project_Excerpt_Tag',
            'PP_Project_Content_Tag',
            'PP_Project_Category_Tag',
            'PP_Project_Location_Label_Tag',
            'PP_Project_Location_Link_Tag',
            'PP_Project_Gallery_Tag',
        ];

        foreach ($tag_classes as $tag_class) {
            if (!class_exists($tag_class)) {
                continue;
            }

            try {
                if (method_exists($dynamic_tags, 'register')) {
                    $dynamic_tags->register(new $tag_class());
                    continue;
                }

                if (method_exists($dynamic_tags, 'register_tag')) {
                    $dynamic_tags->register_tag($tag_class);
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
    }

    public function maybe_set_project_page_template($post_id, $post, $update) {
        if (empty($post_id) || !($post instanceof WP_Post)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (!did_action('elementor/loaded') || !class_exists('\Elementor\Plugin')) {
            return;
        }

        $elementor = \Elementor\Plugin::$instance;
        if (
            !$elementor ||
            empty($elementor->db) ||
            !method_exists($elementor->db, 'is_built_with_elementor') ||
            !$elementor->db->is_built_with_elementor($post_id)
        ) {
            return;
        }

        $current_template = (string) get_post_meta($post_id, '_wp_page_template', true);
        if ($current_template !== '' && $current_template !== 'default') {
            return;
        }

        update_post_meta($post_id, '_wp_page_template', 'elementor_full_width');
    }
}
