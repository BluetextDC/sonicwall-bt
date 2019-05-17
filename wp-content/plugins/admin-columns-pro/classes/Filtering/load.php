<?php

define( 'ACP_FILTERING_FILE', __FILE__ );

AC\Autoloader::instance()->register_prefix( 'ACP\Filtering', __DIR__ . '/classes' );
AC\Autoloader\Underscore::instance()->add_alias( 'ACP\Filtering\Filterable', 'ACP_Column_FilteringInterface' );