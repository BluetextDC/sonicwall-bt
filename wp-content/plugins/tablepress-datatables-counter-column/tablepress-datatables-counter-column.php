<?php
/*
Plugin Name: TablePress Extension: DataTables Counter Column
Plugin URI: https://tablepress.org/extensions/datatables-counter-column/
Description: Custom Extension for TablePress to make it possible to have a fixed "counter column" for DataTables
Version: 1.1
Author: Tobias Bäthge
Author URI: https://tobias.baethge.com/
*/

/*
 * Register necessary Plugin Filters.
 */
add_filter( 'tablepress_shortcode_table_default_shortcode_atts', 'tablepress_add_shortcode_parameters_counter_column' );
add_filter( 'tablepress_table_js_options', 'tablepress_add_counter_column_js_options', 10, 3 );
add_filter( 'tablepress_datatables_parameters', 'tablepress_add_counter_column_js_parameter', 10, 4 );
add_filter( 'tablepress_datatables_command', 'tablepress_add_counter_column_datatables_command', 10, 5 );

/**
 * Add "datatables_counter_column" as a valid parameter to the [table /] Shortcode.
 *
 * @since 1.0
 *
 * @param array $default_atts Default attributes for the TablePress [table /] Shortcode.
 * @return array Extended attributes for the Shortcode.
 */
function tablepress_add_shortcode_parameters_counter_column( $default_atts ) {
	$default_atts['datatables_counter_column'] = false;
	return $default_atts;
}

/**
 * Pass "datatables_counter_column" from Shortcode parameters to JavaScript arguments.
 *
 * @since 1.0
 *
 * @param array  $js_options    Current JS options.
 * @param string $table_id      Table ID.
 * @param array $render_options Render Options.
 * @return array Modified JS options.
 */
function tablepress_add_counter_column_js_options( $js_options, $table_id, $render_options ) {
	$js_options['datatables_counter_column'] = $render_options['datatables_counter_column'];
	return $js_options;
}

/**
 * Evaluate "datatables_counter_column" parameter and add corresponding JavaScript code, if needed.
 *
 * @since 1.0
 *
 * @param string $command    DataTables command.
 * @param string $html_id    HTML ID of the table.
 * @param array  $parameters DataTables parameters.
 * @param string $table_id   Table ID.
 * @param array  $js_options DataTables JS options.
 * @return string Modified DataTables command.
 */
function tablepress_add_counter_column_js_parameter( $parameters, $table_id, $html_id, $js_options ) {
	if ( $js_options['datatables_counter_column'] ) {
		$parameters['columnDefs'] = '"columnDefs": [ { "searchable": false, "orderable": false, "targets": 0 } ]';
	}
	return $parameters;
}

/**
 * Evaluate "datatables_counter_column" parameter and add corresponding JavaScript code, if needed.
 *
 * @since 1.1
 *
 * @param string $command    DataTables command.
 * @param string $html_id    HTML ID of the table.
 * @param array  $parameters DataTables parameters.
 * @param string $table_id   Table ID.
 * @param array  $js_options DataTables JS options.
 * @return string Modified DataTables command.
 */
function tablepress_add_counter_column_datatables_command( $command, $html_id, $parameters, $table_id, $js_options ) {
	if ( empty( $js_options['datatables_counter_column'] ) ) {
		return $command;
	}

	$name = str_replace( '-', '_', "DT-{$html_id}" );
	$command = "$('#{$html_id}').DataTable({$parameters});"; // Change dataTable() to DataTable().
	$command = <<<JS
var {$name} = {$command};
{$name}.on( 'order.dt search.dt', function() {
	{$name}.column( 0, { search: 'applied', order: 'applied' } ).nodes().each( function( cell, i ) {
		cell.innerHTML = i+1;
	} );
} ).draw();
JS;
	return $command;
}
