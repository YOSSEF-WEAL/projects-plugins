<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Elementor {
    public function __construct() {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
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
}
