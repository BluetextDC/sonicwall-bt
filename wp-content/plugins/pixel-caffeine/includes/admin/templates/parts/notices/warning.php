<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var string $title
 * @var string $message
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="alert alert-warning alert-dismissable" role="alert">
	<button
		type="button"
		class="close"
		data-dismiss="alert"
		<?php if ( ! empty( $dismiss_action ) ) : ?>data-dismiss-action="<?php echo esc_attr( $dismiss_action ) ?>"<?php endif; ?>
	>×</button>
	<?php if ( ! empty( $title ) ) : ?><strong><?php echo $title ?></strong><?php endif; ?>
	<?php echo $message ?>
</div>
