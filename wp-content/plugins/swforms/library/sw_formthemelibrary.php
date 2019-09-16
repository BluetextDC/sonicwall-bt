<?php
if ($_REQUEST['param'] == "submit_form") {
    $sw_id = (int) preg_replace('/[^0-9]/', '', $_REQUEST['form_data']['form_id']);                 // example 7 (starts from 1)
    $sw_eloqua_id = (int) preg_replace('/[^0-9]/', '', $_REQUEST['form_data']['eloqua_form_id']);   // 0 means there is no eloqua_form_id, in the form hidden field value="/"  (regex converts / to 0)

    if($sw_id !== 0) {
        if ($sw_eloqua_id !== 0) {
            convertDataToEloquaFeed($_REQUEST['form_data'], $_REQUEST['form_data']['form_id'], $_REQUEST['form_data']['eloqua_form_id']);   // eloqua_form_id hidden field was added to the form html in the later stage of development to differentiate the form which are not needed to be submitted to eloqua.
        }
        if($sw_eloqua_id === 0) {
            $captcha_secret_key = '6LdWQ3QUAAAAAGdFzJWwT3uDTbhrYn6txdeDcx6c';
            $captcha_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captcha_secret_key.'&response='.$_REQUEST['form_data']['g-recaptcha-response']);
            $captcha_result = json_decode($captcha_response);
            if($captcha_result->success) {
                $notification_setings = get_notification_settings($sw_id);

                if($notification_setings && $notification_setings[0]['form_to_mail_id'] !== '') {

                    // --------------- Split To mails to array ----------------
                    $form_submit_mail_id = $notification_setings[0]['form_to_mail_id'];
                    $form_submit_mail_id_trim = preg_replace('/\s+/', '', $form_submit_mail_id);
                    $multiple_recipients = explode (",", $form_submit_mail_id_trim);  

                    // --------------- Split CC mails to array ----------------
                    if(strlen($notification_setings[0]['form_cc_mail_id']) > 0) {
                        $form_cc_mail_id = $notification_setings[0]['form_cc_mail_id'];
                        $form_cc_mail_id_trim = preg_replace('/\s+/', '', $form_cc_mail_id);
                        $form_cc_recipients = explode (",", $form_cc_mail_id_trim); 
                        foreach ($form_cc_recipients as $form_cc) {
                            $mail_cc = '';
                            $mail_cc = 'Cc: ' . $form_cc;
                            $headers[] = $mail_cc;
                        }
                    }
                    
                    // --------------- Split bCC mails to array ----------------
                    if(strlen($notification_setings[0]['form_bcc_mail_id']) > 0) {
                        $form_bcc_mail_id = $notification_setings[0]['form_bcc_mail_id'];
                        $form_bcc_mail_id_trim = preg_replace('/\s+/', '', $form_bcc_mail_id);
                        $form_bcc_recipients = explode (",", $form_bcc_mail_id_trim);
                        foreach ($form_bcc_recipients as $form_bcc) {
                            $mail_bcc = '';
                            $mail_bcc = 'Bcc: ' . $form_bcc;
                            $headers[] = $mail_bcc;
                        }
                    }
        
                    if(!valid_email($multiple_recipients[0])) {
                        echo json_encode(array("status" => 0, "message" => "Invalid Email Address"));
                    } else {
                        $subject = $notification_setings[0]['form_subject'];
                        $subject_time_stamp = time() .' - '. $notification_setings[0]['form_subject'];
                        $headers[] = 'From: SonicWall Website <wp@swmail.nguyenle.me>';
                        $headers[] = 'Content-Type: text/html; charset=UTF-8';
                        $body = mail_template_builder($_REQUEST['form_data']);
                        $send_mail_response = sw_trigger_mail($multiple_recipients, $subject, $body, $headers);
                        if($send_mail_response === true) {
                            save_not_eloqua_form_data_db($notification_setings[0]['form_to_mail_id'], $sw_id, $subject_time_stamp, $_REQUEST['form_data'], '1');
                            echo json_encode(array("status" => 1, "message" => "Mail Successfully sent"));
                        } else {
                            // send a Email notification stating Form Email not sent to the given To address (check for smtp config)
                            save_not_eloqua_form_data_db($notification_setings[0]['form_to_mail_id'], $sw_id, $subject_time_stamp, $_REQUEST['form_data'], '0');
                            echo json_encode(array("status" => 1, "message" => "Failed to send mail"));
                        }
                    }
                } else {
                    echo json_encode(array("status" => 1, "message" => "Error in submitting form, No To email address to submit the form"));
                }
            } else {
                echo json_encode(array("status" => 0, "message" => "Captcha validation was rejected"));
            }
        }
    }
} else if ($_REQUEST['param'] == "sw_form_select") {
    echo json_encode(array("status" => 0, "message" => "Submitted form dosen't have a id"));
}
function valid_email($str) {
    return (!preg_match("/^([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}
function mail_template_builder($data) {
    $html = '<table cellpadding="10" cellspacing="0" border="1"><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody>';
    $i = -1;
    foreach ($data as $key => $value) {
        if($key === 'form_id' || $key === 'eloqua_form_id' || $key === 'g-recaptcha-response') {
        } else {
            $readable_key = deliciousCamelcase($key);
            $i++;
            $html .= '<tr '. ($i % 2 === 0 ? 'bgcolor="#FFF"' : 'bgcolor="#eee"') .'><td style="word-break:break-all;">' . $readable_key . '</td><td style="word-break:break-all;">' . $value . '</td></tr>';
        }
    }
    $html .= '</tbody></table>';
    return $html;
}

function convertDataToEloquaFeed($form_data, $sw_form_id, $eloqua_form_id) {
    $sw_id = (int) preg_replace('/[^0-9]/', '', $sw_form_id);               // type integer
    $sw_eloqua_id = (int) preg_replace('/[^0-9]/', '', $eloqua_form_id);    // type integer - eloqua_form_id hidden field was added to the form html in the later stage of development to differentiate the form which are not needed to be submitted to eloqua.
    $result = get_saved_eloqua_form_fields_map($sw_id);                     // get 'field_map' cloumn from 'wp_sw_forms' table
    $eloqua_form_id = $result[0]['eloqua_form_id'];                         
    $eloqua_form_fields = json_decode($result[0]['field_map']);
    if($result) {
        $post_data = (object) ['type' => 'FormData', 'fieldValues' => array()];
        foreach ($eloqua_form_fields as $field) {
            $obj = (object) ['type' => 'FieldValue', 'id' => $field->{'eloqua_field_id'}, 'name' => $field->{'eloqua_field_name'}, 'value' => $form_data[$field->{'swform_feild_id'}]];
            array_push($post_data->fieldValues, $obj);
        }
        check_access_token($sw_eloqua_id, $sw_id, $post_data);
    } else {
        echo json_encode(array("status" => 0, "message" => "Error in submitting due to unable to fetch form_fields_map from wp_sw_forms table"));
    }
}
function check_access_token($eloqua_form_id, $sw_id, $post_data) {
    if ($_SESSION['access_token']) {
        /* 
        To Do, if the access_token is expired then regenerate new access_token using refresh token & save the new token to DB. 
        Or is it a good approach to test the connect to eloqua by hitting this url 'https://login.eloqua.com/id' & check if the token is valid.
        Also we need to get the Eloqua data center url before pushing the form.
        */
        $eloqua_feedback = push_data_to_eloqua($eloqua_form_id, $post_data);
        if ($eloqua_feedback->{'id'}) {
            save_form_data_db($eloqua_form_id, $sw_id, $eloqua_feedback->{'id'}, $post_data, true);
            echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
        } else {
            $save_form_data_db_resp_1 = save_form_data_db($eloqua_form_id, $sw_id, null, $post_data, false);
            if($save_form_data_db_resp_1 > 0) {
                eloqua_notification_trigger(get_eloqua_notification_mail(eloqua_token())[0]['eloqua_notification_mail'], 'Eloqua - Form submit failed', 'The form with ID: ' . $sw_id . ' & Entry ID: ' . $save_form_data_db_resp_1 . ' is failed to submit to Eloqua', array('Content-Type: text/html; charset=UTF-8'));
                echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
            } else {
                eloqua_notification_trigger(get_eloqua_notification_mail(eloqua_token())[0]['eloqua_notification_mail'], 'Eloqua - Form submit failed', 'The form with Id ' . $sw_id . ' is failed to submit to Eloqua', array('Content-Type: text/html; charset=UTF-8'));
                echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
            }
        }
    } else {
        // code to get access_token from DB
        $_SESSION['access_token'] = get_eloqua_access_token_from_db()[0]['access_token'];
        // handle condition for, if token is not present in db.
        $eloqua_feedback = push_data_to_eloqua($eloqua_form_id, $post_data);
        if ($eloqua_feedback->{'id'}) {
            save_form_data_db($eloqua_form_id, $sw_id, $eloqua_feedback->{'id'}, $post_data, true);
            echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
        } else {
            $save_form_data_db_resp_1 = save_form_data_db($eloqua_form_id, $sw_id, null, $post_data, false);
            if($save_form_data_db_resp_1 > 0) {
                eloqua_notification_trigger(get_eloqua_notification_mail(eloqua_token())[0]['eloqua_notification_mail'], 'Eloqua - Form submit failed', 'The form with ID: ' . $sw_id . ' & Entry ID: ' . $save_form_data_db_resp_1 . ' is failed to submit to Eloqua', array('Content-Type: text/html; charset=UTF-8'));
                echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
            } else {
                eloqua_notification_trigger(get_eloqua_notification_mail(eloqua_token())[0]['eloqua_notification_mail'], 'Eloqua - Form submit failed', 'The form with Id ' . $sw_id . ' is failed to submit to Eloqua', array('Content-Type: text/html; charset=UTF-8'));
                echo json_encode(array("status" => 1, "message" => "Form Successfully pushed to Eloqua"));
            }
        }
    }
}
function get_eloqua_notification_mail($eloqua_token_table) {
	$eloqua_token_table_id = '1';
	global $wpdb;       
        return $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT eloqua_notification_mail from " . $eloqua_token_table . " WHERE id = %d ",$eloqua_token_table_id
            ), ARRAY_A
        );
}
function push_data_to_eloqua($eloqua_form_id, $post_data) {
    $authorization = 'Authorization: Bearer ' . $_SESSION['access_token'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://secure.p01.eloqua.com/API/REST/2.0/data/form/' . $eloqua_form_id);
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
    // curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json' , $authorization ));
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);		// type number
    curl_close($ch);
    
    if($httpcode === 200 || $httpcode === 201 || $httpcode === 202) {	//	valid response from Eloqua
        return json_decode($response);
    } else if($httpcode === 400) {
        return false;
    } else {
        return false;
    }
}
function save_form_data_db($eloqua_form_id, $sw_id, $form_submit_job_id, $post_data, $eloqua_status) {
    global $wpdb;
    $wpdb->insert(sw_forms_entry(), array(
        "sw_form_id" => $sw_id,
        "eloqua_form_id" => $eloqua_form_id,
        "form_submit_job_id" => $form_submit_job_id,
        "sw_form_user_entry" => json_encode($post_data),
        "eloqua_status" => $eloqua_status
    ));
    return $wpdb->insert_id;    // type integer, if success returns the id of the inserted row in db, if failure then returns 0
}
function get_saved_eloqua_form_fields_map($sw_form_id) {
    global $wpdb;
    if($sw_form_id) {
        return $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT eloqua_form_id,field_map  from " . my_form_table() . " WHERE id = %d ",$sw_form_id
            ), ARRAY_A
        );
    } else {
        return '';
    } 
}

function get_notification_settings($sw_form_id) {
    global $wpdb;
    if($sw_form_id) {
        return $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT * from " . sw_mail_notification() . " WHERE my_form_table_id = %d ",$sw_form_id
            ), ARRAY_A
        );
    } else {
        return '';
    }
}
function save_not_eloqua_form_data_db($send_mail_id, $sw_id, $subject_time_stamp, $post_data, $submit_status) {
    $past_data_modified = json_decode(json_encode($post_data));
    unset($past_data_modified -> {'g-recaptcha-response'});
    global $wpdb;
    $test = $wpdb->insert(sw_mail_forms_entry(), array(
        "my_form_table_id" => $sw_id,
        "form_submit_mail_id" => $send_mail_id,
        "sw_form_entry_subject" => $subject_time_stamp,
        "sw_form_user_entry" => json_encode($past_data_modified),
        "mail_submit_status" => $submit_status
    ));
}

function deliciousCamelcase($str) {
    $formattedStr = '';
    $re = '/
          (?<=[a-z])
          (?=[A-Z])
        | (?<=[A-Z])
          (?=[A-Z][a-z])
        /x';
    $a = preg_split($re, $str);
    $formattedStr = ucfirst(implode(' ', $a));
    return $formattedStr;
}
