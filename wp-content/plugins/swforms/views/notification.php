
<?php
global $wpdb;
global $form_id;
global $form_name;
$form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;        // type number
$form_name = isset($_GET['name']) ? $_GET['name'] : '';

function print_swforms_list($sw_form_id) {
    $all_forms = get_swforms_list();
    $sw_forms_list = '<select id="sw_notification_form_select"><option value="">Select a form</option> ';
    if ($all_forms) {
        foreach ($all_forms as $key => $value) {
            $sw_forms_list .= '<option value="' . $value['id'] . '"' . ($sw_form_id == $value['id'] ? 'selected="selected"' : '') . '>' . $value['name'] . '</option>';
        }
    }
    $sw_forms_list .= "</select>";
    $sw_forms_list .= "<span style='margin-left: 20px;'><button type='button' id='sw_forms_notification_submit' class='btn btn-info'>Submit</button></span>";
    echo $sw_forms_list;
}
function get_swforms_list() {
    global $wpdb;
    $eloqua_submit = "0"; // $eloqua_submit
    $test = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT id,name,eloqua_submit from " . my_form_table() . " WHERE eloqua_submit = %d ",$eloqua_submit
        ), ARRAY_A
    );
    return $test;
}
function print_notification_fields($sw_form_id) {
    $notification_fields = get_saved_notification_fields($sw_form_id)[0];
    $forms_field = '';
    $forms_field .= '
        <div class="form-group col-md-12">
            <span class="notification_title_icon dashicons dashicons-editor-help" title="Multiple mail address should be seperated by a comma"></span>    
            <label class="form-label-gap notification_height_correction">To Address *</label>
            <input type="email" style="width: 300px;" name="form_to_mail_id" value="'. ($notification_fields['form_to_mail_id'] ? $notification_fields['form_to_mail_id'] : '') .'" />
        </div>
        <div class="form-group col-md-12">
        <span class="notification_title_icon dashicons dashicons-editor-help" title="Multiple mail address should be seperated by a comma"></span>
            <label class="form-label-gap notification_height_correction">CC Address</label>
            <input type="email" style="width: 300px;" name="form_cc_mail_id" value="'. ($notification_fields['form_cc_mail_id'] ? $notification_fields['form_cc_mail_id'] : '') .'" />
        </div>
        <div class="form-group col-md-12">
        <span class="notification_title_icon dashicons dashicons-editor-help" title="Multiple mail address should be seperated by a comma"></span>
            <label class="form-label-gap notification_height_correction">BCC Address</label>
            <input type="email" style="width: 300px;" name="form_bcc_mail_id" value="'. ($notification_fields['form_bcc_mail_id'] ? $notification_fields['form_bcc_mail_id'] : '') .'" />
        </div>
        <div class="form-group col-md-12">
            <label class="form-label-gap" style="float: left;">Subject</label>
            <textarea name="form_subject" rows="4" cols="50" style="display: inline-block;">'. ($notification_fields['form_subject'] ? $notification_fields['form_subject'] : '') .'</textarea>
        </div>
        <div class="form-group col-md-12">
            <label class="form-label-gap" style="float: left;">After body message</label>
            <textarea name="form_after_body" rows="4" cols="50" style="display: inline-block;">'. ($notification_fields['form_after_body'] ? $notification_fields['form_after_body'] : '') .'</textarea>
        </div>
        ';
    echo $forms_field;
}
function get_saved_notification_fields($sw_form_id) {
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
?>

<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1 style="display: inline-block;">Email Notification Setings</h1>
            </div>
            <div class="panel panel-primary">
                <div class="panel-body">

                    <div class="form-row clearAfter">
                        <div style="padding-left: 0;" class="form-group col-md-12">
                            <label for="sw_forms_select">Select a Form  </label>
                            <?php print_swforms_list($form_id); ?>
                        </div>
                    </div>
                    <br>
                    <div class="clearAfter">
                        <form id="notification_settings" novalidate>
                            <div class="form-row form-field-group clearAfter">
                                <div class="form-group col-md-12">
                                    <label for="eloqua_forms_select" style="font-size: 15px; font-weight: bold; color: #747070;">Notification Fields</label>
                                </div>
                                <input type="hidden" name="sw_form_id" value="<?php echo $form_id ?>" />
                                <?php if($form_id) { ?>
                                <?php print_notification_fields($form_id); ?>
                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-info">Save</button>
                                </div>
                                <?php } ?>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <div id='swforms-loading' class='swforms-loading sw-hide'>
        <div class='swforms-loading-content'>
            <div class='swforms-loading-class'></div>
        </div>
    </div>
</div>

