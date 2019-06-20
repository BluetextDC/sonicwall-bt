<?php

namespace WPBrain\Location;

/**
 * Class LocationProvider
 */
class LocationProvider
{
    private $eu_countries = [
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE',
        'EL', 'ES', 'FR', 'HR', 'IT', 'CY', 'LV',
        'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL',
        'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK',
    ];

    /**
     * @var string
     */
    protected $continent = '';

    /**
     * @var string
     */
    protected $country = '';

    /**
     * @var string
     */
    protected $region = '';

    /**
     * @var string
     */
    protected $city = '';

    /**
     * @var string
     */
    protected $latitude = '';

    /**
     * @var string
     */
    protected $longitude = '';

    /**
     * @param null $ip
     *
     * @return null
     */
    public function ip($ip = NULL)
    {
        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @param null $ip
     */
    public function locate($ip = NULL)
    {
    }

    /**
     * @return bool
     */
    public function isEuMember()
    {
        return $this->country && in_array($this->country, $this->eu_countries);
    }

    /**
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
