<?php
/**
 * Plugin Name: DD Perfect Logo Marquee
 * Description: A hardware-accelerated, responsive infinite logo marquee using a grouped Custom Post Type and native gallery selection.
 * Version: 1.1.1
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 * Text Domain: dd-logo-marquee
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class DD_Logo_Marquee {

    /**
     * Initializes the plugin by hooking into WordPress core actions.
     * * Registers the Custom Post Type, meta boxes, admin scripts for the gallery,
     * enqueues front-end styles, and registers the shortcode.
     * * @return void
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_dd_marquee_group', [ $this, 'save_meta_box' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
        add_shortcode( 'dd_logo_marquee', [ $this, 'render_shortcode' ] );
    }

    /**
     * Registers the 'dd_marquee_group' Custom Post Type.
     * * Configures a dedicated CPT for managing groups of logos. It supports 'title'
     * only, as the images are handled via the custom gallery meta box.
     * * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'               => 'Marquee Groups',
            'singular_name'      => 'Marquee Group',
            'menu_name'          => 'Marquee Groups',
            'add_new'            => 'Add New Group',
            'add_new_item'       => 'Add New Marquee Group',
            'edit_item'          => 'Edit Group',
            'new_item'           => 'New Group',
            'view_item'          => 'View Group',
            'search_items'       => 'Search Groups',
            'not_found'          => 'No groups found',
            'not_found_in_trash' => 'No groups found in Trash',
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false, // Keeps URLs private
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-images-alt2',
            'supports'            => [ 'title' ],
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => false, // Disabled REST API to force classic meta box rendering
        ];

        register_post_type( 'dd_marquee_group', $args );
    }

    /**
     * Registers the custom meta box for the gallery selection.
     * * @return void
     */
    public function add_meta_boxes() {
        add_meta_box(
            'dd_marquee_gallery_meta',
            'Marquee Logos (Gallery)',
            [ $this, 'render_gallery_meta_box' ],
            'dd_marquee_group',
            'normal',
            'high'
        );
    }

    /**
     * Renders the HTML for the gallery selection meta box.
     * * Retrieves existing saved image IDs, outputs a hidden input for data submission,
     * and constructs the visual preview area with management buttons.
     * * @param WP_Post $post The current post object.
     * @return void
     */
    public function render_gallery_meta_box( $post ) {
        wp_nonce_field( 'dd_save_marquee_gallery', 'dd_marquee_gallery_nonce' );
        
        $image_ids = get_post_meta( $post->ID, '_dd_marquee_image_ids', true );
        
        echo '<div id="dd_gallery_container">';
        echo '<input type="hidden" id="dd_marquee_image_ids" name="dd_marquee_image_ids" value="' . esc_attr( $image_ids ) . '" />';
        
        echo '<div id="dd_gallery_preview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">';
        if ( ! empty( $image_ids ) ) {
            $ids_array = explode( ',', $image_ids );
            foreach ( $ids_array as $id ) {
                $image_url = wp_get_attachment_image_url( $id, 'thumbnail' );
                if ( $image_url ) {
                    echo '<img src="' . esc_url( $image_url ) . '" style="max-width: 80px; height: auto; border: 1px solid #ccc; padding: 2px;" />';
                }
            }
        }
        echo '</div>';
        
        echo '<button class="button button-primary" id="dd_add_gallery_images">Select Logos</button> ';
        echo '<button class="button" id="dd_clear_gallery_images">Clear Gallery</button>';
        echo '<p class="description">Select multiple images to form your logo marquee group. Hold CTRL/CMD to select multiple.</p>';
        echo '</div>';
    }

    /**
     * Saves the gallery image IDs to the post meta.
     * * Verifies nonces, checks user permissions, and sanitizes the comma-separated
     * list of attachment IDs before saving to the database.
     * * @param int $post_id The ID of the post being saved.
     * @return void
     */
    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['dd_marquee_gallery_nonce'] ) || ! wp_verify_nonce( $_POST['dd_marquee_gallery_nonce'], 'dd_save_marquee_gallery' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['dd_marquee_image_ids'] ) ) {
            $sanitized_ids = sanitize_text_field( wp_unslash( $_POST['dd_marquee_image_ids'] ) );
            update_post_meta( $post_id, '_dd_marquee_image_ids', $sanitized_ids );
        } else {
            delete_post_meta( $post_id, '_dd_marquee_image_ids' );
        }
    }

    /**
     * Enqueues the native WordPress media uploader and custom JS logic.
     * * Loads the required scripts only on the 'dd_marquee_group' post edit screens.
     * Now includes state hydration on the 'open' event to pre-select existing images
     * in the wp.media modal, preventing accidental gallery resets.
     * * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_admin_scripts( $hook ) {
        global $post_type;
        if ( ( 'post.php' !== $hook && 'post-new.php' !== $hook ) || 'dd_marquee_group' !== $post_type ) {
            return;
        }

        wp_enqueue_media();

        $js = "
        jQuery(document).ready(function($){
            var frame;
            $('#dd_add_gallery_images').on('click', function(e) {
                e.preventDefault();

                if ( frame ) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: 'Select Logos for Marquee',
                    button: { text: 'Use these logos' },
                    multiple: true
                });

                // Hydrate the modal selection state based on existing saved IDs
                frame.on('open', function() {
                    var selection = frame.state().get('selection');
                    var ids = $('#dd_marquee_image_ids').val();

                    if (ids) {
                        var idsArray = ids.split(',');
                        idsArray.forEach(function(id) {
                            var attachment = wp.media.attachment(id);
                            attachment.fetch(); // Ensure attachment data is loaded
                            selection.add(attachment ? [attachment] : []);
                        });
                    }
                });

                // Update the DOM and hidden input when new selections are confirmed
                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    var ids = [];
                    $('#dd_gallery_preview').empty();

                    attachments.forEach(function(attachment) {
                        ids.push(attachment.id);
                        var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        $('#dd_gallery_preview').append('<img src=\"' + imgUrl + '\" style=\"max-width: 80px; height: auto; border: 1px solid #ccc; padding: 2px;\" />');
                    });

                    $('#dd_marquee_image_ids').val(ids.join(','));
                });

                frame.open();
            });

            // Clear button functionality
            $('#dd_clear_gallery_images').on('click', function(e){
                e.preventDefault();
                $('#dd_marquee_image_ids').val('');
                $('#dd_gallery_preview').empty();
            });
        });";

        wp_register_script( 'dd-marquee-admin-js', false );
        wp_enqueue_script( 'dd-marquee-admin-js' );
        wp_add_inline_script( 'dd-marquee-admin-js', $js );
    }

    /**
     * Injects the required CSS styles into the front-end.
     * * Utilizes hardware-accelerated CSS transforms (`translate3d`) to prevent
     * sub-pixel rendering jitter. Registers an inline style block attached to
     * a core dummy handle to keep the plugin entirely self-contained.
     * * @return void
     */
    public function enqueue_styles() {
        $css = '
        .dd-marquee-container {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            display: flex;
            position: relative;
            box-sizing: border-box;
            background: transparent;
        }
        .dd-marquee-track {
            display: flex;
            width: max-content;
            animation: dd-marquee-scroll 30s linear infinite;
        }
        .dd-marquee-track:hover {
            animation-play-state: paused;
        }
        .dd-marquee-group {
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-shrink: 0;
            gap: 3rem;
            padding-right: 3rem; /* Critical: exact match to gap for seamless stitch */
        }
        .dd-marquee-item img {
            max-width: 160px;
            max-height: 80px;
            width: auto;
            height: auto;
            display: block;
            object-fit: contain;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }
        .dd-marquee-item img:hover {
            filter: grayscale(0%);
        }
        @keyframes dd-marquee-scroll {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }
        @media (max-width: 768px) {
            .dd-marquee-group {
                gap: 1.5rem;
                padding-right: 1.5rem;
            }
            .dd-marquee-item img {
                max-width: 100px;
                max-height: 60px;
            }
        }';

        wp_register_style( 'dd-marquee-style', false );
        wp_enqueue_style( 'dd-marquee-style' );
        wp_add_inline_style( 'dd-marquee-style', wp_strip_all_tags( $css ) );
    }

    /**
     * Generates the front-end HTML for the logo marquee via shortcode.
     * * Requires an 'id' attribute to target a specific marquee group. Retrieves
     * the attached gallery IDs and constructs a duplicated DOM group to achieve 
     * a seamless infinite CSS loop.
     * * @param array $atts User-defined shortcode attributes.
     * @return string Compiled HTML output for the marquee.
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => '', // Post ID of the marquee group
        ], $atts, 'dd_logo_marquee' );

        if ( empty( $atts['id'] ) ) {
            return '';
        }

        $image_ids_string = get_post_meta( intval( $atts['id'] ), '_dd_marquee_image_ids', true );

        if ( empty( $image_ids_string ) ) {
            return '';
        }

        $image_ids = explode( ',', $image_ids_string );

        ob_start();
        ?>
        <div class="dd-marquee-group">
            <?php foreach ( $image_ids as $attachment_id ) : ?>
                <?php $img_html = wp_get_attachment_image( $attachment_id, 'medium' ); ?>
                <?php if ( $img_html ) : ?>
                    <div class="dd-marquee-item">
                        <?php echo $img_html; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach ( $image_ids as $attachment_id ) : ?>
                <?php $img_html = wp_get_attachment_image( $attachment_id, 'medium' ); ?>
                <?php if ( $img_html ) : ?>
                    <div class="dd-marquee-item">
                        <?php echo $img_html; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
        $group_html = ob_get_clean();

        // Duplicate the group natively in the DOM to act as the trailing loop for the CSS animation.
        $output  = '<div class="dd-marquee-container">';
        $output .= '<div class="dd-marquee-track">';
        $output .= $group_html; 
        $output .= str_replace( 'class="dd-marquee-group"', 'class="dd-marquee-group" aria-hidden="true"', $group_html );
        $output .= '</div></div>';

        return $output;
    }
}

// Instantiate the class to initialize the plugin.
new DD_Logo_Marquee();