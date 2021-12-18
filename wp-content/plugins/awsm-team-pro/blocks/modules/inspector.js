const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    ExternalLink
} = wp.components;

class ATPInspector extends Component {
    constructor() {
        super(...arguments);
        const { attributes: { download, viewer } } = this.props;
    }
    render() {
        const { setAttributes } = this.props;
        let shortcodeText = jQuery('#awsm-block-popup #atp-shortcode').val();
        let link = "post.php?post="+shortcodeText+"&action=edit";
        return (
            <InspectorControls>
                <PanelBody>
                    <ExternalLink href={link}>{ __( 'Edit Team', 'awsm-team-pro' ) }</ExternalLink>
                </PanelBody>
            </InspectorControls>
        );
    }
}

export default ATPInspector;
