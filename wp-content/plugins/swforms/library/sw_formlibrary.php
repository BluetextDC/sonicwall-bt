<?php

if ($_REQUEST['param'] == "add_form") {
        $my_form_table_resp = $wpdb->insert(my_form_table(), array(
            "name" => $_REQUEST['form_data']['formName'],
            "short_code" => $_REQUEST['form_data']['formShortcode'],
            "author" => $_REQUEST['form_data']['formAuthor'],
            "comments" => $_REQUEST['form_data']['formDescription'],
            "form_json" => stripslashes($_REQUEST['form_data']['formJSON']),
            "form_html" => stripslashes($_REQUEST['form_data']['formHtml']),
            "field_map" => stripslashes($_REQUEST['form_data']['formFieldMap']),
            "eloqua_submit" => $_REQUEST['form_data']['formEloquaSubmit'],
        ));
        if($my_form_table_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Form created successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in creating form"));
        }
        
    } elseif ($_REQUEST['param'] == "edit_form") {
        $my_form_table_update_resp = $wpdb->update(my_form_table(), array(
            "name" => $_REQUEST['form_data']['formName'],
            "short_code" => $_REQUEST['form_data']['formShortcode'],
            "author" => $_REQUEST['form_data']['formAuthor'],
            "comments" => $_REQUEST['form_data']['formDescription'],
            "form_json" => stripslashes($_REQUEST['form_data']['formJSON']),
            "form_html" => stripslashes($_REQUEST['form_data']['formHtml']),
            "field_map" => stripslashes($_REQUEST['form_data']['formFieldMap']),
            "eloqua_submit" => $_REQUEST['form_data']['formEloquaSubmit'],
                ), array(
            "id" => $_REQUEST['form_data']['formId']
        ));
        
        if($my_form_table_update_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Form updated successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in updating form"));
        }
        
        
    } elseif ($_REQUEST['param'] == "delete_form") {
        $delete_form_resp = $wpdb->delete(my_form_table(), array(
            "id" => $_REQUEST['formId']
        ));
        if($delete_form_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Form deleted successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in deleting form"));
        }
    } elseif ($_REQUEST['param'] == "eloqua_signout") {
        $_SESSION['access_token'] = '';
        $eloqua_signout_resp = $wpdb->update(eloqua_token(), array(
            "access_token" => '',
            "token_type" => '',
            "expires_in" => '',
            "refresh_token" => '',
            ), array(
            "id" => 1
        ));
        if($eloqua_signout_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Eloqua Disconnected"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in Eloqua signout"));
        }
    } elseif ($_REQUEST['param'] == "form_name_check") {
        
        $form_entered_name = $_REQUEST['form_name'];
        $results = $wpdb->get_results( "SELECT `name` from ".my_form_table(), ARRAY_A );
        $nameFlag = false;
        foreach($results as $struct) {
            if ($form_entered_name == $struct['name']) {
                $nameFlag = true;
                break;
            }
        }
        if($nameFlag) {
            echo json_encode(array("status" => 1, "message" => true));
        } else {
            echo json_encode(array("status" => 1, "message" => false));
        }
    } else if ($_REQUEST['param'] == "eloqua_feed_settings") {
        function get_saved_eloqua_form_fields_map2($sw_form_id) {
            global $wpdb;
            if($sw_form_id) {
                return $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT field_map from " . my_form_table() . " WHERE id = %d ",$sw_form_id
                    ), ARRAY_A
                );
            } else {
                return '';
            } 
        }
        function local_scope($form_settings_data) {
            global $wpdb;
            $eloqua_form_fields = json_decode(get_saved_eloqua_form_fields_map2($form_settings_data['sw_form_id'])[0]['field_map']);
            $field_map = array();
            if ($eloqua_form_fields) {
                foreach ($eloqua_form_fields as $field) {
                    if(!is_null($form_settings_data[$field->{'swform_feild_id'}])) {
                        $temp_obj = $field;
                        $temp_obj->{'eloqua_field_id'} = explode('|', $form_settings_data[$field->{'swform_feild_id'}])[0];
                        $temp_obj->{'eloqua_field_name'} = explode('|', $form_settings_data[$field->{'swform_feild_id'}])[1];
                        array_push($field_map, $temp_obj);
                    } 
                }
                if(count($field_map) > 0) {
                    $form_settings_resp = $wpdb->update(my_form_table(), array(
                        "field_map" => json_encode($field_map),
                        "eloqua_form_id" => $form_settings_data['eloqua_form_id']
                            ), array(
                        "id" => $form_settings_data['sw_form_id']
                    ));
                    if($form_settings_resp == 1) {
                        echo json_encode(array("status" => 1, "message" => "Eloqua Feed settings saved successfully"));
                    } else {
                        echo json_encode(array("status" => 0, "message" => "Error in saving Eloqua Feed settings"));
                    }
                } else {
                    echo json_encode(array("status" => 0, "message" => "Error in pushing field map to array."));
                }
            } else {
                echo json_encode(array("status" => 0, "message" => "Error in fetching Eloqua Feed"));
            }
        }
        local_scope($_REQUEST['form_data']);
        
    } else if($_REQUEST['param'] == "eloqua_notification_update") {
        $eloqua_notification_update_resp = $wpdb->update(eloqua_token(), array(
            "eloqua_notification_mail" => $_REQUEST['eloqua_notification_mail'],
                ), array(
            "id" => 1
        ));
        if($eloqua_notification_update_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Email Address updated successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in updating Email Address"));
        }
    } else if($_REQUEST['param'] == "notification_settings") {
        $notification_check = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT my_form_table_id from ".sw_mail_notification()." WHERE my_form_table_id = %d ",$_REQUEST['form_data']['sw_form_id']
            ),ARRAY_A
        );
        if($notification_check === null) {
            $sw_mail_notification_resp = $wpdb->insert(sw_mail_notification(), array(
                "my_form_table_id" => $_REQUEST['form_data']['sw_form_id'],
                "form_to_mail_id" => $_REQUEST['form_data']['form_to_mail_id'],
                "form_cc_mail_id" => $_REQUEST['form_data']['form_cc_mail_id'],
                "form_bcc_mail_id" => $_REQUEST['form_data']['form_bcc_mail_id'],
                "form_subject" => $_REQUEST['form_data']['form_subject'],
                "form_after_body" => $_REQUEST['form_data']['form_after_body'],
            ));
        } else if($notification_check['my_form_table_id'] > 0) {        // if condition will automatically convert a string to number
            $sw_mail_notification_resp = $wpdb->update(sw_mail_notification(), array(
                "form_to_mail_id" => $_REQUEST['form_data']['form_to_mail_id'],
                "form_cc_mail_id" => $_REQUEST['form_data']['form_cc_mail_id'],
                "form_bcc_mail_id" => $_REQUEST['form_data']['form_bcc_mail_id'],
                "form_subject" => $_REQUEST['form_data']['form_subject'],
                "form_after_body" => $_REQUEST['form_data']['form_after_body'],
                ), array(
                "my_form_table_id" => $_REQUEST['form_data']['sw_form_id']
            ));
        } else {
            $sw_mail_notification_resp = 0;
        }
        if($sw_mail_notification_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Notification saved successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in saving the notification"));
        }
    } elseif ($_REQUEST['param'] == "delete_entry") {
        $delete_entry_resp = $wpdb->delete(sw_mail_forms_entry(), array(
            "id" => $_REQUEST['formId']
        ));
        if($delete_entry_resp == 1) {
            echo json_encode(array("status" => 1, "message" => "Form deleted successfully"));
        } else {
            echo json_encode(array("status" => 0, "message" => "Error in deleting form"));
        }
    }
