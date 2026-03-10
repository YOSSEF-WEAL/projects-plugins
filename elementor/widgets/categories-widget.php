<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

class PP_Categories_Widget extends Widget_Base {
    public function get_name() {
        return 'pp_categories_widget';
    }

    public function get_title() {
        return __('Projects Categories', 'projects-plugin');
    }

    public function get_icon() {
        return 'eicon-taxonomy-filter';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'projects-plugin'),
        ]);

        $this->add_control('source', [
            'label' => __('Categories Source', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'all',
            'options' => [
                'all' => __('All Categories', 'projects-plugin'),
                'selected' => __('Selected Categories', 'projects-plugin'),
            ],
        ]);

        $this->add_control('categories', [
            'label' => __('Choose Categories', 'projects-plugin'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $this->get_terms_options(),
            'condition' => ['source' => 'selected'],
        ]);

        $this->add_control('layout', [
            'label' => __('Layout', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => [
                'grid' => __('Grid', 'projects-plugin'),
                'slider' => __('Slider', 'projects-plugin'),
            ],
        ]);

        $this->add_control('columns', [
            'label' => __('Columns', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
        ]);

        $this->add_control('show_name', [
            'label' => __('Show Name', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_description', [
            'label' => __('Show Description', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_section', [
            'label' => __('Style', 'projects-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label' => __('Card Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-category-card' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('title_color', [
            'label' => __('Title Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-category-content h3' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('desc_color', [
            'label' => __('Description Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-category-content p' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('card_border', [
            'label' => __('Card Border Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-category-card' => 'border-color: {{VALUE}};'],
        ]);

        $this->add_responsive_control('card_radius', [
            'label' => __('Card Border Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-category-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function get_terms_options() {
        $terms = get_terms([
            'taxonomy' => 'project_category',
            'hide_empty' => false,
        ]);

        $options = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }

        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'taxonomy' => 'project_category',
            'hide_empty' => false,
        ];

        if (($settings['source'] ?? 'all') === 'selected' && !empty($settings['categories'])) {
            $args['include'] = array_map('absint', (array) $settings['categories']);
        }

        $terms = get_terms($args);
        if (is_wp_error($terms) || empty($terms)) {
            return;
        }

        $layout = $settings['layout'] === 'slider' ? 'slider' : 'grid';
        $columns = isset($settings['columns']) ? absint($settings['columns']) : 3;

        echo '<div class="pp-categories pp-layout-' . esc_attr($layout) . ' pp-cols-' . esc_attr($columns) . '">';
        foreach ($terms as $term) {
            set_query_var('pp_term', $term);
            set_query_var('pp_show_name', $settings['show_name'] === 'yes');
            set_query_var('pp_show_description', $settings['show_description'] === 'yes');
            include PP_PATH . 'templates/parts/category-card.php';
        }
        echo '</div>';
    }
}
