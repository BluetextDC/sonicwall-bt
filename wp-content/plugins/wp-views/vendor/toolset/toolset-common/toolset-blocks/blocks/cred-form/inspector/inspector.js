/**
 * The Content Template block inspector component.
 *
 * An "Inspector" component is created that is used inside the Toolset Content Template block to handle all the functionality related
 * to the controls on the Gutenberg editor sidebar.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import CREDFormSelect from './cred-form-select';
import Select2 from '../../common/select2';

/**
 * Internal block libraries
 */
const {
	__,
	sprintf,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	InspectorControls,
} = wp.editor;

const {
	BaseControl,
	RadioControl,
	PanelBody,
} = wp.components;

/**
 * Create an Inspector Controls wrapper Component
 */
export default class Inspector extends Component {
	render() {
		const {
			attributes,
			className,
			onChangeCredForm,
			onChangePostToEdit,
			onChangeAnotherPostToEdit,
			onChangeUserToEdit,
			onChangeAnotherUserToEdit,
			onChangeRelationshipFormType,
			onChangeRelationshipParentItem,
			onChangeRelationshipChildItem,
			onChangeRelationshipParentItemSpecific,
			onChangeRelationshipChildItemSpecific,
		} = this.props;

		const {
			form,
			formType,
			formAction,
			postToEdit,
			anotherPostToEdit,
			userToEdit,
			anotherUserToEdit,
			newPostForms,
			editPostForms,
			newUserForms,
			editUserForms,
			newRelationshipForms,
			relationshipFormType,
			relationshipParentItem,
			relationshipChildItem,
			relationshipParentItemSpecific,
			relationshipChildItemSpecific,
			relationshipData,
		} = attributes;

		let relationshipCreateLabel = '';
		let relationshipCreateChildLabel = '';
		let relationshipCreateChildSubLabel = '';
		let relationshipCreateChildOptionCurrent = '';
		let relationshipCreateChildOptionSpecific = '';
		let relationshipCreateParentLabel = '';
		let relationshipCreateParentSubLabel = '';
		let relationshipCreateParentOptionCurrent = '';
		let relationshipCreateParentOptionSpecific = '';

		if ( Object.keys( relationshipData ).length ) {
			// translators: %1 is the name of a post type name (i.e. Flight) - %2 is also a post type name but with an indefinite article (i.e. a Airport) - %3 is the name of the relationship with a indefinite article (i.e. a Departure)
			relationshipCreateLabel = sprintf( __( 'Connect between any %s and %s as %s' ), relationshipData.parent.labelSingular, relationshipData.child.labelSingularPrefixed, relationshipData.labelSingularPrefixed );
			// translators: %1 is the name of a post type name with an indefinite article (i.e. Airport) - %2 is also a post type name but without an indefinite article (i.e. a Flight) - %3 is the name of the relationship with a indefinite article (i.e. a Departure)
			relationshipCreateChildLabel = sprintf( __( 'Connect %s to a given %s as %s' ), relationshipData.child.labelSingularPrefixed, relationshipData.parent.labelSingular, relationshipData.labelSingularPrefixed );
			// translators: %1 and %2 are post type name (i.e. Airport or Flight)
			relationshipCreateChildSubLabel = sprintf( __( '%s to add %s to' ), relationshipData.parent.labelSingular, relationshipData.child.labelSingular );
			// translators: %1 is post type name (i.e. Airport)
			relationshipCreateChildOptionCurrent = sprintf( __( 'Use the current %s' ), relationshipData.parent.labelSingular );
			// translators: %1 is post type name (i.e. Airport)
			relationshipCreateChildOptionSpecific = sprintf( __( 'Use a specific %s' ), relationshipData.parent.labelSingular );
			// translators: %1 is the name of a post type name with an indefinite article (i.e. Airport) - %2 is also a post type name but without an indefinite article (i.e. a Flight) - %3 is the name of the relationship with a indefinite article (i.e. a Departure)
			relationshipCreateParentLabel = sprintf( __( 'Connect %s to a given %s as %s' ), relationshipData.parent.labelSingularPrefixed, relationshipData.child.labelSingular, relationshipData.labelSingularPrefixed );
			// translators: %1 and %2 are post type name (i.e. Airport or Flight)
			relationshipCreateParentSubLabel = sprintf( __( '%s to add %s to' ), relationshipData.child.labelSingular, relationshipData.parent.labelSingular );
			// translators: %1 is post type name (i.e. Airport)
			relationshipCreateParentOptionCurrent = sprintf( __( 'Use the current %s' ), relationshipData.child.labelSingular );
			// translators: %1 is post type name (i.e. Airport)
			relationshipCreateParentOptionSpecific = sprintf( __( 'Use a specific %s' ), relationshipData.child.labelSingular );
		}

		return (
			<InspectorControls>
				<div className={ className }>
					<PanelBody title={ __( 'Toolset Form' ) }>
						<BaseControl>
							<CREDFormSelect
								attributes={
									{
										newPostForms: newPostForms,
										editPostForms: editPostForms,
										newUserForms: newUserForms,
										editUserForms: editUserForms,
										newRelationshipForms: newRelationshipForms,
										form: form,
									}
								}
								className="components-select-control__input"
								onChangeCredForm={ onChangeCredForm }
							/>
						</BaseControl>
						{
							'edit' === formAction && 'post' === formType ?
								[
									<RadioControl
										key="postToEdit"
										label={ __( 'Post to edit' ) }
										selected={ postToEdit }
										onChange={ onChangePostToEdit }
										options={
											[
												{ value: 'current', label: __( 'The current post' ) },
												{ value: 'another', label: __( 'Another post' ) },
											]
										}
									/>,
									// @todo Switch the custom endpoint with the native one, once it's available.
									// The endpoint used on the autocomplete component below to get the post according to a
									// search keyword, needs to be changed with the native WP REST API endpoint (as soon as
									// it's available).
									// https://github.com/WordPress/gutenberg/issues/2084
									// https://core.trac.wordpress.org/ticket/39965
									'another' === postToEdit ?
										<BaseControl label={ __( 'Post' ) } key="another-post-select">
											<Select2
												onChange={ onChangeAnotherPostToEdit }
												restInfo={
													{
														base: '/toolset/v2/search-posts',
														args: {
															search: '%s',
														},
													}
												}
												value={ anotherPostToEdit }
											/>
										</BaseControl> :
										null,
								] :
								null
						}
						{
							'edit' === formAction && 'user' === formType ?
								[
									<RadioControl
										key="userToEdit"
										label={ __( 'User to edit' ) }
										selected={ userToEdit }
										onChange={ onChangeUserToEdit }
										options={
											[
												{ value: 'current', label: __( 'The current logged in user' ) },
												{ value: 'another', label: __( 'Another user' ) },
											]
										}
									/>,
									'another' === userToEdit ?
										<BaseControl label={ __( 'User' ) } key="another-user-select">
											<Select2
												onChange={ onChangeAnotherUserToEdit }
												restInfo={
													{
														base: '/wp/v2/users',
														args: {
															search: '%s',
															per_page: 20,
															orderby: 'name',
															context: 'edit',
														},
													}
												}
												value={ anotherUserToEdit }
											/>
										</BaseControl> :
										null,
								] :
								null
						}
						{
							'relationship' === formType ?
								[
									<RadioControl
										key="relationshipFormType"
										// translators: The form can be used to create a new relationship or edit or ...
										label={ __( 'What should the form do?' ) }
										selected={ relationshipFormType }
										onChange={ onChangeRelationshipFormType }
										options={
											[
												{ value: 'create', label: relationshipCreateLabel },
												{ value: 'createChild', label: relationshipCreateChildLabel },
												{ value: 'createParent', label: relationshipCreateParentLabel },
											]
										}
									/>,
									'createChild' === relationshipFormType ?
										<RadioControl
											key="relationshipChildItem"
											label={ relationshipCreateChildSubLabel }
											selected={ relationshipParentItem }
											onChange={ onChangeRelationshipParentItem }
											options={
												[
													{ value: '$current', label: relationshipCreateChildOptionCurrent },
													{ value: 'specific', label: relationshipCreateChildOptionSpecific },
												]
											}
										/> :
										null,
									'createChild' === relationshipFormType && 'specific' === relationshipParentItem ?
										<Select2
											onChange={ onChangeRelationshipParentItemSpecific }
											restInfo={
												{
													base: '/toolset/v2/search-posts',
													args: {
														search: '%s',
														post_type: relationshipData.parent.type,
													},
												}
											}
											value={ relationshipParentItemSpecific }
										/> :
										null,
									'createParent' === relationshipFormType ?
										<RadioControl
											key="relationshipParentItem"
											label={ relationshipCreateParentSubLabel }
											selected={ relationshipChildItem }
											onChange={ onChangeRelationshipChildItem }
											options={
												[
													{ value: '$current', label: relationshipCreateParentOptionCurrent },
													{ value: 'specific', label: relationshipCreateParentOptionSpecific },
												]
											}
										/> :
										null,
									'createParent' === relationshipFormType && 'specific' === relationshipChildItem ?
										<Select2
											onChange={ onChangeRelationshipChildItemSpecific }
											restInfo={
												{
													base: '/toolset/v2/search-posts',
													args: {
														search: '%s',
														post_type: relationshipData.child.type,
													},
												}
											}
											value={ relationshipChildItemSpecific }
										/> :
										null,
								] :
								null
						}
					</PanelBody>
				</div>
			</InspectorControls>
		);
	}
}
