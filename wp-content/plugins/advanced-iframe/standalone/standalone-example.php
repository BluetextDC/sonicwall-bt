<html>
<head>
<meta charset="UTF-8">
<title>Example Advanced iFrame Pro standalone</title>
<style>
body { color: #444; font-family: "Open Sans",sans-serif; font-size: 13px; line-height: 1.4em; min-width: 600px; background-color: #f5f5f5; }
h2 { color: #222; font-size: 1.5em; font-weight: 400; margin-bottom: 0.83em; margin-top: 0.83em; }
h1, h2, h3, h4, h5, h6 { font-weight: 600; }
.code { border: 1px solid #bbb; background-color: #f5f5f5; padding:10px; margin: 10px; width:500px;}
#content {padding: 20px; margin-left: auto; margin-right: auto; width:800px; background-color: #fff; }
</style>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../js/ai.js"></script>
</head>   
<body>
<div id="content">
<h1>Advanced iframe pro standalone examples</h1>
<p>
This are some examples how to use the advanced iframe pro plugin standalone without wordpress. The examples below start from a simple include but also show many of the examples you already find in the pro demo. See the setting files below as examples. standalone-advanced-iframe-settings.php is the default if nothing is specified.
</p>
<p>All settings have to be defined in an array $iframeStandaloneOptions where you can use ANY shortcode attribute! Simply use the shortcode attributes without the security_code and the use_shortcode_only.
</p>  
<p>
Please read the <a href="readme.html" target="_blank">readme.html</a> first as there you find very important information about the setup there.
</p>

<p>
This page does load several settings files and includes the standalone-advanced-iframe.php several times. So please check the source code of this page and the setting files of each example.<br />
You see that the settings file do match a standalone shortcode exept that no security key is needed.  
</p>
<h2>Simple iframe example</h2>
<p>
This simple example shows the TinyWebGallery homepage with the following features:
<ul>
<li>Simply include of www.tinywebgallery.com</li>
<li>Lazy load</li>
<li>Show iframe loader</li>
</ul>
</p>
<p>
The settings file is: standalone-advanced-iframe-settings.php
<div class="code">
$iframeStandaloneOptions = array(            <br />
&nbsp;&nbsp;&nbsp;   'id' => 'example1',                       <br />
&nbsp;&nbsp;&nbsp;   'name' => 'example1',                     <br />
&nbsp;&nbsp;&nbsp;   'src' => 'http://www.tinywebgallery.com', <br /> 
&nbsp;&nbsp;&nbsp;   'width' => '600',                         <br />
&nbsp;&nbsp;&nbsp;   'height' => '300',                        <br />
&nbsp;&nbsp;&nbsp;   'enable_lazy_load' => 'true',             <br />
&nbsp;&nbsp;&nbsp;   'show_iframe_loader' => 'true'            <br />
);                                          
</div>
</p>

<?php
// No settings file is specified: standalone-advanced-iframe-settings.php ist used.
// here you should set the $site_url e.g.
// $site_url = 'http://www.yourdomain.com/path-to-advanced-iframe-parent-folder/advanced-iframe';
// $site_url = 'http://www.tinywebgallery.com/advanced-iframe-standalone'
include 'standalone-advanced-iframe.php'; 
?>
<h2>Show only a part of the iframe</h2>
<p>
If you are not on the same domain and you are not able to modify the external page you want to include than the only option you have is to use the pro version and include only a part of the iframe. For this solution you need to specify the size if the iframe and also a "viewport" that defines which area you want to show. This example can also be found for Wordpress <a href="http://www.tinywebgallery.com/blog/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe" target="_blank">here</a>. 
</p>
<p>
The settings file is: standalone-advanced-iframe-settings2.php
<div class="code">
$iframeStandaloneOptions = array(            <br />
&nbsp;&nbsp;&nbsp;   'id' => 'example2',     <br />
&nbsp;&nbsp;&nbsp;   'name' => 'example2',   <br />
&nbsp;&nbsp;&nbsp;   'src' => 'http://examples.tinywebgallery.com/example1u2/screenshot.html', <br /> 
&nbsp;&nbsp;&nbsp;   'width' => '1000',                                                        <br />
&nbsp;&nbsp;&nbsp;   'height' => '2000',                                                       <br />
&nbsp;&nbsp;&nbsp;   'show_part_of_iframe' => 'true',                                          <br />
&nbsp;&nbsp;&nbsp;   'show_part_of_iframe_x' => '290',                                         <br />
&nbsp;&nbsp;&nbsp;   'show_part_of_iframe_y' => '670',                                         <br />
&nbsp;&nbsp;&nbsp;   'show_part_of_iframe_width' => '570',                                     <br />
&nbsp;&nbsp;&nbsp;   'show_part_of_iframe_height' => '320',                                    <br />
&nbsp;&nbsp;&nbsp;   'enable_lazy_load' => 'true',                                             <br />
&nbsp;&nbsp;&nbsp;   'show_iframe_loader' => 'true'                                            <br />
);                                          
</div>
</p>
<?php
$ai_settings_file = 'standalone-advanced-iframe-settings2.php';
include 'standalone-advanced-iframe.php'; 
?>
<h2>
Auto height on the same domain
</h2>
<p>
This example shows how auto heigth and css modifications are used with the standalone version.<br />
The following features are shown:
<ul>
<li>The following page is included: <a target="_blank" href="../example/example_detail1.html">../example/example_detail1.html</a></li>
<li>Auto height on the same domain</li>
<li>Modification of the css on the same domain. Header and footer was removed and the textcolor was changed</li>
</ul> 
</p>
<p>
The settings file is: standalone-advanced-iframe-settings3.php
<div class="code">
$iframeStandaloneOptions = array(                                                                       <br />
&nbsp;&nbsp;&nbsp;   'id' => 'example3',                                                                <br />
&nbsp;&nbsp;&nbsp;   'name' => 'example3',                                                              <br />
&nbsp;&nbsp;&nbsp;   'src' => '../example/example_detail1.html',                                        <br />
&nbsp;&nbsp;&nbsp;   'width' => '700',                                                                  <br />
&nbsp;&nbsp;&nbsp;   'height' => '100',                                                                 <br />
&nbsp;&nbsp;&nbsp;   'onload_resize' => 'true',                                                         <br />
&nbsp;&nbsp;&nbsp;   'iframe_hide_elements' => '#iframe-header,#iframe-footer,#iframe-navigation a',    <br />
&nbsp;&nbsp;&nbsp;   'iframe_content_id' => '#iframe-navigation',                                       <br />
&nbsp;&nbsp;&nbsp;   'iframe_content_styles' => 'color:#f00'                                            <br />
);                                          
</div>
</p>
<?php
$ai_settings_file = 'standalone-advanced-iframe-settings3.php';
include 'standalone-advanced-iframe.php';
?>
<h2>
Auto height using the external workaround
</h2>
<p>
This example shows how the external workaround is used with the standalone version. One important difference to the Wordpress version is the usage of the <a href="../documentation/Advanced iFrame Pro attribute help.htm#xss" target="_blank">external workaround</a>. In the wordpress version the ai_external is generated with the settings from the database while for the standalone version the file standalone-ai_external.js has to be used and the configuration has to be made before you include the file! Examples of the external workaround can also be found for Wordpress <a href="http://www.tinywebgallery.com/blog/advanced-iframe/advanced-iframe-pro-demo/external-workaround-auto-height-and-css-modifications" target="_blank">here</a>. 
The following features are shown:
<ul>
<li>The following page is included: <a target="_blank" href="../example/example_standalone.html">../example/example_standalone.html</a></li>
<li>Auto height on a different domain</li>
<li>Modification of the css on a different domain. Only #iframe-right is shown and the text color was changed.</li>
<li>Resize on element resize on a different domain</li>
</ul> 
</p>
<p>
The settings file is: standalone-advanced-iframe-settings4.php
<div class="code">
$iframeStandaloneOptions = array(                 <br />
&nbsp;&nbsp;&nbsp;    'id' => 'advanced_iframe',  <br />
&nbsp;&nbsp;&nbsp;    'name' => 'advanced_iframe',<br />
&nbsp;&nbsp;&nbsp;   'scrolling' => 'no',         <br />
&nbsp;&nbsp;&nbsp;   'width' => '950',            <br />
&nbsp;&nbsp;&nbsp;   'height' => '200px',         <br />
&nbsp;&nbsp;&nbsp;   'src' => '../example/example_standalone.html', <br />
);</div>
<p>The settings for the external workaround are stored in a file called: standalone-ai_external_config_4.js
<div class="code">
&nbsp;&nbsp;&nbsp;   var iframe_id="advanced_iframe";                                     <br />
&nbsp;&nbsp;&nbsp;   var updateIframeHeight = "true";                                     <br />
&nbsp;&nbsp;&nbsp;   var keepOverflowHidden = "false";                                    <br />
&nbsp;&nbsp;&nbsp;   var hide_page_until_loaded_external = "true";                        <br />
&nbsp;&nbsp;&nbsp;   var onload_show_element_only = "#iframe-right";                      <br />
&nbsp;&nbsp;&nbsp;   var iframe_content_id = "#iframe-right";                             <br />
&nbsp;&nbsp;&nbsp;   var iframe_content_styles = "float:none;height:auto;color:#ff0000;"; <br />
&nbsp;&nbsp;&nbsp;   var resize_on_element_resize = "#iframe-right";                      <br />
&nbsp;&nbsp;&nbsp;   var resize_on_element_resize_delay = "50";                           <br />
&nbsp;&nbsp;&nbsp;   var domain_advanced_iframe = '../';                                  </div>
<p>This is the code you need to include into your external file:
<div class="code">
&lt;script src="../standalone/standalone-ai_external_config_4.js">&lt;/script&gt;<br>
&lt;script src="../standalone/standalone-ai_external.js">&lt;/script&gt;</div>
 
</p>
<?php
$ai_settings_file = 'standalone-advanced-iframe-settings4.php';
include 'standalone-advanced-iframe.php';
?>
<h2>
More examples
</h2>
<p>
As you most likely have found out, you can simply use any of the examples of the <a href="http://www.tinywebgallery.com/blog/advanced-iframe/advanced-iframe-pro-demo" target="_blank">pro demo</a>. Simply use the shortcode attributes without the security_code and the use_shortcode_only. For examples that use the external workaround you have to use the provided standalone version standalone-ai_external.js instead the ai_external.js AND you need to set domain_advanced_iframe before you include the Javascript (see the "Auto height using the external workaround" example above). 
</p>
<p>
Have fun using Advanced iFrame Pro<br />
Michael 
</p>
</div>
</body>
</html>