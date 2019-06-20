<?php

namespace WPBrain\Shortcodes;

use WPBrain\Utils;

/**
 * Shortcodes Manager
 */
class ShortcodesManager extends Utils
{
    private $containers = [
        'wp_brain_if',
        'wp_brain_elseif',
        'wp_brain_else',
        'wp_brain_loop',
    ];

    private $shortcodes = [];

    private $options;

    public function __construct()
    {
        $this->options = wpbrain('options');

        $files = glob(dirname(__FILE__) . '/templates/*.php');

        foreach ($files as $file) {
            $tag = basename($file, '.php');
            $this->shortcodes[$tag] = $file;

            add_shortcode($tag, $this->cb('render_shortcode'));
        }

        if ($this->options->vc_addon_enabled) {
            $this->add_action('vc_before_init');
        }
    }

    public function vc_before_init()
    {
        require dirname(__FILE__) . '/vc_mapper.php';
    }

    public function is_container($tag)
    {
        return in_array($tag, $this->containers);
    }

    public function render_shortcode($atts, $content, $tag)
    {
        if (array_key_exists($tag, $this->shortcodes)) {
            $atts = (array)$atts;

            $output = '';
            if (function_exists('vc_is_page_editable') && vc_is_page_editable()) {
                if ($this->is_container($tag)) {
                    if ($content) {
                        $output = do_shortcode($content);
                    }
                } else {
                    $output = '<strong>' . $tag . '</strong> ' . implode(', ', $atts);
                }
            } else {
                ob_start();
                require $this->shortcodes[$tag];
                $output = ob_get_clean();
            }

            $output = apply_filters("wp_brain_shortcode_output", $output, $tag, $atts, $content);

            return $output;
        }
    }
}
