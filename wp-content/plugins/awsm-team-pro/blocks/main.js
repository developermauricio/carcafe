/**
 * BLOCK: AWSM Team
 *
 * Registering a basic block with Gutenberg.
 */

import ATPInspector from './modules/inspector';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { ServerSideRender, Placeholder, Button } = wp.components;

/**
 * Register: a Gutenberg Block.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'gutenberg-awsm/awsm-team-dynamic', {
	title: __( 'AWSM Team', 'awsm-team-pro' ), // Block title.
	description: __( 'Select your team', 'awsm-team-pro' ), // Block description
	icon: 'admin-users', // Block icon
	category: 'common', // Block category,
	keywords: [ __( 'team', 'awsm-team-pro' ), __( 'awsm team', 'awsm-team-pro' ), __( 'member', 'awsm-team-pro' ) ], // Access the block easily with keyword aliases
	/**
	 * The edit function describes the structure of the block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 */
	edit: ( props ) => {
		const { attributes, setAttributes } = props;
		const { shortcode } = attributes;
		let blockProps = null;
		const setBlockProps = () => {
			blockProps = props;
			blockProps.activeAtpBlock = true;
		};
		jQuery('body').on('click', '#awsm-block-popup #awsm-insert-team', () => {
			let shortcodeText = jQuery('#awsm-block-popup #atp-shortcode').val();
			if( blockProps !== null ) {
				if(blockProps.activeAtpBlock === true) {
					blockProps.activeAtpBlock = false;
					blockProps.setAttributes({
						shortcode: shortcodeText
					});
				}
			}
		});
		if( typeof shortcode !== 'undefined' ) {
			return [
				<ATPInspector { ...{ setAttributes, ...props } } />,
				<ServerSideRender
					block="gutenberg-awsm/awsm-team-dynamic"
					attributes={ attributes }
				/>
			];
		} else {
			return (
				<Placeholder label={ __( 'AWSM Team', 'awsm-team-pro' ) } instructions={ __( 'Pick a team to add to your page.', 'awsm-team-pro' ) } icon="admin-users" className="atp-block-wrapper">
					<Button className="awsm-team-btn" onClick={ setBlockProps } isSecondary isLarge>
						{ __( 'Select Team', 'awsm-team-pro' ) }
					</Button>
				</Placeholder>
			);
		}
	},
	/**
	 * The save function defines the way in which the different attributes should be combined into the final markup, which is then serialized by Gutenberg into post_content.
	 */
	save: ( props ) => {
		const { attributes: { shortcode } } = props;
		return shortcode;
	},
} );
