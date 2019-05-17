/**
 * The Toolset Form block preview component.
 *
 * A "CREDFormPreview" component is created that is used inside the Toolset Form block to handle the previewing of the
 * selected Content Template.
 *
 * @since  2.6.0
 */

import classnames from 'classnames';

const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	Spinner,
} = wp.components;

const {
	toolset_cred_form_block_strings: i18n,
} = window;

export default class CREDFormPreview extends Component {
	// constructor( props ) {
	constructor() {
		super( ...arguments );
		this.getCREDFormInfo = this.getCREDFormInfo.bind( this );
		this.state = {
			fetching: false,
			error: false,
			errorMessage: '',
		};

		this.onUpdateRelationshipData = this.props.onUpdateRelationshipData;
	}

	render() {
		const {
			fetching,
			error,
			errorMessage,
			formInfo,
		} = this.state;

		if ( fetching ) {
			return <div key="fetching" className={ classnames( this.props.className ) } >
				<div key="loading" className={ classnames( 'wp-block-embed is-loading' ) }>
					<Spinner />
					<p>{ __( 'Loading the Toolset Form Preview…' ) }</p>
				</div>
			</div>;
		}

		if ( error ) {
			return <div key="error" className={ classnames( this.props.className ) } >
				<div className={ classnames( 'wpv-cred-form-info-warning' ) }>
					{ errorMessage }
				</div>

			</div>;
		}

		return (
			<div className={ this.props.className } >
				<div key="cred-form-information" className="cred-form-information" >
					<div className={ classnames( 'cred-form-preview-render' ) } dangerouslySetInnerHTML={ { __html: formInfo } }></div>
				</div>
			</div>
		);
	}

	static getDerivedStateFromProps( nextProps, prevState ) {
		if (
			nextProps.attributes.form.ID &&
			nextProps.attributes.form.ID !== prevState.formID &&
			prevState.fetching === false
		) {
			// If the Toolset Form is already there, we're loading a saved block, so we need to render
			// a different thing, which is why this doesn't use 'fetching', as that
			// is for when the user is putting in a new url on the placeholder form
			prevState.fetching = true;
		}

		return prevState;
	}

	componentDidUpdate( prevProps ) {
		if ( this.props.attributes.form.ID !== prevProps.attributes.form.ID ) {
			this.setState( {
				fetching: true,
				error: false,
				errorMessage: '',
			} );
			this.getCREDFormInfo( this.props.attributes.form.ID );
		}
	}

	componentDidMount() {
		this.getCREDFormInfo();
	}

	getCREDFormInfo( formID ) {
		const data = new window.FormData();
		data.append( 'action', i18n.actionName );
		data.append( 'wpnonce', i18n.wpnonce );
		data.append( 'formID', 'undefined' === typeof formID ? this.props.attributes.form.ID : formID );

		window.fetch( window.ajaxurl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
		} ).then( res => res.json() )
			.then( response => {
				const newState = {};
				if (
					0 !== response &&
					response.success &&
					'undefined' !== typeof response.data
				) {
					if ( '' === response.data.formContent ) {
						newState.error = true;
						newState.errorMessage = __( 'The selected Toolset Form has an empty form content.' );
					} else {
						newState.formInfo = response.data.formContent;
						newState.formID = response.data.formID;
					}
				} else {
					let message = '';
					if (
						'undefined' !== typeof response.data &&
						'undefined' !== typeof response.data.message ) {
						message = response.data.message;
					} else {
						message = __( 'An error occurred while trying to get the Toolset Form information.' );
					}
					newState.error = true;
					newState.errorMessage = message;
				}

				newState.fetching = false;

				// Relationship forms need extra data.
				if ( this.props.attributes.form.formType === 'relationship' ) {
					const parameters = '?' +
						'action=' + i18n.association.action + '&' +
						'form=' + this.props.attributes.form.post_name + '&' +
						'wpnonce=' + i18n.association.wpnonce;
					window.fetch( window.ajaxurl + parameters )
						.then( res => res.json() )
						.then( res => {
							this.onUpdateRelationshipData( res.data );
							this.setState( newState );
						} );
				} else {
					this.setState( newState );
				}
			} );
	}
}
