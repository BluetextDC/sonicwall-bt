<?php
/**
 * Form add/edit of conversion
 *
 * @var AEPC_Admin_View $page
 * @var ProductCatalogManager $product_catalog
 * @var string $action
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

switch ( true ) {

	case ! AEPC_Admin::$api->is_logged_in() :
		$fberror = sprintf( __( 'You have to do the %sFacebook Connect%s to enable this option', 'pixel-caffeine' ), '<a href="' . AEPC_Admin::get_page('general-settings')->get_view_url() . '">', '</a>' );
		break;

	case AEPC_Admin::$api->is_expired() :
		$fberror = sprintf( __( 'Your facebook login is expired. Please, do again the %sFacebook Connect%s to enable this option', 'pixel-caffeine' ), '<a href="' . AEPC_Admin::get_page('general-settings')->get_view_url() . '">', '</a>' );
		break;

	case ! AEPC_Admin::$api->get_business_id() :
		$fberror = sprintf( __( 'You need a Business Account associated to the Ad Account selected during login. Please, use a different Ad Account in %sGeneral settings%s page', 'pixel-caffeine' ), '<a href="' . AEPC_Admin::get_page('general-settings')->get_view_url() . '">', '</a>' );
		break;

	default:
		$fberror = '';
		break;

}

// Retrieve the selected addon intersected to the detected one, in order to show the field if now addon selected are activated
$addons = (array) AEPC_Addons_Support::get_detected_addons();
$selected_addons = (array) $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SELECTED_ADDON );
foreach ( $selected_addons as $k => $sa ) {
	foreach ( $addons as $a ) {
		if ( $a->get_slug() === $sa ) {
			continue 2;
		}
	}

	unset( $selected_addons[ $k ] );
}

?>

<input
	type="hidden"
	name="<?php $page->feed_field_name( Configuration::OPTION_FILE_NAME ) ?>"
	id="<?php $page->feed_field_id( Configuration::OPTION_FILE_NAME ) ?>"
	value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FILE_NAME ) ?>"
/>

<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_GOOGLE_CATEGORY ) ?>" class="form-group is-empty">
	<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_GOOGLE_CATEGORY ) ?>" class="control-label">
		<?php _e( 'Default Google Category', 'pixel-caffeine' ) ?>
	</label>
	<div class="control-wrap">
		<div class="js-categories-wrapper">
			<?php try { ?>
				<?php foreach ( $page->get_google_categories_dropdown_lists( $product_catalog ) as $level => $terms ) : ?>
				<input
					type="hidden"
					class="form-control js-google-category"
					name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_GOOGLE_CATEGORY ) ?>[]"
					data-level="<?php echo esc_attr( $level ) ?>"
					data-options="<?php echo esc_attr( json_encode( array_keys( $terms ) ) ) ?>"
					value="<?php echo esc_attr( array_search( true, $terms ) ) ?>"
				/>
				<?php endforeach; ?>
			<?php } catch ( \Exception $e ) {
				$message = sprintf( __( 'Google Categories cannot be retrieved: %s', 'pixel-caffeine' ), $e->getMessage() );
				$page->print_notice( 'error', $message );
			} ?>
		</div>
		<div class="field-helper">
			<small class="text">
				<?php _e( 'A Google Category is mandatory for each item in your Facebook feed. Please select a master category to proceed. You can also select sub-categories to be more specific.', 'pixel-caffeine' ) ?><br/>
			</small>
		</div>
	</div>
</div>

<!--FACEBOOK UPLOAD auto-fb-upload -->
<div class="multi-form-group">

	<!--Switch | FACEBOOK UPLOAD-->
	<div class="form-group form-toggle<?php if ( ! empty( $fberror ) ) echo ' toggle-sub-panel highlight-element warning' ?>">
		<label for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ENABLE ) ?>" class="control-label">Automatic Facebook upload</label>
		<div class="control-wrap">
			<div class="togglebutton">
				<label>
					<input
						type="checkbox"
						id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ENABLE ) ?>"
						name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ENABLE ) ?>"
						value="yes"
						<?php checked( $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ENABLE ) ) ?>
						<?php disabled( ! empty( $fberror ) ) ?>
						data-toggle="collapse"
						data-target="#automatic-facebook-options"
						aria-expanded="false"
						aria-controls="automatic-facebook-options"
					/>
				</label>
			</div>
			<?php if ( ! empty( $fberror ) ) : ?>
			<span class="alert alert-lite alert-warning"><?php echo $fberror ?></span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Show only if togglebutton is checked -->
	<div id="automatic-facebook-options" class="sub-panel sub-panel-box auto-fb-upload collapse<?php echo $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ENABLE ) ? ' in' : '' ?>">
		<div id="fb-catalog-settings" class="sub-form-group" role="tablist" aria-multiselectable="true">
			<h2 class="tit"><?php _e( 'Automatic Facebook upload', 'pixel-caffeine' ) ?></h2>

			<?php $schedule_action = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ACTION ) ?>

			<!--Radio Selector -->
			<div class="fb-option-catalog form-horizontal-inline">
				<div class="form-group form-radio">
					<div class="control-wrap">

						<!--Radio Selector | Create New FB Catalog -->
						<div class="radio">
							<label>
								<input
									type="radio"
									id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ACTION ) ?>"
									name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ACTION ) ?>"
									value="<?php echo esc_attr( Configuration::VALUE_FB_ACTION_NEW ) ?>"
									<?php checked( $schedule_action, Configuration::VALUE_FB_ACTION_NEW ) ?>
									class="js-catalog-option"
									data-target="#fb-create-catalog"
									aria-expanded="false"
									aria-controls="fb-create-catalog"
								/>
								<?php _e( 'Create New Facebook Catalog', 'pixel-caffeine' ) ?>
							</label>
						</div>

						<!--Existing New FB Catalog -->
						<div class="radio">
							<label>
								<input
									type="radio"
									id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ACTION ) ?>"
									name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_ACTION ) ?>"
									value="<?php echo esc_attr( Configuration::VALUE_FB_ACTION_UPDATE ) ?>"
									<?php checked( $schedule_action, Configuration::VALUE_FB_ACTION_UPDATE ) ?>
									class="js-catalog-option"
									data-target="#fb-update-catalog"
									aria-expanded="false"
									aria-controls="fb-update-catalog"
								/>
								<?php _e( 'Use existing Facebook Catalogs', 'pixel-caffeine' ) ?>
							</label>
						</div>

					</div>
				</div>
			</div>

			<!--Panel | Create New FB Catalog -->
			<div id="fb-create-catalog" class="panel create-new-catalog form-horizontal-inline<?php echo $schedule_action !== Configuration::VALUE_FB_ACTION_NEW ? ' hide' : '' ?>" role="tab">
				<div class="panel-body">
					<div class="form-group new-fb-catalog">
						<label class="control-label" for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>">
							<?php _e( 'Name', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_NEW, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_NEW, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
							/>
						</div>
					</div>

					<?php $page->get_form_fields( 'sub/schedule', array(
						'product_catalog' => $product_catalog,
						'group' => Configuration::VALUE_FB_ACTION_NEW
					) ) ?>
				</div>
			</div>

			<!--Panel Selector | Existing New FB Catalog-->
			<div id="fb-update-catalog" class="panel existing-catalog form-horizontal-inline<?php echo $schedule_action !== Configuration::VALUE_FB_ACTION_UPDATE ? ' hide' : '' ?>" role="tab">
				<div class="panel-body">
					<div class="form-group">
						<label for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_ID ) ?>" class="control-label">
							<?php _e( 'Select Catalog', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<select
								class="form-control js-product-catalogs"
								placeholder="<?php _e( 'Select a product catalog', 'pixel-caffeine' ) ?>"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_CATALOG_ID ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_CATALOG_ID ) ?>"
							>
								<option></option>

								<?php if ( $product_catalog_id = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_ID ) ) : ?>
								<option
									<?php selected(true) ?>
									value="<?php echo esc_attr( $product_catalog_id ) ?>"
									data-name="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
								>
									<?php
									printf(
										'%s (#%s)',
										$page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ),
										$page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_ID )
									);
									?>
								</option>
								<?php endif; ?>
							</select>
							<input
								type="hidden"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_CATALOG_NAME ) ?>"
							/>
						</div>
					</div>

					<div class="form-group">
						<label for="event_trigger_on" class="control-label"><?php _e( 'Select Feed', 'pixel-caffeine' ) ?></label>
						<div class="control-wrap">
							<select
								class="form-control js-product-feeds"
								placeholder="<?php _e( 'Select a product feed or create a new one', 'pixel-caffeine' ) ?>"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_FEED_ID ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_FEED_ID ) ?>"
								<?php disabled( true ) ?>
							>
								<option></option>
								<option value="new" class="select2-add"><?php _e( 'Create new feed', 'pixel-caffeine' ) ?></option>

								<?php if ( $product_feed_id = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_ID ) ) : ?>
								<option
									<?php selected(true) ?>
									value="<?php echo esc_attr( $product_feed_id ) ?>"
									data-name="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_NAME ) ?>"
								>
									<?php
									printf(
										'%s (#%s)',
										$page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_NAME ),
										$page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_ID )
									);
									?>
								</option>
								<?php endif; ?>
							</select>
							<input
								type="hidden"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_FEED_NAME ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::VALUE_FB_ACTION_UPDATE, Configuration::OPTION_FB_PRODUCT_FEED_NAME ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_NAME ) ?>"
							/>
						</div>
					</div>

					<?php $page->get_form_fields( 'sub/schedule', array(
						'product_catalog' => $product_catalog,
						'group' => Configuration::VALUE_FB_ACTION_UPDATE,
						'product_feed_id' => $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_ID )
					) ) ?>
				</div>
			</div>

		</div><!--/sub-form-group -->
	</div>

</div>

<div class="multi-form-group">

	<div class="form-group form-toggle">
		<label for="product_feed_advanced_options" class="control-label">
			<?php _e( 'Advanced Options', 'pixel-caffeine' ) ?>
		</label>
		<div class="control-wrap">
			<div class="togglebutton">
				<label>
					<input
						type="checkbox"
						id="product_feed_advanced_options"
						class="js-show-advanced-data"
						<?php checked( $product_catalog && $product_catalog->configuration()->defaults_changed() ) ?>
					>
				</label>
			</div>
		</div>
	</div>

	<div class="sub-panel sub-panel-box advanced-options form-vertical advanced-data collapse">
		<div class="sub-form-group">
			<h2 class="tit"><?php _e( 'Advanced options', 'pixel-caffeine' ) ?></h2>

			<div class="panel">
				<div class="panel-body">
					<h2 class="sub-tit"><?php _e( 'Configuration', 'pixel-caffeine' ) ?></h2>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_IMAGE_SIZE ) ?>" class="form-group is-empty">

						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_IMAGE_SIZE ) ?>" class="control-label">
							<?php _e( 'Image Size', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<select
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_IMAGE_SIZE ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_IMAGE_SIZE ) ?>"
							>
								<?php foreach ( $page->get_image_size_options() as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ) ?>"<?php selected( $k, $page->get_feed_field_value( $product_catalog, 'config', Configuration::OPTION_IMAGE_SIZE ) ) ?>>
										<?php echo esc_html( $v ) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="field-helper">
								<small class="text">
									<?php printf( __( 'Set the size of the image for each product in the feed, between the available image size. To use a custom size, you need to create a custom size through %sthis external plugin%s.', 'pixel-caffeine' ), '<a href="https://wordpress.org/plugins/simple-image-sizes/" target="_blank">', '</a>' ) ?>
								</small>
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_ENABLE_BACKGROUND_SAVE ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_ENABLE_BACKGROUND_SAVE ) ?>" class="control-label">
							<?php _e( 'Save in background', 'pixel-caffeine' ) ?>
							<a
								href="#_"
								class="btn btn-fab btn-help btn-fab-mini"
								data-toggle="tooltip"
								data-placement="top"
								title="<?php _e( 'Enable this when you have many products in your store and your server is not able to run the saving process successfully. Note: this mode might not be working successfully according to your server configuration.', 'pixel-caffeine' ) ?>"></a>
						</label>
						<div class="control-wrap">
							<div class="togglebutton">
								<label>
									<input
										type="checkbox"
										name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_ENABLE_BACKGROUND_SAVE ) ?>"
										id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_ENABLE_BACKGROUND_SAVE ) ?>"
										value="yes"
										<?php checked( $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_ENABLE_BACKGROUND_SAVE ) ) ?>
									>
								</label>
							</div>

							<span class="text-status">
								<small class="text">
									<?php _e( 'Save the feed file in a background process.', 'pixel-caffeine' ) ?><br/>
								</small>
							</span>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SKU_FOR_ID ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SKU_FOR_ID ) ?>" class="control-label">
							<?php _e( 'Use SKU', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<div class="togglebutton">
								<label>
									<input
										type="checkbox"
										name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SKU_FOR_ID ) ?>"
										id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SKU_FOR_ID ) ?>"
										value="yes"
										<?php checked( $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SKU_FOR_ID ) ) ?>
									>
								</label>
							</div>

							<span class="text-status"><small class="text"><?php _e( 'Use SKU as ID in the product feed. If no sku, it gets the ID for default.', 'pixel-caffeine' ) ?></small></span>
						</div>
					</div>

				</div>
			</div>

			<div class="panel">
				<div class="panel-body">
					<h2 class="sub-tit">Product Filters</h2>
					<?php if ( count( $selected_addons ) !== 1 || count( $addons ) > 1 ) : ?>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SELECTED_ADDON ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SELECTED_ADDON ) ?>" class="control-label">
							<?php _e( 'Filter by plugin', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control multi-tags"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SELECTED_ADDON ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_SELECTED_ADDON ) ?>"
								value="<?php echo esc_attr( implode( ',', array_values( $selected_addons ) ) ) ?>"
								data-tags="<?php echo esc_attr( wp_json_encode( $page->get_addons_detected_select2() ) ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>
					<?php else : ?>
						<input
							type="hidden"
							name="<?php $page->feed_field_name( Configuration::OPTION_SELECTED_ADDON ) ?>"
							value="<?php echo esc_attr( implode( ',', array_values( $selected_addons ) ) ) ?>"
						/>
					<?php endif; ?>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TYPE ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TYPE ) ?>" class="control-label">
							<?php _e( 'Filter by product type', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control multi-tags js-product-types"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TYPE ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TYPE ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TYPE ) ?>"
								data-tags="<?php echo esc_attr( json_encode( $page->get_product_types_array() ) ) ?>"
							/>
							<div class="field-helper">
								<small class="text"><?php _e( 'Select if you want only product with certain type to include in the feed. Leave empty to not use this filter', 'pixel-caffeine' ) ?></small>
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_CATEGORY ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_CATEGORY ) ?>" class="control-label">
							<?php _e( 'Filter by product category', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control multi-tags js-product-categories"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_CATEGORY ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_CATEGORY ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_CATEGORY ) ?>"
								data-tags="<?php echo esc_attr( json_encode( $page->get_product_categories_array() ) ) ?>"
							/>
							<div class="field-helper">
								<small class="text"><?php _e( 'Select the categories of products to include in the feed. Leave empty to not use this filter', 'pixel-caffeine' ) ?></small>
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TAG ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TAG ) ?>" class="control-label">
							<?php _e( 'Filter by product tag', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control multi-tags js-product-tags"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TAG ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TAG ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_TAG ) ?>"
								data-tags="<?php echo esc_attr( json_encode( $page->get_product_tags_array() ) ) ?>"
							/>
							<div class="field-helper">
								<small class="text"><?php _e( 'Select the tags of products to include in the feed. Leave empty to not use this filter', 'pixel-caffeine' ) ?></small>
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_STOCK ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_STOCK ) ?>" class="control-label">
							<?php _e( 'Filter by product stock', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control multi-tags"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_STOCK ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_STOCK ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_BY_STOCK ) ?>"
								data-tags="<?php echo esc_attr( json_encode( array(
									array( 'id' => AEPC_Addon_Product_Item::IN_STOCK, 'text' => __( 'In Stock', 'pixel-caffeine' ) ),
									array( 'id' => AEPC_Addon_Product_Item::OUT_OF_STOCK, 'text' => __( 'Out of Stock', 'pixel-caffeine' ) ),
									array( 'id' => AEPC_Addon_Product_Item::PREORDER, 'text' => __( 'Pre-Order', 'pixel-caffeine' ) ),
									array( 'id' => AEPC_Addon_Product_Item::DISCONTINUED, 'text' => __( 'Discountinued', 'pixel-caffeine' ) ),
									array( 'id' => AEPC_Addon_Product_Item::AVAILABLE_FOR_ORDER, 'text' => __( 'Available for order', 'pixel-caffeine' ) ),
								) ) ) ?>"
							/>
							<div class="field-helper">
								<small class="text"><?php _e( 'Include what products to include in base of stock status. Leave empty to include all products of all stock status.', 'pixel-caffeine' ) ?></small>
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_ON_SALE ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_ON_SALE ) ?>" class="control-label">
							<?php _e( 'Filter products on sale', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<div class="togglebutton">
								<label>
									<input
										type="checkbox"
										name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_ON_SALE ) ?>"
										id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_ON_SALE ) ?>"
										value="yes"
										<?php checked( $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FILTER_ON_SALE ) ) ?>
									>
								</label>
							</div>
							<span class="text-status">
								<small class="text"><?php _e( 'Check this option if you want <strong>only</strong> products on sale.', 'pixel-caffeine' ) ?></small>
							</span>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_NO_VARIATIONS ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_NO_VARIATIONS ) ?>" class="control-label">
							<?php _e( 'No Variations', 'pixel-caffeine' ) ?>
						</label>
						<div class="control-wrap">
							<div class="togglebutton">
								<label>
									<input
										type="checkbox"
										name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_NO_VARIATIONS ) ?>"
										id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_NO_VARIATIONS ) ?>"
										value="yes"
										<?php checked( $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_NO_VARIATIONS ) ) ?>
									>
								</label>
							</div>

							<span class="text-status"><small class="text"><?php _e( 'Remove the variations from the feed.', 'pixel-caffeine' ) ?></small></span>
						</div>
					</div>

				</div>
			</div>

			<div class="panel">
				<div class="panel-body">
					<h2 class="sub-tit"><?php _e( 'Special mapping', 'pixel-caffeine' ) ?></h2>

					<p><?php echo sprintf( __( 'Here you can map some of the fields you can define inside the feed. Here a reference of a full field list: %s', 'pixel-caffeine' ), '<a href="https://developers.facebook.com/docs/marketing-api/reference/product-catalog/products/#Creating" target="_blank">https://developers.facebook.com/docs/marketing-api/reference/product-catalog/products/#Creating</a>' ) ?></p>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_BRAND ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_BRAND ) ?>" class="control-label">
							brand
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_BRAND ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_BRAND ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_BRAND ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CONDITION ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CONDITION ) ?>" class="control-label">
							condition
						</label>
						<div class="control-wrap">
							<select
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CONDITION ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CONDITION ) ?>"
								>
								<?php foreach ( $page->get_feed_condition_options() as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ) ?>" ><?php echo esc_html( $v ) ?></option>
								<?php endforeach; ?>
							</select>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_DESCRIPTION ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_DESCRIPTION ) ?>" class="control-label">
							description
						</label>
						<div class="control-wrap">
							<select
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_DESCRIPTION ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_DESCRIPTION ) ?>"
							>
								<?php foreach ( $page->get_feed_description_options() as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ) ?>"<?php selected($k, $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_DESCRIPTION )) ?>><?php echo esc_html( $v ) ?></option>
								<?php endforeach; ?>
							</select>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_PRICE ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_PRICE ) ?>" class="control-label">
							price
						</label>
						<div class="control-wrap">
							<select
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_PRICE ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_PRICE ) ?>"
							>
								<?php foreach ( $page->get_feed_price_options() as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ) ?>"<?php selected($k, $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_PRICE )) ?>><?php echo esc_html( $v ) ?></option>
								<?php endforeach; ?>
							</select>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_0 ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_0 ) ?>" class="control-label">
							custom_label_0
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_0 ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_0 ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_0 ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_1 ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_1 ) ?>" class="control-label">
							custom_label_1
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_1 ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_1 ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_1 ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_2 ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_2 ) ?>" class="control-label">
							custom_label_2
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_2 ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_2 ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_2 ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_3 ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_3 ) ?>" class="control-label">
							custom_label_3
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_3 ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_3 ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_3 ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

					<div id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_4 ) ?>" class="form-group is-empty">
						<label for="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_4 ) ?>" class="control-label">
							custom_label_4
						</label>
						<div class="control-wrap">
							<input
								type="text"
								class="form-control"
								name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_4 ) ?>"
								id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_4 ) ?>"
								value="<?php $page->feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_MAP_CUSTOM_LABEL_4 ) ?>"
							/>
							<div class="field-helper">
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>

</div>

<div class="panel-footer">
	<?php wp_nonce_field( $action . '_product_catalog' ) ?>
	<input type="hidden" name="action" value="aepc_<?php echo esc_attr( $action ) ?>_product_catalog"/>
	<button class="btn btn-raised btn-success btn-save btn-plugin"><?php _e( 'Generate Feed File', 'pixel-caffeine' ) ?></button>
</div>
