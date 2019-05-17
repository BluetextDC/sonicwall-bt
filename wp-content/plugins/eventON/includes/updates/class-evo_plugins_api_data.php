<?php
/**
 * EventON Product Information for Sections in new update information
 * @version 0.1
 */

class EVO_Plugins_API_Data{

	function get_data($name, $slug){
		$eventon_product_information = apply_filters('evo_pluginsapi_sections',
		array(
			'eventon'=>array(
				'description'	=>$this->get_general('description','EventOn', 'eventon'),
				'installation'	=>$this->get_general('installation','EventOn', 'eventon'),
				'register_license'=>'<p><strong>Get free updates</strong></p><p>In order to get free EventON updates and download them directly in here <strong>activate</strong> your copy of EventON with proper license.</p><p><strong>How to get your license key</strong></p><ol><li>Login into your Envato account</li><li>Go to Download tab</li><li>Under EventON click "License Cerificate"</li><li>Open text file and copy the <strong>Item Purchase Code</strong></li><li>Go to myEventON in your website admin</li><li>Under "Licenses" tab find the EventON license and click "Activate Now"</li><li>Paste the copied purchased code from envato, and click "Activate Now"</li><li>Once the license if verified and activated you will be able to download updates automatically</li></ol><br/><br/><p><a href="http://www.myeventon.com/documentation/how-to-find-eventon-license-key/" target="_blank">Updated Documentation</a></p>', 
				'changelog'		=> $this->get_general('changelog','EventOn', 'eventon'),
			),

		));
	
		$output = isset($eventon_product_information[$slug])? $eventon_product_information[$slug]: array();

		// Defaults for all addons
		$output['eventon_reviews'] = $this->evo_reviews();
		$output['latest_news'] = $this->get_general('latest_news','EventOn', 'eventon');
		if(!isset($output['description'])) $output['description'] = $this->get_general('description','EventOn', 'eventon');
		
		return $output;
	}

	function get_general($section, $name, $slug){
		switch ($section) {
			case 'latest_news':
				return 'Make sure to follow us via twitter <code>@myeventon</code> for updates.';
			break;
			case 'description':
				return "<p>EventOn <b>#1 Best Selling</b> WordPress Event Calendar in codecanyon!</p><p>EventOn provide a stylish and minimal calendar design that address to the needs of your visitors and audience. It is also packed with awesome features such as: Repeat events, multi-day events, google map locations, smooth month navigation, featured images, and the list goes on.</p><p>To learn more about eventON please visit <a href='http://www.myeventon.com'>myeventon.com</a>";
			break;
			case 'installation':
				ob_start();
			    ?>
			    <h4>Minimum Requirements:</h4>
			    <p>WordPress 3.8 or higher, PHP 5.2.4 or higher, MySQL 5.0 or higher</p>

			    <h4>Automatic Installation</h4>
			    <p>In order to get automatic updates you will need to activate your version of <?php echo $name;?>. You can learn how to activate this plugin <a href='http://www.myeventon.com/documentation/how-to-get-new-auto-updates-for-eventon/' target='_blank'>in here</a>. Automatic updates will allow you to perform one-click updates to EventOn products direct from your wordpress dashboard.</p>

			    <h4>Manual Installation</h4>
			    <p><strong>Step 1:</strong></p>
			    <p>Download <code><?php echo $slug;?>.zip</code> from <?php echo ($slug=='eventon')? 'codecanyon > my downloads':'<a href="http://myeventon.com/my-account" target="_blank">myeventon.com/my-account</a>';?></p>
			    <p><strong>Step 2:</strong></p>
			    <p>Unzip the zip file content into your computer. </p>
			    <p><strong>Step 3:</strong></p>
			    <p>Open your FTP client and remove files inside <code>wp-content/plugins/<?php echo $slug;?>/</code> folder. </p>
			    <p><strong>Step 4:</strong></p>
			    <p>Update the zip file content into the above mentioned folder in your FTP client. </p>
			    <p><strong>Step 5:</strong></p>
			    <p>Go to <code>../wp-admin</code> of your website and confirm the new version has indeed been updated.</p>

			    <p><a href="http://www.myeventon.com/documentation/can-download-addon-updates/" target="_blank">More information on how to download & update eventON plugins and addons</a></p>
			    <?php
			    return ob_get_clean();
			break;
			case 'changelog':
				return 'Complete updated changelog for this item can be found at <a target="_blank" href="http://www.myeventon.com/documentation/">EventON Changelog.</a> For support & frequently asked questions, visit <a target="_blank" href="http://support.ashanjay.com">The EventON Support Forums</a>';
			break;
		}
	}

	function evo_reviews(){
		ob_start();
		foreach(array(
			1=>array(
				'title'=> 'Design Quality',
				'userurl'=> 'https://codecanyon.net/user/fpmontes',
				'img'=> 'https://0.s3.envato.com/files/140472391/logo.jpg',
				'username'=> 'fpmontes',
				'date'=> 'November 8, 2017',
				'review'=>	'I could give 5 stars for many things: design, usability, customer service and many more. After buying and testing several plugins to post events I think this wins by far. It gives so many possibilities that at first it seems a bit complicated but nothing is further from reality. It adapts perfectly to mobile devices. Perhaps what has most caught my attention has been the amount of possibilities that exist to show the information.'
			),
			2=>array(
				'title'=> 'Customizability',
				'userurl'=> 'https://codecanyon.net/user/joefster',
				'img'=> 'https://0.s3.envato.com/files/107602301/Joefster-favicon-80x80.png',
				'username'=> 'Joefster',
				'date'=> 'November 7, 2017',
				'review'=>	'This is a very, very good, no great plugin. Functionality is great even with the Addons it makes you the king of Events. I use it a lot for different clients. And the support is good, really good.'
			)
		) as $info){
			?>
			<div class='review'>
				<div class="review-head">
					<div class="reviewer-info">
						<div class="review-title-section">
							<h4><?php echo $info['title'];?></h4>
							<div class="star-rating"><div class="wporg-ratings"><span class="star dashicons dashicons-star-filled"></span><span class="star dashicons dashicons-star-filled"></span><span class="star dashicons dashicons-star-filled"></span><span class="star dashicons dashicons-star-filled"></span><span class="star dashicons dashicons-star-filled"></span></div></div>
						</div>
						<p>
							By <a href="<?php echo $info['userurl'];?>" target="_blank"><img style='height:20px' alt="" src="<?php echo $info['img'];?>" class="avatar avatar-16 photo"></a><a href="<?php echo $info['userurl'];?>" target="_blank"><?php echo $info['username'];?></a> on <span class="review-date"><?php echo $info['date'];?></span>			
						</p>
					</div>
				</div>
				<div class="review-body"><?php echo $info['review'];?></div>
			</div>

			<?php
		}
		return ob_get_clean();
	}

}