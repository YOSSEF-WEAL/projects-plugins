<?php
if (!defined('ABSPATH')) {
    exit;
}

$term = get_query_var('pp_term');
if (!$term || is_wp_error($term)) {
    return;
}

$show_name = (bool) get_query_var('pp_show_name', true);
$show_description = (bool) get_query_var('pp_show_description', false);
$image_id = get_term_meta($term->term_id, 'pp_term_image_id', true);
$link = get_term_link($term);
?>
<a class="pp-category-card" href="<?php echo esc_url($link); ?>">
    <div class="pp-category-image">
        <?php
        if ($image_id) {
            echo wp_get_attachment_image((int) $image_id, 'large');
        }
        ?>
    </div>
    <div class="pp-category-content">
        <?php if ($show_name) : ?>
            <h3><?php echo esc_html($term->name); ?></h3>
        <?php endif; ?>
        <?php if ($show_description && !empty($term->description)) : ?>
            <p><?php echo esc_html($term->description); ?></p>
        <?php endif; ?>
    </div>
</a>
