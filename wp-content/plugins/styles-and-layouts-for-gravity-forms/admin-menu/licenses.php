<?php

class Stla_License_Page{

	function __construct(){
		add_action('admin_menu',array($this,'register_menu') );
		add_action( 'admin_init', array( $this, 'setting_fields' ) );
	}

	public function register_menu(){
		add_menu_page(  'Styles & Layouts', 'Styles & Layouts for GF', 'manage_options', 'stla_licenses' );
		add_submenu_page( 'stla_licenses', 'Licenses', 'Licenses', 'manage_options', 'stla_licenses', array( $this, 'license_settings' ) );
	}

	public function license_settings(){

		?>
			<!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php settings_errors(); ?>
        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'stla_licenses' ); ?>
            <?php do_settings_sections( 'stla_licenses' ); ?>
            <?php submit_button(); ?>
        </form>

    </div><!-- /.wrap -->
	<?php
	}


	function setting_fields(){
		// If settings don't exist, create them.
		if ( false == get_option( 'stla_licenses' ) ) {
			add_option( 'stla_licenses' );
		}


		add_settings_section(
			'stla_licenses_section',
			'Add-On Licenses',
			array( $this, 'section_callback' ),
			'stla_licenses'
		);

		do_action('stla-license-fields',$this);

		//register settings
		register_setting( 'stla_licenses', 'stla_licenses' );

	}

	public function section_callback() {

		echo '<h4> Licence Fields will automatically appear once you install addons for \'Styles & Layouts for Gravity Forms\'. You can check all the available addons <a href="https://wpmonks.com/downloads/addon-bundle?utm_source=licence_page">here</a></h4>';
	}


}

new Stla_License_Page();