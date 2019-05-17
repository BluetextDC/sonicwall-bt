(function() {

	tinymce.PluginManager.add('tinyWYM_hidden', function(editor, url) {

		/**
		 * Toggle tiny-wym editor class for users who want hidden by default
		 *
		 * The main plugin adds tiny-wym class on load, this removes it.
		 *
		 */
		editor.on( 'PreInit', function() {

			// Removes tiny-wym class from the editor
			editor.dom.removeClass( editor.$( 'html' ), 'tiny-wym' );

		});

	});

})();