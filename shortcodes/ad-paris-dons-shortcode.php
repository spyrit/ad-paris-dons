<?php
function ad_paris_dons_shortcode()
{
    $api = new AD_PARIS_DONS\AdParisDonsApi();
    $helper = new AD_PARIS_DONS\AdParisDonsHelper();
    $modules = $api->getData();
    ob_start();
        if ($modules['results']): ?>
            <div class="ad-paris-dons-wrapper">
                <div class="ad-paris-dons">
                    <?php foreach ($modules['results'] as $module): ?>
                        <div class="module">
                            <div class="title"
                                 style="background-color: <?php echo $module['backgroundColor'] ?> !important;color: <?php echo $module['textColor']; ?> !important;"
                            ><?php echo $module['title'] ?></div>
                            <div class="content" style="background-image: url('<?php echo $module['image'] ?>') !important;background-color:<?php echo $module['backgroundColorImage'] ?> !important;">
                                <?php if ($module['moreLabel'] && $module['moreLink']): ?>
                                    <a href="<?php echo $helper->add_url_param($module['moreLink']) ?>" title="Accéder à la page : <?php echo $module['moreLabel']; ?>" class="moreLink"
                                       style="color: <?php echo $module['textColor']; ?> !important;"
                                    >
                                        <?php echo $module['moreLabel']; ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($module['buttonLabel'] && $module['buttonLink']): ?>
                                    <a href="<?php echo $helper->add_url_param($module['buttonLink']) ?>" title="Accéder à la page : <?php echo $module['buttonLabel']; ?>"
                                       style="background-color: <?php echo $module['backgroundColor'] ?> !important;color: <?php echo $module['textColor']; ?> !important;"
                                       class="button"
                                    >
                                        <?php echo $module['buttonLabel']; ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($module['buttonList'] && count($module['buttonList']) > 0): ?>
                                    <ul class="buttonList">
                                        <?php foreach ($module['buttonList'] as $button): ?>
                                            <?php if ($button['label'] && $button['link']): ?>
                                                <li>
                                                    <a href="<?php echo $helper->add_url_param($button['link']) ?>" title="Accéder à la page : <?php echo $button['link']; ?>">
                                                        <?php echo $button['label']; ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Configuration requise</p>
        <?php endif; ?>
    <?php return ob_get_clean();
}
add_shortcode('ad-paris-dons-shortcode', 'ad_paris_dons_shortcode');
