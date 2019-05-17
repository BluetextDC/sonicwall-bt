<?php

class Gf_Stla_Welcome_Page{

  function __construct() {
    add_action( 'admin_menu', array( $this, 'register_menu' ) );
  }

    public function register_menu() {

    add_submenu_page(  'stla_licenses', 'Documentation', 'Documentation', 'manage_options', 'stla-documentation', array( $this, 'show_documentation' ) );
    //add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );


  }

  function show_documentation(){
    $gf_stla_version = get_plugin_data( GF_STLA_DIR.'/styles-layouts-gravity-forms.php', $markup = true, $translate = true );
     
?>
<div class="wrap" >

  <h1 class="stla-plugin-title"><?php printf( __( 'Styles & Layouts for Gravity Forms &nbsp;v%s' ), $gf_stla_version["Version"] ); ?></h1>

  <div class="about-text">
    <?php printf(  '<span class="stla-intro"> Styles & Layout for Gravity Forms lets you create beautiful designs for your Forms with WordPress "Customizer". All the changes are previewed instantly.</span> ' ); ?>
  </div>
  <div class="stla-left-container">
    <div class="stla-youtube-video">
    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/bkiBdaxIPjY" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div> 
  </div>
  <div class="stla-right-container">
  <h2 class="stla-sec-heading" style="margin-top:0px;">Features:</h2>

    <ul class="stla-featurelist">
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Live preview all changes.</span></li>
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Large set of options to customiz.</span></li>
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Create different designs for different forms.</span></li>
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Live preview all changes.</span></li>
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Large set of options to customiz.</span></li>
      <li><span class="stla-feature-icons"></span> <span class="stla-feature-text">Create different designs for different forms.</span></li>
    </ul>
    <div class="stla-btn-cont">
      <a href="https://paypal.me/wpmonks" class="stla-btn">Donate to Support Plugin</a>
    </div>
    <br />
    <h2 class="stla-sec-heading" style="margin-top:0px;">Follow us:</h2>
    <ul class="stla-social-list">
      <li class="stla-facebook"><a href="https://www.facebook.com/wpmonks.dev.5"><span class="dashicons dashicons-facebook-alt"></span></a></li>
      <li class="stla-youtube"><a href="https://www.youtube.com/channel/UCVFPUR4yYsMIrayPOqFQspA"><span class="dashicons dashicons-video-alt3"></span></a></li>
      <li class="stla-instagram"><a href="https://www.instagram.com/wpmonks/"><span class="dashicons dashicons-camera"></span></a></li>
      <li class="stla-twitter"><a href="https://twitter.com/wp_monk"><span class="dashicons dashicons-twitter"></span></a></li>
    </ul>
  </div>

  <div class="stla-seond-sec stla-left-container"> 
  <h2 class="stla-sec-heading">How to use:</h2>
    <ul class="stla-inst-list">
          <?php if ( ! class_exists('RGFormsModel') ) { ?>
            <li><strong>Step #0: </strong><a href="http://gravityforms.com/" target="_blank">Install & Activate <code>Gravity Forms</code></a>.</li>
          <?php } ?>
          <li style="margin-bottom: 22px; margin-top: 16px;"> 
          <div class="sk-inst-text"><strong>Step #1:</strong> Go to 'Forms' and edit the form you want to design.</div>
           <div class="stla-inst-img"><img class="stla-image" src="<?php echo GF_STLA_URL . '/admin-menu/images/step1.png'; ?>" /></div> 
          </li>
          <li style="margin-bottom: 22px; margin-top: 16px;">
          <div class="sk-inst-text"> <strong>Step #2:</strong> Click on 'Styles & Layouts' option and design the form.</div>  
          <div class="stla-inst-img"><img class="stla-image" src="<?php echo GF_STLA_URL . '/admin-menu/images/step2.png'; ?>" /></div> 
          </li>
          <li style="margin-bottom: 22px; margin-top: 16px;">     
          <div class="sk-inst-text"><strong>Step #3:</strong> Every change will be previewed instantly. Now click on <strong>Save and Publish</strong> button to save the changes.</div>
          <div class="stla-inst-img"><img class="stla-image" src="<?php echo GF_STLA_URL . '/admin-menu/images/step5.png'; ?>" /></div>
          </li>
          <li style="margin-bottom: 22px; margin-top: 16px;"><strong> <a href="https://wpmonks.com/contact-us/">Contact us</a> for support or custom work. <a href="https://wordpress.org/support/plugin/styles-and-layouts-for-gravity-forms/reviews/#new-post">Rate plugin</a> on WordPress.org to support our work.</strong> </li>
    </ul>
  </div>
  <div class="stla-right-container">
  <h2 class="stla-sec-heading">Gravity Forms Addons:</h2>

<a href="https://wpmonks.com/downloads/addon-bundle?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/addon-bundle.jpg"></a>
<a href="https://wpmonks.com/downloads/material-design?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/material-design.jpg"></a>
<a href="https://wpmonks.com/downloads/bootstrap?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/Bootstrap.png"></a>
<a href="https://wpmonks.com/downloads/theme-pack?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/themes-pack.jpg"></a>
<a href="https://wpmonks.com/downloads/grid-layout?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/grid-layout.jpg"></a>
<a href="https://wpmonks.com/downloads/field-icons?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/field-icons.jpg"></a>
<a href="https://wpmonks.com/downloads/tooltips?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/tooltip.jpg"></a>
<a href="https://wpmonks.com/downloads/custom-themes?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/custom-themes.jpg"></a>
<a href="https://wpmonks.com/styles-and-layouts-for-gravity-forms/?src=customizer#x-section-6"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/more-addons.jpg"></a>
<a href="https://wpmonks.com/contact-us/?src=doc-sidebar"><img class="stla-image stla-sidebar-image" src="<?php echo GF_STLA_URL; ?>/css/images/welcome/support.jpg"></a>
  </div>
</div>
 <?php
}
}

new Gf_Stla_Welcome_Page();