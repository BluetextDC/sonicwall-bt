<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemImageArea extends N2SSItemAbstract {

    protected $type = 'imagearea';

    public function render() {

        if ($this->hasLink()) {
            return $this->getLink($this->getHtml(false), array(
                'style' => 'display: block; width:100%;height:100%;',
                'class' => 'n2-ss-item-content n2-ow'
            ));
        }

        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml($isContent = true) {
        $owner = $this->layer->getOwner();

        $fixedImageUrl = N2ImageHelper::fixed($owner->fill($this->data->get('image', '')));

        $owner->addImage($fixedImageUrl);

        return N2Html::tag('span', array(
            'class' => ($isContent ? 'n2-ss-item-content ' : '') . 'n2-ow',
            'style' => 'display:inline-block;vertical-align:top;width:100%;height:100%;background: URL(' . $fixedImageUrl . ') no-repeat;background-size:' . $this->data->get('fillmode', 'cover') . ';background-position: ' . $this->data->get('positionx', 50) . '% ' . $this->data->get('positiony', 50) . '%;'
        ));
    }

    public function needSize() {
        return true;
    }
}
