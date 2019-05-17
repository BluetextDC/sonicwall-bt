<?php

final class Timeline_Express_Date_Time_Migrations {

	/**
	 * Array of announcement IDs that do not contain any date-time meta
	 * @var array
	 */
	private $announcements_no_date_time;

	public function __construct() {

		$migration_notice                 = get_option( 'timeline_express_date_time_migration_notice', true );
		$this->announcements_no_date_time = (array) get_option( 'timeline_express_date_time_migration_ids', [] );

		if ( ! $migration_notice || empty( $this->announcements_no_date_time ) ) {

			return;

		}

		// Register our hidden page to perform the migrations on
		add_action( 'admin_menu', [ $this, 'tedt_register_hidden_page' ] );

		// Enqueue our migration script
		add_action( 'admin_enqueue_scripts', [ $this, 'tedt_enqueue_script' ] );

		// Display admin notice
		add_action( 'admin_notices', [ $this, 'admin_notice' ] );

		// Cancel migration admin notice
		add_action( 'admin_init', [ $this, 'cancel_admin_notice' ] );

		// AJAX handler
		add_action( 'wp_ajax_migrate_announcement', [ $this, 'migrate_announcement' ] );

	}

	/**
	 * Render the hidden migration page.
	 *
	 * @since 1.0.0
	 */
	public function tedt_register_hidden_page() {

		add_submenu_page(
			null,
			__( 'Timeline Express - Date Time Add-On Migration', 'timeline-express-date-time-add-on' ),
			__( 'Timeline Express - Date Time Add-On Migration', 'timeline-express-date-time-add-on' ),
			'manage_options',
			'timeline-express-date-time-migration',
			[ $this, 'tedt_migration_hidden_page' ]
		);

	}

	/**
	 * Migration page markup.
	 *
	 * @return mixed Markup for the migration page.
	 *
	 * @since 1.0.0
	 */
	public function tedt_migration_hidden_page() {

		$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'date_time_migration' ) ) {

			die( __( 'Cheatin&#8217; uh?' ) );

		}

		?>

		<!-- Hide the admin notice while migrating is in progress -->
		<style type="text/css">.notice.notice-warning.tedt-migration{display:none;}</style>

		<div class="wrap">

			<h2><?php esc_html_e( 'Timeline Express - Date - Time Add-On Migration', 'timeline-express-date-tiem-add-on' ); ?></h2>

			<p class="migration-underway-message"><?php esc_html_e( 'We are performing your migration. Site tight while we convert your existing announcements to use date & time values...', 'timeline-express-date-tiem-add-on' ); ?></p>
			<p class="hidden migration-complete-message"><?php esc_html_e( 'Migration complete...Redirecting...', 'timeline-express-date-tiem-add-on' ); ?><hr /></p>

			<img class="tedt-preloader" src="<?php echo esc_url( includes_url( '/images/spinner.gif' ) ); ?>" />

			<ul class="results"></ul>

		</div>

		<?php

	}

	/**
	 * Enqueue migration script.
	 *
	 * @param  string $hook Current admin page suffix.
	 *
	 * @since 1.0.0
	 */
	public function tedt_enqueue_script( $hook ) {

		if ( 'admin_page_timeline-express-date-time-migration' !== $hook ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'tedt-migration', TIMELINE_EXPRESS_DATE_TIME_URL . "lib/js/migration{$suffix}.js" );

		wp_localize_script( 'tedt-migration', 'tedtMigrationData', [
			'announcementIDS'                  => $this->announcements_no_date_time,
			'timelineExpressAnnouncementsList' => admin_url( 'edit.php?post_type=te_announcements' ),
		] );

	}

	/**
	 * Display the migration admin notice
	 *
	 * @return mixed Markup for the admin notice.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice() {

		$screen = get_current_screen();
		$page   = isset( $screen->parent_file ) ? admin_url( $screen->parent_file ) : admin_url();

		printf(
			'<div class="notice notice-warning tedt-migration" style="background: url(\'%1$s\') #fff no-repeat bottom right; background-size: 125px;">
				<h3 style="margin-bottom: 0;">Timeline Express - Date - Time Add-On</h3>
				<p style="max-width:90%%;">%2$s</p>
				<p>%3$s %4$s</p>
			</div>',
			TIMELINE_EXPRESS_DATE_TIME_URL . 'lib/img/code-parrots-mascot.png',
			sprintf(
				/* translators: 1. The number of announcements that need to be migrated. Boolean. */
				_n(
					"We found %s announcement that doesn't contain a date/time value. Do you want to update the announcement date to a date/time value? Otherwise, the announcement may not be visible on the timeline.",
					'We found %s announcements that do not contain date/time values. Do you want to update the announcement dates to use the date/time values? Otherwise, those announcements may not be visible on the timeline.',
					number_format_i18n( count( $this->announcements_no_date_time ) ),
					'timeline-express-date-time-add-on'
				),
				number_format_i18n( count( $this->announcements_no_date_time ) )
			),
			sprintf(
				'<a href="%1$s" class="button button-primary">%2$s</a>',
				wp_nonce_url( admin_url( 'admin.php?page=timeline-express-date-time-migration' ), 'date_time_migration' ),
				esc_html__( 'Begin Migration', 'timeline-express-date-time-add-on' )
			),
			sprintf(
				'<a href="%1$s" class="button button-secondary">%2$s</a>',
				wp_nonce_url( $page, 'cancel_date_time_migration', '_tedt_wpnonce' ),
				esc_html__( 'No, thanks', 'timeline-express-date-time-add-on' )
			)
		);

	}

	/**
	 * Cancel the migration notice.
	 * Runs when user clicks 'No Thanks' button in the admin notice.
	 *
	 * @since 1.0.0
	 */
	public function cancel_admin_notice() {

		$nonce = filter_input( INPUT_GET, '_tedt_wpnonce', FILTER_SANITIZE_STRING );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'cancel_date_time_migration' ) ) {

			return;

		}

		update_option( 'timeline_express_date_time_migration_notice', false );

		$referer = ! wp_get_referer() ? admin_url() : wp_get_referer();

		wp_safe_redirect( $referer );

	}

	/**
	 * AJAX handler to update an announcement
	 *
	 * @return boolean AJAX response
	 */
	public function migrate_announcement() {

		$announcement_id = (int) filter_input( INPUT_POST, 'announcementID', FILTER_SANITIZE_STRING );

		$last = (bool) filter_input( INPUT_POST, 'last', FILTER_SANITIZE_NUMBER_INT );
		$post = get_post( $announcement_id );

		$announcement_ids = get_option( 'timeline_express_date_time_migration_ids', [] );

		// Unset the announcment ID from the option
		if ( in_array( $announcement_id, $announcement_ids, true ) ) {

			unset( $announcement_ids[ array_search( $announcement_id, $announcement_ids, true ) ] );

			update_option( 'timeline_express_date_time_migration_ids', $announcement_ids );

		}

		/**
		 * Note: When last, we delete the 'timeline_express_date_time_migration_notice' option
		 *       Flush existing page transients, so timelines display the new announcements.
		 */
		if ( $last ) {

			update_option( 'timeline_express_date_time_migration_notice', false );

		}

		// Ensure that the announcement doesn't have an announcement-date-time already
		if ( ! empty( get_post_meta( $announcement_id, 'announcement-date-time', true ) ) ) {

			// Flush existing transients on last
			if ( $last ) {

				delete_timeline_express_transients();

			}

			wp_send_json_error( [
				'last'    => $last,
				'message' => sprintf(
					'%s already has an announcement-date-time value. Nothing updated.',
					esc_html( $post->post_title )
				),
			] );

			return;

		}

		$announcement_date = get_post_meta( $announcement_id, 'announcement_date', true );

		// Update the announcement date-time value
		update_post_meta( $announcement_id, 'announcement-date-time', $announcement_date );

		// Update the announcement date format value, default to site date format option (so 12:00 AM is not displayed initially)
		update_post_meta( $announcement_id, 'announcement-date-format', get_option( 'date_format' ) );

		// Flush existing transients
		if ( $last ) {

			delete_timeline_express_transients();

		}

		wp_send_json_success( [
			'last'    => $last,
			'message' => sprintf(
				'%s updated successfully.',
				esc_html( $post->post_title )
			),
		] );

		exit;

	}

}
new Timeline_Express_Date_Time_Migrations();
