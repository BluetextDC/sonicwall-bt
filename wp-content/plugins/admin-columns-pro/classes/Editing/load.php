<?php

define( 'ACP_EDITING_FILE', __FILE__ );

AC\Autoloader::instance()->register_prefix( 'ACP\Editing', __DIR__ . '/classes' );
AC\Autoloader\Underscore::instance()->add_alias( 'ACP\Editing\Editable', 'ACP_Column_EditingInterface' );