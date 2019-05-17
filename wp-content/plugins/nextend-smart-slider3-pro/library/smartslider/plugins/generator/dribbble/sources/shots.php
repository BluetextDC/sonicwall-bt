<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorDribbbleShots extends N2GeneratorAbstract {

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);
    }

    protected function _getData($count, $startIndex) {
        $data = array();
        $api  = $this->group->getConfiguration()
            ->getApi();

        $result  = null;
        $success = $api->CallAPI('https://api.dribbble.com/v2/user/shots', 'GET', array( 'per_page' => $count + $startIndex ), array( 'FailOnAccessError' => true ), $result);
        if (is_array($result)) {
            $shots = array_slice($result, $startIndex, $count);

            foreach ($shots AS $shot) {
                $p = array(
                    'image'       => isset($shot->images->hidpi) ? $shot->images->hidpi : $shot->images->normal,
                    'thumbnail'   => $shot->images->teaser,
                    'title'       => $shot->title,
                    'description' => $shot->description,
                    'url'         => $shot->html_url,
                    'url_label'   => n2_('View'),

                    'image_normal' => $shot->images->normal
                );
                foreach ($shot->tags AS $j => $tag) {
                    $p['tag_' . ($j + 1)] = $tag;
                }
                $data[] = $p;
            }
        }

        return $data;
    }
}