<?php

if (!defined('ABSPATH')) {
    exit;
}

class PP_Updater {
    const CACHE_KEY = 'pp_github_latest_release';
    const CACHE_TTL = 3600;
    const ERROR_CACHE_TTL = 300;

    private $plugin_file;
    private $plugin_basename;
    private $plugin_slug;
    private $plugin_directory;
    private $current_version;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_basename = plugin_basename($plugin_file);
        $this->plugin_slug = dirname($this->plugin_basename);
        if ($this->plugin_slug === '.' || $this->plugin_slug === '') {
            $this->plugin_slug = sanitize_title(pathinfo($this->plugin_basename, PATHINFO_FILENAME));
        }
        $this->plugin_directory = basename($this->plugin_slug);
        $this->current_version = PP_VERSION;

        add_filter('pre_set_site_transient_update_plugins', [$this, 'inject_update'], 20);
        add_filter('plugins_api', [$this, 'plugins_api'], 20, 3);
        add_filter('upgrader_source_selection', [$this, 'normalize_source_directory'], 10, 4);

        add_action('upgrader_process_complete', [$this, 'clear_cache_after_upgrade'], 10, 2);
        add_action('admin_init', [$this, 'force_check_on_updates_screens']);
    }

    public function inject_update($transient) {
        if (!is_object($transient)) {
            $transient = new stdClass();
        }

        if (!isset($transient->checked) || !is_array($transient->checked)) {
            $transient->checked = [];
        }

        if (!isset($transient->response) || !is_array($transient->response)) {
            $transient->response = [];
        }

        if (!isset($transient->no_update) || !is_array($transient->no_update)) {
            $transient->no_update = [];
        }

        $release = $this->get_latest_release(false);
        if (!$release || empty($release['version']) || empty($release['package_url'])) {
            return $transient;
        }

        $plugin_info = $this->build_plugin_info($release);
        $transient->checked[$this->plugin_basename] = $this->current_version;

        if (version_compare($release['version'], $this->current_version, '>')) {
            $transient->response[$this->plugin_basename] = $plugin_info;
            unset($transient->no_update[$this->plugin_basename]);
        } else {
            $transient->no_update[$this->plugin_basename] = $plugin_info;
            unset($transient->response[$this->plugin_basename]);
        }

        return $transient;
    }

    public function plugins_api($result, $action, $args) {
        if ($action !== 'plugin_information' || empty($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $release = $this->get_latest_release(false);
        if (!$release) {
            return $result;
        }

        $name = $release['name'] ? $release['name'] : __('Projects Plugin for Elementor', 'projects-plugin');
        $changelog = !empty($release['body']) ? wp_kses_post(wpautop($release['body'])) : __('No changelog available.', 'projects-plugin');
        $last_updated = !empty($release['published_at']) ? gmdate('Y-m-d', strtotime($release['published_at'])) : gmdate('Y-m-d');

        return (object) [
            'name' => $name,
            'slug' => $this->plugin_slug,
            'version' => $release['version'],
            'author' => '<a href="https://portfolio-yossef-weal.netlify.app/">Yossef Weal</a>',
            'homepage' => PP_GITHUB_REPOSITORY_URL,
            'download_link' => $release['package_url'],
            'requires' => '6.0',
            'requires_php' => '7.4',
            'last_updated' => $last_updated,
            'sections' => [
                'description' => __('Manage and display projects with Elementor widgets, filtering, pagination, and location support.', 'projects-plugin'),
                'changelog' => $changelog,
            ],
        ];
    }

    public function normalize_source_directory($source, $remote_source, $upgrader, $hook_extra) {
        if (empty($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $source;
        }

        global $wp_filesystem;
        if (!$wp_filesystem) {
            return $source;
        }

        $source = untrailingslashit($source);
        $remote_source = untrailingslashit($remote_source);
        $normalized = $remote_source . '/' . $this->plugin_directory;

        if (wp_normalize_path($source) === wp_normalize_path($normalized) || basename($source) === $this->plugin_directory) {
            return $source;
        }

        if ($wp_filesystem->exists($normalized)) {
            $wp_filesystem->delete($normalized, true);
        }

        $source_is_root = wp_normalize_path($source) === wp_normalize_path($remote_source);

        if ($source_is_root) {
            if (!$wp_filesystem->mkdir($normalized, FS_CHMOD_DIR)) {
                return new WP_Error(
                    'pp_updater_cannot_create_directory',
                    __('Could not prepare plugin update directory.', 'projects-plugin')
                );
            }

            $entries = $wp_filesystem->dirlist($source, false, true);
            if (!is_array($entries)) {
                return new WP_Error(
                    'pp_updater_cannot_read_package',
                    __('Could not read plugin update package files.', 'projects-plugin')
                );
            }

            foreach (array_keys($entries) as $entry_name) {
                if ($entry_name === $this->plugin_directory || $entry_name === '.' || $entry_name === '..') {
                    continue;
                }

                $from = $source . '/' . $entry_name;
                $to = $normalized . '/' . $entry_name;

                if (!$wp_filesystem->move($from, $to, true)) {
                    return new WP_Error(
                        'pp_updater_cannot_move_package_content',
                        __('Could not prepare plugin update package files.', 'projects-plugin')
                    );
                }
            }

            return $normalized;
        }

        if (!$wp_filesystem->move($source, $normalized, true)) {
            return new WP_Error(
                'pp_updater_cannot_normalize_source',
                __('Could not prepare plugin update package.', 'projects-plugin')
            );
        }

        return $normalized;
    }

    public function clear_cache_after_upgrade($upgrader, $hook_extra) {
        if (empty($hook_extra['type']) || $hook_extra['type'] !== 'plugin') {
            return;
        }

        if (!empty($hook_extra['plugin']) && $hook_extra['plugin'] === $this->plugin_basename) {
            delete_site_transient(self::CACHE_KEY);
        }

        if (!empty($hook_extra['plugins']) && is_array($hook_extra['plugins']) && in_array($this->plugin_basename, $hook_extra['plugins'], true)) {
            delete_site_transient(self::CACHE_KEY);
        }
    }

    public function force_check_on_updates_screens() {
        if (!is_admin() || !current_user_can('update_plugins')) {
            return;
        }

        global $pagenow;
        if (!in_array($pagenow, ['plugins.php', 'update-core.php'], true)) {
            return;
        }

        $release = $this->get_latest_release(true);
        if (!$release) {
            return;
        }

        $transient = get_site_transient('update_plugins');
        if (!is_object($transient)) {
            $transient = new stdClass();
        }

        $transient = $this->inject_update($transient);
        $transient->last_checked = time();
        set_site_transient('update_plugins', $transient);
    }

    private function build_plugin_info($release) {
        return (object) [
            'id' => PP_GITHUB_REPOSITORY_URL,
            'slug' => $this->plugin_slug,
            'plugin' => $this->plugin_basename,
            'new_version' => $release['version'],
            'url' => $release['html_url'],
            'package' => $release['package_url'],
            'tested' => get_bloginfo('version'),
            'requires_php' => '7.4',
            'icons' => [],
            'banners' => [],
            'banners_rtl' => [],
            'compatibility' => new stdClass(),
        ];
    }

    private function get_latest_release($force_refresh = false) {
        if ($force_refresh) {
            delete_site_transient(self::CACHE_KEY);
        }

        $cached = get_site_transient(self::CACHE_KEY);
        if (!$force_refresh && $cached !== false) {
            return is_array($cached) ? $cached : false;
        }

        $headers = [
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
        ];

        if (defined('PP_GITHUB_TOKEN') && PP_GITHUB_TOKEN) {
            $headers['Authorization'] = 'Bearer ' . PP_GITHUB_TOKEN;
        }

        $request = wp_remote_get(PP_GITHUB_LATEST_RELEASE_API, [
            'timeout' => 15,
            'headers' => $headers,
        ]);

        if (is_wp_error($request)) {
            set_site_transient(self::CACHE_KEY, 'error', self::ERROR_CACHE_TTL);
            return false;
        }

        $status = wp_remote_retrieve_response_code($request);
        if ($status !== 200) {
            set_site_transient(self::CACHE_KEY, 'error', self::ERROR_CACHE_TTL);
            return false;
        }

        $payload = json_decode(wp_remote_retrieve_body($request), true);
        if (!is_array($payload) || empty($payload['tag_name'])) {
            set_site_transient(self::CACHE_KEY, 'error', self::ERROR_CACHE_TTL);
            return false;
        }

        $version = ltrim((string) $payload['tag_name'], 'vV');
        $package_url = $this->pick_package_url($payload);

        if (!$version || !$package_url) {
            set_site_transient(self::CACHE_KEY, 'error', self::ERROR_CACHE_TTL);
            return false;
        }

        $release = [
            'version' => $version,
            'name' => !empty($payload['name']) ? sanitize_text_field($payload['name']) : '',
            'body' => !empty($payload['body']) ? (string) $payload['body'] : '',
            'html_url' => !empty($payload['html_url']) ? esc_url_raw($payload['html_url']) : PP_GITHUB_REPOSITORY_URL,
            'published_at' => !empty($payload['published_at']) ? (string) $payload['published_at'] : '',
            'package_url' => esc_url_raw($package_url),
        ];

        set_site_transient(self::CACHE_KEY, $release, self::CACHE_TTL);
        return $release;
    }

    private function pick_package_url($payload) {
        if (!empty($payload['assets']) && is_array($payload['assets'])) {
            foreach ($payload['assets'] as $asset) {
                if (empty($asset['browser_download_url']) || empty($asset['name'])) {
                    continue;
                }

                $asset_name = strtolower((string) $asset['name']);
                if (substr($asset_name, -4) === '.zip') {
                    return $asset['browser_download_url'];
                }
            }
        }

        if (!empty($payload['zipball_url'])) {
            return (string) $payload['zipball_url'];
        }

        return '';
    }
}
