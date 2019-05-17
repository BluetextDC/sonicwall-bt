<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJson extends N2SliderGeneratorPluginAbstract {

	protected $name = 'json';

	public function getLabel() {
		return 'JSON';
	}

	protected function loadSources() {

		new N2GeneratorJsonUrl($this, 'url', 'JSON from url');
		new N2GeneratorJsonInput($this, 'input', 'JSON from input');
	}

	public function getPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJson);
