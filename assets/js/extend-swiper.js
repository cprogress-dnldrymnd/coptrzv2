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
    const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl, Button, BaseControl } = wp.components;

    // Architectural whitelist for blocks capable of becoming Swiper instances
    const ALLOWED_BLOCKS = [ 'core/group', 'core/query' ];

    /**
     * MediaUpload row for a single nav icon (prev or next).
     */
    function navIconControl( label, iconId, iconUrl, idAttr, urlAttr, setAttributes ) {
        return el( BaseControl, { label: label, className: 'dd-swiper-nav-icon' },
            el( MediaUploadCheck, null,
                el( MediaUpload, {
                    allowedTypes: [ 'image/svg+xml' ],
                    value: iconId || 0,
                    onSelect: function( media ) {
                        var attrs = {};
                        attrs[ idAttr ] = media.id;
                        attrs[ urlAttr ] = media.url;
                        setAttributes( attrs );
                    },
                    render: function( o ) {
                        return el( Button, { variant: 'secondary', onClick: o.open },
                            iconId ? 'Replace SVG' : 'Select SVG'
                        );
                    }
                } )
            ),
            iconUrl ? el( 'img', {
                src: iconUrl,
                alt: '',
                style: { display: 'block', width: '32px', height: '32px', marginTop: '8px', objectFit: 'contain' }
            } ) : null,
            iconId ? el( Button, {
                variant: 'link',
                isDestructive: true,
                style: { marginTop: '8px', display: 'block' },
                onClick: function() {
                    var attrs = {};
                    attrs[ idAttr ] = 0;
                    attrs[ urlAttr ] = '';
                    setAttributes( attrs );
                }
            }, 'Remove' ) : null
        );
    }

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
            swiperMobileOnly:    { type: 'boolean', default: false },
            swiperNavPrevIconId:  { type: 'number', default: 0 },
            swiperNavPrevIconUrl: { type: 'string', default: '' },
            swiperNavNextIconId:  { type: 'number', default: 0 },
            swiperNavNextIconUrl: { type: 'string', default: '' }
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
                            attributes.swiperNavigation ? el( Fragment, {},
                                navIconControl(
                                    'Previous Icon (SVG)',
                                    attributes.swiperNavPrevIconId,
                                    attributes.swiperNavPrevIconUrl,
                                    'swiperNavPrevIconId',
                                    'swiperNavPrevIconUrl',
                                    setAttributes
                                ),
                                navIconControl(
                                    'Next Icon (SVG)',
                                    attributes.swiperNavNextIconId,
                                    attributes.swiperNavNextIconUrl,
                                    'swiperNavNextIconId',
                                    'swiperNavNextIconUrl',
                                    setAttributes
                                )
                            ) : null,
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
