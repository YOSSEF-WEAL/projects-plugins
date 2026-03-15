<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-01">
    <header class="pp-l01-hero <?php echo pp_single_has_thumbnail() ? 'has-image' : 'no-image'; ?>">
        <?php if (pp_single_has_thumbnail()) : ?>
            <div class="pp-l01-hero-bg"><?php pp_single_render_thumbnail('full'); ?></div>
        <?php endif; ?>
        <div class="pp-l01-hero-overlay"></div>
        <div class="pp-l01-hero-content">
            <?php if (pp_single_primary_term_name('') !== '') : ?><span class="pp-l01-badge"><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
            <h1 class="pp-l01-title"><?php the_title(); ?></h1>
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p class="pp-l01-excerpt"><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
        </div>
    </header>

    <section class="pp-l01-body">
        <?php if (pp_single_has_content()) : ?>
            <div class="pp-l01-content">
                <h2><?php esc_html_e('عن المشروع', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php pp_single_render_content(); ?></div>
            </div>
        <?php endif; ?>

        <?php if (pp_single_has_gallery()) : ?>
            <div class="pp-l01-gallery">
                <?php pp_single_render_gallery(); ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if (pp_single_has_location()) : ?>
        <section class="pp-l01-location">
            <div class="pp-l01-location-copy">
                <h2><?php esc_html_e('الموقع الجغرافي', 'projects-plugin'); ?></h2>
                <?php if (pp_single_location_label('') !== '') : ?><p><?php echo esc_html(pp_single_location_label('')); ?></p><?php endif; ?>
                <?php pp_single_render_location_link(__('عرض الموقع على خرائط جوجل', 'projects-plugin')); ?>
            </div>
            <?php if (pp_single_can_show_embed_map()) : ?>
                <div class="pp-l01-location-map"><?php pp_single_render_location_map(380, 'project-location-map'); ?></div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</article>
