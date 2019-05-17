<?php

require_once("TabBase.php");

class bg_show_hide_help extends bg_show_hide_TabBase {
	public function __construct( $pluginContext, $tabName) {
		parent::__construct( $tabName, "Help", $pluginContext );
	}
	
	public function display() {
		$this->_html = "<h2>How to Use in Posts/Pages?</h2>";
		$this->_html .= "Click the <img src=". plugins_url( "assets/img/tinymce-button.png", __FILE__ ) ." style=\"vertical-align:bottom\"> button in your editor to customize the shortcode parameters and insert shortcode.";
		$this->_html .= "<h2>[bg_collapse] Shortcode Example</h2>";
		$this->_html .= "[bg_collapse view=\"button-orange\" color=\"#72777c\" icon=\"arrow\" expand_text=\"Show More\" collapse_text=\"Show Less\" ]<br>This text will be hidden.<br>[/bg_collapse]";
		$this->_html .= "<h2>[bg_collapse] Shortcode Parameters</h2>";
		$this->_html .= "<ul>";
		$this->_html .= "<li> Type of control to expand/collapse content: <br><b>view</b>=\"button-orange | button-red | button-blue | button-green | link | link-inline | link-list \" </li>";
		$this->_html .= "<li> Link or button text color: <br><b>color</b>=\"any suitable for css value, e.g. #ffffff or white \"</li>";
		$this->_html .= "<li> Button or link icon: <br><b>icon</b>=\"arrow | zoom | eye \" </li>";
		$this->_html .= "<li> Label for expanding content: <br><b>expand_text</b>=\"any text value \"</li>";
		$this->_html .= "<li> Label for collapsing content: <br><b>collapse_text</b>=\"any text value \"</li>";
		$this->_html .= "<li> Inline CSS: <br><b>inline_css</b>=\"any css rule, e.g. font-size: 21px; font-weight: normal; \"</li>";
		$this->_html .= "<li> Onclick JS function: <br><b>onclick</b>=\"your custom JS function name or inline js, e.g. alert('Hello World!'); \"</li>";
		$this->_html .= "</ul>";
		$this->_html .= "<h2>[bg_collapse] Button Examples</h2>";
		$this->_html .= '<table><tr><td>(orange)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-orange-button" style=" color:#4a4949 ;">Show More</button> </td><td>&nbsp;</td>' .
		'<td>(green+arrow)</td><td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-green-button bg-arrow" style=" color:#4a4949 ;">Show More</button> </td></tr>'.
		'<tr><td>(red+zoom)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-red-button bg-zoom" style=" color:#ffffff ;">Show More</button> </td><td>&nbsp;</td>'.
		'<td>(blue+eye)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-blue-button bg-eye" style=" color:#ffffff ;">Show More</button> </td></tr></table>';
		$this->_html .= "<h2>Using [bg_collapse] Shortcode for Collapsing Lists</h2>";
		$this->_html .= "<p>Please note that when you are inserting the shortcode to collapse/expand lists, you will need to slightly modify the list shown in your editor after the insertion. Please follow the steps below to achieve the best results: </p>";
		$this->_html .= "<ol><li>Select the items of your list that you wish to be collapsed/expanded.</li><li>Click the <img src=". plugins_url( "assets/img/tinymce-button.png", __FILE__ ) ." style=\"vertical-align:bottom\"> button and then select <strong>Link In List</strong> from the <strong>Expand/collapse content using</strong> dropdown.</li><li>Make all the needed adjustments in the <strong>Insert Collapse-Expand Shortcode</strong> popup and click <strong>OK</strong>.</li><li>Select all the list items that are inside the shortcode tags and appear nested and click the <strong>Bulleted List</strong> button in your Editor. The items will appear as separate paragraphs, one item per line.</li><li>Repeat the previous step. Now all items: before the opening shortcode tag, inside the shortcode tags and after the closing shortcode tag belong to one list.</li><li>Make sure that the closing shortcode tag is not listed as a separate item. If it is, simply move it to the previous item of your list.</li></ol>";
		$this->_html .= "<p>The screencast below shows you a quick demo on how to create collapsed/expanded lists.</p>";
		//$this->_html .= "<blockquote style=\"border: 1px solid #333;width: 350px;\"><ul style=\"list-style-type: disc; padding-left:30px\"><li>Some</li><li>List</li><li>[bg_collapse view=\"link-list\" color=\"#4a4949\" icon=\"eye\" expand_text=\"\" collapse_text=\" \" ]</li><li>With</li><li>A few</li><li>More</li><li>Hidden</li><li>Items [/bg_collapse]</li><li>Is</li><li>Displayed</li><li>Here</li></ul></blockquote>";
		$this->_html .= "<img src=". plugins_url( "assets/img/link-in-list.gif", __FILE__ ) .">";

		return $this->_html;
		
	}
	
	private $_html = '';
};