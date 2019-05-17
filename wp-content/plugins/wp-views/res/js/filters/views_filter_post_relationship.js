/**
* Views Post Relationship Filter GUI - script
*
* Adds basic interaction for the Post Relationship Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.PostRelationshipFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.spinner				= '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.warning_post_missing	= '<p class="js-wpv-filter-post-relationship-missing js-wpv-permanent-alert-error toolset-alert toolset-alert-warning"><i class="fa fa-info-circle" aria-hidden"true"=""></i> ' + wpv_pr_strings.post.post_type_missing + '</p>';
	self.warning_post_orphan	= '<p class="js-wpv-filter-post-relationship-orphan js-wpv-permanent-alert-error toolset-alert toolset-alert-warning"><i class="fa fa-info-circle" aria-hidden"true"=""></i> ' + wpv_pr_strings.post.post_type_orphan + '</p>';
	self.disabled_for_loop		= '<p class="js-wpv-archive-filter-post-relationship-disabled js-wpv-permanent-alert-error toolset-alert toolset-alert-warning"><i class="fa fa-info-circle" aria-hidden"true"=""></i> ' + wpv_pr_strings.archive.disable_post_relationship_filter + '</p>';
	
	self.post_row = '.js-wpv-filter-row-post-relationship';
	self.post_options_container_selector = '.js-wpv-filter-post-relationship-options';
	self.post_summary_container_selector = '.js-wpv-filter-post-relationship-summary';
	self.post_messages_container_selector = '.js-wpv-filter-row-post-relationship .js-wpv-filter-toolset-messages';
	self.post_edit_open_selector = '.js-wpv-filter-post-relationship-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-relationship-edit-ok';
	
	self.post_type_select = {};
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	//--------------------
	// Functions for post relationship
	//--------------------
	
	//--------------------
	// Events for post relationship
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.post_edit_open_selector, function() {
		self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		$( self.post_close_save_selector ).show();
		$( self.post_row ).addClass( 'wpv-filter-row-current' );
	});
	
	// Track changes in options
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() {
		$( this ).removeClass( 'filter-input-error' );
		$( self.post_close_save_selector ).prop( 'disabled', false );
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options != $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-queue', { section: 'save_filter_post_relationship', action: 'add' } );
			$( self.post_close_save_selector )
				.addClass('button-primary js-wpv-section-unsaved')
				.removeClass('button-secondary')
				.html(
					WPViews.query_filters.icon_save + $( self.post_close_save_selector ).data('save')
				);
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-set-confirm-unload', true );
		} else {
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-queue', { section: 'save_filter_post_relationship', action: 'remove' } );
			$( self.post_close_save_selector )
				.addClass('button-secondary')
				.removeClass('button-primary js-wpv-section-unsaved')
				.html(
					WPViews.query_filters.icon_edit + $( self.post_close_save_selector ).data('close')
				);
			$( self.post_close_save_selector )
				.parent()
					.find( '.unsaved' )
					.remove();
			$( document ).trigger( 'js_event_wpv_set_confirmation_unload_check' );
		}
	});
	
	// Save filter options
	
	self.save_filter_post_relationship = function( event, propagate ) {
		var thiz = $( self.post_close_save_selector );
		WPViews.query_filters.clear_validate_messages( self.post_row );
		
		Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-queue', { section: 'save_filter_post_relationship', action: 'remove' } );
		
		if ( self.post_current_options == $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row( self.post_row );
			thiz.hide();
		} else {
			var valid = WPViews.query_filters.validate_filter_options( '.js-filter-post-relationship' );
			if ( valid ) {
				var action = thiz.data( 'saveaction' ),
				nonce = thiz.data('nonce'),
				spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
				error_container = thiz
					.closest( '.js-filter-row' )
						.find( '.js-wpv-filter-toolset-messages' );
				self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
				var data = {
					action:			action,
					id:				self.view_id,
					filter_options:	self.post_current_options,
					wpnonce:		nonce
				};
				$.ajax( {
					type:		"POST",
					url:		ajaxurl,
					dataType:	"json",
					data:		data,
					success:	function( response ) {
						if ( response.success ) {
							$( self.post_close_save_selector )
								.addClass('button-secondary')
								.removeClass('button-primary js-wpv-section-unsaved')
								.html( 
									WPViews.query_filters.icon_edit + $( self.post_close_save_selector ).data( 'close' )
								);
							$( self.post_summary_container_selector ).html( response.data.summary );
							WPViews.query_filters.close_and_glow_filter_row( self.post_row, 'wpv-filter-saved' );
							Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-parametric-search-hints', response.data.parametric );
							$( document ).trigger( event );
							if ( propagate ) {
								$( document ).trigger( 'js_wpv_save_section_queue' );
							} else {
								$( document ).trigger( 'js_event_wpv_set_confirmation_unload_check' );
							}
						} else {
							Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-ajax-fail', { data: response.data, container: error_container} );
							if ( propagate ) {
								$( document ).trigger( 'js_wpv_save_section_queue' );
							}
						}
					},
					error:		function( ajaxContext ) {
						console.log( "Error: ", textStatus, errorThrown );
						Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-fail-queue', 'save_filter_post_relationship' );
						if ( propagate ) {
							$( document ).trigger( 'js_wpv_save_section_queue' );
						}
					},
					complete:	function() {
						spinnerContainer.remove();
						thiz
							.prop( 'disabled', false )
							.hide();
					}
				});
			} else {
				Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-fail-queue', 'save_filter_post_relationship' );
				if ( propagate ) {
					$( document ).trigger( 'js_wpv_save_section_queue' );
				}
			}
		}
	};
	
	$( document ).on( 'click', self.post_close_save_selector, function() {
		self.save_filter_post_relationship( 'js_event_wpv_save_filter_post_relationship_completed', false );
	});
		
	// Remove filter from the save queue an clean cache
	
	$( document ).on( 'js_event_wpv_query_filter_deleted', function( event, filter_type ) {
		if ( 'post_relationship' == filter_type ) {
			self.post_current_options = '';
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-queue', { section: 'save_filter_post_relationship', action: 'remove' } );
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-get-parametric-search-hints' );
		}
	});
	
	// Update posts selector when changing the specific option post type
	// Cache options to prevent multiple AJAX calls for the same post type

	$( document ).on( 'change', '.js-post-relationship-post-type', function() {
		// Update the parents for the selected type.
		var post_type = $('.js-post-relationship-post-type').val();
		$( 'select#post_relationship_id' ).remove();
		if ( typeof self.post_type_select[post_type] == "undefined" ) {
			var data = {
				action : 'wpv_get_post_relationship_post_select',
				post_type : post_type,
				wpnonce : $('.js-post-relationship-post-type').data('nonce')
			};
			var spinnerContainer = $( self.spinner ).insertAfter( $(this) ).show();
			$.post( ajaxurl, data, function( response ) {
				if ( typeof( response ) !== 'undefined' ) {
					if ( response != 0 ) {
						self.post_type_select[post_type] = response;
						$( '.js-post-relationship-post-type' ).after( self.post_type_select[post_type] );
						$( '.js-post-relationship-shortcode-attribute' ).trigger( 'change' );
					} else {
						console.log( "Error: WordPress AJAX returned " + response );
					}
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			})
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				spinnerContainer.hide();
			});
		} else {
			$( '.js-post-relationship-post-type' ).after( self.post_type_select[post_type] );
			$( '.js-post-relationship-shortcode-attribute' ).trigger( 'change' );
		}
	});
	
	//--------------------
	// Manage post relationship filter restrictions
	//--------------------
	
	/**
	* manage_post_relationship_filter_warning
	*
	* Add or remove the warning messages when:
	* 	- Views- querying non-Types-children post types
	* 	- WPA - combining a query filter by post parent and native, taxonomy or non-Types-children post type archive loops
	*
	* @since 2.1
	*/
	
	self.manage_post_relationship_filter_warning = function() {
		var query_mode					= Toolset.hooks.applyFilters( 'wpv-filter-wpv-edit-screen-get-query-mode', 'normal' ),
		orphan_post_types_selected		= [],
		orphan_loops_selected				= [],
		post_relationship_filter_row	= $( self.post_row ),
		auxiliar_string					= '',
		auxiliar_warning				= '',
		auxiliar_boolean				= false;
		
		if ( post_relationship_filter_row.length > 0 ) {
			if ( 'normal' == query_mode ) {
				$( '.js-wpv-filter-post-relationship-missing, .js-wpv-filter-post-relationship-orphan' ).remove();
				if ( $('.js-wpv-query-post-type:checked').length ) {
					orphan_post_types_selected = $('.js-wpv-query-post-type:checked').map( function() {
						if ( $( this ).data( 'typeschild' ) == 'no' ) {
							return $( this ).parent( 'li' ).find( 'label' ).html();
						}
					}).get();
					if ( orphan_post_types_selected.length > 0 ) {
						auxiliar_string = orphan_post_types_selected.join( ', ' );
						auxiliar_warning = self.warning_post_orphan;
						auxiliar_warning = auxiliar_warning.replace( '%s', auxiliar_string );
						$( auxiliar_warning ).prependTo( self.post_row ).show();
					}
				} else {
					$( self.warning_post_missing ).prependTo( self.post_row ).show();
				}
			} else {
				$( '.js-wpv-archive-filter-post-relationship-disabled' ).remove();
				orphan_loops_selected = $( '.js-wpv-settings-archive-loop input:checked' ).map( function() {
					auxiliar_boolean = false;
					switch ( $( this ).data( 'type' ) ) {
						case 'native':
							auxiliar_boolean = true;
							break;
						case 'post_type' :
							if ( $( this ).data( 'typeschild' ) == 'no' ) {
								auxiliar_boolean = true;
							}
							break;
						case 'taxonomy':
							auxiliar_boolean = true;
							break;
					}
					return auxiliar_boolean ? $( this ).data( 'name' ) : '';
				}).get();
				orphan_loops_selected = _.compact( orphan_loops_selected );
				if ( orphan_loops_selected.length > 0 ) {
					$( self.disabled_for_loop ).prependTo( self.post_row ).show();
				}
			}
		}
	};
	
	// Content selection section saved event
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function( event, query_type ) {
		self.manage_post_relationship_filter_warning();
	});
	
	// Filter creation event
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'post_relationship' ) {
			self.manage_post_relationship_filter_warning();
			Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-manage-save-queue', { section: 'save_filter_post_relationship', action: 'add' } );
		}
		if ( filter_type == 'parametric-all' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		}
	});
	
	$( document ).on( 'js_event_wpv_save_section_loop_selection_completed', function( event ) {
		self.manage_post_relationship_filter_warning();
	});
	
	//--------------------
	// Init hooks
	//--------------------
	
	self.init_hooks = function() {
		// Register the filter saving action
		Toolset.hooks.doAction( 'wpv-action-wpv-edit-screen-define-save-callbacks', {
			handle:		'save_filter_post_relationship',
			callback:	self.save_filter_post_relationship,
			event:		'js_event_wpv_save_filter_post_relationship_completed'
		});
	};
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.manage_post_relationship_filter_warning();
		self.init_hooks();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.post_relationship_filter_gui = new WPViews.PostRelationshipFilterGUI( $ );
});