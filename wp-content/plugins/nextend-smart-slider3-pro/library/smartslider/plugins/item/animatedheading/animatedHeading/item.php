<?php

N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemAnimatedHeading extends N2SSItemAbstract {

    protected $type = 'animatedHeading';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $heading = array();

        $beforeText = $owner->fill($this->data->get('before-text', ''));
        if (!empty($beforeText)) {
            $heading[] = N2Html::tag('div', array(
                'class' => 'n2-ss-animated-heading-before'
            ), $beforeText);
        }

        $animatedText = preg_split('/\r\n|\r|\n/', $owner->fill($this->data->get('animated-text', '')));
        if (!empty($animatedText)) {


            $attributes = array(
                'class' => 'n2-highlighted n2-ss-animated-heading-i'
            );
            $type       = $this->data->get('type', 'slide');
            if ($type != 'slide') {
                $attributes['data-animation-type'] = $type;
            }

            $color = $this->data->get('color', 'ffffffff');
            if ($color != 'ffffffff') {
                $attributes['data-color'] = N2Color::colorToRGBA($color);
            }

            $color2 = $this->data->get('color2', '000000');
            if ($color != '000000') {
                $attributes['data-color2'] = $color2;
            }

            $animateWidth = $this->data->get('animate-width', '1');
            if ($animateWidth != 1) {
                $attributes['data-animate-width'] = $animateWidth;
            }


            $delay = $this->data->get('delay', 0);
            if ($delay > 0) {
                $attributes['data-delay'] = $delay;
            }

            $showDuration = max(0, $this->data->get('show-duration', 1500));
            if ($showDuration != 1500) {
                $attributes['data-show-duration'] = $showDuration;
            }

            $speed = max(0, $this->data->get('speed', 100));
            if ($speed != 100) {
                $attributes['data-speed'] = $speed;
            }

            if ($this->data->get('loop', 0)) {
                $attributes['data-loop'] = 1;
            }

            $animatedInner = '';

            foreach ($animatedText AS $text) {
                $animatedInner .= N2Html::tag('div', array(
                    'class' => 'n2-ss-animated-heading-i-text'
                ), $text);
            }
            $animatedInner = N2Html::tag('div', array(
                'class' => 'n2-ss-animated-heading-i2'
            ), $animatedInner);

            $href = $this->data->get('href', '');
            if (!empty($href) && $href != '#') {
                $heading[] = $this->getLink($animatedInner, $attributes);
            } else {
                $heading[] = N2Html::tag('div', $attributes, $animatedInner);
            }
        }

        $afterText = $owner->fill($this->data->get('after-text', ''));
        if (!empty($afterText)) {
            $heading[] = N2Html::tag('div', array(
                'class' => 'n2-ss-animated-heading-after'
            ), $afterText);
        }


        $font  = $owner->addFont($this->data->get('font'), 'highlight', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');
        $style = $owner->addStyle($this->data->get('style'), 'highlight');

        return $this->heading($this->data->get('priority', 'div'), array(
            "id"    => $this->id,
            "class" => 'n2-ss-animated-heading-wrapper ' . $font . ' ' . $style . ' n2-ss-item-content n2-ow-all'
        ), implode('', $heading));
    }

    private function heading($type, $attributes, $content) {
        if ($type > 0) {
            return N2Html::tag("h{$type}", $attributes, $content);
        }

        return N2Html::tag("div", $attributes, $content);
    }

    /**
     * @param N2SmartSliderComponentOwnerAbstract $owner
     */
    public function loadResources($owner) {
        $owner->addLess(dirname(__FILE__) . "/animatedHeading.n2less", array(
            "sliderid" => $owner->getElementID()
        ));

        if (!$owner->isScriptAdded('animated-heading')) {
            if ($this->isEditor) {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-currently-edited-slide .n2-ss-animated-heading-i\').each($.proxy(function(i, el){new N2Classes.AnimatedHeadingItemAdmin(el, this)}, this));', 'animated-heading');
            } else {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-animated-heading-i\').each($.proxy(function(i, el){new N2Classes.FrontendItemAnimatedHeading(el, this)}, this));', 'animated-heading');
            }
        }
    }
}