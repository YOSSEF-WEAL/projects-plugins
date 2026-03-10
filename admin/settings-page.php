<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e('Projects Plugin Settings', 'projects-plugin'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('pp_settings_group');
        do_settings_sections('pp-settings');
        submit_button();
        ?>
    </form>
</div>
