<?php

$main_categories = main_tech_docs_category();

// this is where we start to have some fun. Basically we need to use ajax to trigger a curl request to get a sub menu when the user selects a main item. Then, from that point, we also need to offer the option of Active versus Legacy, which I don't really understand yet.

?>


<div id="tech-docs-dropdowns">
	<div id="tech-docs-main-categories">	
		<select name="tech-docs-main-selector" id="td-main-selector">
			<option value="">Select A Product</option>
			<?php
				foreach ( $main_categories as $main_cat ) {
					echo '<option value="' . $main_cat->term_id . '">' . $main_cat->name . '</option>';
				}
			?>
		</select>
	</div>

</div>