/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Extends the core Group and Query blocks to support Swiper.js configurations.
 */
const { addFilter } = wp.hooks;
const { createHigherOrderComponent } = wp.compose;
const { Fragment, createElement: el } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl, ToggleControl } = wp.components;

// Define the blocks that can accept our Swiper configuration
const ALLOWED_BLOCKS = [ 'core/group', 'core/query' ];

/**
 * 1. Register Swiper Attributes in the block schema
 */
function addSwiperAttributes( settings, name ) {
    if ( ! ALLOWED_BLOCKS.includes( name ) ) {
        return settings;
    }

    settings.attributes = Object.assign( settings.attributes || {}, {
        swiperSlidesDesktop: { type: 'string', default: '4' },
        swiperSlidesTablet:  { type: 'string', default: '2' },
        swiperSlidesMobile:  { type: 'string', default: '1' },
        swiperSpaceBetween:  { type: 'string', default: '20' },
        swiperAutoplay:      { type: 'boolean', default: false },
        swiperDelay:         { type: 'string', default: '3000' },
        swiperLoop:          { type: 'boolean', default: true },
        swiperPagination:    { type: 'boolean', default: true },
        swiperNavigation:    { type: 'boolean', default: false }
    });

    return settings;
}
addFilter( 'blocks.registerBlockType', 'digitally-disruptive/swiper-attrs', addSwiperAttributes );

/**
 * 2. Conditionally Inject the UI Controls into the Block Sidebar
 */
const addSwiperInspectorControls = createHigherOrderComponent( function( BlockEdit ) {
    return function( props ) {
        if ( ! ALLOWED_BLOCKS.includes( props.name ) ) {
            return el( BlockEdit, props );
        }

        const attributes = props.attributes;
        const setAttributes = props.setAttributes;

        // Trigger UI ONLY if the class 'query-loop-swiper-js' is present
        const hasTriggerClass = attributes.className && attributes.className.includes( 'query-loop-swiper-js' );

        return el( Fragment, {},
            el( BlockEdit, props ),
            hasTriggerClass ? el( InspectorControls, {},
                el( PanelBody, { title: 'Swiper Slider Options', initialOpen: true },
                    
                    // Breakpoints
                    el( TextControl, { label: 'Slides Per View (Desktop)', value: attributes.swiperSlidesDesktop, onChange: function( val ) { setAttributes( { swiperSlidesDesktop: val } ); } } ),
                    el( TextControl, { label: 'Slides Per View (Tablet)', value: attributes.swiperSlidesTablet, onChange: function( val ) { setAttributes( { swiperSlidesTablet: val } ); } } ),
                    el( TextControl, { label: 'Slides Per View (Mobile)', value: attributes.swiperSlidesMobile, onChange: function( val ) { setAttributes( { swiperSlidesMobile: val } ); } } ),
                    
                    // Spacing
                    el( TextControl, { label: 'Space Between (px)', value: attributes.swiperSpaceBetween, onChange: function( val ) { setAttributes( { swiperSpaceBetween: val } ); } } ),
                    
                    // Booleans (Toggles)
                    el( ToggleControl, { label: 'Enable Loop', checked: attributes.swiperLoop, onChange: function( val ) { setAttributes( { swiperLoop: val } ); } } ),
                    el( ToggleControl, { label: 'Enable Pagination (Dots)', checked: attributes.swiperPagination, onChange: function( val ) { setAttributes( { swiperPagination: val } ); } } ),
                    el( ToggleControl, { label: 'Enable Navigation (Arrows)', checked: attributes.swiperNavigation, onChange: function( val ) { setAttributes( { swiperNavigation: val } ); } } ),
                    el( ToggleControl, { label: 'Enable Autoplay', checked: attributes.swiperAutoplay, onChange: function( val ) { setAttributes( { swiperAutoplay: val } ); } } ),
                    
                    // Autoplay conditional field
                    attributes.swiperAutoplay ? el( TextControl, { label: 'Autoplay Delay (ms)', value: attributes.swiperDelay, onChange: function( val ) { setAttributes( { swiperDelay: val } ); } } ) : null

                )
            ) : null
        );
    };
}, 'addSwiperInspectorControls' );

addFilter( 'editor.BlockEdit', 'digitally-disruptive/swiper-ui', addSwiperInspectorControls );