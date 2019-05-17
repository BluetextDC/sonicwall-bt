/** 
*	javascript library VEX is in use for creating message boxes
**/
 
jQuery('.publishing-action').ready(function($){
	// Multiple variables are used to make single line short and readable.
	var dppmcBtName = duplicate_ppmc_ENG.dppmc_bt_name,
		menuName	  = $("[name='menu-name']").val(),
		menuLink	  = 'admin.php?action=duplicate_ppmc_menu_maker',
		menu_clone 	  = menuLink+'&name='+menuName,
		btClass	  = 'button button-primary button-large';

	// Add 'Duplicate' button next to menu 'Save' button.
	if ( '0' === ( duplicate_ppmc_ENG.enable_in_menu ) ) {
		$( '.publishing-action' ).
		append('<a id="DPPM_menu" name="DPPM_menu" PPMC_menu_id="'+menuName+'" class="'+
		btClass+'" >'+dppmcBtName+'</a>');
		
	}
	
	
});
	
// Make sure that document(Webpage) is ready to fire the jQuery script.
jQuery(document).ready(function( $ ){

	/*	Get the option that user has selected for cloning	*/

	$( '[name="duplicate_ppmc_item_no"]' ).bind( 'input',function() {

		var	count	= $(this).val();
		// Avoid getting zero or less than zero as an input for post duplication.
		if ( count < 1 ) {
			$(this).val( 1 );
		} else if ( count > 5 ) {
			vex.dialog.alert({unsafeMessage: 'Can not duplicate more than 5 in a single operation',
							className: 'vex-theme-os'
							});
			$(this).val( 5 );
		}
		// Get the input value again in-case it is changed by above validation process.
		count	= $(this).val();
		var id		 = $(this).attr( 'id' ),
			idn		 = id.replace( 'duplicate_ppmc_item_no','' ),
			adminLink= 'admin.php?action=duplicate_ppmc_post_as_draft&copies=',
			btlink	 = adminLink+count+'&post='+idn;
		$( 'a.'+id ).attr( 'href',btlink );
		
	});
	
	jQuery('a#DPPM_menu').on( 'click', function(e){
		e.preventDefault();
		DPPM_menu_cloning( jQuery( this ) )
	});
	
} // end of ready(function(){}).

); // end of jQuery document.ready(){}

/* Ajax function for duplicating menu */
function DPPM_menu_cloning( context ){
	
	var menu_name	=	jQuery( context ).attr('ppmc_menu_id');

	jQuery.ajax({
		url:duplicate_ppmc_ENG.ajax_url,
		type:'post',
		dataType:'json',
		data:{
			action:'duplicate_ppmc_menu_maker',
			name: menu_name
		},
		beforeSend:function(){
			var HTML_MESSAGE	=	'Duplicating <strong>"'+menu_name+'"</strong> menu.<br/>Be patience while processing complete.';
			
			HTML_MESSAGE	+=	'It may take a while depends on data and server size.<center><div class="DPPM_loader"></div></center>';
			
			vex.dialog.alert({ unsafeMessage: HTML_MESSAGE,
							className: 'vex-theme-os',
							buttons:false
							});			
		}
	}).success(function( response ){
		
		vex.closeAll();
		
		var message = "";
		
		if( response.error ){
			message	= response.error;
		}else{
			message = '<strong>Finished creating duplicate menu.</strong>';
		}
		vex.dialog.alert({ unsafeMessage: message,
						className: 'vex-theme-os',
						callback: function(){
							if(response.menu_id){
								//redirect to new menu page if ID available.
								window.location= "nav-menus.php?action=edit&menu="+response.menu_id;
							}
						}
					});

	});

}

/* Gather data after button click for duplicating post/page */
jQuery('a#Btdppmc').on('click', function(e){
	e.preventDefault();
	console.log('you clicked');
	var $this		=	jQuery( this );
	
	var post_id		=	jQuery( $this ).attr('ppmc_post_id');
	
	var copies		=	jQuery( $this ).parent().find( '#duplicate_ppmc_item_no'+post_id ).val();
	
	DPPM_add_ajax_to_duplicate( post_id, copies );
	
});

/* ajax function for duplicating page/post */
function DPPM_add_ajax_to_duplicate( id, carbon ){
	
	jQuery.ajax({
		url:duplicate_ppmc_ENG.ajax_url,
		type:'post',
		data:{
			action:'duplicate_ppmc',
			post: id,
			copies: carbon
		},
		beforeSend:function(){

			var	HTML_MESSAGE	=	'Duplicating post id: '+id+'. Creating  '+carbon+' more copy/copies.'+'<br/>Be patience while processing complete.';
				HTML_MESSAGE	+=	'It may take a while depends on data and server size.<center><div class="DPPM_loader"></div></center>';
				
			vex.dialog.alert({ unsafeMessage: HTML_MESSAGE,
							className: 'vex-theme-os',
							buttons:false
							});			
		}
	}).success(function( response ){
		
		vex.closeAll();
		var isEditPage	=	(window.location.href).search('edit.php');
			
			vex.dialog.alert({ unsafeMessage: '<strong>Finish duplicating the post/page.</strong>',
							className: 'vex-theme-os',
							callback: function(){
								if( isEditPage >0 ){
									document.location.reload();
								}
							}
							});
		

	});

}