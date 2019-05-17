<div id="wicked-object-folder-pane">
    <div class="wicked-resizer">
        <div class="wicked-splitter-handle ui-resizable-handle ui-resizable-e">
        </div>
    </div>
    <div class="wicked-content">
        <div class="wicked-title"><?php _e( 'Folders', 'wicked-folders' ); ?></div>
        <div class="wicked-toolbar-container"></div>
        <div class="wicked-folder-details-container"></div>
        <div class="wicked-folder-tree"></div>
    </div>
</div>

<script type="text/html" id="tmpl-wicked-folder-details">
    <header>
        <h2><%= title %></h2>
        <span class="wicked-spinner"></span>
        <a class="wicked-close" href="#" title="<?php _e( 'Close', 'wicked-folders' ); ?>"><span class="screen-reader-text"><?php _e( 'Close', 'wicked-folders' ); ?></span></a>
    </header>
    <div>
        <div class="wicked-messages wicked-errors"></div>
        <% if ( 'delete' == mode ) { %>
            <p><%= deleteFolderConfirmation %></p>
        <% } else { %>
            <div class="wicked-folder-name"><input type="text" name="wicked_folder_name" placeholder="<?php _e( 'Folder name', 'wicked-folders' ); ?>" value="<%= folderName %>" /></div>
            <div class="wicked-folder-parent"></div>
        <% } %>
        <% if ( 'edit' == mode ) { %>
            <p>
                <a class="wicked-clone-folder" href="#"><%= cloneFolderLink %></a>
                <span class="dashicons dashicons-editor-help" title="<%= cloneFolderTooltip %>"></span>
            </p>
        <% } %>
    </div>
    <footer>
        <a class="button wicked-cancel" href="#"><?php _e( 'Cancel', 'wicked-folders' ); ?></a>
        <button class="button-primary wicked-save" type="submit"><%= saveButtonLabel %></button>
    </footer>
</script>

<script type="text/html" id="tmpl-wicked-post-drag-details">
    <div class="items">
        <% posts.each( function( post ) { %>
            <div><%= post.get( 'title' ) %></div>
        <% } ); %>
    </div>
</script>

<script>
(function( $ ){
    $(function(){

        var FolderBrowserController = wickedfolders.models.FolderBrowserController,
            FolderCollection = wickedfolders.collections.Folders,
            ObjectFolderPaneController = wickedfolders.models.ObjectFolderPaneController,
            ObjectFolderPane = wickedfolders.views.ObjectFolderPane,
            FolderTree = wickedfolders.views.FolderTree,
            Folder = wickedfolders.models.Folder

        var folders = new FolderCollection(),
            folderData = <?php echo json_encode( $folders ); ?>;

        Backbone.emulateHTTP = true;

        _.each( folderData, function( folder ){
            folders.add( new Folder({
                id:         folder.id,
                parent:     folder.parent,
                name:       folder.name,
                postType:   folder.post_type,
                taxonomy:   folder.taxonomy,
                type:       folder.type,
                lazy:       folder.lazy
            }) );
        });

        var controller = new ObjectFolderPaneController({
            expanded:               <?php echo json_encode( array_values( $state->expanded_folders ) ); ?>,
            postType:               '<?php echo $post_type; ?>',
            taxonomy:               '<?php echo $taxonomy; ?>',
            folder:                 folders.get( '<?php echo $active_folder_id; ?>' ),
            folders:                folders,
            screen:                 '<?php echo $screen->id; ?>',
            nonce:                  '<?php echo wp_create_nonce( 'wicked_folders_save_state' ); ?>',
            treePaneWidth:          <?php echo $state->tree_pane_width; ?>,
            //hideAssignedItems:      <?php echo ( int ) $state->hide_assigned_items; ?>,
            isSearch:               <?php echo empty( $_GET['s'] ) ? 'false' : 'true'; ?>,
            isFolderPaneVisible:    <?php echo $state->is_folder_pane_visible ? 'true' : 'false'; ?>
        });

        var pane = new ObjectFolderPane({
            el:     '#wicked-object-folder-pane',
            model:  controller
        });

        $( '#wp-admin-bar-wicked-folders-toggle-folder-pane > a' ).click( function(){
            if ( $( 'body' ).hasClass( 'wicked-object-folder-pane' ) ) {
                $( 'body' ).removeClass( 'wicked-object-folder-pane' );
                $( this ).text( '<?php _e( 'Show Folder Pane', 'wicked-folders' ); ?>' );
                controller.set( 'isFolderPaneVisible', false );
                $( '#wpcontent' ).css( 'paddingLeft', '' );
            } else {
                $( 'body' ).addClass( 'wicked-object-folder-pane' );
                $( this ).text( '<?php _e( 'Hide Folder Pane', 'wicked-folders' ); ?>' );
                controller.set( 'isFolderPaneVisible', true );
                pane.setWidth( controller.get( 'treePaneWidth' ) );
            }
            return false;
        } );

    });
})( jQuery );
</script>
