<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('pp_single_get_ctx')) {
    function pp_single_get_ctx() {
        global $pp_single_ctx;
        return is_array($pp_single_ctx) ? $pp_single_ctx : [];
    }
}

if (!function_exists('pp_single_get')) {
    function pp_single_get($key, $default = null) {
        $ctx = pp_single_get_ctx();
        return array_key_exists($key, $ctx) ? $ctx[$key] : $default;
    }
}

if (!function_exists('pp_single_has_content')) {
    function pp_single_has_content() {
        $has_content = (bool) pp_single_get('has_content', false);
        return $has_content || pp_single_is_elementor_edit_mode();
    }
}

if (!function_exists('pp_single_render_content')) {
    function pp_single_render_content() {
        $has_real_content = (bool) pp_single_get('has_real_content', false);
        if ($has_real_content) {
            the_content();
            return;
        }

        if (!pp_single_is_elementor_edit_mode()) {
            return;
        }
        ?>
        <div class="pp-editor-placeholder pp-editor-placeholder-content">
            <?php esc_html_e('Project content area (add Elementor widgets here).', 'projects-plugin'); ?>
        </div>
        <?php
    }
}

if (!function_exists('pp_single_is_elementor_edit_mode')) {
    function pp_single_is_elementor_edit_mode() {
        if (!did_action('elementor/loaded')) {
            return false;
        }

        if (isset($_GET['elementor-preview'])) {
            return true;
        }

        if (class_exists('\Elementor\Plugin')) {
            $elementor = \Elementor\Plugin::$instance;
            if ($elementor) {
                if (!empty($elementor->preview) && method_exists($elementor->preview, 'is_preview_mode') && $elementor->preview->is_preview_mode()) {
                    return true;
                }

                if (!empty($elementor->editor) && method_exists($elementor->editor, 'is_edit_mode') && $elementor->editor->is_edit_mode()) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('pp_single_has_gallery')) {
    function pp_single_has_gallery() {
        $gallery_ids = pp_single_get('gallery_ids', []);
        return (is_array($gallery_ids) && !empty($gallery_ids)) || pp_single_is_elementor_edit_mode();
    }
}

if (!function_exists('pp_single_render_gallery')) {
    function pp_single_render_gallery() {
        $gallery_ids = pp_single_get('gallery_ids', []);
        if (is_array($gallery_ids) && !empty($gallery_ids)) {
            $args = ['ids' => $gallery_ids];
            include PP_PATH . 'templates/parts/slider.php';
            return;
        }

        if (!pp_single_is_elementor_edit_mode()) {
            return;
        }
        ?>
        <div class="pp-editor-placeholder pp-editor-placeholder-gallery">
            <?php esc_html_e('Project gallery placeholder (add gallery images in project settings).', 'projects-plugin'); ?>
        </div>
        <?php
    }
}

if (!function_exists('pp_single_has_location')) {
    function pp_single_has_location() {
        return (bool) pp_single_get('has_location', false) || pp_single_is_elementor_edit_mode();
    }
}

if (!function_exists('pp_single_can_show_embed_map')) {
    function pp_single_can_show_embed_map() {
        $can_show_real_map = (bool) pp_single_get('can_show_embed_map', false) && !empty(pp_single_get('location_map_src', ''));
        return $can_show_real_map || pp_single_is_elementor_edit_mode();
    }
}

if (!function_exists('pp_single_render_location_map')) {
    function pp_single_render_location_map($height = 320, $title = 'project-location-map') {
        $has_real_map = (bool) pp_single_get('can_show_embed_map', false) && !empty(pp_single_get('location_map_src', ''));
        if ($has_real_map) {
            $h = max(120, (int) $height);
            ?>
            <iframe title="<?php echo esc_attr($title); ?>" width="100%" height="<?php echo esc_attr((string) $h); ?>" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url((string) pp_single_get('location_map_src', '')); ?>"></iframe>
            <?php
            return;
        }

        if (!pp_single_is_elementor_edit_mode()) {
            return;
        }
        ?>
        <div class="pp-editor-placeholder pp-editor-placeholder-map" style="min-height:<?php echo esc_attr((string) max(120, (int) $height)); ?>px;">
            <?php esc_html_e('Project map placeholder (set location + API key to show map).', 'projects-plugin'); ?>
        </div>
        <?php
    }
}

if (!function_exists('pp_single_render_location_link')) {
    function pp_single_render_location_link($text = '', $class = 'pp-location-link') {
        $label = trim((string) $text);
        if ($label === '') {
            $label = __('View Location', 'projects-plugin');
        }

        if (!pp_single_has_location()) {
            return;
        }

        $has_real_location = (bool) pp_single_get('has_location', false);
        if (!$has_real_location && pp_single_is_elementor_edit_mode()) {
            ?>
            <span class="pp-editor-placeholder-link"><?php echo esc_html($label); ?></span>
            <?php
            return;
        }

        $class = trim((string) $class);
        if ($class !== '') {
            ?>
            <a href="<?php echo esc_url((string) pp_single_get('location_link', '#')); ?>" target="_blank" rel="noopener noreferrer" class="<?php echo esc_attr($class); ?>">
                <?php echo esc_html($label); ?>
            </a>
            <?php
            return;
        }
        ?>
        <a href="<?php echo esc_url((string) pp_single_get('location_link', '#')); ?>" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html($label); ?>
        </a>
        <?php
    }
}

if (!function_exists('pp_single_primary_term_name')) {
    function pp_single_primary_term_name($default = '') {
        $term = pp_single_get('primary_term');
        if ($term && !is_wp_error($term) && isset($term->name)) {
            return (string) $term->name;
        }
        return (string) $default;
    }
}

if (!function_exists('pp_single_location_label')) {
    function pp_single_location_label($default = '') {
        $label = (string) pp_single_get('location_label', '');
        return $label !== '' ? $label : (string) $default;
    }
}

if (!function_exists('pp_single_has_thumbnail')) {
    function pp_single_has_thumbnail() {
        $post_id = (int) pp_single_get('post_id', 0);
        return $post_id > 0 && has_post_thumbnail($post_id);
    }
}

if (!function_exists('pp_single_render_thumbnail')) {
    function pp_single_render_thumbnail($size = 'large') {
        $post_id = (int) pp_single_get('post_id', 0);
        if ($post_id <= 0 || !has_post_thumbnail($post_id)) {
            return;
        }
        echo get_the_post_thumbnail($post_id, $size);
    }
}

if (!function_exists('pp_single_coordinates_text')) {
    function pp_single_coordinates_text($fallback = '-') {
        $lat = (string) pp_single_get('location_lat', '');
        $lng = (string) pp_single_get('location_lng', '');
        $short = (string) pp_single_get('location_shortlink', '');
        $has_valid = (bool) pp_single_get('has_valid_coordinates', false);
        if ($has_valid) {
            return $lat . ', ' . $lng;
        }
        if ($short !== '') {
            return __('Maps link provided', 'projects-plugin');
        }
        return (string) $fallback;
    }
}
