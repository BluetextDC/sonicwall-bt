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
import OptGroup from '../../common/optgroup';

const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	BaseControl,
} = wp.components;

export default class CREDFormSelect extends Component {
	render() {
		const {
			attributes,
			className,
			onChangeCredForm,
		} = this.props;

		const {
			form,
			newPostForms,
			editPostForms,
			newUserForms,
			editUserForms,
			newRelationshipForms,
		} = attributes;

		return (
			(
				'undefined' !== typeof newPostForms &&
				'undefined' !== typeof editPostForms &&
				'undefined' !== typeof newUserForms &&
				'undefined' !== typeof editUserForms &&
				'undefined' !== typeof newRelationshipForms
			) &&
			(
				newPostForms.length > 0 ||
				editPostForms.length > 0 ||
				newUserForms.length > 0 ||
				editUserForms.length > 0 ||
				newRelationshipForms.length > 0
			) ?
				<BaseControl>
					{
						// eslint-disable-next-line jsx-a11y/no-onchange
					} <select
						onChange={ onChangeCredForm }
						value={ form }
						className={ className }
					>
						<option disabled="disabled" value="">{ __( 'Select a Toolset Form' ) }</option>
						{
							newPostForms.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Add Post Forms' ),
											items: newPostForms,
											valueOrigin: 'object',
										}
									}
								/> :
								null
						}
						{
							editPostForms.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Edit Post Forms' ),
											items: editPostForms,
											valueOrigin: 'object',
										}
									}
								/> :
								null
						}
						{
							newUserForms.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Add User Forms' ),
											items: newUserForms,
											valueOrigin: 'object',
										}
									}
								/> :
								null
						}
						{
							editUserForms.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Edit User Forms' ),
											items: editUserForms,
											valueOrigin: 'object',
										}
									}
								/> :
								null
						}
						{
							newRelationshipForms.length > 0 ?
								<OptGroup
									attributes={
										{
											label: __( 'Add Relationship Forms' ),
											items: newRelationshipForms,
											valueOrigin: 'object',
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
						<option>{ __( 'Create a Toolset Form first' ) }</option>
					</select>
				</BaseControl>
		);
	}
}
