jQuery(function($){
    $(document).ready(function() {
        $('.plc-product-name').hide();
		$('.plc-product-type').hide();
		$('.plc-product-data-table').hide();
		$('.firmware-product-info-display').hide();

		$('#product-selector').on('change', function(){
			var prod = $(this).val();
			$('.plc-product-name').hide();
			$('.plc-product-type').hide();
			$('.plc-product-data-table').hide();
			if ( $(this).val() != ''){
				$('#' + prod).show();
				$('.' + prod).show();

				$('#product-type-holder').children().each(function(){
					var pth = $(this);
					if ( pth.css('display') == 'block' && pth.hasClass( 'software') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-software').show();
						$('.hardware-software-toggles').show();
						$('.firmware-product-info-display').hide();
						return false;
					}
					else if ( pth.css('display') == 'block' && pth.hasClass( 'hardware') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-hardware').show();
						$('.hardware-software-toggles').show();
						$('.firmware-product-info-display').hide();
						return false;
					}
					else if ( pth.css('display') == 'block' && pth.hasClass( 'firmware') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-firmware').show();
						$('.hardware-software-toggles').hide();
						$('.firmware-product-info-display').show();
						return false;
					}
				});
			}
        });
        
        $(document).on('click', '.plc-product-type a', function(e){
			e.preventDefault();
			$('.plc-product-data-table').hide();
			var table_id = $(this).attr('id');
			var table_type = $(this).html();
			table_type = table_type.toLowerCase();
			if ( table_type == 'firmware' ){
				$('.hardware-software-toggles').hide();
				$('.firmware-product-info-display').show();
			}
			else {
				$('.hardware-software-toggles').show();
				$('.firmware-product-info-display').hide();
			}

			// console.log( '#' + table_id + '-' + table_type );
			$('#' + table_id + '-' + table_type).show();

        });
        
        $('.has-tip').hover(function(){
			var tip_key = $(this).parent().attr('id');
			var ttip = $('#' + tip_key  + '-tooltip').html();
			$(this).parent().prepend('<div class="tooltip-holder">' + ttip + '</div>');
		}, function(){
			$('.tooltip-holder').remove();
        });
        
    });
});