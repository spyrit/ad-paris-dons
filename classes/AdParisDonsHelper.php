<?php

namespace AD_PARIS_DONS;

class AdParisDonsHelper
{
    public function get_code_denier()
    {
        $options = get_option('ad_paris_dons_config');
        $code = isset($options['code-denier']) ? $options['code-denier'] : '0';

        return $code;
    }

    public function add_url_param($url)
    {
        $codeDenier = $this->get_code_denier();
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            $url .= '&affectation='.$codeDenier;
        } else {
            $url .= '?affectation='.$codeDenier;
        }
        return $url;
    }
}