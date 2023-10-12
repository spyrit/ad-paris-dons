<?php
/*
Plugin Name: AD Paris - Dons
Description: Une extension qui permet d'afficher un ensemble de blocs redirigeant vers des pages de dons.
Author: SPYRIT
Author URI: http://www.spyrit.net
Version: 0.2.1
*/

const AD_PARIS_DONS_VERSION = "0.2.1";
const AD_PARIS_DONS_REMOTE_INFO_URL = "https://raw.githubusercontent.com/spyrit/ad-paris-dons/master/info.json";
const AD_PARIS_DONS_API_URL = "https://plugindenier.dioceseparis.fr/plugindenier.json";
const AD_PARIS_DONS_TRANSIENT_NAME = "ad_paris_dons_data";
$plugin_path = plugin_dir_path(__FILE__);

function ad_paris_dons_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=ad-paris-dons-options">Réglages</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'ad_paris_dons_settings_link');

/**
 * Classes
 */
require_once 'classes/AdParisDonsHelper.php';
require_once 'classes/AdParisDonsApi.php';
require_once 'classes/AdParisDonsSettingsPage.php';

/**
 * Mise à jour
 */
include_once $plugin_path . 'info_update.php';

/**
 * Shortcodes
 */
include_once $plugin_path . 'shortcodes/ad-paris-dons-shortcode.php';

/**
 * Style (backoffice)
 */
function ad_paris_dons_backoffice_load_plugin_css()
{
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('ad_paris_dons_backoffice', $plugin_url . '/assets/css/ad-paris-dons-backoffice.css');
}
add_action('admin_print_styles', 'ad_paris_dons_backoffice_load_plugin_css');

/**
 * Style (frontoffice)
 */
function ad_paris_dons_frontoffice_load_plugin_css()
{
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('ad_paris_dons_frontoffice', $plugin_url . '/assets/css/ad-paris-dons-frontoffice.css');
}
add_action('wp_enqueue_scripts', 'ad_paris_dons_frontoffice_load_plugin_css');

