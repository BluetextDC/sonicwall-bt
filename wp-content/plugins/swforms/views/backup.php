
<?php
global $wpdb;
global $form_id;
$form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;        // type number

function print_swforms_list($sw_form_id) {
    $all_forms = get_swforms_list();
    $sw_forms_list = '<select id="sw_form_entries"><option value="">Select a form</option> ';
    if ($all_forms) {
        foreach ($all_forms as $key => $value) {
            $sw_forms_list .= '<option value="' . $value['id'] . '"' . ($sw_form_id == $value['id'] ? 'selected="selected"' : '') . '>' . $value['name'] . '</option>';
        }
    }
    $sw_forms_list .= "</select>";
    // $sw_forms_list .= "<span style='margin-left: 20px;'><button type='button' id='sw_forms_selected_submit' class='btn btn-info'>Submit</button></span>";
    echo $sw_forms_list;
    /*
    Note: $value['id'] will be a string value
    */
}

function get_swforms_list() {
    global $wpdb;
    return $wpdb->get_results(
        $wpdb->prepare(
                "SELECT id,name from " . my_form_table() . " ORDER by id DESC", ""
        ), ARRAY_A
    );
}

function loadTable($sw_form_id) {
    if($sw_form_id) {
        
    }
}
loadTable($form_id);

?>

<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1 style="display: inline-block;">Form Entries</h1>
            </div>

            <div class="panel panel-primary">
                <!-- <div class="panel-heading">Forms Entries List</div> -->
                <div class="panel-body">
                    <div class="form-row clearAfter">
                        <div style="padding-left: 0;" class="form-group col-md-12">
                            <label for="sw_forms_select">Select a Form: </label>
                            <?php print_swforms_list($form_id); ?>
                        </div>
                    </div>
                    <br>
                    
                    <table id="sw_table_form_data" class="display" cellspacing="0" width="100%">
                        <thead class="text-capitalize">
                            <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Author</th>
                            <th>Created At</th>
                            <th>Comments</th>
                            <th>Eloqua Settings</th>
                            <th style="text-align:center;">Edit / Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                        
                                <tr>
                                    <td colspan="8">No list created</td>
                                </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- ********************* entries.php *********************** -->

<tr>
    <?php
    if (count($form_entries) > 0) {
        foreach ($form_entries->fieldValues as $obj) {
    ?>
        <th><?php echo $obj['name']; ?></th>
    <?php } ?>
        <th style="text-align:center;">View</th>
</tr>



<tbody>
                            <?php
                            if (count($form_entries) > 0) {
                                foreach ($form_entries as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['author']; ?></td>
                                        <td><?php echo $value['created_at']; ?></td>
                                        <td><?php echo $value['comments']; ?></td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info" href="admin.php?page=eloqua-form-settings&id=<?php echo $value['id'] ?>&name=<?php echo $value['name'] ?>"><span class="dashicons dashicons-admin-generic"></span></a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info" href="admin.php?page=add-new&id=<?php echo $value['id'] ?>">Edit</a>
                                            <a class="btn btn-danger btnformdelete" href="javascript:void(0)" data-id="<?php echo $value['id']; ?>">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="8">No list created</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>


<!-- ***************** sw_formthemelibrary.php ******************* -->
<!-- "sw_form_user_entry" => stripslashes(json_encode($post_data)), -->
