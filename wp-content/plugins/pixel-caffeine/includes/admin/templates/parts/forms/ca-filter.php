<?php
/**
 * Form add/edit of conversion
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$radios = array(

	'attributes' => array(

		array(
			'id' => 'language',
			'label' => __( 'Language', 'pixel-caffeine' ),
			'target' => '.collapse-language'
		),

		array(
			'id' => 'referrer',
			'label' => __( 'Referrer', 'pixel-caffeine' ),
			'target' => '.collapse-multi-tags'
		),

		array(
			'id' => 'login_status',
			'label' => __( 'Login status', 'pixel-caffeine' ),
			'target' => '.collapse-login-status'
		),

		array(
			'id' => 'device_type',
			'label' => __( 'Device type', 'pixel-caffeine' ),
			'target' => '.collapse-device-type'
		),

	),

	'blog' => array(

		array(
			'id' => 'categories',
			'label' => __( 'Categories', 'pixel-caffeine' ),
			'target' => '.collapse-categories'
		),

		array(
			'id' => 'tax_post_tag',
			'label' => __( 'Tags', 'pixel-caffeine' ),
			'target' => '.collapse-tags'
		),

		array(
			'id' => 'posts',
			'label' => __( 'Posts', 'pixel-caffeine' ),
			'target' => '.collapse-posts'
		),

		array(
			'id' => 'pages',
			'label' => __( 'Pages', 'pixel-caffeine' ),
			'target' => '.collapse-pages'
		),

		array(
			'id' => 'custom_fields',
			'label' => __( 'Custom field', 'pixel-caffeine' ),
			'target' => '.collapse-custom-fields'
		),

	),

	'ecommerce' => array(

		array(
			'id' => 'ViewContent',
			'label' => __( 'ViewContent', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'Search',
			'label' => __( 'Search', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'AddToCart',
			'label' => __( 'AddToCart', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'AddToWishlist',
			'label' => __( 'AddToWishlist', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'InitiateCheckout',
			'label' => __( 'InitiateCheckout', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'AddPaymentInfo',
			'label' => __( 'AddPaymentInfo', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'Purchase',
			'label' => __( 'Purchase', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'Lead',
			'label' => __( 'Lead', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

		array(
			'id' => 'CompleteRegistration',
			'label' => __( 'CompleteRegistration', 'pixel-caffeine' ),
			'target' => '.collapse-dpa'
		),

	),

);

?>

<div class="btn-group btn-group-justified btn-group-toggle btn-group-lg js-main-condition" data-toggle="buttons">
	<label for="main_condition_include" class="btn btn-raised btn-include js-condition js-include active">
		<input type="radio" name="ca_rule[][main_condition]" id="main_condition_include" value="include" autocomplete="off" checked> <?php _e( 'Include', 'pixel-caffeine' ) ?>
	</label>
	<label for="main_condition_exclude" class="btn btn-raised btn-exclude js-condition js-exclude">
		<input type="radio" name="ca_rule[][main_condition]" id="main_condition_exclude" value="exclude" autocomplete="off"> <?php _e( 'Exclude', 'pixel-caffeine' ) ?>
	</label>
</div>
<div class="form-group form-user">
	<label for="" class="control-label">Users based on</label>
	<div class="control-wrap">
		<select class="form-control js-collapse" data-parent=".js-collapse-events" name="ca_rule[][event_type]" id="ca_event_type">
			<option value="attributes" data-target=".js-attributes" selected="selected"><?php _e( 'Attributes', 'pixel-caffeine' ) ?></option>
			<option value="blog" data-target=".js-blog"><?php _e( 'Blog Behaviour', 'pixel-caffeine' ) ?></option>
			<option value="ecommerce" data-target=".js-ecommerce"><?php _e( 'Ecommerce behaviour', 'pixel-caffeine' ) ?></option>
		</select>
	</div>
</div>
<!-- ./form-group -->

<div class="js-collapse-events">

	<?php foreach ( $radios as $event_type => $events ) : ?>
		<div class="collapse js-<?php echo esc_attr( $event_type ) ?>">
			<div class="form-group form-radio">
				<div class="control-wrap">

					<?php foreach ( $events as $radio ) : ?>
						<div class="radio">
							<label>
								<input
									type="radio"
									name="ca_rule[][event]"
									id="event_<?php echo esc_attr( $radio['id'] ) ?>"
									value="<?php echo esc_attr( $radio['id'] ) ?>"
									class="js-collapse"
									data-parent=".collapse-parameters"
									data-target="<?php echo esc_attr( $radio['target'] ) ?>"
									<?php checked ( ! empty( $radio['checked'] ) ) ?>>
								<span class="circle"></span><span class="check"></span> <?php echo $radio['label'] ?>
							</label>
						</div>
					<?php endforeach; ?>

				</div>
			</div>
		</div>
	<?php endforeach; ?>

</div>
<!-- ./form-group -->

<div class="collapse-parameters">

	<div class="collapse collapse-multi-tags">
		<div class="multiple-fields">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]" id="conditions_0_operator">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" placeholder="">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-language">
		<div class="multiple-fields">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]" id="conditions_0_operator">
						<?php $page->ca_operators_list( array( 'eq', 'neq' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" id="conditions_language" placeholder="<?php esc_attr_e( 'Language format allowed: en-US, it-IT, etc...', 'pixel-caffeine' ) ?> ">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-login-status">
		<div class="multiple-fields">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]" id="conditions_0_operator">
						<?php $page->ca_operators_list( array( 'eq', 'neq' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][value]" id="conditions_0_value">
						<option value="logged_in"><?php _e( 'Logged in', 'pixel-caffeine' ) ?></option>
						<option value="not_logged_in"><?php _e( 'Not logged in', 'pixel-caffeine' ) ?></option>
					</select>
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-device-type">
		<div class="multiple-fields">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'eq', 'neq' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" id="conditions_device_types" placeholder="">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-categories">
		<div class="multiple-fields multiple-three">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" id="taxonomy_key" name="ca_rule[][conditions][0][key]">
						<?php $page->taxonomies_dropdown() ?>
					</select>
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" id="taxonomy_terms" placeholder="<?php _e( 'Categories', 'pixel-caffeine' ) ?>">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-tags">
		<div class="multiple-fields multiple-three">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" id="tag_key" name="ca_rule[][conditions][0][key]">
						<?php $page->tags_dropdown() ?>
					</select>
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" id="tag_terms" placeholder="<?php _e( 'Tags', 'pixel-caffeine' ) ?>">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-posts">
		<div class="multiple-fields multiple-three">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" id="pt_key" name="ca_rule[][conditions][0][key]">
						<?php $page->post_types_dropdown() ?>
					</select>
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags"  name="ca_rule[][conditions][0][value]"id="pt_posts" placeholder="<?php _e( 'Posts', 'pixel-caffeine' ) ?>">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-pages">
		<div class="multiple-fields">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" id="pages" placeholder="<?php _e( 'Pages', 'pixel-caffeine' ) ?>">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-custom-fields">
		<div class="multiple-fields multiple-three">
			<div class="form-group">
				<div class="control-wrap">
					<select class="form-control" id="custom_field_keys" name="ca_rule[][conditions][0][key]"></select>
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]">
						<?php $page->ca_operators_list( array( 'i_contains', 'i_not_contains' ) ) ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" placeholder="<?php esc_attr_e( 'Custom field value', 'pixel-caffeine' ) ?>">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->

	<div class="collapse collapse-dpa">
		<div class="multiple-fields multiple-three">
			<div class="form-group">
				<div class="control-wrap">
					<input type="text" id="dpa_key" class="form-control" name="ca_rule[][conditions][0][key]">
				</div>
				<div class="control-wrap">
					<select class="form-control" name="ca_rule[][conditions][0][operator]" id="conditions_0_operator">
						<?php $page->ca_operators_list() ?>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" id="dpa_value" class="form-control multi-tags" name="ca_rule[][conditions][0][value]" placeholder="">
				</div>
			</div><!-- ./form-group -->
		</div><!-- ./multiple-fields -->
	</div><!-- ./collapse -->
</div>
