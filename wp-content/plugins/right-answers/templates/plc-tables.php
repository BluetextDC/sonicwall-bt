<?php 

	$plc_request = new RARequests();
	$plc_data = json_decode($plc_request->get_plc_tables() );

	$product_list = array();

	if ( !is_admin() ){

		// Define the custom sort function
	     function hardware_sort($a,$b) {
	          return $a['lod']<$b['lod'];
	     }

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
     // usort($product_list, "custom_sort");

	// print_r($product_list);
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
				$x++;
			}
			if ( $prec->Type__c == 'Firmware' ){
				// var_dump($prec->Model__c);
				$master_array[$pnn][$prec->Type__c][$x]['release'] = $prec->Release__c;
				$master_array[$pnn][$prec->Type__c][$x]['model'] = $prec->Model__c;
				$master_array[$pnn][$prec->Type__c][$x]['type'] = $prec->Release_Type__c;
				$master_array[$pnn][$prec->Type__c][$x]['rd'] = $prec->Release_Date__c;
				$master_array[$pnn][$prec->Type__c][$x]['status'] = $prec->Status__c;
				$master_array[$pnn][$prec->Type__c][$x]['upgrade'] = $prec->Recommended_Upgrade__c;
				$master_array[$pnn][$prec->Type__c][$x]['eos'] = $prec->End_of_Support__c;
				$x++;
			}
		}
	}
	
?>
<div id="plc-product-list-holder">
<select name="product_list" id="product-selector">
	<option value="">Select a product to view its lifecycle</option> 
<?php 
	foreach ($product_list as $prod_name) {
		echo '<option value="' . str_replace( ')', '', str_replace( '(', '', str_replace(' ', '-', strtolower( trim( $prod_name ) ) ) ) ) . '">' . $prod_name . '</option>';
	}
?>
</select>
</div>

<div id="product-lifestyle-container-holder">

<?php
	$prod_title = '<div id="product-title-holder">';
	$types = '<div id="product-type-holder">';
	$tables = '<div id="product-table-holder">';
	$software = $hardware = $firmware = '';
	
	foreach ($master_array as $mk => $mv) {
		$pt = str_replace('  ', ' ', $mk);
		$pt_id = str_replace( '(', '', str_replace( ')', '', str_replace(' ', '-', strtolower( trim( $pt ) ) ) ) );
		$prod_title .= '<p id="' . str_replace(' ', '-', strtolower( trim( $pt ) ) ) . '" class="plc-product-name">' . $pt . '</p>';
		foreach ($mv as $kk => $kv) {
			if ( $kk == 'Software') {
				$software .= '<div class="plc-product-type software '. $pt_id . '"><a class="button" href="#" id="' . $pt_id . '">' . $kk . '</a></div>';
				$tables .= '<table id="' . $pt_id . '-' . str_replace(' ', '-', strtolower($kk) ) . '" class="plc-product-data-table"><thead>';
				$tables .= '<tr><th id="software-version">Version</th><th id="software-full-support-date" class="table-date">Full Support Date <span class="has-tip">i</span></th><th id="software-limited-support-date" class="table-date">Limited Support Date <span class="has-tip">i</span></th><th id="software-support-discontinued-date" class="table-date">Support Discontinued Date <span class="has-tip">i</span></th></tr></thead>';
				foreach ($kv as $pr) {
					$tables .= '<tr>';
					foreach ($pr as $pd) {
						$tables .= '<td>' . $pd . '</td>';
					}
					$tables .= '</tr>';
				}
				$tables .= '</table>';
			}
			if ( $kk == 'Hardware' ) {
				$hardware .= '<div class="plc-product-type hardware '. $pt_id . '"><a href="#" id="' . $pt_id . '">' . $kk . '</a></div>';
				$tables .= '<table id="' . $pt_id . '-' . str_replace(' ', '-', strtolower($kk) ) . '" class="plc-product-data-table"><thead>';
				$tables .= '<tr><th id="hardware-model">Model</th><th id="hardware-last-order-day" class="table-date">Last Order Day <span class="has-tip">i</span></th><th id="hardware-arm-begin" class="table-date">ARM Begin <span class="has-tip">i</span></th><th id="hardware-arm-end" class="table-date">ARM End <span class="has-tip">i</span></th><th id="hardware-one-year-ldo" class="table-date">1 Year LDO <span class="has-tip">i</span></th><th id="hardware-lrm-begin" class="table-date">LRM Begin <span class="has-tip">i</span></th><th id="hardware-lrm-end" class="table-date">LRM End <span class="has-tip">i</span></th><th id="hardware-end-of-support" class="table-date">End of Support <span class="has-tip">i</span></th></tr></thead>';
				usort($kv, "hardware_sort");
				foreach ($kv as $pr) {
					$tables .= '<tr>';
					foreach ($pr as $pd) {
						$tables .= '<td>' . $pd . '</td>';
					}
					$tables .= '</tr>';
				}
				$tables .= '</table>';
			}
			if ( $kk == 'Firmware' ) {
				$firmware .= '<div class="plc-product-type firmware '. $pt_id . '"><a href="#" id="' . $pt_id . '">' . $kk . '</a></div>';
				$tables .= '<table id="' . $pt_id . '-' . str_replace(' ', '-', strtolower($kk) ) . '" class="plc-product-data-table"><thead>';
				$tables .= '<tr><th id="firmware-release">Release</th><th id="firmware-model">Model</th><th id="firmware-type">Type <span class="has-tip">i</span></th><th id="firmware-release-date" class="table-date">Release Date <span class="has-tip">i</span></th><th id="firmware-status">Status <span class="has-tip">i</span></th><th id="firmware-recommended-upgrade">Recommended Upgrade <span class="has-tip">i</span></th><th id="firmware-eos-date" class="table-date">EOS Date <span class="has-tip">i</span></th></tr></thead>';
				// need to sort the $kv array by release for firmware only
				arsort($kv);
				foreach ($kv as $pr) {
					// var_dump($pr); echo '<hr />';
					$tables .= '<tr>';
					foreach ($pr as $pd) {
						$tables .= '<td>' . $pd . '</td>';
					}
					$tables .= '</tr>';
				}
				$tables .= '</table>';
			}
			
		}
	}

	$tooltips = '<div id="plc-tooltips-holder">';
	$tooltips .= '<div id="software-full-support-date-tooltip">Fully supported, generally available release/version. Enhancement requests for this release are accepted and may be considered for future releases. Maintenance releases and/or hot fixes are periodically made available for this release. Release/version is fully supported by both Support and Development. Release/version is available for download from Support Portal.</div>';
	$tooltips .= '<div id="software-limited-support-date-tooltip">Limited Support Support is available for this release/version, and we will use best efforts to provide known workarounds or fixes. No new code fixes will be generated except under extreme circumstances and at SonicWall\'s discretion. Enhancement requests are not accepted. Customers are encouraged to plan an upgrade to a release/version on “Full Support”.</div>';
	$tooltips .= '<div id="software-support-discontinued-date-tooltip">Discontinued versions which are retired or discontinued. No new patches or fixes will be created for this release. Not available for download from Support Portal. Support will be provided to assist with upgrading to a supported version. Support is not obligated to provide assistance on this version of the product.</div>';
	$tooltips .= '<div id="hardware-last-order-day-tooltip">Last Order Day is the last day to order the product from SonicWall and signifies SonicWall\'s intent to start the end of life process. The duration of this phase is variable and depends on numerous factors including material availability, SonicWall and channel inventory and end-user demand. Last Day Order is informational only; products in this phase are active. SonicWall continues to sell support contracts.</div>';
	$tooltips .= '<div id="hardware-arm-begin-tooltip">Active Retirement Mode is an announcement by SonicWall to indicate that it is no longer actively manufacturing or selling the product. Products in ARM are removed from all price lists and marketing collateral at this time. Support contracts for products in this phase will remain on price lists and will continue to be available for purchase until the phase has ended. During this time SonicWall may release a limited number of new features and will issue bug fixes only to the latest version of firm</div>';
	$tooltips .= '<div id="hardware-arm-end-tooltip">Active Retirement Mode is an announcement by SonicWall to indicate that it is no longer actively manufacturing or selling the product. Products in ARM are removed from all price lists and marketing collateral at this time. Support contracts for products in this phase will remain on price lists and will continue to be available for purchase until the phase has ended. During this time SonicWall may release a limited number of new features and will issue bug fixes only to the latest version of firm</div>';
	$tooltips .= '<div id="hardware-one-year-ldo-tooltip">1-Year Support Last Day Order represents the final day to purchase a 1-year support contract or subscription service that bundles support from SonicWall. Partners and customers may purchase and activate the 1-year support contract so that the product will be eligible to receive support until the product has reached End of Support.</div>';
	$tooltips .= '<div id="hardware-lrm-begin-tooltip">Limited Retirement Mode (LRM) is an announcement by SonicWall to indicate that it will no longer develop or release firmware updates or new features for these products. Software and firmware support for products in LRM is limited to critical bugs and security vulnerabilities. Software/firmware support and hardware warranty are available throughout LRM for products with an active support contract. The duration of this phase is three years beginning one day after the end of Active Retirement Mode.</div>';
	$tooltips .= '<div id="hardware-lrm-end-tooltip">Limited Retirement Mode (LRM) is an announcement by SonicWall to indicate that it will no longer develop or release firmware updates or new features for these products. Software and firmware support for products in LRM is limited to critical bugs and security vulnerabilities. Software/firmware support and hardware warranty are available throughout LRM for products with an active support contract. The duration of this phase is three years beginning one day after the end of Active Retirement Mode.</div>';
	$tooltips .= '<div id="hardware-end-of-support-tooltip"> End of Support (EOS) is an announcement by SonicWall to indicate that it will no longer provide technical support, firmware updates/upgrades or hardware replacement for the product, and that all remaining unique inventory or materials will become unavailable. SonicWall may continue to offer security service subscriptions such as Content Filtering and Intrusion Prevention during the End of Support phase, but it will no longer provide technical support for the product or any security service running on it. Should a technical issue arise on one of the subscription services that is offered during the End of Support phase, customers may be required to transition to an upgrade product at their own cost. Certain remaining entitlements on the End of Support appliance may be transitioned to the upgrade appliance upon request.</div>';
	$tooltips .= '<div id="firmware-type-tooltip">A complete explanation of release types is available at the bottom of this page. (GR) General Release: General Release software is a mature, widely deployed and proven release, used for production environments. (FR) Feature Release: Feature Release software is a new release that introduces major new features in the product. (MR) Maintenance Release: Maintenance Release software includes bug fixes and enhancements made to a previous release (IR) Initial Release: Initial release software is the first release of a new product. (ER) Early Release: This is software that includes incremental changes to a previous release. (Hotfix) Hotfix Release: Contains the latest fixes and patches, and are provided to customers who are looking to address specific issues. </div>';
	$tooltips .= '<div id="firmware-release-date-tooltip">The date the firmware was released</div>';
	$tooltips .= '<div id="firmware-status-tooltip">The status field indicates whether or not a firmware version is considered Active. - Active:  This firmware version is considered current and is fully supported. - Upgrade Recommended: This firmware version is approaching its EOS (End of Support) date. A recommended upgrade path is provided for all firmware versions approaching EOS.</div>';
	$tooltips .= '<div id="firmware-recommended-upgrade-tooltip">The recommended upgrade section shows the supported upgrade path from an earlier version of firmware to the latest version of SonicOS firmware. Please consult our Upgrade Guides before completing any firmware upgrades.</div>';
	$tooltips .= '<div id="firmware-eos-date-tooltip">End of Support (EOS) is the date which SonicWall will cease support for the related firmware including fixes or upgrades. A recommended upgrade path is suggested for any firmware version approaching the EOS phase. Should a technical issue arise with firmware during the EOS phase, customers will be required to upgrade to a supported firmware version. </div>';
	$tooltips .= '</div>';

	$prod_title .= '</div>';
	$types .= $hardware . $firmware . $software . '</div>';
	$tables .= '</div>';

	echo $prod_title . $types . $tables . $tooltips;
	
?>

</div>