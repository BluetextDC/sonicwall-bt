<?php
class N2SSPluginItemFactoryIcon2 extends N2SSPluginItemFactoryAbstract {

    public $type = 'icon2';

    protected $priority = 5;

    protected $layerProperties = array("desktopportraitwidth" => 50);

    protected $class = 'N2SSItemIcon2';

    public function __construct() {
        $this->title = n2_x('Icon', 'Slide item');
        $this->group = n2_x('Image', 'Layer group');
    }

    function getValues() {
        return array(
            'icon'        => 'fa:smile-o',
            'color'       => '00000080',
            'colorhover'  => '00000000',
            'size'        => 100,
            'href'        => '#',
            'href-target' => '_self',
            'href-rel'    => '',
            'style'       => ''
        );
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
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

        $data->set('icon', $slide->fill($data->get('icon', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-icon2');

        $icon = new N2ElementGroup($settings, 'item-icon2-icon');
        new N2ElementIcon2Manager($icon, 'icon', n2_('Icon'));
        new N2ElementColor($icon, 'color', n2_('Color'), '00000080', array(
            'alpha' => true
        ));
        new N2ElementColor($icon, 'colorhover', n2_('Hover color'), '00000000', array(
            'alpha' => true
        ));

        new N2ElementNumberSlider($settings, 'size', n2_('Size'), 24, array(
            'min'       => 4,
            'max'       => 10000,
            'sliderMax' => 200,
            'step'      => 4,
            'wide'      => 3,
            'unit'      => 'px'
        ));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Icon'), '', array(
            'previewMode' => 'box',
            'preview'     => '<div class="{styleClassName}" style="width:{nextend.activeLayer.find(\'.n2-ss-item\').width()}px;height:{nextend.activeLayer.find(\'.n2-ss-item\').height()}px;">{nextend.activeLayer.find(\'img\').clone().wrap(\'<p>\').parent().html()}</div>',
            'rowClass'    => 'n2-hidden'
        ));


        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryIcon2);
