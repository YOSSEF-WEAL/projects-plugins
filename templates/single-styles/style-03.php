<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-03">
    <header class="pp-l03-head">
        <?php if (pp_single_primary_term_name('') !== '') : ?><span class="pp-l03-term"><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
    </header>

    <section class="pp-l03-grid">
        <?php if (pp_single_has_gallery()) : ?>
            <aside class="pp-l03-gallery"><?php pp_single_render_gallery(); ?></aside>
        <?php endif; ?>

        <?php if (pp_single_has_content()) : ?>
            <div class="pp-l03-content">
                <h2><?php esc_html_e('وصف المشروع', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php pp_single_render_content(); ?></div>
            </div>
        <?php endif; ?>
    </section>

    <?php if (pp_single_has_location()) : ?>
        <footer class="pp-l03-location">
            <strong><?php echo esc_html(pp_single_location_label(__('Project Location', 'projects-plugin'))); ?></strong>
            <?php pp_single_render_location_link(__('عرض الموقع', 'projects-plugin'), ''); ?>
        </footer>
    <?php endif; ?>
</article>
