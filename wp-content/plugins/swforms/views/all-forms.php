<?php
global $wpdb;
$all_forms = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT * from " . my_form_table() . " ORDER by id DESC", ""
        ), ARRAY_A
);
?>
<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1 style="display: inline-block;">Forms</h1>
                <span><a href="admin.php?page=add-new" class="btn btn-primary">Add New</a></span>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">Forms List</div>
                <div class="panel-body">
                    <table id="allFormsTable" class="display" cellspacing="0" width="100%">
                        <thead class="text-capitalize">
                            <tr>
                            <th class="padding-10">ID</th>
                            <th class="padding-10">Name</th>
                            <th class="padding-10">Author</th>
                            <th class="padding-10">Created At</th>
                            <th class="padding-10">Comments</th>
                            <th class="padding-10">Form Settings</th>
                            <th class="padding-10" style="text-align:center;">Edit / Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if (count($all_forms) > 0) {
                                $i = 1;
                                foreach ($all_forms as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['author']; ?></td>
                                        <td><?php echo $value['created_at']; ?></td>
                                        <td><?php echo $value['comments']; ?></td>
                                        <td>
                                            <?php 
                                            if($value['eloqua_submit'] === '1') {
                                                ?> <a class="btn btn-info" href="admin.php?page=eloqua-form-settings&id=<?php echo $value['id'] ?>&name=<?php echo $value['name'] ?>" title="Eloqua feed settings"><span class="dashicons dashicons-admin-generic"></span></a> <?php
                                            } else {
                                                ?> <a class="btn btn-info" href="admin.php?page=email-notification&id=<?php echo $value['id'] ?>&name=<?php echo $value['name'] ?>" title="Notification settings"><span class="dashicons dashicons-email"></span></a> <?php
                                            }
                                            ?>
                                            
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
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
