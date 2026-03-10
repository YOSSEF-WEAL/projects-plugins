<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

class PP_Latest_Projects_Widget extends Widget_Base {
    public function get_name() {
        return 'pp_latest_projects_widget';
    }

    public function get_title() {
        return __('Latest Projects', 'projects-plugin');
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $enabled_views = PP_Helpers::get_setting('enabled_views', ['grid', 'masonry', 'slider', 'list']);
        $layout_options = ['grid' => __('Grid', 'projects-plugin')];
        if (in_array('slider', (array) $enabled_views, true)) {
            $layout_options['slider'] = __('Slider', 'projects-plugin');
        }

        $this->start_controls_section('content_section', [
            'label' => __('Content', 'projects-plugin'),
        ]);

        $this->add_control('count', [
            'label' => __('Projects Count', 'projects-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'default' => 4,
        ]);

        $this->add_control('category', [
            'label' => __('Category (optional)', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['' => __('All', 'projects-plugin')] + $this->get_terms_options(),
        ]);

        $this->add_control('layout', [
            'label' => __('Layout', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => $layout_options,
        ]);

        $this->add_control('columns_desktop', [
            'label' => __('Columns (Desktop)', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
            'condition' => ['layout' => 'grid'],
        ]);

        $this->add_control('columns_tablet', [
            'label' => __('Columns (Tablet)', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => '2',
            'options' => ['1' => '1', '2' => '2', '3' => '3'],
            'condition' => ['layout' => 'grid'],
        ]);

        $this->add_control('columns_mobile', [
            'label' => __('Columns (Mobile)', 'projects-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => '1',
            'options' => ['1' => '1', '2' => '2'],
            'condition' => ['layout' => 'grid'],
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
            'condition' => ['layout' => 'grid'],
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
            'condition' => ['layout' => 'grid'],
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

        $this->add_control('show_title', ['label' => __('Show Title', 'projects-plugin'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_excerpt', ['label' => __('Show Excerpt', 'projects-plugin'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_category', ['label' => __('Show Category', 'projects-plugin'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_button', ['label' => __('Show Button', 'projects-plugin'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);

        $this->add_control('text_view_project', [
            'label' => __('View Project Label', 'projects-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => __('View Project', 'projects-plugin'),
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_section', [
            'label' => __('Style', 'projects-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label' => __('Card Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-project-card' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('title_color', [
            'label' => __('Title Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-title a' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('text_color', [
            'label' => __('Excerpt Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-excerpt' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('category_color', [
            'label' => __('Category Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-category' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('button_bg', [
            'label' => __('Button Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-btn' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Button Text Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-btn' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('card_button_full_width', [
            'label' => __('Card Button Full Width', 'projects-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'display: block; width: 100%; text-align: center;',
            ],
            'condition' => ['show_button' => 'yes'],
        ]);

        $this->add_control('button_border_color', [
            'label' => __('Button Border Color', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .pp-card-btn' => 'border-color: {{VALUE}}; border-style: solid;'],
        ]);

        $this->add_control('slider_nav_bg', [
            'label' => __('Slider Arrow Background', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['layout' => 'slider'],
        ]);

        $this->add_control('slider_nav_border', [
            'label' => __('Slider Arrow Border', 'projects-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pp-slider-nav' => 'border-color: {{VALUE}};',
            ],
            'condition' => ['layout' => 'slider'],
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

        $this->add_responsive_control('button_padding', [
            'label' => __('Button Padding', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('button_radius', [
            'label' => __('Button Border Radius', 'projects-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pp-card-btn, {{WRAPPER}} .pp-slider-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
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
        $args = [
            'post_type' => 'project',
            'post_status' => 'publish',
            'posts_per_page' => max(1, absint($settings['count'] ?? 4)),
        ];

        if (!empty($settings['category'])) {
            $args['tax_query'] = [[
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => $settings['category'],
            ]];
        }

        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
        $is_slider = $layout === 'slider';
        $cols_desktop = $settings['columns_desktop'] ?? '3';
        $cols_tablet = $settings['columns_tablet'] ?? '2';
        $cols_mobile = $settings['columns_mobile'] ?? '1';
        $align_choice = $settings['items_align'] ?? 'start';
        $align_value = in_array($align_choice, ['start', 'center', 'end', 'stretch'], true) ? $align_choice : 'start';
        $justify_choice = $settings['items_justify'] ?? 'start';
        $justify_value = in_array($justify_choice, ['start', 'center', 'end', 'space-between', 'space-around', 'space-evenly', 'stretch'], true) ? $justify_choice : 'start';
        $justify_items_value = in_array($justify_value, ['start', 'center', 'end', 'stretch'], true) ? $justify_value : 'stretch';
        $button_text = !empty($settings['text_view_project']) ? $settings['text_view_project'] : __('View Project', 'projects-plugin');
        $image_size = !empty($settings['thumbnail_size']) ? sanitize_key($settings['thumbnail_size']) : 'large';
        $slider_cards_desktop = max(1, absint($settings['slider_cards_desktop'] ?? 3));
        $slider_cards_tablet = max(1, absint($settings['slider_cards_tablet'] ?? 2));
        $slider_cards_mobile = max(1, absint($settings['slider_cards_mobile'] ?? 1));

        $q = new WP_Query($args);

        echo '<div class="pp-latest-projects-widget" data-layout="' . esc_attr($layout) . '">';

        if ($is_slider) {
            echo '<div class="pp-slider-shell">';
            echo '<button type="button" class="pp-slider-nav pp-slider-prev" aria-label="' . esc_attr__('Previous', 'projects-plugin') . '">';
            echo '<img src="' . esc_url(PP_URL . 'public/caret-right.svg') . '" alt="" aria-hidden="true">';
            echo '</button>';
        }

        $classes = 'pp-latest-projects pp-layout-' . esc_attr($layout);
        if ($is_slider) {
            $classes .= ' pp-slider-track swiper';
        } else {
            $classes .= ' pp-cols-' . esc_attr($cols_desktop);
        }

        $style_vars = '';
        if (!$is_slider) {
            $style_vars = ' style="--pp-cols-desktop:' . esc_attr($cols_desktop) . ';--pp-cols-tablet:' . esc_attr($cols_tablet) . ';--pp-cols-mobile:' . esc_attr($cols_mobile) . ';--pp-justify-items:' . esc_attr($justify_items_value) . ';--pp-align-items:' . esc_attr($align_value) . ';--pp-justify-content:' . esc_attr($justify_value) . ';"';
        }

        echo '<div class="' . esc_attr($classes) . '"' . $style_vars;
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
                    'view_text' => $button_text,
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

        echo '</div>';
    }
}
