<?php
/**
 * Conversion event tracking table
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$conversions = $page->get_conversions();

if ( empty( $conversions ) ) {
	return;
}

?>

<div class="panel panel-ce-tracking">
	<div class="panel-heading"><h2 class="tit"><?php _e( 'Active tracking', 'pixel-caffeine' ) ?></h2></div>
	<div class="panel-body">
		<table class="table table-striped table-hover js-table">
			<thead>
			<tr>
				<th class="name"><?php _e( 'Name', 'pixel-caffeine' ) ?></th>
				<th class="url"><?php _e( 'Trigger', 'pixel-caffeine' ) ?></th>
				<th><?php _e( 'Code', 'pixel-caffeine' ) ?></th>
				<th class="actions"><?php _e( 'Actions', 'pixel-caffeine' ) ?></th>
			<tr>
			</thead>
			<tbody>

			<?php foreach ( $conversions as $id => $event ) : ?>
				<tr>
					<td class="name"><?php echo esc_html( $event['name'] ) ?></td>
					<td class="url">
						<?php
						if ( 'page_visit' == $event['trigger'] ) {
							esc_html_e( 'Page visit', 'pixel-caffeine' );
							echo '<br />' . __( 'URL contains', 'pixel-caffeine' ) . ': ' . $event['url'];
						}

						elseif ( 'link_click' == $event['trigger'] ) {
							esc_html_e( 'Link click', 'pixel-caffeine' );
							echo '<br />' . __( 'URL contains', 'pixel-caffeine' ) . ': ' . $event['url'];
						}

						elseif ( 'css_selector' == $event['trigger'] ) {
							esc_html_e( 'On click css selector', 'pixel-caffeine' );
							echo '<br />' . '"' . esc_html( $event['css'] ) . '"';
						}
						?>
					</td>
					<td>
						<a href="#_"
						   class="show-code"
						   tabindex="<?php echo esc_attr( $id ) ?>"
						   data-toggle="popover"
						   data-trigger="focus"
						   data-content="<?php echo esc_attr( $page->get_track_code( $event ) ) ?>"><?php _e( 'Show code', 'pixel-caffeine' ) ?></a>
					</td>
					<td class="actions">
						<div class="btn-group-sm">
							<a
								href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'conversion-delete', 'id' => $id ), $page->get_view_url() ), 'delete_tracking_conversion' ) ) ?>"
								class="btn btn-fab btn-delete btn-danger js-conversion-delete"
								data-toggle="modal" data-target="#modal-confirm-delete" data-remote="false"
							></a>
							<a href="#_" class="btn btn-fab btn-edit btn-primary"<?php $page->conversion_data_values( $id ) ?> data-toggle="modal" data-target="#modal-conversion-edit"></a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
	</div>

	<?php $page->conversions_pagination( array(
		'list_wrap' => '<div class="panel-footer"><ul class="pagination pagination-sm">%1$s</ul></div>',
		'item_wrap' => '<li>%1$s</li>',
		'item_wrap_active' => '<li class="active">%1$s</li>',
		'item_wrap_disabled' => '<li class="disabled">%1$s</li>',
	) ) ?>
</div>
