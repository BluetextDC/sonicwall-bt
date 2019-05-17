<?php	
/*
 *	The template for displaying event categoroes 
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


	$tax_name = $eventon->frontend->get_localized_event_tax_names_by_slug($tax);

	do_action('eventon_before_main_content');
?>

<div class='wrap evotax_term_card evotax_term_card container'>
	<header class="page-header ">
		<h1 class="page-title"><?php echo $tax_name.': '.single_cat_title( '', false ); ?></h1>
		<?php if ( category_description() ) : // Show an optional category description ?>
		<div class="page-meta"><?php echo category_description(); ?></div>
		<?php endif; ?>
	</header><!-- .archive-header -->
	
	<div id='primary' class='content-area'>
		<div class='<?php echo apply_filters('evotax_template_content_class', 'eventon site-main');?>'>
		
			<div class="evotax_term_details endborder_curves" >						
				
				<h2 class="tax_term_name">
					<i><?php echo $tax_name;?></i>
					<span><?php echo $term->name;?></span>
				</h2>
				<div class='tax_term_description'><?php echo category_description();?></div>
			</div>

		
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