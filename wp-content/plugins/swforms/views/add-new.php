<?php
// echo time();        // 1561295394
$form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
global $wpdb;
$form_detail = $wpdb->get_row(
          $wpdb->prepare(
                    "SELECT * from ".my_form_table()." WHERE id = %d ",$form_id
                  ),ARRAY_A
        );

global $current_user; wp_get_current_user();
if ( is_user_logged_in() ) {
    $user_name = $current_user->display_name;
} else {
    $user_name = '';
}
?>
<script>
    var formjsonFromDb = <?php echo json_encode($form_detail); ?>
</script>

<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1>Add Form</h1>
            </div>
            <div class="panel panel-primary" id="createForm">
                <div class="panel-heading">Create Form</div>
                <div class="panel-body">
                    <div class="clearAfter">
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="formTitle">*Form Title</label>
                                    <input type="text" class="form-control" id="formTitle" value="<?php echo $form_detail['name'] ?>" placeholder="Title">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="formAuthor">Author</label>
                                    <input type="text" class="form-control" id="formAuthor" value="<?php if($form_id === 0) { echo $user_name; } else { echo $form_detail['author']; } ?>" placeholder="Author">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="formDescription">Form Description</label>
                                    <input type="text" class="form-control" id="formDescription" value="<?php echo $form_detail['comments'] ?>" placeholder="Description">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6" style="height: 55px;">
                                    <label for="formEloquaSubmit">Submit to Eloqua </label>
                                    <input type="checkbox" name="eloqua_submit" value="<?php echo $form_detail['eloqua_submit'] ?>" class="form-control" id="formEloquaSubmit" <?php echo ($form_detail['eloqua_submit']=='1' ? 'checked' : ''); ?> >
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr>

                    <div id="build-wrap"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">Generated Form</div>
                <div class="panel-body">
                    <div id="generatedFormHtml" class="clearAfter"><form action="" id="generated_form" class="sw-form-wrap" novalidate><div class="render-wrap"></div></form></div>
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

<script>
    jQuery(document).ready(function() {
        if(jQuery("#formEloquaSubmit").is(":checked")) {
            jQuery("#formSubmitMailId").attr('disabled','disabled');
            jQuery("#formSubmitMailIdWrap").hide();
        } else {
            jQuery("#formSubmitMailId").removeAttr('disabled');
            jQuery("#formSubmitMailIdWrap").show();
        }
        jQuery("#formEloquaSubmit").click(function() {
            if(jQuery(this).is(":checked")) {
                jQuery("#formSubmitMailId").attr('disabled','disabled');
                jQuery("#formSubmitMailIdWrap").hide();
            } else {
                jQuery("#formSubmitMailId").removeAttr('disabled');
                jQuery("#formSubmitMailIdWrap").show();
            }
        });
    });
</script>
