<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Meta_Boxes {
    public static function register() {
        add_action('add_meta_boxes', [__CLASS__, 'add_project_meta_boxes']);
        add_action('save_post_project', [__CLASS__, 'save_project_meta']);

        add_action('project_category_add_form_fields', [__CLASS__, 'add_term_image_field']);
        add_action('project_category_edit_form_fields', [__CLASS__, 'edit_term_image_field']);
        add_action('created_project_category', [__CLASS__, 'save_term_image']);
        add_action('edited_project_category', [__CLASS__, 'save_term_image']);
    }

    public static function add_project_meta_boxes() {
        add_meta_box(
            'pp_project_details',
            __('Project Details', 'projects-plugin'),
            [__CLASS__, 'render_project_meta_box'],
            'project',
            'normal',
            'high'
        );
    }

    public static function render_project_meta_box($post) {
        wp_nonce_field('pp_save_project_meta', 'pp_project_meta_nonce');

        $gallery_ids = get_post_meta($post->ID, '_pp_gallery_ids', true);
        $location_label = get_post_meta($post->ID, '_pp_location_label', true);
        $location_lat = get_post_meta($post->ID, '_pp_location_lat', true);
        $location_lng = get_post_meta($post->ID, '_pp_location_lng', true);
        $location_display = get_post_meta($post->ID, '_pp_location_display', true);
        ?>
        <p>
            <label for="pp_gallery_ids"><strong><?php esc_html_e('Project Slider Images', 'projects-plugin'); ?></strong></label><br>
            <input type="hidden" id="pp_gallery_ids" name="pp_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
            <button type="button" class="button pp-select-gallery"><?php esc_html_e('Select Images', 'projects-plugin'); ?></button>
            <button type="button" class="button pp-clear-gallery"><?php esc_html_e('Clear', 'projects-plugin'); ?></button>
        </p>
        <div class="pp-gallery-preview">
            <?php
            if (!empty($gallery_ids)) {
                foreach (array_filter(array_map('absint', explode(',', $gallery_ids))) as $id) {
                    echo wp_get_attachment_image($id, 'thumbnail');
                }
            }
            ?>
        </div>

        <hr>

        <p>
            <label for="pp_location_label"><strong><?php esc_html_e('Location Title / Address', 'projects-plugin'); ?></strong></label><br>
            <input type="text" id="pp_location_label" name="pp_location_label" class="widefat" value="<?php echo esc_attr($location_label); ?>">
        </p>

        <p>
            <label for="pp_location_lat"><strong><?php esc_html_e('Latitude', 'projects-plugin'); ?></strong></label>
            <input type="text" id="pp_location_lat" name="pp_location_lat" value="<?php echo esc_attr($location_lat); ?>" class="widefat">
        </p>

        <p>
            <label for="pp_location_lng"><strong><?php esc_html_e('Longitude', 'projects-plugin'); ?></strong></label>
            <input type="text" id="pp_location_lng" name="pp_location_lng" value="<?php echo esc_attr($location_lng); ?>" class="widefat">
        </p>

        <p>
            <label for="pp_location_display"><strong><?php esc_html_e('Location Display', 'projects-plugin'); ?></strong></label><br>
            <select id="pp_location_display" name="pp_location_display">
                <option value=""><?php esc_html_e('Default from settings', 'projects-plugin'); ?></option>
                <option value="link" <?php selected($location_display, 'link'); ?>><?php esc_html_e('Link', 'projects-plugin'); ?></option>
                <option value="map" <?php selected($location_display, 'map'); ?>><?php esc_html_e('Map', 'projects-plugin'); ?></option>
            </select>
        </p>
        <?php
    }

    public static function save_project_meta($post_id) {
        if (!isset($_POST['pp_project_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pp_project_meta_nonce'])), 'pp_save_project_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = [
            '_pp_gallery_ids' => isset($_POST['pp_gallery_ids']) ? sanitize_text_field(wp_unslash($_POST['pp_gallery_ids'])) : '',
            '_pp_location_label' => isset($_POST['pp_location_label']) ? sanitize_text_field(wp_unslash($_POST['pp_location_label'])) : '',
            '_pp_location_lat' => isset($_POST['pp_location_lat']) ? sanitize_text_field(wp_unslash($_POST['pp_location_lat'])) : '',
            '_pp_location_lng' => isset($_POST['pp_location_lng']) ? sanitize_text_field(wp_unslash($_POST['pp_location_lng'])) : '',
            '_pp_location_display' => isset($_POST['pp_location_display']) ? sanitize_text_field(wp_unslash($_POST['pp_location_display'])) : '',
        ];

        foreach ($fields as $meta_key => $meta_value) {
            update_post_meta($post_id, $meta_key, $meta_value);
        }
    }

    public static function add_term_image_field() {
        ?>
        <div class="form-field term-group">
            <label for="pp_term_image_id"><?php esc_html_e('Category Image', 'projects-plugin'); ?></label>
            <input type="hidden" id="pp_term_image_id" name="pp_term_image_id" value="">
            <button type="button" class="button pp-term-image-upload"><?php esc_html_e('Upload Image', 'projects-plugin'); ?></button>
        </div>
        <?php
    }

    public static function edit_term_image_field($term) {
        $image_id = get_term_meta($term->term_id, 'pp_term_image_id', true);
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="pp_term_image_id"><?php esc_html_e('Category Image', 'projects-plugin'); ?></label></th>
            <td>
                <input type="hidden" id="pp_term_image_id" name="pp_term_image_id" value="<?php echo esc_attr($image_id); ?>">
                <button type="button" class="button pp-term-image-upload"><?php esc_html_e('Upload Image', 'projects-plugin'); ?></button>
                <button type="button" class="button pp-term-image-remove"><?php esc_html_e('Remove Image', 'projects-plugin'); ?></button>
                <div class="pp-term-image-preview">
                    <?php
                    if ($image_id) {
                        echo wp_get_attachment_image((int) $image_id, 'thumbnail');
                    }
                    ?>
                </div>
            </td>
        </tr>
        <?php
    }

    public static function save_term_image($term_id) {
        if (!current_user_can('manage_categories')) {
            return;
        }

        if (isset($_POST['pp_term_image_id'])) {
            update_term_meta($term_id, 'pp_term_image_id', absint($_POST['pp_term_image_id']));
        }
    }
}
