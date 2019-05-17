<?php
/**
 * Plugin Name: Soon
 * Plugin URI: http://rikschennink.nl/products/soon-wp
 * Description: Soon, animated countdowns for everyone.
 * Version: 1.12.1
 * Author: Rik Schennink
 * Author URI: https://pqina.nl
 */
$soon_version = '1.12.1';
$soon_unique_id = 0;
$soon_data_key = 'soon_options';

/**
 * SOON HTML RENDERER
 */
function soon_echo($name,$content = null, $due = null, $since = null, $session_id = null, $url = null) {
	echo soon_generate($name,$content,$due,$since,$session_id,$url);
}

function soon_generate($name,$content = null,$due = null,$since = null, $session_id = null, $url = null) {

	global $soon_data_key;
	global $soon_unique_id;

	// if no name, exit
	if (!$name) {
		return null;
	}

	// get soon options data
	$data = get_option($soon_data_key);

	// if no data found, exit
	if (!$data) {
		return null;
	}

	if (soon_string_startsWith($data,'{\"')) {
		$data = stripcslashes($data);
	}

	$data = json_decode($data);

	// find setup for given counter (match name with counters in database)
	$setup = null;
	foreach($data->custom as $counter) {
		if ($name == $counter->name) {
			$setup = $counter;
			break;
		}
	}

	// if no setup found, exit
	if (!$setup) {
		return null;
	}

	// get unique id for this counter
	$uid = $soon_unique_id++;

	// set snippet
	$html = $setup->snippet;

	// test for internal content
	if ($content) {

		// adjust complete function if present
		if (strpos($html,'soonCompleteCallback') === false) {

			// create new callback
			$html = str_replace(
				'<div class="soon"',
				'<script>function soonCompleteCallback(){};</script>' .
				'<div class="soon" data-event-complete="soonCompleteCallback"',
				$html
			);

		}

		// setup reveal javascript
		$reveal = 'document.getElementById(\'my-soon-counter-content-' . $uid . '\').style.display=\'block\';';

		// prepend reveal method to callback
		$html = str_replace(
			'soonCompleteCallback(){',
			'soonCompleteCallback(){' . $reveal,
			$html
		);

		// add HTML
		$html .= '<div id="my-soon-counter-content-' . $uid. '" style="display:none">' . $content . '</div>';
	}

	// override due date if set
	if ($due) {

		$html = preg_replace('/data-due=".+?"/i','data-due="' . $due . '"', $html);
		$html = preg_replace('/data-since=".+?"/i','data-due="' . $since . '"', $html);

		// remove since
		$html = preg_replace('/data-since=".+?"/i','',$html);

	}

	// override since date if set
	if ($since) {

		$html = preg_replace('/data-since=".+?"/i','data-since="' . $since . '"', $html);
		$html = preg_replace('/data-due=".+?"/i','data-since="' . $since . '"', $html);

		// remove due
		$html = preg_replace('/data-due=".+?"/i','',$html);
	}

	// replace possible server date with the server date
	$html = str_replace(
		'{{ date }}',
		date('c'),
		$html
	);

	// replace default id with new unique id
	$html = str_replace(
		'my-soon-counter',
		'my-soon-counter-' . $uid,
		$html
	);

	// if has unique id set
	if ($session_id) {
		$html = str_replace(
			'id + "-countdown-offset"',
			'"countdown-offset-' . $session_id . '"',
			$html
		);
	}

	// if url has been set
	if ($url) {
		$html = preg_replace(
			'/{window\.location = "(.+?)"}/i',
			'{window.location = "' . $url . '"}',
			$html
		);
	}

	// transform default callback to unique callback
	$html = str_replace(
		'soonCompleteCallback',
		'soonCompleteCallback_' . $uid,
		$html
	);

	return $html;
}


/**
 * SHORTCODE HANDLER
 */
function soon_create($atts, $content = null) {

	// get name
	$values = shortcode_atts(array(
		'name' => null,
		'due' => null,
		'since' => null,
		'session_id' => null,
		'url' => null
	), $atts);

	if ($content) {
		$content = do_shortcode($content);
	}

	return soon_generate($values['name'], $content, $values['due'], $values['since'], $values['session_id'], $values['url']);
}

// register [soon] shortcode
function soon_register_shortcodes() {
	add_shortcode('soon','soon_create');
}

// register soon scripts
function soon_register_scripts() {
	global $soon_version;
	wp_enqueue_script('soon_scripts', plugins_url('lib/soon.min.js', __FILE__), array(), $soon_version, true  );
	wp_enqueue_style('soon_styles', plugins_url('lib/soon.min.css', __FILE__), array(), $soon_version );
}

add_action('init', 'soon_register_shortcodes');
add_action('wp_enqueue_scripts', 'soon_register_scripts');
add_filter('widget_text', 'do_shortcode');



/**
 * ADMIN PAGE SETUP
 */

add_action('admin_init', 'soon_admin_init');
add_action('admin_menu', 'soon_admin_menu');
add_action('admin_enqueue_scripts', 'soon_register_admin_scripts');
add_action('wp_ajax_soon_storage_load', 'soon_storage_load_callback');
add_action('wp_ajax_soon_storage_save', 'soon_storage_save_callback');

function soon_register_admin_scripts($hook) {

	global $soon_version;

	// always load icon
	wp_register_style('soon_mce_icon',plugins_url('icon.css', __FILE__),array(),$soon_version);
	wp_enqueue_style('soon_mce_icon');

	// stop here if not on admin page
	if (strpos($hook,'_soon_') === false) {return;}

	// register soon scripts
	soon_register_scripts();

	// register admin tool scripts
	wp_register_style('soon_builder_spectrum',plugins_url('builder/app/spectrum.css', __FILE__),array('soon_styles'),$soon_version);
	wp_register_style('soon_builder_datetimepicker',plugins_url('builder/app/jquery.datetimepicker.css', __FILE__),array('soon_styles'),$soon_version);
	wp_register_style('soon_builder_styles',plugins_url('builder/app/styles.css', __FILE__),array('soon_styles'),$soon_version);

	wp_register_script('soon_builder_datetimepicker', plugins_url('builder/app/jquery.datetimepicker.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_spectrum', plugins_url('builder/app/spectrum.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_generator', plugins_url('builder/app/generator.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_presets', plugins_url('builder/app/presets.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_fields', plugins_url('builder/app/fields.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_styler', plugins_url('builder/app/styler.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_renderer', plugins_url('builder/app/renderer.js', __FILE__), array(), $soon_version, true );
	wp_register_script('soon_builder_main', plugins_url('builder/app/main.js', __FILE__), array(), $soon_version, true );

}

// init
function soon_admin_init() {}

function soon_admin_menu() {

	global $soon_version;

	$page = add_options_page(
		'Soon',
		'Soon',
		'manage_options',
		'soon_' . $soon_version,
		'soon_admin_options_page'
	);

	add_action('admin_print_styles-' . $page,'soon_admin_enqueue_styles');
	add_action('admin_print_scripts-' . $page,'soon_admin_enqueue_scripts');

}

function soon_admin_enqueue_styles() {

	wp_enqueue_style('soon_builder_spectrum');
	wp_enqueue_style('soon_builder_datetimepicker');
	wp_enqueue_style('soon_builder_styles');

}

function soon_admin_enqueue_scripts() {

	wp_enqueue_script('soon_builder_jquery');
	wp_enqueue_script('soon_builder_datetimepicker');
	wp_enqueue_script('soon_builder_spectrum');
	wp_enqueue_script('soon_builder_generator');
	wp_enqueue_script('soon_builder_presets');
	wp_enqueue_script('soon_builder_fields');
	wp_enqueue_script('soon_builder_styler');
	wp_enqueue_script('soon_builder_renderer');
	wp_enqueue_script('soon_builder_main');

}

function soon_admin_options_page() {

	require_once('soon-builder.php');

}

function soon_string_startsWith($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function soon_storage_load_callback() {

	global $soon_data_key;
	$data = get_option($soon_data_key);

	if (soon_string_startsWith($data,'{\"')) {
		$data = stripcslashes($data);
	}

	echo $data;

	die();
}


function soon_storage_save_callback() {

	global $soon_data_key;

	$data = $_REQUEST['data'];

	if (add_option( $soon_data_key, $data )==false) {
		update_option( $soon_data_key, $data );
	}

	die();

}


/**
 * TINY MCE SETUP
 */
add_action( 'admin_head', 'soon_add_tinymce' );

function soon_add_tinymce() {

	global $soon_data_key;

	// test page type
	global $typenow;

	if(!in_array($typenow,array('post','page'))){return;}

	// get soon options
	if (get_option($soon_data_key)) {
		add_filter( 'mce_external_plugins', 'soon_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'soon_add_tinymce_button' );
	}

}

function soon_add_tinymce_data() {

	global $soon_data_key;

	// render data
	$data = get_option($soon_data_key);
	if (soon_string_startsWith($data,'{\"')) {
		$data = stripcslashes($data);
	}

	$data = json_decode($data);
	$values = array();
	foreach($data->custom as $item) {
		array_push($values,'{text:"' . $item->name . '",value:"' . $item->name . '"}');
	}
	echo '<script>var soonCounterData = [' . implode(',',$values) .'];</script>';

}

function soon_add_tinymce_plugin( $plugin_array ) {

	soon_add_tinymce_data();

	// load file
	$plugin_array['soon'] = plugins_url( '/builder/index.js', __FILE__ );
	return $plugin_array;

}

function soon_add_tinymce_button( $buttons ) {

	array_push( $buttons, 'soon_counters' );
	return $buttons;

}