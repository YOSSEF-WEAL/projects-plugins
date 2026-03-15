<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
?>
<article class="pp-layout pp-layout-03">
    <header class="pp-l03-head">
        <?php if ($primary_term) : ?><span class="pp-l03-term"><?php echo esc_html($primary_term->name); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
    </header>

    <section class="pp-l03-grid">
        <?php if (!empty($gallery_ids)) : ?>
            <aside class="pp-l03-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></aside>
        <?php endif; ?>

        <?php if ($has_content) : ?>
            <div class="pp-l03-content">
                <h2><?php esc_html_e('وصف المشروع', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php the_content(); ?></div>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($has_location) : ?>
        <footer class="pp-l03-location">
            <strong><?php echo esc_html($location_label ?: __('Project Location', 'projects-plugin')); ?></strong>
            <a href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('عرض الموقع', 'projects-plugin'); ?></a>
        </footer>
    <?php endif; ?>
</article>
