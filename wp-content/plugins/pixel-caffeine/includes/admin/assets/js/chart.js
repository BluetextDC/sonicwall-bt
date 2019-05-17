/**
 * UI scripts for admin settings page
 */

import $ from 'jquery';
import Highcharts from 'highcharts/highstock';

jQuery(document).ready(function(){
    'use strict';

	let chartBox = $('#activity-chart');
	if ( chartBox.length ) {
		$.getJSON( aepc_admin.ajax_url + '?action=' + aepc_admin.actions.get_pixel_stats.name + '&_wpnonce=' + aepc_admin.actions.get_pixel_stats.nonce, function (stats) {
			if ( typeof stats.success !== 'undefined' && false === stats.success ) {
				Utils.addMessage( chartBox, 'info', stats.data[0].message );
				return;
			}

			let getTextWidth = function(text) {
				// re-use canvas object for better performance
				let canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
				let context = canvas.getContext("2d");
				context.font = 'normal 12px sans-serif';
				let metrics = context.measureText(text);
				return metrics.width;
			};

			// Set default min range as soon as the chart is initialized
			var	defaultMinRangeDate = new Date();
			defaultMinRangeDate.setUTCDate( defaultMinRangeDate.getUTCDate() - 7 );
			defaultMinRangeDate.setUTCHours( 0, 0, 0, 0 );

			Highcharts.stockChart( 'activity-chart', {
				chart: {
					type: 'line'
				},

				title: {
					text: null
				},

				navigator: {
					enabled: true
				},

				rangeSelector : {
					enabled: false
				},

				plotOptions: {
					spline: {
						marker: {
							enabled: true
						}
					}
				},

				xAxis: {
					min: defaultMinRangeDate.getTime()
				},

				yAxis: {
					gridLineColor: "#F4F4F4"
				},

				series: [{
					name: 'Pixel fires',
					data: stats,
					dataGrouping: {
						approximation: 'sum',
						forced: true,
						units: [['day', [1]]]
					},
					pointInterval: 3600 * 1000 // one hour
				}]
			});

			chartBox.closest('.panel').find('select#date-range').select2({
				minimumResultsForSearch: 5,
				width: 'element'
			});

			// Set date range
			chartBox.closest('.panel').on( 'change.chart.range', 'select#date-range', function() {
				let chart = chartBox.highcharts(),
					range = $(this).val(),
					today = new Date(),
					yesterday = new Date();

				yesterday.setDate( today.getUTCDate() - 1 );

				if ( 'today' === range ) {
					chart.xAxis[0].setExtremes( today.setUTCHours( 0, 0, 0, 0 ), today.setUTCHours( 23, 59, 59, 999 ) );
					chart.xAxis[0].setDataGrouping({
						approximation: 'sum',
						forced: true,
						units: [['hour', [1]]]
					});
				}

				else if ( 'yesterday' === range ) {
					chart.xAxis[0].setExtremes( yesterday.setUTCHours( 0, 0, 0, 0 ), yesterday.setUTCHours( 23, 59, 59, 999 ) );
					chart.xAxis[0].setDataGrouping({
						approximation: 'sum',
						forced: true,
						units: [['hour', [1]]]
					});
				}

				else if ( 'last-7-days' === range ) {
					let last_7_days = yesterday;
					last_7_days.setDate( today.getUTCDate() - 7 );
					chart.xAxis[0].setExtremes( last_7_days.setUTCHours( 0, 0, 0, 0 ), today.setUTCHours( 23, 59, 59, 999 ) );
					chart.xAxis[0].setDataGrouping({
						approximation: 'sum',
						forced: true,
						units: [['day', [1]]]
					});
				}

				else if ( 'last-14-days' === range ) {
					let last_14_days = yesterday;
					last_14_days.setDate( today.getUTCDate() - 14 );
					chart.xAxis[0].setExtremes( last_14_days.setUTCHours( 0, 0, 0, 0 ), today.setUTCHours( 23, 59, 59, 999 ) );
					chart.xAxis[0].setDataGrouping({
						approximation: 'sum',
						forced: true,
						units: [['day', [1]]]
					});
				}
			});

		});
	}

});
