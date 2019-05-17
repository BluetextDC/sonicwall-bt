<?php
/**
 * HTML for the facebook connect box when user is logged out
 *
 * @var AEPC_Admin_View $page
 * @var AEPC_Facebook_Adapter $fb
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

?>

<article class="sub-panel sub-panel-fb-connect">
	<div class="control-label">
		<h3 class="tit">
			<?php _e( 'Facebook Connect', 'pixel-caffeine' ) ?>
			<a href="#_" class="btn btn-fab btn-help btn-fab-mini" data-toggle="tooltip" data-placement="top" title="<?php _e( 'Connect your Ad account in Pixel Caffeine', 'pixel-caffeine' ) ?>"></a>
		</h3>
	</div>
	<p class="text"><?php _e( 'The easiest whay to get up and running with all the advanced features. Connect your Facebook account and you\'re good to go!', 'pixel-caffeine' ) ?></p>

	<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-primary btn-raised btn-fb-connect btn-block">
		<?php _e( 'Facebook Connect', 'pixel-caffeine' ) ?>
	</a>
</article><!-- ./sub-panel -->
