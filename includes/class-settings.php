<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Settings {
    public static function get_single_style_options() {
        return [
            'style-01' => '01',
            'style-02' => '02',
            'style-03' => '03',
            'style-04' => '04',
            'style-05' => '05',
            'style-06' => '06',
            'style-07' => '07',
            'style-08' => '08',
            'style-09' => '09',
            'style-10' => '10',
        ];
    }

    public static function menu() {
        add_menu_page(
            __('Projects Plugin', 'projects-plugin'),
            __('Projects Plugin', 'projects-plugin'),
            'manage_options',
            'pp-settings',
            [__CLASS__, 'render_page'],
            'dashicons-admin-generic',
            58
        );
    }

    public static function register() {
        register_setting('pp_settings_group', 'pp_settings', [
            'sanitize_callback' => [__CLASS__, 'sanitize'],
        ]);

        add_settings_section('pp_general', __('General Settings', 'projects-plugin'), '__return_false', 'pp-settings-general');
        add_settings_section('pp_single_project', __('Single Project Layout', 'projects-plugin'), '__return_false', 'pp-settings-single');

        add_settings_field('location_display_default', __('Default Location Display', 'projects-plugin'), [__CLASS__, 'field_location_display'], 'pp-settings-general', 'pp_general');
        add_settings_field('google_maps_api_key', __('Google Maps API Key', 'projects-plugin'), [__CLASS__, 'field_maps_key'], 'pp-settings-general', 'pp_general');
        add_settings_field('projects_per_page', __('Default Projects Per Page', 'projects-plugin'), [__CLASS__, 'field_projects_per_page'], 'pp-settings-general', 'pp_general');
        add_settings_field('pagination_per_page', __('Pagination Items Per Page', 'projects-plugin'), [__CLASS__, 'field_pagination_per_page'], 'pp-settings-general', 'pp_general');
        add_settings_field('enabled_views', __('Enable View Modes', 'projects-plugin'), [__CLASS__, 'field_enabled_views'], 'pp-settings-general', 'pp_general');
        add_settings_field('enable_single_template', __('Use Plugin Single Template', 'projects-plugin'), [__CLASS__, 'field_enable_single_template'], 'pp-settings-single', 'pp_single_project');
        add_settings_field('enable_archive_template', __('Use Plugin Archive Template', 'projects-plugin'), [__CLASS__, 'field_enable_archive_template'], 'pp-settings-single', 'pp_single_project');
        add_settings_field('single_project_style', __('Single Project Style', 'projects-plugin'), [__CLASS__, 'field_single_project_style'], 'pp-settings-single', 'pp_single_project');
    }

    public static function sanitize($input) {
        $clean = [];
        $clean['location_display_default'] = in_array(($input['location_display_default'] ?? 'link'), ['link', 'map'], true) ? $input['location_display_default'] : 'link';
        $clean['google_maps_api_key'] = sanitize_text_field($input['google_maps_api_key'] ?? '');
        $clean['projects_per_page'] = max(1, absint($input['projects_per_page'] ?? 9));
        $clean['pagination_per_page'] = max(1, absint($input['pagination_per_page'] ?? 9));

        $allowed_views = ['grid', 'masonry', 'slider', 'list'];
        $views = isset($input['enabled_views']) && is_array($input['enabled_views']) ? array_map('sanitize_text_field', $input['enabled_views']) : [];
        $clean['enabled_views'] = array_values(array_intersect($allowed_views, $views));
        $clean['enable_single_template'] = !empty($input['enable_single_template']) ? 1 : 0;
        $clean['enable_archive_template'] = !empty($input['enable_archive_template']) ? 1 : 0;
        $style_options = self::get_single_style_options();
        $single_style = sanitize_key($input['single_project_style'] ?? 'style-01');
        $clean['single_project_style'] = array_key_exists($single_style, $style_options) ? $single_style : 'style-01';

        return $clean;
    }

    public static function render_page() {
        require PP_PATH . 'admin/settings-page.php';
    }

    private static function settings() {
        return PP_Helpers::get_settings();
    }

    public static function field_location_display() {
        $settings = self::settings();
        ?>
        <select name="pp_settings[location_display_default]">
            <option value="link" <?php selected($settings['location_display_default'], 'link'); ?>><?php esc_html_e('Link', 'projects-plugin'); ?></option>
            <option value="map" <?php selected($settings['location_display_default'], 'map'); ?>><?php esc_html_e('Map', 'projects-plugin'); ?></option>
        </select>
        <?php
    }

    public static function field_maps_key() {
        $settings = self::settings();
        ?>
        <input type="text" class="regular-text" name="pp_settings[google_maps_api_key]" value="<?php echo esc_attr($settings['google_maps_api_key']); ?>">
        <?php
    }

    public static function field_projects_per_page() {
        $settings = self::settings();
        ?>
        <input type="number" min="1" name="pp_settings[projects_per_page]" value="<?php echo esc_attr($settings['projects_per_page']); ?>">
        <?php
    }

    public static function field_pagination_per_page() {
        $settings = self::settings();
        ?>
        <input type="number" min="1" name="pp_settings[pagination_per_page]" value="<?php echo esc_attr($settings['pagination_per_page']); ?>">
        <?php
    }

    public static function field_enabled_views() {
        $settings = self::settings();
        $selected = is_array($settings['enabled_views']) ? $settings['enabled_views'] : [];
        $views = ['grid', 'masonry', 'slider', 'list'];
        foreach ($views as $view) {
            ?>
            <label style="display:block;margin-bottom:6px;">
                <input type="checkbox" name="pp_settings[enabled_views][]" value="<?php echo esc_attr($view); ?>" <?php checked(in_array($view, $selected, true)); ?>>
                <?php echo esc_html(ucfirst($view)); ?>
            </label>
            <?php
        }
    }

    public static function field_single_project_style() {
        $settings = self::settings();
        $selected = isset($settings['single_project_style']) ? sanitize_key((string) $settings['single_project_style']) : 'style-01';
        $options = self::get_single_style_options();
        ?>
        <div class="pp-style-picker" role="radiogroup" aria-label="<?php esc_attr_e('Single project style picker', 'projects-plugin'); ?>">
            <?php foreach ($options as $value => $label) : ?>
                <label class="pp-style-choice">
                    <input
                        type="radio"
                        name="pp_settings[single_project_style]"
                        value="<?php echo esc_attr($value); ?>"
                        <?php checked($selected, $value); ?>
                    >
                    <span class="pp-style-choice-card">
                        <span class="pp-style-choice-check" aria-hidden="true"></span>
                        <span class="pp-style-choice-preview pp-preview-<?php echo esc_attr($value); ?>" aria-hidden="true">
                            <?php self::render_single_style_preview($value); ?>
                        </span>
                        <span class="pp-style-choice-title"><?php echo esc_html($label); ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description">
            <?php esc_html_e('Choose the visual style for the single project page layout.', 'projects-plugin'); ?>
        </p>
        <?php
    }

    public static function field_enable_single_template() {
        $settings = self::settings();
        $enabled = !empty($settings['enable_single_template']);
        ?>
        <label>
            <input type="checkbox" name="pp_settings[enable_single_template]" value="1" <?php checked($enabled); ?>>
            <?php esc_html_e('Use plugin built-in single project layout.', 'projects-plugin'); ?>
        </label>
        <p class="description">
            <?php esc_html_e('Disable this if you want your theme or Elementor templates to control single project pages.', 'projects-plugin'); ?>
        </p>
        <?php
    }

    public static function field_enable_archive_template() {
        $settings = self::settings();
        $enabled = !empty($settings['enable_archive_template']);
        ?>
        <label>
            <input type="checkbox" name="pp_settings[enable_archive_template]" value="1" <?php checked($enabled); ?>>
            <?php esc_html_e('Use plugin built-in archive layout.', 'projects-plugin'); ?>
        </label>
        <p class="description">
            <?php esc_html_e('Disable this if you want your theme or Elementor templates to control project archive/category pages.', 'projects-plugin'); ?>
        </p>
        <?php
    }

    private static function render_single_style_preview($style_key) {
        $blocks_map = [
            'style-01' => ['hero', 'content', 'gallery', 'location-left', 'location-right'],
            'style-02' => ['intro-left', 'intro-right', 'gallery', 'content', 'location'],
            'style-03' => ['head', 'sidebar', 'content', 'footer'],
            'style-04' => ['banner', 'step-1', 'step-2', 'step-3'],
            'style-05' => ['cover', 'floating', 'content', 'gallery', 'footer'],
            'style-06' => ['top-main', 'top-side', 'content', 'gallery'],
            'style-07' => ['head', 'strip', 'content', 'sticky'],
            'style-08' => ['sidebar', 'content', 'gallery'],
            'style-09' => ['head', 'mosaic-left', 'mosaic-right', 'content'],
            'style-10' => ['hero-left', 'hero-right', 'facts', 'content', 'gallery', 'map'],
        ];

        $blocks = isset($blocks_map[$style_key]) ? $blocks_map[$style_key] : $blocks_map['style-01'];

        echo '<span class="pp-mini pp-mini-' . esc_attr($style_key) . '">';
        foreach ($blocks as $block) {
            echo '<span class="pp-mini-block pp-mini-' . esc_attr($block) . '"></span>';
        }
        echo '</span>';
    }
}
