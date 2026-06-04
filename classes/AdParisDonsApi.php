<?php

namespace AD_PARIS_DONS;

use AD_PARIS_DONS\AdParisDonsHelper as Helper;

class AdParisDonsApi
{
    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper();
    }

    public function getData(bool $ignoreCache = false): array|false
    {
        $dataFromCache = $this->getDataFromCache();
        if ($dataFromCache === false || $ignoreCache) {
            return $this->cacheData();
        }

        return $dataFromCache;
    }

    private function cacheData(): array|false
    {
        $data = $this->callApi();
        if ($data === false) {
            return false;
        }

        set_transient(AD_PARIS_DONS_TRANSIENT_NAME, $data, 86400);

        return $this->decodeApiPayload($data);
    }

    private function getDataFromCache(): array|false
    {
        $cached = get_transient(AD_PARIS_DONS_TRANSIENT_NAME);
        if ($cached === false || !is_string($cached)) {
            return false;
        }

        return $this->decodeApiPayload($cached);
    }

    private function decodeApiPayload(string $payload): array|false
    {
        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return false;
        }

        return $decoded;
    }

    private function callApi(): string|false
    {
        $args = [
            'user-agent' => 'AD Paris Dons',
        ];

        $response = wp_remote_get($this->helper->add_url_param(AD_PARIS_DONS_API_URL), $args);
        if (is_wp_error($response)) {
            return false;
        }

        $statusCode = (int) wp_remote_retrieve_response_code($response);
        if ($statusCode < 200 || $statusCode >= 300) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if ($body === '') {
            return false;
        }

        $date = new \DateTime();
        update_option('ad_paris_dons_api_last_call', $date->format('d/m/Y H:i'));

        return $body;
    }
}
