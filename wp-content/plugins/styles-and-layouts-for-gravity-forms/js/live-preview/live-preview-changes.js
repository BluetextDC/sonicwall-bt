jQuery(document).ready( function( $ ) {
	

var formId =gf_stla_localize_current_form.formId;
var gradientColor1 = '';
var gradientColor2 = '';
var gradientDirection = 'left,';
var gradientDirectionSafari = 'right,';
var gradientDirectionStandard = 'to left,';
var backgroundOpacity = '';
var backgroundColor ='';
var backgroundType ='';
var gradientStandard = '';
var gradientOpera = '';
var gradientFirefox = '';
var gradientSafari = '';
var backgroundImage = '';

//get intial value of background type selected by user
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-type]', function( control ) {
                backgroundType = control.get();
                
            }); 

//get initial saved value of background opacity
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-opacity]', function( control ) {
                backgroundOpacity = control.get();
                
            }); 
//get intial background image
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-image]', function( control ) {
                backgroundImage = control.get();
                
            }); 

//get initial saved value of background color
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-color]', function( control ) {

                hexColor = control.get();
                if(hexColor){
                  hexColor = hexColor.match(/[^#]\S+/g);
                  backgroundColor = hexToRgbNew(hexColor[0]);
                  backgroundColor = updateBackgroundOpacity(backgroundOpacity, backgroundColor);
                }
            }); 


//get initial saved value of Gradient color 1
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-color-1]', function( control ) {
                color = control.get();
                h = color/360;
                 gradientColor1 = hslTorgb(h)
                 gradientColor1 = updateBackgroundOpacity(backgroundOpacity, gradientColor1);

            }); 

//get initial saved value of Gradient color 2
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-color-2]', function( control ) {
                color = control.get();
                h = color/360;
                 gradientColor2 = hslTorgb(h);
                 gradientColor2 = updateBackgroundOpacity(backgroundOpacity, gradientColor2);
            }); 

//get initial saved value of Gradient direction and save it to different variables 
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-direction]', function( control ) {
                direction = control.get();
                  switch(direction){
                  case 'left':
                      gradientDirection = 'right,';
                      gradientDirectionSafari = 'left,';
                      gradientDirectionStandard = 'to right,';
                      break;
                   case 'diagonal':
                      gradientDirection = 'bottom right,';
                      gradientDirectionSafari = 'left top,';
                      gradientDirectionStandard = 'to bottom right,'; 
                      break;
                    default:
                      gradientDirection = '';
                      gradientDirectionSafari = '';
                      gradientDirectionStandard = ''; 
                }
            }); 

//Save new value of gradient direction in different variables
function saveGradientDirection(direction){
      switch(direction){
                  case 'left':
                      gradientDirection = 'right,';
                      gradientDirectionSafari = 'left,';
                      gradientDirectionStandard = 'to right,';
                      break;
                   case 'diagonal':
                      gradientDirection = 'bottom right,';
                      gradientDirectionSafari = 'left top,';
                      gradientDirectionStandard = 'to bottom right,'; 
                      break;
                    default:
                      gradientDirection = '';
                      gradientDirectionSafari = '';
                      gradientDirectionStandard = ''; 
                }
                  
}


/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes h, s, and l are contained in the set [0, 1] and
 * returns r, g, and b in the set [0, 255].
 *
 * @param   {number}  h       The hue
 * @param   {number}  s       The saturation
 * @param   {number}  l       The lightness
 * @return  {Array}           The RGB representation


 */



function hslTorgb(h, s=0.5, l=0.4){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        var hue2rgb = function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return 'rgb('+Math.round(r * 255)+', '+Math.round(g * 255)+', '+ Math.round(b * 255)+')';
}

//function to convert hex to rgb
function hexToRgbNew(hex) {
  var arrBuff = new ArrayBuffer(4);
  var vw = new DataView(arrBuff);
  vw.setUint32(0,parseInt(hex, 16),false);
  var arrByte = new Uint8Array(arrBuff);

  return "rgb("+arrByte[1] + "," + arrByte[2] + "," + arrByte[3]+")";
}

//Add opacity to rgb

function updateBackgroundOpacity(opacity, color){
  
  color = color.replace(')','');
  color = color.split(',');

  var rgbLength = color.length;
  if(rgbLength == 3){
    color.push(opacity);
  }
  else{
    color[3] = opacity;
  }
  color[0] = color[0].replace('rgba(', '');
  color[0] = color[0].replace('rgb(', '');
  color = "rgba("+color[0]+","+color[1]+","+color[2]+","+color[3]+")";
  
  return color;
}

//Set Gradient color properites for all browsers

function setGradientProperties(){

gradientStandard = 'linear-gradient('+gradientDirectionStandard.concat(gradientColor1)+','+ gradientColor2+')';
gradientOpera = '-o-linear-gradient('+gradientDirection.concat(gradientColor1)+','+ gradientColor2+')';
gradientFirefox = '-moz-linear-gradient('+gradientDirection.concat(gradientColor1)+','+ gradientColor2+')';
gradientSafari = '-webkit-linear-gradient('+gradientDirectionSafari.concat(gradientColor1)+','+ gradientColor2+')';
}



/**
 * Not compatible with wordpress 4.7 onwards
 * using wp_localize script from now onwards
 */
// (window.onpopstate = function () {
//     var match,
//         pl     = /\+/g,  // Regex for replacing addition symbol with a space
//         search = /([^&=]+)=?([^&]*)/g,
//         decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
//         query  = window.location.search.substring(1);

//     formId = {};
//     while (match = search.exec(query))
//        formId[decode(match[1])] = decode(match[2]);
// })();
 
//Check if px is added, if not then add automatically (not for margins and paddings)
function addPxToValue(to){

  if(!(/\D/.test(to))){
    to = to+'px';
  }
     return to;
}

//Check if px is added, if not then add automatically  for margins and paddings
function addPxToMarginPadding(to){
var marginPadding = to.split(" ");


var arrayLength = marginPadding.length;
var newMarginPadding = '';
for (var i = 0; i < arrayLength; i++) {
  if(!(/\D/.test(marginPadding[i]))){
    marginPadding[i] = marginPadding[i]+'px';
  }
   newMarginPadding +=marginPadding[i]+' ';

}
     return newMarginPadding;
}

function addGoogleFont(FontName) {
var fontPlus='';
    FontName=FontName.split(" ");
    if($.isArray(FontName)){
      fontPlus = FontName[0];
      for(var i=1; i<FontName.length; i++){
       fontPlus = fontPlus +'+'+FontName[i];
      }

    }

    $("<link href='https://fonts.googleapis.com/css?family=" + fontPlus + "' rel='stylesheet' type='text/css'>").appendTo("head");
}

//function to set bold/italic/uppercase and underline values

function setFontStyles( value ){
	var value = value.split('|');
	var fontStyles = {
		"font-weight": "normal",
		"font-style": "normal",
		"text-transform": "none",
		"text-decoration": "none"
	};
	value.map( function(currentValue){
		// if( fontStyles !== ''){
		// 	fontStyles = fontStyles +',';
		// }
		switch (currentValue){
			case 'bold':
			fontStyles["font-weight"]= "bold";
			break;
			case 'italic':
			fontStyles["font-style"]= "italic";
			break;
			case 'uppercase':
			fontStyles["text-transform"]= "uppercase";
			break;
			case 'underline':
			fontStyles["text-decoration"]= "underline";
			break;
			default:
			break;
		}
	});
	return fontStyles;
}
//********************************* Form Wrapper *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-color]', function( value ) {
    value.bind( function( to ) {
		backgroundColor = '';

				hexColor = to;
				if( hexColor ){
					
				hexColor = hexColor.match(/[^#]\S+/g);
				backgroundColor = 'inherit';
				
					backgroundColor = hexToRgbNew(hexColor[0]);
				
                
				backgroundColor = updateBackgroundOpacity(backgroundOpacity, backgroundColor);
			}

                $( '#gform_wrapper_'+formId ).css( 'background-image','none' );
               // $( '#gform_wrapper_'+formId ).css( 'background','' );
			$( '#gform_wrapper_'+formId ).css( 'background',backgroundColor );
		
           // console.log(backgroundColor);
         } );
  } );

    wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-color-1]', function( value ) {
    value.bind( function( to ) {
              var h = to/360;
            gradientColor1 = hslTorgb(h);
            gradientColor1 = updateBackgroundOpacity(backgroundOpacity, gradientColor1);
             setGradientProperties();
            $( '#gform_wrapper_'+formId ).css( 'background', gradientStandard);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientOpera);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientFirefox);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientSafari);
         } );
  } );

      wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-color-2]', function( value ) {
    value.bind( function( to ) {
              var h = to/360;
            gradientColor2 = hslTorgb(h);
            gradientColor2 = updateBackgroundOpacity(backgroundOpacity, gradientColor2);
            setGradientProperties();
            $( '#gform_wrapper_'+formId ).css( 'background', gradientStandard);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientOpera);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientFirefox);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientSafari);
         } );
  } );

      wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][gradient-direction]', function( value ) {
    value.bind( function( to ) {
             var returnValue =  saveGradientDirection(to) ;
             setGradientProperties();
            $( '#gform_wrapper_'+formId ).css( 'background', gradientStandard);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientOpera);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientFirefox);
            $( '#gform_wrapper_'+formId ).css( 'background', gradientSafari);
         } );
      
  } );

       wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-opacity]', function( value ) {
    value.bind( function( to ) {

          backgroundOpacity = to;
          if(backgroundColor){
            backgroundColor = updateBackgroundOpacity(backgroundOpacity, backgroundColor);
          }
            gradientColor1 = updateBackgroundOpacity(backgroundOpacity, gradientColor1);
             gradientColor2 = updateBackgroundOpacity(backgroundOpacity, gradientColor2);
             setGradientProperties();

            if(backgroundType == 'gradient'){
              $( '#gform_wrapper_'+formId ).css( 'background-image','none' );
              $( '#gform_wrapper_'+formId ).css( 'background-color',backgroundColor );
              $( '#gform_wrapper_'+formId ).css( 'background', gradientStandard);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientOpera);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientFirefox);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientSafari);
              
             }
             if(backgroundType == 'color'){
              $( '#gform_wrapper_'+formId ).css( 'background-image','none' );
               // $( '#gform_wrapper_'+formId ).css( 'background', "none");
                $( '#gform_wrapper_'+formId ).css( 'background-color', backgroundColor);
             } 
         } );
  } );

      wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-type]', function( value ) {
    value.bind( function( to ) {
            backgroundType = to;
            setGradientProperties();
            if(backgroundType == 'gradient'){
             
              $( '#gform_wrapper_'+formId ).css( 'background-image','none' );
              $( '#gform_wrapper_'+formId ).css( 'background',backgroundColor );
              $( '#gform_wrapper_'+formId ).css( 'background', gradientStandard);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientOpera);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientFirefox);
              $( '#gform_wrapper_'+formId ).css( 'background', gradientSafari);
              
             }
             if(backgroundType == 'color'){

                $( '#gform_wrapper_'+formId ).css( 'background-image','none' );
               // $( '#gform_wrapper_'+formId ).css( 'background', "");
                $( '#gform_wrapper_'+formId ).css( 'background', backgroundColor);
             } 

             if(backgroundType == 'image'){

                $( '#gform_wrapper_'+formId ).css( 'background-image','url(' + backgroundImage + ')' );

             } 

         } );
  } );

  // wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-opacity]', function( value ) {
  //   value.bind( function( to ) {
  //           $( '#gform_wrapper_'+formId ).css( 'opacity',to );
  //        } );
  // } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId ).css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][font]', function( value ) {
    value.bind( function( to ) {
      if(to == 'Default') {
         $( '#gform_wrapper_'+formId ).css( 'font-family','inherit' );
      }
      else{
              addGoogleFont(to);
            $( '#gform_wrapper_'+formId ).css( 'font-family','"'+to+'"' );
          }
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId ).css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][background-image]', function( value ) {
    value.bind( function( to ) {
      backgroundImage = to;
            $( '#gform_wrapper_'+formId ).css( 'background-image','url(' + to + ')' );
         } );
  } );

// wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][margin]', function( value ) {

//     value.bind( function( to ) {
//      to = addPxToMarginPadding(to);
//             $( '#gform_wrapper_'+formId ).css( 'margin',to );
//          } );
//   } );

// ***** start ****
wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][margin-top]', function( value ) {

  value.bind( function( to ) {
   to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'margin-top',to );
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][margin-right]', function( value ) {

  value.bind( function( to ) {
   to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'margin-right',to );
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][margin-bottom]', function( value ) {

  value.bind( function( to ) {
   to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'margin-bottom',to );
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][margin-left]', function( value ) {

  value.bind( function( to ) {
   to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'margin-left',to );
       } );
} );

// ***** end ****

// wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][padding]', function( value ) {
//     value.bind( function( to ) {
//       to = addPxToMarginPadding(to);
//             $( '#gform_wrapper_'+formId ).css( 'padding',to);
//          } );
//   } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][padding-top]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'padding-top',to);
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][padding-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'padding-right',to);
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][padding-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'padding-bottom',to);
       } );
} );


wp.customize( 'gf_stla_form_id_'+formId+'[form-wrapper][padding-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId ).css( 'padding-left',to);
       } );
} );

//********************************* Form Header *******************************************

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'background',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-header][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-header][border-type]', function( value ) {
    value.bind( function( to ) {

            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-header][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-header][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'border-radius',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-header][margin-top]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'margin-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][margin-bottom]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'margin-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][margin-left]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'margin-left',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][margin-right]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'margin-right',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-header][padding-top]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][padding-bottom]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'padding-bottom',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][padding-right]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-header][padding-left]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading' ).css( 'padding-left',to);
         } );
  } );

  


//********************************* Form Title *******************************************


wp.customize( 'gf_stla_form_id_'+formId+'[form-title][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( setFontStyles( to ) );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-title][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'font-size',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-title][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'text-align',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-title][margin-top]', function( value ) {
    value.bind( function( to ) {
  to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'margin-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][margin-bottom]', function( value ) {
    value.bind( function( to ) {
  to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'margin-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][margin-right]', function( value ) {
    value.bind( function( to ) {
  to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'margin-right',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][margin-left]', function( value ) {
    value.bind( function( to ) {
  to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'margin-left',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-title][padding-top]', function( value ) {
    value.bind( function( to ) {
   to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][padding-bottom]', function( value ) {
    value.bind( function( to ) {
   to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'padding-bottom',to);
         } );
  } );


  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][padding-right]', function( value ) {
    value.bind( function( to ) {
   to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-title][padding-left]', function( value ) {
    value.bind( function( to ) {
   to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_title' ).css( 'padding-left',to);
         } );
  } );
//********************************* Form Description *******************************************

wp.customize( 'gf_stla_form_id_'+formId+'[form-description][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( setFontStyles( to ) );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-description][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'font-size',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-description][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'text-align',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[form-description][margin-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'margin-top',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][margin-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'margin-bottom',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][margin-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'margin-right',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][margin-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'margin-left',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[form-description][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'padding-top',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][padding-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'padding-bottom',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][padding-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'padding-left',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[form-description][padding-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_heading .gform_description' ).css( 'padding-right',to);
         } );
  } );

//********************************* Dropdown Fields *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'background',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][margin-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'margin-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][margin-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'margin-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][margin-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'margin-right',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][margin-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'margin-left',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'padding-top',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'padding-bottom',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'padding-right',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[dropdown-fields][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield select' ).css( 'padding-left',to);
         } );
  } );
//********************************* Radio Inputs *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][font-color]', function( value ) {
    value.bind( function( to ) {
            $( ' #gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio label' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][font-style]', function( value ) {
    value.bind( function( to ) {
            $( ' #gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio label' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio label' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'width',to );
         } );
  } );


// wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][margin-top]', function( value ) {
//     value.bind( function( to ) {
//      to = addPxToMarginPadding(to);
//             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'margin-top',to );
//          } );
//   } );


wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'padding-top',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'padding-bottom',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'padding-right',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[radio-inputs][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_radio' ).css( 'padding-left',to);
         } );
  } );
//********************************* Checkbox Inputs *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][font-style]', function( value ) {
    value.bind( function( to ) {
            $( ' #gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox' ).css( 'width',to );
         } );
  } );


// wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][margin]', function( value ) {
//     value.bind( function( to ) {
//             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox' ).css( 'margin',to );
//          } );
//   } );

wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'padding-top',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'padding-bottom',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'padding-right',to);
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[checkbox-inputs][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_checkbox label' ).css( 'padding-left',to);
         } );
  } );
//********************************* Field Labels *******************************************

  // wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][display]', function( value ) {
  //   value.bind( function( to ) {
  //           if(to){
  //             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'display','none' );
  //           }
  //           else{
  //             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'display','inherit' );
  //           }
  //        } );
  // } );
  
  wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'text-align',to );
         } );
  } );


// wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][margin]', function( value ) {
//     value.bind( function( to ) {
//       to = addPxToMarginPadding(to);
//             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'margin',to );
//          } );
//   } );

wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'padding-bottom',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-labels][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_label' ).css( 'padding-left',to);
         } );
  } );

//********************************* Sub Labels *******************************************


wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][font-color]', function( value ) {
    value.bind( function( to ) {
                $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'color',to );

              $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'color',to );
             
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'color',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][font-style]', function( value ) {
    value.bind( function( to ) {
                $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( setFontStyles( to ) );

              $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( setFontStyles( to ) );
             
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( setFontStyles( to ) );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'font-size',to );

             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'font-size',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'font-size',to );

         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'padding-left',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'padding-left',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'padding-left',to );

             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'padding-left',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'padding-left',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'padding-left',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'padding-left',to );
         } );
  } );


  wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'padding-right',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'padding-right',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'padding-right',to );

             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'padding-right',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'padding-right',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'padding-right',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'padding-right',to );
         } );
  } );


  wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'padding-top',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'padding-top',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'padding-top',to );

             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'padding-top',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'padding-top',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'padding-top',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'padding-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-sub-labels][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_full label' ).css( 'padding-bottom',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_right label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_complex .ginput_left label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_hour label' ).css( 'padding-bottom',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_time_minute label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_month label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_day label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_date_year label' ).css( 'padding-bottom',to );

             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_first label' ).css( 'padding-bottom',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .name_last label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_1 label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_line_2 label' ).css( 'padding-bottom',to);
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_city label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_state label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_zip label' ).css( 'padding-bottom',to );
             $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .address_country label' ).css( 'padding-bottom',to );
         } );
  } );
//********************************* Field Descriptions *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( setFontStyles( to ));
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'display','block' );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'text-align',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][margin]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'margin',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][padding-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'padding-bottom',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][padding-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[field-descriptions][padding-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .gfield_description' ).css( 'padding-left',to);
         } );
  } );

//********************************* Text Fields *******************************************

var allTextFields = "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=text], #gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=email], #gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=tel], #gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=[password], #gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=url]";

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][font-color]', function( value ) {
    value.bind( function( to ) {
            $( "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=text]" ).css( 'color',to );
            $( "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=email]" ).css( 'color',to );
            $( "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=tel]" ).css( 'color',to );
            $( "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=password]" ).css( 'color',to );
            $( "#gform_wrapper_"+formId+" .gform_body .gform_fields .gfield input[type=url]" ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( setFontStyles( to ));
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( setFontStyles( to ));
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( setFontStyles( to ));
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( setFontStyles( to ));
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( setFontStyles( to ));
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'background',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][margin-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'margin-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][margin-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'margin-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'margin-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'margin-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'margin-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'margin-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][margin-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'margin-right',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'margin-right',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'margin-right',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'margin-right',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'margin-right',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][margin-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'margin-left',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'margin-left',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'margin-left',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'margin-left',to );
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'margin-left',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][padding-top]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][padding-bottom]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'padding-bottom',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][padding-right]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][padding-left]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=email]' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=text]' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=url]' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=password]' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield input[type=tel]' ).css( 'padding-left',to);
         } );
  } );

//********************************* Paragraph Textarea Fields *******************************************
  wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'color',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'font-size',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'background',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[text-fields][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'border-radius',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'width',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][margin-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'margin-top',to );
         } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][margin-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'margin-bottom',to );
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][margin-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'margin-left',to );
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][margin-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'margin-right',to );
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'padding-top',to);
         } );
} );
wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][padding-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'padding-bottom',to);
       } );
} );
wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][padding-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'padding-right',to);
       } );
} );
wp.customize( 'gf_stla_form_id_'+formId+'[paragraph-textarea][padding-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield textarea' ).css( 'padding-left',to);
       } );
} );

//********************************* List Field Table*******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-table][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list' ).css( 'background-color',to );
         } );
  } );

  //********************************* List Field Heading*******************************************

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][font-size]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( setFontStyles( to ) );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( setFontStyles( to ));
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'color',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'background-color',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'text-align',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'padding-top',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'padding-bottom',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'padding-right',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-heading][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list thead th:not(:last-child)' ).css( 'padding-left',to );
         } );
  } );

//********************************* List Field Cell*******************************************

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell input' ).css( 'font-size',to );
         } );
  } );

      wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell input' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell input' ).css( setFontStyles( to ) );
         } );
  } );

     wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell input' ).css( 'background-color',to );
         } );
  } );

     wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell input' ).css( 'text-align',to );
         } );
  } );

  //********************************* List Field Cell Container*******************************************

   wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell-container][padding-top]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell' ).css( 'padding-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell-container][padding-bottom]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell' ).css( 'padding-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell-container][padding-right]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell' ).css( 'padding-right',to );
         } );
  } );
  wp.customize( 'gf_stla_form_id_'+formId+'[list-field-cell-container][padding-left]', function( value ) {
    value.bind( function( to ) {
     to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gfield .ginput_list table.gfield_list tbody tr td.gfield_list_cell' ).css( 'padding-left',to );
         } );
  } );

//********************************* Submit Button *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][button-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'background',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'background',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( setFontStyles( to ) );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( setFontStyles( to ) );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( setFontStyles( to ) );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( setFontStyles( to ));
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( setFontStyles( to ));
         } );
  } );

  // wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][hover-color]', function( value ) {
  //   value.bind( function( to ) {
  //           $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]:hover' ).css( 'background',to );
  //           $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button:hover' ).css( 'background',to );
  //        } );
  // } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][height]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'height',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'height',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'height',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'height',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'height',to );

         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][button-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer' ).css( 'text-align',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'float',to );
             $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'text-align',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'font-size',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'font-size',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'border-width',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'border-style',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'border-color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'border-radius',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'color',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'color',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][margin-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'margin-top',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'margin-top',to );
         } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][margin-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'margin-bottom',to );
          $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'margin-bottom',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'margin-bottom',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'margin-bottom',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'margin-bottom',to );
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][margin-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'margin-right',to );
          $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'margin-right',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'margin-right',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'margin-right',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'margin-right',to );
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][margin-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'margin-left',to );
          $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'margin-left',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'margin-left',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'margin-left',to );
          $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'margin-left',to );
       } );
} );



wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'padding-top',to);
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'padding-top',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'padding-top',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'padding-top',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][padding-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'padding-bottom',to);
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'padding-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'padding-bottom',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'padding-bottom',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][padding-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'padding-right',to);
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'padding-right',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'padding-right',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'padding-right',to );
         } );
  } );


  wp.customize( 'gf_stla_form_id_'+formId+'[submit-button][padding-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_footer input[type=submit]' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_footer button.mdl-button' ).css( 'padding-left',to);
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_previous_button' ).css( 'padding-left',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer .gform_next_button' ).css( 'padding-left',to );
            $( '#gform_wrapper_'+formId+' .gform_page_footer input[type=submit]' ).css( 'padding-left',to );
         } );
  } );

//********************************* Section Break Title *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[section-break-title][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_title' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[section-break-title][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_title' ).css( setFontStyles( to ) );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[section-break-title][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_title' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[section-break-title][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_title' ).css( 'text-align',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[section-break-title][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_title' ).css( 'background-color',to );
         } );
  } );


//********************************* Section Break Description *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( setFontStyles( to ) );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( 'background-color',to );
         } );
 } );
  
   wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( 'text-align',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][margin]', function( value ) {
    value.bind( function( to ) {
    to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection .gsection_description' ).css( 'margin',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection' ).css( 'padding-top',to);
         } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][padding-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection' ).css( 'padding-bottom',to);
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][padding-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection' ).css( 'padding-right',to);
       } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[section-break-description][padding-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .gform_body .gform_fields .gsection' ).css( 'padding-left',to);
       } );
} );


//********************************* Confirmation Message *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( 'color',to );
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][font-style]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( setFontStyles( to ) );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( 'text-align',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( 'background',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gforms_confirmation_message_'+formId ).css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][margin]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'margin',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'padding-top',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][padding-bottom]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'padding-bottom',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][padding-right]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'padding-right',to);
         } );
  } );

  wp.customize( 'gf_stla_form_id_'+formId+'[confirmation-message][padding-left]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gforms_confirmation_message_'+formId ).css( 'padding-left',to);
         } );
  } );


//********************************* error Message *******************************************


  wp.customize( 'gf_stla_form_id_'+formId+'[error-message][font-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][text-align]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'text-align',to );
         } );
  } );

   wp.customize( 'gf_stla_form_id_'+formId+'[error-message][font-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'font-size',to );
         } );
  } );


wp.customize( 'gf_stla_form_id_'+formId+'[error-message][max-width]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][background-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'background',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][border-size]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'border-width',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][border-type]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'border-style',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][border-color]', function( value ) {
    value.bind( function( to ) {
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'border-color',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][border-radius]', function( value ) {
    value.bind( function( to ) {
      to = addPxToValue(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'border-radius',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][margin]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'margin',to );
         } );
  } );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][padding-top]', function( value ) {
    value.bind( function( to ) {
      to = addPxToMarginPadding(to);
            $( '#gform_wrapper_'+formId+' .validation_error').css( 'padding-top',to);
         } );
} );

wp.customize( 'gf_stla_form_id_'+formId+'[error-message][padding-bottom]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .validation_error').css( 'padding-bottom',to);
       } );
} );
wp.customize( 'gf_stla_form_id_'+formId+'[error-message][padding-right]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .validation_error').css( 'padding-right',to);
       } );
} );
wp.customize( 'gf_stla_form_id_'+formId+'[error-message][padding-left]', function( value ) {
  value.bind( function( to ) {
    to = addPxToMarginPadding(to);
          $( '#gform_wrapper_'+formId+' .validation_error').css( 'padding-left',to);
       } );
} );

} );

