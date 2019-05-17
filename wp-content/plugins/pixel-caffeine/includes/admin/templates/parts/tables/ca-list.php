<?php
/**
 * Custom audiences list table
 *
 * @var AEPC_Admin_View $page
 * @var bool $disabled
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$audiences = $page->get_audiences();

if ( ( ! isset( $_GET['paged'] ) || $_GET['paged'] <= 1 ) && empty( $audiences ) ) {
	return;
}

?>

<div class="panel panel-ca-list<?php echo $disabled ? ' disabled-box' : '' ?>">
	<div class="panel-heading">
		<h2 class="tit"><?php _e( 'Custom Audiences', 'pixel-caffeine' ) ?></h2>
	</div>
	<div class="panel-body">
		<table class="table table-striped table-hover js-table">
			<thead>
			<tr>
				<th class="name"><?php _e( 'Name', 'pixel-caffeine' ) ?></th>
				<th><?php _e( 'Date', 'pixel-caffeine' ) ?></th>
				<th><?php _e( 'Size', 'pixel-caffeine' ) ?></th>
				<th class="actions"><?php _e( 'Actions', 'pixel-caffeine' ) ?></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ( $audiences as $audience ) : ?>
			<tr>
				<td class="name">
					<?php echo $audience->get_name() ?>
					<small class="info-extra"><?php echo $audience->get_description() ?></small>
				</td>
				<td class="date">
					<?php $audience->human_date( 'h_time' ) ?>
					<small class="info-extra"><?php $audience->human_date( 't_time' ) ?></small>
				</td>
				<td>
					<?php _e( 'Not available', 'pixel-caffeine' ) ?>
					<a
						href="#_"
						class="btn btn-fab btn-help btn-fab-mini"
						data-toggle="popover"
						data-placement="top"
						data-html="true"
						data-content="<?php printf( __( 'The custom audience size indicator is not available due to Facebook policies based on %sAdvertising Principles%s.', 'pixel-caffeine' ), "<a href='https://newsroom.fb.com/news/2017/11/our-advertising-principles/' target='_blank'>", '</a>' ) ?>"></a>
				</td>
				<td class="actions">
					<div class="btn-group-sm">
						<a
							href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'ca-delete', 'id' => $audience->get_id() ), $page->get_view_url() ), 'delete_custom_audience' ) ) ?>"
							data-toggle="modal" data-target="#modal-confirm-delete" data-remote="false"
							class="btn btn-fab btn-delete btn-danger js-conversion-delete"
						></a>
						<a href="#_" class="btn btn-fab btn-clone btn-primary"<?php $page->audience_data_values( $audience->get_id() ) ?>data-toggle="modal" data-target="#modal-ca-clone"></a>
						<a href="#_" class="btn btn-fab btn-edit btn-primary"<?php $page->audience_data_values( $audience->get_id() ) ?>data-toggle="modal" data-target="#modal-ca-edit"></a>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
	</div>
	<!-- ./panel-body -->

	<?php $page->audiences_pagination( array(
		'list_wrap' => '<div class="panel-footer"><ul class="pagination pagination-sm">%1$s</ul></div>',
		'item_wrap' => '<li>%1$s</li>',
		'item_wrap_active' => '<li class="active">%1$s</li>',
		'item_wrap_disabled' => '<li class="disabled">%1$s</li>',
	) ) ?>
</div>
<!-- ./panel-ca-list -->
