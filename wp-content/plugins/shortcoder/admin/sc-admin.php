<?php

class Shortcoder_Admin{
    
    private static $pagehook = 'settings_page_shortcoder';
    
    public static function init(){
        
        // Add menu
        add_action( 'admin_menu', array( __class__, 'add_menu' ) );
        
        // Enqueue the scripts and styles
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        
        // Register the action for admin ajax features
        add_action( 'wp_ajax_sc_admin_ajax', array( __CLASS__, 'admin_ajax' ) );
        
        // Register action links
        add_filter( 'plugin_action_links_' . SC_BASE_NAME, array( __CLASS__, 'action_links' ) );
        
        // Add Quick Tag button to the editor
        add_action( 'admin_footer', array( __class__, 'add_qt_button' ) );
        
        // Add TinyMCE button
        add_action( 'admin_init', array( __class__, 'register_mce' ) );
        
    }
    
    public static function add_menu(){
        
        add_options_page( 'Shortcoder', 'Shortcoder', 'manage_options', 'shortcoder', array( __class__, 'admin_page' ) );
        
    }
    
    public static function enqueue_scripts( $hook ){
        
        if( $hook == self::$pagehook ){
            
            wp_enqueue_style( 'sc-admin-css', SC_ADMIN_URL . '/css/style.css', array(), SC_VERSION );
            wp_enqueue_style( 'sc-selectize', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.min.css', array(), SC_VERSION );
            
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'sc-admin-js', SC_ADMIN_URL . '/js/script.js', array( 'jquery' ), SC_VERSION );
            wp_enqueue_script( 'sc-selectize', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js', array( 'jquery' ), SC_VERSION );
        
        } 
    }
    
    public static function admin_page(){
        
        echo '<div class="wrap">';
        echo '<div class="head_wrap">';
        echo '<h1 class="sc_title">Shortcoder <span class="title-count">' . SC_VERSION . '</span></h1>';
        self::top_sharebar();
        self::print_notice();
        echo '</div>';
        
        echo '<div id="content">';
        
        $g = self::clean_get();
        
        if( !isset( $g[ 'action' ] ) ){
            $g[ 'action' ] = 'list';
        }
        
        if( $g[ 'action' ] == 'list' ){
            self::list_shortcodes();
        }
        
        if( $g[ 'action' ] == 'edit' ){
            self::edit_shortcode();
        }
        
        if( $g[ 'action' ] == 'new' ){
            self::new_shortcode();
        }
        
        echo '</div>';
        
        self::page_bottom();
        
        echo '</div>';
        
    }
    
    public static function list_shortcodes(){
        
        Shortcoder_Import::check_import();
        
        $shortcodes = Shortcoder::list_all();
        $g = self::clean_get();
        
        echo '<h3 class="page_title">' . __( 'List of shortcodes created', 'shortcoder' ) . ' (' . count( $shortcodes ) . ')';
        echo '<span class="sc_menu">';
        
        echo '<button class="button sc_tags_filt_btn" tooltip="' . __( 'Filter by tags', 'shortcoder' ) . '"><span class="dashicons dashicons-tag sc_tags_filt_icon"></span>';
        echo '<div class="sc_tags_filter_wrap"><select class="sc_tags_filter" multiple>';
        $all_tags = Shortcoder::get_tags();
        foreach($all_tags as $tag){
            echo '<option value="' . $tag . '">' . $tag . '</option>';
        }
        echo '</select></div>';
        echo '</button>';
        
        echo '<span class="button search_btn" tooltip="' . __( 'Search shortcodes', 'shortcoder' ) . '"><span class="dashicons dashicons-search"></span><input type="search" class="search_box" placeholder="Search ..."/></span>';
        
        echo '<label for="import" class="button" tooltip="' . __( 'Import shortcodes', 'shortcoder' ) . '"><span class="dashicons dashicons-download"></span></label>';
        
        echo '<a href="' . self::get_link(array(
                'action' => 'sc_export',
                '_wpnonce' => wp_create_nonce( 'sc_export_data' )
            ), 'admin-ajax.php' ) . '" class="button" tooltip="' . __( 'Export shortcodes', 'shortcoder' ) . '"><span class="dashicons dashicons-upload"></span></a>';
        
        
        echo '<button class="button sort_btn" tooltip="' . __( 'Sort list', 'shortcoder' ) . '"><span class="dashicons dashicons-menu"></span> <span class="dashicons dashicons-arrow-down-alt sort_icon"></span></button>';
        
        echo '<a href="' . self::get_link(array( 'action' => 'new' )) . '" class="button button-primary sc_new_btn"><span class="dashicons dashicons-plus"></span> ' . __( 'Create a new shortcode', 'shortcoder' ) . '</a>';
        
        echo '</span>';
        echo '</h3>';
        
        echo '<ul class="sc_list" data-empty="' . __( 'No shortcodes are created. Go ahead create one !', 'shortcoder' ) . '">';
        foreach( $shortcodes as $name => $data ){
            
            $data = wp_parse_args( $data, Shortcoder::defaults() );
            
            $link = self::get_link(array(
                'action' => 'edit',
                'id' => base64_encode( $name )
            ));
            
            $delete_link = self::get_link(array(
                'action' => 'sc_admin_ajax',
                'do' => 'delete',
                'id' => base64_encode( $name ),
                '_wpnonce' => wp_create_nonce( 'sc_delete_nonce' )
            ), 'admin-ajax.php' );
            
            $disabled_text = ( $data[ 'disabled' ] == '1' ) ? '<small class="disabled_text">' . __( 'Temporarily disabled', 'shortcoder' ) . '</small>' : '';
            
            $selected_tags = implode( ',', $data[ 'tags' ] );
            
            echo '<li data-name="' . esc_attr( $name ) . '" data-tags="' . esc_attr( $selected_tags ) . '">';
            echo '<a href="' . $link . '" class="sc_link" title="' . __( 'Edit shortcode', 'shortcoder' ) . '">' . esc_attr( $name ) . $disabled_text . '</a>';
            
            echo '<span class="sc_controls">';
            
            if( isset( $data[ 'tags' ] ) && !empty( $data[ 'tags' ] ) && is_array( $data[ 'tags' ] ) ){
                echo '<ul class="sc_tags_list">';
                foreach( $data['tags'] as $tag ){
                    echo '<li data-tag-id="' . $tag . '">' . $tag . '</li>';
                }
                echo '</ul>';
            }
            
            echo '<a href="#" class="sc_copy" title="' . __( 'Copy shortcode', 'shortcoder' ) . '"><span class="dashicons dashicons-editor-code"></span></a>';
            echo '<a href="' . $delete_link . '" class="sc_delete" title="' . __( 'Delete', 'shortcoder' ) . '"><span class="dashicons dashicons-trash"></span></a>';
            echo '</span>';
            
            echo '<input type="text" value="' . self::get_shortcode( $name ) . '" class="sc_copy_box" readonly="readonly" title="' . __( 'Copy shortcode', 'shortcoder' ) . '" />';
            
            echo '</li>';
            
        }
        echo '</ul>';
            
        Shortcoder_Import::import_form();
        
    }
    
    public static function new_shortcode(){
        self::edit_shortcode( 'new' );
    }
    
    public static function edit_shortcode( $action = 'edit' ){
        
        self::save_shortcode();
        
        $shortcodes = Shortcoder::list_all();
        $g = self::clean_get();
        
        $page_title = __( 'New shortcode', 'shortcoder' );
        $action_btn = __( 'Create shortcode', 'shortcoder' );
        $sc_name = '';
        $values = array();
        
        if( $action == 'edit' ){
            
            $page_title = __( 'Edit shortcode', 'shortcoder' );
            $action_btn = __( 'Save settings', 'shortcoder' );
            
            if( !isset( $g[ 'id' ] ) ){
                echo '<p align="center">' . __( 'No shortcode ID provided !' ) . '</p>';
                return false;
            }
            
            $sc_name = base64_decode( $g[ 'id' ] );
            
            if( !array_key_exists( $sc_name, $shortcodes ) ){
                echo '<p align="center">' . __( 'Invalid shortcode ID or no such shortcode with name [' . esc_attr( $sc_name ) . '] exists !' ) . '</p>';
                return false;
            }
            
            $values = $shortcodes[ $sc_name ];
            
        }
        
        $values = wp_parse_args( $values, Shortcoder::defaults() );
        
        echo '<h3 class="page_title">' . $page_title;
        echo '<div class="sc_menu">';
        echo '<a href="' . self::get_link() . '" class="button sc_back_btn"><span class="dashicons dashicons-arrow-left-alt2"></span> ' . __( 'Back', 'shortcoder' ) . '</a>';
        echo '</div>';
        echo '</h3>';
        
        $note_btn = '<span class="dashicons dashicons-info sc_note_btn" title="Open note"></span>';
        
        echo '<form method="post" id="sc_edit_form">';
        
        echo '<div class="sc_section">';
        echo '<label for="sc_name">' . __( 'Name', 'shortcoder' ) . '</label>';
        echo '<div class="sc_name_wrap"><input type="text" id="sc_name" name="sc_name" value="' . esc_attr( $sc_name ) . '" class="widefat" required="required" placeholder="' . __( 'Enter a name for the shortcode, case sensitive', 'shortcoder' ) . '" pattern="[a-zA-z0-9 \-]+" />';
        echo ( $action == 'edit' ) ? '<div class="copy_shortcode">Your shortcode is - <input type="text" value="' . esc_attr( self::get_shortcode( $sc_name ) ) . '" class="sc_copy_field" readonly="readonly" title="' . __( 'Copy shortcode', 'shortcoder' ) . '"/></div>' : '';
        echo ( $action != 'edit' ) ? '<div class="copy_shortcode">' . __( 'Allowed characters A to Z, a to z, 0 to 9, hyphens, underscores and space', 'shortcoder' ) . '</div>' : '';
        echo '</div>';
        echo '</div>';
        
        echo '<input type="hidden" name="sc_old_name" value="' . esc_attr( $sc_name ) . '" />';
        
        echo '<div class="sc_section">';
        echo '<label for="sc_content">' . __( 'Shortcode content', 'shortcoder' ) . $note_btn . '</label>';
        
        echo '<p class="sc_note">' . __( 'Note: You can use any HTML, JavaScript, CSS as shortcode content. Shortcoder does not manipulate the shortcode content. What you provide above is what you get as output. Please verify the shortcode content for any syntax or JavaScript errors.', 'shortcoder' ) . '</p>';
        
        $editors_list = array(
            'text' => 'Text editor',
            'visual' => 'Visual editor',
            'code' => 'Code editor'
        );
        $editor = ( isset( $g[ 'editor' ] ) && array_key_exists( $g[ 'editor' ], $editors_list ) ) ? $g[ 'editor' ] : $values[ 'editor' ];
        
        echo '<span class="sc_editor_list sc_editor_icon_' . $editor . '"><select name="sc_editor" class="sc_editor button">';
        foreach( $editors_list as $id => $name ){
            echo '<option value="' . $id . '" ' . selected( $editor, $id ) . '>' . $name . '</option>';
        }
        echo '</select></span>';
        
        self::load_editor( $editor, $values[ 'content' ] );
        
        echo '</div>';
        
        $device_options = array(
            'all' => __( 'On both desktop and mobile devices', 'shortcoder' ),
            'mobile_only' => __( 'On mobile devices alone', 'shortcoder' ),
            'desktop_only' => __( 'On desktops alone', 'shortcoder' )
        );
        
        echo '<div class="sc_settings">';
        
        echo '<div class="sc_section">';
        echo '<h4>' . __( 'Settings', 'shortcoder' ) . '</h4>';
        echo '<label><input type="checkbox" name="sc_disable" value="1" ' . checked( $values[ 'disabled' ], '1', false ) . '/> ' . __( 'Temporarily disable this shortcode', 'shortcoder' ) . '</label>';
        echo '<label><input type="checkbox" name="sc_hide_admin" value="1" ' . checked( $values[ 'hide_admin' ], '1', false ) . '/> ' . __( 'Disable this Shortcode for administrators' ) . '</label>';
        echo '</div>';
        
        echo '<div class="sc_section">';
        echo '<h4>' . __( 'Visibility', 'shortcoder' ) . '</h4>';
        echo '<label>' . __( 'Show this shortcode', 'shortcoder' );
        echo '<select name="sc_devices">';
        foreach( $device_options as $id => $name ){
            echo '<option value="' . $id . '" ' . selected( $values[ 'devices' ], $id ) . '>' . $name . '</option>';
        }
        echo '</select></label>';
        echo '</div>';
        
        echo '<div class="sc_section">';
        echo '<h4>' . __( 'Tags', 'shortcoder' ) . '</h4>';
        echo '<select name="sc_tags[]" class="sc_edit_tags" multiple>';
        $all_tags = Shortcoder::get_tags();
        foreach($all_tags as $tag){
            echo '<option value="' . $tag . '" ' . ( in_array( $tag, $values[ 'tags' ] ) ? 'selected="selected"' : '' ) . '>' . $tag . '</option>';
        }
        echo '</select>';
        echo '</div>';
        
        echo '</div>';
        
        wp_nonce_field( 'sc_edit_nonce' );
        
        echo '<footer class="page_footer">';
        echo '<button class="button button-primary sc_save">' . $action_btn . '</button>';
        
        if( $action == 'edit' ){
            $delete_link = self::get_link(array(
                'action' => 'sc_admin_ajax',
                'do' => 'delete',
                'id' => base64_encode( $sc_name ),
                '_wpnonce' => wp_create_nonce( 'sc_delete_nonce' )
            ), 'admin-ajax.php' );
            echo '<a href="' . $delete_link . '" class="button sc_delete_ep" title="' . __( 'Delete', 'shortcoder' ) . '"><span class="dashicons dashicons-trash"></span></a>';
        }
        
        echo '</footer>';
        
        echo '</form>';
        
        $sc_wp_params = Shortcoder::wp_params_list();
        
        echo '<ul class="params_wrap">';

        foreach( $sc_wp_params as $group => $group_info ){
            echo '<li>' . $group_info[ 'name' ];
            echo '<ul class="wp_params">';
            foreach( $group_info[ 'params' ] as $param_id => $param_name ){
                echo '<li data-id="' . $param_id . '">' . $param_name . '</li>';
            }
            echo '</ul></li>';
        }
        
        echo '<li>' . __( 'Custom parameter', 'shortcoder' ) . '<ul>';
        echo '<li class="isc_form"><h4>' . __( 'Enter custom parameter name', 'shortcoder' ) . '</h4>';
                echo '<input type="text" class="cp_box" pattern="[a-zA-Z0-9]+"/> <button class="button cp_btn">' . __( 'Insert parameter', 'shortcoder' ) . '</button><p class="isc_info cp_info"><small>' . __( 'Only alphabets and numbers allowed. Custom parameters are case insensitive', 'shortcoder' ) . '</small></p></li>';
        echo '</ul></li>';
        
        echo '<li>' . __( 'Custom Fields', 'shortcoder' ) . '<ul>';
        echo '<li class="isc_form"><h4>' . __( 'Enter custom field name', 'shortcoder' ) . '</h4>';
                echo '<input type="text" class="cf_box" pattern="[a-zA-Z0-9_-]+"/> <button class="button cf_btn">' . __( 'Insert custom field', 'shortcoder' ) . '</button><p class="isc_info cf_info"><small>' . __( 'Only alphabets, numbers, underscore and hyphens are allowed. Cannot be empty.', 'shortcoder' ) . '</small></p></li>';
        echo '</ul></li>';
        
        echo '</ul>';
    }
    
    public static function save_shortcode(){
        
        if( $_POST && check_admin_referer( 'sc_edit_nonce' ) ){
            
            $p = wp_parse_args( self::clean_post(), array(
                'sc_name' => '',
                'sc_old_name' => '',
                'sc_content' => '',
                'sc_disable' => 0,
                'sc_hide_admin' => 0,
                'sc_devices' => 'all',
                'sc_editor' => 'text',
                'sc_tags' => array()
            ));
            
            $name = trim( self::clean_name( $p[ 'sc_name' ] ) );
            $old_name = trim( self::clean_name( $p[ 'sc_old_name' ] ) );
            $sc_name_changed = ( !empty( $old_name ) && $old_name != $name );
            
            if( empty( $name ) ){
                self::print_notice( 0 );
                return false;
            }
            
            $shortcodes = Shortcoder::list_all();
            $values = array(
                'content' => $p[ 'sc_content' ],
                'disabled' => $p[ 'sc_disable' ],
                'hide_admin' => $p[ 'sc_hide_admin' ],
                'devices' => $p[ 'sc_devices' ],
                'editor' => $p[ 'sc_editor' ],
                'tags' => $p[ 'sc_tags' ]
            );
            
            if( $sc_name_changed ){
                unset( $shortcodes[ $old_name ] );
            }
            
            if( array_key_exists( $name, $shortcodes ) ){
                self::print_notice( 2 );
            }else{
                self::print_notice( 1 );
            }
            
            $shortcodes[ $name ] = $values;
            
            update_option( 'shortcoder_data', $shortcodes );
            
            if( $sc_name_changed ){
                wp_safe_redirect( self::get_link( array(
                    'action' => 'edit',
                    'id' => base64_encode( $name )
                )));
            }
            
        }
        
    }
    
    public static function delete_shortcode( $name ){
        
        $shortcodes = Shortcoder::list_all();
        
        if( array_key_exists( $name, $shortcodes ) ){
            unset( $shortcodes[ $name ] );
            update_option( 'shortcoder_data', $shortcodes );
            return true;
        }else{
            return false;
        }
        
    }
    
    public static function get_link( $params = array(), $page = 'options-general.php' ){
        
        $params[ 'page' ] = 'shortcoder';
        return add_query_arg( $params, admin_url( $page ) );
        
    }
    
    public static function get_shortcode( $name = '' ){
        return esc_attr( '[sc name="' . $name . '"]' );
    }
    
    public static function admin_ajax(){
        
        $g = self::clean_get();
        
        if( $g[ 'do' ] == 'delete' && isset( $g[ 'id' ] ) && check_admin_referer( 'sc_delete_nonce' ) ){
            $sc_name = base64_decode( $g[ 'id' ] );
            if( self::delete_shortcode( $sc_name ) ){
                echo 'DELETED';
            }else{
                echo 'FAILED';
            }
        }
        
        if( $g[ 'do' ] == 'insert_shortcode' ){
            include_once( 'sc-insert.php' );
        }
        
        die(0);
    }
    
    public static function add_qt_button(){
        
        $screen = get_current_screen();
        if( self::$pagehook == $screen->id )
            return;
        
        add_thickbox();
        
        echo '
        <script>
        if(window.addEventListener){
            window.addEventListener("load", function(){
                if( typeof QTags === "function" ){
                    QTags.addButton( "QT_sc_insert", "Shortcoder", sc_show_insert );
                }
            });
        }
        
        function sc_show_insert(){
            tb_show( "Insert a Shortcode", "' . admin_url( 'admin-ajax.php?action=sc_admin_ajax&do=insert_shortcode&TB_iframe=true' ) . '" );
        }
        </script>';
    }
    
    public static function register_mce(){
        add_filter( 'mce_buttons', array( __class__, 'register_mce_button' ) );
        add_filter( 'mce_external_plugins', array( __class__, 'register_mce_js' ) );
    }
    
    public static function register_mce_button( $buttons ){
        
        if( self::is_sc_admin() )
            return $buttons;
        
        array_push( $buttons, 'separator', 'shortcoder' );
        return $buttons;
        
    }
    
    public static function register_mce_js( $plugins ){
        
        if( self::is_sc_admin() )
            return $plugins;
        
        $plugins[ 'shortcoder' ] =  SC_ADMIN_URL . '/js/tinymce/editor_plugin.js';
        return $plugins;
        
    }
    
    public static function load_editor( $type, $value ){
        
        if( $type == 'code' ){
            self::load_codemirror_editor( $value );
        }else{
            wp_editor( $value, 'sc_content', array( 'wpautop'=> false, 'textarea_rows'=> 15, 'tinymce' => ( $type == 'visual' ) ) );
        }
        
    }
    
    public static function load_codemirror_editor( $value ){
        echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/codemirror.min.css" rel="stylesheet">';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/codemirror.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/mode/htmlmixed/htmlmixed.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/mode/css/css.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/mode/xml/xml.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/mode/javascript/javascript.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.42.0/addon/selection/active-line.min.js"></script>';
        
        echo '<div class="sc_cm_menu"></div>';
        echo '<textarea name="sc_content" id="sc_content">' . esc_textarea( $value ) . '</textarea>';
        
        echo '<script>var sc_cm_editor = true;</script>';
    }
    
    public static function page_bottom(){
        
        echo '<div class="coffee_box">
        <div class="coffee_amt_wrap">
        <p><select class="coffee_amt">
            <option value="2">$2</option>
            <option value="3">$3</option>
            <option value="4">$4</option>
            <option value="5" selected="selected">$5</option>
            <option value="6">$6</option>
            <option value="7">$7</option>
            <option value="8">$8</option>
            <option value="9">$9</option>
            <option value="10">$10</option>
            <option value="11">$11</option>
            <option value="12">$12</option>
            <option value="">Custom</option>
        </select></p>
        <a class="button button-primary buy_coffee_btn" href="https://www.paypal.me/vaakash/5" data-link="https://www.paypal.me/vaakash/" target="_blank">Buy me a coffee !</a>
        </div>
        <h2>Buy me a coffee !</h2>
        <p>Thank you for using Shortcoder. If you found the plugin useful buy me a coffee ! Your donation will motivate and make me happy for all the efforts. You can donate via PayPal.</p>';
        echo '</div>';
        
        echo '<p class="credits_box"><img src="' . SC_ADMIN_URL . '/images/aw.png" /> Created by <a href="https://goo.gl/aHKnsM" target="_blank">Aakash Chakravarthy</a> - Follow me on <a href="https://twitter.com/vaakash" target="_blank">Twitter</a>, <a href="https://fb.com/aakashweb" target="_blank">Facebook</a>, <a href="https://www.linkedin.com/in/vaakash/" target="_blank">LinkedIn</a>. Check out <a href="https://goo.gl/OAxx4l" target="_blank">my other works</a>.
        
        <a href="https://goo.gl/ltvnIE" class="rate_link" target="_blank">Rate <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span> if you like Shortcoder</a>
        
        </p>';
        
    }
    
    public static function top_sharebar(){
        echo '
        <div class="top_sharebar">
        
        <a href="https://goo.gl/r8Qr7Y" class="help_link" target="_blank" title="Help"><span class="dashicons dashicons-editor-help"></span></a>
        <a href="https://goo.gl/URfxp2" class="help_link" target="_blank" title="Report issue"><span class="dashicons dashicons-flag"></span></a>
        
        <a class="share_btn rate_btn" href="https://goo.gl/ltvnIE" target="_blank" title="Please rate 5 stars if you like Shortcoder"><span class="dashicons dashicons-star-filled"></span> Rate 5 stars</a>
        <a class="share_btn twitter" href="https://twitter.com/intent/tweet?ref_src=twsrc%5Etfw&related=vaakash&text=Check%20out%20Shortcoder,%20a%20%23wordpress%20plugin%20to%20create%20shortcodes%20for%20HTML,%20JavaScript%20snippets%20easily&tw_p=tweetbutton&url=https%3A%2F%2Fwww.aakashweb.com%2Fwordpress-plugins%2Fshortcoder%2F&via=vaakash" target="_blank"><span class="dashicons dashicons-twitter"></span> Tweet about Shortcoder</a>
        
        </div>';
    }
    
    public static function action_links( $links ){
        array_unshift( $links, '<a href="https://goo.gl/qMF3iE" target="_blank">Donate</a>' );
        array_unshift( $links, '<a href="'. esc_url( admin_url( 'options-general.php?page=shortcoder' ) ) .'">Settings</a>' );
        return $links;
    }
    
    public static function print_notice( $id = '' ){
        
        $g = self::clean_get();
        $type = 'success';
        $msg = '';
        
        if( $id == '' ){
            if( !isset( $g[ 'msg' ] ) ){
                return false;
            }
            $id = $g[ 'msg' ];
        }
        
        if( $id == 0 ){
            $msg = __( 'Shortcode name is empty. Cannot save settings !', 'shortcoder' );
            $type = 'error';
        }
        
        if( $id == 1 ){
            $msg = __( 'Shortcode created successfully', 'shortcoder' );
        }
        
        if( $id == 2 ){
            $msg = __( 'Shortcode updated successfully', 'shortcoder' );
        }
        
        if( $id == 3 ){
            $msg = __( 'Shortcode deleted successfully', 'shortcoder' );
        }
        
        if( $id == 4 ){
            $msg = __( 'Shortcode name edited successfully', 'shortcoder' );
        }
        
        if( $msg != '' ){
            echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $msg . '</p></div>';
        }
    }
    
    public static function clean_name( $name = '' ){
        
        return trim( preg_replace('/[^0-9a-zA-Z\- _]/', '', $name ) );
        
    }
    
    public static function clean_get(){
        
        foreach( $_GET as $k=>$v ){
            $_GET[$k] = sanitize_text_field( $v );
        }

        return $_GET;
    }
    
    public static function clean_post(){
        
        return stripslashes_deep( $_POST );
        
    }
    
    public static function is_sc_admin(){
        
        if( !function_exists( 'get_current_screen' ) )
            return false;
        
        $screen = get_current_screen();
        if( self::$pagehook == $screen->id ){
            return true;
        }else{
            return false;
        }
        
    }
    
}

Shortcoder_Admin::init();

?>