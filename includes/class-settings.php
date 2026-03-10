<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Settings {
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

        add_settings_section('pp_general', __('General Settings', 'projects-plugin'), '__return_false', 'pp-settings');

        add_settings_field('location_display_default', __('Default Location Display', 'projects-plugin'), [__CLASS__, 'field_location_display'], 'pp-settings', 'pp_general');
        add_settings_field('google_maps_api_key', __('Google Maps API Key', 'projects-plugin'), [__CLASS__, 'field_maps_key'], 'pp-settings', 'pp_general');
        add_settings_field('projects_per_page', __('Default Projects Per Page', 'projects-plugin'), [__CLASS__, 'field_projects_per_page'], 'pp-settings', 'pp_general');
        add_settings_field('pagination_per_page', __('Pagination Items Per Page', 'projects-plugin'), [__CLASS__, 'field_pagination_per_page'], 'pp-settings', 'pp_general');
        add_settings_field('enabled_views', __('Enable View Modes', 'projects-plugin'), [__CLASS__, 'field_enabled_views'], 'pp-settings', 'pp_general');
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
}
