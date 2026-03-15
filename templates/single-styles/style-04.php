<?php
if (!defined('ABSPATH')) {
    exit;
}

$has_content = trim((string) get_the_content()) !== '';
$map_src = '';
if ($has_location) {
    $api_key = PP_Helpers::get_setting('google_maps_api_key', '');
    $map_src = 'https://www.google.com/maps/embed/v1/view?zoom=14&center=' . rawurlencode($location_lat . ',' . $location_lng);
    if (!empty($api_key)) {
        $map_src .= '&key=' . rawurlencode($api_key);
    }
}
?>
<article class="pp-layout pp-layout-04">
    <header class="pp-l04-hero">
        <div class="pp-l04-hero-copy">
            <span class="pp-l04-kicker"><?php esc_html_e('Project', 'projects-plugin'); ?></span>
            <h1><?php the_title(); ?></h1>
            <?php if (!empty($project_excerpt)) : ?>
                <p class="pp-l04-excerpt"><?php echo esc_html($project_excerpt); ?></p>
            <?php endif; ?>
        </div>
        <div class="pp-l04-hero-media">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large'); ?>
            <?php else : ?>
                <div class="pp-l04-media-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </header>

    <section class="pp-l04-facts">
        <div class="pp-l04-fact">
            <span><?php esc_html_e('Category', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html($primary_term ? $primary_term->name : __('General', 'projects-plugin')); ?></strong>
        </div>
        <div class="pp-l04-fact">
            <span><?php esc_html_e('Location', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html($location_label ?: __('Main Branch', 'projects-plugin')); ?></strong>
        </div>
        <div class="pp-l04-fact">
            <span><?php esc_html_e('Published', 'projects-plugin'); ?></span>
            <strong><?php echo esc_html(get_the_date()); ?></strong>
        </div>
    </section>

    <section class="pp-l04-main">
        <?php if ($has_content) : ?>
            <div class="pp-l04-content">
                <h2><?php esc_html_e('Project Overview', 'projects-plugin'); ?></h2>
                <div class="pp-project-content"><?php the_content(); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($gallery_ids)) : ?>
            <aside class="pp-l04-gallery">
                <h2><?php esc_html_e('Project Gallery', 'projects-plugin'); ?></h2>
                <?php $args = ['ids' => $gallery_ids]; include PP_PATH . 'templates/parts/slider.php'; ?>
            </aside>
        <?php endif; ?>
    </section>

    <?php if ($has_location) : ?>
        <section class="pp-l04-location">
            <div class="pp-l04-location-head">
                <h2><?php esc_html_e('Project Location', 'projects-plugin'); ?></h2>
                <a class="pp-location-link" href="<?php echo esc_url(PP_Helpers::get_google_maps_link($location_lat, $location_lng, $location_label)); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Open in Google Maps', 'projects-plugin'); ?>
                </a>
            </div>
            <div class="pp-l04-map">
                <iframe title="project-location-map" width="100%" height="320" style="border:0" loading="lazy" allowfullscreen src="<?php echo esc_url($map_src); ?>"></iframe>
            </div>
        </section>
    <?php endif; ?>
</article>
