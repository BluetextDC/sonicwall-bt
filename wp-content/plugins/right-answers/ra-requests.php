<?php 
class RARequests {

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
 		//do stuff here if needed
 	}
     
    private $sol_id = "";
     
    public function simulateRADowntime($func)
    {
        return;
//        die("Server Down: ".$func);
    }
     

    public function get_new_solutions($page, $lang_override = false){

        $this->simulateRADowntime("get_new_solutions");
		$curl = curl_init();
        
        $language = $this->getRALanguage();
        
        if ($lang_override)
        {
            $language = $lang_override;
        }
        
        //Calculate the last day in RA date format
        $startDate = date('m/d/Y',strtotime("-1 days"));
        $endDate = date('m/d/Y');
          
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'search/?startDate='.$startDate.'&endDate='.$endDate.'&companyCode=sonicwall&appInterface=ss&collections=custom_SS&page=' . $page.'&language='.$language,
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return json_decode($response);
		}

 	}
    
 	public function search_for_solution($query_text, $page, $lang_override = false){

 		// 
        $this->simulateRADowntime("search_for_solution");
		$curl = curl_init();
        
        $language = $this->getRALanguage();
        
        if ($lang_override)
        {
            $language = $lang_override;
        }
        
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'search/?companyCode=sonicwall&appInterface=ss&collections=custom_SS&templates=template-sonicwall-Solutions&queryText=' . $query_text . '&page=' . $page.'&language='.$language,
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response;
		}

 	}

 	public function search_for_sub_solution($query_text, $sub_cat_name, $page){

        $this->simulateRADowntime("search_for_sub_solution");
        
        $language = $this->getRALanguage();
        
		$curl = curl_init();
        
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'search/?' . $this->companycode . '&' . $this->appinterface . '&collections=custom_SS&templates=template-sonicwall-Solutions&queryText=' . $query_text . '&taxonomyPath=' . $sub_cat_name . '&page=' . $page.'&language='.$language,
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response;
		}
 	}


 	public function get_single_solution($solution_id, $preload = false){

        if (!$solution_id)
        {
            return;
        }
        $key = "ra_single_solution_".$solution_id;
        
        
        $response = $response = getRACacheItem($key);
        
        
        if ( $response == false || $preload ) {
            
            $this->simulateRADowntime("get_single_solution: ".$key);

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $this->baseurl . 'solution/' . $solution_id . '?' . $this->companycode . '&' . $this->appinterface,
              CURLOPT_USERPWD => $this->username . ":" . $this->password,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_FRESH_CONNECT => true,
              CURLOPT_HTTPHEADER => array(
                "Accept-Encoding: application/gzip,deflate",
                "Connection: Keep-Alive",
                "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
                "cache-control: no-cache"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              return "cURL Error #:" . $err;
            } else {

            $cache_time = 900;

            if ($preload)
            {
                //Set the cache_time to 24 hours
                $cache_time = 86400;
            }

            setRACacheItem($key, $response, $cache_time);
              return $response;
            }
        }
        
        return $response;

 	}


 	public function get_frequent_searches(){

        $this->simulateRADowntime("get_frequent_searches");
        
		$curl = curl_init();

        $language = $this->getRALanguage();
        
        if ($language == "English")
        {
            curl_setopt_array($curl, array(
              CURLOPT_URL => $this->baseurl . 'topviews/company/?' . $this->companycode . '&' . $this->appinterface . '&collections=custom_SS&language='.$language,
              CURLOPT_USERPWD => $this->username . ":" . $this->password,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_FRESH_CONNECT => true,
              CURLOPT_HTTPHEADER => array(
                "Accept-Encoding: application/gzip,deflate",
                "Connection: Keep-Alive",
                "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
                "cache-control: no-cache"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              return "cURL Error #:" . $err;
            } else {
              return $response;
            }
        }
        else
        {
            $solutions = json_decode($this->search_for_solution("", 1));

           return json_encode($solutions->solutions);
        }

 	}

 	public function search_by_category($category_name, $page){

        $this->simulateRADowntime("search_by_category");
        
		$curl = curl_init();
        $language = $this->getRALanguage();
        
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'search/?' . $this->companycode . '&' . $this->appinterface . '&' . '&taxonomyPath=' . $category_name . '&page=' . $page.'&language='.$language.'&collections=custom_SS',
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response;
		}

 	}
     
    public function getRALanguage()
    {
        $language = 'English';
        
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            
            switch (ICL_LANGUAGE_CODE) {
                case "en-us":
                    $language = 'English';
                    break;
                case "zh-cn":
                    $language = 'Chinese';
                    break;
                case "fr-fr":
                    $language = 'French';
                    break;
                case "de-de":
                    $language = 'German';
                    break;
                case "ja-jp":
                    $language = 'Japanese';
                    break;
                case "ko-kr":
                    $language = 'Korean';
                    break;
                case "pt-br":
                    $language = 'Portuguese';
                    break;
                case "es-mx":
                    $language = 'Spanish';
                    break;
                case "it":
                    $language = 'Italian';
                    break;
                default:
                    $language = 'English';
            }
        }
        
        if (isset($_GET['lang_override']))
        {
            //Set a cookie to remember it
            setcookie('lang_override', $_GET['lang_override'], time() + (86400 * 30), "/"); 
            
            return $_GET['lang_override'];
        }
            
        if (isset($_COOKIE['lang_override']))
        {
            $language = $_COOKIE['lang_override'];
        }
        
        if (isset($_GLOBAL['lang_override']))
        {
            $language = $_GLOBAL['lang_override'];
        }
        
        return $language;
    }
     
    public function build_category_slug($category) {
        
        if ($category && $category->value)
        {
            return sanitize_title($category->value);
        }
        
        return false;
    }
    public function get_ra_categories($preload = false) {

        $language = $this->getRALanguage();
        
        $key = "ra_categories_".$language;
        
        $response = getRACacheItem($key);
            
        if ($response == false || $preload) {
            
            $this->simulateRADowntime("get_categories");

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $this->baseurl . 'search/?' . $this->companycode . '&' . $this->appinterface . '&status=published&page=1' .'&language='.$language,
              CURLOPT_USERPWD => $this->username . ":" . $this->password,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_FRESH_CONNECT => true,
              CURLOPT_HTTPHEADER => array(
                "Accept-Encoding: application/gzip,deflate",
                "Connection: Keep-Alive",
                "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
                "cache-control: no-cache"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            $response = json_decode($response)->browsePaths;


            curl_close($curl);

            if ($err) {
              return "cURL Error #:" . $err;
            } else {
                $cache_time = 900;

                if ($preload)
                {
                    //Set the cache_time to 24 hours
                    $cache_time = 86400;
                }
                setRACacheItem($key, $response, $cache_time);
              return $response;
            } 

        }
        
        return $response;
        
 	}

 	public function alert_search($page, $preload = false){

        $language = $this->getRALanguage();
        
        $key = "ra_alert_search_".$language."_".$page;
        
        $response = getRACacheItem($key);
        
        if ( $response == false || $preload ) {
            
            
            $response = $this->alert_search_language($page, $language);
            
            $cache_time = 900;
            if ($page == 1)
            {
                $cache_time = 300;
            }
            
            if ($preload)
            {
                //Set the cache_time to 24 hours
                $cache_time = 86400;
            }
            setRACacheItem($key, $response, $cache_time);
            
        }
        
        return $response;
        
 	}
     
    private function alert_search_language($page, $language)
    {
        $this->simulateRADowntime("alert_search");
        
 		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'search/?' . $this->companycode . '&' . $this->appinterface . '&collections=custom_SS;custom_Support&status=published&templates=template-sonicwall-Alerts&page=' . $page.'&language='.$language,
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
            
         $alerts = json_decode($response);
         
         if ($alerts && $alerts->solutions)
         {
             return $alerts;
         }
         else if ($language != "English")
         {
             //No alerts for this language, return english
             return $this->alert_search_language($page, "English");
         }
         else
         {
             return $alerts;
         }
         
		}    
    }

 	public function upvote_answer($accepted_sol){

 		$action = '&action=SOLVED';

        $this->simulateRADowntime("upvote_answer");
        
 		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->baseurl . 'log/solution/' . $accepted_sol . '?' . $this->companycode . '&' . $this->appinterface . $action,
		  CURLOPT_USERPWD => $this->username . ":" . $this->password,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_FRESH_CONNECT => true,
		  CURLOPT_HTTPHEADER => array(
		    "Accept-Encoding: application/gzip,deflate",
		    "Connection: Keep-Alive",
		    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response;
		}
 	}
    
    public function downvote_answer($downvote_email, $accepted_sol, $down_comment){
		$comment = 'comments=' . urlencode($down_comment);
		$commentTitle = 'commentTitle=';
		$emailAddress = 'emailAddress=' . urlencode($downvote_email);
		$parentId = 'parentID=0';
		$hiddenForm = 'hiddenFromSS=false';
		$this->simulateRADowntime("downvote_answer");
		   
		$curl = curl_init();
	   	curl_setopt_array($curl, array(
			CURLOPT_URL => $this->baseurl . 'comments/' . $accepted_sol . '?' . $this->companycode . '&' . $this->appinterface . '&' . $comment . '&' . $commentTitle . '&' . $emailAddress . '&' . $parentId . '&' . $hiddenForm,
			CURLOPT_USERPWD => $this->username . ":" . $this->password,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded",
				"Accept-Encoding: gzip,deflate",
				"Connection: Keep-Alive",
				"User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
				"Host: sonicwall.rightanswers.com",
				"cache-control: no-cache"
			),
	   ));
	   $response = curl_exec($curl);
	   $err = curl_error($curl);
	   curl_close($curl);
	   if ($err) {
		 return "cURL Error #:" . $err;
	   } else {
		 return $response;
	   }
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
		  CURLOPT_URL => "https://sonicwall.my.salesforce.com/services/data/v44.0/query/?q=SELECT+Id,Type__c,Product_Name__c,Model__c,ARM_Begin__c,ARM_End__c,End_of_Support__c,LRM_Begin__c,LRM_End__c,Last_order_day__c,X1_Year_LDO__c,Release__c,Release_Date__c,Release_Type__c,Status__c,Recommended_Upgrade__c,URL__c,Applicable_For__c,Full_Support_as_of__c,Limited_Support_as_of__c,Support_Discontinued__c+FROM+PLC_Table__c",
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
