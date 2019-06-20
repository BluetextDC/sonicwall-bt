<?php

namespace WPBrain;

/**
 * Class Utils
 * @package WPBrain
 */
class Utils
{
    /**
     * @param array          $array
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function array_get($array, $key, $alt = NULL)
    {
        return isset($array[$key]) ? $array[$key] : $alt;
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function REQUEST($key, $alt = NULL)
    {
        return self::array_get($_REQUEST, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function GET($key, $alt = NULL)
    {
        return self::array_get($_GET, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function POST($key, $alt = NULL)
    {
        return self::array_get($_POST, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function SESSION($key, $alt = NULL)
    {
        return self::array_get($_SESSION, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function COOKIE($key, $alt = NULL)
    {
        return self::array_get($_COOKIE, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function SERVER($key, $alt = NULL)
    {
        return self::array_get($_SERVER, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function ENV($key, $alt = NULL)
    {
        return self::array_get($_ENV, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function GLOBALS($key, $alt = NULL)
    {
        return self::array_get($GLOBALS, $key, $alt);
    }

    /**
     * @param string|integer $key
     * @param mixed          $value
     * @param int            $days
     *
     * @return bool
     */
    public static function set_cookie($key, $value, $days = 1)
    {
        $lifetime = time() + $days * DAY_IN_SECONDS;

        return setcookie($key, $value, $lifetime, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
    }

    /**
     * @param string|integer $key
     *
     * @return bool
     */
    public static function del_cookie($key)
    {
        return self::set_cookie($key, '', -1);
    }

    /**
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public static function flash_cookie($key, $alt = NULL)
    {
        $value = self::COOKIE($key, $alt);
        if ($value) {
            self::del_cookie($key);
        }

        return $value;
    }

    /**
     * @param string       $name
     * @param integer|null $id
     *
     * @return mixed
     */
    public static function pmeta($name, $id = NULL)
    {
        global $post;
        $id = $id ? $id : $post->ID;

        return get_post_meta($id, $name, true);
    }

    /**
     * @param string       $name
     * @param integer|null $id
     *
     * @return mixed
     */
    public static function umeta($name, $id = NULL)
    {
        global $current_user;
        $id = $id ? $id : $current_user->ID;

        return get_user_meta($id, $name, true);
    }

    /**
     * @return \WPBrain\SmartObject
     */
    public function get_options()
    {
        static $options = NULL;

        if (is_null($options)) {
            $options = get_option('wp_brain_options') ?: [];
            $default = [
                'vc_addon_enabled' => true,
                'elementor_addon_enabled' => true,
                'tinymce_plugin_enabled' => true,
                'widgets_visibility_enabled' => true,
                'menus_visibility_enabled' => true,
                'menus_visibility_noconflict' => false,
                'geolocation_provider' => "GeoIp2",
                'geolocation_geoip2_mmdb' => "",
                'geolocation_dbip_key' => "free",
                'geolocation_ipstack_key' => "",
                'geolocation_ipinfo_token' => "",
            ];

            $options = array_merge($default, $options);

            $options = apply_filters('wp_brain_get_options', $options);
        }

        return new SmartObject($options);
    }

    /**
     * @param array|object $data
     *
     * @return string
     */
    public static function export($data)
    {
        if (!empty($data) && (is_array($data) || is_object($data))) {
            return base64_encode(json_encode($data));
        }
    }

    /**
     * @param array|object $data
     */
    public static function e_export($data)
    {
        echo self::export($data);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public static function import($string)
    {
        return json_decode(base64_decode($string));
    }

    /**
     * @param mixed $checked
     *
     * @return string
     */
    public static function checked($checked)
    {
        if ($checked) {
            return ' checked="checked"';
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public static function checkbox($name, $value)
    {
        $id = str_replace('[', '_', $name);
        $id = str_replace(']', '', $id);
        ?>
        <input type="hidden" name="<?php echo $name ?>" value="false"><label class="checkbox-switch">
        <input type="checkbox" id="<?php echo $id ?>" name="<?php echo $name ?>" <?php echo self::checked($value) ?> value="true">
        <span class="slider"></span> </label>
        <?php
    }

    /**
     * @param array      $choices
     * @param mixed|null $current
     * @param bool       $echo
     *
     * @return string
     */
    public static function html_options($choices, $current = NULL, $echo = true)
    {
        $options = '';
        if (is_array($choices)) {
            foreach ($choices as $value => $label) {
                $selected = selected($value, $current, false);
                $options .= sprintf('<option value="%s"%s>%s</option>', $value, $selected, $label);
            }
        }
        if ($echo) {
            echo $options;
        } else {
            return $options;
        }
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public function url($path = NULL)
    {
        $url = WPBRAIN_URL . $path;
        if ($path) {
            $file = $this->path($path);
            if (is_file($file)) {
                $mtime = filemtime($file);
                $url .= "?mt=$mtime";
            }
        }

        return $url;
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public function path($path = NULL)
    {
        $base = WPBRAIN_DIR;
        if (DIRECTORY_SEPARATOR !== '/') {
            $base = str_replace('\\', '/', $base);
        }
        return $base . trim($path);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function cb($name)
    {
        if (method_exists($this, $name)) {
            return [&$this, $name];
        }
    }

    /**
     * @param string              $handler
     * @param string|integer|null $arg1
     * @param integer|null        $arg2
     * @param integer|null        $arg3
     */
    protected function add_action($handler, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL)
    {
        if (is_string($arg1)) {
            $callback = $this->cb($arg1);
            $priority = $arg2;
            $args_num = $arg3;
        } else {
            $method = str_replace('-', '_', $handler);
            $callback = $this->cb($method);
            $priority = $arg1;
            $args_num = $arg2;
        }

        if (is_null($priority)) {
            $priority = 11;
        }
        if (is_null($args_num)) {
            $args_num = 5;
        }

        add_action($handler, $callback, $priority, $args_num);
    }

    /**
     * @param string              $handler
     * @param string|integer|null $arg1
     * @param integer|null        $arg2
     */
    protected function remove_action($handler, $arg1 = NULL, $arg2 = NULL)
    {
        if (is_string($arg1)) {
            $callback = $this->cb($arg1);
            $priority = $arg2;
        } else {
            $method = str_replace('-', '_', $handler);
            $callback = $this->cb($method);
            $priority = $arg1;
        }

        if (is_null($priority)) {
            $priority = 11;
        }

        remove_action($handler, $callback, $priority);
    }

    /**
     * @param string              $handler
     * @param string|integer|null $arg1
     * @param integer|null        $arg2
     * @param integer|null        $arg3
     */
    protected function add_filter($handler, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL)
    {
        if (is_string($arg1)) {
            $callback = $this->cb($arg1);
            $priority = $arg2;
            $args_num = $arg3;
        } else {
            $method = str_replace('-', '_', $handler);
            $callback = $this->cb($method);
            $priority = $arg1;
            $args_num = $arg2;
        }

        if (is_null($priority)) {
            $priority = 11;
        }
        if (is_null($args_num)) {
            $args_num = 5;
        }

        add_filter($handler, $callback, $priority, $args_num);
    }

    /**
     * @param string              $handler
     * @param string|integer|null $arg1
     * @param integer|null        $arg2
     */
    protected function remove_filter($handler, $arg1 = NULL, $arg2 = NULL)
    {
        if (is_string($arg1)) {
            $callback = $this->cb($arg1);
            $priority = $arg2;
        } else {
            $method = str_replace('-', '_', $handler);
            $callback = $this->cb($method);
            $priority = $arg1;
        }

        if (is_null($priority)) {
            $priority = 11;
        }

        remove_filter($handler, $callback, $priority);
    }
}
