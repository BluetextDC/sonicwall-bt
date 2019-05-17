<?php
N2Loader::import('libraries.form.elements.skin');

class N2ElementParticleJSPresets extends N2ElementSkin {

    protected $fixed = true;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $labels = array(
            'link'        => n2_('Link'),
            'polygons'    => n2_('Polygons'),
            'bloom'       => n2_('Bloom'),
            'web'         => n2_('Web'),
            'blackwidow'  => n2_('Black widow'),
            'zodiac'      => n2_('Zodiac'),
            'fading-dots' => n2_('Fading dots'),
            'pirouette'   => n2_('Pirouette'),
            'sparkling'   => n2_('Sparkling'),
        );

        $this->options = array(
            '0' => array(
                'label'    => n2_('Disabled'),
                'settings' => array()
            )
        );

        $folder    = NEXTEND_SMARTSLIDER_ASSETS . '/js/particlejs/presets/';
        $files     = N2Filesystem::files($folder);
        $extension = 'json';
        for ($i = 0; $i < count($files); $i++) {
            $pathInfo = pathinfo($files[$i]);
            if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {

                $jsProp = json_decode(N2Filesystem::readFile($folder . $pathInfo['filename'] . '.json'), true);

                $this->options[$pathInfo['filename']] = array(
                    'label'    => $labels[$pathInfo['filename']],
                    'settings' => array(
                        'color'      => substr($jsProp['particles']["color"]["value"], 1) . str_pad(dechex($jsProp['particles']["opacity"]["value"] * 255), 2, "0", STR_PAD_LEFT),
                        'line-color' => substr($jsProp['particles']["line_linked"]["color"], 1) . str_pad(dechex($jsProp['particles']["line_linked"]["opacity"] * 255), 2, "0", STR_PAD_LEFT),
                        'hover'      => $jsProp['interactivity']["events"]["onhover"]['enable'] ? $jsProp['interactivity']["events"]["onhover"]['mode'] : 0,
                        'click'      => $jsProp['interactivity']["events"]["onclick"]['enable'] ? $jsProp['interactivity']["events"]["onclick"]['mode'] : 0,
                        'number'     => $jsProp['particles']["number"]["value"],
                        'speed'      => $jsProp['particles']["move"]["speed"]
                    )
                );

            }
        }

        $this->options['custom'] = array(
            'label'    => n2_('Custom'),
            'settings' => array()
        );
    }
}
