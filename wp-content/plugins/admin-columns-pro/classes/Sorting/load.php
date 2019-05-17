<?php

define( 'ACP_SORTING_FILE', __FILE__ );

AC\Autoloader::instance()->register_prefix( 'ACP\Sorting', __DIR__ . '/classes' );
AC\Autoloader\Underscore::instance()->add_alias( 'ACP\Sorting\Sortable', 'ACP_Column_SortingInterface' );