( function( $) {

	var migration = {

		init: function() {

			// When a single announcement is found, an object is passed - convert to array here
			if ( ! tedtMigrationData.announcementIDS.isArray ) {
				tedtMigrationData.announcementIDS = Object.keys( tedtMigrationData.announcementIDS ).map( function ( key ) {
					return tedtMigrationData.announcementIDS[ key ];
				} );
			}

			migration.updateAnnouncement( 0 );

		},

		/**
		 * Fire the AJAX request to update the announcement.
		 *
		 * @param integer key The announcement ID index to update.
		 */
		updateAnnouncement: function( key ) {

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					'action': 'migrate_announcement',
					'announcementID': tedtMigrationData.announcementIDS[ key ],
					'last': ( ( tedtMigrationData.announcementIDS.length - 1 ) === key ) ? 1 : 0
				},
				success: function( response ) {

					$( '.results' ).append( '<li>' + response.data.message + '</li>' );

					if ( response.data.last ) {

						$( '.tedt-preloader' ).addClass( 'hidden' );
						$( '.migration-underway-message' ).addClass( 'hidden' );
						$( '.migration-complete-message' ).removeClass( 'hidden' );

						setTimeout( function() {

							window.location.replace( tedtMigrationData.timelineExpressAnnouncementsList );

						}, 2000 );

						return;

					}

					setTimeout( function() {

						migration.updateAnnouncement( key + 1 );

					}, 600 );

				}
			} );

		},

	};

		$( document ).on( 'ready', function() {

			setTimeout( function() {

				migration.init();

			}, 2000 );

		} );

} )( jQuery );
