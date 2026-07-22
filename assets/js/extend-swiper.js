/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects a Checkbox and Swiper Configuration Panel into Group, Grid, Row, and Query Loop blocks.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function( wp ) {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl } = wp.components;

    // Architectural whitelist for blocks capable of becoming Swiper instances
    const ALLOWED_BLOCKS = [ 'core/group', 'core/query' ];

    /**
     * 1. Register Universal Swiper Attributes
     */
    function addSwiperAttributes( settings, name ) {
        if ( ! ALLOWED_BLOCKS.includes( name ) ) return settings;

        settings.attributes = Object.assign( settings.attributes || {}, {
            isSwiperSlider:      { type: 'boolean', default: false },
            swiperSlidesDesktop: { type: 'string', default: '4' },
            swiperSlidesTablet:  { type: 'string', default: '2' },
            swiperSlidesMobile:  { type: 'string', default: '1' },
            swiperSpaceBetween:  { type: 'string', default: '20' },
            swiperLoop:          { type: 'boolean', default: true },
            swiperPagination:    { type: 'boolean', default: true },
            swiperNavigation:    { type: 'boolean', default: false },
            swiperAutoplay:      { type: 'boolean', default: false },
            swiperDelay:         { type: 'string', default: '3000' },
            swiperMobileOnly:    { type: 'boolean', default: false }
        });
        return settings;
    }
    addFilter( 'blocks.registerBlockType', 'digitally-disruptive/swiper-attrs', addSwiperAttributes );

    /**
     * 2. Inject the Toggle & Configuration UI into the Block Sidebar
     */
    const addSwiperUI = createHigherOrderComponent( function( BlockEdit ) {
        return function( props ) {
            if ( ! ALLOWED_BLOCKS.includes( props.name ) ) return el( BlockEdit, props );

            const { attributes, setAttributes } = props;

            return el( Fragment, {},
                el( BlockEdit, props ),
                el( InspectorControls, {},
                    el( PanelBody, { title: 'Swiper Slider Configuration', initialOpen: false },
                        
                        // The Master Toggle
                        el( ToggleControl, {
                            label: 'Is Swiper slider?',
                            help: 'Converts this block into a dynamic Swiper carousel.',
                            checked: attributes.isSwiperSlider,
                            onChange: function( val ) { setAttributes( { isSwiperSlider: val } ); }
                        } ),

                        // Render configuration fields ONLY if the toggle is checked
                        attributes.isSwiperSlider ? el( Fragment, {},
                            el( ToggleControl, {
                                label: 'Mobile Only (grid on desktop)',
                                help: 'Below 768px this becomes a Swiper carousel; at 768px and up it renders as a normal grid.',
                                checked: attributes.swiperMobileOnly,
                                onChange: function( val ) { setAttributes( { swiperMobileOnly: val } ); }
                            } ),
                            el( TextControl, {
                                label: 'Slides Per View (Desktop)',
                                type: 'number',
                                value: attributes.swiperSlidesDesktop,
                                onChange: function( val ) { setAttributes( { swiperSlidesDesktop: val } ); }
                            } ),
                            el( TextControl, {
                                label: 'Slides Per View (Tablet)',
                                type: 'number',
                                value: attributes.swiperSlidesTablet,
                                onChange: function( val ) { setAttributes( { swiperSlidesTablet: val } ); }
                            } ),
                            el( TextControl, {
                                label: 'Slides Per View (Mobile)',
                                type: 'number',
                                value: attributes.swiperSlidesMobile,
                                onChange: function( val ) { setAttributes( { swiperSlidesMobile: val } ); }
                            } ),
                            el( TextControl, {
                                label: 'Space Between Slides (px)',
                                type: 'number',
                                value: attributes.swiperSpaceBetween,
                                onChange: function( val ) { setAttributes( { swiperSpaceBetween: val } ); }
                            } ),
                            el( ToggleControl, {
                                label: 'Enable Loop',
                                checked: attributes.swiperLoop,
                                onChange: function( val ) { setAttributes( { swiperLoop: val } ); }
                            } ),
                            el( ToggleControl, {
                                label: 'Enable Pagination (Dots)',
                                checked: attributes.swiperPagination,
                                onChange: function( val ) { setAttributes( { swiperPagination: val } ); }
                            } ),
                            el( ToggleControl, {
                                label: 'Enable Navigation (Arrows)',
                                checked: attributes.swiperNavigation,
                                onChange: function( val ) { setAttributes( { swiperNavigation: val } ); }
                            } ),
                            el( ToggleControl, {
                                label: 'Enable Autoplay',
                                checked: attributes.swiperAutoplay,
                                onChange: function( val ) { setAttributes( { swiperAutoplay: val } ); }
                            } ),
                            attributes.swiperAutoplay ? el( TextControl, {
                                label: 'Autoplay Delay (ms)',
                                type: 'number',
                                value: attributes.swiperDelay,
                                onChange: function( val ) { setAttributes( { swiperDelay: val } ); }
                            } ) : null
                        ) : null
                    )
                )
            );
        };
    }, 'addSwiperUI' );
    addFilter( 'editor.BlockEdit', 'digitally-disruptive/swiper-ui', addSwiperUI );
})( window.wp );