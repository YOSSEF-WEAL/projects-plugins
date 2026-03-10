<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="pp-single-project pp-single-project-v2" dir="rtl">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $post_id = get_the_ID();
        $gallery_ids = PP_Helpers::get_project_gallery_ids($post_id);
        $location_label = get_post_meta($post_id, '_pp_location_label', true);
        $location_lat = get_post_meta($post_id, '_pp_location_lat', true);
        $location_lng = get_post_meta($post_id, '_pp_location_lng', true);
        $has_location = !empty($location_lat) && !empty($location_lng);
        $terms = get_the_terms($post_id, 'project_category');
        $primary_term = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;
        $project_excerpt = get_the_excerpt($post_id);
        if (empty($project_excerpt)) {
            $project_excerpt = PP_Helpers::get_excerpt($post_id, 22);
        }
        ?>

        <section class="pp-sp-hero <?php echo has_post_thumbnail() ? 'has-image' : 'no-image'; ?>">
            <?php if (has_post_thumbnail()) : ?>
                <div class="pp-sp-hero-bg"><?php the_post_thumbnail('full'); ?></div>
            <?php endif; ?>

            <div class="pp-sp-hero-overlay"></div>
            <div class="pp-sp-hero-content">
                <?php if ($primary_term) : ?>
                    <span class="pp-sp-badge"><?php echo esc_html($primary_term->name); ?></span>
                <?php endif; ?>

                <h1 class="pp-sp-title"><?php the_title(); ?></h1>

                <div class="pp-sp-meta">
                    <?php if (!empty($location_label)) : ?>
                        <span class="pp-sp-meta-item"><?php echo esc_html($location_label); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($project_excerpt)) : ?>
                        <p class="pp-sp-meta-excerpt"><?php echo esc_html($project_excerpt); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if (!empty($gallery_ids) || trim((string) get_the_content()) !== '') : ?>
            <section class="pp-sp-section pp-sp-intro-section">
                <div class="pp-sp-intro-grid">
                    <?php if (trim((string) get_the_content()) !== '') : ?>
                        <div class="pp-sp-main-content pp-sp-intro-content">
                            <h2 class="pp-sp-section-title"><?php esc_html_e('عن المشروع', 'projects-plugin'); ?></h2>
                            <div class="pp-project-content"><?php the_content(); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($gallery_ids)) : ?>
                        <div class="pp-sp-intro-slider">
                            <div class="pp-sp-section-head">
                                <h2><?php esc_html_e('معرض الصور', 'projects-plugin'); ?></h2>
                            </div>
                            <?php
                            $args = ['ids' => $gallery_ids];
                            include PP_PATH . 'templates/parts/slider.php';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($has_location) : ?>
            <section class="pp-sp-section pp-sp-location-section">
                <div class="pp-sp-grid pp-sp-location-grid">
                    <div class="pp-sp-location-copy">
                        <h2 class="pp-sp-section-title"><?php esc_html_e('الموقع الجغرافي', 'projects-plugin'); ?></h2>
                        <?php if (!empty($location_label)) : ?>
                            <p><?php echo esc_html($location_label); ?></p>
                        <?php endif; ?>
                        <div class="pp-sp-location-actions">
                            <a href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer" class="pp-location-link">
                                <?php esc_html_e('عرض الموقع على خرائط جوجل', 'projects-plugin'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="pp-sp-location-map-wrap">
                        <?php
                        $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
                        $src = 'https://www.google.com/maps/embed/v1/view?zoom=14&center=' . rawurlencode($location_lat . ',' . $location_lng);
                        if (!empty($api_key)) {
                            $src .= '&key=' . rawurlencode($api_key);
                        }
                        ?>
                        <div class="pp-location-map">
                            <iframe title="project-location-map" width="100%" height="420" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($src); ?>"></iframe>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
