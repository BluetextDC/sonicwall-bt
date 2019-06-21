<?php

namespace WPBrain\Location;

/*
 * ipdata.co
 */

class IpData extends APILocationProvider
{
    protected $api_url = "https://api.ipdata.co/{IP}";

    protected $api_continent = 'continent_code';
    protected $api_country = 'country_code';
}
