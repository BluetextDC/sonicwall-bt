<?php
global $form_id;
global $form_name;
$form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;        // type number
$form_name = isset($_GET['name']) ? $_GET['name'] : '';

function print_swforms_list($sw_form_id) {
    $all_forms = get_swforms_list();
    $sw_forms_list = '<select id="sw_forms_select"><option value="">Select a form</option> ';
    if ($all_forms) {
        foreach ($all_forms as $key => $value) {
            $sw_forms_list .= '<option value="' . $value['id'] . '"' . ($sw_form_id == $value['id'] ? 'selected="selected"' : '') . '>' . $value['name'] . '</option>';
        }
    }
    $sw_forms_list .= "</select>";
    $sw_forms_list .= "<span style='margin-left: 20px;'><button type='button' id='sw_forms_selected_submit' class='btn btn-info'>Submit</button></span>";
    echo $sw_forms_list;
}
function get_swforms_list() {
    global $wpdb;
    $eloqua_submit = "1"; // $eloqua_submit
    $test = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT id,name,eloqua_submit from " . my_form_table() . " WHERE eloqua_submit = %d ",$eloqua_submit
        ), ARRAY_A
    );
    return $test;
}

function print_eloqua_forms_list($sw_form_id) {
    $eloqua_forms = get_eloqua_forms_list();
    $saved_eloqua_form_id = get_saved_eloqua_form_id($sw_form_id)[0]['eloqua_form_id'];        // type string
    $forms_list = '<select id="eloqua_forms_select" name="eloqua_form_id"><option value="">Select a form</option> ';
    if ($eloqua_forms) {
        foreach ($eloqua_forms->elements as $form) {
            $forms_list .= '<option value="' . $form->{'id'} . '"' . ($saved_eloqua_form_id == $form->{'id'} ? 'selected="selected"' : '') . '>' . $form->{'name'} . '</option>';
        }
    }
    $forms_list .= "</select>";
    echo $forms_list;
}
function get_saved_eloqua_form_id($sw_form_id) {
    global $wpdb;       
    if($sw_form_id) {
        return $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT eloqua_form_id from " . my_form_table() . " WHERE id = %d ",$sw_form_id
            ), ARRAY_A
        );
    } else {
        return '';
    } 
}
function get_eloqua_forms_list() {
    if ($_SESSION['access_token']) {
		$authorization = 'Authorization: Bearer ' . $_SESSION['access_token'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://secure.p01.eloqua.com/API/REST/2.0/assets/forms?depth=minimal');
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
			echo 'Response Code 400 Bad Request from get_base_url method';
			return false;
		} else {
			echo 'Unsupported Response Code';
			return false;
		}
	} else {
		return false;
	}
}

function print_swforms_fields($sw_form_id) {
    $eloqua_form_fields = json_decode(get_saved_eloqua_form_fields_map($sw_form_id)[0]['field_map']);
    $eloqua_fields = get_eloqua_form_fields_of_selected_swform($sw_form_id);
    $forms_field = '';
    if ($eloqua_form_fields) {
        foreach ($eloqua_form_fields as $field) {
            $forms_field .= '
                <div class="form-group col-md-12">
                    <label class="form-label-gap" for="' . $field->{'swform_feild_id'} . '">' . $field->{'swform_feild_name'} . '</label>
                    <select id="' . $field->{'swform_feild_id'} . '" name="' . $field->{'swform_feild_id'} . '">' . eloqua_field_builder($field->{'eloqua_field_id'}, $eloqua_fields) . '</select>
                </div>';
        }
    }
    echo $forms_field;
}
function eloqua_field_builder($eloqua_field_id, $eloqua_fields) {
    $eloqua_field_render = '<option value="">Select a form</option>';
    foreach ($eloqua_fields->elements as $eloqua_field) {
        $eloqua_field_render .= '<option value="' . $eloqua_field->{'id'} . '|' . $eloqua_field->{'name'} . '"' . ($eloqua_field_id == $eloqua_field->{'id'} ? 'selected="selected"' : '') . '>' . $eloqua_field->{'name'} . '</option>';
    }
    return $eloqua_field_render;
}
function get_saved_eloqua_form_fields_map($sw_form_id) {
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
function get_eloqua_form_fields_of_selected_swform($sw_form_id) {
    if ($_SESSION['access_token']) {
        $saved_eloqua_form_id = get_saved_eloqua_form_id($sw_form_id)[0]['eloqua_form_id'];        // type string
        if (!$saved_eloqua_form_id) {
            $saved_eloqua_form_id = '530';
        }
        if($saved_eloqua_form_id) {
            $authorization = 'Authorization: Bearer ' . $_SESSION['access_token'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://secure.p01.eloqua.com/API/REST/2.0/assets/form/' . $saved_eloqua_form_id . '?depth=partial');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            // curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json' , $authorization ));
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);		// type number
            curl_close($ch);
        }
		if($httpcode === 200 || $httpcode === 201 || $httpcode === 202) {	//	valid response from Eloqua
            return json_decode($response);
		} else if($httpcode === 400) {
			return false;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

?>
<br><br>
<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">Eloqua Feed Settings</div>
                    <div class="panel-body">
                        <div class="clearAfter">
                            <form id="eloqua_feed_settings">
                                <div class="form-row clearAfter">
                                    <div class="form-group col-md-12">
                                        <label for="sw_forms_select" class="form-label-gap">Select a SWForm</label>
                                        <?php print_swforms_list($form_id); ?>
                                    </div>
                                </div>
                                <?php if($form_id) { ?>
                                <div class="form-row clearAfter">
                                    <div class="form-group col-md-12">
                                        <label for="eloqua_forms_select" class="form-label-gap">SW Form Name</label>
                                        <input type="text" class="form-control" style="width: 194px; display: inline-block;" readonly value="<?php echo $form_name ?>" >
                                    </div>
                                </div>
                                <div class="form-row clearAfter">
                                    <div class="form-group col-md-12">
                                        <label for="eloqua_forms_select" class="form-label-gap">Select a Eloqua Form</label>
                                        <?php print_eloqua_forms_list($form_id); ?>
                                    </div>
                                </div>

                                <div class="form-row form-field-group clearAfter">
                                    <div class="form-group col-md-12">
                                        <label for="eloqua_forms_select" style="font-size: 15px; font-weight: bold; color: #747070;">Form Fields</label>
                                    </div>
                                    <input type="hidden" name="sw_form_id" value="<?php echo $form_id ?>" id="sw_form_id">
                                    <?php print_swforms_fields($form_id); ?>
                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn btn-info">Save</button>
                                    </div>
                                </div>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

