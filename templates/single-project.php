<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$single_style = sanitize_key((string) PP_Helpers::get_setting('single_project_style', 'style-01'));
$single_style_key = preg_match('/^style-\d{2}$/', $single_style) ? $single_style : 'style-01';
$single_style_class = 'pp-single-' . $single_style_key;
?>
<main class="pp-single-project pp-single-project-v2 <?php echo esc_attr($single_style_class); ?>" dir="rtl">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $post_id = get_the_ID();
        $gallery_ids = PP_Helpers::get_project_gallery_ids($post_id);
        $location_label = get_post_meta($post_id, '_pp_location_label', true);
        $location_lat = get_post_meta($post_id, '_pp_location_lat', true);
        $location_lng = get_post_meta($post_id, '_pp_location_lng', true);
        $has_location = !empty($location_lat) && !empty($location_lng);
        $terms = get_the_terms($post_id, 'project_category');
        $primary_term = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;
        $project_excerpt = get_the_excerpt($post_id);
        if (empty($project_excerpt)) {
            $project_excerpt = PP_Helpers::get_excerpt($post_id, 22);
        }
        $single_template_file = PP_PATH . 'templates/single-styles/' . $single_style_key . '.php';
        if (!file_exists($single_template_file)) {
            $single_template_file = PP_PATH . 'templates/single-styles/style-01.php';
        }
        include $single_template_file;
        ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
