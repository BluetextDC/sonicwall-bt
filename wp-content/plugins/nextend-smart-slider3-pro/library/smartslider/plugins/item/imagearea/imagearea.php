<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryImageArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'imagearea';

    protected $priority = 6;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected $class = 'N2SSItemImageArea';

    public function __construct() {
        $this->title = n2_x('Image area', 'Slide item');
        $this->group = n2_x('Image', 'Layer group');
    }

    function getValues() {
        return array(
            'image'       => '$system$/images/placeholder/image.png',
            'href'        => '#',
            'href-target' => '_self',
            'href-rel'    => '',
            'fillmode'    => 'cover',
            'positionx'   => 50,
            'positiony'   => 50
        );
    }

    function getPath() {
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

        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-imagearea');

        new N2ElementImage($settings, 'image', n2_('Image'), '', array(
            'fixed'      => true,
            'style'      => 'width:236px;',
            'relatedAlt' => 'item_imagealt'
        ));

        $background = new N2ElementGroup($settings, 'item-imagearea-background');
        new N2ElementList($background, 'fillmode', n2_('Fill mode'), 'cover', array(
            'options' => array(
                'cover'   => n2_('Fill'),
                'contain' => n2_('Fit')
            )
        ));

        $backgroundPosition = new N2ElementGroup($background, 'item-imagearea-background-position', n2_('Background position'));
        new N2ElementNumber($backgroundPosition, 'positionx', '', 50, array(
            'min'      => 0,
            'max'      => 100,
            'wide'     => 3,
            'sublabel' => 'X',
            'unit'     => '%'
        ));
        new N2ElementNumber($backgroundPosition, 'positiony', '', 50, array(
            'min'      => 0,
            'max'      => 100,
            'wide'     => 3,
            'sublabel' => 'Y',
            'unit'     => '%'
        ));

        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryImageArea);
