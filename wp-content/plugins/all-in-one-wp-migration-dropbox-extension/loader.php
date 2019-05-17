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

// Include all the files that you want to load in here
if ( defined( 'WP_CLI' ) ) {
	require_once AI1WMDE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'class-ai1wmde-dropbox-wp-cli-command.php';

	require_once AI1WMDE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'class-ai1wm-backup-wp-cli-command.php';
}

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-main-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-settings-controller.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-dropbox.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-upload.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-retention.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-done.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-dropbox.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-download.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-settings.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-database.php';

require_once AI1WMDE_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-settings.php';

require_once AI1WMDE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'dropbox-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-dropbox-client.php';

require_once AI1WMDE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'dropbox-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-dropbox-curl.php';
