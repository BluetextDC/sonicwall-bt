<?php 
$basic_auth_url = 'https://login.eloqua.com/id';
global $CLIENT_ID;
$CLIENT_ID = "0d0f31c9-989a-4d86-9e8a-a2353be15795";
global $CLIENT_SECRET;
$CLIENT_SECRET = "1yEy1RVU-JEy1BljwNSgdKiQOFNd6Inq0-C3SP5nzDSpleNPdvuM8oVc-aHK9HA7U1ROqNDLc-2njHp7qT23c3Ns1Ssb8mNgRZqs";
global $URL;
$URL = "https://login.eloqua.com/auth/oauth2/token";
$rest_api_version = '2.0';
$_oauth_token_url = 'https://login.eloqua.com/auth/oauth2/token';
$urls = '';

if($_GET['code']) {
	$code = $_GET['code'];
}

if($code) {
	$params = array(
        "code" => $code,
        "redirect_uri" => "http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings",
        "grant_type" => "authorization_code"
	);

	// Base64 Encoding
	$ready_to_encode = $CLIENT_ID . ':' . $CLIENT_SECRET;
	$base64_encoded_data = base64_encode($ready_to_encode);

	// Curl Headers
	$content_type = 'Content-Type: application/json';
	$accept = 'Accept: application/json';
	$authorization_primary = 'Authorization: Basic ' . $base64_encoded_data;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_POST, 1);      // curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8', 'Accept: application/json' , $authorization_primary));
	/*
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Basic ' . $base64_encoded_data
	));
	*/
	$response_access_token = curl_exec($ch);
	curl_close($ch);
	
	if ($response_access_token['access_token']) {
		$eloqua_token_update_status = update_eloqua_token($response_access_token);
		if ($eloqua_token_update_status) {
			// reload the page (so that $_SESSION['access_token'] gets the value from the DB & this inturn calls print_eloqua_status() method - this inturn will get the data center url from eloqua & store it in DB & session)
		}
	} else {
		// Do Nothing
	}
	
}

function update_eloqua_token($response_access_token_data) {
	global $wpdb;
	$update_eloqua_token_db_response = $wpdb->update(eloqua_token(), array(
		"access_token" => $response_access_token_data['access_token'],
		"token_type" => $response_access_token_data['token_type'],
		"expires_in" => $response_access_token_data['expires_in'],
		"refresh_token" => $response_access_token_data['refresh_token'],
		"created_at" => null
			), array(
		"id" => 1
	));
	if ($update_eloqua_token_db_response == 1) { 
		return true;
	} else {
		return false;
		// write inside the log file - token update failed.
	}
}

// Returns Basic Auth URL - https://login.eloqua.com/id
function get_auth_url() {
	return $basic_auth_url;
}

// Function to get base_url (Eloqua data center URL)
function get_base_url($visit) {	
	// echo $visit;
	if ($_SESSION['access_token']) {
		$authorization = 'Authorization: Bearer ' . $_SESSION['access_token'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://login.eloqua.com/id');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
		// curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json' , $authorization ));
		$base_url_response = curl_exec($ch);	// if token is expired then we get 'Not authenticated.' with type string.
		// $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);		// type number
		curl_close($ch);
		$response_object_base_url = json_decode($base_url_response);
		if($response_object_base_url === "Not authenticated.") {
			return false;
		} else {
			return $response_object_base_url;
		}
	} else {
		return false;
	}
	// var_dump($_SESSION['base_url_data']);
}

//  Function to test eloqua connecion
function test_eloqua_connection() {
	$base_url_response = get_base_url('First time');
	if($base_url_response && $base_url_response->{'urls'}->{'base'}) {
		// update the DB for base_url_data
		$_SESSION['data_center_url'] = $base_url_response->{'urls'}->{'base'};
		$db_data_center_resp = update_db_eloqua_data_center_url($base_url_response->{'urls'}->{'base'});
		if ($db_data_center_resp) {
			return $base_url_response->{'urls'}->{'base'};
		} else {
			return false;
		}
	} else {
		// call the method to get the new access token from the refresh token (update session storage & db) & call the get_base_url() method again
		$refresh_token = get_referesh_token(eloqua_token());
		if ($refresh_token) {
			$new_token = generate_access_token_from_refresh_token($refresh_token);
			if($new_token) {
				$new_eloqua_token_status = update_eloqua_token($new_token);
				if($new_eloqua_token_status) {
					$_SESSION['access_token'] = $new_token['access_token'];
					$base_url_response2 = get_base_url('Second Time');
					if($base_url_response2->{'urls'}->{'base'}) {
						$_SESSION['data_center_url'] = $base_url_response2->{'urls'}->{'base'};
						$db_data_center_resp2 = update_db_eloqua_data_center_url($base_url_response->{'urls'}->{'base'});
						if ($db_data_center_resp2) {
							return $base_url_response->{'urls'}->{'base'};
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			// Log - No refresh token in DB
			return false;
		}
		return false;
	}
}
function update_db_eloqua_data_center_url($data_center_url) {
	global $wpdb;
	$update_db_eloqua_data_center_url_resp1 = $wpdb->update(eloqua_token(), array(
		"data_center_url" => '',
			), array(
		"id" => '1'
	));
	$update_db_eloqua_data_center_url_resp2 = $wpdb->update(eloqua_token(), array(
		"data_center_url" => $data_center_url,
			), array(
		"id" => '1'
	));
	if($update_db_eloqua_data_center_url_resp2 == 1) {
		return true;
	} else {
		return false;
	}
}
function get_referesh_token($eloqua_token_table) {
	$eloqua_token_table_id = '1';
	global $wpdb;       
        return $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT refresh_token from " . $eloqua_token_table . " WHERE id = %d ",$eloqua_token_table_id
            ), ARRAY_A
        );
}
function generate_access_token_from_refresh_token($refresh_token) {
	global $URL;
	global $CLIENT_ID;
	global $CLIENT_SECRET;
	$params_refresh = array(
		"refresh_token" => $refresh_token,
        "redirect_uri" => "http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings",
		"grant_type" => "refresh_token",
		"scope" => "full"
	);
	// Base64 Encoding
	$ready_to_encode = $CLIENT_ID . ':' . $CLIENT_SECRET;
	$base64_encoded_data = base64_encode($ready_to_encode);

	// Curl Headers
	$content_type = 'Content-Type: application/json';
	$accept = 'Accept: application/json';
	$authorization_refresh = 'Authorization: Basic ' . $base64_encoded_data;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_POST, 1);      // curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params_refresh);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json' , $authorization_refresh));
	/*
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Basic ' . $base64_encoded_data
	));
	*/
	$response_access_token = curl_exec($ch);
	curl_close($ch);
	if($response_access_token['access_token']) {
		return $response_access_token;
	} else {
		return false;
	}
}


function setup_urls($urls_data) {
	global $urls;
	$rest_urls = array();
	foreach ( $urls_data->{'apis'}->{'rest'} as $key => $rest_url ) {
		$rest_urls[ $key ] = str_replace( '{version}', '2.0', $rest_url );		// ToDo declare this '2.0' in a global variable 
	}
	$urls = $rest_urls;
	return true;
}

function print_eloqua_status() {
	$html = '';
	$connection_status = test_eloqua_connection();
	if ($connection_status) {
		$html = 'Connected';
	} else {
		$html = 'Disconnected';
	}
	echo $html;
}

function settings_eloqua_status() {
	if($_SESSION['access_token']) {
		$html = '<li class="list-group-item">
			<b>Logout: </b>
			<span><button type="button" onclick="eloquaSignout()" class="btn button-custom">Disconnect Eloqua</button></span>
		</li>';
	} else {
		$html = '<li class="list-group-item">
			<b>Login: </b>
			<span><a href="https://login.eloqua.com/auth/oauth2/authorize?response_type=code&client_id=0d0f31c9-989a-4d86-9e8a-a2353be15795&redirect_uri=http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings" class="btn button-custom">Authenticate with your Eloqua Account</a></span>
		</li>';
	}
	echo $html;
}

function settings_eloqua_test() {
	$html = '<li class="list-group-item">
				<b>Test Eloqua Connection: </b>
				<span><button type="button" onclick="testEloquaConnection()" class="btn button-custom">Test Eloqua Connection</button></span>
			</li>';
	echo $html;
}

function error($msg) {
	$response = [];
	$response['success'] = false;
	$response['message'] = $msg;
	return json_encode($response);
}

function eloquaSignout() {
	// remove access token from session & remove access token from DB.
	echo 'Eloqua connection Terminated';
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

?>
<br>
<div class="sw_forms_settings">
	<ul class="nav nav-tabs">
		<li class=""><a href="#tab-1">Settings</a></li>
		<li class="active"><a href="#tab-2">Eloqua</a></li>
		
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane">
			<p>Settings</p>
		</div>
		<div id="tab-2" class="tab-pane active">
			<ul class="list-group">
				<?php if($_SESSION['access_token']) { ?>
					<li class="list-group-item">
						<b>Eloqua Connection Status: </b>
						<span>
							<!-- <div class="led led-green"></div> -->
							<?php print_eloqua_status() ?>
						</span>
					</li>
				<?php } ?>
				<?php
				settings_eloqua_status();
				if ($_SESSION['access_token']) {settings_eloqua_test();}
				?>
				<li class="list-group-item">
					<b>Eloqua Notification Mail <span title="Notification mail will be sent to this email address when eloqua goes down or when a form submission is failed." class="dashicons dashicons-editor-help"></span>: </b>
					<span><input type="text" id="update_eloqua_notification_mail" value="<?php echo get_eloqua_notification_mail(eloqua_token())[0]['eloqua_notification_mail']; ?>" /> <span class="dashicons dashicons-image-rotate update_eloqua_icon" id="update_eloqua_notification_call"></span>
				</li>
				<li class="list-group-item">
					<b>Auth Token: </b>
					<span><input type="text" value="<?php echo $_SESSION['access_token'] ?>" /></span>
				</li>
			</ul>
		</div>
	</div>
</div>
