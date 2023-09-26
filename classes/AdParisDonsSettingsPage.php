<?php

namespace AD_PARIS_DONS;

class AdParisDonsSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'ad_paris_dons_plugin_page']);
        add_action('admin_init', [$this, 'ad_paris_dons_page_init']);
    }

    /**
     * Add options page
     */
    public function ad_paris_dons_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'AD Paris - Dons',
            'AD Paris - Dons',
            'manage_options',
            'ad-paris-dons-options',
            [$this, 'ad_paris_dons_admin_page']
        );
    }

    /**
     * Options page callback
     */
    public function ad_paris_dons_admin_page()
    {
        $api = new \AD_PARIS_DONS\AdParisDonsApi();

        if (isset($_GET['page']) && $_GET['page'] === 'ad-paris-dons-options' && isset($_POST['force-cache'])) {
            $api->getData(true);
        }
        $this->options = get_option('ad_paris_dons_config'); ?>
        <div class="wrap">
            <h1><img src="<?= plugin_dir_url(__FILE__) ?>../assets/img/ad-paris-dons-favicon.png" alt="Logo AD Paris">AD Paris - Dons <span>v<?= AD_PARIS_DONS_VERSION ?></span></h1>
            <div class="notices-wrap"><?php do_action('admin_notices') ?></div>
            <div class="container">
                <form method="post" action="options.php">
                    <?php
                        settings_fields('ad-paris-dons-group');
                        do_settings_sections('ad-paris-dons-options');
                    ?>
                    <?php submit_button(); ?>
                </form>
                <h2>Comment l'utiliser ?</h2>
                <div class="using">
                    <h3>Insérez le code court suivant dans votre page ou article :</h3>
                    <p class="shortcode">[ad-paris-dons-shortcode]</p>
                </div>
                <form method="POST" class="force-cache">
                    <h2>Récupération des données</h2>
                    <div class="content">
                        <?php submit_button("Forcer l'actualisation", 'large', 'force-cache', true, '') ?>
                        <?php if (get_option('ad_paris_dons_api_last_call')): ?>
                            <p style="margin-top: 10px;">Dernière récupération&nbsp;: <strong><?php echo esc_html(get_option('ad_paris_dons_api_last_call')) ?></strong></p>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="copy">
                    AD Paris - Dons version <?= AD_PARIS_DONS_VERSION ?><br>
                    Développé par <a href="https://www.spyrit.net" title="Accéder au site de SPYRIT" target="_blank">Spyrit systèmes d'information</a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function ad_paris_dons_page_init()
    {
        register_setting(
            'ad-paris-dons-group',
            'ad_paris_dons_config'
        );
        add_settings_section(
            'ad-paris-dons-section-api',
            'API',
            null,
            'ad-paris-dons-options'
        );
        add_settings_field(
            'ad-paris-dons-code-denier',
            'Code denier',
            [$this, 'ad_paris_dons_code_denier_callback'],
            'ad-paris-dons-options',
            'ad-paris-dons-section-api'
        );
    }

    public function ad_paris_dons_code_denier_callback()
    {
        printf(
            '<input type="text" id="code-denier" name="ad_paris_dons_config[code-denier]" value="%s" placeholder="Code denier" />',
            isset($this->options['code-denier']) ? esc_attr($this->options['code-denier']) : ''
        );
    }
}

if (is_admin()) {
    $settings_page = new AdParisDonsSettingsPage();
}