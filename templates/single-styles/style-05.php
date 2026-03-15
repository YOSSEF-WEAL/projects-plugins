<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-05">
    <header class="pp-l05-hero <?php echo pp_single_has_thumbnail() ? 'has-image' : 'no-image'; ?>">
        <?php pp_single_render_thumbnail('full'); ?>
    </header>

    <section class="pp-l05-floating-card">
        <?php if (pp_single_primary_term_name('') !== '') : ?><span class="pp-l05-term"><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
        <?php if (pp_single_location_label('') !== '') : ?><div class="pp-l05-location-text"><?php echo esc_html(pp_single_location_label('')); ?></div><?php endif; ?>
    </section>

    <section class="pp-l05-main">
        <?php if (pp_single_has_gallery()) : ?>
            <div class="pp-l05-gallery"><?php pp_single_render_gallery(); ?></div>
        <?php endif; ?>
        <?php if (pp_single_has_content()) : ?>
            <div class="pp-l05-content"><?php pp_single_render_content(); ?></div>
        <?php endif; ?>
    </section>

    <?php if (pp_single_has_location()) : ?>
        <footer class="pp-l05-footer">
            <?php pp_single_render_location_link(__('عرض الموقع على خرائط جوجل', 'projects-plugin')); ?>
        </footer>
    <?php endif; ?>
</article>
