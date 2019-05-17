<?php
/** 
 * Helper functions to be used by eventon or its addons
 * front-end only
 *
 * @version 0.7
 * @updated  2.5.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_helper{	
	public $options2;
	public function __construct(){
		$this->opt2 = get_option('evcal_options_evcal_2');
	}
	 

	// Create posts 
		function create_posts($args){
			if(!empty($args) && is_array($args)){
				$valid_type = (function_exists('post_type_exists') &&  post_type_exists($args['post_type']));

				if(!$valid_type)
					return false;

				$__post_content = !empty($_POST['post_content'])? $_POST['post_content']: 
					(!empty($args['post_content'])?$args['post_content']:false);
				$__post_content = ($__post_content)?
			        	convert_chars(stripslashes($__post_content)): '';

			    // author id
			    $current_user = wp_get_current_user();
		        $author_id =  (($current_user instanceof WP_User)) ? $current_user->ID : ( !empty($args['author_id'])? $args['author_id']:1);

			    $new_post = array(
		            'post_title'   => wp_strip_all_tags($args['post_title']),
		            'post_content' => $__post_content,
		            'post_status'  => $args['post_status'],
		            'post_type'    => $args['post_type'],
		            'post_name'    => sanitize_title($args['post_title']),
		            'post_author'  => $author_id,
		        );
			    return wp_insert_post($new_post);
			}else{
				return false;
			}
		}

		function create_custom_meta($post_id, $field, $value){
			add_post_meta($post_id, $field, $value);
		}

	// Eventon Settings helper
		function get_html($type, $args){
			switch($type){
				case 'email_preview':
					ob_start();
					echo '<div class="evo_email_preview"><p>Headers: '.$args['headers'][0].'</p>';
					echo '<p>To: '.$args['to'].'</p>';
					echo '<p>Subject: '.$args['subject'].'</p>';
					echo '<div class="evo_email_preview_body">'.$args['message'].'</div></div>';
					return ob_get_clean();
				break;
			}
		}

	// ADMIN & Frontend Helper
	// @updated 2.5.2
		public function send_email($args){
			$defaults = array(
				'html'=>'yes',
				'preview'=>'no',
				'to'=>'',
				'from'=>'',
				'from_name'=>'','from_email'=>'',
				'header'=>'',
				'subject'=>'',
				'message'=>'',
				'type'=>'',// bcc
				'attachments'=> array(),
				'return_details'=>false
			);
			$args = array_merge($defaults, $args);

			if($args['html']=='yes'){
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type'));
			}

			if(!empty($args['header'])){
				$headers = $args['header'];
			}else{
				$headers = array();
				if(empty($args['from_email'])){
					$headers[] = 'From: '.$args['from'];
				}else{
					$headers[] = 'From: '.(!empty($args['from_name'])? $args['from_name']:'') .' <'.
						$args['from_email'] . '>';
				}
			}	

			$return = '';	

			if($args['preview']=='yes'){
				$return = array(
					'to'=>$args['to'],
					'subject'=>$args['subject'],
					'message'=>$args['message'],
					'headers'=>$headers
				);
			// bcc version of things
			}else if(!empty($args['type']) && $args['type']=='bcc' ){
				$bcc = (is_array($args['to']))? implode(',', $args['to']): $args['to'];
				$headers[] = "Bcc: ".$bcc;
				$return = wp_mail($args['from'], $args['subject'], $args['message'], $headers, $args['attachments']);	
			}else{
				$return = wp_mail($args['to'], $args['subject'], $args['message'], $headers, $args['attachments']);
			}

			if($args['html']=='yes'){
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			} 

			if($args['return_details']){
				// get the errors
				$ts_mail_errors = array();
				if(!$return){
					global $ts_mail_errors;
					global $phpmailer;

					if (!isset($ts_mail_errors)) $ts_mail_errors = array();

					if (isset($phpmailer)) {
						$ts_mail_errors[] = $phpmailer->ErrorInfo;
					}
				}
				return array('result'=>$return, 'error'=>$ts_mail_errors);
			}else{
				return $return;
			}
			
		}
		function set_html_content_type() {
			return 'text/html';
		}
		function set_charset_type() {
			return 'utf8';
		}

		// GET email body with eventon header and footer for email included
		public function get_email_body_content($message=''){
			global $eventon;

			ob_start();
			echo $eventon->get_email_part('header');
			echo !empty($message)? $message:'';
			echo $eventon->get_email_part('footer');
			return ob_get_clean();
		}

	// YES NO Button
		function html_yesnobtn($args=''){
			$defaults = array(
				'id'=>'',
				'var'=>'',
				'no'=>'',
				'default'=>'',
				'input'=>false,
				'inputAttr'=>'',
				'label'=>'',
				'guide'=>'',
				'guide_position'=>'',
				'abs'=>'no',// absolute positioning of the button
				'attr'=>'', // array
				'afterstatement'=>'',
				'lang'=>'L1'
			);
			
			$args = shortcode_atts($defaults, $args);

			$_attr = $no = '';

			if(!empty($args['var'])){
				$no = ($args['var']	=='yes')? 
					 null: 
					 ( (!empty($args['default']) && $args['default']=='yes')? null:'NO');
			}else{
				$no = (!empty($args['default']) && $args['default']=='yes')? null:'NO';
			}

			if(!empty($args['attr'])){
				foreach($args['attr'] as $at=>$av){
					$_attr .= $at.'="'.$av.'" ';
				}
			}

			// input field
			$input = '';
			if($args['input']){
				$input_value = (!empty($args['var']))? 
					$args['var']: (!empty($args['default'])? $args['default']:'no');

				// Attribut values for input field
				$inputAttr = '';
				if(!empty($args['inputAttr'])){
					foreach($args['inputAttr'] as $at=>$av){
						$inputAttr .= $at.'="'.$av.'" ';
					}
				}

				// input field
				$input = "<input {$inputAttr} type='hidden' name='{$args['id']}' value='{$input_value}'/>";
			}

			$guide = '';
			if(!empty($args['guide'])){
				$guide = $this->tooltips($args['guide'], $args['guide_position']);
			}

			$label = '';
			if(!empty($args['label']))
				$label = "<label class='ajde_yn_btn_label' for='{$args['id']}'>{$args['label']}{$guide}</label>";

			$text_NO = eventon_get_custom_language($this->opt2, 'evo_lang_no', 'NO', $args['lang']);
			$text_YES = eventon_get_custom_language($this->opt2, 'evo_lang_yes', 'YES', $args['lang']);

			return '<span id="'.$args['id'].'" class="ajde_yn_btn '.($no? 'NO':null).''.(($args['abs']=='yes')? ' absolute':null).'" '.$_attr.' data-afterstatement="'.$args['afterstatement'].'"><span class="btn_inner" style=""><em class="no">'.$text_NO.'</em><span class="catchHandle"></span><em class="yes">'.$text_YES.'</em></span></span>'.$input.$label;
		}

	// tool tips
		function tooltips($content, $position='', $handleClass=false, $echo = false){
			// tool tip position
				if(!empty($position)){
					$L = ' L';
					
					if($position=='UL')
						$L = ' UL';
					if($position=='U')
						$L = ' U';
				}else{
					$L = null;
				}

			$output = "<span class='ajdeToolTip{$L} fa ". ($handleClass? 'handle':'')."' data-handle='{$handleClass}'><em>{$content}</em></span>";

			if(!$echo)
				return $output;			
			
			echo $output;
		}
		function echo_tooltips($content, $position=''){
			$this->tooltips($content, $position='',true);
		}

	// template locator
	// pass: paths array, file name, default template with full path and file
		function template_locator($paths, $file, $template){
			foreach($paths as $path){
				if(file_exists($path.$file) ){	
					$template = $path.$file;
					break;
				}
			}				
			if ( ! $template ) { 
				$template = AJDE_EVCAL_PATH . '/templates/' . $file;
			}

			return $template;
		}	

}