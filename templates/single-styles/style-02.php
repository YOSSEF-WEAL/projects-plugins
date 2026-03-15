<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-02">
    <header class="pp-l02-hero">
        <div class="pp-l02-copy">
            <?php if (pp_single_primary_term_name('') !== '') : ?><span class="pp-l02-kicker"><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
            <?php if (pp_single_location_label('') !== '') : ?><div class="pp-l02-meta"><?php echo esc_html(pp_single_location_label('')); ?></div><?php endif; ?>
        </div>
        <div class="pp-l02-cover">
            <?php pp_single_render_thumbnail('large'); ?>
        </div>
    </header>

    <?php if (pp_single_has_gallery()) : ?>
        <section class="pp-l02-gallery"><?php pp_single_render_gallery(); ?></section>
    <?php endif; ?>

    <?php if (pp_single_has_content()) : ?>
        <section class="pp-l02-content">
            <h2><?php esc_html_e('تفاصيل المشروع', 'projects-plugin'); ?></h2>
            <div class="pp-project-content"><?php pp_single_render_content(); ?></div>
        </section>
    <?php endif; ?>

    <?php if (pp_single_has_location()) : ?>
        <section class="pp-l02-location">
            <?php if (pp_single_can_show_embed_map()) : ?>
                <?php pp_single_render_location_map(300, 'project-location-map'); ?>
            <?php endif; ?>
            <?php pp_single_render_location_link(__('فتح في خرائط جوجل', 'projects-plugin')); ?>
        </section>
    <?php endif; ?>
</article>
