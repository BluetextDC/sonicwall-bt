<?php

namespace WPBrain\Location;

/*
 * ip-api.com
 */

class IpApi extends APILocationProvider
{
    protected $api_url = "http://ip-api.com/json/{IP}";

    protected $api_country = 'countryCode';
    protected $api_region = 'regionName';
    protected $api_latitude = 'lat';
    protected $api_longitude = 'lon';
}
