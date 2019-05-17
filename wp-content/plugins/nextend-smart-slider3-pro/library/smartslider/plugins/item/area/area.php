<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'area';

    protected $priority = 100;

    protected $class = 'N2SSItemArea';

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    public function __construct() {
        $this->title = n2_x('Area', 'Slide item');
        $this->group = n2_x('Advanced', 'Layer group');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'width'        => '',
            'height'       => '',
            'color'        => '000000ff',
            'gradient'     => 'off',
            'color2'       => '000000ff',
            'css'          => '',
            'borderWidth'  => 0,
            'borderColor'  => 'ffffff1f',
            'borderRadius' => 0,
            'href'         => '#',
            'href-target'  => '_self',
            'href-rel'     => '',
        );
    }

    public function upgradeData($data) {
        $linkV1 = $data->get('link', '');
        if (!empty($linkV1)) {
            list($link, $target, $rel) = array_pad((array)N2Parse::parse($linkV1), 3, '');
            $data->un_set('link');
            $data->set('href', $link);
            $data->set('href-target', $target);
            $data->set('href-rel', $rel);
        }
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-area');

        $colors = new N2ElementGroup($settings, 'item-area-colors');
        new N2ElementColor($colors, 'color', n2_('Background color'), '00000000', array(
            'alpha' => true
        ));

        new N2ElementList($colors, 'gradient', n2_('Gradient'), 'off', array(
            'options' => array(
                'off'        => n2_('Off'),
                'vertical'   => '&darr;',
                'horizontal' => '&rarr;',
                'diagonal1'  => '&#8599;',
                'diagonal2'  => '&#8600;'
            )
        ));

        new N2ElementColor($colors, 'color2', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        $size = new N2ElementGroup($settings, 'item-area-size');
        new N2ElementNumber($size, 'width', n2_('Width'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));
        new N2ElementNumber($size, 'height', n2_('Height'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));

        new N2ElementTextarea($settings, 'css', n2_('Custom CSS'), '', array(
            'rowClass' => 'n2-expert'
        ));

        $border = new N2ElementGroup($settings, 'item-area-border');
        new N2ElementNumber($border, 'borderRadius', n2_('Border radius'), 0, array(
            'wide' => 3,
            'unit' => 'px'
        ));
        new N2ElementNumber($border, 'borderWidth', n2_('Border'), 0, array(
            'wide' => 3,
            'min'  => 0,
            'unit' => 'px'
        ));
        new N2ElementColor($border, 'borderColor', n2_('Color'), '00000000', array(
            'alpha' => true
        ));

        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'));
    }


}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryArea);
