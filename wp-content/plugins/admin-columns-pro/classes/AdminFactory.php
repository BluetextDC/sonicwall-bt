<?php

namespace ACP;

use AC;
use ACP\Editing\Admin\CustomFieldEditing;
use ACP\Sorting\Admin\Section\ResetSorting;
use ACP\Sorting\Admin\ShowAllResults;

class AdminFactory {

	public function create( $is_network, AC\Admin $admin, License\API $api = null ) {

		if ( $is_network ) {

			$page = new AC\Admin\Page\Settings();
			$page->register_section( new License\Settings( $api ) );

			$admin->register_page( $page )
			      ->register();

		} else {

			// General Settings
			$general = AC\Admin\GeneralSectionFactory::create();

			$general->register_setting( new CustomFieldEditing() )
			        ->register_setting( new ShowAllResults() );

			/** @var AC\Admin\Page\Settings $settings */
			$settings = AC()->admin()->get_page( AC\Admin\Page\Settings::NAME );
			$settings->register_section( new License\Settings( $api ) )
			         ->register_section( new ResetSorting() );

			$admin->register_page( new Admin\Page\ExportImport() );
		}
	}

}