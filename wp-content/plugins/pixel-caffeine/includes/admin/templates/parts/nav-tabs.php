<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$tabs = apply_filters( 'aepc_admin_nav_tabs', AEPC_Admin_Menu::get_page_titles() );
$skip_tabs = array(
	'welcome'
);

?>

<nav class="nav-tab-wrapper">
	<?php
	foreach ( $tabs as $tab => $label ) {
		if ( in_array( $tab, $skip_tabs ) ) {
			continue;
		}

		?><a href="<?php echo $page->get_view_url( 'tab=' . $tab ) ?>" class="nav-tab<?php echo $tab === $page->get_current_tab() ? ' nav-tab-active' : '' ?>"><?php echo esc_html( $label ) ?></a><?php
	}
	?>
</nav>
