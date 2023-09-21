<?php

namespace AD_PARIS_DONS;
use AD_PARIS_DONS\AdParisDonsHelper as Helper;

class AdParisDonsApi
{
    private $helper;

    public function __construct()
    {
        $this->helper = new Helper();
    }

    public function getData($ignoreCache = false)
    {
        $dataFromCache = $this->getDataFromCache();
        if (!$dataFromCache || $ignoreCache) {
            return $this->cacheData();
        }

        return $dataFromCache;
    }

    private function cacheData()
    {
        $data = $this->callApi();
        if ($data) {
            set_transient(AD_PARIS_DONS_TRANSIENT_NAME, $data, '86400');
            return json_decode($data, true);
        }
        return false;
    }

    private function getDataFromCache()
    {
        if (get_transient(AD_PARIS_DONS_TRANSIENT_NAME)) {
            return json_decode(get_transient(AD_PARIS_DONS_TRANSIENT_NAME), true);
        }
        return false;
    }

    private function callApi()
    {
        $args = [
            'user-agent' => 'AD Paris Dons',
        ];

        $response = wp_remote_get($this->helper->add_url_param(AD_PARIS_DONS_API_URL), $args);
        $body = wp_remote_retrieve_body($response);

        $date = new \DateTime();
        update_option('ad_paris_dons_api_last_call', $date->format('d/m/Y H:i'));

        return $body;
    }
}