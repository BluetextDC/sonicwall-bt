(function($){
	$(function(){

		var View = wickedfolders.views.View,
			FolderTree = wickedfolders.views.FolderTree,
			AttachmentFilters = wp.media.view.AttachmentFilters;

		var WickedFoldersAttachmentFilter = AttachmentFilters.extend({

			id: 'wicked-folders-attachment-filter',

			createFilters: function() {

				var filters = {};

				_.each( WickedFoldersProData.folders || {}, function( value, index ) {

					var space = '&nbsp;&nbsp;&nbsp;';

					filters[ index ] = {
						text: 	space.repeat( value.depth ) + value.name,
						props: 	{
							wf_attachment_folders: value.id,
							wicked_folder_type: value.type
						}
					};

				});

				filters.all = {
					text:  wickedFoldersL10n.allFolders,
					props: {
						wf_attachment_folders: ''
					},
					priority: 10
				};

				this.filters = filters;

			}

		});

		wickedfolders.views.AttachmentsBrowserItemsMovedFeedback = wickedfolders.views.View.extend({
			className: 'wicked-items-moved-feedback',

			render: function(){
				// TODO: l10n
				this.$el.html( 'Item(s) moved' );
				return this;
			}
		});

		wickedfolders.views.AttachmentsBrowserDragDetails = wickedfolders.views.View.extend({
			className: 'wicked-drag-details',
			template: _.template( $( '#tmpl-wicked-attachment-browser-drag-details' ).html() ),

			initialize: function(){
				this.collection.on( 'add remove reset', this.render, this );
			},

			render: function(){
				this.$el.html( this.template({
					count: this.collection.length
				}) );
				return this;
			}
		});

		wickedfolders.views.UploaderFolderSelect = wickedfolders.views.FolderSelect.extend({
			events: {
				'change': 'changed'
			},

			initialize: function(){
				this.options.controller.state().on( 'change:wickedSelectedFolder', function( folder ){
					var folder = this.options.controller.state().get( 'wickedSelectedFolder' );
					// Make sure the folder exists as an option before trying
					// to change it (for example, dynamic folders aren't part of
					// the dropdown). Also, only change the folder if syncing
					// is enabled
					if ( this.$( '[value="' + folder.id + '"]' ).length && this.options.syncUploadFolderDropdown ) {
						/*
						// This can happen if wickedSelectedFolder change event fires too early
						if ( ! _.isFunction( this.options.controller.uploader.param ) ) {
							return false;
						}
						*/
						this.options.selected = folder.id;
						this.$el.val( folder.id );
						this.options.controller.uploader.uploader.param( 'wicked_folder_id', folder.id );
					}
				}, this );

				wickedfolders.views.FolderSelect.prototype.initialize.apply( this, arguments );
			},

			changed: function(){
				wickedfolders.views.FolderSelect.prototype.changed.apply( this, arguments );
				var id = this.$el.val();
				this.options.controller.uploader.uploader.param( 'wicked_folder_id', id );
			}
		});

		wickedfolders.views.AttachmentsBrowserFolderTreeToggle = wickedfolders.views.View.extend({
			tagName: 	'a',
			className: 	'wicked-toggle-folders',
			attributes: {
				href: 	'#',
				title: 	wickedFoldersL10n.toggleFolders
			},
			events: {
				'click': 'click'
			},

			initialize: function(){
				var state = this.options.controller.state(),
					preferenceKey = this.options.controller.options.modal ? 'wickedFoldersPaneExpandedModal' : 'wickedFoldersPaneExpandedGrid',
					expandedPreference = window.getUserSetting( preferenceKey, true ),
					expandedPreference = 'false' == expandedPreference ? false : true;

				state.on( 'change:wickedFoldersPaneExpanded', function( state ){

					window.setUserSetting( preferenceKey, state.get( 'wickedFoldersPaneExpanded' ) );
					this.render();
					$( window ).trigger( 'resize.media-modal-columns' );
				}, this );

				if ( ! state.has( 'wickedFoldersPaneExpanded' ) ) {
					state.set( 'wickedFoldersPaneExpanded', expandedPreference );
				}

			},

			click: function( e ){
				e.preventDefault();
				var expanded = this.options.controller.state().get( 'wickedFoldersPaneExpanded' );
				this.options.controller.state().set( 'wickedFoldersPaneExpanded', ! expanded );
			},

			render: function(){
				var expanded = this.options.controller.state().get( 'wickedFoldersPaneExpanded' );
				if ( expanded ) {
					this.options.browser.$el.addClass( 'wicked-folder-pane-expanded' );
				} else {
					this.options.browser.$el.removeClass( 'wicked-folder-pane-expanded' );
				}
				return this;
			}

		});

		wickedfolders.views.AttachmentFolders = wp.media.view.Attachment.extend({
	        tagName:    'div',
			className: 	'wicked-attachment-folders',
			events: {
				'change .wicked-tree .wicked-checkbox input': 'changeChecked'
			},

	        initialize: function(){

				this.options = _.defaults( this.options, {
					wickedFolders: new wickedfolders.collections.Folders()
				});

				this.folderTree = new wickedfolders.views.FolderTree({
					collection: 	this.options.wickedFolders,
					showCheckboxes: true,
					model: 			new wickedfolders.models.FolderTreeState({
						checked: 	this.model.get( 'wickedFolders' )
					})
				});

				this.folderTree.model.on( 'change:checked', this.update, this );

				this.$el.append( '<h2>' + wickedFoldersL10n.attachmentFolders + '</h2>' );
				this.$el.append( '<div class="wicked-container">' );

				wp.media.view.Attachment.prototype.initialize.apply( this, arguments );

				// TODO: look into a better way of handling this...for now, this
				// is a crude way of getting a newly uploaded attachment to show
				// up in the selected folder
				this.model.on( 'change:nonces', function( model ){
					this.options.browser.filterAttachments();
				}, this );

				this.options.wickedFolders.on( 'add remove change:parent', this.render, this );
	        },

	        render: function(){

				// See wp.media.view.Attachment.render for read only logic
				var options = _.defaults( this.model.toJSON(), {
					readOnly: 	false,
					can: 		{}
				}, this.options );

				if ( options.nonces ) {
					options.can.save = !! options.nonces.update;
				}

				if ( options.controller.state().get('allowLocalEdits') ) {
					options.allowLocalEdits = true;
				}

				options.readOnly = options.can.save || options.allowLocalEdits ? false : true;

				this.folderTree.model.set( 'checked', this.model.get( 'wickedFolders' ), {silent: true} );
				this.folderTree.model.set( 'readOnly', options.readOnly );

				this.folderTree.render();

				this.folderTree.$( 'li' ).addClass( 'wicked-expanded' );
				this.$( '.wicked-container' ).html( this.folderTree.el );
				this.folderTree.delegateEvents();

				if ( this.options.wickedFolders.children( '0' ).length ) {
					this.$el.show();
				} else {
					this.$el.hide();
				}

	        },

			update: function(){

				var checked = _.map( this.$( '[type="checkbox"]:checked' ), function( item ){
					return $( item ).val();
				});

				if ( ! checked.length ) checked = false;

				// Update silently to prevent view from being re-rendered
				this.model.set( 'wickedFolders', checked, {silent: true} );
				this.model.save();

			},

			changeChecked: function(){
				this.options.browser.filterAttachments();
			},

			remove: function(){
				this.folderTree.remove();
				wp.media.view.Attachment.prototype.remove.apply( this, arguments );
			}

		});

		wickedfolders.views.AttachmentsBrowserFolderTree = FolderTree.extend({
			className: 'wicked-folder-tree',

			initialize: function() {

				FolderTree.prototype.initialize.apply( this, arguments );

				this.model.on( 'change:selected', function( state ){
					var selected = state.get( 'selected' ),
						folder = this.collection.get( selected );

					// TODO: fix folder tree state model so a selected folder always
					// exists
					if ( _.isUndefined( folder ) ) folder = new wickedfolders.models.Folder({
						id: '0',
					 	type: 'Wicked_Folders_Folder'
					});

					this.options.controller.state().set( 'wickedSelectedFolder', folder );

					if ( '0' == folder.id ) {
						this.options.props.unset( 'wicked_folder_type', { silent: true } );
						this.options.props.unset( 'wf_attachment_folders' );
					} else {
						// An AJAX request gets triggered anytime a property is changed
						// so set the first property silently
						this.options.props.set( 'wicked_folder_type', folder.get( 'type' ), { silent: true } );
						this.options.props.set( 'wf_attachment_folders', selected );
					}

					// Deactivate bulk select mode
					if ( ! this.options.controller.options.modal ) {
						this.options.controller.deactivateMode( 'select' ).activateMode( 'edit' );
					}

					this.expandToSelected();

				}, this );

				this.model.on( 'change:expanded', function( state ){

					var expanded = state.get( 'expanded' );

					this.options.controller.state().set( 'wickedExpandedFolders', expanded );

				}, this );

				this.options.props.on( 'change:wf_attachment_folders', function( props ){
					var selected = props.get( 'wf_attachment_folders' ) || '0';
					this.model.set( 'selected', selected );
				}, this );

				this.collection.on( 'add remove change:parent', this.render, this );

			},

			render: function(){
				var selected = this.options.controller.state().get( 'wickedSelectedFolder' ),
					expanded = this.options.controller.state().get( 'wickedExpandedFolders' );

				if ( ! selected ) selected = { id: '0' };
				if ( ! expanded ) expanded = [ '0' ];

				this.model.set( 'selected', selected.id );
				this.model.set( 'expanded', expanded );

				wickedfolders.views.FolderTree.prototype.render.call( this );

				this.initDragDrop();

				return this;

			},

			initDragDrop: function(){

				var view = this;

				this.$( '.wicked-folder-leaf.wicked-movable' ).not( '[data-folder-id="0"]' ).draggable( {
					revert: 'invalid',
					helper: 'clone'
				} );

				this.$( '.wicked-tree [data-folder-id="0"] .wicked-folder' ).droppable( {
					hoverClass: 'wicked-drop-hover',
					accept: function( draggable ){

						// TODO: prevent attachments from being moved into folder they're already in

						var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' ),
							folder = view.collection.get( view.model.get( 'selected' ) ),
							accept = false;

						if ( draggable.hasClass( 'wicked-folder' ) || draggable.hasClass( 'wicked-folder-leaf' ) || draggable.hasClass( 'attachment' ) ) {
							accept = true;
						}

						if ( draggable.hasClass( 'wicked-folder-leaf' ) ) {
							var parent = draggable.parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );
							// Don't allow folders to be moved to the folder they're already in
							if ( destinationFolderId == parent ) {
								accept = false;
							}
						}

						// For now, prevent attachments from being dragged to the
						// root folder
						if ( draggable.hasClass( 'attachment' ) && '0' == destinationFolderId ) {
							accept = false;
						}

						// Prevent attachments from being drug into folder they're
						// already in
						if ( draggable.hasClass( 'attachment' ) && destinationFolderId == folder.id ) {
							accept = false;
						}

						return accept;
					},
					tolerance: 'pointer',
					drop: function( e, ui ) {

						// TODO: clean this up

						var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' ),
							allAttachments = wp.media.model.Attachments.all,
							currentFolderId = view.model.get( 'selected' ),
							currentFolder = view.collection.get( currentFolderId );

						if ( ui.helper.hasClass( 'wicked-drag-details' ) ) {
							var attachments = view.options.dragSelection,
								copy = e.shiftKey;

							// Loop through attachments being moved
							attachments.each( function( attachment ){
								// Get the attachent's current folders
								var folders = _.clone( attachment.get( 'wickedFolders' ) );
								// If we're not copying the attachment, remove
								// the attachment from the current folder
								if ( ! copy ) folders = _.without( folders, currentFolderId );
								// Add the destination folder
								folders = _.union( folders, [ destinationFolderId ] );
								// Update the attachment's folders
								attachment.set( 'wickedFolders', folders );
							});

							view.options.browser.flashItemsMovedFeedback();

							// Update the attachment browser display
							view.options.browser.filterAttachments();

							// Clear the selection in normal folders to prevent
							// the same selection from inadvertently being
							// dragged again
							if ( 'Wicked_Folders_Term_Folder' == currentFolder.get( 'type' ) ) {
								view.options.browser.options.selection.reset();
							}

							// Move or copy the selected attachments
							// TODO: error handling?
							$.ajax(
								ajaxurl,
								{
									data: {
										// TODO: add nonce
										'action':                   'wicked_folders_move_object',
										'object_type':              'post',
										'object_id':                attachments.pluck( 'id' ),
										'destination_object_id':    destinationFolderId,
										// Omitting the source folder will result in a copy on the back-end
										'source_folder_id':         copy ? false : currentFolderId
									},
									method: 'POST',
									dataType: 'json',
									success: function( data ) {
									},
									error: function( data ) {
									}
								}
							);

						} else {

							var objectId = $( ui.draggable ).attr( 'data-folder-id' ),
								folder = view.collection.get( objectId );

							folder.set( 'parent', destinationFolderId );
							folder.save();

						}

					}
				});

			}

		});

		wickedfolders.views.AttachmentsBrowserFolderPane = View.extend({
			className: 'wicked-folder-pane',

			initialize: function(){

				// TODO: use a template
				this.$el.append( '<div class="wicked-folder-pane-toolbar-container" /> ' );
				this.$el.append( '<div class="wicked-folder-details-container" /> ' );
				this.$el.append( '<div class="wicked-folder-tree-container" /> ' );

				this.toolbar = new wickedfolders.views.AttachmentsBrowserFolderPaneToolbar({
					parent:		this,
					collection:	this.collection,
					state:		this.options.state
				});

				this.folderTree = new wickedfolders.views.AttachmentsBrowserFolderTree({
					collection: this.collection,
					model:		this.options.state,
					controller: this.options.controller,
					props: 		this.options.props,
					browser:	this.options.browser,
					dragSelection: this.options.dragSelection
				});

				this.createFolderDetails();

				this.$( '.wicked-folder-pane-toolbar-container' ).append( this.toolbar.render().el );
				this.$( '.wicked-folder-tree-container' ).append( this.folderTree.render().el );

				this.options.state.on( 'change:selected', this.folderChanged, this );

			},

			folderChanged: function(){
				// TODO: figure out why 'selected' points to deleted folder in some instances
				// Example: Open modal, switch to Create Gallery, add folder,
				// switch to dynamic folder, switch back to newly created folder,
				// delete folder...selected is still pointing to deleted folder
				var id = this.options.state.get( 'selected' )
					folder = this.collection.get( id ),
					mode = this.folderDetails.options.mode;

				if ( ! _.isUndefined( folder ) && 'Wicked_Folders_Term_Folder' == folder.get( 'type' ) ) {
					// Only regenerate the view if we're not in add mode
					if ( 'edit' == mode ) {
						this.createFolderDetails({
							mode: mode
						});
					}
				} else {
					this.folderDetails.$el.hide();
				}

			},

			addFolder: function(){
				// TODO: disable add folder button instead while in add mode
				// Don't do anything if the folder details view is already in
				// add mode and visible
				if ( 'add' == this.folderDetails.options.mode && this.folderDetails.$el.is( ':visible' ) ) return;

				var id = this.options.state.get( 'selected' ),
					folder = this.collection.get( id ),
					parent = '0';

				if ( 'Wicked_Folders_Term_Folder' == folder.get( 'type' ) ) {
					parent = folder.id;
				}

				this.createFolderDetails({
					mode: 	'add',
					model: 	new wickedfolders.models.Folder({
						postType: 	'attachment',
						taxonomy: 	'wf_attachment_folders',
						parent: 	parent
					})
				});

				this.folderDetails.$el.show();
				this.folderDetails.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();


			},

			editFolder: function(){

				this.createFolderDetails({
					mode: 'edit'
				});

				this.folderDetails.$el.show();
				this.folderDetails.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();
			},

			deleteFolder: function(){
				this.createFolderDetails({
					mode: 'delete'
				});
				this.folderDetails.$el.show();
			},

			expandAll: function(){
				var ids = this.collection.pluck( 'id' );
				this.folderTree.model.set( 'expanded', ids );
			},

			collapseAll: function(){
				this.folderTree.model.set( 'expanded', [ '0' ] );
			},

			createFolderDetails: function( args ){

				var id = this.options.state.get( 'selected' ),
					folder = this.collection.get( id )
					args = args || {},
					visible = false;

				_.defaults( args, {
					controller: this.options.controller,
					collection:	this.collection,
					state:		this.options.state,
					model:		folder
				} );

				if ( ! _.isUndefined( this.folderDetails ) ) {
					visible = this.folderDetails.$el.is( ':visible' );
					this.folderDetails.remove();
				}

				this.folderDetails = new wickedfolders.views.AttachmentsBrowserFolderDetails( args );

				this.$( '.wicked-folder-details-container' ).append( this.folderDetails.render().el );

				if ( ! visible ) this.folderDetails.$el.hide();

			}

		});

		wickedfolders.views.AttachmentsBrowserFolderPaneToolbar = View.extend({
			tagName: 	'ul',
			className: 	'wicked-folder-pane-toolbar',
			events: {
				'click a': 						'clickLink',
				'click .wicked-add-folder': 	'addFolder',
				'click .wicked-edit-folder': 	'editFolder',
				'click .wicked-delete-folder': 	'deleteFolder',
				'click .wicked-expand-all': 	'expandAll',
				'click .wicked-collapse-all': 	'collapseAll',
			},

			initialize: function(){

				var l10n = wickedFoldersL10n;

				this.$el.append( '<li><a class="wicked-add-folder" href="#" title="' + l10n.addNewFolderLink + '"><span class="screen-reader-text">' + l10n.addNewFolderLink + '</span></a></li>' );
				this.$el.append( '<li><a class="wicked-edit-folder" href="#" title="' + l10n.editFolderLink + '"><span class="screen-reader-text">' + l10n.editFolderLink + '</span></a></li>' );
				this.$el.append( '<li><a class="wicked-delete-folder" href="#" title="' + l10n.deleteFolderLink + '"><span class="screen-reader-text">' + l10n.deleteFolderLink + '</span></a></li>' );
				this.$el.append( '<li><a class="wicked-expand-all" href="#" title="' + l10n.expandAllFoldersLink + '"><span class="screen-reader-text">' + l10n.expandAllFoldersLink + '</span></a></li>' );
				this.$el.append( '<li><a class="wicked-collapse-all" href="#" title="' + l10n.collapseAllFoldersLink + '"><span class="screen-reader-text">' + l10n.collapseAllFoldersLink + '</span></a></li>' );

				this.options.state.on( 'change:selected', this.onFolderChanged, this );

				this.onFolderChanged();

			},

			clickLink: function( e ){
				e.preventDefault();
			},

			addFolder: function(){
				this.options.parent.addFolder();
			},

			editFolder: function( e ){
				if ( $( e.currentTarget ).hasClass( 'wicked-disabled' ) ) return;
				this.options.parent.editFolder();
			},

			deleteFolder: function( e ){
				if ( $( e.currentTarget ).hasClass( 'wicked-disabled' ) ) return;
				this.options.parent.deleteFolder();
			},

			expandAll: function(){
				this.options.parent.expandAll();
			},

			collapseAll: function(){
				this.options.parent.collapseAll();
			},

			onFolderChanged: function(){

				var id = this.options.state.get( 'selected' ),
					folder = this.collection.get( id );

				// TODO: fix folder tree state model so a selected folder always
				// exists
				if ( _.isUndefined( folder ) ) folder = new wickedfolders.models.Folder();

				this.$( 'a' ).removeClass( 'wicked-disabled' );

				if ( 'Wicked_Folders_Term_Folder' != folder.get( 'type' ) ) {
					this.$( '.wicked-edit-folder' ).addClass( 'wicked-disabled' );
					this.$( '.wicked-delete-folder' ).addClass( 'wicked-disabled' );
				}

			}

		});

		wickedfolders.views.AttachmentsBrowserFolderDetails = View.extend({
			className: 'wicked-folder-details',
			events: {
				'keyup input': 			'keyup',
				'keydown input': 		'keydown',
				'blur input': 			'setSaveButtonState',
				'click .wicked-save': 	'save',
				'click .wicked-delete': 'delete',
				'click .wicked-cancel': 'cancel',
				'click .wicked-close': 	'cancel'
			},
			template: _.template( $( '#tmpl-wicked-attachment-browser-folder-details' ).html() ),

			initialize: function(){

				_.defaults( this.options, {
					mode: 'add'
				} );

				if ( _.isUndefined( this.model ) ) {
					this.model = new wickedfolders.models.Folder({
						postType: 'attachment',
						taxonomy: 'wf_attachment_folders'
					});
				}

				this.folderSelect = new wickedfolders.views.FolderSelect({
					collection: this.collection,
					selected:	this.model.get( 'parent' )
				});

				this.listenTo( this.model, 'change:parent', this.folderParentChanged );

			},

			remove: function(){
				this.folderSelect.remove();
				View.prototype.remove.apply(this, arguments);
			},

			render: function(){

				var mode = this.options.mode,
					title = wickedFoldersL10n.editFolderLink,
					saveButtonLabel = wickedFoldersL10n.save;

				if ( 'add' == mode ) {
	                title = wickedFoldersL10n.addNewFolderLink;
	            }

	            if ( 'delete' == mode ) {
	                title           = wickedFoldersL10n.deleteFolderLink;
	                saveButtonLabel = wickedFoldersL10n.delete;
	            }

				var html = this.template({
					mode: 						this.options.mode,
					title: 						title,
					folderName:                 this.model.get( 'name' ),
					saveButtonLabel:            saveButtonLabel,
					deleteFolderConfirmation: 	wickedFoldersL10n.deleteFolderConfirmation
				});

				this.folderSelect.options.selected = this.model.get( 'parent' );

				this.$el.html( html );

				this.$( '.wicked-folder-parent' ).html( this.folderSelect.render().el );

				this.setSaveButtonState();

				return this;
			},

			keyup: function( e ){
				// Escape button
				if ( 27 == e.which ) this.$el.hide();
				this.setSaveButtonState();
			},

			keydown: function( e ) {
				// Enter key
				if ( 13 == e.which && this.$( '[name="wicked_folder_name"]' ).val().length > 0 ) {
					this.save();
				}
			},

			cancel: function( e ) {
				e.preventDefault();
				this.$el.hide();
			},

			save: function(){

				var view = this,
					parent = this.model.get( 'parent' );

				view.clearMessages();
				view.setBusy( true );

				if ( 'delete' == this.options.mode ) {
					//this.model.set( '_actionOverride', 'wicked_folders_delete_folder' );
					this.model.set( '_methodOverride', 'DELETE' );
	                this.model.destroy( {
						wait: true,
	                    success: function( model, response, options ){
							// Move the deleted folder's children to it's parent
							var children = view.collection.where( { parent: model.id } );

							if ( children.length ) {
								_.each( children, function( child ){
									// Keep silent to prevent unnecessary re-renders
									// by views monitoring the collection
									child.set( 'parent', parent, { silent: true } );
								} );
								// Trigger an event so that views monitoring the
								// collection will re-render
								view.collection.trigger( 'remove', model, {} );
							}
							view.options.state.set( 'selected', parent );
							view.setBusy( false );
							view.$el.hide();
	                    }
	                } );
	            } else {
					view.model.set( {
						name:   this.$( '[name="wicked_folder_name"]' ).val(),
						parent: this.$( '[name="wicked_folder_parent"]' ).val()
					} );
					this.model.save( {}, {
						success: function( model, response, options ){
							if ( 'add' == view.options.mode ) {
								view.collection.add( model );
								view.model = new wickedfolders.models.Folder({
									postType: 	'attachment',
									taxonomy: 	'wf_attachment_folders',
									parent:		model.get( 'parent' )
								});
								view.render();
								// TODO: l10n
								view.flashMessage( 'Folder added.' );
								view.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();
							}
							view.setSaveButtonState();
							view.setBusy( false );
							//if ( 'edit' == view.options.mode && view.options.controller.state().frame.options.modal ) {
							if ( 'edit' == view.options.mode ) {
								view.$el.hide();
							}
						},
						error: function( model, response, options ){
							var message = 'Error saving folder.';

							if ( _.has( response, 'responseJSON' ) ) {
								message = response.responseJSON.message;
							} else if ( _.has( response, 'statusText' ) ) {
								message = response.statusText;
							}

							view.setErrorMessage( message );
							view.setSaveButtonState();
							view.setBusy( false );
						}
					} );
				}

			},

			setSaveButtonState: function(){

				var disabled = false;

				if ( 'delete' != this.options.mode ) {
					if ( this.$( '[name="wicked_folder_name"]' ).val().length < 1 ) {
						disabled = true;
					}
				}

				this.$( '.wicked-save' ).prop( 'disabled', disabled );

			},

			setBusy: function( isBusy ){
				if ( isBusy ) {
					this.$( '.wicked-spinner' ).css( 'display', 'inline-block' );
					this.$( '[name="wicked_folder_name"]' ).prop( 'disabled', true );
					this.$( '[name="wicked_folder_parent"]' ).prop( 'disabled', true );
					this.$( '.wicked-save' ).prop( 'disabled', true );
				} else {
					this.$( '.wicked-spinner' ).hide();
					this.$( '[name="wicked_folder_name"]' ).prop( 'disabled', false );
					this.$( '[name="wicked_folder_parent"]' ).prop( 'disabled', false );
					this.setSaveButtonState();
				}
			},

			clearMessages: function(){
				this.$( '.wicked-messages' ).removeClass( 'wicked-errors wicked-success' ).empty().hide();
			},

			setErrorMessage: function( message ){
				this.$( '.wicked-messages' ).addClass( 'wicked-errors' ).text( message ).show();
			},

			flashMessage: function( message ){
				var view = this;
				this.$( '.wicked-messages' ).addClass( 'wicked-success' ).text( message ).show();
				setTimeout( function(){
					view.$( '.wicked-messages' ).fadeOut();
				}, 1000 );
			},

			folderParentChanged: function( folder ){
				// Model change event will trigger folder select view to re-render
				// so just update the view's selected option
				this.folderSelect.options.selected = this.model.get( 'parent' );
			}

		});

		// TODO: namespace everything

		var Folder = wickedfolders.models.Folder,
			FolderTree = wickedfolders.views.FolderTree,
			FolderTreeState = wickedfolders.models.FolderTreeState,
			FolderCollection = wickedfolders.collections.Folders,
			UploaderInline = wp.media.view.UploaderInline,
			Attachments = wp.media.view.Attachments,
			AttachmentsBrowser = wp.media.view.AttachmentsBrowser,
			AttachmentsBrowserDragDetails = wickedfolders.views.AttachmentsBrowserDragDetails,
			AttachmentsBrowserFolderPane = wickedfolders.views.AttachmentsBrowserFolderPane,
			AttachmentsBrowserFolderTree = wickedfolders.views.AttachmentsBrowserFolderTree,
			AttachmentsBrowserFolderTreeToggle = wickedfolders.views.AttachmentsBrowserFolderTreeToggle,
			AttachmentsBrowserItemsMovedFeedback = wickedfolders.views.AttachmentsBrowserItemsMovedFeedback;

		// TODO: move dragSelection out of global scope
		var folders = new FolderCollection(),
			dragSelection = new Backbone.Collection(),
			persistentState = new Backbone.Model({ selectedFolder: false, expandedFolders: false }),
			allFolders = folders;

		_.each( WickedFoldersProData.folders, function( folder, index ) {
			folders.add( new Folder({
				id: 		folder.id,
				name: 		folder.name,
				parent: 	folder.parent,
				type: 		folder.type,
				postType: 	'attachment',
				taxonomy:	'wf_attachment_folders'
			}) );
		});

		// Extend WordPress attachment browser
		wp.media.view.AttachmentsBrowser = AttachmentsBrowser.extend({

			initialize: function() {

				// Set persistent state before initialization to avoid issue
				// with change event firing too early in inline uploader before
				// uploader is fully ready
				if ( WickedFoldersProData.persistFolderState ) {
					if ( persistentState.get( 'selectedFolder' ) ) {
						this.controller.state().set( 'wickedSelectedFolder', persistentState.get( 'selectedFolder' ) );
					}
					if ( persistentState.get( 'expandedFolders' ) ) {
						this.controller.state().set( 'wickedExpandedFolders', persistentState.get( 'expandedFolders' ) );
					}
				}

				AttachmentsBrowser.prototype.initialize.apply( this, arguments );

				this.filterAttachmentsDebounced = _.debounce( this.filterAttachments, 100 );

				/*
				this.model.frame.on( 'open', function(){
					var views = this.views.get();
					_.each( views, function( view ){
						if ( view.$el.hasClass( 'wicked-folder-pane') ) {
						}
					});
				}, this );
				*/

				this.createFolderPane();

				this.createFolderPaneToggle();

				this.createItemsMovedFeedback();

				this.controller.state().on( 'change:wickedSelectedFolder', function(){
					persistentState.set( 'selectedFolder', this.controller.state().get( 'wickedSelectedFolder' ) );
				}, this );

				this.controller.state().on( 'change:wickedExpandedFolders', function(){
					persistentState.set( 'expandedFolders', this.controller.state().get( 'wickedExpandedFolders' ) );
				}, this );

				this.collection.props.on( 'change:query', this.filterAttachments, this );
				this.controller.state().get( 'library' ).on( 'reset', this.filterAttachments, this );
				wp.media.model.Attachments.all.on( 'change:uploading', this.filterAttachmentsDebounced, this );
				wp.media.model.Attachments.all.on( 'add remove', this.filterAttachmentsDebounced, this );

			},

			createFolderPane: function(){

				var folderTreeState = new FolderTreeState();

				this.views.add( new AttachmentsBrowserFolderPane({
					collection: folders,
					state:		folderTreeState,
					controller: this.controller,
					props: 		this.collection.props,
					browser:	this,
					dragSelection: dragSelection
				}) );

				if ( 'gallery-edit' == this.controller.state().id ) {
					this.$el.removeClass( 'wicked-folder-pane-enabled' );
				} else {
					this.$el.addClass( 'wicked-folder-pane-enabled' );
				}

				if ( WickedFoldersProData.persistFolderState ) {
					persistentState.on( 'change:selectedFolder', function( state ){
						folderTreeState.set( 'selected', state.get( 'selectedFolder' ).id );
					});
					persistentState.on( 'change:expandedFolders', function( state ){
						folderTreeState.set( 'expanded', state.get( 'expandedFolders' ) );
					});
				}

			},

			createFolderPaneToggle: function(){

				if ( this.controller.options.modal ) {
					this.views.add( new AttachmentsBrowserFolderTreeToggle({
						controller: this.controller,
						browser: 	this
					}) );
				} else {
					this.toolbar.set( 'wickedFolderToggle', new AttachmentsBrowserFolderTreeToggle({
						controller: this.controller,
						browser: 	this,
						priority: 	-85
					}).render() );
				}

			},

			createItemsMovedFeedback: function(){
				this.views.add( new AttachmentsBrowserItemsMovedFeedback({
					controller: this.controller,
					browser: 	this
				}) );
			},

			createToolbar: function() {

				AttachmentsBrowser.prototype.createToolbar.call( this );

				/*if ( ! this.controller.options.modal ) {
					this.toolbar.set( 'MediaLibraryTaxonomyFilter', new WickedFoldersAttachmentFilter({
						controller: this.controller,
						model:      this.collection.props,
						priority: 	-75
					}).render() );
				}*/

			},

			createSingle: function() {
				var sidebar = this.sidebar,
					single = this.options.selection.single();

				AttachmentsBrowser.prototype.createSingle.call( this );

				sidebar.set( 'wicked-folders', new wickedfolders.views.AttachmentFolders({
					controller: 			this.controller,
					rerenderOnModelChange: 	true,
					model: 					single,
					priority:   			120,
					wickedFolders: 			folders,
					browser: 				this
				}) );

			},

			disposeSingle: function() {
				var sidebar = this.sidebar;

				AttachmentsBrowser.prototype.disposeSingle.call( this );

				sidebar.unset( 'wicked-folders' );

			},

			flashItemsMovedFeedback: function(){
				var $feedback = this.$( '.wicked-items-moved-feedback' );
				// TODO: might need to debounce instead...
				$feedback.show().delay( 750 ).fadeOut( 500 );

			},

			filterAttachments: function(){

				var state = this.controller.state(),
					attachments = this.controller.state().get( 'library' ),
					allAttachments = wp.media.model.Attachments.all,
					folder = this.controller.state().get( 'wickedSelectedFolder' ),
					folders = [],
					remove = []
					add = [],
					year = false
					month = false;

				if ( ! folder ) return;

				// Don't filter gallery edit state
				if ( 'gallery-edit' == state.id ) return;

				// Only filter term folders (except for the unassinged dynamic folder)
				if ( 'Wicked_Folders_Term_Folder' != folder.get( 'type' ) && 'unassigned_dynamic_folder' != folder.id ) return;

				// Not entirely clear on how the order filter works when filtering
				// by the native WordPress date filter but it prevents items that
				// have recently been drug to the folder from showing up so I'm
				// deleting it here. Logic has been added below to filter
				// attachments by date so that the date filter still works.
				// See Query.initialize for order filter function.
				delete attachments.mirroring.filters.order;

				if ( ! _.isUndefined( attachments.mirroring.props ) ) {
					year = attachments.mirroring.props.get( 'year' );
					month = attachments.mirroring.props.get( 'monthnum' );
				}

				allAttachments.each( function( attachment ){
					// Respect the collection's filters (e.g. date, mime, etc.)
					var valid = true;
					if ( attachments.mirroring ) {
						valid = attachments.mirroring.validator( attachment );
					}
					// Since we deleted the order filter earlier, we need to
					// filter by date so the WordPress date filter still works
					if ( year && month ) {
						if ( ! ( year == attachment.get( 'date' ).getFullYear() && month == ( attachment.get( 'date' ).getMonth() + 1 ) ) ) {
							valid = false;
						}
					}
					// Include all items in 'all folders'
					if ( '0' == folder.id ) {
						add.push( attachment );
					} else if ( 'unassigned_dynamic_folder' == folder.id ) {
						if ( _.size( attachment.get( 'wickedFolders' ) ) > 0 ) {
							remove.push( attachment );
						} else {
							add.push( attachment );
						}
					} else {
						var folders = attachment.get( 'wickedFolders' );

						// If include children is enabled, check if attachment
						// is assigned to any child folder as well
						if ( WickedFoldersProData.includeChildren ) {
							if ( _.isArray( folders ) ) {
								if ( folders.length ) {
									var descendants = allFolders.descendantIds( folder.id ),
										result = _.intersection( folders, descendants );

									if ( result.length ) {
										folders = folders.concat( [ folder.id ] );
									}
								}
							}
						}

						if ( folders.length ) {
							if ( -1 == folders.indexOf( folder.id ) ) {
								remove.push( attachment );
							} else if ( valid ) {
								add.push( attachment );
							}
						} else if ( attachment.get( 'uploading' ) ) {
							add.push( attachment );
						} else {
							remove.push( attachment );
						}
					}
				});

				attachments.remove( remove );
				attachments.add( add );

			}

		});

		// Extend WordPress Attachments view
		wp.media.view.Attachments = Attachments.extend({
			initialize: function(){

				Attachments.prototype.initialize.apply( this, arguments );

				// TODO: determine if this impacts performance and, if so,
				// find a more efficient approach
				this.collection.on( 'add', this.initDragDrop, this );

			},

			render: function(){

				Attachments.prototype.render.apply( this, arguments );

				this.initDragDrop();

			},

			initDragDrop: function(){

				var view = this,
					collection = this.collection,
					orderby = collection.props.get('orderby'),
					sortingEnabled = 'menuOrder' === orderby || ! collection.comparator;

				// Don't interfere when sorting the attachment selection is enabled
				if ( sortingEnabled ) return;

				this.$( '.attachment' ).draggable( {
					revert: 'invalid',
					cursor: 'default',
					delay: 100,
					cursorAt: {
						top: -5,
						left: -5
					},
					helper: function( e ){
						if ( view.options.selection.length > 1 ) {
							dragSelection.reset( view.options.selection.models );
							var selection = view.options.selection;
						} else {
							var id = $( e.currentTarget ).attr( 'data-id' ),
								attachment = view.collection.get( id );
								selection = new Backbone.Collection( attachment );
							dragSelection.reset( attachment );
						}
						var dragger = new AttachmentsBrowserDragDetails({
							collection: selection
						});
						return dragger.render().el;
					},
					start: function(){
						view.$el.addClass( 'wicked-dragging-attachment' );
					},
					stop: function(){
						view.$el.removeClass( 'wicked-dragging-attachment' );
					}
				} );

			}
		});

		// Extend WordPress inline uploader
		wp.media.view.UploaderInline = UploaderInline.extend({
			render: function(){

				UploaderInline.prototype.render.apply( this, arguments );

				var folder = this.options.controller.state().get( 'wickedSelectedFolder' ),
					folderId = '0';

				if ( WickedFoldersProData.syncUploadFolderDropdown && ! _.isUndefined( folder ) ) {
					folderId = folder.id;
				}

				var folderSelect = new wickedfolders.views.UploaderFolderSelect({
					el: 						this.$( '#wicked-upload-folder' ),
					controller: 				this.options.controller,
					collection:					folders,
					defaultText:				wickedFoldersL10n.assignToFolder,
					selected:					folderId,
					syncUploadFolderDropdown: 	WickedFoldersProData.syncUploadFolderDropdown
				});

				folderSelect.render();

			}
		});

		/*
		var frame = new wp.media.view.MediaFrame.Select({
			// Modal title
			title: 'Select profile background',

			// Enable/disable multiple select
			multiple: true,

			// Library WordPress query arguments.
			library: {
				order: 'ASC',

				// [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
				// 'id', 'post__in', 'menuOrder' ]
				orderby: 'title',

				// mime type. e.g. 'image', 'image/jpeg'
				//type: 'image',

				// Searches the attachment title.
				search: null,

				// Attached to a specific post (ID).
				uploadedTo: null
			},

			button: {
				text: 'Set profile background'
			}
		});
		frame.open();
		*/
	});
})(jQuery);
