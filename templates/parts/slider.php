<?php
if (!defined('ABSPATH')) {
    exit;
}

$ids = isset($args['ids']) && is_array($args['ids']) ? $args['ids'] : [];
if (empty($ids)) {
    return;
}

$slider_uid = 'pp-gallery-' . wp_unique_id();
$show_thumbs = count($ids) > 1;
?>
<div class="pp-gallery-slider" data-pp-gallery-slider="1" id="<?php echo esc_attr($slider_uid); ?>">
    <div class="pp-gallery-main-wrap">
        <button type="button" class="pp-gallery-zoom" aria-label="Zoom">
            <img src="<?php echo esc_url(PP_URL . 'public/arrows-out.svg'); ?>" alt="" aria-hidden="true">
        </button>

        <button type="button" class="pp-gallery-nav pp-gallery-prev" aria-label="Previous">
            <img src="<?php echo esc_url(PP_URL . 'public/caret-right.svg'); ?>" alt="" aria-hidden="true">
        </button>

        <div class="swiper pp-gallery-main">
            <div class="swiper-wrapper">
                <?php foreach ($ids as $id) : ?>
                    <?php $full_url = wp_get_attachment_image_url((int) $id, 'full'); ?>
                    <div class="swiper-slide pp-slide" data-full="<?php echo esc_url($full_url); ?>">
                        <?php echo wp_get_attachment_image((int) $id, 'large'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="button" class="pp-gallery-nav pp-gallery-next" aria-label="Next">
            <img src="<?php echo esc_url(PP_URL . 'public/caret-left.svg'); ?>" alt="" aria-hidden="true">
        </button>
    </div>

    <?php if ($show_thumbs) : ?>
        <div class="swiper pp-gallery-thumbs">
            <div class="swiper-wrapper">
                <?php foreach ($ids as $id) : ?>
                    <div class="swiper-slide pp-thumb-slide">
                        <?php echo wp_get_attachment_image((int) $id, 'thumbnail'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
