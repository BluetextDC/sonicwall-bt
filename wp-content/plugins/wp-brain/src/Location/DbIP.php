<?php

namespace WPBrain\Location;

/*
 * db-ip.com
 */

class DbIP extends APILocationProvider
{
    protected $api_url = "http://api.db-ip.com/v2/{KEY}/{IP}";

    protected $api_continent = 'continentCode';
    protected $api_country = 'countryCode';
    protected $api_region = 'stateProv';

    public function __construct($api_key = NULL)
    {
        if (is_null($api_key)) {
            $api_key = wpbrain('options')->geolocation_dbip_key;
        }

        parent::__construct($api_key);
    }
}
