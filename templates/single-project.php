<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
require_once PP_PATH . 'templates/parts/single-components.php';

$single_style = sanitize_key((string) PP_Helpers::get_setting('single_project_style', 'style-01'));
$single_style_key = preg_match('/^style-\d{2}$/', $single_style) ? $single_style : 'style-01';
$single_style_class = 'pp-single-' . $single_style_key;
?>
<main class="pp-single-project pp-single-project-v2 <?php echo esc_attr($single_style_class); ?>" dir="rtl">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        global $pp_single_ctx;
        $post_id = get_the_ID();
        $gallery_ids = PP_Helpers::get_project_gallery_ids($post_id);
        $location_label = get_post_meta($post_id, '_pp_location_label', true);
        $location_lat = get_post_meta($post_id, '_pp_location_lat', true);
        $location_lng = get_post_meta($post_id, '_pp_location_lng', true);
        $location_shortlink = get_post_meta($post_id, '_pp_location_shortlink', true);
        $location_display_mode = PP_Helpers::get_project_location_display($post_id);
        $has_valid_coordinates = PP_Helpers::is_valid_coordinates($location_lat, $location_lng);
        $location_link = PP_Helpers::get_project_location_link($post_id, $location_lat, $location_lng, $location_label);
        $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
        $can_show_embed_map = $location_display_mode === 'map' && $has_valid_coordinates && !empty($api_key);
        $location_map_src = $can_show_embed_map ? PP_Helpers::get_google_embed_src($location_lat, $location_lng, $api_key, 14) : '';
        $has_location = $has_valid_coordinates || !empty($location_shortlink) || !empty($location_label);
        $terms = get_the_terms($post_id, 'project_category');
        $primary_term = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;
        $has_content = trim((string) get_the_content()) !== '';
        $project_excerpt = get_the_excerpt($post_id);
        if (empty($project_excerpt)) {
            $project_excerpt = PP_Helpers::get_excerpt($post_id, 22);
        }

        $pp_single_ctx = [
            'post_id' => $post_id,
            'gallery_ids' => $gallery_ids,
            'location_label' => $location_label,
            'location_lat' => $location_lat,
            'location_lng' => $location_lng,
            'location_shortlink' => $location_shortlink,
            'location_display_mode' => $location_display_mode,
            'has_valid_coordinates' => $has_valid_coordinates,
            'location_link' => $location_link,
            'can_show_embed_map' => $can_show_embed_map,
            'location_map_src' => $location_map_src,
            'has_location' => $has_location,
            'primary_term' => $primary_term,
            'project_excerpt' => $project_excerpt,
            'has_content' => $has_content,
        ];
        $single_template_file = PP_PATH . 'templates/single-styles/' . $single_style_key . '.php';
        if (!file_exists($single_template_file)) {
            $single_template_file = PP_PATH . 'templates/single-styles/style-01.php';
        }
        include $single_template_file;
        ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
