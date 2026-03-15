<?php
if (!defined('ABSPATH')) {
    exit;
}

$tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'general';
$allowed_tabs = ['general', 'single-project'];
if (!in_array($tab, $allowed_tabs, true)) {
    $tab = 'general';
}

$tabs = [
    'general' => __('General Settings', 'projects-plugin'),
    'single-project' => __('Single Project Layout', 'projects-plugin'),
];

$base_url = admin_url('admin.php?page=pp-settings');
$settings_page = $tab === 'single-project' ? 'pp-settings-single' : 'pp-settings-general';
?>
<div class="wrap">
    <h1><?php esc_html_e('Projects Plugin Settings', 'projects-plugin'); ?></h1>
    <h2 class="nav-tab-wrapper" style="margin-bottom:16px;">
        <?php foreach ($tabs as $tab_key => $tab_label) : ?>
            <?php $tab_url = add_query_arg('tab', $tab_key, $base_url); ?>
            <a href="<?php echo esc_url($tab_url); ?>" class="nav-tab <?php echo $tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html($tab_label); ?>
            </a>
        <?php endforeach; ?>
    </h2>

    <form method="post" action="options.php">
        <?php
        settings_fields('pp_settings_group');
        do_settings_sections($settings_page);
        submit_button();
        ?>
    </form>
</div>
