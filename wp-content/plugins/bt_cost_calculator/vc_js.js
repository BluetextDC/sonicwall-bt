// Cost calculator
// author: Bold Themes - http://www.bold-themes.com/
// Date: 15 Nov, 2017
// web: http://www.bold-themes.com

console && console.log('admin_enqueue_js.js is loaded');


window.BTCCItem = vc.shortcode_view.extend({
	changeShortcodeParams: function ( model ) {
		model.get( "params" )[ "condition" ] = model.getParam( 'condition' ).replace( "<", "&lt;" ).replace ( ">", "&gt;" );
		window.BTCCItem.__super__.changeShortcodeParams.call( this, model );
	}
});


