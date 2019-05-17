<?php 
$error_page = "
<html>
<head>
<title>Page Not Found</title>
<style>
img {
padding: 50px;
}
h1, h2, p {
font-family: sans-serif;
}

</style>
</head>
<body>
<center>
<a href='https://sonicwall.com'><img src='https://d3ik27cqx8s5ub.cloudfront.net/media/uploads/2018/04/Logo.svg'></a>
<h1>404: Page Not Found</h1>
<h2>We Can't Find That Page</h2>
<p>The Site has returned a 404 error.</p>
</center>
</body>
</html>
";
die($error_page);
	if ( !defined('ABSPATH') ){ die(); }
	
	global $avia_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();


	 echo avia_title(array('title' => __('Error 404 - page not found', 'avia_framework')));
	 
	 do_action( 'ava_after_main_title' );
	?>


		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>
			
			<?php 
				do_action('avia_404_extra'); // allows user to hook into 404 page fr extra functionallity. eg: send mail that page is missing, output additional information
			?>
			
			<div class='container'>

				<main class='template-page content <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content'));?>>


                    <div class="entry entry-content-wrapper clearfix" id='search-fail'>
                    <?php

                    get_template_part('includes/error404');

                    ?>
                    </div>

				<!--end content-->
				</main>

				<?php

				//get the sidebar
				$avia_config['currently_viewing'] = 'page';
				//get_sidebar();

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->




<?php get_footer(); ?>
