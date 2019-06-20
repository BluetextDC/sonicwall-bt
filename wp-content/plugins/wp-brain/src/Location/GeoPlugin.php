<?php

namespace WPBrain\Location;

/*
 * geoplugin.com
 */

class GeoPlugin extends APILocationProvider
{
    protected $api_url = "http://www.geoplugin.net/json.gp?ip={IP}";

    protected $api_continent = 'geoplugin_continentCode';
    protected $api_country = 'geoplugin_countryCode';
    protected $api_region = 'geoplugin_region';
    protected $api_city = 'geoplugin_city';
    protected $api_latitude = 'geoplugin_latitude';
    protected $api_longitude = 'geoplugin_longitude';
}
