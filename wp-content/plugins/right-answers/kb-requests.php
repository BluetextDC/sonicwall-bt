<?php

 class KBRequests {

 	public function __construct() 
    { 
        //Constructor
 	}
     
    public function get_new_solutions($page, $lang_override = false)
    {
        $ra = new RARequests();
        return $ra->get_new_solutions($page, $lang_override);
    }
     
 	public function search_for_solution($query_text, $page, $lang_override = false)
    {
        $ra = new RARequests();
        return $ra->search_for_solution($query_text, $page, $lang_override);
 	}

 	public function search_for_sub_solution($query_text, $sub_cat_name, $page)
    {
        $ra = new RARequests();
        return $ra->search_for_sub_solution($query_text, $sub_cat_name, $page);
 	}

 	public function get_single_solution($solution_id, $preload = false)
    {
        if (!$preload)
        {
            global $wpdb;
        
            //Get the solution data from MySQL
            $sql = "SELECT * FROM {$wpdb->prefix}ra_data WHERE sol_id = %d LIMIT 1";
            $sql = $wpdb->prepare($sql, $solution_id);
            $sol_data = $wpdb->get_results($sql);

            if ($sol_data && count($sol_data) == 1)
            {              
                return json_decode($sol_data[0]->data);
            }
        }
        
        //Failed to find in the DB, query ra
        $ra = new RARequests();
        return $ra->get_single_solution($solution_id, $preload);
 	}

 	public function get_frequent_searches()
    {
        $ra = new RARequests();
        return $ra->get_frequent_searches();
 	}

 	public function search_by_category($category_name, $page)
    {
        $ra = new RARequests();
        return $ra->search_by_category($category_name, $page);
 	}
     
    public function getRALanguage()
    {
        $ra = new RARequests();
        return $ra->getRALanguage();
    }
     
    public function build_category_slug($category) 
    {
        $ra = new RARequests();
        return $ra->build_category_slug($category);
    }
    public function get_ra_categories($preload = false) 
    {
        $ra = new RARequests();
        return $ra->get_ra_categories($preload);
 	}

 	public function alert_search($page, $preload = false)
    {
        $ra = new RARequests();
        return $ra->alert_search($page, $preload);
 	}
     
    private function alert_search_language($page, $language)
    {
        $ra = new RARequests();
        return $ra->alert_search_language($page, $language);
    }

 	public function upvote_answer($accepted_sol)
    {
        $ra = new RARequests();
        return $ra->upvote_answer($accepted_sol);
 	}
     
    public function downvote_answer($downvote_email, $accepted_sol, $down_comment)
    {
        $ra = new RARequests();
        return $ra->downvote_answer($downvote_email, $accepted_sol, $down_comment);
    }

	public function salesforce_refresh() 
    {
	    $ra = new RARequests();
        return $ra->salesforce_refresh();
	}

 	public function get_plc_tables()
    {
        $ra = new RARequests();
        return $ra->get_plc_tables();
 	}

 }
