<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorInstagramMyPhotos extends N2GeneratorAbstract {

    /** @var  Instagram */
    private $client;
    private $userId = 0;

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);
    }

    protected function _getData($count, $startIndex) {
        $this->client = $this->group->getConfiguration()
            ->getApi();

        $user = json_decode($this->client->getUserSelf(), true);
        if ($user['meta']['code'] == 200 && isset($user['data'])) {
            $this->userId = $user['data']['id'];
        } else {
            N2Message::error("API didn't returned the user. Response code:" . $user['meta']['code']);
            return array();
        }

        $data = array();
        try {
            $items = $this->getPage();
            if (!empty($items)) {
                foreach ($items AS $item) {
                    if ($item['type'] == 'image' || $item['type'] == 'carousel') {
                        $record                          = array();
                        $record['title']                 = $record['caption'] = is_array($item['caption']) ? $item['caption']['text'] : '';
                        $record['image']                 = $record['standard_res_image'] = $item['images']['standard_resolution']['url'];
                        $record['thumbnail']             = $record['thumbnail_image'] = $item['images']['thumbnail']['url'];
                        $record['description']           = n2_('Description is not available');
                        $record['url']                   = $item['link'];
                        $record['url_label']             = n2_('View image');
                        $record['author_name']           = $record['owner_full_name'] = $item['user']['full_name'];
                        $record['author_url']            = $record['owner_website'] = (isset($item['user']['website']) ? $item['user']['website'] : '#');
                        $record['low_res_image']         = $item['images']['low_resolution']['url'];
                        $record['owner_username']        = $item['user']['username'];
                        $record['owner_profile_picture'] = $item['user']['profile_picture'];
                        $record['owner_bio']             = isset($item['user']['bio']) ? $item['user']['bio'] : '';
                        $record['likes_count']           = $item['likes']['count'];
                        $record['comments_count']        = $item['comments']['count'];

                        $data[] = &$record;
                        unset($record);
                    }
                }
            } else {
                N2Message::error('Wrong response from the API or 0 images returned.');
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
        $data = array_slice($data, $startIndex, $count);
        return $data;
    }

    private function getPage() {
        $response = json_decode($this->client->getUserRecent($this->userId, null, '', '', '', 20), true);
        if ($response['meta']['code'] == 200) {
            return $response['data'];
        } else {
            N2Message::error('API response code:' . $response['meta']['code']);
            return null;
        }
    }
}