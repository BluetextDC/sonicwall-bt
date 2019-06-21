<?php

namespace WPBrain\Presets;

use WPBrain\Utils;
use WPBrain\SmartObject;

/**
 * Presets Manager
 */
class PresetsManager extends Utils
{
    /**
     * @var array
     */
    private $presets = [];

    public function save_presets($presets)
    {
        $existing_ids = [];
        foreach ($presets as $preset) {
            if (!empty($preset['id'])) {
                $existing_ids[] = $preset['id'];
            }
        }

        $existing_ids = array_unique($existing_ids, SORT_STRING);

        foreach ($presets as &$preset) {
            if (empty($preset['id'])) {
                do {
                    $uniqid = uniqid();
                } while (in_array($uniqid, $existing_ids));

                $existing_ids[] = $uniqid;
                $preset['id'] = $uniqid;
            }
        }

        $presets = apply_filters('wp_brain_save_presets', $presets);
        update_option('wp_brain_presets', $presets);
    }

    public function get_presets($default = [])
    {
        if (empty($this->presets)) {
            $presets = get_option('wp_brain_presets', $default);
            $presets = apply_filters('wp_brain_get_presets', $presets, $default);
            $this->presets = $presets;
        }

        return $this->presets;
    }

    public function get_preset($preset_id)
    {
        $presets = $this->get_presets();
        $preset = NULL;
        foreach ($presets as $p) {
            if ($p['id'] == $preset_id) {
                $preset = $p;
                break;
            }
        }
        $preset = apply_filters('wp_brain_get_preset', $preset, $preset_id);

        return $preset;
    }
}
