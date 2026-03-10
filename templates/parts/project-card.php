<?php
if (!defined('ABSPATH')) {
    exit;
}

$ctx = isset($pp_card_context) && is_array($pp_card_context) ? $pp_card_context : [];

$post_id = isset($ctx['post_id']) ? (int) $ctx['post_id'] : 0;
if ($post_id <= 0) {
    $post_id = (int) get_query_var('pp_post_id', 0);
}
if ($post_id <= 0) {
    $post_id = get_the_ID();
}

$show_image = array_key_exists('show_image', $ctx) ? (bool) $ctx['show_image'] : (bool) get_query_var('pp_show_image', true);
$image_size_raw = array_key_exists('image_size', $ctx) ? (string) $ctx['image_size'] : (string) get_query_var('pp_image_size', 'large');
$image_size = sanitize_key($image_size_raw);
$allowed_sizes = get_intermediate_image_sizes();
$allowed_sizes[] = 'full';
if (!in_array($image_size, $allowed_sizes, true)) {
    $image_size = 'large';
}
$show_title = array_key_exists('show_title', $ctx) ? (bool) $ctx['show_title'] : (bool) get_query_var('pp_show_title', true);
$show_excerpt = array_key_exists('show_excerpt', $ctx) ? (bool) $ctx['show_excerpt'] : (bool) get_query_var('pp_show_excerpt', true);
$show_category = array_key_exists('show_category', $ctx) ? (bool) $ctx['show_category'] : (bool) get_query_var('pp_show_category', true);
$show_button = array_key_exists('show_button', $ctx) ? (bool) $ctx['show_button'] : (bool) get_query_var('pp_show_button', true);
$button_text = array_key_exists('view_text', $ctx) ? (string) $ctx['view_text'] : (string) get_query_var('pp_view_project_text', __('View Project', 'projects-plugin'));
$terms = get_the_terms($post_id, 'project_category');
?>
<article class="pp-project-card">
    <?php if ($show_image && has_post_thumbnail($post_id)) : ?>
        <a class="pp-card-image" href="<?php echo esc_url(get_permalink($post_id)); ?>">
            <?php echo get_the_post_thumbnail($post_id, $image_size); ?>
        </a>
    <?php endif; ?>

    <div class="pp-card-content">
        <?php if ($show_category && !empty($terms) && !is_wp_error($terms)) : ?>
            <div class="pp-card-category"><?php echo esc_html($terms[0]->name); ?></div>
        <?php endif; ?>

        <?php if ($show_title) : ?>
            <h3 class="pp-card-title"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h3>
        <?php endif; ?>

        <?php if ($show_excerpt) : ?>
            <p class="pp-card-excerpt"><?php echo esc_html(PP_Helpers::get_excerpt($post_id, 20)); ?></p>
        <?php endif; ?>

        <?php if ($show_button) : ?>
            <a class="pp-card-btn" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($button_text); ?></a>
        <?php endif; ?>
    </div>
</article>
