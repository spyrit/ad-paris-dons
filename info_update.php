<?php

add_filter('plugins_api', 'ad_paris_dons_plugin_info', 20, 3);

/**
 * @return object|false
 */
function ad_paris_dons_decode_remote_info(mixed $remote): object|false
{
    if (!is_array($remote) || empty($remote['body']) || !is_string($remote['body'])) {
        return false;
    }

    $decoded = json_decode($remote['body'], false);
    if (!is_object($decoded)) {
        return false;
    }

    return $decoded;
}

/**
 * @return array<string, mixed>|false
 */
function ad_paris_dons_fetch_remote_update(): array|false
{
    $remote = get_transient('spyrit_upgrade_ad_paris_dons');
    if ($remote !== false) {
        return is_array($remote) ? $remote : false;
    }

    $remote = wp_remote_get(
        AD_PARIS_DONS_REMOTE_INFO_URL,
        [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]
    );

    if (is_wp_error($remote)) {
        return false;
    }

    $statusCode = (int) wp_remote_retrieve_response_code($remote);
    if ($statusCode !== 200 || wp_remote_retrieve_body($remote) === '') {
        return false;
    }

    set_transient('spyrit_upgrade_ad_paris_dons', $remote, 43200);

    return $remote;
}

function ad_paris_dons_plugin_info(mixed $res, string $action, object $args): mixed
{
    if ($action !== 'plugin_information') {
        return false;
    }

    if (!isset($args->slug) || $args->slug !== 'ad-paris-dons') {
        return $res;
    }

    $remote = ad_paris_dons_fetch_remote_update();
    $info = ad_paris_dons_decode_remote_info($remote);
    if ($info === false) {
        return false;
    }

    $res = new stdClass();
    $res->name = $info->name ?? '';
    $res->slug = 'ad-paris-dons';
    $res->version = $info->version ?? '';
    $res->tested = $info->tested ?? '';
    $res->requires = $info->requires ?? '';
    $res->requires_php = $info->requires_php ?? '';
    $res->download_link = $info->download_url ?? '';
    $res->trunk = $info->download_url ?? '';
    $res->last_updated = $info->last_updated ?? '';
    $sections = isset($info->sections) && is_object($info->sections) ? $info->sections : null;
    $res->sections = [
        'description' => $sections->description ?? '',
        'installation' => $sections->installation ?? '',
        'changelog' => $sections->changelog ?? '',
    ];
    $res->banners = [
        'low' => plugins_url('/assets/img/ad-paris-dons-banner-772x250.png', __FILE__),
        'high' => plugins_url('/assets/img/ad-paris-dons-banner-1544x500.png', __FILE__),
    ];

    return $res;
}

add_filter('site_transient_update_plugins', 'ad_paris_dons_push_update');

function ad_paris_dons_push_update(object|false $transient): object|false
{
    if (!is_object($transient) || empty($transient->checked)) {
        return $transient;
    }

    $remote = ad_paris_dons_fetch_remote_update();
    $info = ad_paris_dons_decode_remote_info($remote);
    if ($info === false || empty($info->version) || empty($info->requires) || empty($info->download_url)) {
        return $transient;
    }

    if (
        version_compare(AD_PARIS_DONS_VERSION, (string) $info->version, '<')
        && version_compare((string) $info->requires, get_bloginfo('version'), '<')
    ) {
        $res = new stdClass();
        $res->slug = 'ad-paris-dons';
        $res->plugin = 'ad-paris-dons/ad-paris-dons.php';
        $res->new_version = (string) $info->version;
        $res->tested = (string) ($info->tested ?? '');
        $res->requires_php = (string) ($info->requires_php ?? '');
        $res->package = (string) $info->download_url;
        $transient->response[$res->plugin] = $res;
    }

    return $transient;
}

add_action('upgrader_process_complete', 'ad_paris_dons_after_update', 10, 2);

function ad_paris_dons_after_update(mixed $upgrader_object, array $options): void
{
    if (($options['action'] ?? '') === 'update' && ($options['type'] ?? '') === 'plugin') {
        delete_transient('spyrit_upgrade_ad_paris_dons');
    }
}
