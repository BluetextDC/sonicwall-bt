<?php
/*
Plugin Name: Morph, by Bonfire 
Plugin URI: http://bonfirethemes.com/
Description: A Flyout Menu for WordPress. Customize under Appearance → Customize → Morph Plugin.
Version: 2.1
Author: Bonfire Themes
Author URI: http://bonfirethemes.com/
License: GPL2
*/



    //
	// WORDPRESS LIVE CUSTOMIZER
	//
	function morph_theme_customizer( $wp_customize ) {
        
        //
        // ADD "Morph Plugin" PANEL TO LIVE CUSTOMIZER 
        //
        $wp_customize->add_panel('morph_panel', array('title' => __('Morph Plugin', 'morph'),'priority' => 10,));
        
        //
        // ADD "Header Bar & Logo" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_header_logo_section',array('title' => __( 'Header Bar & Logo', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* hide header bar */
        $wp_customize->add_setting('bonfire_morph_hide_header_logo',array('sanitize_callback' => 'sanitize_bonfire_morph_hide_header_logo',));
        function sanitize_bonfire_morph_hide_header_logo( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_hide_header_logo',array('type' => 'checkbox','label' => 'Hide header bar & logo', 'section' => 'morph_header_logo_section',));
        
        /* menu button color */
        $wp_customize->add_setting( 'bonfire_morph_header_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_header_color',array(
            'label' => 'Header background', 'settings' => 'bonfire_morph_header_color', 'section' => 'morph_header_logo_section' )
        ));
        
        /* header opacity */
        $wp_customize->add_setting('bonfire_morph_header_overlay',array('sanitize_callback' => 'sanitize_bonfire_morph_header_overlay',));
        function sanitize_bonfire_morph_header_overlay($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_header_overlay',array(
            'type' => 'text',
            'label' => 'Header opacity',
            'description' => 'From 0-1 (example: 0.9) If empty, defaults to 1',
            'section' => 'morph_header_logo_section',
        ));
        
        /* show shadow behind header */
        $wp_customize->add_setting('bonfire_morph_show_header_shadow',array('sanitize_callback' => 'sanitize_bonfire_morph_show_header_shadow',));
        function sanitize_bonfire_morph_show_header_shadow( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_show_header_shadow',array('type' => 'checkbox','label' => 'Show shadow','description' => 'If ticked, header has a subtle shadow behind it.', 'section' => 'morph_header_logo_section',));
        
        /* logo image */
        $wp_customize->add_setting('bonfire_morph_logo_image');
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,'bonfire_morph_logo_image',
            array(
                'label' => 'Upload logo image',
                'description' => '<h4>How-to add retina logo:</h4> If you would like to add a retina logo, upload both the regular and retina logo files and make sure they are named correctly. If retina logo is uploaded, it will be shown on retina screens with no further setup necessary. <br><br> Example:<br> my-logo.png and my-logo@2x.png (note that the retina logo must have "@2x" at the end).<br><br>',
                'settings' => 'bonfire_morph_logo_image',
                'section' => 'morph_header_logo_section',
        )
        ));
        
        /* logo text color */
        $wp_customize->add_setting( 'bonfire_morph_logo_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_logo_color',array(
            'label' => 'Logo', 'settings' => 'bonfire_morph_logo_color', 'section' => 'morph_header_logo_section' )
        ));
        
        /* logo text hover color */
        $wp_customize->add_setting( 'bonfire_morph_logo_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_logo_hover_color',array(
            'label' => 'Logo hover', 'settings' => 'bonfire_morph_logo_hover_color', 'section' => 'morph_header_logo_section' )
        ));
        
        //
        // ADD "Menu Button" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_menu_button_section',array('title' => __( 'Menu Button', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* hide menu button */
        $wp_customize->add_setting('bonfire_morph_hide_menu_button',array('sanitize_callback' => 'sanitize_bonfire_morph_hide_menu_button',));
        function sanitize_bonfire_morph_hide_menu_button( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_hide_menu_button',array('type' => 'checkbox','label' => 'Hide menu button','description' => 'Useful if you would like to use a custom element as the menu activator (in this case give it the "morph-main-menu-activator" class).', 'section' => 'morph_menu_button_section',));
        
        /* menu button style */
        $wp_customize->add_setting('bonfire_morph_menu_button_style',array(
            'default' => 'style1',
        ));
        $wp_customize->add_control('bonfire_morph_menu_button_style',array(
            'type' => 'select',
            'label' => 'Button style',
            'section' => 'morph_menu_button_section',
            'choices' => array(
                'style1' => 'Style #1',
                'style2' => 'Style #2',
                'style3' => 'Style #3',
        ),
        ));
        
        /* menu button animation */
        $wp_customize->add_setting('bonfire_morph_button_animation',array(
            'default' => 'none',
        ));
        $wp_customize->add_control('bonfire_morph_button_animation',array(
            'type' => 'select',
            'label' => 'Button animation',
            'section' => 'morph_menu_button_section',
            'choices' => array(
                'none' => 'No animation',
                'minussign' => 'Minus sign',
                'xsign' => 'X sign',
        ),
        ));
        
        /* menu button color */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_color',array(
            'label' => 'Button', 'settings' => 'bonfire_morph_menu_button_color', 'section' => 'morph_menu_button_section' )
        ));
        
        /* menu button hover color */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_hover_color',array(
            'label' => 'Button hover', 'settings' => 'bonfire_morph_menu_button_hover_color', 'section' => 'morph_menu_button_section' )
        ));
        
        /* menu button active */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_active_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_active_color',array(
            'label' => 'Button active', 'settings' => 'bonfire_morph_menu_button_active_color', 'section' => 'morph_menu_button_section' )
        ));
        
        /* menu button active hover */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_active_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_active_hover_color',array(
            'label' => 'Button active hover', 'settings' => 'bonfire_morph_menu_button_active_hover_color', 'section' => 'morph_menu_button_section' )
        ));
        
        /* menu button animation speed */
        $wp_customize->add_setting('bonfire_morph_menu_button_speed',array('sanitize_callback' => 'sanitize_bonfire_morph_menu_button_speed',));
        function sanitize_bonfire_morph_menu_button_speed($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_menu_button_speed',array(
            'type' => 'text',
            'label' => 'Animation speed (in seconds)',
            'description' => 'Example: 0.5. If empty, defaults to 0.25',
            'section' => 'morph_menu_button_section',
        ));
        
        /* menu button label */
        $wp_customize->add_setting('morph_menu_button_label',array('default' => '','sanitize_callback' => 'sanitize_morph_menu_button_label',));
        function sanitize_morph_menu_button_label($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('morph_menu_button_label',array('type' => 'text','label' => 'Label','description' => 'If empty, no label will be shown','section' => 'morph_menu_button_section',));
        
        /* menu button label color */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_label_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_label_color',array(
            'label' => 'Label', 'settings' => 'bonfire_morph_menu_button_label_color', 'section' => 'morph_menu_button_section' )
        ));
        
        /* menu button label hover color */
        $wp_customize->add_setting( 'bonfire_morph_menu_button_label_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_menu_button_label_hover_color',array(
            'label' => 'Label hover', 'settings' => 'bonfire_morph_menu_button_label_hover_color', 'section' => 'morph_menu_button_section' )
        ));
        
        //
        // ADD "Heading" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_heading_section',array('title' => __( 'Heading', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* heading text */
        $wp_customize->add_setting('bonfire_morph_heading_text',array('sanitize_callback' => 'sanitize_bonfire_morph_heading_text',));
        function sanitize_bonfire_morph_heading_text($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_heading_text',array(
            'type' => 'text',
            'label' => 'Heading text',
            'section' => 'morph_heading_section',
        ));
        
        /* heading text color */
        $wp_customize->add_setting( 'bonfire_morph_heading_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_heading_text_color',array(
            'label' => 'Heading text', 'settings' => 'bonfire_morph_heading_text_color', 'section' => 'morph_heading_section' )
        ));
        
        /* subheading text */
        $wp_customize->add_setting('bonfire_morph_subheading_text',array('sanitize_callback' => 'sanitize_bonfire_morph_subheading_text',));
        function sanitize_bonfire_morph_subheading_text($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_subheading_text',array(
            'type' => 'text',
            'label' => 'Sub-heading text',
            'section' => 'morph_heading_section',
        ));
        
        /* sub-heading text color */
        $wp_customize->add_setting( 'bonfire_morph_subheading_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_subheading_text_color',array(
            'label' => 'Sub-heading text', 'settings' => 'bonfire_morph_subheading_text_color', 'section' => 'morph_heading_section' )
        ));
        
        /* heading image */
        $wp_customize->add_setting('bonfire_morph_heading_image');
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,'bonfire_morph_heading_image',
            array(
                'label' => 'Heading image',
                'settings' => 'bonfire_morph_heading_image',
                'section' => 'morph_heading_section',
        )
        ));
        
        /* heading image as pattern */
        $wp_customize->add_setting('bonfire_morph_heading_image_pattern',array('sanitize_callback' => 'sanitize_bonfire_morph_heading_image_pattern',));
        function sanitize_bonfire_morph_heading_image_pattern( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_heading_image_pattern',array('type' => 'checkbox','label' => 'Show heading image as pattern', 'section' => 'morph_heading_section',));
        
        /* heading height */
        $wp_customize->add_setting('bonfire_morph_heading_height',array('sanitize_callback' => 'sanitize_bonfire_morph_heading_height',));
        function sanitize_bonfire_morph_heading_height($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_heading_height',array(
            'type' => 'text',
            'label' => 'Custom heading height (in pixels)',
            'description' => 'If empty, defaults to 200px',
            'section' => 'morph_heading_section',
        ));
        
        /* heading overlay color */
        $wp_customize->add_setting( 'bonfire_morph_heading_overlay_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_heading_overlay_color',array(
            'label' => 'Heading overlay', 'settings' => 'bonfire_morph_heading_overlay_color', 'section' => 'morph_heading_section' )
        ));
        
        /* heading overlay opacity */
        $wp_customize->add_setting('bonfire_morph_heading_overlay',array('sanitize_callback' => 'sanitize_bonfire_morph_heading_overlay',));
        function sanitize_bonfire_morph_heading_overlay($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_heading_overlay',array(
            'type' => 'text',
            'label' => 'Heading overlay opacity',
            'description' => 'From 0-1 (example: 0.5) If empty, defaults to 0.2',
            'section' => 'morph_heading_section',
        ));

        /* heading bottom border color */
        $wp_customize->add_setting( 'bonfire_morph_heading_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_heading_border_color',array(
            'label' => 'Bottom border', 'settings' => 'bonfire_morph_heading_border_color', 'section' => 'morph_heading_section' )
        ));
        
        /* heading bottom border thickness */
        $wp_customize->add_setting('bonfire_morph_border_thickness',array('sanitize_callback' => 'sanitize_bonfire_morph_border_thickness',));
        function sanitize_bonfire_morph_border_thickness($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_border_thickness',array(
            'type' => 'text',
            'label' => 'Bottom border thickness (in pixels)',
            'description' => 'If empty, defaults to 3. To hide border, enter value as 0',
            'section' => 'morph_heading_section',
        ));
        
        //
        // ADD "Main Menu" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_main_menu_section',array('title' => __( 'Main Menu', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* alternate sub-menu activation */
        $wp_customize->add_setting('bonfire_morph_sub_menu_activation',array('sanitize_callback' => 'sanitize_bonfire_morph_sub_menu_activation',));
        function sanitize_bonfire_morph_sub_menu_activation( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_sub_menu_activation',array('type' => 'checkbox','label' => 'Alternate sub-menu activation','description' => 'Makes entire top-level menu item open the sub-menu. If unchecked, only the arrow icon opens sub-menu.', 'section' => 'morph_main_menu_section',));
        
        /* main menu background color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_background_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_background_color',array(
            'label' => 'Background', 'settings' => 'bonfire_morph_main_menu_background_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu item color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_item_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_item_color',array(
            'label' => 'Menu item', 'settings' => 'bonfire_morph_main_menu_item_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu item hover color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_item_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_item_hover_color',array(
            'label' => 'Menu item hover', 'settings' => 'bonfire_morph_main_menu_item_hover_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu item description color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_item_description_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_item_description_color',array(
            'label' => 'Menu item description', 'settings' => 'bonfire_morph_main_menu_item_description_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* sub-menu item color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_subitem_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_subitem_color',array(
            'label' => 'Sub-menu item', 'settings' => 'bonfire_morph_main_menu_subitem_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* sub-menu item hover color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_subitem_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_subitem_hover_color',array(
            'label' => 'Sub-menu item hover', 'settings' => 'bonfire_morph_main_menu_subitem_hover_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* sub-menu arrow color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_arrow_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_arrow_color',array(
            'label' => 'Sub-menu arrow', 'settings' => 'bonfire_morph_main_menu_arrow_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* sub-menu arrow hover color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_arrow_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_arrow_hover_color',array(
            'label' => 'Sub-menu arrow hover', 'settings' => 'bonfire_morph_main_menu_arrow_hover_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu icon color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_icon_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_icon_color',array(
            'label' => 'Icon', 'settings' => 'bonfire_morph_main_menu_icon_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu icon hover color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_icon_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_icon_hover_color',array(
            'label' => 'Icon hover', 'settings' => 'bonfire_morph_main_menu_icon_hover_color', 'section' => 'morph_main_menu_section' )
        ));
        
        /* menu border color */
        $wp_customize->add_setting( 'bonfire_morph_main_menu_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_main_menu_border_color',array(
            'label' => 'Border', 'settings' => 'bonfire_morph_main_menu_border_color', 'section' => 'morph_main_menu_section' )
        ));
        
        //
        // ADD "Secondary Menu" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_secondary_menu_section',array('title' => __( 'Secondary Menu', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* hide secondary menu */
        $wp_customize->add_setting('bonfire_morph_hide_secondary_menu',array('sanitize_callback' => 'sanitize_bonfire_morph_hide_secondary_menu',));
        function sanitize_bonfire_morph_hide_secondary_menu( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_hide_secondary_menu',array('type' => 'checkbox','label' => 'Hide secondary menu', 'section' => 'morph_secondary_menu_section',));
        
        /* secondary menu button color */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_button_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_button_color',array(
            'label' => 'Secondary menu button', 'settings' => 'bonfire_morph_secondary_menu_button_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu button hover color */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_button_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_button_hover_color',array(
            'label' => 'Secondary menu button hover', 'settings' => 'bonfire_morph_secondary_menu_button_hover_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu button active color */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_button_active_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_button_active_color',array(
            'label' => 'Secondary menu button active', 'settings' => 'bonfire_morph_secondary_menu_button_active_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu button active hover color */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_button_active_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_button_active_hover_color',array(
            'label' => 'Secondary menu button active hover', 'settings' => 'bonfire_morph_secondary_menu_button_active_hover_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu background */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_background_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_background_color',array(
            'label' => 'Secondary menu background', 'settings' => 'bonfire_morph_secondary_menu_background_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu border (top, left, right) */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_border_color',array(
            'label' => 'Secondary menu border (top, left, right)', 'settings' => 'bonfire_morph_secondary_menu_border_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu border (bottom) */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_bottom_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_bottom_border_color',array(
            'label' => 'Secondary menu border (bottom)', 'settings' => 'bonfire_morph_secondary_menu_bottom_border_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu item */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_item_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_item_color',array(
            'label' => 'Secondary menu item', 'settings' => 'bonfire_morph_secondary_menu_item_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        /* secondary menu item hover */
        $wp_customize->add_setting( 'bonfire_morph_secondary_menu_item_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_secondary_menu_item_hover_color',array(
            'label' => 'Secondary menu item hover', 'settings' => 'bonfire_morph_secondary_menu_item_hover_color', 'section' => 'morph_secondary_menu_section' )
        ));
        
        //
        // ADD "Search" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_search_section',array('title' => __( 'Search', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* hide search */
        $wp_customize->add_setting('bonfire_morph_hide_search',array('sanitize_callback' => 'sanitize_bonfire_morph_hide_search',));
        function sanitize_bonfire_morph_hide_search( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_hide_search',array('type' => 'checkbox','label' => 'Hide search', 'section' => 'morph_search_section',));
        
        /* search button color */
        $wp_customize->add_setting( 'bonfire_morph_search_button_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_button_color',array(
            'label' => 'Search button', 'settings' => 'bonfire_morph_search_button_color', 'section' => 'morph_search_section' )
        ));
        
        /* search button hover color */
        $wp_customize->add_setting( 'bonfire_morph_search_button_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_button_hover_color',array(
            'label' => 'Search button hover', 'settings' => 'bonfire_morph_search_button_hover_color', 'section' => 'morph_search_section' )
        ));
        
        /* search close button color */
        $wp_customize->add_setting( 'bonfire_morph_search_close_button_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_close_button_color',array(
            'label' => 'Close search button', 'settings' => 'bonfire_morph_search_close_button_color', 'section' => 'morph_search_section' )
        ));
        
        /* search close button hover color */
        $wp_customize->add_setting( 'bonfire_morph_search_close_button_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_close_button_hover_color',array(
            'label' => 'Close search hover', 'settings' => 'bonfire_morph_search_close_button_hover_color', 'section' => 'morph_search_section' )
        ));
        
        /* search field border color */
        $wp_customize->add_setting( 'bonfire_morph_search_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_border_color',array(
            'label' => 'Search field border', 'settings' => 'bonfire_morph_search_border_color', 'section' => 'morph_search_section' )
        ));
        
        /* search field text color */
        $wp_customize->add_setting( 'bonfire_morph_search_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_search_text_color',array(
            'label' => 'Search field text', 'settings' => 'bonfire_morph_search_text_color', 'section' => 'morph_search_section' )
        ));
        
        //
        // ADD "Widgets" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_widgets_section',array('title' => __( 'Widgets', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* widget title color */
        $wp_customize->add_setting( 'bonfire_morph_widget_title_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_title_color',array(
            'label' => 'Widget title', 'settings' => 'bonfire_morph_widget_title_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget text color */
        $wp_customize->add_setting( 'bonfire_morph_widget_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_text_color',array(
            'label' => 'Widget text', 'settings' => 'bonfire_morph_widget_text_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget text secondary (dates, captions) */
        $wp_customize->add_setting( 'bonfire_morph_widget_secondary_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_secondary_text_color',array(
            'label' => 'Widget text secondary (dates, captions)', 'settings' => 'bonfire_morph_widget_secondary_text_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget link color */
        $wp_customize->add_setting( 'bonfire_morph_widget_link_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_link_color',array(
            'label' => 'Widget links', 'settings' => 'bonfire_morph_widget_link_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget search border color*/
        $wp_customize->add_setting( 'bonfire_morph_widget_search_border_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_search_border_color',array(
            'label' => 'Widget search border', 'settings' => 'bonfire_morph_widget_search_border_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget search field color */
        $wp_customize->add_setting( 'bonfire_morph_widget_search_field_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_search_field_color',array(
            'label' => 'Widget search field', 'settings' => 'bonfire_morph_widget_search_field_color', 'section' => 'morph_widgets_section' )
        ));
        
        /* widget search field placeholder color */
        $wp_customize->add_setting( 'bonfire_morph_widget_search_field_placeholder_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_widget_search_field_placeholder_color',array(
            'label' => 'Widget search field placeholder', 'settings' => 'bonfire_morph_widget_search_field_placeholder_color', 'section' => 'morph_widgets_section' )
        ));
        
        //
        // ADD "Misc" SECTION TO "Morph Plugin" PANEL 
        //
        $wp_customize->add_section('morph_misc_section',array('title' => __( 'Misc', 'morph' ),'panel'  => 'morph_panel','priority' => 1));
        
        /* thin button variations */
        $wp_customize->add_setting('bonfire_morph_thin_buttons',array('sanitize_callback' => 'sanitize_bonfire_morph_thin_buttons',));
        function sanitize_bonfire_morph_thin_buttons( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_thin_buttons',array('type' => 'checkbox','label' => 'Show thin button variations','description' => 'Applies to menu and secondary menu buttons, search and search close buttons, submenu arrows.', 'section' => 'morph_misc_section',));
        
        /* right side positioning */
        $wp_customize->add_setting('bonfire_morph_position_right',array('sanitize_callback' => 'sanitize_bonfire_morph_position_right',));
        function sanitize_bonfire_morph_position_right( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_position_right',array('type' => 'checkbox','label' => 'Right side position','description' => 'Places menu on right side of screen. If unticked, menu will be placed on left side of screen.', 'section' => 'morph_misc_section',));
        
        /* absolute positioning */
        $wp_customize->add_setting('bonfire_morph_absolute_position',array('sanitize_callback' => 'sanitize_bonfire_morph_absolute_position',));
        function sanitize_bonfire_morph_absolute_position( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_absolute_position',array('type' => 'checkbox','label' => 'Absolute positioning','description' => 'Main menu button, header bar and logo will leave the screen when scrolled.', 'section' => 'morph_misc_section',));
        
        /* flyout animation speed */
        $wp_customize->add_setting('bonfire_morph_menu_speed',array('sanitize_callback' => 'sanitize_bonfire_morph_menu_speed',));
        function sanitize_bonfire_morph_menu_speed($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_menu_speed',array(
            'type' => 'text',
            'label' => 'Custom fly-out speed (in seconds)',
            'description' => 'Example: 0.75. If empty, defaults to 0.5',
            'section' => 'morph_misc_section',
        ));
        
        /* menu flyout width */
        $wp_customize->add_setting('bonfire_morph_flyout_width',array('sanitize_callback' => 'sanitize_bonfire_morph_flyout_width',));
        function sanitize_bonfire_morph_flyout_width($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_flyout_width',array(
            'type' => 'text',
            'label' => 'Menu fly-out width (in pixels)',
            'description' => 'Set custom width for main menu flyout. If empty, defaults to 270.',
            'section' => 'morph_misc_section',
        ));
        
        /* background overlay opacity */
        $wp_customize->add_setting('bonfire_morph_background_overlay_opacity',array('sanitize_callback' => 'sanitize_bonfire_morph_background_overlay_opacity',));
        function sanitize_bonfire_morph_background_overlay_opacity($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_background_overlay_opacity',array(
            'type' => 'text',
            'label' => 'Background overlay opacity',
            'description' => 'From 0-1 (example: 0.5) If empty, defaults to 0.3',
            'section' => 'morph_misc_section',
        ));
        
        /* backgrond overlay color */
        $wp_customize->add_setting( 'bonfire_morph_background_overlay_color', array( 'sanitize_callback' => 'sanitize_hex_color' ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bonfire_morph_background_overlay_color',array(
            'label' => 'Background overlay', 'settings' => 'bonfire_morph_background_overlay_color', 'section' => 'morph_misc_section' )
        ));
        
        /* don't load FontAwesome */
        $wp_customize->add_setting('bonfire_morph_disable_fontawesome',array('sanitize_callback' => 'sanitize_bonfire_morph_disable_fontawesome',));
        function sanitize_bonfire_morph_disable_fontawesome( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_disable_fontawesome',array('type' => 'checkbox','label' => 'Do not load FontAwesome icon set','description' => 'Useful if you only use the default menubar icons or if your theme or another plugin already loads it, to prevent it from being loaded twice unnecessarily.', 'section' => 'morph_misc_section',));
        
        /* don't load retina.js */
        $wp_customize->add_setting('morph_no_retina',array('sanitize_callback' => 'sanitize_morph_no_retina',));
        function sanitize_morph_no_retina( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('morph_no_retina',array('type' => 'checkbox','label' => 'Disable retina image support','description' => 'If you are not using the logo image location or just do not want retina image support, enable this option to prevent the retina.js file from being loaded unnecessarily.','section' => 'morph_misc_section',));
        
        /* don't load fancy scrollbar */
        $wp_customize->add_setting('morph_no_scrollbar',array('sanitize_callback' => 'sanitize_morph_no_scrollbar',));
        function sanitize_morph_no_scrollbar( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('morph_no_scrollbar',array('type' => 'checkbox','label' => 'Disable fancy scrollbar on desktop','description' => 'Whether this option is enabled or not, touch devices use their native scroll behavior regardless.','section' => 'morph_misc_section',));
        
        /* visible on load */
        $wp_customize->add_setting('bonfire_morph_visible_on_load',array('sanitize_callback' => 'sanitize_bonfire_morph_visible_on_load',));
        function sanitize_bonfire_morph_visible_on_load( $input ) { if ( $input == 1 ) { return 1; } else { return ''; } }
        $wp_customize->add_control('bonfire_morph_visible_on_load',array('type' => 'checkbox','label' => 'Always visible','description' => 'Does not affect the overlay.', 'section' => 'morph_misc_section',));
        
        /* smaller than */
        $wp_customize->add_setting('bonfire_morph_smaller_than',array('sanitize_callback' => 'sanitize_bonfire_morph_smaller_than',));
        function sanitize_bonfire_morph_smaller_than($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_smaller_than',array(
            'type' => 'text',
            'label' => 'Hide at certain width/resolution',
            'description' => '<strong>Example #1:</strong> If you want to show Morph on desktop only, enter the values as 0 and 500. <br><br> <strong>Example #2:</strong> If you want to show Morph on mobile only, enter the values as 500 and 5000. <br><br> Feel free to experiment with your own values to get the result that is right for you. If fields are empty, Morph will be visible at all browser widths and resolutions. <br><br> Hide Morph menu if browser width or screen resolution (in pixels) is between...',
            'section' => 'morph_misc_section',
        ));
        
        /* larger than */
        $wp_customize->add_setting('bonfire_morph_larger_than',array('sanitize_callback' => 'sanitize_bonfire_morph_larger_than',));
        function sanitize_bonfire_morph_larger_than($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_larger_than',array(
            'type' => 'text',
            'description' => '..and:',
            'section' => 'morph_misc_section',
        ));
        
        /* hide theme elemets */
        $wp_customize->add_setting('bonfire_morph_hide_theme_elements',array('sanitize_callback' => 'sanitize_bonfire_morph_hide_theme_elements',));
        function sanitize_bonfire_morph_hide_theme_elements($input) {return wp_kses_post(force_balance_tags($input));}
        $wp_customize->add_control('bonfire_morph_hide_theme_elements',array(
            'type' => 'text',
            'label' => 'Advanced: Hide theme menu, logo, search etc.',
            'description' => 'If you have set Morph to show only at a certain resolution, know the classes/IDs of your theme menu, logo and search, and would like to hide them when Morph is visible, enter the classes/IDs into this field (example: "#my-theme-menu"). Multiple classes/IDs can be entered (separate with comma as you would in a stylesheet.',
            'section' => 'morph_misc_section',
        ));
        
    }
	add_action('customize_register', 'morph_theme_customizer');

	?>
<?php

	//
	// Add meta tag to theme header (no tap highlighting on Windows devices)
	//
	
	function bonfire_morph_meta() {
	?>
		<meta name="msapplication-tap-highlight" content="no" /> 
	<?php
	}
	add_action('wp_head','bonfire_morph_meta');

	//
	// Add menu to theme
	//
	
	function bonfire_morph_footer() {
	?>

		<!-- BEGIN MAIN MENU BUTTON -->
        <?php if( get_theme_mod('bonfire_morph_hide_menu_button') == '') { ?>
		<div class="morph-main-menu-button-wrapper<?php if ( is_admin_bar_showing() ) { ?> wp-toolbar-active<?php } ?><?php if( get_theme_mod('bonfire_morph_absolute_position') != '') { ?> morph-absolute<?php } ?><?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-menu-button-right<?php } ?>">
        
        <!-- BEGIN MENU BUTTON LABEL (if one is entered) -->
        <?php if(get_theme_mod('morph_menu_button_label') != '') { ?>
        <div class="morph-menu-button-label<?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-menu-button-label-right<?php } ?>">
            <?php echo get_theme_mod('morph_menu_button_label'); ?>
        </div>
        <?php } ?>
        <!-- END MENU BUTTON LABEL (if one is entered) -->
        
        <?php $morph_menu_button_style = get_theme_mod( 'bonfire_morph_menu_button_style' ); if( $morph_menu_button_style == '' ) { 
            echo '
            <div class="morph-main-menu-button">
                <div class="morph-main-menu-button-middle"></div>
            </div>
            ';
        }
        else if( $morph_menu_button_style != '' ) { switch ( $morph_menu_button_style ) {
        case 'style1':
        echo '
            <div class="morph-main-menu-button">
                <div class="morph-main-menu-button-middle"></div>
            </div>
        ';
        break; case 'style2':
        echo '
            <div class="morph-menu-button-three-alt">
                <div class="morph-menu-button-three-alt-middle"></div>
            </div>
        ';
        break; case 'style3':
        echo '
            <div class="morph-menu-button-four">
                <div class="morph-menu-button-four-middle"></div>
            </div>
        ';
        break; }} ?>

		</div>
		<?php } ?>
		<!-- END MAIN MENU BUTTON -->
        
        <!-- BEGIN LOGO -->
        <?php if( get_theme_mod('bonfire_morph_hide_header_logo') == '') { ?>
        <div class="morph-logo-wrapper<?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-logo-wrapper-right<?php } ?><?php if( get_theme_mod('bonfire_morph_absolute_position') != '') { ?> morph-absolute<?php } ?><?php if ( is_admin_bar_showing() ) { ?> wp-toolbar-active<?php } ?>">
            <?php if ( get_theme_mod( 'bonfire_morph_logo_image' ) ) : ?>
                <!-- BEGIN LOGO IMAGE -->
                <div class="morph-logo-image">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo get_theme_mod( 'bonfire_morph_logo_image' ); ?>" data-rjs="3" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a>
                </div>
                <!-- END LOGO IMAGE -->
            <?php else : ?>
                <!-- BEGIN LOGO -->
                <div class="morph-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                </div>
                <!-- END LOGO -->
            <?php endif; ?>
        </div>
        <?php } ?>
        <!-- END LOGO -->
        
        <!-- BEGIN HEADER BAR -->
        <?php if( get_theme_mod('bonfire_morph_hide_header_logo') == '') { ?>
        <div class="morph-header-bar<?php if( get_theme_mod('bonfire_morph_absolute_position') != '') { ?> morph-absolute<?php } ?><?php if ( is_admin_bar_showing() ) { ?> wp-toolbar-active<?php } ?>"></div>
        <?php } ?>
        <!-- END HEADER BAR -->

		<!-- BEGIN MAIN WRAPPER -->
		<div class="morph-main-wrapper<?php if ( is_admin_bar_showing() ) { ?> wp-toolbar-active<?php } ?><?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-main-wrapper-right<?php } ?>">
			<div class="morph-main-wrapper-inner">
				<div class="morph-main">
	
					<!-- BEGIN HEADING CONTENT -->
					<div class="morph-heading-wrapper">
						<div class="morph-heading-inner">
							<!-- BEGIN HEADING TEXT -->
							<div class="morph-heading-text">
                                <?php echo get_theme_mod('bonfire_morph_heading_text'); ?>
							</div>
							<!-- END HEADING TEXT -->
							<!-- BEGIN SUBHEADING TEXT -->
							<div class="morph-subheading-text">
                                <?php echo get_theme_mod('bonfire_morph_subheading_text'); ?>
							</div>
							<!-- END SUBHEADING TEXT -->
							
							<!-- BEGIN SEARCH BUTTON -->
							<?php if( get_theme_mod('bonfire_morph_hide_search') == '') { ?>
							<div class="morph-search-button<?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-search-button-right<?php } ?>">
								<div class="taptap-search-button"></div>
							</div>
							<?php } ?>
							<!-- END SEARCH BUTTON -->
					
							<!-- BEGIN SEARCH FORM CLOSE ICON -->
							<div class="morph-search-close-wrapper<?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-search-close-wrapper-right<?php } ?>">
									<div class="morph-search-close-button">
									</div>
							</div>
							<!-- END SEARCH FORM CLOSE ICON -->

							<!-- BEGIN SEARCH FORM -->
							<div class="morph-search-wrapper<?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-search-wrapper-right<?php } ?>">
								<form method="get" id="searchform" action="<?php echo esc_url( home_url('') ); ?>/">
									<input type="text" name="s" id="s">
								</form>
							</div>
							<!-- END SEARCH FORM -->
							
							<!-- BEGIN SECONDARY MENU BUTTON -->
							<?php if( get_theme_mod('bonfire_morph_hide_secondary_menu') == '') { ?>
							<div class="morph-secondary-menu-button">
								<!-- BEGIN SECONDARY MENU -->
								<div class="morph-secondary-menu-wrapper">
									<?php wp_nav_menu( array( 'container_class' => 'morph-by-bonfire-secondary', 'theme_location' => 'morph-by-bonfire-secondary', 'fallback_cb' => '' ) ); ?>
								</div>
								<!-- END SECONDARY MENU -->
								
								<div class="morph-secondary-button">
                                    <div class="morph-secondary-button-inner"></div>
                                </div>
							</div>
							<?php } ?>
							<!-- END SECONDARY MENU BUTTON -->
						</div>
					</div>
					<!-- END HEADING CONTENT -->
					
					<!-- BEGIN HEADING BACKGROUND OVERLAY -->
					<div class="morph-heading-overlay"></div>
					<!-- END HEADING BACKGROUND OVERLAY -->
					
					<!-- BEGIN HEADING IMAGE -->
					<div class="morph-heading-image"></div>
					<!-- END HEADING IMAGE -->
	
					<!-- BEGIN MAIN MENU + WIDGETS -->
					<div class="morph-menu-wrapper">
						<!-- BEGIN MAIN MENU -->
                        <?php $walker = new Morph_Menu_Description; ?>
                        <?php wp_nav_menu( array( 'container_class' => 'morph-by-bonfire', 'theme_location' => 'morph-by-bonfire', 'walker' => $walker, 'fallback_cb' => '' ) ); ?>
						<!-- END MAIN MENU -->
						
						<!-- BEGIN WIDGETS -->
						<div class="morph-widgets-wrapper">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Morph Widgets') ) : ?><?php endif; ?>
						</div>
						<!-- END WIDGETS -->
					</div>
					<!-- END MAIN MENU + WIDGETS -->
	
				</div>
			</div>
		</div>
		<!-- END MAIN WRAPPER -->
		
		<!-- BEGIN MAIN BACKGROUND -->
		<div class="morph-main-background<?php if ( is_admin_bar_showing() ) { ?> wp-toolbar-active<?php } ?><?php if( get_theme_mod('bonfire_morph_position_right') != '') { ?> morph-main-background-right<?php } ?>">
		</div>
		<!-- END MAIN BACKGROUND -->
		
		<!-- BEGIN BACKGROUND OVERLAY (when menu open) -->
		<div class="morph-background-overlay"></div>
		<!-- END BACKGROUND OVERLAY (when menu open) -->

	<?php
	}
	add_action('wp_footer','bonfire_morph_footer');

	//
	// ENQUEUE morph.css
	//
	function bonfire_morph_css() {
		wp_register_style( 'bonfire-morph-css', plugins_url( '/morph.css', __FILE__ ) . '', array(), '1', 'all' );
		wp_enqueue_style( 'bonfire-morph-css' );
	}
	add_action( 'wp_enqueue_scripts', 'bonfire_morph_css' );

	//
	// ENQUEUE morph-accordion.js
	//
	function bonfire_morph_accordion() {
        if( get_theme_mod('bonfire_morph_sub_menu_activation') != '') {
			wp_register_script( 'bonfire-morph-accordion-full-link', plugins_url( '/js/morph-accordion-full-link.js', __FILE__ ) . '', array( 'jquery' ), '1' );  
			wp_enqueue_script( 'bonfire-morph-accordion-full-link' );
		} else {
			wp_register_script( 'bonfire-morph-accordion', plugins_url( '/js/morph-accordion.js', __FILE__ ) . '', array( 'jquery' ), '1' );  
		wp_enqueue_script( 'bonfire-morph-accordion' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'bonfire_morph_accordion' );    

	//
	// ENQUEUE morph.js
	//
	function bonfire_morph_js() {
		wp_register_script( 'bonfire-morph-js', plugins_url( '/js/morph.js', __FILE__ ) . '', array( 'jquery' ), '1', true );  
		wp_enqueue_script( 'bonfire-morph-js' );
	}
	add_action( 'wp_enqueue_scripts', 'bonfire_morph_js' );
    
    //
	// ENQUEUE morph-visible-on-load.js
	//
	function bonfire_morph_visible_on_load_js() {
        if( get_theme_mod('bonfire_morph_visible_on_load') != '') {
            wp_register_script( 'morph-visible-on-load', plugins_url( '/js/morph-visible-on-load.js', __FILE__ ) . '', array( 'jquery' ), '1', true );  
            wp_enqueue_script( 'morph-visible-on-load' );
        }
	}
	add_action( 'wp_enqueue_scripts', 'bonfire_morph_visible_on_load_js' );
    
	//
	// ENQUEUE search.js
	//
	function bonfire_morph_search_js() {
		wp_register_script( 'bonfire-morph-search-js', plugins_url( '/js/search.js', __FILE__ ) . '', array( 'jquery' ), '1', true );
		wp_enqueue_script( 'bonfire-morph-search-js' );
	}
	add_action( 'wp_enqueue_scripts', 'bonfire_morph_search_js' );
    
    //
	// ENQUEUE retina.min.js
	//
    if(get_theme_mod('morph_no_retina') == '') {
        function bonfire_morph_retina_js() {
            wp_register_script( 'bonfire-morph-retina-js', plugins_url( '/js/retina.min.js', __FILE__ ) . '', array( 'jquery' ), '1', true );  
            wp_enqueue_script( 'bonfire-morph-retina-js' );
        }
        add_action( 'wp_enqueue_scripts', 'bonfire_morph_retina_js' );
    }
    
    //
	// ENQUEUE jquery.scrollbar.min.js (except on touch devices)
	//
    if(get_theme_mod('morph_no_scrollbar') == '') {
        function bonfire_morph_scrollbar_js() {
            if ( wp_is_mobile() ) { } else {
                wp_register_script( 'bonfire-morph-scrollbar-js', plugins_url( '/js/jquery.scrollbar.min.js', __FILE__ ) . '', array( 'jquery' ), '1', true );  
                wp_enqueue_script( 'bonfire-morph-scrollbar-js' );
            }
        }
        add_action( 'wp_enqueue_scripts', 'bonfire_morph_scrollbar_js' );
    }
    
    //
	// ENQUEUE Google WebFonts
	//
    function morph_fonts_url() {
		$font_url = '';

		if ( 'off' !== _x( 'on', 'Google font: on or off', 'morph' ) ) {
			$font_url = add_query_arg( 'family', urlencode( 'Roboto:400,500' ), "//fonts.googleapis.com/css" );
		}
		return $font_url;
	}
	function morph_scripts() {
		wp_enqueue_style( 'morph-fonts', morph_fonts_url(), array(), '1.0.0' );
	}
	add_action( 'wp_enqueue_scripts', 'morph_scripts' );
	
	//
	// Enqueue font-awesome.min.css (icons for menu, if option to hide not enabled)
	//
	if( get_theme_mod('bonfire_morph_disable_fontawesome') == '') {
		function bonfire_morph_fontawesome() {  
			wp_register_style( 'morph-fontawesome', plugins_url( '/fonts/font-awesome/css/font-awesome.min.css', __FILE__ ) . '', array(), '1', 'all' );  
			wp_enqueue_style( 'morph-fontawesome' );
		}
		add_action( 'wp_enqueue_scripts', 'bonfire_morph_fontawesome' );
	}

	//
	// Register Custom Menu Function
	//
	if (function_exists('register_nav_menus')) {
		register_nav_menus( array(
			'morph-by-bonfire' => ( 'Morph plugin (primary)' ),
			'morph-by-bonfire-secondary' => ( 'Morph plugin (secondary)' )
		) );
	}
    
    //
	// Add the walker class (for menu descriptions)
	//
	
	class Morph_Menu_Description extends Walker_Nav_Menu {
		function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			
			$class_names = $value = '';
	
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';
	
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
	
			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';
	
			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= '<div class="morph-menu-item-description">' . $item->description . '</div>';
			$item_output .= '</a>';
			$item_output .= $args->after;
	
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id );
		}
	}

	///////////////////////////////////////
	// Register Widgets
	///////////////////////////////////////
	function bonfire_morph_widgets_init() {
	
		register_sidebar( array(
		'name' => __( 'Morph Widgets', 'bonfire' ),
		'id' => 'morph-main-widgets',
		'description' => __( 'Drag widgets here', 'bonfire' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
		));

	}
	add_action( 'widgets_init', 'bonfire_morph_widgets_init' );
    
    //
	// Allow Shortcodes in Text Widget
	//
	add_filter('widget_text', 'do_shortcode');

	//
	// Insert theme customizer options into the footer
	//
	function bonfire_morph_header_customize() {
	?>

		<style>
		/**************************************************************
		*** CUSTOM COLORS
		**************************************************************/
        /* header */
        .morph-header-bar {
            background-color:<?php echo get_theme_mod('bonfire_morph_header_color'); ?>;
            opacity:<?php echo get_theme_mod('bonfire_morph_header_overlay'); ?>;
            <?php if( get_theme_mod('bonfire_morph_show_header_shadow') != '') { ?>
            -webkit-box-shadow:0px 0px 2px 0px rgba(0,0,0,0.5);
            -moz-box-shadow:0px 0px 2px 0px rgba(0,0,0,0.5);
            box-shadow:0px 0px 2px 0px rgba(0,0,0,0.5);
            <?php } ?>
        }
        /* logo colors */
        .morph-logo a { color:<?php echo get_theme_mod('bonfire_morph_logo_color'); ?>; }
        .morph-logo a:hover { color:<?php echo get_theme_mod('bonfire_morph_logo_hover_color'); ?>; }
		/* widgets */
		.morph-widgets-wrapper .widgettitle { color:<?php echo get_theme_mod('bonfire_morph_widget_title_color'); ?>; }
		.morph-widgets-wrapper .widget { color:<?php echo get_theme_mod('bonfire_morph_widget_text_color'); ?>; }
		.morph-widgets-wrapper .post-date,
		.morph-widgets-wrapper .rss-date,
		.morph-widgets-wrapper .wp-caption,
		.morph-widgets-wrapper .wp-caption-text { color:<?php echo get_theme_mod('bonfire_morph_widget_secondary_text_color'); ?>; }
		.morph-widgets-wrapper .widget a { color:<?php echo get_theme_mod('bonfire_morph_widget_link_color'); ?>; }
		.morph-widgets-wrapper .widget_search input { color:<?php echo get_theme_mod('bonfire_morph_widget_search_field_color'); ?> !important; border-color:<?php echo get_theme_mod('bonfire_morph_widget_search_border_color'); ?> !important; }
        /* searchform placeholder */
        .morph-widgets-wrapper .widget_search input#s::-webkit-input-placeholder { color:<?php echo get_theme_mod('bonfire_morph_widget_search_field_placeholder_color'); ?> !important; }
        .morph-widgets-wrapper .widget_search input#s:-moz-placeholder { color:<?php echo get_theme_mod('bonfire_morph_widget_search_field_placeholder_color'); ?> !important; }
        .morph-widgets-wrapper .widget_search input#s::-moz-placeholder { color:<?php echo get_theme_mod('bonfire_morph_widget_search_field_placeholder_color'); ?> !important; }
        
		/* menu button */
		.morph-main-menu-button:after,
		.morph-main-menu-button:before,
		.morph-main-menu-button div.morph-main-menu-button-middle:before,
		.morph-menu-button-three-alt:after,
		.morph-menu-button-three-alt:before,
		.morph-menu-button-three-alt div.morph-menu-button-three-alt-middle:before,
		.morph-menu-button-four:after,
		.morph-menu-button-four:before,
		.morph-menu-button-four div.morph-menu-button-four-middle:before,
		.morph-menu-button-four div.morph-menu-button-four-middle:after { background-color:<?php echo get_theme_mod('bonfire_morph_menu_button_color'); ?>; }
        /* menu button label */
        .morph-menu-button-label { color:<?php echo get_theme_mod('bonfire_morph_menu_button_label_color'); ?>; }
		/* main menu button hover */
		<?php if ( wp_is_mobile() ) { ?>
		<?php } else { ?>
		.morph-main-menu-button:hover:after,
		.morph-main-menu-button:hover:before,
		.morph-main-menu-button:hover div.morph-main-menu-button-middle:before,
		.morph-menu-button-three-alt:hover:after,
		.morph-menu-button-three-alt:hover:before,
		.morph-menu-button-three-alt:hover div.morph-menu-button-three-alt-middle:before,
		.morph-menu-button-four:hover:after,
		.morph-menu-button-four:hover:before,
		.morph-menu-button-four:hover div.morph-menu-button-four-middle:before,
		.morph-menu-button-four:hover div.morph-menu-button-four-middle:after { background-color:<?php echo get_theme_mod('bonfire_morph_menu_button_hover_color'); ?>; }
        /* menu button label */
        .morph-main-menu-button-wrapper:hover .morph-menu-button-label { color:<?php echo get_theme_mod('bonfire_morph_menu_button_label_hover_color'); ?>; }
		<?php } ?>
		/* menu button active */
		.morph-menu-active .morph-main-menu-button:after,
		.morph-menu-active .morph-main-menu-button:before,
		.morph-menu-active .morph-main-menu-button div.morph-main-menu-button-middle:before,
		.morph-menu-active .morph-menu-button-three-alt:after,
		.morph-menu-active .morph-menu-button-three-alt:before,
		.morph-menu-active .morph-menu-button-three-alt div.morph-menu-button-three-alt-middle:before,
		.morph-menu-active .morph-menu-button-four:after,
		.morph-menu-active .morph-menu-button-four:before,
		.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:before,
		.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:after { background-color:<?php echo get_theme_mod('bonfire_morph_menu_button_active_color'); ?>; }
		/* menu button active hover */
		<?php if ( wp_is_mobile() ) { ?>
		<?php } else { ?>
		.morph-menu-active .morph-main-menu-button:hover:after,
		.morph-menu-active .morph-main-menu-button:hover:before,
		.morph-menu-active .morph-main-menu-button:hover div.morph-main-menu-button-middle:before,
		.morph-menu-active .morph-menu-button-three-alt:hover:after,
		.morph-menu-active .morph-menu-button-three-alt:hover:before,
		.morph-menu-active .morph-menu-button-three-alt:hover div.morph-menu-button-three-alt-middle:before,
		.morph-menu-active .morph-menu-button-four:hover:after,
		.morph-menu-active .morph-menu-button-four:hover:before,
		.morph-menu-active .morph-menu-button-four:hover div.morph-menu-button-four-middle:before,
		.morph-menu-active .morph-menu-button-four:hover div.morph-menu-button-four-middle:after { background-color:<?php echo get_theme_mod('bonfire_morph_menu_button_active_hover_color'); ?>; }
		<?php } ?>
		/* custom menu button speed */
		.morph-main-menu-button:after,
		.morph-main-menu-button:before,
		.morph-main-menu-button div.morph-main-menu-button-middle:before,
		.morph-menu-button-three-alt:after,
		.morph-menu-button-three-alt:before,
		.morph-menu-button-three-alt div.morph-menu-button-three-alt-middle:before,
		.morph-menu-button-four:after,
		.morph-menu-button-four:before,
		.morph-menu-button-four div.morph-menu-button-four-middle:before,
		.morph-menu-button-four div.morph-menu-button-four-middle:after {
			-webkit-transition:all <?php echo get_theme_mod('bonfire_morph_menu_button_speed'); ?>s ease !important;
			-moz-transition:all <?php echo get_theme_mod('bonfire_morph_menu_button_speed'); ?>s ease !important;
			transition:all <?php echo get_theme_mod('bonfire_morph_menu_button_speed'); ?>s ease !important;
		}

		/* secondary menu button */
		.morph-secondary-button-inner,
        .morph-secondary-button-inner::before,
        .morph-secondary-button-inner::after { background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_button_color'); ?>;}
		/* secondary menu button hover */
		<?php if ( wp_is_mobile() ) { ?>
		<?php } else { ?>
		.morph-secondary-button:hover .morph-secondary-button-inner,
        .morph-secondary-button:hover .morph-secondary-button-inner::before,
        .morph-secondary-button:hover .morph-secondary-button-inner::after { background-color:#A0A0A0; background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_button_hover_color'); ?>;}
		<?php } ?>
		/* secondary menu button active */
		.morph-secondary-menu-button-active .morph-secondary-button-inner,
        .morph-secondary-menu-button-active .morph-secondary-button-inner::before,
        .morph-secondary-menu-button-active .morph-secondary-button-inner::after { background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_button_active_color'); ?>; }
		/* secondary menu button active hover */
		<?php if ( wp_is_mobile() ) { ?>
		<?php } else { ?>
		.morph-secondary-menu-button-active .morph-secondary-button:hover .morph-secondary-button-inner,
        .morph-secondary-menu-button-active .morph-secondary-button:hover .morph-secondary-button-inner::before,
        .morph-secondary-menu-button-active .morph-secondary-button:hover .morph-secondary-button-inner::after { background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_button_active_hover_color'); ?>; }
		<?php } ?>
		
		/* search button */
		.taptap-search-button { border-color:<?php echo get_theme_mod('bonfire_morph_search_button_color'); ?>; }
        .taptap-search-button::after { background-color:<?php echo get_theme_mod('bonfire_morph_search_button_color'); ?>; }
		.morph-search-button:hover .taptap-search-button { border-color:<?php echo get_theme_mod('bonfire_morph_search_button_hover_color'); ?>; }
        .morph-search-button:hover .taptap-search-button::after { background-color:<?php echo get_theme_mod('bonfire_morph_search_button_hover_color'); ?>; }
		/* search close button */
		.morph-search-close-button:before,
		.morph-search-close-button:after { background-color:<?php echo get_theme_mod('bonfire_morph_search_close_button_color'); ?>; }
		.morph-search-close-wrapper:hover .morph-search-close-button:before,
		.morph-search-close-wrapper:hover .morph-search-close-button:after { background-color:<?php echo get_theme_mod('bonfire_morph_search_close_button_hover_color'); ?>; }
		/* search field border + text */
		.morph-search-wrapper { border-color:<?php echo get_theme_mod('bonfire_morph_search_border_color'); ?>; }
		.morph-search-wrapper #searchform input { color:<?php echo get_theme_mod('bonfire_morph_search_text_color'); ?>; }
		
		/* heading + sub-heading text */
		.morph-heading-text { color:<?php echo get_theme_mod('bonfire_morph_heading_text_color'); ?>; }
		.morph-subheading-text { color:<?php echo get_theme_mod('bonfire_morph_subheading_text_color'); ?>; }
		/* heading overlay */
		.morph-heading-overlay { background-color:<?php echo get_theme_mod('bonfire_morph_heading_overlay_color'); ?>; }
        /* heading bottom border + border thickness */
		.morph-heading-wrapper { border-bottom-color:<?php echo get_theme_mod('bonfire_morph_heading_border_color'); ?>; border-bottom-width:<?php echo get_theme_mod('bonfire_morph_border_thickness'); ?>px; }

		/* main menu */
		.morph-main-background { background-color:<?php echo get_theme_mod('bonfire_morph_main_menu_background_color'); ?>; }
		.morph-by-bonfire ul li a { color:<?php echo get_theme_mod('bonfire_morph_main_menu_item_color'); ?>; }
        .morph-menu-item-description { color:<?php echo get_theme_mod('bonfire_morph_main_menu_item_description_color'); ?>; }
		.morph-by-bonfire ul.sub-menu li a { color:<?php echo get_theme_mod('bonfire_morph_main_menu_subitem_color'); ?>; }
        .morph-by-bonfire a i { color:<?php echo get_theme_mod('bonfire_morph_main_menu_icon_color'); ?>; }
		<?php if ( wp_is_mobile() ) { ?>
		<?php } else { ?>
        /* menu item hovers */
        .morph-by-bonfire ul li.full-item-arrow-hover > a,
		.morph-by-bonfire ul li a:hover { color:<?php if( get_theme_mod('bonfire_morph_main_menu_item_hover_color') != '') { ?><?php echo get_theme_mod('bonfire_morph_main_menu_item_hover_color'); ?><?php } else { ?>#55565C<?php } ?>; }
        .morph-by-bonfire ul li.full-item-arrow-hover > a i,
		.morph-by-bonfire a:hover i { color:<?php if( get_theme_mod('bonfire_morph_main_menu_icon_hover_color') != '') { ?><?php echo get_theme_mod('bonfire_morph_main_menu_icon_hover_color'); ?><?php } else { ?>#55565C<?php } ?>; }
        .morph-by-bonfire ul.sub-menu li.full-item-arrow-hover > a,
        .morph-by-bonfire ul.sub-menu li a:hover { color:<?php if( get_theme_mod('bonfire_morph_main_menu_subitem_hover_color') != '') { ?><?php echo get_theme_mod('bonfire_morph_main_menu_subitem_hover_color'); ?><?php } else { ?>#77787C<?php } ?>; }
        /* sub-menu arrow hover */
        .full-item-arrow-hover > .morph-sub-arrow .morph-sub-arrow-inner::before,
        .full-item-arrow-hover > .morph-sub-arrow .morph-sub-arrow-inner::after,
        .morph-sub-arrow:hover .morph-sub-arrow-inner::before,
        .morph-sub-arrow:hover .morph-sub-arrow-inner::after { background-color:<?php if( get_theme_mod('bonfire_morph_main_menu_arrow_hover_color') != '') { ?><?php echo get_theme_mod('bonfire_morph_main_menu_arrow_hover_color'); ?><?php } else { ?>#888<?php } ?>; }
        <?php } ?>
		.morph-by-bonfire ul li.border a { border-color:<?php echo get_theme_mod('bonfire_morph_main_menu_border_color'); ?>; }
        
        /* sub-menu arrow */
        <?php if( get_theme_mod('bonfire_morph_sub_menu_activation') != '') { ?>
        .morph-sub-arrow-inner { border:none; }
        <?php } ?>
        .morph-sub-arrow .morph-sub-arrow-inner::before,
        .morph-sub-arrow .morph-sub-arrow-inner::after { background-color:<?php echo get_theme_mod('bonfire_morph_main_menu_arrow_color'); ?>; }
		
		/* secondary menu */
		.morph-secondary-menu-wrapper { background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_background_color'); ?>; border-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_border_color'); ?>; }
		.morph-secondary-menu-wrapper:after { background-color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_bottom_border_color'); ?>; }
		.morph-secondary-menu-wrapper a { color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_item_color'); ?>; }
		.morph-secondary-menu-wrapper a:hover { color:<?php echo get_theme_mod('bonfire_morph_secondary_menu_item_hover_color'); ?>; }
		
		/* background overlay opacity */
		.morph-background-overlay-active { opacity:<?php echo get_theme_mod('bonfire_morph_background_overlay_opacity'); ?>; }
        /* background overlay color */
		.morph-background-overlay { background-color:<?php echo get_theme_mod('bonfire_morph_background_overlay_color'); ?>; }
        
        /* flyout speed */
        .morph-menu-wrapper,
        .morph-main-background,
        .morph-main-wrapper {
			-webkit-transition:all <?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s ease !important;
			-moz-transition:all <?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s ease !important;
			transition:all <?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s ease !important;
		}
        .morph-menu-wrapper-active .morph-by-bonfire ul li a {
            -webkit-transition-duration:<?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s !important;
            -moz-transition-duration:<?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s !important;
            transition-duration:<?php echo get_theme_mod('bonfire_morph_menu_speed'); ?>s !important;
        }
        
        /* custom width */
        .morph-menu-wrapper,
        .morph-main-background,
        .morph-main-wrapper {
            width:<?php echo get_theme_mod('bonfire_morph_flyout_width'); ?>px;
            max-width:100%;
        }
        .morph-main-wrapper,
        .morph-main-background {
            -webkit-transform:translateX(-<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
            -moz-transform:translateX(-<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
            transform:translateX(-<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
        }
        .morph-main-wrapper-right,
        .morph-main-background-right {
            -webkit-transform:translateX(<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
            -moz-transform:translateX(<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
            transform:translateX(<?php if( get_theme_mod('bonfire_morph_flyout_width') != '') { ?><?php echo get_theme_mod('bonfire_morph_flyout_width'); ?><?php } else {?>310<?php } ?>px);
        }

		/* menu button animations (-/X) */
        <?php $morph_button_animation= get_theme_mod( 'bonfire_morph_button_animation' ); if( $morph_button_animation != '' ) { switch ( $morph_button_animation) {
        case 'minussign': ?>
            /* middle bar #1 animation (3 lines) */
			.morph-menu-active .morph-main-menu-button div.morph-main-menu-button-middle:before {
				margin:0 0 -1px 0;
			}
			/* top bar fade out (3 lines) */
			.morph-menu-active .morph-main-menu-button:before {
				opacity:0;
				
				-webkit-transform:translateY(4px);
				-moz-transform:translateY(4px);
				-ms-transform:translateY(4px);
				transform:translateY(4px);
			}
			/* bottom bar fade out (3 lines) */
			.morph-menu-active .morph-main-menu-button:after {
				opacity:0;
				
				-webkit-transform:translateY(-3px);
				-moz-transform:translateY(-3px);
				-ms-transform:translateY(-3px);
				transform:translateY(-3px);
			}
			/* middle bar animation (alternate 3 lines) */
			.morph-menu-active .morph-menu-button-three-alt div.morph-menu-button-three-alt-middle:before {
				margin:0 0 -1px 0;
			}
			/* top bar fade out (alternate 3 lines) */
			.morph-menu-active .morph-menu-button-three-alt:before {
				width:0;
				
				-webkit-transition:all .25s ease;
				-moz-transition:all .25s ease;
				transition:all .25s ease;
			}
			/* bottom bar fade out (alternate 3 lines) */
			.morph-menu-active .morph-menu-button-three-alt:after {
				width:0;
				
				-webkit-transition:all .25s ease;
				-moz-transition:all .25s ease;
				transition:all .25s ease;
			}
			/* top bar animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four:before {
				-webkit-transform:translateY(9px);
				-moz-transform:translateY(9px);
				transform:translateY(9px);
			}
			/* middle bar #1 animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:before {
				-webkit-transform:translateY(3px);
				-moz-transform:translateY(3px);
				transform:translateY(3px);
			}
			/* middle bar #2 animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:after {
				-webkit-transform:translateY(-3px);
				-moz-transform:translateY(-3px);
				transform:translateY(-3px);
			}
			/* bottom bar animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four:after {
				-webkit-transform:translateY(-9px);
				-moz-transform:translateY(-9px);
				transform:translateY(-9px);
			}
        <?php break; case 'xsign': ?>
            /* top bar animation (3 lines) */
			.morph-menu-active .morph-main-menu-button:before {
				margin:2px 0 0 0;
				
				transform:translateY(4px) rotate(45deg);
				-moz-transform:translateY(4px) rotate(45deg);
				-ms-transform:translateY(4px) rotate(45deg);
				-webkit-transform:translateY(4px) rotate(45deg);
			}
			/* bottom bar animation (3 lines) */
			.morph-menu-active .morph-main-menu-button:after {
				transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-4<?php } else { ?>-5<?php } ?>px) rotate(-45deg);
				-moz-transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-4<?php } else { ?>-5<?php } ?>px) rotate(-45deg);
				-ms-transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-4<?php } else { ?>-5<?php } ?>px) rotate(-45deg);
				-webkit-transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-4<?php } else { ?>-5<?php } ?>px) rotate(-45deg);
			}
			/* middle bar fade out (3 lines) */
			.morph-menu-active div.morph-main-menu-button-middle:before {
				opacity:0;
				
				-webkit-transition:all .15s ease;
				-moz-transition:all .15s ease;
				-ms-transition:all .15s ease;
				transition:all .15s ease;
			}
			/* top bar animation (alternate 3 lines) */
			.morph-menu-active .morph-menu-button-three-alt:before {
				width:24px;
				margin-right:7px;
				
				transform:translateY(6px) rotate(45deg);
				-moz-transform:translateY(6px) rotate(45deg);
				-webkit-transform:translateY(6px) rotate(45deg);
			}
			/* bottom bar animation (alternate 3 lines) */
			.morph-menu-active .morph-menu-button-three-alt:after {
				width:24px;
				margin:8px 0 0 0px;
				
				transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-9<?php } else { ?>-10<?php } ?>px) rotate(-45deg);
				-moz-transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-9<?php } else { ?>-10<?php } ?>px) rotate(-45deg);
				-webkit-transform:translateY(<?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>-9<?php } else { ?>-10<?php } ?>px) rotate(-45deg);
			}
			/* middle bar fade out (alternate 3 lines) */
			.morph-menu-active div.morph-menu-button-three-alt-middle:before {
				opacity:0;
				
				-webkit-transition:all .15s ease;
				-moz-transition:all .15s ease;
				transition:all .15s ease;
			}
			/* top bar animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four:before {
				width:11px;
				
				transform:translateY(3px) translateX(-1px) rotate(45deg);
				-moz-transform:translateY(3px) translateX(-1px) rotate(45deg);
				-webkit-transform:translateY(3px) translateX(-1px) rotate(45deg);
			}
			/* middle bar #1 animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:before {
				width:11px;
				
				transform:translateY(-3px) translateX(11px) rotate(-45deg);
				-moz-transform:translateY(-3px) translateX(11px) rotate(-45deg);
				-webkit-transform:translateY(-3px) translateX(11px) rotate(-45deg);
			}
			/* middle bar #2 animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four div.morph-menu-button-four-middle:after {
				width:11px;
				
				transform:translateY(3px) translateX(-1px) rotate(-45deg);
				-moz-transform:translateY(3px) translateX(-1px) rotate(-45deg);
				-webkit-transform:translateY(3px) translateX(-1px) rotate(-45deg);
			}
			/* bottom bar animation (alternate 4 lines) */
			.morph-menu-active .morph-menu-button-four:after {
				width:11px;
				
				transform:translateY(-3px) translateX(11px) rotate(45deg);
				-moz-transform:translateY(-3px) translateX(11px) rotate(45deg);
				-webkit-transform:translateY(-3px) translateX(11px) rotate(45deg);
			}
        <?php break; }} ?>

		/* custom heading height */
		.morph-heading-wrapper,
		.morph-heading-overlay,
		.morph-heading-image { height:<?php echo get_theme_mod('bonfire_morph_heading_height'); ?>px; }
		.morph-menu-wrapper { top:<?php echo get_theme_mod('bonfire_morph_heading_height'); ?>px; }
		/* heading overlay opacity */
		.morph-heading-overlay { opacity:<?php echo get_theme_mod('bonfire_morph_heading_overlay'); ?>; }
		/* heading image */
		.morph-heading-image {
			background-image:url(<?php echo get_theme_mod('bonfire_morph_heading_image'); ?>);
		}
		/* heading image pattern */
        <?php if( get_theme_mod('bonfire_morph_heading_image_pattern') != '') { ?>
		.morph-heading-image { background-size:auto; }
		<?php } ?>
        
        /* thin button variations */
        <?php if(get_theme_mod('bonfire_morph_thin_buttons') != '') { ?>
        /* menu buttons */
        .morph-main-menu-button:after,
        .morph-main-menu-button:before,
        .morph-main-menu-button div.morph-main-menu-button-middle:before {
            height:1px;
            margin:0 0 6px 0;
        }
        .morph-menu-button-three-alt:after,
        .morph-menu-button-three-alt:before,
        .morph-menu-button-three-alt div.morph-menu-button-three-alt-middle:before {
            height:1px;
            margin-top:5px;
            margin-bottom:5px;
        }
        .morph-menu-button-four:after,
        .morph-menu-button-four:before,
        .morph-menu-button-four div.morph-menu-button-four-middle:before,
        .morph-menu-button-four div.morph-menu-button-four-middle:after {
            height:1px;
            margin-top:5px;
        }
        /* search button */
        .taptap-search-button {
            border-width:1px;
        }
        .taptap-search-button::after {
            top:10px;
            left:8px;
            height:1px;
        }
        /* search form close button */
        .morph-search-close-button:before,
        .morph-search-close-button:after {
            height:1px;
        }
        .morph-search-close-button:after {	
            -webkit-transform:translateY(5px) rotate(-45deg);
            -moz-transform:translateY(5px) rotate(-45deg);
            transform:translateY(5px) rotate(-45deg);
        }
        /* search form */
        .morph-search-wrapper {
            border-width:1px;
        }
        /* sub-menu arrow */
        .morph-sub-arrow-inner::before,
        .morph-sub-arrow-inner::after {
            height:1px;
        }
        .morph-sub-arrow-inner::after {
            left:17px;
        }
        /* secondary menu button */
        .morph-secondary-button-inner,
        .morph-secondary-button-inner::before,
        .morph-secondary-button-inner::after {
            width:3px;
            height:3px;
        }
        .morph-secondary-button-inner::after { top:3px; }
        <?php } ?>
        
		/* hide morph between resolutions */
		@media ( min-width:<?php echo get_theme_mod('bonfire_morph_smaller_than'); ?>px) and (max-width:<?php echo get_theme_mod('bonfire_morph_larger_than'); ?>px) {
			.morph-logo-wrapper,
            .morph-header-bar,
            .morph-main-menu-button-wrapper,
			.morph-main-wrapper,
			.morph-main-background,
			.morph-background-overlay { display:none; }
			body { margin-top:0; }
		}
        /* hide theme elements */
		<?php if( get_theme_mod('bonfire_morph_hide_theme_elements') != '') { ?>
		@media screen and (max-width:<?php echo get_theme_mod('bonfire_morph_smaller_than'); ?>px) {
			<?php echo get_theme_mod('bonfire_morph_hide_theme_elements'); ?> { display:none !important; }
		}
		@media screen and (min-width:<?php echo get_theme_mod('bonfire_morph_larger_than'); ?>px) {
			<?php echo get_theme_mod('bonfire_morph_hide_theme_elements'); ?> { display:none !important; }
		}
		<?php } ?>
		</style>
        
        <?php if(get_theme_mod('morph_no_retina') == '') { ?>
        <script>
        // BEGIN RETINA IMAGE SUPPORT
        jQuery(document).ready(function(){
        'use strict';
            retinajs( jQuery('.morph-logo-image img') );
        });
        // END RETINA IMAGE SUPPORT
        </script>
        <?php } ?>
		<!-- END CUSTOM COLORS (WP THEME CUSTOMIZER) -->
	
	<?php
	}
	add_action('wp_footer','bonfire_morph_header_customize');

?>