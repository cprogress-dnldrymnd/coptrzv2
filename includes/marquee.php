<?php
/**
 * Plugin Name: DD Perfect Logo Marquee
 * Description: A hardware-accelerated, responsive infinite logo marquee using a dedicated Custom Post Type. Features pause-on-hover and seamless pure CSS loops.
 * Version: 1.0.0
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
     * * Registers the Custom Post Type, enqueues the required styles, and
     * registers the shortcode for front-end rendering.
     * * @return void
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
        add_shortcode( 'dd_logo_marquee', [ $this, 'render_shortcode' ] );
    }

    /**
     * Registers the 'dd_marquee_logo' Custom Post Type.
     * * Configures a dedicated CPT for logo management. It supports 'title',
     * 'thumbnail' (the logo image), and 'page-attributes' to allow for
     * custom menu ordering. Excluded from front-end search for clean architecture.
     * * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'               => 'Marquee Logos',
            'singular_name'      => 'Marquee Logo',
            'menu_name'          => 'Marquee Logos',
            'add_new'            => 'Add New Logo',
            'add_new_item'       => 'Add New Marquee Logo',
            'edit_item'          => 'Edit Logo',
            'new_item'           => 'New Logo',
            'view_item'          => 'View Logo',
            'search_items'       => 'Search Logos',
            'not_found'          => 'No logos found',
            'not_found_in_trash' => 'No logos found in Trash',
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false, // Keeps URLs private
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-images-alt2',
            'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => true,
        ];

        register_post_type( 'dd_marquee_logo', $args );
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
     * * Queries the 'dd_marquee_logo' CPT, ordered by 'menu_order' for user control.
     * Constructs a duplicated DOM group to achieve a seamless infinite CSS loop.
     * * @param array $atts User-defined shortcode attributes.
     * @return string Compiled HTML output for the marquee.
     */
    public function render_shortcode( $atts ) {
        $args = [
            'post_type'      => 'dd_marquee_logo',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        ?>
        <div class="dd-marquee-group">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="dd-marquee-item">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
        <?php
        $group_html = ob_get_clean();
        wp_reset_postdata();

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