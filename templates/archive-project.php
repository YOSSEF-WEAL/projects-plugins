<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$terms = get_terms([
    'taxonomy' => 'project_category',
    'hide_empty' => false,
]);
?>
<main class="pp-archive container">
    <header class="pp-archive-header">
        <h1><?php post_type_archive_title(); ?></h1>
    </header>

    <?php if (!is_wp_error($terms) && !empty($terms)) : ?>
        <div class="pp-projects-filters">
            <a class="pp-filter-btn active" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"><?php esc_html_e('All', 'projects-plugin'); ?></a>
            <?php foreach ($terms as $term) : ?>
                <a class="pp-filter-btn" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="pp-projects-list pp-layout-grid pp-cols-3">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <?php
            set_query_var('pp_show_image', true);
            set_query_var('pp_show_title', true);
            set_query_var('pp_show_excerpt', true);
            set_query_var('pp_show_category', true);
            set_query_var('pp_show_button', true);
            include PP_PATH . 'templates/parts/project-card.php';
            ?>
        <?php endwhile; endif; ?>
    </div>

    <div class="pp-pagination">
        <?php the_posts_pagination(); ?>
    </div>
</main>
<?php get_footer(); ?>
