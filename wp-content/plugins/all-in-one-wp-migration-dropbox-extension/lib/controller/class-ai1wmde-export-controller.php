<?php
/**
 * Copyright (C) 2014-2019 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wmde_Export_Controller {

	public static function button() {
		return Ai1wm_Template::get_content(
			'export/button',
			array( 'token' => get_option( 'ai1wmde_dropbox_token', false ) ),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function inactive_themes() {
		Ai1wm_Template::render(
			'export/inactive-themes',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function inactive_plugins() {
		Ai1wm_Template::render(
			'export/inactive-plugins',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function cache_files() {
		Ai1wm_Template::render(
			'export/cache-files',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}
}
