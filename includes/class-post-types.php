<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Post_Types {
    public static function register() {
        self::register_project_post_type();
        self::register_project_category_taxonomy();
    }

    private static function register_project_post_type() {
        $labels = [
            'name' => __('Projects', 'projects-plugin'),
            'singular_name' => __('Project', 'projects-plugin'),
            'add_new_item' => __('Add New Project', 'projects-plugin'),
            'edit_item' => __('Edit Project', 'projects-plugin'),
            'new_item' => __('New Project', 'projects-plugin'),
            'view_item' => __('View Project', 'projects-plugin'),
            'search_items' => __('Search Projects', 'projects-plugin'),
            'not_found' => __('No projects found', 'projects-plugin'),
            'menu_name' => __('Projects', 'projects-plugin'),
        ];

        register_post_type('project', [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'projects'],
            'menu_icon' => 'dashicons-portfolio',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest' => true,
        ]);
    }

    private static function register_project_category_taxonomy() {
        $labels = [
            'name' => __('Project Categories', 'projects-plugin'),
            'singular_name' => __('Project Category', 'projects-plugin'),
            'search_items' => __('Search Project Categories', 'projects-plugin'),
            'all_items' => __('All Project Categories', 'projects-plugin'),
            'edit_item' => __('Edit Project Category', 'projects-plugin'),
            'update_item' => __('Update Project Category', 'projects-plugin'),
            'add_new_item' => __('Add New Project Category', 'projects-plugin'),
            'new_item_name' => __('New Project Category Name', 'projects-plugin'),
            'menu_name' => __('Project Categories', 'projects-plugin'),
        ];

        register_taxonomy('project_category', ['project'], [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'project-category'],
        ]);
    }
}
