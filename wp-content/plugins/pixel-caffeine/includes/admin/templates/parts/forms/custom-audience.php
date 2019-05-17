<?php
/**
 * Form add/edit custom audience
 *
 * @var AEPC_Admin_View $page
 * @var string $action
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div id="ca_name_field" class="form-group<?php $page->field_class( 'ca_name' ) ?>">
	<label for="ca_name" class="control-label"><?php _e( 'Name', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<input
			type="text"
			class="form-control"
			name="ca_name"
			id="ca_name"
			placeholder="<?php _e( 'Name of your new custom audience', 'pixel-caffeine' ) ?>"
			value="<?php echo 'edit' == $action ? '{{{ data.name }}}' : $page->get_field_value( 'ca_name', '' ) ?>">

		<div class="field-helper">
			<?php $page->print_field_error( 'ca_name', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>
<div id="ca_description_field" class="form-group<?php $page->field_class( 'ca_description' ) ?>">
	<label for="ca_description" class="control-label"><?php _e( 'Description', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<input
			type="text"
			class="form-control"
			name="ca_description"
			id="ca_description"
			placeholder="<?php _e( 'Description of your new custom audience', 'pixel-caffeine' ) ?>"
			value="<?php echo 'edit' == $action ? '{{{ data.description }}}' : $page->get_field_value( 'ca_description', '' ) ?>">

		<div class="field-helper">
			<?php $page->print_field_error( 'ca_description', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>

<?php if ( 'edit' != $action ) : ?>
<div id="ca_prefill_field" class="form-group form-toggle<?php $page->field_class( 'ca_prefill' ) ?>">
	<label for="ca_prefill" class="control-label"><?php _e( 'Pre-fill data', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<div class="togglebutton">
			<label>
				<input
					type="checkbox"
					name="ca_prefill"
					id="ca_prefill"
					class="js-switch-labeled-tosave"
					data-original-value="<?php echo $page->get_field_value( 'ca_prefill', 'yes' ) ?>"
					value="yes"
					<?php checked( $page->get_field_value( 'ca_prefill', 'yes' ), 'yes' ); ?>>
			</label>
		</div>

		<?php if ( 'yes' == $page->get_field_value( 'ca_prefill', 'yes' ) ) : ?>
			<span class="text-status text-status-on text-success"><?php _e( 'Pre-fill data is ON!', 'pixel-caffeine' ) ?></span>
		<?php else : ?>
			<span class="text-status text-status-off text-danger"><?php _e( 'Pre-fill data is OFF!', 'pixel-caffeine' ) ?></span>
		<?php endif; ?>

		<div class="field-helper">
			<?php $page->print_field_error( 'ca_prefill', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>
<?php endif; ?>

<div id="ca_retention_field" class="form-group form-horizontal-inline has-error-long <?php $page->field_class( 'ca_retention' ) ?>">
	<label for="ca_retention" class="control-label"><?php _e( 'Retention', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<input
			type="text"
			class="form-control"
			name="ca_retention"
			id="ca_retention"
			placeholder="<?php _e( 'Num', 'pixel-caffeine' ) ?>"
			value="<?php echo 'edit' == $action ? '{{{ data.retention }}}' : $page->get_field_value( 'ca_retention', '' ) ?>">
		<span class="text"><?php _e( 'days', 'pixel-caffeine' ) ?></span>
		<div class="field-helper">
			<?php $page->print_field_error( 'ca_retention', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>
<div id="ca_include_url_field" class="form-group<?php $page->field_class( 'ca_include_url' ) ?>">
	<label for="ca_include_url" class="control-label"><?php _e( 'URL to include', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<div class="input-group select-and-multi-tags">
			<div class="input-group-btn">
				<select name="ca_include_url_condition" id="ca_include_url_condition" class="form-control dropdown-width-max">
					<option value="i_contains"<# if ( 'i_contains' == data.include_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL contains', 'pixel-caffeine' ) ?></option>
					<option value="eq"<# if ( 'eq' == data.include_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL equals', 'pixel-caffeine' ) ?></option>
					<option value="regex_match"<# if ( 'regex_match' == data.include_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL matches regular expression', 'pixel-caffeine' ) ?></option>
				</select>
			</div>
			<input
				type="text"
				class="form-control multi-tags"
				name="ca_include_url"
				id="ca_include_url"
				value="<?php echo 'edit' == $action ? '{{{ data.include_url }}}' : $page->get_field_value( 'ca_include_url', '' ) ?>" />
		</div>
		<div class="field-helper">
			<?php $page->print_field_error( 'ca_include_url', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>
<div id="ca_exclude_url_field" class="form-group<?php $page->field_class( 'ca_exclude_url' ) ?>">
	<label for="ca_exclude_url" class="control-label"><?php _e( 'URL to exclude', 'pixel-caffeine' ) ?></label>
	<div class="control-wrap">
		<div class="input-group select-and-multi-tags">
			<div class="input-group-btn">
				<select name="ca_exclude_url_condition" id="ca_exclude_url_condition" class="form-control dropdown-width-max">
					<option value="i_contains"<# if ( 'i_contains' == data.exclude_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL contains', 'pixel-caffeine' ) ?></option>
					<option value="eq"<# if ( 'eq' == data.exclude_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL equals', 'pixel-caffeine' ) ?></option>
					<option value="regex_match"<# if ( 'regex_match' == data.exclude_url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'URL matches regular expression', 'pixel-caffeine' ) ?></option>
				</select>
			</div>
			<input
				type="text"
				class="form-control multi-tags"
				name="ca_exclude_url"
				id="ca_exclude_url"
				value="<?php echo 'edit' == $action ? '{{{ data.exclude_url }}}' : $page->get_field_value( 'ca_exclude_url', '' ) ?>" />
		</div>
		<div class="field-helper">
			<?php $page->print_field_error( 'ca_exclude_url', '<span class="help-block help-block-error">', '</span>' ) ?>
		</div>
	</div>
</div>
<div class="panel panel-ca-filters">
	<div class="panel-heading">
		<h3 class="tit"><?php _e( 'Special Filters', 'pixel-caffeine' ) ?>
			<a href="#_" class="btn btn-success add-new-filter" data-toggle="modal" data-target="#modal-ca-new-filter"><?php _e( 'Add new filter', 'pixel-caffeine' ) ?></a></h3>
	</div>
	<div class="panel-body js-ca-filters">

		<?php if ( 'add' == $action ) : $rule = (array) $page->get_field_value( 'ca_rule', array() ); ?>

			<div class="no-filters-feedback<?php echo ! empty( $rule ) ? ' hide' : '' ?>">
				<p><?php _e( 'No filters set yet', 'pixel-caffeine' ) ?></p>
			</div>

			<?php foreach ( array( 'include', 'exclude' ) as $main_condition ) :
				$filters = isset( $rule[ $main_condition ] ) ? $rule[ $main_condition ] : array();
				?>
				<div class="form-group<?php echo empty( $filters ) ? ' hide' : '' ?> js-<?php echo $main_condition ?>-filters">
					<label for="" class="control-label"><?php 'include' == $main_condition ? _e( 'Include only users who', 'pixel-caffeine' ) : _e( 'Exclude only users who', 'pixel-caffeine' ) ?></label>
					<div class="control-wrap">
						<ul class="list-filter">
							<?php foreach ( $filters as $index => $filter ) :

								if ( $filter['main_condition'] != $main_condition ) {
									continue;
								}

								$tmp_ca = new AEPC_Admin_CA();
								?>
								<li data-filter-id="<?php echo $index ?>">
									<?php if ( $index > 0 ) : ?>
										<strong class="filter-and"><?php esc_html_e( 'and', 'pixel-caffeine' ) ?></strong>
									<?php endif; ?>
									<div class="label"><?php echo $tmp_ca->get_human_filter( $filter, '<em>', '</em>' ); ?></div>
									<div class="hide hidden-fields">
										<input type="hidden" name="ca_rule[<?php echo $index ?>][main_condition]" value="<?php echo esc_attr( $filter['main_condition'] ) ?>">
										<input type="hidden" name="ca_rule[<?php echo $index ?>][event_type]" value="<?php echo esc_attr( $filter['event_type'] ) ?>">
										<input type="hidden" name="ca_rule[<?php echo $index ?>][event]" value="<?php echo esc_attr( $filter['event'] ) ?>">
										<?php foreach ( $filter['conditions'] as $c_index => $condition ) : ?>
											<?php if ( ! empty( $condition['key'] ) ) : ?>
												<input type="hidden" name="ca_rule[<?php echo $index ?>][conditions][<?php echo esc_attr( $c_index ) ?>][key]" value="<?php echo esc_attr( $condition['key'] ) ?>">
											<?php endif; ?>
											<input type="hidden" name="ca_rule[<?php echo $index ?>][conditions][<?php echo esc_attr( $c_index ) ?>][operator]" value="<?php echo esc_attr(  $condition['operator'] ) ?>">
											<input type="hidden" name="ca_rule[<?php echo $index ?>][conditions][<?php echo esc_attr( $c_index ) ?>][value]" value="<?php echo esc_attr(  $condition['value'] ) ?>">
										<?php endforeach; ?>
									</div>
									<div class="actions">
										<div class="btn-group-sm">
											<a href="#_" class="btn btn-fab btn-delete btn-danger"></a>
											<a href="#_" class="btn btn-fab btn-edit btn-primary"></a>
										</div>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<!-- ./form-group -->
			<?php endforeach; ?>
		<!-- ./form-group -->

		<?php else : ?>

			<div class="no-filters-feedback<# if ( ! _.isEmpty( data.include_filters ) || ! _.isEmpty( data.exclude_filters ) ) { #> hide<# } #>">
				<p><?php _e( 'No filters set yet', 'pixel-caffeine' ) ?></p>
			</div>

			<?php foreach ( array( 'include', 'exclude' ) as $main_condition ) : ?>
			<div class="form-group<# if ( _.isEmpty( data.<?php echo $main_condition ?>_filters ) ) { #> hide<# } #> js-<?php echo $main_condition ?>-filters">
				<label for="" class="control-label"><?php 'include' == $main_condition ? _e( 'Include only users who', 'pixel-caffeine' ) : _e( 'Exclude only users who', 'pixel-caffeine' ) ?></label>
				<div class="control-wrap">
					<ul class="list-filter">
						<# _.each( data.<?php echo $main_condition ?>_filters, function( rule, index ) { #>
							<li data-filter-id="{{{ index }}}">
								<# if ( index > 0 ) { #>
									<strong class="filter-and"><?php esc_html_e( 'and', 'pixel-caffeine' ) ?></strong>
								<# } #>
								<div class="label">{{{ rule.statement }}}</div>
									<div class="hide hidden-fields">
										<input type="hidden" name="ca_rule[{{{ index }}}][main_condition]" value="{{{ rule.main_condition }}}">
										<input type="hidden" name="ca_rule[{{{ index }}}][event_type]" value="{{{ rule.event_type }}}">
										<input type="hidden" name="ca_rule[{{{ index }}}][event]" value="{{{ rule.event }}}">
										<# _.each( rule.conditions, function( condition, c_index ) { #>
											<# if ( ! _.isEmpty( condition.key ) ) { #>
												<input type="hidden" name="ca_rule[{{{ index }}}][conditions][{{{ c_index }}}][key]" value="{{{ condition.key }}}">
											<# } #>
											<input type="hidden" name="ca_rule[{{{ index }}}][conditions][{{{ c_index }}}][operator]" value="{{{ condition.operator }}}">
											<input type="hidden" name="ca_rule[{{{ index }}}][conditions][{{{ c_index }}}][value]" value="{{{ condition.value }}}">
										<# } ); #>
									</div>
								<div class="actions">
									<div class="btn-group-sm">
										<a href="#_" class="btn btn-fab btn-delete btn-danger"></a>
										<a href="#_" class="btn btn-fab btn-edit btn-primary"></a>
									</div>
								</div>
							</li>
						<# } ); #>
					</ul>
				</div>
			</div>
			<!-- ./form-group -->
			<?php endforeach; ?>

		<?php endif; ?>

	</div>
	<!-- ./panel-body -->
</div>
<!-- ./panel-ca-filters -->

<?php
$page->register_script_template( 'ca-filter-item', '
	<li data-filter-id="{{{ data.index }}}">
		<# if ( data.nfilters > 0 ) { #>
			<strong class="filter-and">' . esc_html__( 'and', 'pixel-caffeine' ) . '</strong>
		<# } #>
		<div class="label">{{{ data.statement }}}</div>
		<div class="hide hidden-fields">{{{ data.hidden_inputs }}}</div>
		<div class="actions">
			<div class="btn-group-sm">
				<a href="#_" class="btn btn-fab btn-delete btn-danger"></a>
				<a href="#_" class="btn btn-fab btn-edit btn-primary"></a>
			</div>
		</div>
	</li>
');
?>
