<?php

class N2SmartsliderLayersModel extends N2Model {

    /**
     * @var N2SmartSliderBackend
     */
    private $renderable;

    /**
     * N2SmartsliderLayersModel constructor.
     *
     * @param N2SmartSliderRenderableAbstract $renderable
     */
    public function __construct($renderable) {
        $this->renderable = $renderable;

        parent::__construct();
    }

    function renderForm($data = array()) {
        N2Loader::import('libraries.animations.manager');
    


        N2Loader::import('libraries.form.form');
        $form = new N2Form();
        $form->getContext()
             ->set('renderable', $this->renderable);
        $form->loadArray($data);

        $sidebar = new N2TabTabbedSidebar($form, 'slide-editor-settings', '', array(
            'classes'    => 'n2-sidebar-tab-switcher n2-tab-bordered n2-sidebar-tab-bg',
            'active'     => 1,
            'underlined' => 1
        ));

        $this->formItem($sidebar);

        $this->formRow($sidebar);

        $this->formColumn($sidebar);

        $this->formContent($sidebar);

        $this->formDesign($sidebar);
        $this->formAnimations($sidebar);
    

        $this->formProperties($sidebar);
        $this->formGroup($sidebar);
    

        echo $form->render('layer');
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formItem($form) {

        $_item = new N2TabGrouppedSidebar($form, 'item', '', array(
            'icon' => 'n2-i-window-layer',
            'tip'  => n2_('Layer')
        ));

        $item = new N2Tab($_item, 'row-settings');

        new N2ElementItems($item, 'items');
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formRow($form) {


        $_rowSettings = new N2TabGrouppedSidebar($form, 'row', '', array(
            'icon' => 'n2-i-window-layer',
            'tip'  => n2_('Row')
        ));
        $rowSettings  = new N2Tab($_rowSettings, 'row-settings');
        new N2ElementColumns($rowSettings, 'row-columns', n2_('Columns'), '1');

        $rowSpacings = new N2ElementGroup($rowSettings, 'row-spacings');
        new N2ElementNumberSlider($rowSpacings, 'row-gutter', n2_('Gutter'), '', array(
            'min'           => 0,
            'max'           => 300,
            'sliderMax'     => 160,
            'unit'          => 'px',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            ),
            'style'         => 'width: 22px;'
        ));
        $padding = new N2ElementMarginPadding($rowSpacings, 'row-padding', n2_('Padding'), '10|*|10|*|10|*|10|*|px+', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($padding, 'row-padding-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'style'  => 'width: 22px;'
            ));
        }

        new N2ElementUnits($padding, 'row-padding-5', '', '', array(
            'units' => array(
                'px+' => 'px+',
                'px'  => 'px'
            )
        ));

        $rowSettingsOther = new N2ElementGroup($rowSettings, 'row-settings-other');
        new N2ElementNumberSlider($rowSettingsOther, 'row-wrap-after', n2_('Wrap after'), 0, array(
            'min'   => 0,
            'max'   => 10,
            'style' => 'width:22px;',
            'unit'  => n2_('Columns')
        ));
        new N2ElementOnOff($rowSettingsOther, 'row-fullwidth', n2_('Full width'), 1);
        new N2ElementOnOff($rowSettingsOther, 'row-stretch', n2_('Stretch'), 0);

        new N2ElementInnerAlign($rowSettings, 'row-inneralign', n2_('Inner align'), 'inherit', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        $link = new N2ElementGroup($rowSettings, 'row-link', '');
        new N2ElementUrl($link, 'row-href', n2_('Link'), '', array(
            'style' => 'width:150px;'
        ));
        new N2ElementLinkTarget($link, 'row-href-target', n2_('Target window'));

        $rowBackground = new N2ElementGroup($rowSettings, 'row-background');
        new N2ElementImage($rowBackground, 'row-background-image', n2_('Background image'), '');
        new N2ElementNumber($rowBackground, 'row-background-focus-x', n2_('Focus'), 50, array(
            'subLabel' => 'X',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementNumber($rowBackground, 'row-background-focus-y', ' ', 50, array(
            'subLabel' => 'Y',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementOnOff($rowBackground, 'row-background-parallax', n2_('Parallax'), 0);

        new N2ElementStyleMode($rowSettings, 'row-style-mode', n2_('Style'), '', array(
            'options' => array(
                ''       => 'Normal',
                '-hover' => 'HOVER'
            )
        ));

        $rowBackgroundColor = new N2ElementGroup($rowSettings, 'row-background-color');
        new N2ElementColor($rowBackgroundColor, 'row-background-color', n2_('Background color'), 'ffffff00', array(
            'alpha' => true
        ));

        new N2ElementList($rowBackgroundColor, 'row-background-gradient', n2_('Gradient'), 'off', array(
            'options'       => array(
                'off'        => n2_('Off'),
                'vertical'   => '&darr;',
                'horizontal' => '&rarr;',
                'diagonal1'  => '&#8599;',
                'diagonal2'  => '&#8600;'
            ),
            'relatedFields' => array(
                'layerrow-background-color-end'
            )
        ));

        new N2ElementColor($rowBackgroundColor, 'row-background-color-end', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        new N2ElementNumberAutocomplete($rowSettings, 'row-border-radius', n2_('Border radius'), 0, array(
            'values' => array(
                0,
                3,
                5,
                10,
                99
            ),
            'style'  => 'width: 22px;',
            'unit'   => 'px'
        ));

        $boxShadow = new N2ElementConnected($rowSettings, 'row-boxshadow', n2_('Box shadow'), '0|*|0|*|0|*|0|*|00000080');
        new N2ElementNumberAutocomplete($boxShadow, 'row-boxshadow-1', false, 0, array(
            'values' => array(
                0
            ),
            'style'  => 'width: 22px;'
        ));
        new N2ElementNumberAutocomplete($boxShadow, 'row-boxshadow-2', false, 0, array(
            'values' => array(
                0
            ),
            'style'  => 'width: 22px;'
        ));
        new N2ElementNumberAutocomplete($boxShadow, 'row-boxshadow-3', false, 0, array(
            'values' => array(
                0
            ),
            'style'  => 'width: 22px;'
        ));
        new N2ElementNumberAutocomplete($boxShadow, 'row-boxshadow-4', false, 0, array(
            'values' => array(
                0
            ),
            'style'  => 'width: 22px;'
        ));
        new N2ElementColor($boxShadow, 'row-boxshadow-5', false, '', array(
            'alpha' => true
        ));

        new N2ElementHidden($rowSettings, 'row-opened', false, 1);
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formColumn($form) {


        $_colSettings = new N2TabGrouppedSidebar($form, 'column', '', array(
            'icon' => 'n2-i-window-layer',
            'tip'  => n2_('Column')
        ));
        $colSettings  = new N2Tab($_colSettings, 'col-settings');

        new N2ElementHidden($colSettings, 'col-order', false, '0');

        $colAlign = new N2ElementGroup($colSettings, 'col-align');
        new N2ElementInnerAlign($colAlign, 'col-inneralign', n2_('Inner align'), 'inherit', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementVerticalAlign($colAlign, 'col-verticalalign', n2_('Vertical align'), 'flex-start');


        $colSpacings = new N2ElementGroup($colSettings, 'col-spacings');

        $padding = new N2ElementMarginPadding($colSpacings, 'col-padding', n2_('Padding'), '5|*|5|*|5|*|5|*|px+', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($padding, 'col-padding-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'style'  => 'width: 22px;'
            ));
        }

        new N2ElementUnits($padding, 'col-padding-5', '', '', array(
            'units' => array(
                'px+' => 'px+',
                'px'  => 'px'
            )
        ));

        new N2ElementNumber($colSpacings, 'col-maxwidth', n2_('Max width'), 0, array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            ),
            'style'         => 'width:32px;'
        ));

        $link = new N2ElementGroup($colSettings, 'col-link', '');
        new N2ElementUrl($link, 'col-href', n2_('Link'), '', array(
            'style' => 'width:150px;'
        ));
        new N2ElementLinkTarget($link, 'col-href-target', n2_('Target window'));

        $colBackground = new N2ElementGroup($colSettings, 'col-background');
        new N2ElementImage($colBackground, 'col-background-image', n2_('Background image'), '');
        new N2ElementNumber($colBackground, 'col-background-focus-x', n2_('Focus'), 50, array(
            'subLabel' => 'X',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementNumber($colBackground, 'col-background-focus-y', ' ', 50, array(
            'subLabel' => 'Y',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementOnOff($colBackground, 'col-background-parallax', n2_('Parallax'), 0);

        new N2ElementStyleMode($colSettings, 'col-style-mode', n2_('Style'), '', array(
            'options' => array(
                ''       => 'Normal',
                '-hover' => 'HOVER'
            )
        ));

        $colBackgroundColor = new N2ElementGroup($colSettings, 'col-background-color');
        new N2ElementColor($colBackgroundColor, 'col-background-color', n2_('Background color'), 'ffffff00', array(
            'alpha' => true
        ));

        new N2ElementList($colBackgroundColor, 'col-background-gradient', n2_('Gradient'), 'off', array(
            'options'       => array(
                'off'        => n2_('Off'),
                'vertical'   => '&darr;',
                'horizontal' => '&rarr;',
                'diagonal1'  => '&#8599;',
                'diagonal2'  => '&#8600;'
            ),
            'relatedFields' => array(
                'layercol-background-color-end'
            )
        ));

        new N2ElementColor($colBackgroundColor, 'col-background-color-end', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        $border = new N2ElementGroup($colSettings, 'col-border');


        $borderWidth = new N2ElementMarginPadding($border, 'col-border-width', n2_('Border'), '0|*|0|*|0|*|0', array(
            'unit' => 'px'
        ));

        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($borderWidth, 'col-border-width-' . $i, false, '', array(
                'values' => array(
                    0,
                    1,
                    2,
                    3,
                    5
                ),
                'style'  => 'width: 22px;'
            ));
        }

        new N2ElementList($border, 'col-border-style', n2_('Style'), 'none', array(
            'options' => array(
                'none'   => n2_('None'),
                'solid'  => n2_('Solid'),
                'dashed' => n2_('Dashed'),
                'dotted' => n2_('Dotted'),
            )
        ));

        new N2ElementColor($border, 'col-border-color', n2_('Color'), 'ffffffff', array(
            'alpha' => true
        ));


        new N2ElementNumberAutocomplete($border, 'col-border-radius', n2_('Border radius'), 0, array(
            'values' => array(
                0,
                3,
                5,
                10,
                99
            ),
            'style'  => 'width: 22px;',
            'unit'   => 'px'
        ));

        $boxShadow = new N2ElementConnected($colSettings, 'col-boxshadow', n2_('Box shadow'), '0|*|0|*|0|*|0|*|00000080');
        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($boxShadow, 'col-boxshadow-' . $i, false, 0, array(
                'values' => array(
                    0
                ),
                'style'  => 'width: 22px;'
            ));
        }
        new N2ElementColor($boxShadow, 'col-boxshadow-5', false, '', array(
            'alpha' => true
        ));

        new N2ElementHidden($colSettings, 'col-opened', false, 1);
        new N2ElementHidden($colSettings, 'col-colwidth', false, '');
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formContent($form) {


        $_contentSettings = new N2TabGrouppedSidebar($form, 'content', '', array(
            'icon' => 'n2-i-window-layer',
            'tip'  => n2_('Content')
        ));
        $contentSettings  = new N2Tab($_contentSettings, 'content-settings');

        $contentAlign = new N2ElementGroup($contentSettings, 'content-align');
        new N2ElementInnerAlign($contentAlign, 'content-inneralign', n2_('Inner align'), 'inherit', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementVerticalAlign($contentAlign, 'content-verticalalign', n2_('Vertical align'), 'center');

        $padding = new N2ElementMarginPadding($contentSettings, 'content-padding', n2_('Padding'), '5|*|5|*|5|*|5|*|px+', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($padding, 'content-padding-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'style'  => 'width: 22px;'
            ));
        }

        new N2ElementUnits($padding, 'content-padding-5', '', '', array(
            'units' => array(
                'px+' => 'px+',
                'px'  => 'px'
            )
        ));


        $contentSpacings = new N2ElementGroup($contentSettings, 'content-spacings');

        new N2ElementNumber($contentSpacings, 'content-maxwidth', n2_('Max width'), 0, array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            ),
            'style'         => 'width:32px;'
        ));
        new N2ElementHAlign($contentSpacings, 'content-selfalign', n2_('Position'), 'inherit', array(
            'inherit'       => true,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));


        $contentBackground = new N2ElementGroup($contentSettings, 'content-background');
        new N2ElementImage($contentBackground, 'content-background-image', n2_('Background image'), '');
        new N2ElementNumber($contentBackground, 'content-background-focus-x', n2_('Focus'), 50, array(
            'subLabel' => 'X',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementNumber($contentBackground, 'content-background-focus-y', ' ', 50, array(
            'subLabel' => 'Y',
            'min'      => 0,
            'max'      => 100,
            'unit'     => '%',
            'style'    => 'width:22px;'
        ));
        new N2ElementOnOff($contentBackground, 'content-background-parallax', n2_('Parallax'), 0);

        new N2ElementStyleMode($contentSettings, 'content-style-mode', n2_('Style'), '', array(
            'options' => array(
                ''       => 'Normal',
                '-hover' => 'HOVER'
            )
        ));

        $contentBackgroundColor = new N2ElementGroup($contentSettings, 'content-background-color');
        new N2ElementColor($contentBackgroundColor, 'content-background-color', n2_('Background color'), 'ffffff00', array(
            'alpha' => true
        ));

        new N2ElementList($contentBackgroundColor, 'content-background-gradient', n2_('Gradient'), 'off', array(
            'options'       => array(
                'off'        => n2_('Off'),
                'vertical'   => '&darr;',
                'horizontal' => '&rarr;',
                'diagonal1'  => '&#8599;',
                'diagonal2'  => '&#8600;'
            ),
            'relatedFields' => array(
                'layercontent-background-color-end'
            )
        ));

        new N2ElementColor($contentBackgroundColor, 'content-background-color-end', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        new N2ElementHidden($contentSettings, 'content-opened', false, 1);
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formDesign($form) {
        $design = new N2TabGrouppedSidebar($form, 'style', '', array(
            'icon' => 'n2-i-window-design',
            'tip'  => n2_('Design')
        ));

        $css = new N2TabBasicCSS($design, 'slide-editor-design-css');

        $font = new N2TabBasicCSSFont($css, 'basiccssfont', n2_('Font'));

        new N2ElementFamily($font, 'family', n2_('Family'), 'Arial, Helvetica', array(
            'style' => 'width:150px;'
        ));
        new N2ElementColor($font, 'color', n2_('Color'), '000000FF', array(
            'alpha' => true
        ));
        $size = new N2ElementConnected($font, 'size', n2_('Size'), '14|*|px');
        new N2ElementNumberSlider($size, 'size-1', false, '', array(
            'min'       => 1,
            'max'       => 10000,
            'sliderMax' => 100,
            'units'     => array(
                'pxMin'       => 1,
                'pxMax'       => 10000,
                'pxSliderMax' => 100,
                '%Min'        => 1,
                '%Max'        => 10000,
                '%SliderMax'  => 600
            ),
            'style'     => 'width: 22px;'
        ));
        new N2ElementUnits($size, 'size-2', false, '', array(
            'units' => array(
                'px' => 'px',
                '%'  => '%'
            )
        ));

        new N2ElementList($font, 'weight', n2_('Font weight'), '', array(
            'options' => array(
                '0'   => n2_('Normal'),
                '1'   => n2_('Bold'),
                '100' => '100',
                '200' => '200 - ' . n2_('Extra light'),
                '300' => '300 - ' . n2_('Light'),
                '400' => '400 - ' . n2_('Normal'),
                '500' => '500',
                '600' => '600 - ' . n2_('Semi bold'),
                '700' => '700 - ' . n2_('Bold'),
                '800' => '800 - ' . n2_('Extra bold'),
                '900' => '900'
            )
        ));

        new N2ElementTextAutocomplete($font, 'lineheight', n2_('Line height'), '18px', array(
            'values' => array(
                'normal',
                '1',
                '1.2',
                '1.5',
                '1.8',
                '2'
            ),
            'style'  => 'width:70px;'
        ));

        new N2ElementTextAlign($font, 'textalign', n2_('Text align'), 'inherit');
        new N2ElementDecoration($font, 'decoration', n2_('Decoration'));

        $style = new N2TabBasicCSSStyle($css, 'basiccssstyle', n2_('Style'));

        new N2ElementColor($style, 'backgroundcolor', n2_('Background color'), '000000FF', array(
            'alpha' => true
        ));

        $paddingConnected = new N2ElementConnected($style, 'padding', n2_('Padding'), '0|*|0|*|0|*|0|*|px');
        new N2ElementNumberAutocomplete($paddingConnected, 'padding-1', '', '', array(
            'style'  => 'width: 22px;',
            'values' => array(
                0,
                5,
                10,
                20,
                30
            )
        ));
        new N2ElementNumberAutocomplete($paddingConnected, 'padding-2', '', '', array(
            'style'  => 'width: 22px;',
            'values' => array(
                0,
                5,
                10,
                20,
                30
            )
        ));
        new N2ElementNumberAutocomplete($paddingConnected, 'padding-3', '', '', array(
            'style'  => 'width: 22px;',
            'values' => array(
                0,
                5,
                10,
                20,
                30
            )
        ));
        new N2ElementNumberAutocomplete($paddingConnected, 'padding-4', '', '', array(
            'style'  => 'width: 22px;',
            'values' => array(
                0,
                5,
                10,
                20,
                30
            )
        ));
        new N2ElementUnits($paddingConnected, 'padding-5', '', '', array(
            'units' => array(
                'px',
                'em',
                '%'
            )
        ));

        $border = new N2ElementMixed($style, 'border', n2_('Border'), '0|*|solid|*|000000ff');
        new N2ElementNumber($border, 'border-1', false, '', array(
            'style' => 'width:32px;',
            'unit'  => 'px'
        ));
        new N2ElementList($border, 'border-2', false, '', array(
            'options' => array(
                'none'   => n2_('None'),
                'dotted' => n2_('Dotted'),
                'dashed' => n2_('Dashed'),
                'solid'  => n2_('Solid'),
                'double' => n2_('Double'),
                'groove' => n2_('Groove'),
                'ridge'  => n2_('Ridge'),
                'inset'  => n2_('Inset'),
                'outset' => n2_('Outset')
            )
        ));
        new N2ElementColor($border, 'border-3', false, '', array(
            'alpha' => true
        ));

        new N2ElementNumberAutocomplete($style, 'opacity', n2_('Opacity'), '100', array(
            'values' => array(
                0,
                50,
                90,
                100
            ),
            'unit'   => '%',
            'style'  => 'width: 22px;'
        ));

        new N2ElementNumberAutocomplete($style, 'borderradius', n2_('Border radius'), '0', array(
            'values' => array(
                0,
                3,
                5,
                10,
                99
            ),
            'unit'   => 'px',
            'style'  => 'width: 22px;'
        ));
    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formAnimations($form) {

        $group      = new N2TabGrouppedSidebar($form, 'animations', '', array(
            'icon' => 'n2-i-window-animation',
            'tip'  => n2_('Animation')
        ));
        $animations = new N2TabTabbedSidebar($group, 'animations-tabbed', '', array(
            'classes'    => 'n2-sidebar-tab-switcher n2-tab-bordered n2-sidebar-tab-bg',
            'active'     => 1,
            'underlined' => 1
        ));

        new N2TabGrouppedSidebar($animations, 'animations-in', n2_('In'));
        new N2TabGrouppedSidebar($animations, 'animations-loop', n2_('Loop'));
        new N2TabGrouppedSidebar($animations, 'animations-out', n2_('Out'));

        $__events = new N2TabGrouppedSidebar($animations, 'animations-events', n2_('Events'));
        $_events  = new N2Tab($__events, 'animations-events');

        $events = new N2ElementGroup($_events, 'animations-events-events');

        $eventNames = array(
            'layerAnimationPlayIn',
            'LayerClick',
            'LayerMouseEnter',
            'LayerMouseLeave',
            'SlideClick',
            'SlideMouseEnter',
            'SlideMouseLeave',
            'SliderClick',
            'SliderMouseEnter',
            'SliderMouseLeave'
        );

        new N2ElementAutocomplete($events, 'in-play-event', n2_('Plays in when'), '', array(
            'options' => $eventNames,
            'style'   => 'width:260px;'
        ));

        new N2ElementAutocomplete($events, 'out-play-event', n2_('Plays out when'), '', array(
            'options'  => array_merge($eventNames, array('InstantOut')),
            'style'    => 'width:260px;',
            'rowClass' => 'n2-expert'
        ));

        new N2ElementAutocomplete($events, 'loop-play-event', n2_('Plays loop when'), '', array(
            'options'  => $eventNames,
            'style'    => 'width:260px;',
            'rowClass' => 'n2-expert'
        ));

        new N2ElementAutocomplete($events, 'loop-pause-event', n2_('Pauses loop when'), '', array(
            'options'  => $eventNames,
            'style'    => 'width:260px;',
            'rowClass' => 'n2-expert'
        ));

        new N2ElementAutocomplete($events, 'loop-stop-event', n2_('Stops loop when'), '', array(
            'options'  => $eventNames,
            'style'    => 'width:260px;',
            'rowClass' => 'n2-expert'
        ));

        new N2ElementOnOff($events, 'repeatable', n2_('Repeatable'), 0, array(
            'relatedFields' => array(
                'layerstart-delay',
                'layerend-delay'
            )
        ));

        new N2ElementNumberAutocomplete($events, 'start-delay', n2_('Start delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));

        new N2ElementNumberAutocomplete($events, 'end-delay', n2_('End delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new N2ElementOnOff($events, 'loop-repeat-self-only', n2_('Repeat loop only'), 0, array(
            'rowClass' => 'n2-expert'
        ));

        $triggers = new N2ElementEditorGroup($_events, 'animations-events-triggers', n2_('Trigger custom event on'));

        new N2ElementText($triggers, 'onclick', n2_('Click'), '', array(
            'style' => 'width:73px;'
        ));
        new N2ElementText($triggers, 'onmouseenter', n2_('Mouse enter'), '', array(
            'style' => 'width:73px;'
        ));
        new N2ElementText($triggers, 'onmouseleave', n2_('Mouse leave'), '', array(
            'style' => 'width:73px;'
        ));
        new N2ElementText($triggers, 'onplay', n2_('Media started'), '', array(
            'style' => 'width:73px;'
        ));
        new N2ElementText($triggers, 'onpause', n2_('Media paused'), '', array(
            'style' => 'width:73px;'
        ));
        new N2ElementText($triggers, 'onstop', n2_('Media stopped'), '', array(
            'style' => 'width:73px;'
        ));

        $this->formAnimationsBasic($group);
        $this->formAnimationsReveal($group);

    
    }

    protected function formAnimationsBasic($form) {

        $basicForm = new N2Tab($form, 'layer-animation-basic-form', false);
        $timing    = new N2ElementGroup($basicForm, 'animation-timing');
        new N2ElementNumberAutocomplete($timing, '-anim-duration', n2_('Duration'), 500, array(
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new N2ElementNumberAutocomplete($timing, '-anim-delay', n2_('Delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new N2ElementEasing($timing, '-anim-ease', n2_('Easing'), 'easeOutCubic');


        $effect = new N2ElementGroup($basicForm, 'animation-effect');
        new N2ElementNumberAutocomplete($effect, '-anim-opacity', n2_('Opacity'), 100, array(
            'wide'   => 3,
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                50,
                100
            ),
            'unit'   => '%'
        ));
        new N2ElementNumberSlider($effect, '-anim-n2blur', n2_('Blur'), 0, array(
            'wide' => 3,
            'min'  => 0,
            'max'  => 100,
            'unit' => 'px'
        ));

        $offset = new N2ElementGroup($basicForm, 'animation-offset', n2_('Offset'));
        new N2ElementNumberAutocomplete($offset, '-anim-x', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                -800,
                -400,
                -200,
                -100,
                -50,
                0,
                50,
                100,
                200,
                400,
                800
            ),
            'unit'     => 'px'
        ));
        new N2ElementNumberAutocomplete($offset, '-anim-y', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                -800,
                -400,
                -200,
                -100,
                -50,
                0,
                50,
                100,
                200,
                400,
                800
            ),
            'unit'     => 'px'
        ));
        new N2ElementNumber($offset, '-anim-z', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'unit'     => 'px'
        ));


        $rotate = new N2ElementGroup($basicForm, 'animation-rotate', n2_('Rotate'));
        new N2ElementNumberAutocomplete($rotate, '-anim-rotationX', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));
        new N2ElementNumberAutocomplete($rotate, '-anim-rotationY', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));
        new N2ElementNumberAutocomplete($rotate, '-anim-rotationZ', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        $scaleSkew = new N2ElementGroup($basicForm, 'animation-scale-skew');
        $scale     = new N2ElementGroup($scaleSkew, 'animation-scale', n2_('Scale'));
        new N2ElementNumberAutocomplete($scale, '-anim-scaleX', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'min'      => 0,
            'values'   => array(
                0,
                50,
                100,
                150
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($scale, '-anim-scaleY', '', 0, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'min'      => 0,
            'values'   => array(
                0,
                50,
                100,
                150
            ),
            'unit'     => '%'
        ));


        new N2ElementNumber($scaleSkew, '-anim-skew', n2_('Skew'), 0, array(
            'wide' => 4,
            'unit' => '%'
        ));

        $layerAnimationBasicFormIn = new N2Tab($form, 'layer-animation-basic-form-in', false, array(
            'class' => 'n2-tab-layer-animation-basic-form-settings-global'
        ));

        new N2ElementOnOff($layerAnimationBasicFormIn, 'basic-in-special-zero', n2_('Special zero'), 0);

        $transformOrigin = new N2ElementMixed($layerAnimationBasicFormIn, 'basic-in-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new N2ElementNumberAutocomplete($transformOrigin, 'in-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'in-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'in-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'values'   => array(
                0,
            ),
            'unit'     => 'px',
            'rowClass' => 'n2-expert'
        ));

        $layerAnimationBasicFormLoop = new N2Tab($form, 'layer-animation-basic-form-loop', false, array(
            'class' => 'n2-tab-layer-animation-basic-form-settings-global'
        ));

        $repeat = new N2ElementGroup($layerAnimationBasicFormLoop, 'basic-loop-repeat');
        new N2ElementNumber($repeat, 'basic-loop-repeat-count', n2_('Repeat Count'), 0, array(
            'wide' => 3,
            'unit' => n2_('loop')
        ));
        new N2ElementNumber($repeat, 'basic-loop-repeat-start-delay', n2_('Start delay'), 0, array(
            'wide' => 5,
            'unit' => 'ms'
        ));

        $transformOrigin = new N2ElementMixed($layerAnimationBasicFormLoop, 'basic-loop-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new N2ElementNumberAutocomplete($transformOrigin, 'loop-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'loop-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'loop-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'values'   => array(
                0,
            ),
            'unit'     => 'px',
            'rowClass' => 'n2-expert'
        ));

        $layerAnimationBasicFormOut = new N2Tab($form, 'layer-animation-basic-form-out', false, array(
            'class' => 'n2-tab-layer-animation-basic-form-settings-global'
        ));

        $transformOrigin = new N2ElementMixed($layerAnimationBasicFormOut, 'basic-out-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new N2ElementNumberAutocomplete($transformOrigin, 'out-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'out-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new N2ElementNumberAutocomplete($transformOrigin, 'out-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'values'   => array(
                0,
            ),
            'unit'     => 'px',
            'rowClass' => 'n2-expert'
        ));
    
    }

    protected function formAnimationsReveal($form) {
        $revealForm = new N2Tab($form, 'layer-animation-reveal-form', false);

        $revealDetails = new N2ElementGroup($revealForm, '-reveal-details-container');
        new N2ElementColor($revealDetails, '-reveal-color', n2_('Color'), 'ffffff');
        new N2ElementNumberAutocomplete($revealDetails, '-reveal-duration', n2_('Duration'), 500, array(
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new N2ElementNumberAutocomplete($revealDetails, '-reveal-delay', n2_('Delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));

        $revealDirection = new N2ElementGroup($revealForm, '-reveal-direction-container');

        $options = array(
            'no'                   => n2_('No'),
            'top'                  => n2_('Slide') . ' - ' . n2_('Top'),
            'right'                => n2_('Slide') . ' - ' . n2_('Right'),
            'bottom'               => n2_('Slide') . ' - ' . n2_('Bottom'),
            'left'                 => n2_('Slide') . ' - ' . n2_('Left'),
            'skew-top'             => n2_('Skew') . ' - ' . n2_('Top'),
            'skew-right'           => n2_('Skew') . ' - ' . n2_('Right'),
            'skew-bottom'          => n2_('Skew') . ' - ' . n2_('Bottom'),
            'skew-left'            => n2_('Skew') . ' - ' . n2_('Left'),
            'curtains-horizontal'  => n2_('Curtains') . ' - ' . n2_('Horizontal'),
            'curtains-vertical'    => n2_('Curtains') . ' - ' . n2_('Vertical'),
            'curtains-diagonal-1'  => n2_('Curtains') . ' - ' . n2_('Diagonal') . ' 1',
            'curtains-diagonal-2'  => n2_('Curtains') . ' - ' . n2_('Diagonal') . ' 2',
            'rotate-top-left'      => n2_('Rotate') . ' - ' . n2_('Top Left') . ' 1',
            'rotate-top-left-'     => n2_('Rotate') . ' - ' . n2_('Top Left') . ' 2',
            'rotate-top-right'     => n2_('Rotate') . ' - ' . n2_('Top Right') . ' 1',
            'rotate-top-right-'    => n2_('Rotate') . ' - ' . n2_('Top Right') . ' 2',
            'rotate-bottom-right'  => n2_('Rotate') . ' - ' . n2_('Bottom Right') . ' 1',
            'rotate-bottom-right-' => n2_('Rotate') . ' - ' . n2_('Bottom Right') . ' 2',
            'rotate-bottom-left'   => n2_('Rotate') . ' - ' . n2_('Bottom Left') . ' 1',
            'rotate-bottom-left-'  => n2_('Rotate') . ' - ' . n2_('Bottom Left') . ' 2',
            'circle-top'           => n2_('Circle') . ' - ' . n2_('Top'),
            'circle-right'         => n2_('Circle') . ' - ' . n2_('Right'),
            'circle-bottom'        => n2_('Circle') . ' - ' . n2_('Bottom'),
            'circle-left'          => n2_('Circle') . ' - ' . n2_('Left'),
        );

        new N2ElementList($revealDirection, '-reveal-from', n2_('From'), 'top', array(
            'options' => $options
        ));
        new N2ElementEasing($revealDirection, '-reveal-from-ease', n2_('Easing'), 'easeOutCubic');

        unset($options['no']);
        new N2ElementList($revealDirection, '-reveal-to', n2_('To'), 'bottom', array(
            'options' => $options
        ));
        new N2ElementEasing($revealDirection, '-reveal-to-ease', n2_('Easing'), 'easeOutCubic');

        new N2ElementList($revealDirection, '-reveal-content', n2_('Content'), '', array(
            'options' => array(
                ''           => n2_('Default'),
                'fade'       => n2_('Fade'),
                'scale-up'   => n2_('Scale Up'),
                'scale-down' => n2_('Scale Down'),
                'top'        => n2_('Top'),
                'right'      => n2_('Right'),
                'bottom'     => n2_('Bottom'),
                'left'       => n2_('Left')
            )
        ));

    
    }


    /**
     * @param N2FormElementContainer $form
     */
    protected function formProperties($form) {


        $_layerProperties = new N2TabGrouppedSidebar($form, 'position', '', array(
            'icon' => 'n2-i-window-settings',
            'tip'  => n2_('Settings')
        ));
        $settings         = new N2Tab($_layerProperties, 'layer-properties');

        new N2ElementText($settings, 'generator-visible', n2_('Hide layer if provided variable is empty'), '', array(
            'rowClass' => 'n2-ss-generator-param',
            'style'    => 'width:270px;'
        ));

        $alignment = new N2ElementGroup($settings, 'layer-alignment', '', array(
            'rowAttributes' => array(
                'data-placement' => 'normal'
            )
        ));
        new N2ElementNumber($alignment, 'normal-maxwidth', n2_('Max width'), 0, array(
            'style'         => 'width:32px;',
            'unit'          => 'px',
            'min'           => 0,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new N2ElementHAlign($alignment, 'normal-selfalign', n2_('Position'), 'inherit', array(
            'inherit'       => true,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        $other = new N2ElementGroup($settings, 'layer-other');
        new N2ElementList($other, 'parallax', n2_('Parallax depth'), 0, array(
            'tip'     => n2_('More parallax options in slider settings -> Layer animations tab.'),
            'options' => array(
                '0'  => n2_('Off'),
                '1'  => 1,
                '2'  => 2,
                '3'  => 3,
                '4'  => 4,
                '5'  => 5,
                '6'  => 6,
                '7'  => 7,
                '8'  => 8,
                '9'  => 9,
                '10' => 10
            )
        ));
    

        new N2ElementList($other, 'crop', n2_('Crop'), 'visible', array(
            'options' => array(
                'visible' => n2_('Off'),
                'hidden'  => n2_('On'),
                'auto'    => n2_('Scroll'),
                'mask'    => n2_('Mask')
            )
        ));

        new N2ElementNumber($other, 'rotation', n2_('Rotation'), 0, array(
            'style' => 'width:32px',
            'unit'  => '°'
        ));

        $normalGroup = new N2ElementGroup($settings, 'layer-normal-group', '', array(
            'rowAttributes' => array(
                'data-placement' => 'normal'
            )
        ));

        $margin = new N2ElementMarginPadding($normalGroup, 'normal-margin', n2_('Margin'), '0|*|0|*|0|*|0|*|px+', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($margin, 'normal-margin-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'style'  => 'width: 22px;'
            ));
        }

        new N2ElementUnits($margin, 'normal-margin-5', '', '', array(
            'units' => array(
                'px+' => 'px+',
                'px'  => 'px'
            )
        ));

        new N2ElementNumber($normalGroup, 'normal-height', n2_('Height'), 0, array(
            'style'         => 'width:32px;',
            'unit'          => 'px+',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        $align = new N2ElementGroup($settings, 'layer-align', '', array(
            'rowAttributes' => array(
                'data-placement' => 'absolute'
            )
        ));

        new N2ElementHAlign($align, 'align', n2_('Align'), 'left', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new N2ElementVAlign($align, 'valign', n2_('Vertical align'), 'top', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));


        $font = new N2ElementGroup($settings, 'layer-font');
        new N2ElementOnOff($font, 'adaptive-font', n2_('Adaptive sizing'), 0);
        new N2ElementNumberAutocomplete($font, 'font-size', n2_('Font size modifier'), 100, array(
            'values'        => array(
                60,
                80,
                100,
                120,
                140,
                160,
                180
            ),
            'unit'          => '%',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        $position = new N2ElementGroup($settings, 'layer-position', '', array(
            'rowAttributes' => array(
                'data-placement' => 'absolute'
            )
        ));
        new N2ElementNumber($position, 'left', n2_('Position'), '', array(
            'sublabel'      => 'X',
            'unit'          => 'px',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementNumber($position, 'top', n2_('Position'), '', array(
            'sublabel'      => 'Y',
            'unit'          => 'px',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementOnOff($position, 'responsive-position', n2_('Responsive'), 1);


        $size = new N2ElementGroup($settings, 'layer-size', '', array(
            'rowAttributes' => array(
                'data-placement' => 'absolute'
            )
        ));
        new N2ElementText($size, 'width', n2_('Width'), '', array(
            'unit'          => 'px',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementText($size, 'height', n2_('Height'), '', array(
            'unit'          => 'px',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementOnOff($size, 'responsive-size', n2_('Responsive'), 1);

        new N2ElementDevices($settings, 'show', n2_('Show on'));
        $parentPicker = new N2ElementGroup($settings, 'layer-parent-picker', '', array(
            'rowClass'      => 'n2-ss-parent-picker n2-expert',
            'rowAttributes' => array(
                'data-placement' => 'absolute'
            )
        ));
        new N2ElementLayerPicker($parentPicker, 'parentid', n2_('Parent'), '', array(
            'rowClass' => 'n2-layer-picker-container'
        ));
        new N2ElementHAlign($parentPicker, 'parentalign', n2_('Horizontal'), 'left', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new N2ElementVAlign($parentPicker, 'parentvalign', n2_('Vertical'), 'top', array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
    

        new N2ElementText($settings, 'class', n2_('CSS class'), '');

        new N2ElementButton($settings, 'resettodesktop', n2_('Reset position'), n2_('Reset'));

        new N2ElementHidden($settings, 'id', false, '', array(
            'rowClass' => 'n2-hidden'
        ));

        new N2ElementDisabled($settings, 'uniqueclass', false, '', array(
            'rowClass' => 'n2-hidden'
        ));

    }

    /**
     * @param N2FormElementContainer $form
     */
    protected function formGroup($form) {

        $group    = new N2TabGrouppedSidebar($form, 'layer-group', '', array(
            'icon' => 'n2-i-window-layer',
            'tip'  => n2_('Group')
        ));
        $settings = new N2Tab($group, 'layer-group');


        $other = new N2ElementGroup($settings, 'layer-other');
        new N2ElementList($other, 'group-parallax', n2_('Parallax depth'), 0, array(
            'tip'     => n2_('More parallax options in slider settings -> Layer animations tab.'),
            'options' => array(
                '0'  => n2_('Off'),
                '1'  => 1,
                '2'  => 2,
                '3'  => 3,
                '4'  => 4,
                '5'  => 5,
                '6'  => 6,
                '7'  => 7,
                '8'  => 8,
                '9'  => 9,
                '10' => 10
            )
        ));
        new N2ElementOnOff($other, 'group-adaptive-font', n2_('Adaptive sizing'), 0);
        new N2ElementNumberAutocomplete($other, 'group-font-size', n2_('Font size modifier'), 100, array(
            'values'        => array(
                60,
                80,
                100,
                120,
                140,
                160,
                180
            ),
            'unit'          => '%',
            'style'         => 'width:32px;',
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new N2ElementText($settings, 'group-generator-visible', n2_('Hide layer if provided variable is empty'), '', array(
            'rowClass' => 'n2-ss-generator-param',
            'style'    => 'width:270px;'
        ));

        new N2ElementDevices($settings, 'group-show', n2_('Show on'));

    
    }
} 