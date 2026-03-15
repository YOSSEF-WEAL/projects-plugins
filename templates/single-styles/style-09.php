<?php
if (!defined('ABSPATH')) {
    exit;
}
$has_content = trim((string) get_the_content()) !== '';
?>
<article class="pp-layout pp-layout-09">
    <header class="pp-l09-head">
        <h1><?php the_title(); ?></h1>
        <?php if (!empty($project_excerpt)) : ?><p><?php echo esc_html($project_excerpt); ?></p><?php endif; ?>
    </header>

    <section class="pp-l09-mosaic">
        <div class="pp-l09-cover"><?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?></div>
        <?php if (!empty($gallery_ids)) : ?><div class="pp-l09-gallery"><?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?></div><?php endif; ?>
    </section>

    <?php if ($has_content) : ?><section class="pp-l09-content"><?php the_content(); ?></section><?php endif; ?>
</article>
