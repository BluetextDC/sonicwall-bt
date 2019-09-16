<?php
    $code = $_GET['code'];
    $basic_auth_url = 'https://login.eloqua.com/id';
    $CLIENT_ID = "0d0f31c9-989a-4d86-9e8a-a2353be15795";
    $CLIENT_SECRET = "1yEy1RVU-JEy1BljwNSgdKiQOFNd6Inq0-C3SP5nzDSpleNPdvuM8oVc-aHK9HA7U1ROqNDLc-2njHp7qT23c3Ns1Ssb8mNgRZqs";
    // $URL = "https://login.eloqua.com/auth/oauth2/token";
    /*
    json_decode ( string $json [, bool $assoc = FALSE [, int $depth = 512 [, int $options = 0 ]]] ) : mixed
    Takes a JSON encoded string and converts it into a PHP variable, 
    JSON encoded string -> "{"site":{"id":1103843350,"name":"SonicWallSandbox"},"user":{"id":260,"username":"Sharath.Boregowda","displayName":"Sharath Boregowda","firstName":"Sharath","lastName":"Boregowda","emailAddress":"sboregowda@sonicwall.com"}}" 
    PHP variable -> stdClass Object ( [site] => stdClass Object ( [id] => 1103843350 [name] => SonicWallSandbox ) [user] => stdClass Object ( [id] => 260 [username] => Sharath.Boregowda [displayName] => Sharath Boregowda [firstName] => Sharath [lastName] => Boregowda [emailAddress] => sboregowda@sonicwall.com ) )

    json_encode() 

    */

/*
    // Curl Postfeilds
    $grant_code = 'code: ' . $code;
    $redirect_uri = 'redirect_uri: http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings';
    $grant_type = 'grant_type: authorization_code';

    // Base64 Encoding
    $ready_to_encode = $CLIENT_ID . ':' . $CLIENT_SECRET;
    $base64_encoded_data = base64_encode($ready_to_encode);

    // Curl Headers
    $content_type = 'Content-Type: application/json';
    $accept = 'Accept: application/json';
    $authorization = 'Authorization: Basic ' . $base64_encoded_data;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    // curl_setopt($ch, CURLOPT_POST, true);      // curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array($grant_code, $redirect_uri , $grant_type));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($content_type, $accept, $authorization));

    $response = curl_exec($ch);
    curl_close($ch);
    var_dump($response);

    */

    function generate_access_token_from_refresh_token($refresh_token) {
        $URL = 'https://login.eloqua.com/auth/oauth2/token';
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
        var_dump($response_access_token);
    }
    generate_access_token_from_refresh_token('MTEwMzg0MzM1MDoxY005Q1M5OFMxRWVYRERYS3JYMERpdnR+cXpMUEQ0M2gxUHdWcDdOS1kwRHFxSEJaVjlJUmpES3pMeXlLbXNaZ2pjQlk2Sk82WC1hUn5GSHB4bXRwdlMxTzc2akxaQnlRTzlt');

    /*
    $token_split_1 = explode("&",$response);
    $token_split_2 = $token_split_1[0];
    $token_split_3 = explode("=",$token_split_2);
    if($token_split_3[1]) {
        $_SESSION['eloqua_access_token'] = $token_split_3[1];
        ?>
        <script>pageRedirect('form-settings')</script>
        <?php
    } else {
        $_SESSION['eloqua_access_token'] = '';
        echo "<h3>Error in connection, Please try connecting again.</h3>";
    }
    */

    /*
    // $jsonData = json_decode('access_token=f58fb63333ea120bb8d9f3ecb9f91c28799b9287&scope=&token_type=bearer', true);

    if($data->access_token != "") {
        session_start();
        $_SESSION['eloqua_access_token'] = $data->access_token;
        header('Location: http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings');       // to redirect to a page in php, this will not work in wordpress
        exit;
    }
    */
    // $url = 'http://your-end-point';  
        // $foo = 'bar';
        // $post_data = array('email' => urlencode($foo));
    
        // $result = wp_remote_post( 'https://github.com/login/oauth/access_token', array( 'body' => $postParams, 'headers' => $headers ) );
        /*
        $response = wp_remote_post(
			$url, array(
				'headers'   => array(
					'Content-Type' => 'application/json',
				),
				'body'      => $args_string,
				'sslverify' => false,
				'timeout'   => $this->timeout,
			)
        );
        */

        /*
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Accept' => 'application/json',
            ),
            'httpversion' => '1.0',
            'sslverify' => false,
            'body' => array( 
                'code' => $_GET['code'],
                'redirect_uri' => 'http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=callback-page',
                'client_id' => $CLIENT_ID,
                'client_secret' => $CLIENT_SECRET,
                'grant_type' => 'authorization_code'
            )
        );
        $response = wp_remote_post( 'https://github.com/login/oauth/access_token', $args );
        var_dump($response);
        */

    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $response = curl_exec($ch);
    curl_setopt($ch);
    $data = json_decode($response);
    */

        /*
    $postParams = [
        'client_id' => $CLIENT_ID,
        'client_secret' => $CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => 'http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=form-settings',
        'grant_type' => 'authorization_code'
    ];

    $result = implode(',', $body);
    */

    /*
    $CLIENT_ID = "0d0f31c9-989a-4d86-9e8a-a2353be15795";
    $CLIENT_SECRET = "1yEy1RVU-JEy1BljwNSgdKiQOFNd6Inq0-C3SP5nzDSpleNPdvuM8oVc-aHK9HA7U1ROqNDLc-2njHp7qT23c3Ns1Ssb8mNgRZqs";
    $URL = "https://login.eloqua.com/auth/oauth2/token";
    $params = array(
        "code" => $code,
        "client_id" => $CLIENT_ID,
        "client_secret" => $CLIENT_SECRET,
        "redirect_uri" => 'http://localhost/my_first_plugin/wordpress/wp-admin/admin.php?page=callback-page',
        "grant_type" => "authorization_code"
    );
    $ready_to_encode = $CLIENT_ID . ':' . $CLIENT_SECRET;
    $base64_encoded_data = base64_encode($ready_to_encode);

    $ch = curl_init();
    curl_setopt($ch, constant("CURLOPT_" . 'URL'), $URL);
    curl_setopt($ch, constant("CURLOPT_" . 'POST'), true);
    curl_setopt($ch, constant("CURLOPT_" . 'POSTFIELDS'), $params);
    curl_setopt($ch, constant("CURLOPT_" . 'RETURNTRANSFER'), true);
    curl_setopt($ch, constant("CURLOPT_" . 'HTTPHEADER'), array(
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Basic ' . $base64_encoded_data
    ));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json' , $authorization ));
    $response = curl_exec($ch);
    curl_close($ch);

    var_dump($response);
    */
    
?>
