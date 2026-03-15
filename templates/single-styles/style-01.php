<?php
if (!defined('ABSPATH')) {
    exit;
}

$has_content = trim((string) get_the_content()) !== '';
$map_src = '';
if ($has_location) {
    $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
    $map_src = 'https://www.google.com/maps/embed/v1/view?zoom=14&center=' . rawurlencode($location_lat . ',' . $location_lng);
    if (!empty($api_key)) {
        $map_src .= '&key=' . rawurlencode($api_key);
    }
}
?>
<article class="pp-layout pp-layout-01">
    <header class="pp-l01-hero <?php echo has_post_thumbnail() ? 'has-image' : 'no-image'; ?>">
        <?php if (has_post_thumbnail()) : ?>
            <div class="pp-l01-hero-bg"><?php the_post_thumbnail('full'); ?></div>
        <?php endif; ?>
        <div class="pp-l01-hero-overlay"></div>
        <div class="pp-l01-hero-content">
            <?php if ($primary_term) : ?><span class="pp-l01-badge"><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
            <h1 class="pp-l01-title"><?php the_title(); ?></h1>
            <?php if (!empty($project_excerpt)) : ?><p class="pp-l01-excerpt"><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
        </div>
    </header>

    <section class="pp-l01-body">
        <?php if ($has_content) : ?>
            <div class="pp-l01-content">
                <h2><?php esc_html_e('عن المشروع', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php the_content(); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($gallery_ids)) : ?>
            <div class="pp-l01-gallery">
                <?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($has_location) : ?>
        <section class="pp-l01-location">
            <div class="pp-l01-location-copy">
                <h2><?php esc_html_e('الموقع الجغرافي', 'projects-plugin'); ?></h2>
                <?php if (!empty($location_label)) : ?><p><?php echo esc_html($location_label); ?></p><?php endif; ?>
                <a href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link"><?php esc_html_e('عرض الموقع على خرائط جوجل', 'projects-plugin'); ?></a>
            </div>
            <div class="pp-l01-location-map"><iframe title="project-location-map" width="100%" height="380" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($map_src); ?>"></iframe></div>
        </section>
    <?php endif; ?>
</article>
