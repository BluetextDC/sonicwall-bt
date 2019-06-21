<?php

namespace WPBrain\Location;

use WPBrain\SmartObject;

/**
 * Class LocationProvider
 */
class APILocationProvider extends LocationProvider
{
    /**
     * @var string
     */
    protected $api_url;

    /**
     * @var string
     */
    protected $api_key;

    /**
     * @var string
     */
    protected $api_continent = 'continent';

    /**
     * @var string
     */
    protected $api_country = 'country';

    /**
     * @var string
     */
    protected $api_region = 'region';

    /**
     * @var string
     */
    protected $api_city = 'city';

    /**
     * @var string
     */
    protected $api_latitude = 'latitude';

    /**
     * @var string
     */
    protected $api_longitude = 'longitude';

    /**
     * LocationProvider constructor.
     *
     * @param null $api_key
     */
    public function __construct($api_key = NULL)
    {
        $this->api_key = $api_key;
    }

    /**
     * @param null $ip
     */
    public function locate($ip = NULL)
    {
        $ip = $this->ip($ip);

        $url = str_replace("{IP}", $ip, $this->api_url);
        if ($this->api_key) {
            $url = str_replace("{KEY}", $this->api_key, $url);
        }

        $data = $this->fetch($url);
        $data = $this->format($data);

        $this->continent = $data->get($this->api_continent);
        $this->country = $data->get($this->api_country);
        $this->region = $data->get($this->api_region);
        $this->city = $data->get($this->api_city);
        $this->latitude = $data->get($this->api_latitude);
        $this->longitude = $data->get($this->api_longitude);
    }

    /**
     * @param $url
     *
     * @return \WPBrain\SmartObject
     */
    private function fetch($url)
    {
        $json = "";

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                ],
            ]);
            $json = curl_exec($ch);
            curl_close($ch);
        }

        return new SmartObject(json_decode($json, true));
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function format($data)
    {
        return $data;
    }
}
