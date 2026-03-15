<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$label = get_post_meta($post_id, '_pp_location_label', true);
$lat = get_post_meta($post_id, '_pp_location_lat', true);
$lng = get_post_meta($post_id, '_pp_location_lng', true);
$shortlink = get_post_meta($post_id, '_pp_location_shortlink', true);
$display = PP_Helpers::get_project_location_display($post_id);
$api_key = PP_Helpers::get_setting('google_maps_api_key', '');
$has_valid_coordinates = PP_Helpers::is_valid_coordinates($lat, $lng);
$link = PP_Helpers::get_google_maps_link($lat, $lng, $label, $shortlink);

if (!$has_valid_coordinates && empty($shortlink) && empty($label)) {
    return;
}

if ($display === 'link' || empty($api_key) || !$has_valid_coordinates) :
    ?>
    <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link">
        <?php echo esc_html($label ?: __('View Location', 'projects-plugin')); ?>
    </a>
<?php else :
    $src = PP_Helpers::get_google_embed_src($lat, $lng, $api_key, 14);
    if ($src === '') :
        ?>
        <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link">
            <?php echo esc_html($label ?: __('View Location', 'projects-plugin')); ?>
        </a>
    <?php else : ?>
    ?>
    <div class="pp-location-map">
        <iframe title="project-location" width="100%" height="300" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($src); ?>"></iframe>
    </div>
    <?php endif; ?>
<?php endif; ?>
