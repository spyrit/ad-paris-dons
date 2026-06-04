<?php

namespace AD_PARIS_DONS;

class AdParisDonsSettingsPage
{
    /**
     * @var array<string, mixed>|false
     */
    private array|false $options = false;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'ad_paris_dons_plugin_page']);
        add_action('admin_init', [$this, 'ad_paris_dons_page_init']);
    }

    public function ad_paris_dons_plugin_page(): void
    {
        add_options_page(
            'AD Paris - Dons',
            'AD Paris - Dons',
            'manage_options',
            'ad-paris-dons-options',
            [$this, 'ad_paris_dons_admin_page']
        );
    }

    public function ad_paris_dons_admin_page(): void
    {
        $api = new AdParisDonsApi();

        if (
            isset($_GET['page'], $_POST['force-cache'], $_POST['ad_paris_dons_force_cache_nonce'])
            && sanitize_text_field(wp_unslash((string) $_GET['page'])) === 'ad-paris-dons-options'
            && wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['ad_paris_dons_force_cache_nonce'])), 'ad_paris_dons_force_cache')
        ) {
            $api->getData(true);
        }

        $options = get_option('ad_paris_dons_config');
        $this->options = is_array($options) ? $options : false;
        ?>
        <div class="wrap">
            <h1><img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/img/ad-paris-dons-favicon.png'); ?>" alt="Logo AD Paris">AD Paris - Dons <span>v<?php echo esc_html(AD_PARIS_DONS_VERSION); ?></span></h1>
            <div class="notices-wrap"><?php do_action('admin_notices'); ?></div>
            <div class="container">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('ad-paris-dons-group');
                    do_settings_sections('ad-paris-dons-options');
                    submit_button();
                    ?>
                </form>
                <h2>Comment l'utiliser ?</h2>
                <div class="using">
                    <h3>Insérez le code court suivant dans votre page ou article :</h3>
                    <p class="shortcode">[ad-paris-dons-shortcode]</p>
                </div>
                <form method="POST" class="force-cache">
                    <?php wp_nonce_field('ad_paris_dons_force_cache', 'ad_paris_dons_force_cache_nonce'); ?>
                    <h2>Récupération des données</h2>
                    <div class="content">
                        <?php submit_button("Forcer l'actualisation", 'large', 'force-cache', true, ''); ?>
                        <?php if (get_option('ad_paris_dons_api_last_call')) : ?>
                            <p style="margin-top: 10px;">Dernière récupération&nbsp;: <strong><?php echo esc_html((string) get_option('ad_paris_dons_api_last_call')); ?></strong></p>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="copy">
                    AD Paris - Dons version <?php echo esc_html(AD_PARIS_DONS_VERSION); ?><br>
                    Développé par <a href="https://www.spyrit.net" title="Accéder au site de SPYRIT" target="_blank">Spyrit systèmes d'information</a>
                </div>
            </div>
        </div>
        <?php
    }

    public function ad_paris_dons_page_init(): void
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

    public function ad_paris_dons_code_denier_callback(): void
    {
        $value = '';
        if (is_array($this->options) && isset($this->options['code-denier'])) {
            $value = (string) $this->options['code-denier'];
        }

        printf(
            '<input type="text" id="code-denier" name="ad_paris_dons_config[code-denier]" value="%s" placeholder="Code denier" />',
            esc_attr($value)
        );
    }
}

if (is_admin()) {
    new AdParisDonsSettingsPage();
}
