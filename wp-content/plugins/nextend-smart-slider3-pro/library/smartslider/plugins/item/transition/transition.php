<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryTransition extends N2SSPluginItemFactoryAbstract {

    protected $type = 'transition';

    protected $priority = 7;

    protected $layerProperties = array("desktopportraitwidth" => 200);

    protected $class = 'N2SSItemTransition';

    public function __construct() {
        $this->title = n2_x('Transition', 'Slide item');
        $this->group = n2_x('Image', 'Layer group');
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/transition.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    function getValues() {
        return array(
            'animation'      => 'Fade',
            'image'          => '$system$/images/placeholder/imagefront.png',
            'image2'         => '$system$/images/placeholder/imageback.png',
            'alt'            => '',
            'alt2'           => '',
            'href'           => '#',
            'href-target'    => '_self',
            'href-rel'       => '',
            'image-optimize' => 1
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
        $data->set('image2', $slide->fill($data->get('image2', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addImage($data->get('image2'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image', '')));
        $data->set('image2', $import->fixImage($data->get('image2', '')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));
        $data->set('image2', N2ImageHelper::fixed($data->get('image2')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-transition');


        new N2ElementList($settings, 'animation', n2_('Animation'), '', array(
            'options' => array(
                'Fade'           => n2_('Fade'),
                'VerticalFlip'   => n2_('Vertical flip'),
                'HorizontalFlip' => n2_('Horizontal flip')
            )
        ));

        $images = new N2ElementGroup($settings, 'item-transition-images');

        new N2ElementImage($images, 'image', n2_('Image'), '', array(
            'fixed'      => true,
            'style'      => 'width:236px;',
            'relatedAlt' => 'item_transitionalt'
        ));

        new N2ElementImage($images, 'image2', n2_('Image'), '', array(
            'fixed'      => true,
            'style'      => 'width:236px;',
            'relatedAlt' => 'item_transitionalt2'
        ));
        new N2ElementText($images, 'alt', 'SEO - ' . n2_('Alt tag'), '', array(
            'style' => 'width:280px;'
        ));
        new N2ElementText($images, 'alt2', 'SEO - ' . n2_('Alt tag'), '', array(
            'style' => 'width:280px;'
        ));

        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'), '');

        new N2ElementOnOff($settings, 'image-optimize', n2_('Optimize image'), 1, array(
            'rowClass' => 'n2-expert'
        ));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryTransition);
