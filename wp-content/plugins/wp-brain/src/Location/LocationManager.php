<?php

namespace WPBrain\Location;

use WPBrain\SmartObject;

class LocationManager
{
    private $providers = [
        'GeoIp2' => "MaxMind GeoIp2",
        'GeoPlugin' => "geoplugin.com",
        'GeoBytes' => "geobytes.com",
        'IpData' => "ipdata.co",
        'IpApi' => "ip-api.com",
        'DbIP' => "db-ip.com",
        'IpStack' => "ipstack.com",
        'IpInfo' => "ipinfo.io",
    ];

    /**
     * @param string $provider_id
     * @param string $key
     *
     * @return \WPBrain\Location\LocationProvider|null
     */
    public function provider($provider_id = NULL)
    {
        if (is_null($provider_id)) {
            $provider_id = wpbrain('options')->geolocation_provider;
        }

        $provider = NULL;
        if (key_exists($provider_id, $this->providers)) {
            $class_name = __NAMESPACE__ . '\\' . $provider_id;
            if (class_exists($class_name)) {
                $provider = new $class_name();
            }
        }

        return $provider;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
