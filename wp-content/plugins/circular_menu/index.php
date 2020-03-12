<?php
/*
Plugin Name: Circular Menu
Plugin URI: http://sonicwall.com
Description: A WordPress plugin to generate circular menus
Version: 1.2.1
Author: Brad Kendall
*/


//Settings

function circularMenuLightbox($link)
{
    $ie =(strpos($_SERVER["HTTP_USER_AGENT"], 'Trident/7.0; rv:11.0') > -1);
    
    if ($link->lightbox == "on" && !$ie)
    {
        return " data-fancybox data-type='iframe' ";
    }
    else if ($link->lightbox == "on" && $ie)
    {
        return " target='_blank' ";
    }
    
    return "";
}

function getDatasheets()
{
    $datasheets = array();

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'resources-slug' ),
        'post_status'            => array( 'publish' ),
        'nopaging'               => true,
        'order'                  => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'wpcf-content-type',
                'value'   => 'Datasheet',
                'compare' => 'LIKE',
            ),
        ),
    );

    // The Query
    $resources_query = new WP_Query( $args );

    $resources = $resources_query->posts;

    foreach($resources as $resource) {
        $datasheet = new stdClass();
        $datasheet->ID = $resource->ID;
        $datasheet->title = $resource->post_title;
        $datasheet->link = get_post_meta($resource->ID, 'wpcf-resource-content', true);

        $datasheets[] = $datasheet;
    }

    return $datasheets;
}

function circular_menu_add_custom_box()
{
    wp_register_script( 'circular_menu_admin_js', plugins_url('js/admin.js', __FILE__));
    wp_enqueue_script( 'circular_menu_admin_js' );
    $screens = ['post', 'page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'circular_menu_box_id', // Unique ID
            'Circular Menu',  // Box title
            'circular_menu_custom_box_html',  // Content callback, must be of type callable
            $screen // Post type
        );
    }
}

function circular_menu_custom_box_html($post)
{
    //Get the data
    $circular_menu_enabled = get_post_meta($post->ID, 'circular_menu_enabled', true);
    $circular_menu_x_pos = get_post_meta($post->ID, 'circular_menu_x_pos', true);
    $circular_menu_y_pos = get_post_meta($post->ID, 'circular_menu_y_pos', true);
    $circular_menu_anchor = get_post_meta($post->ID, 'circular_menu_anchor', true);

    if (!$circular_menu_x_pos)
    {
        $circular_menu_x_pos = get_option('circular_menu_x_pos');
    }

    if (!$circular_menu_y_pos)
    {
        $circular_menu_y_pos = get_option('circular_menu_y_pos');
    }

    if (!$circular_menu_anchor)
    {
        $circular_menu_anchor = get_option('circular_menu_anchor');
    }

    $links = getPostLinks($post);
    
    $datasheets = getDatasheets();

    ?>
    <table class="form-table circularMenuForm">
        <tr valign="top">
        <th scope="row">Enable / Disable Circular Menu</th>
        <td>
            <select name="circular_menu_enabled" id="circular_menu_enabled" class="postbox">
                <option value="" <?php selected($circular_menu_enabled, ''); ?>>Enabled</option>
                <option value="disabled" <?php selected($circular_menu_enabled, 'disabled'); ?>>Disabled</option>
            </select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Custom X Position (in pixels)</th>
        <td>
            <input type="number" name="circular_menu_x_pos" value="<?php echo $circular_menu_x_pos; ?>" disabled="disabled">
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Custom Y Position (in pixels)</th>
        <td>
            <input type="number" name="circular_menu_y_pos" value="<?php echo $circular_menu_y_pos; ?>" disabled="disabled">
        </td>
        </tr>

        <tr>
            <th scope="row">Anchor</th>
            <td>
                Top / Left
                <input type="radio" name="circular_menu_anchor" value="tl" <?php checked( $circular_menu_anchor, 'tl' ); ?>>
            </td>
            <td>
                Top / Right
                <input type="radio" name="circular_menu_anchor" value="tr" <?php checked( $circular_menu_anchor, 'tr' ); ?>>
            </td>
            <td>
                Bottom / Left
                <input type="radio" name="circular_menu_anchor" value="bl" <?php checked( $circular_menu_anchor, 'bl' ); ?>>
            </td>
            <td>
                Bottom / Right
                <input type="radio" name="circular_menu_anchor" value="br" <?php checked( $circular_menu_anchor, 'br' ); ?>>
            </td>
        </tr>

    </table>

    <style>
    .circularMenuForm td input[type=text] {
        width: 100%;
    }
    </style>
    <table class="circularMenuForm" style="overflow-x: scroll;">
        <thead>
            <td>Title</td>
            <td>Url</td>
            <td>Icon</td>
            <td>New Window</td>
            <td>Enabled</td>
            <td>Lightbox</td>
            <td>Datasheet</td>
            <td>Default</td>
        </thead>
        <tbody>
            <?php $i = 0; foreach($links as $link) { ?>
            <tr class="link-group">
                <td style="max-width: 100px;">
                    <input type="text" class="circular_menu_title" name="circular_menu_title_<?php echo $i;?>" placeholder="Resources" value="<?php echo $link->title;?>" disabled="disabled" data-default="<?php echo $link->default->title;?>">
                </td>
                <td>
                    <input type="text" class="circular_menu_link" name="circular_menu_link_<?php echo $i;?>" placeholder="https://sonicwall.com" value="<?php echo $link->link;?>" disabled="disabled" data-default="<?php echo $link->default->link;?>">
                </td>
                <td style="max-width: 125px;">
                    <input type="text" class="circular_menu_icon" name="circular_menu_icon_<?php echo $i;?>" placeholder="fa-facebook" value="<?php echo $link->icon;?>" disabled="disabled" data-default="<?php echo $link->default->icon;?>">
                </td>
                <td>
                    <input type="checkbox" class="circular_menu_new_window" name="circular_menu_new_window_<?php echo $i;?>" <?php checked( $link->new_window, 'on' ); ?> disabled="disabled" data-default="<?php echo $link->default->new_window;?>">
                </td>
                <td>
                    <input type="checkbox" class="circular_menu_enabled" name="circular_menu_enabled_<?php echo $i;?>" <?php checked( $link->enabled, 'on' ); ?> disabled="disabled" data-default="<?php echo $link->default->enabled;?>">
                </td>
                <td>
                    <input type="checkbox" class="circular_menu_lightbox" name="circular_menu_lightbox_<?php echo $i;?>" <?php checked( $link->lightbox, 'on' ); ?> disabled="disabled" data-default="<?php echo $link->default->lightbox;?>">
                </td>
                <td>
                    <select class="datasheet-selector" disabled="disabled" style="max-width: 100px;">
                        <option value=""></option>
                        <?php
                        foreach ($datasheets as $datasheet)
                        {
                            ?>
                            <option value="<?php echo $datasheet->link;?>"><?php echo $datasheet->title;?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                   <input type="checkbox" class="toggle-default" name="circular_menu_custom_<?php echo $i;?>" <?php checked( $link->custom, false ); ?> disabled="disabled">
                </td>
            </tr>
            <?php 
            $i++; 
          }?>
        </tbody>
    </table>
<?php
}

function getPostLinks($post)
{
    $defaultLinks = getDefaultLinks();

    $links = array();

    for ($i = 0; $i < getNumberOfLinks(); $i++)
    {
        if (!get_post_meta($post->ID, 'circular_menu_title_'.$i))
        {
            $copy =  $defaultLinks[$i];
            $copy->default = $defaultLinks[$i];
            $links[] = $copy;
        }
        else
        {
            $linkObj = new stdClass();
            $linkObj->title = get_post_meta($post->ID, 'circular_menu_title_'.$i, true);
            $linkObj->link = get_post_meta($post->ID, 'circular_menu_link_'.$i, true);
            $linkObj->icon = get_post_meta($post->ID, 'circular_menu_icon_'.$i, true);
            $linkObj->new_window = get_post_meta($post->ID, 'circular_menu_new_window_'.$i, true);
            $linkObj->enabled = get_post_meta($post->ID, 'circular_menu_enabled_'.$i, true);
            $linkObj->lightbox = get_post_meta($post->ID, 'circular_menu_lightbox_'.$i, true);
            $linkObj->custom = true;
            $linkObj->default = $defaultLinks[$i];
            $links[] = $linkObj;
        }
    }
    
    $switch_to_default = true;
    
    foreach($links as $link)
    {
        if ($link->enabled == "on")
        {
            $switch_to_default = false;
            break;
        }
    }
    
    if ($switch_to_default)
    {
        $links = $defaultLinks;   
    }
    
    return $links;
}

function getDefaultLinks()
{
    $links = array();

    for ($i = 0; $i < getNumberOfLinks(); $i++)
    {
        $linkObj = new stdClass();
        $linkObj->title = get_option('circular_menu_title_'.$i);
        $linkObj->link = get_option('circular_menu_link_'.$i);
        $linkObj->icon = get_option('circular_menu_icon_'.$i);
        $linkObj->new_window = get_option('circular_menu_new_window_'.$i);
        $linkObj->enabled = get_option('circular_menu_enabled_'.$i);
        $linkObj->lightbox = get_option('circular_menu_lightbox_'.$i);
        $linkObj->custom = false;

        $links[] = $linkObj;
    }

    return $links;
}

function circular_menu_save_postdata($post_id)
{
    if( ! ( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id ) ) ) {
               
        if (array_key_exists('circular_menu_enabled', $_POST)) {
            update_post_meta(
                $post_id,
                'circular_menu_enabled',
                $_POST['circular_menu_enabled']
            );
        }

        
            if (array_key_exists('circular_menu_x_pos', $_POST)) {
                update_post_meta(
                    $post_id,
                    'circular_menu_x_pos',
                    $_POST['circular_menu_x_pos']
                );
            }

            if (array_key_exists('circular_menu_y_pos', $_POST)) {
                update_post_meta(
                    $post_id,
                    'circular_menu_y_pos',
                    $_POST['circular_menu_y_pos']
                );
            }

            if (array_key_exists('circular_menu_anchor', $_POST)) {
                update_post_meta(
                    $post_id,
                    'circular_menu_anchor',
                    $_POST['circular_menu_anchor']
                );
            }


            for ($i = 0; $i <= getNumberOfLinks(); $i++)
            {
                //Update in not default

                if (!array_key_exists('circular_menu_custom_'.$i, $_POST)) {
                    //Custom is enabled, save the changes
                    if (array_key_exists('circular_menu_title_'.$i, $_POST)) {
                        update_post_meta(
                            $post_id,
                            'circular_menu_title_'.$i,
                            $_POST['circular_menu_title_'.$i]
                        );
                    }

                    if (array_key_exists('circular_menu_link_'.$i, $_POST)) {
                        update_post_meta(
                            $post_id,
                            'circular_menu_link_'.$i,
                            $_POST['circular_menu_link_'.$i]
                        );
                    }

                    if (array_key_exists('circular_menu_icon_'.$i, $_POST)) {
                        update_post_meta(
                            $post_id,
                            'circular_menu_icon_'.$i,
                            $_POST['circular_menu_icon_'.$i]
                        );
                    }

                    $circular_menu_new_window = "off";

                    if (array_key_exists('circular_menu_new_window_'.$i, $_POST)) {
                        $circular_menu_new_window = "on";
                    }
                  
                    update_post_meta(
                        $post_id,
                        'circular_menu_new_window_'.$i,
                        $circular_menu_new_window
                    );

                    $circular_menu_enabled = "off";

                    if (array_key_exists('circular_menu_enabled_'.$i, $_POST)) {
                        $circular_menu_enabled = "on";
                    }
                  
                    update_post_meta(
                        $post_id,
                        'circular_menu_enabled_'.$i,
                        $circular_menu_enabled
                    );

                    $circular_menu_lightbox = "off";

                    if (array_key_exists('circular_menu_lightbox_'.$i, $_POST)) {
                        $circular_menu_lightbox = "on";
                    }
                  
                    update_post_meta(
                        $post_id,
                        'circular_menu_lightbox_'.$i,
                        $circular_menu_lightbox
                    );
                }
                else
                {
                    //Custom is diabled, delete the changes

                    delete_post_meta(
                        $post_id,
                        'circular_menu_title_'.$i
                    );
             
                    delete_post_meta(
                        $post_id,
                        'circular_menu_link_'.$i
                    );
             
                    delete_post_meta(
                        $post_id,
                        'circular_menu_icon_'.$i
                    );
                      
                    delete_post_meta(
                        $post_id,
                        'circular_menu_new_window_'.$i
                    );

                    delete_post_meta(
                        $post_id,
                        'circular_menu_enabled_'.$i
                    );

                    delete_post_meta(
                        $post_id,
                        'circular_menu_lightbox_'.$i
                    );


                }
        }
    }
}

add_action('save_post', 'circular_menu_save_postdata');

add_action('add_meta_boxes', 'circular_menu_add_custom_box');

//The actual plugin

function enabledForPost($post)
{
    $menu_enabled = get_metadata('post', $post->ID, 'circular_menu_enabled', true);
    
    $ret = new stdClass();

    $ret->post = $menu_enabled === "enabled";
    $ret->default = $menu_enabled === "";

    return $ret;
}

function load_circular_menu($content)
{
	global $post;
	if ($post && $post->ID)
	{
        $enabled = enabledForPost($post);

		if ($enabled->post || $enabled->default)
		{
            wp_register_script('circular_menu_js', plugins_url('js/menu.js', __FILE__));
            wp_enqueue_script('circular_menu_js');
            wp_register_style('circular_menu_css', plugins_url('css/menu.css',__FILE__ ));
            wp_enqueue_style('circular_menu_css');
            wp_register_style('fancybox_css', plugins_url('css/jquery.fancybox.min.css', __FILE__ ));
            wp_enqueue_style('fancybox_css');
            wp_register_script('fancybox_js', plugins_url('js/jquery.fancybox.min.js', __FILE__ ));
            wp_enqueue_script('fancybox_js');
            wp_register_script('clipboard_js', plugins_url('js/clipboard.min.js', __FILE__));
            wp_enqueue_script('clipboard_js');
            wp_register_script('lightbox_toolbar_js', plugins_url('js/lightbox-toolbar.js', __FILE__));
            wp_enqueue_script('lightbox_toolbar_js');

			return build_circular_menu().$content;
		}
	}

    return $content;
}

function getMenuData($post)
{
    // $enabled = enabledForPost($post);

    $ret = new stdClass();

    $ret->links = getPostLinks($post);

    $ret->x = get_metadata('post', $post->ID, 'circular_menu_x_pos', true);

    if (!$ret->x)
    {
        $ret->x = get_option('circular_menu_x_pos');
    }
    $ret->y = get_metadata('post', $post->ID, 'circular_menu_y_pos', true);

    if (!$ret->y)
    {
        $ret->y = get_option('circular_menu_y_pos');
    }

    $ret->anchor = get_metadata('post', $post->ID, 'circular_menu_anchor', true);

    if (!$ret->anchor)
    {
        $ret->anchor = get_option('circular_menu_anchor');
    }
    

    return $ret;
}

function build_circular_menu()
{
     $template = load_template( dirname( __FILE__ ) . '/templates/menu.php' );

     return $template;
}

function getNumberOfLinks()
{
    return 6;
}

add_action('the_content','load_circular_menu');


//Admin menu
// create custom plugin settings menu
add_action('admin_menu', 'circular_menu_menu');

function circular_menu_menu() {

    wp_register_script( 'circular_menu_admin_js', plugins_url('js/admin.js', __FILE__));
    wp_enqueue_script( 'circular_menu_admin_js' );

    //create new top-level menu
    add_menu_page('Circular Menu', 'Circular Menu', 'administrator', __FILE__, 'circular_menu_settings_page' );

    //call register settings function
    add_action( 'admin_init', 'register_circular_menu_settings' );
}


function register_circular_menu_settings() {

    //register our settings
    register_setting( 'circular-menu-settings-group', 'circular_menu_x_pos' );
    register_setting( 'circular-menu-settings-group', 'circular_menu_y_pos' );
    register_setting( 'circular-menu-settings-group', 'circular_menu_anchor' );

    //Register all the links
    for ($i = 0; $i < getNumberOfLinks(); $i++)
    {
        register_setting( 'circular-menu-settings-group', 'circular_menu_title_'.$i );
        register_setting( 'circular-menu-settings-group', 'circular_menu_link_'.$i );
        register_setting( 'circular-menu-settings-group', 'circular_menu_icon_'.$i );
        register_setting( 'circular-menu-settings-group', 'circular_menu_new_window_'.$i );
        register_setting( 'circular-menu-settings-group', 'circular_menu_enabled_'.$i );
        register_setting( 'circular-menu-settings-group', 'circular_menu_lightbox_'.$i );
    }
    
}

function circular_menu_settings_page() {
?>
<div class="wrap">
<h1>Circular Menu</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'circular-menu-settings-group' ); ?>
    <?php do_settings_sections( 'circular-menu-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                Custom X Position (in pixels)
            </th>
            <td>
                <input type="number" name="circular_menu_x_pos" value="<?php echo esc_attr( get_option('circular_menu_x_pos') ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                Custom Y Position (in pixels)
            </th>
            <td>
                <input type="number" name="circular_menu_y_pos" value="<?php echo esc_attr( get_option('circular_menu_y_pos') ); ?>" />
            </td>
        </tr>

        <tr>
            <th scope="row">Anchor</th>
            <td>
                Top / Left
                <input type="radio" name="circular_menu_anchor" value="tl" <?php checked( get_option('circular_menu_anchor'), 'tl' ); ?>>
            </td>
            <td>
                Top / Right
                <input type="radio" name="circular_menu_anchor" value="tr" <?php checked( get_option('circular_menu_anchor'), 'tr' ); ?>>
            </td>
            <td>
                Bottom / Left
                <input type="radio" name="circular_menu_anchor" value="bl" <?php checked( get_option('circular_menu_anchor'), 'bl' ); ?>>
            </td>
            <td>
                Bottom / Right
                <input type="radio" name="circular_menu_anchor" value="br" <?php checked( get_option('circular_menu_anchor'), 'br' ); ?>>
            </td>
        </tr>

    </table>
    
    <table class="form-table">
        <thead>
            <td>Link #</td>
            <td>Title</td>
            <td>Url</td>
            <td>Icon</td>
            <td>New Window</td>
            <td>Enabled</td>
            <td>Lightbox</td>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < getNumberOfLinks(); $i++) { ?>
            <tr>
                <td>
                    <p>Link <?php echo $i + 1;?></p>
                </td>
                <td>
                    <input type="text" name="circular_menu_title_<?php echo $i;?>" value="<?php echo esc_attr( get_option('circular_menu_title_'.$i) ); ?>" />
                </td>
                <td>
                    <input type="text" name="circular_menu_link_<?php echo $i;?>" value="<?php echo esc_attr( get_option('circular_menu_link_'.$i) ); ?>" />
                </td>
                <td>
                    <input type="text" name="circular_menu_icon_<?php echo $i;?>" value="<?php echo esc_attr( get_option('circular_menu_icon_'.$i) ); ?>" />
                </td>
                <td>
                    <input type="checkbox" name="circular_menu_new_window_<?php echo $i;?>" <?php checked( get_option('circular_menu_new_window_'.$i), 'on' ); ?> />
                </td>
                <td>
                    <input type="checkbox" name="circular_menu_enabled_<?php echo $i;?>" <?php checked( get_option('circular_menu_enabled_'.$i), 'on' ); ?> />
                </td>
                <td>
                    <input type="checkbox" name="circular_menu_lightbox_<?php echo $i;?>" <?php checked( get_option('circular_menu_lightbox_'.$i), 'on' ); ?> />
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>