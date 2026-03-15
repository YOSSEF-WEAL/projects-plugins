<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
?>
<article class="pp-layout pp-layout-08">
    <section class="pp-l08-wrap">
        <aside class="pp-l08-side">
            <h1><?php the_title(); ?></h1>
            <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
            <?php if ($primary_term) : ?><span class="pp-l08-chip"><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
            <?php if ($has_location) : ?><a class="pp-location-link" href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('الموقع', 'projects-plugin'); ?></a><?php endif; ?>
        </aside>

        <div class="pp-l08-main">
            <?php if ($has_content) : ?><section class="pp-l08-content"><?php the_content(); ?></section><?php endif; ?>
            <?php if (!empty($gallery_ids)) : ?><section class="pp-l08-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></section><?php endif; ?>
        </div>
    </section>
</article>
