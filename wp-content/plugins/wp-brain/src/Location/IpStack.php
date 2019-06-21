<?php

namespace WPBrain\Location;

/*
 * ipstack.com
 */

class IpStack extends APILocationProvider
{
    protected $api_url = "http://api.ipstack.com/{IP}?access_key={KEY}";

    protected $api_continent = 'continent_code';
    protected $api_country = 'country_code';
    protected $api_region = 'region_name';

    public function __construct($api_key = NULL)
    {
        if (is_null($api_key)) {
            $api_key = wpbrain('options')->geolocation_ipstack_key;
        }

        parent::__construct($api_key);
    }
}
