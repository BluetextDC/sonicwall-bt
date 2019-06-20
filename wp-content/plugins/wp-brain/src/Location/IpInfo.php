<?php

namespace WPBrain\Location;

/**
 * ipinfo.io
 */
class IpInfo extends APILocationProvider
{
    protected $api_url = "ipinfo.io/{IP}/geo/?token={KEY}";

    public function __construct($api_key = NULL)
    {
        if (is_null($api_key)) {
            $api_key = wpbrain('options')->geolocation_ipinfo_token;
        }

        parent::__construct($api_key);
    }

    protected function format($data)
    {
        if (isset($data->loc)) {
            $coordinates = explode(',', $data->loc);
            $data->latitude = $coordinates[0];
            $data->longitude = $coordinates[1];
            unset($data->loc);
        }

        return $data;
    }
}
