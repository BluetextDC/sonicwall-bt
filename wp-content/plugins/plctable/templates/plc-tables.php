<?php 

	wp_cache_flush();

    $plc_request = new SWRequests();
    $plc_data = json_decode($plc_request->get_plc_tables() );

	$product_list = array();

	if ( !is_admin() ){

        // Define the custom sort function
        /*
	     function hardware_sort($a,$b) {
	          return $a['lod']<$b['lod'];
         }
         */

	}
     

	foreach ($plc_data->records as $rec) {
		$pname_array = explode(',', $rec->Product_Name__c);
		foreach ($pname_array as $pname) {
			$pname = trim( $pname );
			$pname = str_replace('  ', ' ', $pname);
			if ( !in_array($pname, $product_list) ){
				array_push($product_list, $pname );
			}
		}
	}

    $prooduct_list = sort($product_list, SORT_STRING);

	// Sort the multidimensional array
	$x = 0;
	foreach ($plc_data->records as $prec) {
		$p_name_array = explode(',', $prec->Product_Name__c);

		foreach ($p_name_array as $pnn) {
			$pnn = trim( $pnn );
			$pnn = str_replace('  ', ' ', $pnn);

			if ( $prec->Type__c == 'Software' ){
				$master_array[$pnn][$prec->Type__c][$x]['version'] = $prec->Model__c;
				$master_array[$pnn][$prec->Type__c][$x]['fsaof'] = $prec->Full_Support_as_of__c;
				$master_array[$pnn][$prec->Type__c][$x]['lsaof'] = $prec->Limited_Support_as_of__c;
				$master_array[$pnn][$prec->Type__c][$x]['sd'] = $prec->Support_Discontinued__c;
				$master_array[$pnn][$prec->Type__c][$x]['sortid'] = $prec->sorting_id__c;
				$x++;
            }

			if ( $prec->Type__c == 'Hardware' ){
				$master_array[$pnn][$prec->Type__c][$x]['model'] = $prec->Model__c;
				$master_array[$pnn][$prec->Type__c][$x]['lod'] = $prec->Last_order_day__c;
				$master_array[$pnn][$prec->Type__c][$x]['armb'] = $prec->ARM_Begin__c;
				$master_array[$pnn][$prec->Type__c][$x]['arme'] = $prec->ARM_End__c;
				$master_array[$pnn][$prec->Type__c][$x]['oneyldo'] = $prec->X1_Year_LDO__c;
				$master_array[$pnn][$prec->Type__c][$x]['lrmb'] = $prec->LRM_Begin__c;
				$master_array[$pnn][$prec->Type__c][$x]['lrme'] = $prec->LRM_End__c;
				$master_array[$pnn][$prec->Type__c][$x]['eos'] = $prec->End_of_Support__c;
				$master_array[$pnn][$prec->Type__c][$x]['sortid'] = $prec->sorting_id__c;
				// $master_array[$pnn][$prec->Type__c][$x]['last_modified_time'] = $prec->Last_Modified_Time__c;
				$x++;
			}
			if ( $prec->Type__c == 'Firmware' ){
				$master_array[$pnn][$prec->Type__c][$x]['release'] = $prec->Release__c;
				$master_array[$pnn][$prec->Type__c][$x]['model'] = $prec->Model__c;
				$master_array[$pnn][$prec->Type__c][$x]['type'] = $prec->Release_Type__c;
				$master_array[$pnn][$prec->Type__c][$x]['rd'] = $prec->Release_Date__c;
				$master_array[$pnn][$prec->Type__c][$x]['status'] = $prec->Status__c;
				$master_array[$pnn][$prec->Type__c][$x]['upgrade'] = $prec->Recommended_Upgrade__c;
				$master_array[$pnn][$prec->Type__c][$x]['eos'] = $prec->End_of_Support__c;
				$master_array[$pnn][$prec->Type__c][$x]['sortid'] = $prec->sorting_id__c;
				$x++;
			}
		}
	}
?>
<div id="plc-product-list-holder"></div>

<?php
	echo '<pre class="plc-formatted-data" style="display:none;">'. json_encode($master_array) .'</pre>';
	echo '<pre class="plc-product-list-array" style="display:none;">'.json_encode($product_list).'</pre>';
?>