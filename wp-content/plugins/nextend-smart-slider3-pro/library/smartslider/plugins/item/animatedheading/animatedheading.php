<?php

N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryAnimatedHeading extends N2SSPluginItemFactoryAbstract {

    protected $type = 'animatedHeading';

    protected $priority = 100;

    private $font = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"36||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{},{}]}';

    private $style = '{"name":"Static","data":[{},{"padding":"0|*|0|*|0|*|0|*|px"},{}]}';

    protected $class = 'N2SSItemAnimatedHeading';

    public function __construct() {
        $this->title = n2_x('Animated Heading', 'Slide item');
        $this->group = n2_('Content');
    }

    function getValues() {
        self::initDefault();

        return array(
            'type'          => 'slide',
            'color'         => 'ffffffff',
            'color2'        => '000000',
            'loop'          => 0,
            'delay'         => 0,
            'speed'         => 100,
            'show-duration' => 1500,
            'animate-width' => 1,

            'priority'      => 'div',
            'before-text'   => 'We are Passionate About',
            'animated-text' => "Amazing Food\nGreat Hospitality",
            'href'          => '#',
            'href-target'   => '_self',
            'href-rel'      => '',
            'after-text'    => '',
            'title'         => '',

            'font'  => $this->font,
            'style' => $this->style,

            'class' => ''
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

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/animatedHeading.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    private function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-font');
            if (is_array($res)) {
                $this->font = $res['value'];
            }
            if (is_numeric($this->font)) {
                N2FontRenderer::preLoad($this->font);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-style');
            if (is_array($res)) {
                $this->style = $res['value'];
            }
            if (is_numeric($this->style)) {
                N2StyleRenderer::preLoad($this->style);
            }
            $inited = true;
        }
    }

    public function globalDefaultItemFontAndStyle($fontTab, $styleTab) {
        self::initDefault();

        new N2ElementFont($fontTab, 'item-heading-font', n2_('Item') . ' - ' . n2_('Heading'), $this->font, array(
            'previewMode' => 'hover'
        ));

        new N2ElementStyle($styleTab, 'item-heading-style', n2_('Item') . ' - ' . n2_('Heading'), $this->style, array(
            'previewMode' => 'heading'
        ));
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-animated-heading');

        $highlight = new N2ElementGroup($settings, 'item-animated-heading-highlight');

        new N2ElementList($highlight, 'type', n2_('Type'), '', array(
            'options'            => array(
                'fade'        => n2_('Fade'),
                'rotating'    => n2_('Rotating'),
                'drop-in'     => n2_('Drop-in'),
                'slide'       => n2_('Slide'),
                'slide-down'  => n2_('Slide down'),
                'typewriter1' => n2_('Typewriter 1'),
                'typewriter2' => n2_('Typewriter 2'),
                'chars'       => n2_('Chars'),
                'chars2'      => n2_('Chars 2')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'fade',
                        'rotating',
                        'drop-in',
                        'slide',
                        'slide-down',
                        'chars',
                        'chars2'
                    ),
                    'field'  => 'item_animatedHeadinganimate-width'
                ),
                array(
                    'values' => array(
                        'typewriter1',
                        'typewriter2'
                    ),
                    'field'  => 'item_animatedHeadingcolor'
                ),
                array(
                    'values' => array(
                        'typewriter2'
                    ),
                    'field'  => 'item_animatedHeadingcolor2'
                )
            )
        ));
        new N2ElementOnOff($highlight, 'animate-width', n2_('Auto width'), 1);
        new N2ElementColor($highlight, 'color', n2_('Cursor Color'), '', array(
            'alpha' => true
        ));
        new N2ElementColor($highlight, 'color2', n2_('Text Color'), '');

        new N2ElementText($settings, 'before-text', n2_('Before text'), '', array(
            'style' => 'width: 230px;'
        ));

        new N2ElementTextarea($settings, 'animated-text', n2_('Animated text'), '', array(
            'fieldStyle' => 'height: 70px; width: 230px;resize: vertical;'
        ));

        new N2ElementText($settings, 'after-text', n2_('After text'), '', array(
            'style' => 'width: 230px;'
        ));


        $animation = new N2ElementGroup($settings, 'item-animated-heading-animation');

        new N2ElementOnOff($animation, 'loop', n2_('Loop'), 1);

        new N2ElementNumber($animation, 'delay', n2_('Delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5,
            'min'  => 0
        ));
        new N2ElementNumberSlider($animation, 'speed', n2_('Speed'), 100, array(
            'style'     => 'width:35px;',
            'unit'      => '%',
            'min'       => 10,
            'max'       => 400,
            'step'      => 1,
            'sliderMax' => 150
        ));
        new N2ElementNumber($animation, 'show-duration', n2_('Show duration'), 1500, array(
            'unit' => 'ms',
            'wide' => 5,
            'min'  => 0
        ));

        $link = new N2ElementGroup($settings, 'link', '');
        new N2ElementUrl($link, 'href', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementLinkTarget($link, 'href-target', n2_('Target window'));
        new N2ElementLinkRel($link, 'href-rel', n2_('Rel'));

        $other = new N2ElementGroup($settings, 'item-highlightheading-other');
        new N2ElementList($other, 'priority', 'Tag', 'div', array(
            'options' => array(
                'div' => 'div',
                '1'   => 'H1',
                '2'   => 'H2',
                '3'   => 'H3',
                '4'   => 'H4',
                '5'   => 'H5',
                '6'   => 'H6'
            )
        ));

        new N2ElementFont($settings, 'font', n2_('Font') . ' - ' . n2_('Heading'), '', array(
            'previewMode' => 'highlight',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};"><div class="{styleClassName} {fontClassName}">Lorem ipsum dolor sit amet, <div style="display:inline-block;">consectetur</div> adipiscing elit</div></div>',
            'set'         => 1000,
            'style'       => 'item_animatedHeadingstyle',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Heading'), '', array(
            'previewMode' => 'highlight',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};"><div class="{styleClassName} {fontClassName}">Lorem ipsum dolor sit amet, <div style="display:inline-block;">consectetur</div> adipiscing elit</div></div>',
            'set'         => 1000,
            'font'        => 'item_animatedHeadingfont',
            'rowClass'    => 'n2-hidden'
        ));


        new N2ElementText($settings, 'class', n2_('Custom CSS classes'), '', array(
            'style'    => 'width:174px;',
            'rowClass' => 'n2-expert'
        ));

    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryAnimatedHeading);
