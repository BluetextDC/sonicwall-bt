<?php
/*
Plugin Name: SW Custom Mega Menu
Plugin URI: http://sonicwall.com
Description: A WordPress plugin to generate complex, custom mega menus
Version: 1.0.4
Author: Brad Kendall
*/


add_action('wp_enqueue_scripts', 'megamenu_override');

function megamenu_override() {
  define( 'MEGAMENU_OVERRIDE_URI', plugin_dir_url(__FILE__) );
  wp_dequeue_script( 'avia-megamenu' );
  wp_deregister_script( 'avia-megamenu' );
  wp_enqueue_script( 'avia-megamenu',  MEGAMENU_OVERRIDE_URI . 'avia-snippet-megamenu.js', array(), '', true );
}

add_action ('wp_enqueue_scripts', 'sticky_header_override');

function sticky_header_override() {
  define( 'STICKY_HEADER_OVERRIDE_URI', plugin_dir_url(__FILE__) );
  wp_dequeue_script( 'avia-sticky-header' );
  wp_deregister_script( 'avia-sticky-header' );
  wp_enqueue_script( 'avia-sticky-header',  STICKY_HEADER_OVERRIDE_URI . 'avia-snippet-sticky-header.js', array(), '', true );
}

class new_walker extends Walker_Nav_Menu
{

	var $debug;
	var $menu_type;
    var $col_type;
	var $output_append;
	var $menu_left_parent;


	function isProductMenu()
	{
		return $this->menu_type === "products";
	}

	function isSolutionsMenu()
	{
		return $this->menu_type === "solutions";
	}

	function isPartnersMenu()
	{
		return $this->menu_type === "partners";
	}

	function getMenuType()
	{
		return $this->menu_type;
	}
    
    function getColType()
    {
        return $this->col_type;
    }

	function setMenuType($item, $depth)
	{
		//TODO - Change this to a proper lookup
		if ($depth == 0 && in_array("menu-products", $item->classes))
		{
			$this->menu_type = "products";
		}
		else if ($depth == 0 && in_array("menu-solutions", $item->classes))
		{
			$this->menu_type = "solutions";
		}
		else if ($depth == 0 && in_array("menu-partners", $item->classes))
		{
			$this->menu_type = "partners";
		}
		else if ($depth == 0)
		{
			$this->menu_type = "default";
		}
	}
    
    function setColumnType($item, $depth)
	{
		//TODO - Change this to a proper lookup
		if ($depth == 0 && in_array("4-cols", $item->classes))
		{
			$this->col_type = "col-md-3";
		}
		else if ($depth == 0 && in_array("3-cols", $item->classes))
        {
            $this->col_type = "col-md-4";
        }
	}

	function comment($message, $depth, &$output, $args)
	{
		if (!$this->debug)
		{
			return $output;
		}
		$output .= "\n<!–– ";
		$output .= $message;
		$output .= " - depth: ".$depth;
		$output .= " - menu_type: ".$this->getMenuType();
		$output .= " ––>\n";

		return $output;
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
	        $t = '';
	        $n = '';
	    } else {
	        $t = "\t";
	        $n = "\n";
	    }
	    $indent = str_repeat( $t, $depth );


		if ($this->isProductMenu())
		{
			if ($depth == 0)
			{
				$output = $this->comment("begin start_lvl", $depth, $output, $args);
				//Add the nav container
				$output .= "{$n}{$indent}<div class='menu-container'><div class='menu-container-center'>{$n}";

				//Add the left hand side
				$output .= "{$n}{$indent}<div class='menu-left'>{$n}";
				$output = $this->comment("end start_lvl", $depth, $output, $args);	
			}
			else if ($depth == 1)
			{
				$this->output_append = $this->comment("begin start_lvl", $depth, $this->output_append, $args);
				$this->output_append .= "{$n}{$indent}<div class='menu-right menu-item-{$this->menu_left_parent}'>{$n}";

				//Add the image / text container
				$this->output_append .= "{$n}{$indent}<div class='details-container'><img alt='Product Image'><p></p></div>{$n}";

				$this->output_append = $this->comment("end start_lvl", $depth, $this->output_append, $args);	
			}
		}
		else if ($this->isSolutionsMenu())
		{
			if ($depth == 0)
			{
				$output = $this->comment("begin start_lvl", $depth, $output, $args);
				//Add the nav container
				$output .= "{$n}{$indent}<div class='menu-container'>{$n}";

				//Add the left hand side
				$output .= "{$n}{$indent}<div class='grid-container container'>{$n}";
				$output .= "{$n}{$indent}<div class='row'>{$n}";

                $col_type = $this->getColType();
				//Added
				$output .= "{$n}{$indent}<div class='".$col_type."'>{$n}";
				//End Added
				$output = $this->comment("end start_lvl", $depth, $output, $args);	
			}
			else if ($depth == 1)
			{

				$output = $this->comment("begin start_lvl", $depth, $output, $args);
				// $output .= "{$n}{$indent}<div class='menu-right>{$n}";


				$output = $this->comment("end start_lvl", $depth, $output, $args);	
			}
		}
		else if ($this->isPartnersMenu())
		{
			if ($depth == 0)
			{
				$output = $this->comment("begin start_lvl", $depth, $output, $args);
				//Add the nav container
				$output .= "{$n}{$indent}<div class='menu-container'>{$n}";

				//Add the left hand side
				$output .= "{$n}{$indent}<div class='grid-container container'>{$n}";
				$output .= "{$n}{$indent}<div class='row-fluid'>{$n}";
				$output = $this->comment("end start_lvl", $depth, $output, $args);	
			}
			else if ($depth == 1)
			{

				$output = $this->comment("begin start_lvl", $depth, $output, $args);
				// $output .= "{$n}{$indent}<div class='menu-right>{$n}";


				$output = $this->comment("end start_lvl", $depth, $output, $args);	
			}
		}
		else
		{		
		 	$output = $this->comment("begin start_lvl", $depth, $output, $args);
		    // Default class.
		    $classes = array( 'sub-menu' );
		 

		    $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		 
		    $output .= "{$n}{$indent}<ul$class_names>{$n}";
		    $output = $this->comment("end start_lvl", $depth, $output, $args);	
		}
	
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
                $n = '';
        } else {
                $t = "\t";
                $n = "\n";
        }
        $indent = str_repeat( $t, $depth );

        if ($this->isProductMenu())
        {
        	if ($depth == 0)
	        {
	        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
	        	$output .= "$indent</div>{$n}";

	        	if ($this->output_append)
	        	{
	        		$output .= $this->output_append;
	        		$this->output_append = "";
	        	}
	        	$output = $this->comment("end end_lvl", $depth, $output, $args);
	        }
	        else if ($depth == 1)
	        {
	        	$this->output_append = $this->comment("begin end_lvl", $depth, $this->output_append, $args);
	        	//Close the left container
	        	$this->output_append .= "$indent</div>{$n}";
	        	$this->output_append = $this->comment("end end_lvl", $depth, $this->output_append, $args);
	        }
        }
        else if ($this->isSolutionsMenu())
        {
        	if ($depth == 0)
	        {
	        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
	        	$output .= "$indent</div></div>{$n}";

	        	
	        	$output = $this->comment("end end_lvl", $depth, $output, $args);
	        }
	        else if ($depth == 1)
	        {
	        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
	        	//Close the left container
	        	// $output .= "$indent</div>{$n}";
	        	$output = $this->comment("end end_lvl", $depth, $output, $args);
	        }
        }
        else if ($this->isPartnersMenu())
        {
        	if ($depth == 0)
	        {
	        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
	        	$output .= "$indent</div></div>{$n}";

	        	
	        	$output = $this->comment("end end_lvl", $depth, $output, $args);
	        }
	        else if ($depth == 1)
	        {
	        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
	        	//Close the left container
	        	// $output .= "$indent</div>{$n}";
	        	$output = $this->comment("end end_lvl", $depth, $output, $args);
	        }
        }
        else
        {
        	$output = $this->comment("begin end_lvl", $depth, $output, $args);
        	$output .= "$indent</ul>{$n}";
        	$output = $this->comment("end end_lvl", $depth, $output, $args);
        }
	}


    function get_default_item($item)
    {
        if ( function_exists('icl_object_id') ) {

            $default_language = wpml_get_default_language(); // will return 'en'
            $default_item = icl_object_id($item->ID, 'post', true, $default_language);
            
            if ($default_item)
            {
                return $default_item;
            }
            else
            {
                return $item;
            }
        }
        else
        {
            return $item;
        }
    }
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	
		$this->setMenuType($item, $depth);
        $this->setColumnType($item, $depth);
        
        $default_item = $this->get_default_item($item);
       
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

	
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		if ($this->isProductMenu() && $depth > 0)
		{
			if ($depth == 1)
			{

				//Set the id of the parent for the menu-right javascript
				$this->menu_left_parent = $item->ID;
				$output = $this->comment("begin start_el", $depth, $output, $args);

				$output .= $indent . '<div' . $id . $class_names .'>';
				$output .= $indent . '<div class="left-block"></div>';
				$item_output = $args->before;
				$item_output .= '<a'. $attributes .'>';

				//Add the icon if it exists
				$icon = get_field('product-icon', $default_item);
      
				// append icon
				if( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}

				//Check for menu-item-pop

				if (in_array("menu-item-pop", $item->classes))
				{
					$image = get_field('product-image', $default_item);

					if ($image) {
						$item_output .= '<img src="'.$image.'" alt="'.$title.'">';
					}
				}

				$item_output .= $args->link_before . $title . $args->link_after;

				$item_output .= '</a>';
				if (in_array("menu-item-pop", $item->classes))
				{
					$description = get_field('product-description', $default_item);

					if ($description)
					{
						$item_output .= '<p>'.$description.'</p>';
					}
				}
				$item_output .= $args->after;

				
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
			else if ($depth == 2)
			{
				$this->output_append = $this->comment("begin start_el", $depth, $this->output_append, $args);
				$this->output_append .= $indent . '<div' . $id . $class_names .'>';

				$data_attributes = "";

				$image = get_field('product-image', $default_item);
				if ($image)
				{
					$data_attributes .= ' data-product-image="'.$image.'"';
				}

				$description = get_field('product-description', $default_item);

				if ($description)
				{
					$data_attributes .= ' data-product-description="'.$description.'"';
				}

				$item_output = $args->before;
				$item_output .= '<div class="item-container" '. $data_attributes .'>';
                
				$icon = get_field('product-icon', $default_item);
				if ( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}
				$item_output .= '<a'. $attributes .'><p>';
				$item_output .= $args->link_before . $title . $args->link_after;
				$item_output .= '</p></a>';
				$item_output .= "</div>";
				$item_output .= $args->after;

				
				$this->output_append .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
		}
		else if ($this->isSolutionsMenu() && $depth > 0)
		{
			if ($depth == 1)
			{
				//Set the id of the parent for the menu-right javascript
				$this->menu_left_parent = $item->ID;
				$output = $this->comment("begin start_el", $depth, $output, $args);

				$class = "";

				if (in_array("menu-spacer", $item->classes))
				{
					$class = " menu-spacer ";
				}

				if (in_array("menu-column", $item->classes))
				{	
                    $col_type = $this->getColType();
					//End the column and start a new one
					$output .= $indent . '</div>';
					$output .= "{$n}{$indent}<div class='".$col_type."'>{$n}";
				}

				$output .= $indent . '<div' . $id .' class="col-md-12 solutions-header '.$class.'">';

				$output .= $indent . '<div class="mason-container">';
				$item_output = $args->before;
				$item_output .= '<a'. $attributes .'>';

				//Add the icon if it exists
				$icon = get_field('product-icon', $default_item);
				// append icon
				if( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}

				$item_output .= $args->link_before . $title . $args->link_after;
				$item_output .= '</a>';
				$item_output .= $args->after;

				
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
			else if ($depth == 2)
			{
				$output = $this->comment("begin start_el", $depth, $output, $args);
				$output .= $indent . '<div' . $id . $class_names .'>';

				$data_attributes = "";

				$image = get_field('product-image', $default_item);
				if ($image)
				{
					$data_attributes .= ' data-product-image="'.$image.'"';
				}

				$description = get_field('product-description', $default_item);

				if ($description)
				{
					$data_attributes .= ' data-product-description="'.$description.'"';
				}

				$item_output = $args->before;
				$item_output .= '<div class="item-container" '. $data_attributes .'>';
				$icon = get_field('product-icon', $default_item);
				if ( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before . $title . $args->link_after;
				$item_output .= '</a>';
				$item_output .= "</div>";
				$item_output .= $args->after;

				
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
		}
		else if ($this->isPartnersMenu() && $depth > 0)
		{
			if ($depth == 1)
			{
				//Set the id of the parent for the menu-right javascript
				$this->menu_left_parent = $item->ID;
				$output = $this->comment("begin start_el", $depth, $output, $args);
                $col_type = $this->getColType();
				$output .= $indent . '<div' . $id .' class="'.$col_type.' partners-header">';

				$item_output = $args->before;
				$item_output .= '<a'. $attributes .'>';

				//Add the icon if it exists
				$icon = get_field('product-icon', $default_item);
				// append icon
				if( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}

				$item_output .= $args->link_before . '<span>'.$title.'</span>'. $args->link_after;
				$item_output .= '</a>';
				$item_output .= $args->after;

				$description = get_field('product-description', $default_item);

				if ($description)
				{
					$item_output .= "<p>{$description}</p>";
				}

				
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
			else if ($depth == 2)
			{
				$output = $this->comment("begin start_el", $depth, $output, $args);
				$output .= $indent . '<div' . $id . $class_names .'>';

				$data_attributes = "";

				$image = get_field('product-image', $default_utem);
				if ($image)
				{
					$data_attributes .= ' data-product-image="'.$image.'"';
				}

				$description = get_field('product-description', $default_item);

				if ($description)
				{
					$data_attributes .= ' data-product-description="'.$description.'"';
				}

				$item_output = $args->before;
				$item_output .= '<div class="item-container" '. $data_attributes .'>';
				$icon = get_field('product-icon', $default_tem);
				if ( $icon ) {
					$item_output .= '<i class="'.$icon.'"></i> ';
				}
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before . $title . $args->link_after;
				$item_output .= '</a>';
				$item_output .= "</div>";
				$item_output .= $args->after;

				
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
		}
		else
		{
			$output = $this->comment("begin start_el", $depth, $output, $args);
			$output .= $indent . '<li' . $id . $class_names .'>';

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . $title . $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		
	}


	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
        } else {
                $t = "\t";
                $n = "\n";
        }

        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        if ($this->isProductMenu())
        {
        	if ($depth == 1)
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
	        	$output .= "</div>{$n}";
	        	$output = $this->comment("end end_el", $depth, $output, $args);
	        }
	        else if ($depth == 2)
	        {
	        	$this->output_append = $this->comment("begin end_el", $depth, $this->output_append, $args);
				$this->output_append .= "</div>{$n}";
				$this->output_append = $this->comment("end end_el", $depth, $this->output_append, $args);
	        }
	        else
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
        		//Close the menu center container
        		$output .= "$indent</div>{$n}";
        		//Close the menu container
        		$output .= "$indent</div>{$n}";
        		$output = $this->comment("end end_el", $depth, $output, $args);
	        }
        }
        else if ($this->isSolutionsMenu())
        {
        	if ($depth == 1)
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
	        	$output .= "</div></div>{$n}";
	        	$output = $this->comment("end end_el", $depth, $output, $args);
	        }
	        else if ($depth == 2)
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
				$output .= "</div>{$n}";
				$output = $this->comment("end end_el", $depth, $output, $args);
	        }
	        else
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
        		//Close the menu container
        		$output .= "$indent</div>{$n}";
        		$output = $this->comment("end end_el", $depth, $output, $args);
	        }
        }
        else if ($this->isPartnersMenu())
        {
        	if ($depth == 1)
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
	        	$output .= "</div>{$n}";
	        	$output = $this->comment("end end_el", $depth, $output, $args);
	        }
	        else if ($depth == 2)
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
				$output .= "</div>{$n}";
				$output = $this->comment("end end_el", $depth, $output, $args);
	        }
	        else
	        {
	        	$output = $this->comment("begin end_el", $depth, $output, $args);
        		//Close the menu container
        		$output .= "$indent</div>{$n}";
        		$output = $this->comment("end end_el", $depth, $output, $args);
	        }
        }
        else
        {
        	$output = $this->comment("begin end_el", $depth, $output, $args);

        	$output .= "</li>{$n}";
        	$output = $this->comment("end end_el", $depth, $output, $args);
        }        
	}


}

add_action('init', 'register_script');
function register_script() {
    wp_register_style( 'menu_style', plugins_url('/mega-menu.css', __FILE__), false, '1.0.0', 'all');
    wp_register_script( 'menu_script', plugins_url('/mega-menu.js', __FILE__), array( 'jquery' ), '1.0.0');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');

function enqueue_style(){
   wp_enqueue_style( 'menu_style' );
   wp_enqueue_script( 'menu_script' );
}


add_filter( 'wp_nav_menu_args' , 'my_new_menu' );
function my_new_menu( $args ) {
$args['walker'] = new new_walker();
return $args;
}	
?>
