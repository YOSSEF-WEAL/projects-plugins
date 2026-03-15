<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-07">
    <header class="pp-l07-head">
        <?php if (pp_single_primary_term_name('') !== '') : ?><span><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
        <h1><?php the_title(); ?></h1>
    </header>

    <?php if (pp_single_has_gallery()) : ?>
        <section class="pp-l07-strip"><?php pp_single_render_gallery(); ?></section>
    <?php endif; ?>

    <section class="pp-l07-columns">
        <?php if (pp_single_has_content()) : ?><div class="pp-l07-content"><?php pp_single_render_content(); ?></div><?php endif; ?>
        <aside class="pp-l07-side">
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
            <?php if (pp_single_location_label('') !== '') : ?><p><?php echo esc_html(pp_single_location_label('')); ?></p><?php endif; ?>
            <?php if (pp_single_has_location()) : ?><?php pp_single_render_location_link(__('استكشف الموقع', 'projects-plugin')); ?><?php endif; ?>
        </aside>
    </section>
</article>
