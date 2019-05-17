/**
 * Handles the creation and the behavior of the Toolset Content Template block.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import classnames from 'classnames';
import icon from './icon';
import Inspector from './inspector/inspector';
import CREDFormSelect from './inspector/cred-form-select';
import CREDFormPreview from './cred-form-preview';
import './styles/editor.scss';

/**
 * Internal block libraries
 */
const {
	__,
} = wp.i18n;

const {
	registerBlockType,
} = wp.blocks;

const {
	Placeholder,
} = wp.components;

const {
	RawHTML,
} = wp.element;

const {
	toolset_cred_form_block_strings: i18n,
} = window;

const name = i18n.blockName;

/**
 * Forms are grouped by Form type (Post, User and Relationship), and each of them by Editing or Adding.
 * This funcion search a form by its `post_name`
 *
 * @param {Object} forms An object containing the forms: `i18n.publishedForms`
 * @param {String} formName The name of the form: `post_name`
 * @return {Object} An object containing form data
 */
const getFormByFormName = ( forms, formName ) => {
	if ( forms instanceof Array ) {
		const selectedItem = forms.filter( formItem => formItem.post_name === formName );
		return selectedItem.length ? selectedItem[ 0 ] : null;
	}
	let result = null;
	Object.keys( forms ).forEach( key => {
		result = result || getFormByFormName( forms[ key ], formName );
		return result;
	} );
	return result;
};

const settings = {
	title: __( 'Form' ),
	description: __( 'Add a Toolset Form to the editor.' ),
	category: i18n.blockCategory,
	icon: icon.blockIcon,
	keywords: [
		__( 'Toolset' ),
		__( 'Form' ),
		__( 'Shortcode' ),
	],

	edit: props => {
		const onChangeCredForm = ( event ) => {
			const formInfo = JSON.parse( event.target.value );
			props.setAttributes( { form: event.target.value } );
			props.setAttributes( { formType: formInfo.formType } );
			props.setAttributes( { formAction: formInfo.formAction } );
		};

		const onChangePostToEdit = ( value ) => {
			props.setAttributes( { postToEdit: value } );
		};

		const onChangeAnotherPostToEdit = ( value ) => {
			props.setAttributes( { anotherPostToEdit: value } );
		};

		const onChangeUserToEdit = ( value ) => {
			props.setAttributes( { userToEdit: value } );
		};

		const onChangeAnotherUserToEdit = ( value ) => {
			if ( null === value ) {
				value = '';
			}
			props.setAttributes( { anotherUserToEdit: value } );
		};

		const onChangeRelationshipFormType = ( value ) => {
			props.setAttributes( { relationshipFormType: value } );
		};

		const onChangeRelationshipParentItem = ( value ) => {
			props.setAttributes( { relationshipParentItem: value } );
		};

		const onChangeRelationshipChildItem = ( value ) => {
			props.setAttributes( { relationshipChildItem: value } );
		};

		const onChangeRelationshipParentItemSpecific = ( option ) => {
			props.setAttributes( { relationshipParentItemSpecific: option } );
		};

		const onChangeRelationshipChildItemSpecific = ( option ) => {
			props.setAttributes( { relationshipChildItemSpecific: option } );
		};

		const assignFormTypeAndAction = ( forms, type, action ) => {
			forms.map( ( item ) => {
				item.formType = type;
				item.formAction = action;
				return item;
			} );

			return forms;
		};

		/**
		 * When selecting a relationship, extra data must be fetched and added for using in the Inspector
		 *
		 * @param {object} data Extra data.
		 */
		const onUpdateRelationshipData = data => {
			props.setAttributes( { relationshipData: data } );
		};

		const newPostForms = assignFormTypeAndAction( i18n.publishedForms.postForms.new, 'post', 'new' );
		const editPostForms = assignFormTypeAndAction( i18n.publishedForms.postForms.edit, 'post', 'edit' );
		const newUserForms = assignFormTypeAndAction( i18n.publishedForms.userForms.new, 'user', 'new' );
		const editUserForms = assignFormTypeAndAction( i18n.publishedForms.userForms.edit, 'user', 'edit' );
		const newRelationshipForms = assignFormTypeAndAction( i18n.publishedForms.relationshipForms.new, 'relationship', 'new' );
		const relationshipData = !! props.attributes.relationshipData ? props.attributes.relationshipData.relationship : {};
		// props.attributes.form has not the same value that the select option, so it needs to get it again.
		const formData = !! props.attributes.form ? JSON.parse( props.attributes.form ) : {};
		const formValue = JSON.stringify( getFormByFormName( i18n.publishedForms, formData.post_name || '' ) );

		return [
			!! (
				props.focus ||
				props.isSelected
			) && (
				<Inspector
					key="wpv-gutenberg-cred-form-block-render-inspector"
					className={ classnames( 'wp-block-toolset-cred-form-inspector' ) }
					attributes={
						{
							newPostForms: newPostForms,
							editPostForms: editPostForms,
							newUserForms: newUserForms,
							editUserForms: editUserForms,
							newRelationshipForms: newRelationshipForms,
							form: formValue,
							formType: props.attributes.formType,
							formAction: props.attributes.formAction,
							postToEdit: props.attributes.postToEdit,
							anotherPostToEdit: props.attributes.anotherPostToEdit,
							userToEdit: props.attributes.userToEdit,
							anotherUserToEdit: props.attributes.anotherUserToEdit,
							relationshipFormType: props.attributes.relationshipFormType,
							relationshipParentItem: props.attributes.relationshipParentItem,
							relationshipChildItem: props.attributes.relationshipChildItem,
							relationshipParentItemSpecific: props.attributes.relationshipParentItemSpecific,
							relationshipChildItemSpecific: props.attributes.relationshipChildItemSpecific,
							relationshipData: relationshipData,
						}
					}
					onChangeCredForm={ onChangeCredForm }
					onChangePostToEdit={ onChangePostToEdit }
					onChangeUserToEdit={ onChangeUserToEdit }
					onChangeAnotherPostToEdit={ onChangeAnotherPostToEdit }
					onChangeAnotherUserToEdit={ onChangeAnotherUserToEdit }
					onChangeRelationshipFormType={ onChangeRelationshipFormType }
					onChangeRelationshipParentItem={ onChangeRelationshipParentItem }
					onChangeRelationshipChildItem={ onChangeRelationshipChildItem }
					onChangeRelationshipParentItemSpecific={ onChangeRelationshipParentItemSpecific }
					onChangeRelationshipChildItemSpecific={ onChangeRelationshipChildItemSpecific }
				/>
			),
			(
				! props.attributes.form ?
					<Placeholder
						key="cred-form-block-placeholder"
						className={ classnames( 'wp-block-toolset-cred-form' ) }
					>
						<div className="wp-block-toolset-cred-form-placeholder">
							{ icon.blockPlaceholder }
							<h2>{ __( 'Toolset Form' ) }</h2>
							<CREDFormSelect
								attributes={
									{
										newPostForms: newPostForms,
										editPostForms: editPostForms,
										newUserForms: newUserForms,
										editUserForms: editUserForms,
										newRelationshipForms: newRelationshipForms,
										form: '',
									}
								}
								className={ classnames( 'components-select-control__input' ) }
								onChangeCredForm={ onChangeCredForm }
							/>
						</div>
					</Placeholder> :
					<CREDFormPreview
						key="toolset-cred-form-gutenberg-block-preview"
						className={ classnames( props.className, 'wp-block-toolset-cred-form-preview' ) }
						attributes={
							{
								form: JSON.parse( props.attributes.form ),
							}
						}
						onUpdateRelationshipData={ onUpdateRelationshipData }
					/>
			),
		];
	},
	save: ( props ) => {
		let form = props.attributes.form || '';
		let post = '',
			user = '',
			relationship = '',
			shortcodeStart = '[cred-form';

		const shortcodeEnd = ']';

		if ( ! form.length ) {
			return null;
		}
		form = JSON.parse( form );

		if (
			'post' === props.attributes.formType &&
			'edit' === props.attributes.formAction &&
			'another' === props.attributes.postToEdit &&
			props.attributes.anotherPostToEdit
		) {
			post = `post="${ props.attributes.anotherPostToEdit.value }" `;
		}

		if ( 'user' === props.attributes.formType ) {
			shortcodeStart = '[cred_user_form';
			if (
				'edit' === props.attributes.formAction &&
				'another' === props.attributes.userToEdit &&
				!! props.attributes.anotherUserToEdit &&
				!! props.attributes.anotherUserToEdit.value
			) {
				user = `user="${ props.attributes.anotherUserToEdit.value }" `;
			}
		}

		if ( 'relationship' === props.attributes.formType ) {
			shortcodeStart = '[cred-relationship-form';

			if ( 'createParent' === props.attributes.relationshipFormType ) {
				const parentItem = 'specific' === props.attributes.relationshipChildItem ? props.attributes.relationshipChildItemSpecific.value : props.attributes.relationshipChildItem;
				relationship += `child_item="${ parentItem }" `;
			} else if ( 'createChild' === props.attributes.relationshipFormType ) {
				const parentItem = 'specific' === props.attributes.relationshipParentItem ? props.attributes.relationshipParentItemSpecific.value : props.attributes.relationshipParentItem;
				relationship += `parent_item="${ parentItem }" `;
			}
			post = '';
		}

		form = ' form="' + form.post_name + '" ';

		return <RawHTML>{ shortcodeStart + form + post + user + relationship + shortcodeEnd }</RawHTML>;
	},
};

if ( i18n.isCREDActive ) {
	registerBlockType( name, settings );
}
