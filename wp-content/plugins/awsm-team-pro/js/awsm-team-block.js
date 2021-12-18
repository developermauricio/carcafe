( function( blocks, element, components ) {
 
    var el = element.createElement,
    registerBlockType = blocks.registerBlockType,
    ServerSideRender = components.ServerSideRender,
    TextControl = wp.components.TextControl,
    InspectorControls = wp.editor.InspectorControls;
    registerBlockType( 'gutenberg-awsm/awsm-team-dynamic', {
        title: 'AWSM Team',
        icon: 'admin-users',
        category: 'widgets',
 
        edit: function( props ) {
            return [
            /*
             * The ServerSideRender element uses the REST API to automatically call
             * php_block_render() in your PHP code whenever it needs to get an updated
             * view of the block.
             */
            el( ServerSideRender, {
                block: 'gutenberg-awsm/awsm-team-dynamic',
                attributes: props.attributes,
            } ),
            /*
             * InspectorControls lets you add controls to the Block sidebar. In this case,
             * we're adding a TextControl, which lets us edit the 'foo' attribute (which
             * we defined in the PHP). The onChange property is a little bit of magic to tell
             * the block editor to update the value of our 'foo' property, and to re-render
             * the block.
             */
            el( InspectorControls, {},
                el( TextControl, {
                    label: 'Team settings',
                    value: props.attributes.foo,
                    onChange: ( value ) => { props.setAttributes( { foo: value } ); },
                } )
            ),
        ];


            // return (
            //     el(ServerSideRender, {
            //         block: "gutenberg-awsm/awsm-team-dynamic",
            //         attributes: props.attributes
            //     } )
            // );
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
) );

