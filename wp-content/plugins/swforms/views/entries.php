
<?php
global $wpdb;
global $form_id;
global $sw_form_name;
global $form_esub;

$form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;        // $form_id type number
$form_esub = isset($_GET['esub']) ? intval($_GET['esub']) : 0;
if ($form_id && $form_esub === 1) {
    $form_entries = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT * from " . sw_forms_entry() . " WHERE sw_form_id = %d ",$form_id . "ORDER BY created_at DESC"
        ), ARRAY_A
    );
} else {
    $non_eloqua_form_entries = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT * from " . sw_mail_forms_entry() . " WHERE my_form_table_id = %d ",$form_id . "ORDER BY created_at DESC"
        ), ARRAY_A
    );
}

function print_swforms_list($sw_form_id) {
    global $sw_form_name;
    $all_forms = get_swforms_list();
    $sw_forms_list = '<select id="sw_form_entries"><option value="">Select a form</option> ';
    if ($all_forms) {
        foreach ($all_forms as $key => $value) {
            if($sw_form_id == $value['id']) {
                $sw_form_name = $value['name'];
            }
            $sw_forms_list .= '<option value="' . $value['id'] . '|' . $value['eloqua_submit'] . '"' . ($sw_form_id == $value['id'] ? 'selected="selected"' : '') . '>' . $value['name'] . '</option>'; /*  Note: $value['id'] will be a string value */
        }
    }
    $sw_forms_list .= "</select>";
    echo $sw_forms_list;
}

function get_swforms_list() {
    global $wpdb;
    return $wpdb->get_results(
        $wpdb->prepare(
                "SELECT id,name,eloqua_form_id,eloqua_submit from " . my_form_table() . " ORDER by id DESC", ""
        ), ARRAY_A
    );
}
?>

<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1 style="display: inline-block;">Form Entries</h1>
            </div>
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="form-row clearAfter">
                        <div style="padding-left: 0;" class="form-group col-md-12">
                            <label for="sw_forms_select">Select a Form: </label>
                            <?php print_swforms_list($form_id); ?>
                        </div>
                    </div>
                    <br>

                    <?php 
                    if($form_esub === 1) {
                    ?>
                        <!-- ********************** Eloqua entry table begins ********************** -->
                        <table id="sw_table_form_data" class="display" cellspacing="0" width="100%">
                            <thead class="text-capitalize">
                                <tr>
                            <?php
                            if (count($form_entries) > 0) {
                                ?>
                                <th>ID</th>
                                <?php
                                $temp2 = json_decode($form_entries[0]['sw_form_user_entry']); 
                                foreach ($temp2->{'fieldValues'} as $form_data_head) {
                                    ?>
                                    <th> <?php echo $form_data_head->{'name'} ?> </th>
                                <?php
                                }
                                ?> <th>Status</th><th>View</th> <?php
                            } else {
                                ?> <th>No Data</th> <?php 
                            }
                            ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if (count($form_entries) > 0) {
                                    foreach ($form_entries as $form_data) {
                                        $temp = json_decode($form_data['sw_form_user_entry']); 
                                        ?>
                                        <tr>
                                            <td><?php echo $form_data['id'] ?></td>
                                        <?php
                                        foreach ($temp->{'fieldValues'} as $field) {
                                        ?>
                                        <td><?php echo $field->{'value'}; ?></td>
                                        <?php
                                        }
                                        ?>
                                        <td><?php if($form_data['eloqua_status'] == 1) { echo 'Success'; } else { echo 'Failed'; } ?> </td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info" href="admin.php?page=form-entry-detail&eloqua_form_id=<?php echo $form_data['eloqua_form_id'] ?>&form_submit_job_id=<?php echo $form_data['form_submit_job_id'] ?>&sw_form_name=<?php echo $sw_form_name ?>"><span class="dashicons dashicons-visibility"></span></a>
                                        </td>
                                        </tr>
                                        <?php                                    
                                }
                                    
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="12">No list created</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- ********************** Eloqua entry table ends ********************** -->
                    <?php
                    } else {
                    ?>
                        <!-- ********************** Non Eloqua entry table begins ********************** -->
                        <table id="sw_non_eloqua_entry" class="display" cellspacing="0" width="100%">
                            <thead class="text-capitalize">
                                <tr>
                            <?php
                            if (count($non_eloqua_form_entries) > 0) {
                                ?>
                                <th>ID</th>
                                <th>To Address</th>
                                <th>Subject</th>
                                <?php
                                $temp3 = json_decode($non_eloqua_form_entries[0]['sw_form_user_entry']); 
                                foreach ($temp3 as $form_data_head => $form_data_body) {
                                    ?>
                                    <th> <?php echo $form_data_head ?> </th>
                                <?php
                                }
                                ?> <th>Status</th><th>View</th> <th class="padding-10" style="text-align:center;">Delete</th> <?php
                            } else {
                                ?> <th>No Data</th> <?php 
                            }
                            ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if (count($non_eloqua_form_entries) > 0) {
                                    foreach ($non_eloqua_form_entries as $form_data) {
                                        $temp4 = json_decode($form_data['sw_form_user_entry']); 
                                        ?>
                                        <tr>
                                            <td><?php echo $form_data['id'] ?></td>
                                            <td><?php echo $form_data['form_submit_mail_id'] ?></td>
                                            <td><?php echo $form_data['sw_form_entry_subject'] ?></td>
                                        <?php
                                        foreach ($temp4 as $form_data_head => $form_data_body) {
                                        ?>
                                        <td><?php echo $form_data_body; ?></td>
                                        <?php
                                        }
                                        ?>
                                        <td><?php if($form_data['mail_submit_status'] == 1) { echo 'Success'; } else if($form_data['mail_submit_status'] == 0) { echo 'Failed'; } else { echo 'Unknown'; } ?> </td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info" href="javascript:void(0);"><span class="dashicons dashicons-visibility"></span></a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-danger entryDelete" href="javascript:void(0)" data-id="<?php echo $form_data['id']; ?>">Delete</a>
                                        </td>
                                        </tr>
                                        <?php                                    
                                }
                                    
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="21">No list created</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- ********************** Non Eloqua entry table ends ********************** -->
                    <?php
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>

