jQuery(document).ready(function() {
    // ------------- form builder -------------
    var formBuilderData;
    var obj = {
        swFormData: {
            formId: null,
            formName: '',
            formShortcode: null,
            formDescription: null,
            formAuthor: '',
            formJSON: null,
            formHtml: '',
            formEloquaId: '',
            formEloquaName: '',
            formFieldMap: null,
            formEloquaSubmit: '0'
        },
        buildFormData: function(data) {
            if(data) {
                obj.swFormData.formId = Number(data['id']);
                obj.swFormData.formName = data['name'];
                obj.swFormData.formShortcode = data['short_code'];
                obj.swFormData.formDescription = data['comments'];
                obj.swFormData.formAuthor = data['author'];
                obj.swFormData.formJSON = JSON.parse(data['form_json']);
                formBuilderData = JSON.parse(JSON.stringify(obj.swFormData.formJSON));
                obj.swFormData.formHtml = data['form_html'];
                obj.swFormData.formEloquaSubmit = data['eloqua_submit'];
            }
        },
        validateForm: function(data) {
            var validFlag = true;
            var errorMsg = "";
            if(jQuery('#formTitle').attr('data-dirty') === "true") {
                validFlag = false;
                errorMsg = errorMsg.concat("Form name already taken. ");
            }
            if(data.formName === '' && data.formJSON.length > 0){
                validFlag = false;
                errorMsg = errorMsg.concat("Please Enter a form name.");
            }
            if (!validFlag) {
                jQuery.notifyBar({
                    cssClass: "error",
                    html: errorMsg
                });
            }
            return validFlag;
        },
        submitForm: function(data) {
            if(data.formId) {
                jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'edit_form', form_data: data}, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.status == 1) {
                        jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
                        jQuery.notifyBar({
                            cssClass: "success",
                            html: data.message
                        });
                        setTimeout(function() {
                            window.location.href = 'admin.php?page=form-list';
                        }, 800)
                    } else {
                        jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
                        jQuery.notifyBar({
                            cssClass: "error",
                            html: data.message
                        });
                    }
                    
                });
            } else {
                jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'add_form', form_data: data}, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.status == 1) {
                        jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
                        jQuery.notifyBar({
                            cssClass: "success",
                            html: data.message
                        });
                        setTimeout(function() {
                            window.location.href = 'admin.php?page=form-list';
                        }, 800)
                    } else {
                        jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
                        jQuery.notifyBar({
                            cssClass: "error",
                            html: data.message
                        });
                    }
                    
                });
            }
        }
    };

    jQuery('#allFormsTable').DataTable();

    if( typeof formjsonFromDb !== 'undefined' ) {
        obj.buildFormData(formjsonFromDb);
    }
    var options = {
        dataType: 'json',
        defaultFields: formBuilderData,
        onSave: function(evt, formData) {                         // formData Type:  string
            jQuery('#swforms-loading').removeClass('sw-hide').addClass('sw-show');
            jQuery('.render-wrap').formRender({formData});
            var htmlData = jQuery('#generatedFormHtml').html();     // of type string    
            obj.swFormData.formJSON = formData;
            obj.swFormData.formHtml = htmlData;
            formBuilderData = JSON.parse(JSON.stringify(formData));
            obj.swFormData.formName = jQuery('#formTitle').val();
            obj.swFormData.formDescription = jQuery('#formDescription').val();
            obj.swFormData.formAuthor = jQuery('#formAuthor').val();
            if(jQuery('#formEloquaSubmit').prop("checked") == true){
                obj.swFormData.formEloquaSubmit = '1';
            } else if(jQuery('#formEloquaSubmit').prop("checked") == false){
                obj.swFormData.formEloquaSubmit = '0';
            }
            var formFieldMapTemp = [];
            if(obj.swFormData.formJSON) {
                JSON.parse(obj.swFormData.formJSON).forEach(function(data, index) {
                    if(data['type'] !== 'button') {
                        formFieldMapTemp.push({'swform_feild_id': data['name'], 'swform_feild_name': data['label'] ? data['label'] : data['name'], 'eloqua_field_name': '', 'eloqua_field_id': ''});
                    }
                });
                obj.swFormData.formFieldMap = JSON.stringify(formFieldMapTemp);
            }
            if(obj.validateForm(obj.swFormData)) {
                obj.submitForm(obj.swFormData);
            } else {
                jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
            }
        }
    };
    jQuery('#build-wrap').formBuilder(options);

    jQuery(document).on("click", ".btnformdelete", function() {
        var conf = confirm("Are you sure want to delete?");
        if (conf) { //if(true)
            var form_id = jQuery(this).attr("data-id");
            jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'delete_form', formId: form_id}, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.status == 1) {
                    jQuery.notifyBar({
                        cssClass: "success",
                        html: data.message
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 1300)
                } else {
                    jQuery.notifyBar({
                        cssClass: "error",
                        html: data.message
                    });
                }
            });
        }
    });

    jQuery(document).on("click", "#update_eloqua_notification_call", function() {
        var update_eloqua_notification_mail = jQuery('#update_eloqua_notification_mail').val();
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if(update_eloqua_notification_mail.match(mailformat)) {
            jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'eloqua_notification_update', eloqua_notification_mail: update_eloqua_notification_mail }, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.status == 1) {
                    jQuery.notifyBar({
                        cssClass: "success",
                        html: data.message
                    });
                    window.location.reload();
                } else {
                    jQuery.notifyBar({
                        cssClass: "error",
                        html: data.message
                    });
                }
            });
        } else {
            jQuery.notifyBar({
                cssClass: "error",
                html: 'Please enter a proper email id'
            });
        }
    });

    // -------------- Check form name for repetation --------------
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1200;  //time in ms, 5 second for example
    var formTitle = jQuery('#formTitle');

    //on keyup, start the countdown
    jQuery(formTitle).on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() { doneTyping(jQuery(formTitle).val()); }, doneTypingInterval);
    });
  
    //on keydown, clear the countdown 
    jQuery(formTitle).on('keydown', function () {
        clearTimeout(typingTimer);
    });

    if(jQuery("#sw_table_form_data").length !== 0) {
        jQuery('#sw_table_form_data').DataTable({
            "scrollX": true
        });
    }
    if(jQuery("#sw_non_eloqua_entry").length !== 0) {
        jQuery('#sw_non_eloqua_entry').DataTable({
            "scrollX": true,
            "scrollY": "calc(100vh - 400px)"
        });
    }

    jQuery('#sw_form_entries').on('change', function() {
        var selected_sw_form_entries = jQuery('#sw_form_entries').val().split("|")[0];
        var selected_sw_form_entries_eloqua_submit = jQuery('#sw_form_entries').val().split("|")[1];
        window.location.href= swsiteurl + '/wp-admin/admin.php?page=form-entries&id=' + selected_sw_form_entries + '&esub=' + selected_sw_form_entries_eloqua_submit;
    });
    jQuery('#sw_forms_selected_submit').on('click', function() {
        var selected_sw_form_id = jQuery('#sw_forms_select').val();
        var selected_sw_form_name = jQuery('#sw_forms_select option:selected').text();
        if (selected_sw_form_id && selected_sw_form_name) {
            window.location.href= swsiteurl + '/wp-admin/admin.php?page=eloqua-form-settings&id=' + selected_sw_form_id + '&name=' + selected_sw_form_name;
        }
    });
    jQuery('#sw_forms_notification_submit').on('click', function() {
        var selected_notification_form_id = jQuery('#sw_notification_form_select').val();
        var selected_notification_form_name = jQuery('#sw_notification_form_select option:selected').text();
        if (selected_notification_form_id && selected_notification_form_name) {
            window.location.href= swsiteurl + '/wp-admin/admin.php?page=email-notification&id=' + selected_notification_form_id + '&name=' + selected_notification_form_name;
        }
    });
    jQuery('#eloqua_feed_settings').submit(function(event) {
        event.preventDefault();
        var eloquaFeedSettingsObj = JSON.parse(JSON.stringify(jQuery(this).serializeArray()));
        var sw_form_id_flag = 0;
        var eloqua_form_id_flag = 0;
        var count = 0;
        var eloquaFeedSettingsPostData = {};
        for (let field of eloquaFeedSettingsObj) {
            count += 1;
            eloquaFeedSettingsPostData[field['name']] = field['value'];
            if(field['name'] === 'eloqua_form_id') {
                eloqua_form_id_flag = 1;
                if (field['value'] === '') {
                    eloqua_form_id_flag = 2;
                }
            }
            if(field['name'] === 'sw_form_id') {
                sw_form_id_flag = 1;
                if (field['value'] === '') {
                    sw_form_id_flag = 2;
                }
            }
        }
        if(eloqua_form_id_flag > 1 || sw_form_id_flag > 1) {
            jQuery.notifyBar({
                cssClass: "error",
                html: 'Please select a SWForm & Eloqua form'
            });
        } else {
            jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'eloqua_feed_settings', form_data: eloquaFeedSettingsPostData}, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.status == 1) {
                    jQuery.notifyBar({
                        cssClass: "success",
                        html: data.message
                    });
                } else {
                    jQuery.notifyBar({
                        cssClass: "error",
                        html: data.message
                    });
                }
                
            });
        }
      });

    jQuery('#notification_settings').submit(function(event) {
        event.preventDefault();
        jQuery('#swforms-loading').removeClass('sw-hide').addClass('sw-show');
        var validateNotificationResponse = validateNotificationForm();
        var notificationSettingsPostData = {};
        if (validateNotificationResponse) {
            var formNotificationSettingsObj = JSON.parse(JSON.stringify(jQuery(this).serializeArray()));
            for (let field of formNotificationSettingsObj) {
                notificationSettingsPostData[field['name']] = field['value'];
            }
            jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'notification_settings', form_data: notificationSettingsPostData}, function(response) {
                var data = jQuery.parseJSON(response);
                jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
                if (data.status == 1) {
                    jQuery.notifyBar({
                        cssClass: "success",
                        html: data.message
                    });
                    setTimeout(function() {
                        window.location.reload();
                    }, 300)
                } else {
                    jQuery.notifyBar({
                        cssClass: "error",
                        html: data.message
                    });
                }
            });
        } else {
            jQuery('#swforms-loading').removeClass('sw-show').addClass('sw-hide');
            jQuery.notifyBar({
                cssClass: "error",
                html: 'Error in submitting the form'
            });
        }
    });

    jQuery(document).on("click", ".entryDelete", function() {
        var conf = confirm("Are you sure want to delete?");
        if (conf) { //if(true)
            var form_id = jQuery(this).attr("data-id");
            jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'delete_entry', formId: form_id}, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.status == 1) {
                    jQuery.notifyBar({
                        cssClass: "success",
                        html: data.message
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 1300)
                } else {
                    jQuery.notifyBar({
                        cssClass: "error",
                        html: data.message
                    });
                }
            });
        }
    });
});

function validateNotificationForm() {
    var notification_flag = true;
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    jQuery( "#notification_settings input[type=email]" ).each(function() {
        var label = jQuery(this).siblings('label').html();
        if (jQuery(this).attr('name') === 'form_to_mail_id') {
            if (jQuery(this).val()) {
                var toMailArr = jQuery(this).val().replace(' ', '').split(',');
                var toErrorPosition = [];
                var toInnerStatus = true;
                toMailArr.forEach(function(item, index) {
                    if(mailformat.test(item)) {

                    } else {
                        notification_flag = false;
                        toInnerStatus = false;
                        toErrorPosition.push(index);
                    }
                });
                if(toInnerStatus === false) {
                    var toFieldValidationMsg = `Please enter a proper ${label} at position `;
                    toErrorPosition.forEach(function(data, index) {
                        if(index == 0) {
                            toFieldValidationMsg += data+1;
                        } else {
                            toFieldValidationMsg += ', ';
                            toFieldValidationMsg += data+1;
                        }
                        
                    });
                    jQuery(this).siblings('.sw-admin-validation-class').remove();
                    jQuery(this).after(`<span class="sw-admin-validation-class">${toFieldValidationMsg}</span>`);
                }
            } else {
                jQuery(this).siblings('.sw-admin-validation-class').remove();
                jQuery(this).after('<span class="sw-admin-validation-class">Field cannot be empty</span>');
                notification_flag = false;
            }
        } else if(jQuery(this).attr('name') === 'form_cc_mail_id') {
            if (jQuery(this).val()) {
                var ccMailArr = jQuery(this).val().replace(' ', '').split(',');
                var ccErrorPosition = [];
                var ccInnerStatus = true;
                ccMailArr.forEach(function(item, index) {
                    if(mailformat.test(item)) {

                    } else {
                        notification_flag = false;
                        ccInnerStatus = false;
                        ccErrorPosition.push(index);
                    }
                });
                if(ccInnerStatus === false) {
                    var ccFieldValidationMsg = `Please enter a proper ${label} at position `;
                    ccErrorPosition.forEach(function(data, index) {
                        if(index == 0) {
                            ccFieldValidationMsg += data+1;
                        } else {
                            ccFieldValidationMsg += ', ';
                            ccFieldValidationMsg += data+1;
                        }
                        
                    });
                    jQuery(this).siblings('.sw-admin-validation-class').remove();
                    jQuery(this).after(`<span class="sw-admin-validation-class">${ccFieldValidationMsg}</span>`);
                }
            } else {
                jQuery(this).siblings('.sw-admin-validation-class').remove();
            }
        }  else if(jQuery(this).attr('name') === 'form_bcc_mail_id') {
            if (jQuery(this).val()) {
                var bccMailArr = jQuery(this).val().replace(' ', '').split(',');
                var bccErrorPosition = [];
                var bccInnerStatus = true;
                bccMailArr.forEach(function(item, index) {
                    if(mailformat.test(item)) {

                    } else {
                        notification_flag = false;
                        bccInnerStatus = false;
                        bccErrorPosition.push(index);
                    }
                });
                if(bccInnerStatus === false) {
                    var bccFieldValidationMsg = `Please enter a proper ${label} at position `;
                    bccErrorPosition.forEach(function(data, index) {
                        if(index == 0) {
                            bccFieldValidationMsg += data+1;
                        } else {
                            bccFieldValidationMsg += ', ';
                            bccFieldValidationMsg += data+1;
                        }
                    });
                    jQuery(this).siblings('.sw-admin-validation-class').remove();
                    jQuery(this).after(`<span class="sw-admin-validation-class">${bccFieldValidationMsg}</span>`);
                }
            } else {
                jQuery(this).siblings('.sw-admin-validation-class').remove();
            }
        }
    });
    return notification_flag;
}

function sw_form_select() {
    var selected_sw_form = jQuery('#sw_forms_select').val();
    if (selected_sw_form !== '') {
        jQuery.post(swformsajaxurl, {action: 'swformthemelibrary', param: 'sw_form_select', formId: selected_sw_form}, function(response) {
            console.log('Response', response);
        });
    }
}

// -------------- Check form name for repetation - user is "finished typing," do something -------------
function doneTyping(formName) {
    jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'form_name_check', form_name: formName}, function(response) {
        var data = jQuery.parseJSON(response);
        if (data.status == 1) {
            if(data.message) {
                jQuery('#formTitle').attr('data-dirty', true);
                jQuery('#formTitle').siblings('label').append('<span id="formTitleWarning" style="color: red">   * Name already taken</span>');
            } else {
                jQuery('#formTitle').attr('data-dirty', false);
                jQuery('#formTitle').siblings('label').find('#formTitleWarning').remove();
            }
        }
    });
}

function formFloatLabel() {
    const FloatLabel = (() => {
    // add active class and placeholder 
    const handleFocus = (e) => {
        const target = e.target;
        target.parentNode.classList.add('active');
        target.setAttribute('placeholder', target.getAttribute('data-placeholder'));
    };
    // remove active class and placeholder
    const handleBlur = (e) => {
        const target = e.target;
        if(!target.value) {
        target.parentNode.classList.remove('active');
        }
        target.removeAttribute('placeholder');    
    };  
    // register events
    const bindEvents = (element) => {
        const floatField = element.querySelector('input');
        floatField.addEventListener('focus', handleFocus);
        floatField.addEventListener('blur', handleBlur);
    };
    // get DOM elements
    const init = () => {
        const floatContainers = document.querySelectorAll('.fb-text, .fb-number');
        floatContainers.forEach((element) => {
        if (element.querySelector('input').value) {
            element.classList.add('active');
        }       
        bindEvents(element);
        });
    };
    return {
        init: init
    };
    })();
    FloatLabel.init();
}

window.addEventListener("load", function() {
	// store tabs variables
	var tabs = document.querySelectorAll("ul.nav-tabs > li");
	for (i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener("click", switchTab);
	}
	function switchTab(event) {
		event.preventDefault();
		document.querySelector("ul.nav-tabs li.active").classList.remove("active");
		document.querySelector(".tab-pane.active").classList.remove("active");
		var clickedTab = event.currentTarget;
		var anchor = event.target;
		var activePaneID = anchor.getAttribute("href");
		clickedTab.classList.add("active");
		document.querySelector(activePaneID).classList.add("active");
	}
});

function eloquaAuthenticate() {
    console.log('Authenticate to Eloqua');
}

function pageRedirect(pageSlug) {
    setTimeout(function() {
        window.location.href = `admin.php?page=${pageSlug}`;
    }, 400);
}

function eloquaSignout() {
    jQuery.post(swformsajaxurl, {action: 'swformlibrary', param: 'eloqua_signout'}, function(response) {
        // console.log(response);
        var data = jQuery.parseJSON(response);
        if (data.status == 1) {
            jQuery.notifyBar({
                cssClass: "success",
                html: data.message
            });
            window.location.reload();
        } else {
            jQuery.notifyBar({
                cssClass: "error",
                html: data.message
            });
        }
        
    });
}

function testEloquaConnection() {
    console.log('testEloquaConnection');
}
