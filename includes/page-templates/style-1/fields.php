<?php
/**
 * Enterprise Page Custom Flexible Content Fields
 *
 * Drop this file anywhere in your theme (e.g. /inc/page-fields.php)
 * and add the following to functions.php:
 *
 *   require_once get_template_directory() . '/inc/page-fields.php';
 */

if ( ! class_exists( 'Page_Template_Style_1_Fields' ) ) :

class Page_Template_Style_1_Fields {

    private static $instance = null;

    public static function init() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'add_meta_boxes',          [ $this, 'add_meta_box' ] );
        add_action( 'save_post',               [ $this, 'save_meta' ], 10, 2 );
        add_action( 'admin_enqueue_scripts',   [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_pts1_get_post_titles', [ $this, 'ajax_get_post_titles' ] );
    }

    // =========================================================================
    // LAYOUT DEFINITIONS
    // =========================================================================

    public function get_layouts() {
        return [
            'hero' => [
                'label' => '🦸 Hero',
                'fields' => [
                    [ 'name' => 'bg_image_id',  'label' => 'Background Image', 'type' => 'image'    ],
                    [ 'name' => 'heading',       'label' => 'Heading',          'type' => 'text'     ],
                    [ 'name' => 'description',   'label' => 'Description',      'type' => 'textarea' ],
                    [ 'name' => 'buttons', 'label' => 'Buttons', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text',  'label' => 'Button Text',  'type' => 'text'   ],
                        [ 'name' => 'url',   'label' => 'Button URL',   'type' => 'url'    ],
                        [ 'name' => 'style', 'label' => 'Style',        'type' => 'select', 'options' => [
                            'button-accent'   => 'Accent (blue)',
                            'button-bordered' => 'Bordered (outline)',
                        ]],
                    ]],
                ],
            ],

            'usp' => [
                'label' => '⭐ USP Bar',
                'fields' => [
                    [ 'name' => 'items', 'label' => 'USP Items', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'icon',        'label' => 'Icon',        'type' => 'select', 'options' => [
                            'dot'  => '• Dot (blue)',
                            'star' => '★ Star (green)',
                        ]],
                        [ 'name' => 'bold_text',   'label' => 'Bold Text',   'type' => 'text' ],
                        [ 'name' => 'normal_text', 'label' => 'Normal Text', 'type' => 'text' ],
                    ]],
                ],
            ],

            'two_columns' => [
                'label' => '📐 Two Columns',
                'fields' => [
                    [ 'name' => 'heading',         'label' => 'Section Heading', 'type' => 'text'     ],
                    [ 'name' => 'image_id',         'label' => 'Image',           'type' => 'image'    ],
                    [ 'name' => 'content_heading',  'label' => 'Content Heading', 'type' => 'text'     ],
                    [ 'name' => 'content',          'label' => 'Content',         'type' => 'wysiwyg'  ],
                    [ 'name' => 'btn_text',         'label' => 'Button Text',     'type' => 'text'     ],
                    [ 'name' => 'btn_url',          'label' => 'Button URL',      'type' => 'url'      ],
                ],
            ],

            'number_box' => [
                'label' => '🔢 Number Box Steps',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'    ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'wysiwyg' ],
                    [ 'name' => 'items', 'label' => 'Steps', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'label', 'label' => 'Label', 'type' => 'text' ],
                    ]],
                ],
            ],

            'checklist_dark' => [
                'label' => '✅ Checklist (Dark)',
                'fields' => [
                    [ 'name' => 'heading',  'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'intro',    'label' => 'Intro Text',  'type' => 'textarea' ],
                    [ 'name' => 'items', 'label' => 'Checklist Items', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Item Text', 'type' => 'text' ],
                    ]],
                    [ 'name' => 'outro',    'label' => 'Outro Text',  'type' => 'textarea' ],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text'     ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'      ],
                ],
            ],

            'guides' => [
                'label' => '📚 Guides',
                'fields' => [
                    [ 'name' => 'heading',       'label' => 'Heading',       'type' => 'text'     ],
                    [ 'name' => 'description',   'label' => 'Description',   'type' => 'textarea' ],
                    [ 'name' => 'section_label', 'label' => 'Section Label', 'type' => 'text'     ],
                    [ 'name' => 'items', 'label' => 'Guides', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'image_id',   'label' => 'Thumbnail',   'type' => 'image' ],
                        [ 'name' => 'name',       'label' => 'Guide Name',  'type' => 'text'  ],
                        [ 'name' => 'url',        'label' => 'Guide URL',   'type' => 'url'   ],
                        [ 'name' => 'link_label', 'label' => 'Link Label',  'type' => 'text'  ],
                    ]],
                ],
            ],

            'chip' => [
                'label' => '🔴 Chip Cards (Warning)',
                'fields' => [
                    [ 'name' => 'heading',      'label' => 'Heading',       'type' => 'text'         ],
                    [ 'name' => 'description',  'label' => 'Description',   'type' => 'text'         ],
                    [ 'name' => 'border_color', 'label' => 'Border Colour', 'type' => 'color_picker', 'default' => '#FF0E0E' ],
                    [ 'name' => 'text_color',   'label' => 'Text Colour',   'type' => 'color_picker', 'default' => '#FF0E0E' ],
                    [ 'name' => 'items', 'label' => 'Chip Items', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Text', 'type' => 'text' ],
                    ]],
                    [ 'name' => 'outro',    'label' => 'Outro Text',  'type' => 'text' ],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text' ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'  ],
                ],
            ],

            'industries' => [
                'label' => '🏭 Industries',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                    [ 'name' => 'items', 'label' => 'Industry Cards', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'image_id',    'label' => 'Image',       'type' => 'image'    ],
                        [ 'name' => 'title',       'label' => 'Title',       'type' => 'text'     ],
                        [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                        [ 'name' => 'url',         'label' => 'Link URL',    'type' => 'url'      ],
                    ]],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text' ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'  ],
                ],
            ],

            'cta' => [
                'label' => '📣 CTA (with bg image)',
                'fields' => [
                    [ 'name' => 'bg_image_id',  'label' => 'Background Image', 'type' => 'image'    ],
                    [ 'name' => 'heading',       'label' => 'Heading',          'type' => 'text'     ],
                    [ 'name' => 'description',   'label' => 'Description',      'type' => 'textarea' ],
                    [ 'name' => 'btn_text',      'label' => 'Button Text',      'type' => 'text'     ],
                    [ 'name' => 'btn_url',       'label' => 'Button URL',       'type' => 'url'      ],
                ],
            ],

            'case_study' => [
                'label' => '📋 Case Studies',
                'fields' => [
                    [ 'name' => 'heading',  'label' => 'Heading',  'type' => 'text'     ],
                    [ 'name' => 'post_ids', 'label' => 'Posts',    'type' => 'post_ids' ],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text' ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'  ],
                ],
            ],

            'products' => [
                'label' => '🛒 Products Slider',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                    [ 'name' => 'sub_label',   'label' => 'Sub-label',   'type' => 'text'     ],
                    [ 'name' => 'product_ids', 'label' => 'Products',    'type' => 'post_ids', 'post_type' => 'product' ],
                    [ 'name' => 'btn_text',    'label' => 'Button Text', 'type' => 'text'     ],
                    [ 'name' => 'btn_url',     'label' => 'Button URL',  'type' => 'url'      ],
                ],
            ],

            'cta_simple' => [
                'label' => '📣 CTA Simple',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                    [ 'name' => 'btn_text',    'label' => 'Button Text', 'type' => 'text'     ],
                    [ 'name' => 'btn_url',     'label' => 'Button URL',  'type' => 'url'      ],
                ],
            ],

            'testimonials' => [
                'label' => '💬 Testimonials',
                'fields' => [
                    [ 'name' => 'heading',  'label' => 'Heading',     'type' => 'text' ],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text' ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'  ],
                ],
            ],

            'logos' => [
                'label' => '🖼 Logos / Partners',
                'fields' => [
                    [ 'name' => 'label',     'label' => 'Label',             'type' => 'text' ],
                    [ 'name' => 'shortcode', 'label' => 'Marquee Shortcode', 'type' => 'text', 'description' => 'e.g. [dd_logo_marquee id=417707]' ],
                ],
            ],

            'checklist_light' => [
                'label' => '✅ Checklist (Light)',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                    [ 'name' => 'items', 'label' => 'Checklist Items', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Item Text', 'type' => 'text' ],
                    ]],
                ],
            ],

            'chip_v2' => [
                'label' => '🔵 Chip Cards V2 (Dark bg)',
                'fields' => [
                    [ 'name' => 'heading',    'label' => 'Heading',            'type' => 'text'     ],
                    [ 'name' => 'subheading', 'label' => 'Subheading',         'type' => 'text'     ],
                    [ 'name' => 'description','label' => 'Description',        'type' => 'textarea' ],
                    [ 'name' => 'col_label',  'label' => 'Left Column Label',  'type' => 'text'     ],
                    [ 'name' => 'items', 'label' => 'Proof Points', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Text (HTML allowed)', 'type' => 'text' ],
                    ]],
                    [ 'name' => 'btn_text', 'label' => 'Button Text', 'type' => 'text' ],
                    [ 'name' => 'btn_url',  'label' => 'Button URL',  'type' => 'url'  ],
                ],
            ],

            'number_box_v2' => [
                'label' => '🔢 Number Box V2 (smaller)',
                'fields' => [
                    [ 'name' => 'heading', 'label' => 'Heading', 'type' => 'text' ],
                    [ 'name' => 'items', 'label' => 'Steps', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Step Description', 'type' => 'text' ],
                    ]],
                    [ 'name' => 'footer_note', 'label' => 'Footer Note', 'type' => 'text' ],
                ],
            ],

            'chip_v3' => [
                'label' => '🔴 Chip Cards V3 (Compact)',
                'fields' => [
                    [ 'name' => 'heading',      'label' => 'Heading',       'type' => 'text'         ],
                    [ 'name' => 'border_color', 'label' => 'Border Colour', 'type' => 'color_picker', 'default' => '#FF0E0E' ],
                    [ 'name' => 'text_color',   'label' => 'Text Colour',   'type' => 'color_picker', 'default' => '#FF0E0E' ],
                    [ 'name' => 'items', 'label' => 'Items', 'type' => 'repeater', 'sub_fields' => [
                        [ 'name' => 'text', 'label' => 'Text', 'type' => 'text' ],
                    ]],
                ],
            ],

            'cta_full_width' => [
                'label' => '📣 CTA Full Width',
                'fields' => [
                    [ 'name' => 'heading',     'label' => 'Heading',     'type' => 'text'     ],
                    [ 'name' => 'description', 'label' => 'Description', 'type' => 'textarea' ],
                    [ 'name' => 'btn_text',    'label' => 'Button Text', 'type' => 'text'     ],
                    [ 'name' => 'btn_url',     'label' => 'Button URL',  'type' => 'url'      ],
                ],
            ],
        ];
    }

    // =========================================================================
    // SCRIPTS & STYLES
    // =========================================================================

    public function enqueue_scripts( $hook ) {
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ] ) ) return;

        $post_id = absint( $_GET['post'] ?? 0 );
        if ( $post_id && get_page_template_slug( $post_id ) !== 'page-template-style-1.php' ) return;

        wp_enqueue_media();
        wp_enqueue_script( 'jquery-ui-sortable' );

        wp_enqueue_style(
            'pts1-admin',
            get_template_directory_uri() . '/assets/css/page-admin.css',
            [],
            '1.0'
        );

        wp_enqueue_script(
            'pts1-admin',
            get_template_directory_uri() . '/assets/js/page-admin.js',
            [ 'jquery', 'jquery-ui-sortable' ],
            '1.0',
            true
        );

        wp_localize_script( 'pts1-admin', 'pts1Admin', [
            'nonce'   => wp_create_nonce( 'pts1_admin_nonce' ),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ] );
    }

    // =========================================================================
    // META BOX
    // =========================================================================

    public function add_meta_box() {
        add_meta_box(
            'pts1_page_sections',
            'Page Sections',
            [ $this, 'render_meta_box' ],
            'page',
            'normal',
            'high'
        );
    }

    public function render_meta_box( $post ) {
        if ( get_page_template_slug( $post->ID ) !== 'page-template-style-1.php' ) {
            echo '<p style="color:#888;font-style:italic">This meta box is only active on the <strong>Page Enterprise</strong> template.</p>';
            return;
        }

        wp_nonce_field( 'pts1_save_sections', 'ep_nonce' );

        $sections = pts1_get_sections( $post->ID );
        $layouts  = $this->get_layouts();
        ?>
        <div id="ep-flexible-content" class="ep-flexible-content">

            <div id="ep-sections-list" class="ep-sections-list">
                <?php
                foreach ( $sections as $index => $section ) {
                    $this->render_section_row( $section['layout'], $index, $section );
                }
                ?>
            </div>

            <div class="ep-add-section-wrap">
                <button type="button" class="ep-add-section-btn button button-primary">
                    <span>＋ Add Section</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="ep-layout-picker" style="display:none">
                    <div class="ep-layout-picker-grid">
                        <?php foreach ( $layouts as $key => $layout ) : ?>
                            <button type="button" class="ep-add-layout-btn" data-layout="<?= esc_attr( $key ) ?>">
                                <?= esc_html( $layout['label'] ) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div><!-- #ep-flexible-content -->

        <!-- JS Templates — hidden, cloned on demand -->
        <div id="ep-layout-templates" style="display:none" aria-hidden="true">
            <?php foreach ( $layouts as $key => $layout ) : ?>
                <div class="ep-tpl" id="ep-tpl-<?= esc_attr( $key ) ?>">
                    <?php $this->render_section_row( $key, '__IDX__', [] ); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    // =========================================================================
    // RENDER HELPERS
    // =========================================================================

    /**
     * Render one full section row (used both for saved data and JS templates).
     */
    private function render_section_row( $layout_key, $index, $data ) {
        $layouts = $this->get_layouts();
        $layout  = $layouts[ $layout_key ] ?? null;
        if ( ! $layout ) return;

        $title = '';
        if ( isset( $data['heading'] ) && $data['heading'] ) {
            $title = $data['heading'];
        } elseif ( isset( $data['label'] ) && $data['label'] ) {
            $title = $data['label'];
        }
        ?>
        <div class="ep-section-row" data-layout="<?= esc_attr( $layout_key ) ?>">
            <input type="hidden"
                   name="sections[<?= $index ?>][layout]"
                   value="<?= esc_attr( $layout_key ) ?>">

            <div class="ep-section-header">
                <span class="ep-handle" title="Drag to reorder">⠿</span>
                <span class="ep-layout-badge"><?= esc_html( $layout['label'] ) ?></span>
                <span class="ep-section-title"><?= esc_html( $title ) ?></span>
                <div class="ep-section-actions">
                    <button type="button" class="ep-btn-icon ep-btn-up"   title="Move up">↑</button>
                    <button type="button" class="ep-btn-icon ep-btn-down" title="Move down">↓</button>
                    <button type="button" class="ep-btn-icon ep-btn-toggle" title="Collapse">−</button>
                    <button type="button" class="ep-btn-icon ep-btn-remove" title="Remove section">✕</button>
                </div>
            </div>

            <div class="ep-section-body">
                <?php foreach ( $layout['fields'] as $field ) :
                    $value = $data[ $field['name'] ] ?? null;
                    $this->render_field_row( $index, $field, $value );
                endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render one labelled field row.
     */
    private function render_field_row( $section_index, $field, $value ) {
        $name_prefix = "sections[{$section_index}]";
        ?>
        <div class="ep-field-row<?= $field['type'] === 'repeater' ? ' ep-field-row--repeater' : '' ?>">
            <div class="ep-field-label">
                <?= esc_html( $field['label'] ) ?>
            </div>
            <div class="ep-field-input">
                <?php if ( $field['type'] === 'repeater' ) : ?>
                    <?php $this->render_repeater( $name_prefix, $field, is_array( $value ) ? $value : [] ); ?>
                <?php else : ?>
                    <?php $this->render_single_field( $name_prefix, $field, $value ); ?>
                <?php endif; ?>
                <?php if ( ! empty( $field['description'] ) ) : ?>
                    <p class="ep-field-desc"><?= esc_html( $field['description'] ) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a single (non-repeater) field input.
     */
    private function render_single_field( $name_prefix, $field, $value ) {
        $name = $name_prefix . '[' . $field['name'] . ']';

        switch ( $field['type'] ) {

            case 'text':
            case 'url':
                printf(
                    '<input type="text" name="%s" value="%s" class="widefat ep-input ep-heading-source">',
                    esc_attr( $name ),
                    esc_attr( $value ?? '' )
                );
                break;

            case 'textarea':
            case 'wysiwyg':
                printf(
                    '<textarea name="%s" class="widefat ep-input" rows="4">%s</textarea>',
                    esc_attr( $name ),
                    esc_textarea( $value ?? '' )
                );
                if ( $field['type'] === 'wysiwyg' ) {
                    echo '<p class="ep-field-desc">HTML is supported.</p>';
                }
                break;

            case 'select':
                echo '<select name="' . esc_attr( $name ) . '" class="widefat ep-input">';
                foreach ( $field['options'] as $opt_val => $opt_label ) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr( $opt_val ),
                        selected( $value, $opt_val, false ),
                        esc_html( $opt_label )
                    );
                }
                echo '</select>';
                break;

            case 'image':
                $img_id  = intval( $value ?? 0 );
                $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : '';
                $btn_lbl = $img_id ? 'Change Image' : 'Upload Image';
                ?>
                <div class="ep-image-field">
                    <input type="hidden"
                           name="<?= esc_attr( $name ) ?>"
                           value="<?= esc_attr( $img_id ) ?>"
                           class="ep-image-id">
                    <div class="ep-image-preview">
                        <?php if ( $img_url ) : ?>
                            <img src="<?= esc_url( $img_url ) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="ep-image-actions">
                        <button type="button" class="button ep-upload-image"><?= esc_html( $btn_lbl ) ?></button>
                        <button type="button"
                                class="button ep-remove-image"
                                <?= $img_id ? '' : 'style="display:none"' ?>>Remove</button>
                    </div>
                </div>
                <?php
                break;

            case 'color_picker':
                $default = $field['default'] ?? '#000000';
                $current = $value ?? $default;
                ?>
                <div class="ep-color-field">
                    <span class="ep-color-swatch" style="background:<?= esc_attr( $current ) ?>"></span>
                    <input type="text"
                           name="<?= esc_attr( $name ) ?>"
                           value="<?= esc_attr( $current ) ?>"
                           class="ep-input ep-color-input"
                           placeholder="<?= esc_attr( $default ) ?>">
                    <p class="ep-field-desc">Enter any valid CSS colour value (hex, rgba, etc.)</p>
                </div>
                <?php
                break;

            case 'post_ids':
                $post_type = $field['post_type'] ?? 'any';
                $ids_raw   = is_array( $value ) ? implode( ', ', $value ) : ( $value ?? '' );
                // Build a preview of current titles
                $id_arr    = array_filter( array_map( 'intval', explode( ',', $ids_raw ) ) );
                ?>
                <div class="ep-post-ids-field" data-post-type="<?= esc_attr( $post_type ) ?>">
                    <input type="text"
                           name="<?= esc_attr( $name ) ?>"
                           value="<?= esc_attr( $ids_raw ) ?>"
                           class="widefat ep-input ep-post-ids-input"
                           placeholder="Enter comma-separated post IDs, e.g. 123, 456, 789">
                    <p class="ep-field-desc">
                        Comma-separated IDs. Post type: <code><?= esc_html( $post_type ) ?></code>
                    </p>
                    <div class="ep-post-ids-preview">
                        <?php if ( $id_arr ) : ?>
                            <ul>
                                <?php foreach ( $id_arr as $pid ) :
                                    $t = get_the_title( $pid );
                                    if ( $t ) : ?>
                                        <li><span class="ep-post-id">#<?= esc_html( $pid ) ?></span> <?= esc_html( $t ) ?></li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                break;
        }
    }

    /**
     * Render a repeater field with its rows + a hidden template row.
     */
    private function render_repeater( $name_prefix, $field, $rows ) {
        $rep_prefix = $name_prefix . '[' . $field['name'] . ']';
        ?>
        <div class="ep-repeater" data-repeater="<?= esc_attr( $field['name'] ) ?>">
            <div class="ep-repeater-rows">
                <?php foreach ( $rows as $row_index => $row ) :
                    $this->render_repeater_row( $rep_prefix, $field['sub_fields'], $row_index, $row );
                endforeach; ?>
            </div>

            <!-- hidden template row — __RIDX__ is replaced by JS -->
            <div class="ep-repeater-template" style="display:none">
                <?php $this->render_repeater_row( $rep_prefix, $field['sub_fields'], '__RIDX__', [] ); ?>
            </div>

            <button type="button" class="button ep-add-row">
                ＋ Add <?= esc_html( $field['label'] ) ?>
            </button>
        </div>
        <?php
    }

    /**
     * Render a single repeater row.
     */
    private function render_repeater_row( $rep_prefix, $sub_fields, $row_index, $row_data ) {
        $row_prefix = $rep_prefix . '[' . $row_index . ']';
        ?>
        <div class="ep-repeater-row">
            <div class="ep-row-handle" title="Drag to reorder">⠿</div>
            <div class="ep-row-fields">
                <?php foreach ( $sub_fields as $sub ) :
                    $val = $row_data[ $sub['name'] ] ?? null;
                    ?>
                    <div class="ep-sub-field">
                        <label class="ep-sub-label"><?= esc_html( $sub['label'] ) ?></label>
                        <?php $this->render_single_field( $row_prefix, $sub, $val ); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="ep-btn-icon ep-remove-row" title="Remove row">✕</button>
        </div>
        <?php
    }

    // =========================================================================
    // SAVE
    // =========================================================================

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['ep_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['ep_nonce'], 'pts1_save_sections' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $raw     = $_POST['sections'] ?? [];
        $layouts = $this->get_layouts();

        if ( ! is_array( $raw ) ) {
            update_post_meta( $post_id, '_pts1_sections', '[]' );
            return;
        }

        // Sort by numeric key so drag-and-drop order is respected.
        ksort( $raw, SORT_NUMERIC );

        $sections = [];
        foreach ( $raw as $section_data ) {
            $layout_key = sanitize_text_field( $section_data['layout'] ?? '' );
            if ( ! $layout_key || ! isset( $layouts[ $layout_key ] ) ) continue;

            $layout    = $layouts[ $layout_key ];
            $processed = [ 'layout' => $layout_key ];

            foreach ( $layout['fields'] as $field ) {
                $processed[ $field['name'] ] = $this->sanitize_field(
                    $field,
                    $section_data[ $field['name'] ] ?? null
                );
            }

            $sections[] = $processed;
        }

        update_post_meta( $post_id, '_pts1_sections', wp_json_encode( $sections ) );
    }

    private function sanitize_field( $field, $value ) {
        if ( $field['type'] === 'repeater' ) {
            if ( ! is_array( $value ) ) return [];
            ksort( $value, SORT_NUMERIC );
            $rows = [];
            foreach ( $value as $row ) {
                // Skip rows where every text-like sub-field is blank.
                $has_content = false;
                foreach ( $field['sub_fields'] as $sub ) {
                    $v = $row[ $sub['name'] ] ?? '';
                    if ( in_array( $sub['type'], [ 'text', 'url', 'textarea', 'wysiwyg' ], true ) && trim( (string) $v ) !== '' ) {
                        $has_content = true;
                        break;
                    }
                    if ( $sub['type'] === 'image' && intval( $v ) > 0 ) {
                        $has_content = true;
                        break;
                    }
                }
                if ( ! $has_content ) continue;

                $processed_row = [];
                foreach ( $field['sub_fields'] as $sub ) {
                    $processed_row[ $sub['name'] ] = $this->sanitize_single( $sub, $row[ $sub['name'] ] ?? null );
                }
                $rows[] = $processed_row;
            }
            return $rows;
        }

        if ( $field['type'] === 'post_ids' ) {
            $ids = explode( ',', (string) ( $value ?? '' ) );
            return array_values( array_filter( array_map( 'intval', $ids ) ) );
        }

        return $this->sanitize_single( $field, $value );
    }

    private function sanitize_single( $field, $value ) {
        switch ( $field['type'] ) {
            case 'url':
                return esc_url_raw( $value ?? '' );
            case 'image':
                return absint( $value ?? 0 );
            case 'wysiwyg':
                return wp_kses_post( $value ?? '' );
            case 'color_picker':
                return sanitize_text_field( $value ?? ( $field['default'] ?? '' ) );
            default:
                return sanitize_text_field( $value ?? '' );
        }
    }

    // =========================================================================
    // AJAX
    // =========================================================================

    public function ajax_get_post_titles() {
        check_ajax_referer( 'pts1_admin_nonce', 'nonce' );
        $ids    = array_filter( array_map( 'absint', (array) ( $_POST['ids'] ?? [] ) ) );
        $result = [];
        foreach ( $ids as $id ) {
            $title = get_the_title( $id );
            if ( $title ) $result[ $id ] = $title;
        }
        wp_send_json_success( $result );
    }
}

Page_Template_Style_1_Fields::init();

endif; // class_exists

// =============================================================================
// FRONTEND HELPER FUNCTIONS
// =============================================================================

/**
 * Get all sections for a post.
 */
function pts1_get_sections( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    $json    = get_post_meta( $post_id, '_pts1_sections', true );
    return ( $json && $json !== '' ) ? json_decode( $json, true ) : [];
}

/**
 * Get a plain field value from the current section.
 */
function pts1_field( $section, $key, $default = '' ) {
    return $section[ $key ] ?? $default;
}

/**
 * Get an image as an associative array from an attachment ID stored in a section.
 * Returns null if no image.
 */
function pts1_img( $section, $key, $size = 'full' ) {
    $id = absint( $section[ $key ] ?? 0 );
    if ( ! $id ) return null;
    $src = wp_get_attachment_image_src( $id, $size );
    if ( ! $src ) return null;
    return [
        'id'     => $id,
        'url'    => $src[0],
        'width'  => $src[1],
        'height' => $src[2],
        'alt'    => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
    ];
}

/**
 * Get a repeater field as an array of rows.
 */
function pts1_repeater( $section, $key ) {
    return is_array( $section[ $key ] ?? null ) ? $section[ $key ] : [];
}

/**
 * Get post IDs stored in a post_ids field.
 */
function pts1_post_ids( $section, $key ) {
    $val = $section[ $key ] ?? [];
    if ( is_array( $val ) ) return array_filter( array_map( 'intval', $val ) );
    return array_filter( array_map( 'intval', explode( ',', (string) $val ) ) );
}