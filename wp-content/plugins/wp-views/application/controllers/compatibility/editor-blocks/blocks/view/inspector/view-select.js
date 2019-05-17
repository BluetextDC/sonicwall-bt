/**
 * The View block ViewSelect component.
 *
 * A "ViewSelect" component is created that is used inside the Toolset View block Inspector component to handle the View
 * selection. A special component is needed in order to support grouping of Posts/Taxonomy/Users Views.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import OptGroup from 'ToolsetBlocks/blocks/common/optgroup';

const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	BaseControl,
} = wp.components;

export default class ViewSelect extends Component {
	render() {
		const {
			attributes,
			className,
			onChangeView,
		} = this.props;

		const {
			view,
			posts,
			taxonomy,
			users,
		} = attributes;

		return (
			(
				'undefined' !== typeof posts &&
				'undefined' !== typeof taxonomy &&
				'undefined' !== typeof users
			) &&
			(
				posts.length > 0 ||
				taxonomy.length > 0 ||
				users.length > 0
			) ?
				<BaseControl>
					{
						// eslint-disable-next-line jsx-a11y/no-onchange
					} <select
						onChange={ onChangeView }
						value={ view }
						className={ className }
					>
						<option disabled="disabled" value="">{ __( 'Select a View', 'wpv-views' ) }</option>
						{
							posts.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Post Views', 'wpv-views' ),
											items: posts,
										}
									}
								/> :
								null
						}

						{
							taxonomy.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Taxonomy Views', 'wpv-views' ),
											items: taxonomy,
										}
									}
								/> :
								null
						}
						{
							users.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'User Views', 'wpv-views' ),
											items: users,
										}
									}
								/> :
								null
						}
					</select>
				</BaseControl> :
				<BaseControl>
					<select
						disabled="disabled"
						className={ className }
					>
						<option>{ __( 'Create a View first', 'wpv-views' ) }</option>
					</select>
				</BaseControl>
		);
	}
}
