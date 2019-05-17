<?php

require_once("TabBase.php");

class bg_show_hide_Presets extends bg_show_hide_TabBase {
	public function __construct( $pluginContext, $tabName) {
		parent::__construct( $tabName, "Presets", $pluginContext );
	}
	
	public function display() {
		$this->_html = "<h2>Add your shortcode presets</h2>";
		$this->_html .= "<form id=\"bg_shce_Presets\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
			<table class=\"form-table\">
				<tr>
					<th>Preset 1</th>
					<td><textarea name=\"bg_preset1\" id=\"bg_preset1\" value=\"%%PRESET_1%%\" rows=\"4\" cols=\"50\" disabled>%%PRESET_1%%</textarea></td>
				</tr>
			</table>
			<input type=\"submit\" class=\"button button-primary\" id=\"save_plugin_presets\" name=\"save_plugin_presets\" value=\"Save Plugin Presets\" disabled>
		</form>
		<p style=\"width:600px;\">
		To enable presets, upgrade to our premium version <a href=\"https://bunte-giraffe.de/product/show-hide-collapse-expand-faq/\"><b>Show-Hide/Collapse-Expand + FAQ</b> for <b>&euro;6.95</b> </a>. <br>
		In addition to presets it allows you to create great FAQs,
		change the initial state of the content (collapsed or expanded), use as accordion, group multiple items to be collapsed/expanded simultaneously and more. <br>Check our website <a href=\"https://bunte-giraffe.de\">bunte-giraffe.de</a> to see the list of features in the premium version.
		<br><br>
		Presets allow you to optimize your workflow with Show-Hide/Collapse-Expand and minimize the time you spend selecting options each time you insert the shortcode.
		Great thing about presets is that when you decide to change the look and feel of your shortcodes, you will only need to do it once on this tab and all the shortcodes
		on your website done with the preset will be updated. Plus you can override any shortcode parameters set in the preset by adding them to your shortcode upon inserting.
		</p>";
		
		$this->_html = str_replace( "%%ADMIN_POST_URL%%",
			$this->getPluginContext()->getAdminPostUrl(),
			$this->_html );
						
		$this->_html = str_replace( "%%PRESET_1%%",
			str_replace('\"', '"', $this->getPluginContext()->getPreset1()),
			$this->_html );
					
		return $this->_html;
		
	}
	
	private $_html = '';
};