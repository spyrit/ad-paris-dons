<?php

namespace AD_PARIS_DONS;

class AdParisDonsHelper
{
    public function get_code_denier(): string
    {
        $options = get_option('ad_paris_dons_config');
        if (!is_array($options)) {
            return '0';
        }

        return isset($options['code-denier']) ? (string) $options['code-denier'] : '0';
    }

    public function add_url_param(string $url): string
    {
        $codeDenier = $this->get_code_denier();
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            $url .= '&affectation=' . rawurlencode($codeDenier);
        } else {
            $url .= '?affectation=' . rawurlencode($codeDenier);
        }

        return $url;
    }
}
