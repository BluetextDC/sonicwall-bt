<?php	
/*
 *	The template for displaying event categoroes - event organizer 
 *
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 0.1
 */	
	
	global $eventon;

	get_header();

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

	$term = get_term_by( 'slug', $term, $tax );

	do_action('eventon_before_main_content');

	//$term_meta = get_option( "taxonomy_".$term->term_id );
	$term_meta = evo_get_term_meta( 'event_organizer',$term->term_id );

	// organizer image
		$img_url = false;
		if(!empty($term_meta['evo_org_img'])){
			$img_url = wp_get_attachment_image_src($term_meta['evo_org_img'],'full');
			$img_url = $img_url[0];
		}

		
	$organizer_link_a = (!empty($term_meta['evcal_org_exlink']))? 
		'<a target="_blank" href="'. evo_format_link($term_meta['evcal_org_exlink']) .'">': false;
	$organizer_link_b = ($organizer_link_a)? '</a>':false;
?>

<div class='wrap evotax_term_card evo_organizer_card'>
	
	<header class='page-header'>
		<h1 class="page-title"><?php evo_lang_e('Events by this organizer');?></h1>
	</header>

	<div id='primary' class="content-area">
		<div class='eventon site-main'>
			<div class="evo_location_tax evotax_term_details" style='background-image:url(<?php echo $img_url;?>)'>
				
				<?php if($img_url):?>
					<div class="location_circle term_image_circle" style='background-image:url(<?php echo $img_url;?>)'></div>
				<?php endif;?>

				<h2 class="organizer_name tax_term_name"><span><?php echo $organizer_link_a.$term->name.$organizer_link_b;?></span></h2>
				<div class='organizer_description tax_term_description'><?php echo category_description();?>
					<?php if(!empty($term_meta['evcal_org_contact'])):?>
						<p class='contactinfo'><?php echo $term_meta['evcal_org_contact'];?></p>
					<?php endif;?>
					<?php
						echo (!empty($term_meta['evcal_org_address']))? '<p>'.$term_meta['evcal_org_address'].'</p>':null; 
					?>
				</div>				
			</div>	

			<?php if( !empty($term_meta['evcal_org_address']) ):?>
				<div id='evo_locationcard_gmap' class="evo_location_map term_location_map" data-address='<?php echo stripslashes($term_meta['evcal_org_address']);?>' data-latlng='' data-location_type='add'data-zoom='16'></div>
			<?php endif;?>		
			
			<h3 class="evotax_term_subtitle organizer_subtitle"><?php evo_lang_e('Events by this organizer');?></h3>
		
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