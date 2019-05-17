<?php
class N2SSPluginItemFactoryIcon extends N2SSPluginItemFactoryAbstract {

    public $type = 'icon';

    protected $priority = 8;

    protected $layerProperties = array("desktopportraitwidth" => 50);

    protected $class = 'N2SSItemIcon';

    public function __construct() {
        $this->title = n2_x('Icon - Legacy', 'Slide item');
        $this->group = n2_x('Advanced', 'Layer group');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="32" height="32"><rect width="100" height="100" data-style="{style}" /></svg>',
            'color'       => '00000080',
            'color-hover' => '00000000',
            'size'        => '100%|*|auto',
            'href'        => '#',
            'href-target' => '_self',
            'href-rel'    => '',
            'style'       => ''
        );
    }

    public function isLegacy() {
        return true;
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
        $settings = new N2Tab($form, 'item-icon');
        new N2ElementTextarea($settings, 'icon', n2_('Icon'), '', array(
            'fieldStyle' => 'width: 236px;height: 200px;resize: vertical;'
        ));

        $icon = new N2ElementGroup($settings, 'item-icon-icon');
        new N2ElementColor($icon, 'color', n2_('Color'), '00000080', array(
            'alpha' => true
        ));
        new N2ElementColor($icon, 'color-hover', n2_('Hover color'), '00000000', array(
            'alpha' => true
        ));

        $size = new N2ElementMixed($settings, 'size', '', '');
        new N2ElementText($size, 'size-width', n2_('Width'), '', array(
            'style' => 'width:100px;'
        ));
        new N2ElementText($size, 'size-height', n2_('Height'), '', array(
            'style' => 'width:100px;'
        ));

        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Icon'), '', array(
            'previewMode' => 'box',
            'preview'     => '<div class="{styleClassName}" style="width:{nextend.activeLayer.find(\'.n2-ss-item\').width()}px;height:{nextend.activeLayer.find(\'.n2-ss-item\').height()}px;">{nextend.activeLayer.find(\'img\').clone().wrap(\'<p>\').parent().html()}</div>',
            'rowClass'    => 'n2-hidden'
        ));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryIcon);
