<?php
if (!defined('ABSPATH')) {
    exit;
}

$has_content = trim((string) get_the_content()) !== '';
$map_src = '';
if ($has_location) {
    $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
    $map_src = 'https://www.google.com/maps/embed/v1/view?zoom=13&center=' . rawurlencode($location_lat . ',' . $location_lng);
    if (!empty($api_key)) {
        $map_src .= '&key=' . rawurlencode($api_key);
    }
}
?>
<article class="pp-layout pp-layout-02">
    <header class="pp-l02-hero">
        <div class="pp-l02-copy">
            <?php if ($primary_term) : ?><span class="pp-l02-kicker"><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
            <?php if (!empty($location_label)) : ?><div class="pp-l02-meta"><?php echo esc_html($location_label); ?></div><?php endif; ?>
        </div>
        <div class="pp-l02-cover">
            <?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?>
        </div>
    </header>

    <?php if (!empty($gallery_ids)) : ?>
        <section class="pp-l02-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></section>
    <?php endif; ?>

    <?php if ($has_content) : ?>
        <section class="pp-l02-content">
            <h2><?php esc_html_e('تفاصيل المشروع', 'projects-plugin'); ?></h2>
            <div class="pp-project-content"><?php the_content(); ?></div>
        </section>
    <?php endif; ?>

    <?php if ($has_location) : ?>
        <section class="pp-l02-location">
            <iframe title="project-location-map" width="100%" height="300" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($map_src); ?>"></iframe>
            <a href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link"><?php esc_html_e('فتح في خرائط جوجل', 'projects-plugin'); ?></a>
        </section>
    <?php endif; ?>
</article>
