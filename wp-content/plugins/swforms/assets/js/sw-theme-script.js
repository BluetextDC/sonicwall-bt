document.addEventListener('DOMContentLoaded', function () {
  jQuery('#generated_form').append('<input type="hidden" name="eloqua_form_id" value="'+ jQuery('#eloqua_form_id').val() +'" /> <input type="hidden" name="form_id" value="'+ jQuery('#form_id').val() +'" />');
  jQuery('.fb-button').before('<div class="g-recaptcha" data-sitekey="6LdWQ3QUAAAAAMmiHoXQ0Qs90PtoHPv7lw_B7CcT"></div>');

  if(jQuery("#generated_form #selectCountry").length !== 0) {
    if(jQuery("#generated_form #selectState").length !== 0) {
      populateCountries("selectCountry", "selectState");
    }
  }

  jQuery(".sw-form-wrap .sw-half").each(function() {
    // jQuery(this).closest('.form-group').css("width","50%");
    jQuery(this).closest('.form-group').addClass("sw-field-width");
  });

  jQuery( ".sw-form-wrap input[type=text], .sw-form-wrap input[type=email], .sw-form-wrap input[type=tel], .sw-form-wrap input[type=number]" ).on('focus', function() {
    if (jQuery(this).val().length === 0) {
      jQuery( this ).siblings("label").addClass("sw-label-up");
      jQuery( this ).css({'padding' : '12px 6px 8px 6px'});
    }
  });
  jQuery( ".sw-form-wrap input[type=text], .sw-form-wrap input[type=email], .sw-form-wrap input[type=tel], .sw-form-wrap input[type=number]" ).on('blur', function() {
    if (jQuery(this).val().length === 0) {
      jQuery( this ).siblings("label").removeClass("sw-label-up");
      jQuery( this ).css({'padding' : '10px 6px 10px 6px'});
      jQuery( this ).removeClass("sw-this-do");
    }
  });

 jQuery(".sw-form-wrap input[type=number]").on('keydown', function(evt) {
  if(navigator.userAgent.indexOf('MSIE')!==-1
  || navigator.appVersion.indexOf('Trident/') > -1){
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
    }
  } else if(/Edge/.test(navigator.userAgent)) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
    }
  } 
 });

  jQuery( ".sw-form-wrap input[type=text][required=required], .sw-form-wrap input[type=email][required=required], .sw-form-wrap input[type=tel][required=required], .sw-form-wrap input[type=number][required=required]" ).on('blur', function() {
    if (jQuery(this).val().length === 0) {
      jQuery(this).attr('style', 'border-color: #790000 !important');
    } else {
      jQuery(this).prop("style").removeProperty("border-color");
      jQuery(this).siblings('.sw-validation-class').remove();
    }
  });
  jQuery( ".sw-form-wrap textarea[required=required]" ).on('blur', function() {
    if (jQuery(this).val().length === 0) {
      jQuery(this).parent().addClass("borderR");
    } else {
      jQuery(this).parent().removeClass("borderR");
      jQuery(this).siblings('.sw-validation-class').remove();
    }
  });

  jQuery( ".sw-form-wrap textarea[maxlength]" ).each(function() {
    var max_length = jQuery(this).attr('maxlength');
    jQuery(this).before('<span class="sw-count-class">0 / ' + max_length + '</span>');
  });
  jQuery( ".sw-form-wrap textarea[maxlength]" ).on('keyup', function() {
    var max_length = jQuery(this).attr('maxlength');
    var text_count = jQuery(this).val().length;
    jQuery(this).siblings('.sw-count-class').html(text_count + ' / ' + max_length);
  });
  jQuery( ".sw-form-wrap input[type=number]" ).on('keydown', function(evt) {
    if (evt.which == 189) {
        evt.preventDefault();
    }
  });

  jQuery( ".sw-form-wrap select" ).on('focus', function() {
    jQuery( this ).siblings("label").addClass("sw-label-up sw-show-label");
    jQuery( this ).addClass("sw-field-change");
  });

  jQuery('#generated_form').submit(function(event) {
    event.preventDefault();
    jQuery('#swforms-loading').removeClass('sw-hide').addClass('sw-show');
    var validationResponse = validateForm();
    if (validationResponse) {
      var formData = JSON.parse(JSON.stringify(jQuery(this).serializeArray()));
      var formDataConverted = arrOfObjToObj(formData);

   jQuery.post(swformsajaxurl, {action: 'swformthemelibrary', param: 'submit_form', form_data: formDataConverted}, function(response) {
    var data = jQuery.parseJSON(response);
    if (data.status == 1) {
      jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
      window.location.href = swsiteurl + "/partners/become-a-partner/tech-alliance-partner-thank-you";
      console.log(data.message);
    } else {
      jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
      console.log(data.message);
      if(data.message === 'Captcha validation was rejected') {
        jQuery('.g-recaptcha > div').css({'border' : '1px solid #790000', 'border-radius' : '6px'});
	jQuery('.g-recaptcha .sw-captcha-validation-class').remove();
        jQuery('.g-recaptcha > div').after('<span class="sw-captcha-validation-class">Check for captcha validation</span>');
      }
    }
  });

    } else {
      jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
      jQuery( ".sw-form-wrap [required=required]" ).each(function() {
        if(jQuery(this).siblings('.sw-validation-class').length !== 0) {
          jQuery('html, body').animate({
            scrollTop: jQuery(this).offset().top - 200
          }, 500);
          return false;
        }
      });
    }
  });

});

function validateForm() {
  var formStatus = true;
  var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  var phoneFormat = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
  jQuery( ".sw-form-wrap [required=required]" ).each(function() {
    var label = jQuery(this).siblings('label').html();
    if (jQuery(this)[0].nodeName.toLowerCase() === 'input') {
      if (jQuery(this).attr('type') === 'text') {
        if (jQuery(this).val()) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).prop("style").removeProperty("border-color");
        } else {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).after('<span class="sw-validation-class">Field cannot be empty</span>');
          jQuery(this).attr('style', 'border-color: #790000 !important');
          formStatus = false;
        }
      } else if (jQuery(this).attr('type') === 'email') {
        if (jQuery(this).val() === undefined || jQuery(this).val() === '' || jQuery(this).val() === null) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).attr('style', 'border-color: #790000 !important');
          jQuery(this).after('<span class="sw-validation-class">Field cannot be empty</span>');
          formStatus = false;
        } else if(jQuery(this).val().match(mailformat)) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).prop("style").removeProperty("border-color");
        } else {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).attr('style', 'border-color: #790000 !important');
          jQuery(this).after('<span class="sw-validation-class">Enter a proper ' + label + '</span>');
          formStatus = false;
        }
      } else if (jQuery(this).attr('type') === 'tel') {
        if (jQuery(this).val() === undefined || jQuery(this).val() === '' || jQuery(this).val() === null) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).attr('style', 'border-color: #790000 !important');
          jQuery(this).after('<span class="sw-validation-class">Field cannot be empty</span>');
          formStatus = false;
        } else if(jQuery(this).val().match(phoneFormat)) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).prop("style").removeProperty("border-color");
        } else {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).prop("style").removeProperty("border-color");
          jQuery(this).after('<span class="sw-validation-class">Enter a proper Enter a proper ' + label + '</span>');
          formStatus = false;
        }
      } else if (jQuery(this).attr('type') === 'checkbox') {

      } else if (jQuery(this).attr('type') === 'number') {
        if (jQuery(this).val() === undefined || jQuery(this).val() === '' || jQuery(this).val() === null) {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).attr('style', 'border-color: #790000 !important');
          jQuery(this).after('<span class="sw-validation-class">Field cannot be empty</span>');
          formStatus = false;
        } else {
          jQuery(this).siblings('.sw-validation-class').remove();
          jQuery(this).prop("style").removeProperty("border-color");
        }
      } else if (jQuery(this).attr('type') === 'password') {

      } else if (jQuery(this).attr('type') === 'radio') {

      } else if (jQuery(this).attr('type') === 'date') {

      }
    } else if (jQuery(this)[0].nodeName.toLowerCase() === 'select') {
      var selectedOption = jQuery(this).children("option:selected").val();
      if (selectedOption === undefined || selectedOption === null || selectedOption === '' || selectedOption === '-1') {
        jQuery(this).siblings('.sw-validation-class').remove();
        jQuery(this).attr('style', 'border-color: #790000 !important');
        jQuery(this).after('<span class="sw-validation-class">Select a option</span>');
        formStatus = false;
      } else {
        jQuery(this).siblings('.sw-validation-class').remove();
        jQuery(this).prop("style").removeProperty("border-color");
      }
    } else if (jQuery(this)[0].nodeName.toLowerCase() === 'textarea') {
      if (jQuery(this).val()) {
        jQuery(this).siblings('.sw-validation-class').remove();
        jQuery(this).parent().removeClass("borderR");
      } else {
        jQuery(this).siblings('.sw-validation-class').remove();
        jQuery(this).after('<span class="sw-validation-class">Field cannot be empty</span>');
        jQuery(this).parent().addClass("borderR");
        formStatus = false;
      }
    }
  });
  return formStatus;
}

function arrOfObjToObj(data) {
  var result = {};
  data.forEach(function(obj) {
    result[obj['name']] = obj['value'];
  });
  return result;
}
