<?php

namespace WPBrain\Location;

/*
 * geobytes.com
 */

class GeoBytes extends APILocationProvider
{
    protected $api_url = "http://gd.geobytes.com/GetCityDetails/?fqcn={IP}";

    protected $api_country = 'geobytesinternet';
    protected $api_region = 'geobytesregion';
    protected $api_city = 'geobytescity';
    protected $api_latitude = 'geobyteslatitude';
    protected $api_longitude = 'geobyteslongitude';
}
