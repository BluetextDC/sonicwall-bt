<?php

class SWRequests {
    private $username = 'apitest';
 	private $password = 'nC0@jC4uIJ3ng';

 	private $sfuser = 'apipuser@sonicwall.com';
 	private $sfpwd = 's0nicw@llap!p';

 	private $sfclientid = '3MVG9szVa2RxsqBYHykZ1lGfxu0Df4HxP22v8aiY0onbl1Z4Ie5f1OHXCvX9tQvnW4OTrNVOq_Kbs9U_fuWHP';
 	private $sfclientsecret = '7807290639959773883';

 	private $btoken = '';

 	// public $baseurl = 'https://sonicwallstg.rightanswers.com/portal/api/rest/';
 	public $baseurl = 'https://sonicwall.rightanswers.com/portal/api/rest/';

 	public $companycode = 'companyCode=sonicwall';
 	public $appinterface = 'appInterface=ss';

 	public function __construct() { 
 		// do stuff here if needed
    }
    public function simulateRADowntime($func) {
        return;
        // die("Server Down: ".$func);
    }

    public function salesforce_refresh() {
	    $params = '&grant_type=password' . 
	        '&client_id=' . $this->sfclientid .
	        '&client_secret=' . $this->sfclientsecret . 
	        '&username=' . $this->sfuser . 
	        '&password=' . $this->sfpwd;
	    
	    $token_url = "https://login.salesforce.com/services/oauth2/token";
	    
        $this->simulateRADowntime("salesforce_refresh");
        
	    $curl = curl_init();
	    curl_setopt_array($curl, array( CURLOPT_URL => $token_url,
	    CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $params,
		) );
	    $json_response = curl_exec($curl);
	    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    if ( $status != 200 ) {
	    	echo 'token_url = ' . $token_url . "\n";
	    	// echo 'params = ' . $params . "\n";
	        echo 'response = ' . $json_response . "\n";
	        die("Error: call to URL $token_url failed with status $status\n");
	    }
	    curl_close($curl);
	    
	    $result = json_decode($json_response);
	   
	    return $result->access_token;
	}

    public function get_plc_tables(){

        if ( empty( $this->btoken ) ) {
            $this->btoken = $this->salesforce_refresh();
        }

       $this->simulateRADowntime("get_plc_tables");
       
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => "https://sonicwall.my.salesforce.com/services/data/v44.0/query/?q=SELECT+Id,Type__c,Product_Name__c,Model__c,ARM_Begin__c,ARM_End__c,End_of_Support__c,LRM_Begin__c,LRM_End__c,Last_order_day__c,X1_Year_LDO__c,Release__c,Release_Date__c,Release_Type__c,Status__c,Recommended_Upgrade__c,URL__c,Applicable_For__c,Full_Support_as_of__c,Limited_Support_as_of__c,Support_Discontinued__c,LastModifiedById,Last_Modified_Time__c,sorting_id__c+FROM+PLC_Table__c",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_POSTFIELDS => "",
         CURLOPT_HTTPHEADER => array(
           "Authorization: Bearer " . $this->btoken,
           $this->sfuser . ":" . $this->sfpwd,
           "cache-control: no-cache"
         ),
       ));
    /*
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sonicwall.my.salesforce.com/services/data/v44.0/query/?q=SELECT+%2A+FROM+PLC_Table__c",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer " . $this->btoken,
          $this->sfuser . ":" . $this->sfpwd,
          "cache-control: no-cache"
        ),
      ));
      */

       $response = curl_exec($curl);

       $err = curl_error($curl);

       curl_close($curl);

       if ($err) {
         return "cURL Error #:" . $err;
       } else {
         return $response;
       }




    }
     
}