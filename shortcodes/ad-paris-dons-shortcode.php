<?php

function ad_paris_dons_shortcode(): string
{
    $api = new AD_PARIS_DONS\AdParisDonsApi();
    $helper = new AD_PARIS_DONS\AdParisDonsHelper();
    $modules = $api->getData();

    if (!is_array($modules) || empty($modules['results']) || !is_array($modules['results'])) {
        return '<p>Configuration requise</p>';
    }

    ob_start();
    ?>
    <div class="ad-paris-dons-wrapper">
        <div class="ad-paris-dons">
            <?php foreach ($modules['results'] as $module) : ?>
                <?php if (!is_array($module)) : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <div class="module-ad-paris-dons">
                    <div class="title-ad-paris-dons"
                         style="background-color: <?php echo esc_attr((string) ($module['backgroundColor'] ?? '')); ?> !important;color: <?php echo esc_attr((string) ($module['textColor'] ?? '')); ?> !important;"
                    ><?php echo esc_html((string) ($module['title'] ?? '')); ?></div>
                    <div class="content-ad-paris-dons" style="background-image: url('<?php echo esc_url((string) ($module['image'] ?? '')); ?>') !important;background-color:<?php echo esc_attr((string) ($module['backgroundColorImage'] ?? '')); ?> !important;">
                        <?php if (!empty($module['moreLabel']) && !empty($module['moreLink'])) : ?>
                            <a href="<?php echo esc_url($helper->add_url_param((string) $module['moreLink'])); ?>" title="<?php echo esc_attr('Accéder à la page : ' . $module['moreLabel']); ?>" class="moreLink-ad-paris-dons"
                               style="color: <?php echo esc_attr((string) ($module['textColor'] ?? '')); ?> !important;"
                            >
                                <?php echo esc_html((string) $module['moreLabel']); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($module['buttonLabel']) && !empty($module['buttonLink'])) : ?>
                            <a href="<?php echo esc_url($helper->add_url_param((string) $module['buttonLink'])); ?>" title="<?php echo esc_attr('Accéder à la page : ' . $module['buttonLabel']); ?>"
                               style="background-color: <?php echo esc_attr((string) ($module['backgroundColor'] ?? '')); ?> !important;color: <?php echo esc_attr((string) ($module['textColor'] ?? '')); ?> !important;"
                               class="button-ad-paris-dons"
                            >
                                <?php echo esc_html((string) $module['buttonLabel']); ?>
                            </a>
                        <?php endif; ?>
                        <?php
                        $buttonList = $module['buttonList'] ?? null;
                        if (is_array($buttonList) && $buttonList !== []) :
                            ?>
                            <ul class="buttonList-ad-paris-dons">
                                <?php foreach ($buttonList as $button) : ?>
                                    <?php if (!is_array($button) || empty($button['label']) || empty($button['link'])) : ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <li>
                                        <a href="<?php echo esc_url($helper->add_url_param((string) $button['link'])); ?>" title="<?php echo esc_attr('Accéder à la page : ' . $button['label']); ?>">
                                            <?php echo esc_html((string) $button['label']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

add_shortcode('ad-paris-dons-shortcode', 'ad_paris_dons_shortcode');
