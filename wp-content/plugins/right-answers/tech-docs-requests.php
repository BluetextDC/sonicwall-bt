<?php 

	// functions to make rest requests to Tech Docs site

class SW_Tech_Docs {

	// private $base_url = 'https://wordpress-228475-699031.cloudwaysapps.com/';
	private $base_url = 'https://techdocs.sonicwall.com/';

	public function __construct() { 
 		//do stuff here if needed
 	}

 	private function curl_builder( $curl_args ){

 		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->base_url . $curl_args,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTPHEADER => array(
		   "Authorization: Basic " . base64_encode( 'rkasselman' . ':' . 'xa6u hDrN b9ZX Ic0d kUHb GI6u'),
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
			if ( json_decode( $response ) !== NULL ){
				return json_decode( $response );
			}
		  	else {
		  		return $response;
		  	}
		}

 	}

 	public function curl_request( $the_url ){
 		$req = $this->curl_builder( $the_url );
 		return $req;
 	}
}


function main_tech_docs_category(){
	$td = new SW_Tech_Docs();
	$mtcats = $td->curl_request('wp-json/swtd/v1/terms?term-slug=tech-docs-category');
	return $mtcats;
}

if ( is_admin() ){
	add_action( 'wp_ajax_child_cat_getter', 'child_cat_getter' );
	add_action( 'wp_ajax_nopriv_child_cat_getter', 'child_cat_getter' );
	add_action( 'wp_ajax_main_topic_titles', 'main_topic_titles' );
	add_action( 'wp_ajax_nopriv_main_topic_titles', 'main_topic_titles' );
}

function child_cat_getter(){

	$parent_term_id = $_REQUEST['parent-term'];
	$td = new SW_Tech_Docs();
	$sub_cats = $td->curl_request('wp-json/swtd/v1/terms?term-slug=tech-docs-category&term-parent=' . $parent_term_id);

	if ( !empty( $sub_cats ) ){
		$sub_cat_selector = '<select name="sub-cat-selector" id="sub_cat_selector">';
		$sub_cat_selector .= '<option value="">Select A Model</option>';
		foreach ($sub_cats as $scats) {
			$sub_cat_selector .= '<option value="' . $scats->term_id . '">' . $scats->name . '</option>';
		}
		$sub_cat_selector .= '</select>';

		echo $sub_cat_selector;
		wp_die();
	}
	else {
		echo false;
		wp_die();
	}
	
}

function main_topic_titles(){
	$topic_id = $_REQUEST['parent-term'];
	if ( isset( $_REQUEST['model-term'] ) ) {
		$model_id = '&child-term=' . $_REQUEST['model-term'];
	}
	else {
		$model_id = '';
	}
	$td = new SW_Tech_Docs();
	$post_titles = $td->curl_request('wp-json/swtd/v1/book_titles?parent-term=' . $topic_id . $model_id );

	if ( !empty( $post_titles ) ){
		$title_holder = '<div class="sn_item result_gray_areas sn_result_area">';
		foreach ($post_titles as $pt) {
			if ( !empty( $pt->data ) ){
				$title_holder .= '<div style="background-color: lightgray; margin-bottom: 10px;"><div style="padding:10px;">';
				$title_holder .= '<h3>' . $pt->book_type . '</h3>';
				foreach ($pt->data as $book_title ) {
					if ( !empty($book_title->pdf_only))
                    {
                        $post_name = $book_title->post_name;
                        if (!$post_name)
                        {
                            if ($book_title && $book_title->pdf_only && strlen($book_title->pdf_only) > 0)
                            {
                                try {
                                    $a = new SimpleXMLElement($book_title->pdf_only);
                                    
                                    if ($a && isset($a['href']) && strlen($a['href']) > 0)
                                    {
                                        $filename = basename($a['href'], ".pdf");
                                    
                                        if ($filename && strlen($filename) > 0)
                                        {
                                             $post_name = $filename;
                                        }
                                         
                                    }
                                }
                                catch (Exception $e)
                                {
                                    //Die silently
                                }
                                
                            }
                            
                        }
                        $a_href = str_replace("<a ", "<a data-fancybox data-type='iframe' data-src-override='".home_url()."/support/technical-documentation/" . $post_name."' ", $book_title->pdf_only);
                        
                        if ( wp_is_mobile() ) {
                            $a_basic = str_replace('<a ', '<a target="_blank" ', $book_title->pdf_only);
                            $title_holder .= '<p>' . $a_basic . '</p>';
                        }
						else
                        {
                            $title_holder .= '<p>' . $a_href . '</p>';
                        }
                    
					}
                    else if ( !empty($book_title->first_child_post_id))
                    {
                        $title_holder .= '<p><a data-fancybox data-type="iframe" href="/support/technical-documentation/' . $book_title->post_name . '" >' . $book_title->parent_post_title . '</a></p>';
					}
					
				}
//                
//                $title_holder .= '<script>window.initFancybox();</script>';
				$title_holder .= '</div></div>';
			}
		}

		$title_holder .= '</div>';

		echo $title_holder;
		wp_die();
	}
	else {
		echo false; 
		wp_die();
	}

	
}

//	add_filter( 'the_posts', 'generate_fake_pages', -10 );
	/**
	 * Create a fake page called "fake"
	 *
	 * $fake_slug can be modified to match whatever string is required
	 *
	 *
	 * @param   object  $posts  Original posts object
	 * @global  object  $wp     The main WordPress object
	 * @global  object  $wp     The main WordPress query object
	 * @return  object  $posts  Modified posts object
	 */
	function generate_fake_pages( $posts ) {
		global $wp, $wp_query;

		// $url_slug = $the_slug; // URL slug of the fake page
		$url_slug_base = 'support/technical-documentation/';

		$td = new SW_Tech_Docs();
		$book_parents = $td->curl_request('wp-json/swtd/v1/book_covers');


		// need to get all the book covers here
		

		foreach ($book_parents as $book_cover) {
			# code...
		
			$url_slug = $url_slug_base . $book_cover->post_slug;

			if ( ! defined( 'FAKE_PAGE' ) && ( strtolower( $wp->request ) == $url_slug ) ) {

				// stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
				define( 'FAKE_PAGE', true );

				// create a fake virtual page
				$post = new stdClass;
				$post->post_author    = 1;
				$post->post_name      = $url_slug;
				$post->guid           = home_url() . '/' . $url_slug;
				$post->post_title     = 'Sonicwall Techincal Documentation';
				$post->post_content   = $book_cover->post_content;
				$post->ID             = -999;
				$post->post_type      = 'page';
				$post->post_status    = 'static';
				$post->comment_status = 'closed';
				$post->ping_status    = 'open';
				$post->comment_count  = 0;
				$post->post_date      = current_time( 'mysql' );
				$post->post_date_gmt  = current_time( 'mysql', 1 );
				$posts                = NULL;
				$posts[]              = $post;

				// make wpQuery believe this is a real page too
				$wp_query->is_page             = true;
				$wp_query->is_singular         = true;
				$wp_query->is_home             = false;
				$wp_query->is_archive          = false;
				$wp_query->is_category         = false;
				unset( $wp_query->query[ 'error' ] );
				$wp_query->query_vars[ 'error' ] = '';
				$wp_query->is_404 = false;
			}
		}
		return $posts;
	}