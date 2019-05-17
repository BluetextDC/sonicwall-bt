<?php

define( 'ACP_EXPORT_FILE', __FILE__ );

AC\Autoloader::instance()->register_prefix( 'ACP\Export', __DIR__ . '/classes' );
AC\Autoloader\Underscore::instance()->add_alias( 'ACP\Export\Exportable', 'ACP_Export_Column' );