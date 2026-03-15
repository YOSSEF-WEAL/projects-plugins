<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-08">
    <section class="pp-l08-wrap">
        <aside class="pp-l08-side">
            <h1><?php the_title(); ?></h1>
            <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
            <?php if (pp_single_primary_term_name('') !== '') : ?><span class="pp-l08-chip"><?php echo esc_html(pp_single_primary_term_name('')); ?></span><?php endif; ?>
            <?php if (pp_single_has_location()) : ?><?php pp_single_render_location_link(__('الموقع', 'projects-plugin')); ?><?php endif; ?>
        </aside>

        <div class="pp-l08-main">
            <?php if (pp_single_has_content()) : ?><section class="pp-l08-content"><?php pp_single_render_content(); ?></section><?php endif; ?>
            <?php if (pp_single_has_gallery()) : ?><section class="pp-l08-gallery"><?php pp_single_render_gallery(); ?></section><?php endif; ?>
        </div>
    </section>
</article>
