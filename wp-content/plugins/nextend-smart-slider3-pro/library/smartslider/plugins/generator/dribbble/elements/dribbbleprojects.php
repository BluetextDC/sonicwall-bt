<?php

N2Loader::import('libraries.form.elements.list');
N2Loader::import('libraries.parse.parse');

class N2ElementDribbbleProjects extends N2ElementList {

    /** @var  N2OAuth */
    protected $api;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        try {
            $success = $this->api->CallAPI('https://api.dribbble.com/v2/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            if (count($result)) {
                foreach ($result AS $project) {
                    $this->options[$project->id] = $project->name;
                }
                if ($this->getValue() == '') {
                    $this->setValue($result[0]->id);
                }
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
    }

    protected function fetchElement() {
        return parent::fetchElement();
    }

    public function setApi($api) {
        $this->api = $api;
    }
}
