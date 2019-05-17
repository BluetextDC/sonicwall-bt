<?php	
/*
 *	The template for displaying event categoroes - event location 
 * 	In order to customize this archive page template
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 2.4.7
 */	
	
	global $eventon;

	get_header();

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

	$term = get_term_by( 'slug', $term, $tax );

	do_action('eventon_before_main_content');

	$term_meta = evo_get_term_meta( 'event_location',$term->term_id );
	//$term_meta = get_option( "taxonomy_".$term->term_id );

	// location image
		$img_url = false;
		if(!empty($term_meta['evo_loc_img'])){
			$img_url = wp_get_attachment_image_src($term_meta['evo_loc_img'],'full');
			$img_url = $img_url[0];
		}

	//location address
		$location_address = $location_latlan = false;
		$location_type = 'add';

			$location_latlan = (!empty($term_meta['location_lat']) && $term_meta['location_lon'])?
				$term_meta['location_lat'].','.$term_meta['location_lon']:false;

		if(empty($term_meta['location_address'])){
			if($location_latlan){
				$location_type ='latlng';
				$location_address = true;
			}
		}else{
			$location_address = stripslashes($term_meta['location_address']);
		}
		
		
?>

<div class='wrap evotax_term_card evo_location_card'>
	
	<header class='page-header'>
		<h1 class="page-title"><?php evo_lang_e('Events at this location');?></h1>
	</header>
	
	<div id='primary' class="content-area">
		<div class='eventon site-main'>
			<div class="evo_location_tax evotax_term_details" style='background-image:url(<?php echo $img_url;?>)'>
				
				<?php if($img_url):?>
					<div class="location_circle term_image_circle" style='background-image:url(<?php echo $img_url;?>)'></div>
				<?php endif;?>
				
				<h2 class="location_name tax_term_name"><span><?php echo $term->name;?></span></h2>
				<?php if($location_type=='add'):?><p class="location_address"><span><i class='fa fa-map-marker'></i> <?php echo $location_address;?></span></p><?php endif;?>
				<div class='location_description tax_term_description'><?php echo category_description();?></div>
			</div>
			
			<?php if($location_address):?>
				<div id='evo_locationcard_gmap' class="evo_location_map" data-address='<?php echo $location_address;?>' data-latlng='<?php echo $location_latlan;?>' data-location_type='<?php echo $location_type;?>'data-zoom='16'></div>
			<?php endif;?>

			<h3 class="location_subtitle evotax_term_subtitle"><?php evo_lang_e('Events at this location');?></h3>
		
		<?php 
			$shortcode = apply_filters('evo_tax_archieve_page_shortcode', 
				'[add_eventon_list number_of_months="5" '.$tax.'='.$term->term_id.' hide_mult_occur="no" hide_empty_months="yes"]', 
				$tax,
				$term->term_id
			);
			echo do_shortcode($shortcode);
		?>
		</div>
	</div>

	<?php get_sidebar(); ?>

</div>

<?php	do_action('eventon_after_main_content'); ?>

<?php get_footer(); ?>