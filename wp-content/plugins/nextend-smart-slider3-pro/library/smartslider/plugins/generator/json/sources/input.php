<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorJsonInput extends N2GeneratorAbstract {

    protected $layout = 'image';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementTextarea($filter, 'source', 'JSON', '', array(
            'fieldStyle' => 'width:300px;height: 200px;'
        ));

        new N2ElementList($filter, 'json_level', 'Level separation', 2, array(
            'tip'     => 'JSON codes can be customized to have many different levels. From a code it is impossible to know from which level do you want to use the given datas on the different slides, so you have to select that level from this list.',
            'options' => array(
                1 => 'first level',
                2 => 'second level',
                3 => 'third level'
            )
        ));
    }

    protected function flatten_array($array, $parent = '', $basekey = '') {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        if (!empty($basekey)) {
            $result['base_name'] = $basekey;
        }
        foreach ($array as $key => $value) {
            $original_key = $key;
            if (!empty($parent)) {
                $key = $parent . '_' . $key;
            }
            $result[$key . '_name'] = $original_key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten_array($value, $key));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    protected function _getData($count, $startIndex) {
        $source = $this->data->get('source', '');
        $data   = array();

        $json = json_decode($source, true);
        if (!is_array($json)) {
            N2Message::error(n2_('The given text is not valid JSON! <a href="https://jsonlint.com/" target="_blank">Validate your code</a> to make sure it is correct.'));
        } else {
            switch ($this->data->get('json_level', 2)) {
                case 1:
                    $data[] = $this->flatten_array($json);
                    break;
                case 2:
                    foreach ($json AS $key => $json_row) {
                        if (is_array($json_row)) {
                            $data[] = $this->flatten_array($json_row, '', $key);
                        }
                    }
                    break;
                case 3:
                    $array_values = array_values($json);
                    if (is_array($array_values)) {
                        $array_shift = array_shift($array_values);
                        if (is_array($array_shift) && !empty($array_shift)) {
                            foreach ($array_shift AS $key => $json_row) {
                                if (is_array($json_row)) {
                                    $data[] = $this->flatten_array($json_row, '', $key);
                                }
                            }
                        }
                    }
                    break;

            }
            if (empty($data)) {
                N2Message::error(n2_('Try to change the "Level separation" setting.'));
            }
        }

        return $data;
    }
}