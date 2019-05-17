<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemArea extends N2SSItemAbstract {

    protected $type = 'area';

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
        $style = '';

        $color    = $this->data->get('color');
        $gradient = $this->data->get('gradient', 'off');

        if ($gradient != 'off') {
            $colorEnd = $this->data->get('color2');
            switch ($gradient) {
                case 'horizontal':
                    $style .= 'background:linear-gradient(to right, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'vertical':
                    $style .= 'background:linear-gradient(to bottom, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal1':
                    $style .= 'background:linear-gradient(45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal2':
                    $style .= 'background:linear-gradient(135deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
            }
        } else {
            if (strlen($color) == 8 && substr($color, 6, 2) != '00') {
                $style = 'background-color: #' . substr($color, 0, 6) . ';';
                $style .= "background-color: " . N2Color::colorToRGBA($color) . ";";
            }
        }

        $height = '100%';

        $_width = $this->data->get('width');
        if (!empty($_width)) {
            $width = $_width . 'px';
            $style .= 'width:' . $_width . 'px;';
        }

        $_height = $this->data->get('height');
        if (!empty($_height)) {
            $height = $_height . 'px';
        }

        $style .= 'height:' . $height . ';';

        $borderWidth = max(0, intval($this->data->get('borderWidth')));
        if ($borderWidth > 0) {
            list($borderHex, $borderRgba) = N2Color::colorToCss($this->data->get('borderColor'));
            $style .= 'border:' . $borderWidth . 'px solid #' . $borderHex . ';border:' . $borderWidth . 'px solid ' . $borderRgba . ';box-sizing: border-box;';
        }
        $borderRadius = max(0, intval($this->data->get('borderRadius')));
        if ($borderRadius > 0) {
            $style .= 'border-radius:' . $borderRadius . 'px;';
        }

        return N2Html::tag('div', array(
            'class' => ($isContent ? 'n2-ss-item-content ' : '') . 'n2-ow',
            'style' => $style . $this->data->get('css')
        ));
    }

    public function needSize() {
        return true;
    }
}
