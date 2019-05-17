<?php
/**
 * Logs list table
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\Logs\LogRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$logRepository = new LogRepository();
$limit = apply_filters( 'aepc_logs_per_page', 20 );
$paged = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
$offset = $limit * ( $paged - 1 );
$logs = $logRepository->findAll( array( 'date' => 'DESC' ), $limit, $offset );

?>

<div class="panel panel-log-list">
	<div class="panel-heading">
		<h2 class="tit"><?php _e( 'Logs', 'pixel-caffeine' ) ?></h2>
		<?php if ( $logs ) : ?>
		<a class="btn js-remove-logs" href="#_"><?php _e( 'Clear all', 'pixel-caffeine' ) ?></a>
		<?php endif; ?>
	</div>
	<div class="panel-body">
		<?php if ( $logs ) : ?>
		<table class="table table-striped table-hover js-table">
			<thead>
			<tr>
				<th class="name"><?php _e( 'Exception', 'pixel-caffeine' ) ?></th>
				<th><?php _e( 'Date', 'pixel-caffeine' ) ?></th>
				<th><?php _e( 'Message', 'pixel-caffeine' ) ?></th>
				<th class="actions"><?php _e( 'Actions', 'pixel-caffeine' ) ?></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ( $logs as $log ) : ?>
			<tr>
				<td class="exception">
					<strong>
						<?php
						$exception = explode( '\\', $log->getException() );
						echo array_pop( $exception );
						?>
					</strong>
				</td>
				<td class="date">
					<?php echo $page->get_human_date( $log->getDate(), 'h_time' ) ?>
					<small class="info-extra"><?php echo $page->get_human_date( $log->getDate(), 't_time' ) ?></small>
				</td>
				<td>
					<?php echo $log->getMessage() ?>
				</td>
				<td class="actions">
					<div class="btn-group-sm">
						<a href="<?php echo esc_url_raw( wp_nonce_url( $page->get_view_url( array(
							'action' => 'aepc_download_log_report',
							'log' => $log->getId()
						) ), 'aepc_download_log_report' ) ) ?>"
						   target="_blank"
						   class="btn btn-download btn-primary btn-raised"
						>
							<?php _e( 'Download report', 'pixel-caffeine' ) ?>
						</a>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
		<?php else : ?>
			<p class="text"><?php _e( 'No logs registered yet.', 'pixel-caffeine' ) ?></p>
		<?php endif ?>
	</div>
	<!-- ./panel-body -->

	<?php
	if ( $logsCount = $logRepository->getCountAll() ) {
		echo $page->get_pagination( $logsCount, array(
			'per_page'           => $limit,
			'list_wrap'          => '<div class="panel-footer"><ul class="pagination pagination-sm">%1$s</ul></div>',
			'item_wrap'          => '<li>%1$s</li>',
			'item_wrap_active'   => '<li class="active">%1$s</li>',
			'item_wrap_disabled' => '<li class="disabled">%1$s</li>',
		) );
	}
	?>
</div>
<!-- ./panel-ca-list -->

<?php $page->get_template_part( 'modals/confirm-delete', array(
	'title' => __( 'Remove all logs', 'pixel-caffeine' ),
	'message' => __( 'Are you sure you want to remove all logs?', 'pixel-caffeine' )
) ) ?>
