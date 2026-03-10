<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$label = get_post_meta($post_id, '_pp_location_label', true);
$lat = get_post_meta($post_id, '_pp_location_lat', true);
$lng = get_post_meta($post_id, '_pp_location_lng', true);
$display = PP_Helpers::get_project_location_display($post_id);

if (empty($lat) || empty($lng)) {
    return;
}

if ($display === 'link') :
    $link = PP_Helpers::get_google_maps_link($lat, $lng, $label);
    ?>
    <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link">
        <?php echo esc_html($label ?: __('View Location', 'projects-plugin')); ?>
    </a>
<?php else :
    $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
    $src = 'https://www.google.com/maps/embed/v1/view?zoom=14&center=' . rawurlencode($lat . ',' . $lng);
    if (!empty($api_key)) {
        $src .= '&key=' . rawurlencode($api_key);
    }
    ?>
    <div class="pp-location-map">
        <iframe title="project-location" width="100%" height="300" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($src); ?>"></iframe>
    </div>
<?php endif; ?>
