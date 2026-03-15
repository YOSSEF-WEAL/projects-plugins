<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
?>
<article class="pp-layout pp-layout-05">
    <header class="pp-l05-hero <?php echo has_post_thumbnail() ? 'has-image' : 'no-image'; ?>">
        <?php if (has_post_thumbnail()) { the_post_thumbnail('full'); } ?>
    </header>

    <section class="pp-l05-floating-card">
        <?php if ($primary_term) : ?><span class="pp-l05-term"><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
        <?php if (!empty($location_label)) : ?><div class="pp-l05-location-text"><?php echo esc_html($location_label); ?></div><?php endif; ?>
    </section>

    <section class="pp-l05-main">
        <?php if (!empty($gallery_ids)) : ?>
            <div class="pp-l05-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></div>
        <?php endif; ?>
        <?php if ($has_content) : ?>
            <div class="pp-l05-content"><?php the_content(); ?></div>
        <?php endif; ?>
    </section>

    <?php if ($has_location) : ?>
        <footer class="pp-l05-footer">
            <a class="pp-location-link" href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('عرض الموقع على خرائط جوجل', 'projects-plugin'); ?></a>
        </footer>
    <?php endif; ?>
</article>
