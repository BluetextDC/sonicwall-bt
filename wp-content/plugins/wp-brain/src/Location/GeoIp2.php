<?php

namespace WPBrain\Location;

use GeoIp2\Database\Reader;
use Exception;

/**
 * ipinfo.io
 */
class GeoIp2 extends LocationProvider
{
    private $reader = NULL;

    public function __construct()
    {
        $file = wpbrain('options')->geolocation_geoip2_mmdb;

        if (empty($file) || strpos($file, '.mmdb') === false || !file_exists($file)) {
            $file = WPBRAIN_DIR . 'data/GeoLite2-City.mmdb';
        }

        try {
            $this->reader = new Reader($file);
        } catch (Exception $e) {
        }
    }

    public function locate($ip = NULL)
    {
        if ($this->reader) {
            $ip = $this->ip($ip);

            try {
                $record = $this->reader->city($ip);
            } catch (Exception $e) {
                return;
            }

            // Get continent code
            if (isset($record->continent->code)) {
                $this->continent = $record->continent->code;
            }

            // Get country code
            if (isset($record->country->isoCode)) {
                $this->country = $record->country->isoCode;
            }

            // Get region name
            if (isset($record->subdivisions[0]->name)) {
                $this->region = $record->subdivisions[0]->name;
            }

            // Get city name
            if (isset($record->city->name)) {
                $this->city = $record->city->name;
            }

            // Get latitude
            if (isset($record->location->latitude)) {
                $this->latitude = $record->location->latitude;
            }

            // Get longitude
            if (isset($record->location->longitude)) {
                $this->longitude = $record->location->longitude;
            }
        }
    }
}
