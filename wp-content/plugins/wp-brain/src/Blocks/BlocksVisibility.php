<?php

namespace WPBrain\Blocks;

use WPBrain\Utils;

class BlocksVisibility extends Utils
{
    public function __construct()
    {
        $this->add_action('enqueue_block_editor_assets');
        $this->add_action('pre_render_block');
    }

    public function enqueue_block_editor_assets()
    {
        wp_enqueue_script('wpbrain-blocks', $this->url('admin/assets/js/blocks.min.js'), ['wp-blocks', 'wpbrain']);
    }

    public function pre_render_block($render, $block)
    {
        if (isset($block['attrs']['wpbrainPreset'])) {
            $preset_id = $block['attrs']['wpbrainPreset'];
            if ($preset_id) {
                $preset = wpbrain('presets')->get_preset($preset_id);

                if (!empty($preset['rules'])) {
                    $rules = $preset['rules'];

                    $visible = wpbrain('validator')->validate($rules);
                    if( !$visible ) {
                        $render = false;
                    }
                }
            }
        }

        return $render;
    }
}
