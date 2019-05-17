<?php
/**
 * Calendar functions
 * @version 2.5.5
 */

class evo_fnc{

// construct
	public function __construct(){
		$this->options_1 = get_option('evcal_options_evcal_1');
	}

// return the login message with button for fields that require login
	function get_field_login_message(){
		global $wp;
		$options_1 = $this->options_1 ;
		$current_url = home_url(add_query_arg(array(),$wp->request));

		$link = wp_login_url($current_url);

		if(!empty($options_1['evo_login_link']))
			$link = $options_1['evo_login_link'];

		return sprintf("%s <a href='%s' class='evcal_btn'>%s</a>", evo_lang('Login required to see the information') , $link, evo_lang('Login'));
	}

// run special character encoding
	function htmlspecialchars_decode($data){
		return ( evo_settings_check_yn($this->options_1, 'evo_dis_icshtmldecode'))? 
			$data:
			htmlspecialchars_decode($data);
	}	


// time functions
	function time_since($old_time, $new_time){
        $since = $new_time - $old_time;
        // array of time period chunks
        $chunks = array(
            /* translators: 1: The number of years in an interval of time. */
            array( 60 * 60 * 24 * 365, _n_noop( '%s year', '%s years', 'wp-crontrol' ) ),
            /* translators: 1: The number of months in an interval of time. */
            array( 60 * 60 * 24 * 30, _n_noop( '%s month', '%s months', 'wp-crontrol' ) ),
            /* translators: 1: The number of weeks in an interval of time. */
            array( 60 * 60 * 24 * 7, _n_noop( '%s week', '%s weeks', 'wp-crontrol' ) ),
            /* translators: 1: The number of days in an interval of time. */
            array( 60 * 60 * 24, _n_noop( '%s day', '%s days', 'wp-crontrol' ) ),
            /* translators: 1: The number of hours in an interval of time. */
            array( 60 * 60, _n_noop( '%s hour', '%s hours', 'wp-crontrol' ) ),
            /* translators: 1: The number of minutes in an interval of time. */
            array( 60, _n_noop( '%s minute', '%s minutes', 'wp-crontrol' ) ),
            /* translators: 1: The number of seconds in an interval of time. */
            array( 1, _n_noop( '%s second', '%s seconds', 'wp-crontrol' ) ),
        );

        if ( $since <= 0 ) {
            return __( 'now', 'wp-crontrol' );
        }

        // we only want to output two chunks of time here, eg:
        // x years, xx months
        // x days, xx hours
        // so there's only two bits of calculation below:

        // step one: the first chunk
        for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
            $seconds = $chunks[ $i ][0];
            $name = $chunks[ $i ][1];

            // finding the biggest chunk (if the chunk fits, break)
            if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
                break;
            }
        }

        // set output var
        $output = sprintf( translate_nooped_plural( $name, $count, 'wp-crontrol' ), $count );

        // step two: the second chunk
        if ( $i + 1 < $j ) {
            $seconds2 = $chunks[ $i + 1 ][0];
            $name2 = $chunks[ $i + 1 ][1];

            if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
                // add to output var
                $output .= ' ' . sprintf( translate_nooped_plural( $name2, $count2, 'wp-crontrol' ), $count2 );
            }
        }

        return $output;
    }


// wpdb based event post meta retrieval
// @since 2.5.5
	function event_meta($event_id, $fields){
		global $wpdb;

		$fields_str = '';
		$select = '';

		asort($fields);

		$len = count($fields); $i=1;
		foreach($fields as $field){
			$fields_str .= "'{$field}". ($i==$len? "'":"',");
			$select .= "MT.meta_value AS {$field}" . ($i==$len? "":",");
			$i++;
		}

		//print_r($fields_str);
        $sql = "SELECT MT.meta_value
            FROM $wpdb->postmeta AS MT
            WHERE MT.meta_key IN ({$fields_str}) 
            AND MT.post_id='{$event_id}' ORDER BY MT.meta_key DESC";
		$results = $wpdb->get_results( $sql);

		if(!$results && count($results) ==0) return false;

		//print_r($sql);
		//print_r($fields);

		$output = array();
		foreach($results as $index=>$result){
			$output[ $fields[$index]] = maybe_unserialize($result->meta_value);
		}
        //print_r($output);
		return $output;

	}

// use this to save multiple event post meta values with one data base query 
// @since 2.5.6
    function update_event_meta($event_id, $fields){
        // check required values
        $event_id = absint($event_id); if(!$event_id) return false;
        $table = _get_meta_table('post');   if(!$table) return false;


        $values = array();
        foreach($fields as $meta_key=>$meta_value){
            $meta_key = wp_unslash($meta_key);
            $meta_value = maybe_serialize(wp_unslash($meta_value));

            $values[] = "('{$meta_key}','{$meta_value}','{$event_id}')";
        }

        $values = implode(',', $values);

        global $wpdb;

        $res = $wpdb->update(
            $table,
            array(
                'meta_value'=>'yes'
            ),
            array(
                'meta_key'=>'_evoto_block_assoc'
            )
        );

        /*$results = $wpdb->query(  
            "INSERT INTO $wpdb->postmeta (meta_key, meta_value, post_id)
            VALUES ('_evoto_block_assoc','yes','1840') 
            ON DUPLICATE KEY UPDATE meta_key=VALUES(meta_key), meta_value=VALUES(meta_value)");

        echo $wpdb->show_errors(); 
        echo $wpdb->print_error();
        */

    }

}