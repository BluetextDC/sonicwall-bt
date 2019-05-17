<?php
/**
 * Timeline Express :: Addons Page Class
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 **/

class Timeline_Express_Addons {
	/**
	 * Main constructor
	 */
	public function __construct() {

		$this->render_addons_page();

	}

	/**
	 * Render the addons page content
	 *
	 * @return string addons page contents
	 */
	public function render_addons_page() {

		ob_start();

		include_once( TIMELINE_EXPRESS_PATH . 'lib/admin/pages/page.addons.php' );

		return ob_get_contents();

	}

}

new Timeline_Express_Addons;
