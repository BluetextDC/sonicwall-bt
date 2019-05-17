<?php

require_once("TabBase.php");

class bg_show_hide_Settings extends bg_show_hide_TabBase {
	public function __construct( $pluginContext, $tabName) {
		parent::__construct( $tabName, "Settings", $pluginContext );
	}
	
	public function display() {
		$this->_html = "<h2>Add effects and change animation speed</h2>";
		$this->_html .= "<form id=\"bg_shce_Settings\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
			<table class=\"form-table\">
				<tr>
					<th colspan=\"2\"><input type=\"checkbox\" name=\"enable_effects\" id=\"enable_effects\" value=\"%%EFFECTS_ENABLED%%\" %%EFFECTS_ENABLED_DEFAULT%% onclick=\"if (!this.checked) {jQuery('.bg-effects').hide();} else { jQuery('.bg-effects').show();}\"> Enable jQuery UI effects </th>
				</tr>
				<tr class=\"bg-effects\" style=\"display:none;\">
					<th>
						<label for=\"bg_shce_effect\">Effect for displaying <br>hidden content: </label>
					</th>
					<td>
						<select name=\"bg_shce_effect\" id=\"bg_shce_effect\" >	
							<option value=\"blind\" id=\"bg-blind\">Blind</option>
							<option value=\"fold\" id=\"bg-fold\">Fold</option>
							<option value=\"highlight\" id=\"bg-highlight\">Highlight</option>
							<option value=\"slide\" id=\"bg-slide\">Slide</option>
						</select>
						<button onclick=\"event.preventDefault();jQuery(this).next().children().toggle(jQuery(this).prev().val(), 'swing', parseInt( jQuery('#bg_shce_speed').val() )+1 );\" class=\"button button-secondary\">Preview</button>
						<div style=\"clear:both;margin-top:10px;margin-bottom:10px;height:50px;\">
							<div style=\"border: 1px solid #0085ba; padding: 20px; width:250px;\">
								Click <b>Preview</b> to see how the effect <br>
								will be displayed when you use it
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<label for=\"bg_shce_speed\">Custom animation speed (ms): </label>
					</th>
					<td>
						<input name=\"bg_shce_speed\" id=\"bg_shce_speed\" type=\"text\" value=\"%%ANIMATION_SPEED%%\">							
					</td>
				</tr>
				<tr>
					<th>
						<label for=\"bg_shce_speed\">Make buttons and links stick to the bottom of collapsed sections</label>
					</th>
					<td>
						<input name=\"bg_shce_stick_to_bottom\" id=\"bg_shce_stick_to_bottom\" type=\"checkbox\" value=\"%%STICK_TO_BOTTOM%%\" %%STICK_TO_BOTTOM_DEFAULT%%>							
					</td>
				</tr>
			</table>
			<input type=\"submit\" class=\"button button-primary\" id=\"save_plugin_settings\" name=\"save_plugin_settings\" value=\"Save Plugin Settings\">
			<input type=\"hidden\" name=\"action\" value=\"bg_show_hide_save_plugin_settings\">
		</form>";
		
		$this->_html = str_replace( "%%ADMIN_POST_URL%%",
			$this->getPluginContext()->getAdminPostUrl(),
			$this->_html );
						
		$this->_html = str_replace( "%%EFFECTS_ENABLED_DEFAULT%%",
			$this->getPluginContext()->getEffectsEnabledOption() === '1' ? 'checked' : '',
			$this->_html );		

		$this->_html = str_replace( "%%EFFECTS_ENABLED%%",
			$this->getPluginContext()->getEffectsEnabledOption(),
			$this->_html );		
		
		$this->_html = str_replace( "%%ANIMATION_EFFECT%%",
			$this->getPluginContext()->getAnimationEffect(),
			$this->_html );
						
		$this->_html = str_replace( "%%ANIMATION_SPEED%%",
			$this->getPluginContext()->getAnimationSpeed(),
			$this->_html );
			
		$this->_html = str_replace( "%%STICK_TO_BOTTOM%%",
			$this->getPluginContext()->getStickToBottom(),
			$this->_html );	
			
		$this->_html = str_replace( "%%STICK_TO_BOTTOM_DEFAULT%%",
			$this->getPluginContext()->getStickToBottom() === '1' ? 'checked' : '',
			$this->_html );	
			
		wp_localize_script( "bg-show-hide-script", 'BG_SHCE_USE_EFFECTS', $this->getPluginContext()->getEffectsEnabledOption());
		wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_SPEED', $this->getPluginContext()->getAnimationSpeed());
		wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_OPTIONS', 'none');
		wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_EFFECT', $this->getPluginContext()->getAnimationEffect());	
		
		return $this->_html;
		
	}
	
	private $_html = '';
};