<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Title extends Model\Post\TitleRaw {

	public function get_view_settings() {
		$settings = parent::get_view_settings();

		$settings['js']['selector'] = 'a.row-title';

		return $settings;
	}

}