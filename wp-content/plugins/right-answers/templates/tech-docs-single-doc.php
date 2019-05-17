<?php

// tech-docs-single-doc

	$doc_id = $_REQUEST['doc_id'];
    $post_slug = get_query_var('td-slug');

    $swtd = new SW_Tech_Docs();

    if ($doc_id)
    {
        $req_post = 'wp-json/wp/v2/topic_api/' . $doc_id;
    }
    else if ($post_slug)
    {
       //Get the last bit of the slug to pull the right document but keep hierarchical urls
       $slug_parts = explode("/",$post_slug);
        
       if (count($slug_parts) > 1)
       {
           //We need to perform a lookup due to duplicate slug issues
           $id_post = 'wp-json/swtd/v1/permalink_search/?post-permalink='.$post_slug;
           $post = $swtd->curl_request($id_post);
           
           if ($post && $post->pdf)
           {
               if (wp_redirect($post->pdf_redirect))
               {
                   exit();
               }
           }
           
           $req_post = 'wp-json/wp/v2/topic_api/' . $post->post_id;
       }
       else
       {
           $id_post = 'wp-json/swtd/v1/first_child/?post-permalink='.$post_slug;
           $first_child = $swtd->curl_request($id_post);
           
           $url = "/support/technical-documentation/".$first_child->redirect;
   
           //Do the redirect
           if (wp_redirect($url))
           {
                exit();
           }
           
        $req_post = 'wp-json/wp/v2/topic_api?slug=' . $post_slug;
       }
    }
    else
    {
        return render_td_error();
    }
	


	$td_post = $swtd->curl_request($req_post);

	if ( $td_post !== NULL ){
        if (is_array($td_post) && count($td_post) > 0)
        {
            $td_post = $td_post[0];
        }
    
        
        if ($td_post && $td_post->content && $td_post->content->rendered)
        {
           echo '<div id="techdocs-container"><div id="toc-toggle"></div>'.$td_post->content->rendered.'</div>';  
        }
        else
        {
            return render_td_error();
        }
        
	}
	else {
		return render_td_error();
	}

	
function render_td_error()
{
    echo '<h3>Sorry. The page you requested is temporarily unavailable.</h3> <p>A report has been filed and this problem will be addressed as soon as possible.</p>';
}