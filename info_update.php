<?php
add_filter('plugins_api', 'ad_paris_dons_plugin_info', 20, 3);

function ad_paris_dons_plugin_info($res, $action, $args)
{
    if ($action !== 'plugin_information') {
        return false;
    }

    if ('ad-paris-dons' !== $args->slug) {
        return $res;
    }

    if (false == $remote = get_transient('spyrit_upgrade_ad_paris_dons')) {
        $remote = wp_remote_get(
            AD_PARIS_DONS_REMOTE_INFO_URL,
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json'
                ]]
        );

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
            set_transient('spyrit_upgrade_ad_paris_dons', $remote, 43200);
        }
    }

    if ($remote) {
        $remote = json_decode($remote['body']);
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = 'ad-paris-dons';
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = $remote->last_updated;
        $res->sections = [
            'description' => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog' => $remote->sections->changelog,
        ];

        $res->banners = [
            'low' => plugins_url('/assets/img/ad-paris-dons-banner-772x250.png', __FILE__),
            'high' => plugins_url('/assets/img/ad-paris-dons-banner-1544x500.png', __FILE__)
        ];
        return $res;
    }

    return false;
}


add_filter('site_transient_update_plugins', 'ad_paris_dons_push_update');

function ad_paris_dons_push_update($transient)
{
    if (empty($transient->checked)) {
        return $transient;
    }

    if (false == $remote = get_transient('spyrit_upgrade_ad_paris_dons')) {
        $remote = wp_remote_get(
            AD_PARIS_DONS_REMOTE_INFO_URL,
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json'
                ]]
        );

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
            set_transient('spyrit_upgrade_ad_paris_dons', $remote, 43200);
        }
    }

    if ($remote) {
        $remote = json_decode($remote['body']);

        if ($remote && version_compare(AD_PARIS_DONS_VERSION, $remote->version, '<') && version_compare($remote->requires, get_bloginfo('version'), '<')) {
            $res = new stdClass();
            $res->slug = 'ad-paris-dons';
            $res->plugin = 'ad-paris-dons/ad-paris-dons.php';
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $transient->response[$res->plugin] = $res;
        }
    }
    return $transient;
}

add_action('upgrader_process_complete', 'ad_paris_dons_after_update', 10, 2);

function ad_paris_dons_after_update($upgrader_object, $options)
{
    if ($options['action'] == 'update' && $options['type'] === 'plugin') {
        delete_transient('spyrit_upgrade_ad_paris_dons');
    }
}
