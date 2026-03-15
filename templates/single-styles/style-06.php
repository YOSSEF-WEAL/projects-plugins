<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
$terms = get_the_terms($post_id, 'project_category');
?>
<article class="pp-layout pp-layout-06">
    <header class="pp-l06-top">
        <div>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
        </div>
        <div class="pp-l06-facts">
            <div><strong><?php esc_html_e('التصنيف', 'projects-plugin'); ?></strong><span><?php echo !empty($terms) && !is_wp_error($terms) ? esc_html($terms[0]->name) : '-'; ?></span></div>
            <div><strong><?php esc_html_e('الموقع', 'projects-plugin'); ?></strong><span><?php echo esc_html($location_label ?: '-'); ?></span></div>
            <div><strong><?php esc_html_e('الإحداثيات', 'projects-plugin'); ?></strong><span><?php echo esc_html($location_lat && $location_lng ? ($location_lat . ', ' . $location_lng) : '-'); ?></span></div>
        </div>
    </header>

    <section class="pp-l06-grid">
        <?php if ($has_content) : ?><div class="pp-l06-content"><?php the_content(); ?></div><?php endif; ?>
        <?php if (!empty($gallery_ids)) : ?><aside class="pp-l06-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></aside><?php endif; ?>
    </section>
</article>
