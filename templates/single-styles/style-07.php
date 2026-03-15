<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
?>
<article class="pp-layout pp-layout-07">
    <header class="pp-l07-head">
        <?php if ($primary_term) : ?><span><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
    </header>

    <?php if (!empty($gallery_ids)) : ?>
        <section class="pp-l07-strip"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></section>
    <?php endif; ?>

    <section class="pp-l07-columns">
        <?php if ($has_content) : ?><div class="pp-l07-content"><?php the_content(); ?></div><?php endif; ?>
        <aside class="pp-l07-side">
            <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
            <?php if (!empty($location_label)) : ?><p><?php echo esc_html($location_label); ?></p><?php endif; ?>
            <?php if ($has_location) : ?><a class="pp-location-link" href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('استكشف الموقع', 'projects-plugin'); ?></a><?php endif; ?>
        </aside>
    </section>
</article>
