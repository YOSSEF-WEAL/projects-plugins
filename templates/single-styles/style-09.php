<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article class="pp-layout pp-layout-09">
    <header class="pp-l09-head">
        <h1><?php the_title(); ?></h1>
        <?php if (!empty((string) pp_single_get('project_excerpt', ''))) : ?><p><?php echo esc_html((string) pp_single_get('project_excerpt', '')); ?></p><?php endif; ?>
    </header>

    <section class="pp-l09-mosaic">
        <div class="pp-l09-cover"><?php pp_single_render_thumbnail('large'); ?></div>
        <?php if (pp_single_has_gallery()) : ?><div class="pp-l09-gallery"><?php pp_single_render_gallery(); ?></div><?php endif; ?>
    </section>

    <?php if (pp_single_has_content()) : ?><section class="pp-l09-content"><?php pp_single_render_content(); ?></section><?php endif; ?>
</article>
