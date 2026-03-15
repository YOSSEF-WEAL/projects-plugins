<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-10">
    <header class="pp-l10-hero <?php echo pp_single_has_thumbnail() ? 'has-image' : 'no-image'; ?>">
        <div class="pp-l10-hero-copy">
            <span class="pp-l10-kicker"><?php esc_html_e('Project', 'projects-plugin'); ?></span>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?>
                <p class="pp-l10-excerpt"><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p>
            <?php endif; ?>
        </div>

        <div class="pp-l10-hero-media">
            <?php if (pp_single_has_thumbnail()) : ?>
                <?php pp_single_render_thumbnail('large'); ?>
            <?php else : ?>
                <div class="pp-l10-media-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </header>

    <section class="pp-l10-facts">
        <div class="pp-l10-fact">
            <span><?php esc_html_e('Category', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html(pp_single_primary_term_name(__('Uncategorized', 'projects-plugin'))); ?></strong>
        </div>
        <div class="pp-l10-fact">
            <span><?php esc_html_e('Location', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html(pp_single_location_label(__('No location', 'projects-plugin'))); ?></strong>
        </div>
        <div class="pp-l10-fact">
            <span><?php esc_html_e('Published', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html(get_the_date()); ?></strong>
        </div>
        <div class="pp-l10-fact">
            <span><?php esc_html_e('Status', 'projects-plugin'); ?></span>
            <strong><?php esc_html_e('Active', 'projects-plugin'); ?></strong>
        </div>
    </section>

    <section class="pp-l10-main">
        <?php if (pp_single_has_content()) : ?>
            <div class="pp-l10-content">
                <h2><?php esc_html_e('Project Overview', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php pp_single_render_content(); ?></div>
            </div>
        <?php endif; ?>

        <?php if (pp_single_has_gallery()) : ?>
            <aside class="pp-l10-gallery">
                <h2><?php esc_html_e('Project Gallery', 'projects-plugin'); ?></h2>
                <?php pp_single_render_gallery(); ?>
            </aside>
        <?php endif; ?>
    </section>

    <?php if (pp_single_has_location()) : ?>
        <section class="pp-l10-location">
            <div class="pp-l10-location-head">
                <h2><?php esc_html_e('Project Location', 'projects-plugin'); ?></h2>
                <?php pp_single_render_location_link(__('Open Detailed Map', 'projects-plugin')); ?>
            </div>
            <?php if (pp_single_can_show_embed_map()) : ?>
                <div class="pp-l10-map">
                    <?php pp_single_render_location_map(340, 'project-location-map'); ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</article>
