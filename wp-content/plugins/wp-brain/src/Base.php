<?php

namespace WPBrain;

/**
 * Base function for WP Brain controllers
 */
trait Base
{
    private $instances = [];

    public static function getInstance()
    {
        static $instance = NULL;

        if (is_null($instance)) {
            $instance = new self;
        }

        return $instance;
    }

    public function setModule($reference, $instance)
    {
        $class = get_class($instance);
        $nspos = strrpos($class, '\\');
        if ($nspos !== false) {
            $class = substr($class, $nspos + 1);
        }
        $this->{$class} = &$this->instances[$reference];
        $this->instances[$reference] = $instance;
    }

    public function hasModule($reference)
    {
        return array_key_exists($reference, $this->instances);
    }

    public function getModule($reference)
    {
        return self::array_get($this->instances, $reference);
    }

    /**
     * @return bool
     */
    public function is_elementor_active()
    {
        return defined('ELEMENTOR_VERSION') && class_exists('\Elementor\Plugin');
    }

}
