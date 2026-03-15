<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-06">
    <header class="pp-l06-top">
        <div>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
        </div>
        <div class="pp-l06-facts">
            <div><strong><?php esc_html_e('التصنيف', 'projects-plugin'); ?></strong><span><?php echo esc_html(pp_single_primary_term_name('-')); ?></span></div>
            <div><strong><?php esc_html_e('الموقع', 'projects-plugin'); ?></strong><span><?php echo esc_html(pp_single_location_label('-')); ?></span></div>
            <div><strong><?php esc_html_e('الإحداثيات', 'projects-plugin'); ?></strong><span><?php echo esc_html(pp_single_coordinates_text('-')); ?></span></div>
        </div>
    </header>

    <section class="pp-l06-grid">
        <?php if (pp_single_has_content()) : ?><div class="pp-l06-content"><?php pp_single_render_content(); ?></div><?php endif; ?>
        <?php if (pp_single_has_gallery()) : ?><aside class="pp-l06-gallery"><?php pp_single_render_gallery(); ?></aside><?php endif; ?>
    </section>
</article>
