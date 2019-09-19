<?php
global $eloqua_form_id;
global $form_submit_job_id;
global $sw_form_name;

$eloqua_form_id = isset($_GET['eloqua_form_id']) ? intval($_GET['eloqua_form_id']) : 0;        // type number
$form_submit_job_id = isset($_GET['form_submit_job_id']) ? $_GET['form_submit_job_id'] : '';
$sw_form_name = isset($_GET['sw_form_name']) ? $_GET['sw_form_name'] : '';

function print_eloqua_form_entry() {
    $get_eloqua_form_entry_resp = get_eloqua_form_entry();
    $sw_form_report = '<ul class="list-group list-group-flush entry-detail-list">';
    if($get_eloqua_form_entry_resp) {
        foreach ($get_eloqua_form_entry_resp as $key => $value) {

        }
    } else {
        $sw_form_report .= '<li class="list-group-item">No Entry</li>';
    }
    $sw_form_report .= '</ul>';

}
function get_eloqua_form_entry() {
    if ($_SESSION['access_token']) {
		$authorization = 'Authorization: Bearer ' . $_SESSION['access_token'];
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://secure.p01.eloqua.com/API/REST/2.0/data/form/' . $eloqua_form_id . '//formData/' . $form_submit_job_id . '?depth=minimal');
        // https://secure.p01.eloqua.com/API/REST/2.0/data/form/530/formData/5964499
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
	} else {
		return false;
	}
}

?>

<div style="padding: 0 10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="sw-forms-admin-heading">
                <h1 style="display: inline-block;">Forms Submit Report</h1>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo $sw_form_name ?></div>
                <div class="panel-body">
                    <?php if($eloqua_form_id && $form_submit_job_id){ print_eloqua_form_entry(); }?>
                </div>
            </div>

        </div>
    </div>
</div>
