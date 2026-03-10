<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

class PP_Projects_Widget extends Widget_Base {
    public function get_name() {
        return 'pp_projects_widget';
    }

    public function get_title() {
        return __('Projects', 'projects-plugin');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $enabled_views = PP_Helpers::get_setting('enabled_views', ['grid', 'masonry', 'slider', 'list']);
        $view_options = [];
        $all_view_options = [
            'grid' => __('Grid', 'projects-plugin'),
            'masonry' => __('Masonry', 'projects-plugin'),
            'slider' => __('Slider', 'projects-plugin'),
            'list' => __('List', 'projects-plugin'),
        ];
        foreach ($all_view_options as $key => $label) {
            if (in_array($key, (array) $enabled_views, true)) {
                $view_options[$key] = $label;
            }
        }
        if (empty($view_options)) {
            $view_options = ['grid' => __('Grid', 'projects-plugin')];
        }

        $this->start_controls_section('content_section', [
            'label' => __('Content', 'projects-plugin'),
        ]);

        $this->add_control('source', [
            'label' => __('Data Source', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'all',
            'options' => [
                'all' => __('All Projects', 'projects-plugin'),
                'category' => __('Specific Category', 'projects-plugin'),
                'latest' => __('Latest X Projects', 'projects-plugin'),
            ],
        ]);

        $this->add_control('category', [
            'label' => __('Category', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_terms_options(),
            'condition' => ['source' => 'category'],
        ]);

        $this->add_control('latest_count', [
            'label' => __('Latest Count', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'default' => 6,
            'condition' => ['source' => 'latest'],
        ]);

        $this->add_control('layout', [
            'label' => __('Layout', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => array_key_first($view_options),
            'options' => $view_options,
        ]);

        $this->add_control('show_filters', [
            'label' => __('Show Category Filters', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('pagination', [
            'label' => __('Pagination', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'classic',
            'options' => [
                'classic' => __('Classic', 'projects-plugin'),
                'load_more' => __('Load More', 'projects-plugin'),
                'infinite' => __('Infinite Scroll', 'projects-plugin'),
            ],
            'condition' => ['layout!' => 'slider'],
        ]);

        $this->add_control('per_page', [
            'label' => __('Projects Per Page', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'default' => (int) PP_Helpers::get_setting('pagination_per_page', 9),
            'description' => __('Overrides the global pagination items per page for this widget.', 'projects-plugin'),
        ]);

        $this->add_control('columns', [
            'label' => __('Columns', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
            'condition' => ['layout!' => 'slider'],
        ]);

        $this->add_control('items_align', [
            'label' => __('Items Alignment', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'start',
            'options' => [
                'start' => __('Start', 'projects-plugin'),
                'center' => __('Center', 'projects-plugin'),
                'end' => __('End', 'projects-plugin'),
                'stretch' => __('Stretch', 'projects-plugin'),
            ],
            'condition' => ['layout!' => 'slider'],
        ]);

        $this->add_control('items_justify', [
            'label' => __('Justify Content', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'start',
            'options' => [
                'start' => __('Start', 'projects-plugin'),
                'center' => __('Center', 'projects-plugin'),
                'end' => __('End', 'projects-plugin'),
                'space-between' => __('Space Between', 'projects-plugin'),
                'space-around' => __('Space Around', 'projects-plugin'),
                'space-evenly' => __('Space Evenly', 'projects-plugin'),
                'stretch' => __('Stretch', 'projects-plugin'),
            ],
            'condition' => ['layout!' => 'slider'],
        ]);

        $this->add_control('slider_cards_desktop', [
            'label' => __('Slider Cards (Desktop)', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 6,
            'default' => 3,
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('slider_cards_tablet', [
            'label' => __('Slider Cards (Tablet)', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 4,
            'default' => 2,
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('slider_cards_mobile', [
            'label' => __('Slider Cards (Mobile)', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 2,
            'default' => 1,
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('show_image', [
            'label' => __('Show Image', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'thumbnail',
            'label' => __('Image Size', 'projects-plugin'),
            'default' => 'large',
            'exclude' => ['custom'],
            'condition' => ['show_image' => 'yes'],
        ]);

        $this->add_control('show_title', [
            'label' => __('Show Title', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_excerpt', [
            'label' => __('Show Excerpt', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_category', [
            'label' => __('Show Category', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_button', [
            'label' => __('Show Button', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('text_all', [
            'label' => __('All Filter Label', 'projects-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => __('All', 'projects-plugin'),
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('text_view_project', [
            'label' => __('View Project Label', 'projects-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => __('View Project', 'projects-plugin'),
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('text_load_more', [
            'label' => __('Load More Label', 'projects-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Load More', 'projects-plugin'),
            'condition' => ['pagination' => 'load_more'],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('card_style_section', [
            'label' => __('Card Style', 'projects-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label' => __('Card Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-project-card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_border', [
            'label' => __('Card Border Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-project-card' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('title_color', [
            'label' => __('Title Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-title a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('text_color', [
            'label' => __('Excerpt Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-excerpt' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('category_color', [
            'label' => __('Category Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-category' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('image_width', [
            'label' => __('Image Width', 'projects-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                '%' => ['min' => 10, 'max' => 100],
                'px' => ['min' => 40, 'max' => 1200],
            ],
            'selectors' => [
                '{{WRAPPER}} .pp-card-image img' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_image' => 'yes'],
        ]);

        $this->add_responsive_control('image_height', [
            'label' => __('Image Height', 'projects-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 80, 'max' => 900],
                'vh' => ['min' => 10, 'max' => 90],
            ],
            'selectors' => [
                '{{WRAPPER}} .pp-card-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
            ],
            'condition' => ['show_image' => 'yes'],
        ]);

        $this->add_responsive_control('card_radius', [
            'label' => __('Card Border Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-project-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_content_padding', [
            'label' => __('Card Content Padding', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('buttons_style_section', [
            'label' => __('Buttons Style', 'projects-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_button_heading', [
            'label' => __('Card Button', 'projects-plugin'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('button_bg', [
            'label' => __('Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Text Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'color: {{VALUE}};',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('button_border_color', [
            'label' => __('Border Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'border-color: {{VALUE}}; border-style: solid;',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('card_button_full_width', [
            'label' => __('Full Width', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'display: block; width: 100%; text-align: center;',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_responsive_control('button_padding', [
            'label' => __('Padding', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_responsive_control('card_button_radius', [
            'label' => __('Border Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('filter_button_heading', [
            'label' => __('Filters Buttons', 'projects-plugin'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_bg_default', [
            'label' => __('Default Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_text_default', [
            'label' => __('Default Text', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn' => 'color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_border_default', [
            'label' => __('Default Border', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn' => 'border-color: {{VALUE}}; border-style: solid;',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_bg_hover', [
            'label' => __('Hover Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn:hover, {{WRAPPER}} .pp-filter-btn:focus-visible' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_text_hover', [
            'label' => __('Hover Text', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn:hover, {{WRAPPER}} .pp-filter-btn:focus-visible' => 'color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_border_hover', [
            'label' => __('Hover Border', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn:hover, {{WRAPPER}} .pp-filter-btn:focus-visible' => 'border-color: {{VALUE}}; border-style: solid;',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_bg_active', [
            'label' => __('Active Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn.active' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_text_active', [
            'label' => __('Active Text', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn.active' => 'color: {{VALUE}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('filter_button_border_active', [
            'label' => __('Active Border', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn.active' => 'border-color: {{VALUE}}; border-style: solid;',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_responsive_control('filter_button_padding', [
            'label' => __('Filters Padding', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_responsive_control('filter_button_radius', [
            'label' => __('Filters Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-filter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_filters' => 'yes'],
        ]);

        $this->add_control('slider_buttons_heading', [
            'label' => __('Slider Arrows', 'projects-plugin'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('slider_nav_bg', [
            'label' => __('Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('slider_nav_border', [
            'label' => __('Border', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'border-color: {{VALUE}}; border-style: solid;',
            ],
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_responsive_control('slider_nav_size', [
            'label' => __('Slider Arrow Size', 'projects-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 24, 'max' => 100],
            ],
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_responsive_control('slider_nav_radius', [
            'label' => __('Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['layout' => 'slider'],
        ]);

        $this->end_controls_section();

        // Pagination style controls
        $this->start_controls_section('pagination_style_section', [
            'label' => __('Pagination', 'projects-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('pagination_alignment', [
            'label' => __('Alignment', 'projects-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __('Start', 'projects-plugin'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'projects-plugin'),
                    'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' => __('End', 'projects-plugin'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .pp-pagination' => 'display:flex; flex-wrap:wrap; justify-content: {{VALUE}};',
                '{{WRAPPER}} .pp-load-more' => 'display:inline-flex; justify-content:center;',
            ],
        ]);

        $this->add_responsive_control('pagination_gap', [
            'label' => __('Spacing', 'projects-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'range' => [
                'px' => ['min' => 0, 'max' => 40],
                'em' => ['min' => 0, 'max' => 3],
            ],
            'selectors' => [
                '{{WRAPPER}} .pp-pagination' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('pagination_text_color', [
            'label' => __('Link Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a' => 'color: {{VALUE}};',
                '{{WRAPPER}} .pp-load-more' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_bg_color', [
            'label' => __('Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a, {{WRAPPER}} .pp-pagination span' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .pp-load-more' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_border_color', [
            'label' => __('Border Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a, {{WRAPPER}} .pp-pagination span' => 'border:1px solid {{VALUE}};',
                '{{WRAPPER}} .pp-load-more' => 'border:1px solid {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_hover_color', [
            'label' => __('Hover Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .pp-load-more:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_active_bg', [
            'label' => __('Active Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination span.current' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_active_color', [
            'label' => __('Active Text', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-pagination span.current' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('pagination_padding', [
            'label' => __('Padding', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a, {{WRAPPER}} .pp-pagination span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .pp-load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('pagination_radius', [
            'label' => __('Border Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-pagination a, {{WRAPPER}} .pp-pagination span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .pp-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function get_terms_options() {
        $terms = get_terms(['taxonomy' => 'project_category', 'hide_empty' => false]);
        $options = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->slug] = $term->name;
            }
        }
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $per_page = max(1, absint($settings['per_page'] ?? PP_Helpers::get_setting('pagination_per_page', 9)));
        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
        $is_slider = $layout === 'slider';

        $query_args = [
            'post_type' => 'project',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => max(1, get_query_var('paged', 1)),
        ];

        if (($settings['source'] ?? 'all') === 'category' && !empty($settings['category'])) {
            $query_args['tax_query'] = [[
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => $settings['category'],
            ]];
        }

        if (($settings['source'] ?? 'all') === 'latest') {
            $query_args['posts_per_page'] = max(1, absint($settings['latest_count'] ?? 6));
        }

        $all_text = !empty($settings['text_all']) ? $settings['text_all'] : __('All', 'projects-plugin');
        $view_text = !empty($settings['text_view_project']) ? $settings['text_view_project'] : __('View Project', 'projects-plugin');
        $load_more_text = !empty($settings['text_load_more']) ? $settings['text_load_more'] : __('Load More', 'projects-plugin');

        $image_size = !empty($settings['thumbnail_size']) ? sanitize_key($settings['thumbnail_size']) : 'large';
        $slider_cards_desktop = max(1, absint($settings['slider_cards_desktop'] ?? 3));
        $slider_cards_tablet = max(1, absint($settings['slider_cards_tablet'] ?? 2));
        $slider_cards_mobile = max(1, absint($settings['slider_cards_mobile'] ?? 1));
        $align_choice = $settings['items_align'] ?? 'start';
        $align_value = in_array($align_choice, ['start', 'center', 'end', 'stretch'], true) ? $align_choice : 'start';
        $justify_choice = $settings['items_justify'] ?? 'start';
        $justify_value = in_array($justify_choice, ['start', 'center', 'end', 'space-between', 'space-around', 'space-evenly', 'stretch'], true) ? $justify_choice : 'start';
        $justify_items_value = in_array($justify_value, ['start', 'center', 'end', 'stretch'], true) ? $justify_value : 'stretch';
        $card_config = [
            'show_image' => (($settings['show_image'] ?? 'yes') === 'yes') ? 1 : 0,
            'show_title' => (($settings['show_title'] ?? 'yes') === 'yes') ? 1 : 0,
            'show_excerpt' => (($settings['show_excerpt'] ?? 'yes') === 'yes') ? 1 : 0,
            'show_category' => (($settings['show_category'] ?? 'yes') === 'yes') ? 1 : 0,
            'show_button' => (($settings['show_button'] ?? 'yes') === 'yes') ? 1 : 0,
            'image_size' => $image_size,
            'view_text' => $view_text,
        ];
        $card_config_json = wp_json_encode($card_config);

        $q = new WP_Query($query_args);

        echo '<div class="pp-projects-widget"';
        echo ' data-layout="' . esc_attr($layout) . '"';
        echo ' data-pagination="' . esc_attr($settings['pagination'] ?? 'classic') . '"';
        echo ' data-columns="' . esc_attr($settings['columns'] ?? '3') . '"';
        echo ' data-show-image="' . esc_attr(($settings['show_image'] ?? 'yes') === 'yes' ? '1' : '0') . '"';
        echo ' data-show-title="' . esc_attr(($settings['show_title'] ?? 'yes') === 'yes' ? '1' : '0') . '"';
        echo ' data-show-excerpt="' . esc_attr(($settings['show_excerpt'] ?? 'yes') === 'yes' ? '1' : '0') . '"';
        echo ' data-show-category="' . esc_attr(($settings['show_category'] ?? 'yes') === 'yes' ? '1' : '0') . '"';
        echo ' data-show-button="' . esc_attr(($settings['show_button'] ?? 'yes') === 'yes' ? '1' : '0') . '"';
        echo ' data-image-size="' . esc_attr($image_size) . '"';
        echo ' data-view-project-text="' . esc_attr($view_text) . '"';
        echo ' data-per-page="' . esc_attr((string) $query_args['posts_per_page']) . '"';
        echo ' data-card-config="' . esc_attr((string) $card_config_json) . '"';
        echo '>';

        if (($settings['show_filters'] ?? '') === 'yes') {
            $this->render_filters($all_text);
        }

        if ($is_slider) {
            echo '<div class="pp-slider-shell">';
            echo '<button type="button" class="pp-slider-nav pp-slider-prev" aria-label="' . esc_attr__('Previous', 'projects-plugin') . '">';
            echo '<img src="' . esc_url(PP_URL . 'public/caret-right.svg') . '" alt="" aria-hidden="true">';
            echo '</button>';
        }

        $list_classes = 'pp-projects-list pp-layout-' . esc_attr($layout);
        if (!$is_slider) {
            $list_classes .= ' pp-cols-' . esc_attr($settings['columns'] ?? '3');
        } else {
            $list_classes .= ' pp-slider-track swiper';
        }

        $style_vars = '';
        if (!$is_slider) {
            $style_vars = ' style="--pp-justify-items:' . esc_attr($justify_items_value) . ';--pp-align-items:' . esc_attr($align_value) . ';--pp-justify-content:' . esc_attr($justify_value) . ';"';
        }

        echo '<div class="' . esc_attr($list_classes) . '"' . $style_vars;
        if ($is_slider) {
            echo ' data-slider-desktop="' . esc_attr($slider_cards_desktop) . '"';
            echo ' data-slider-tablet="' . esc_attr($slider_cards_tablet) . '"';
            echo ' data-slider-mobile="' . esc_attr($slider_cards_mobile) . '"';
        }
        echo '>';

        if ($is_slider) {
            echo '<div class="swiper-wrapper">';
        }

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                if ($is_slider) {
                    echo '<div class="swiper-slide pp-project-slide">';
                }

                $pp_card_context = [
                    'post_id' => get_the_ID(),
                    'show_image' => ($settings['show_image'] ?? 'yes') === 'yes',
                    'image_size' => $image_size,
                    'show_title' => ($settings['show_title'] ?? 'yes') === 'yes',
                    'show_excerpt' => ($settings['show_excerpt'] ?? 'yes') === 'yes',
                    'show_category' => ($settings['show_category'] ?? 'yes') === 'yes',
                    'show_button' => ($settings['show_button'] ?? 'yes') === 'yes',
                    'view_text' => $view_text,
                ];
                include PP_PATH . 'templates/parts/project-card.php';
                unset($pp_card_context);

                if ($is_slider) {
                    echo '</div>';
                }
            }
            wp_reset_postdata();
        }

        if ($is_slider) {
            echo '</div>';
        }

        echo '</div>';

        if ($is_slider) {
            echo '<button type="button" class="pp-slider-nav pp-slider-next" aria-label="' . esc_attr__('Next', 'projects-plugin') . '">';
            echo '<img src="' . esc_url(PP_URL . 'public/caret-left.svg') . '" alt="" aria-hidden="true">';
            echo '</button>';
            echo '</div>';
        }

        $this->render_pagination($q, $settings['pagination'] ?? 'classic', $load_more_text, $is_slider);

        echo '</div>';
    }

    private function render_filters($all_text) {
        $terms = get_terms(['taxonomy' => 'project_category', 'hide_empty' => false]);
        if (is_wp_error($terms) || empty($terms)) {
            return;
        }

        echo '<div class="pp-projects-filters">';
        echo '<button class="pp-filter-btn active" data-category="" data-category-id="">' . esc_html($all_text) . '</button>';
        foreach ($terms as $term) {
            echo '<button class="pp-filter-btn" data-category="' . esc_attr($term->slug) . '" data-category-id="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</button>';
        }
        echo '</div>';
    }

    private function render_pagination($query, $mode, $load_more_text, $is_slider = false) {
        if ($is_slider) {
            return;
        }

        if ($mode === 'classic') {
            echo '<div class="pp-pagination">';
            echo paginate_links([
                'total' => max(1, $query->max_num_pages),
                'current' => max(1, get_query_var('paged', 1)),
            ]);
            echo '</div>';
            return;
        }

        if ($mode === 'load_more') {
            echo '<button class="pp-load-more button">' . esc_html($load_more_text) . '</button>';
            return;
        }

        if ($mode === 'infinite') {
            echo '<div class="pp-infinite-trigger" aria-hidden="true"></div>';
        }
    }
}
