<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
function insert_carbon_block_into_page($page_id)
{

    $blocks = parse_blocks(get_post_field('post_content', $page_id));

    // Find the index where you want to insert the block
    $insert_index = 2; // Example: Insert after the second block

    // Create the Carbon Fields block
    $carbon_block = array(
        'blockName' => 'carbon-fields/call-to-action',
        'attrs' => array(
            'title' => 'This is a dynamic heading',
            'module_id' => 'Dynamic content from Carbon Fields!'
        )
    );

    // Insert the block
    array_splice($blocks, $insert_index, 0, array($carbon_block));

    // Update the post content
    wp_update_post(array(
        'ID' => $page_id,
        'post_content' => serialize_blocks($blocks)
    ));
}

// Example usage (run this once to insert the block):
$page_id_to_modify = 64544; // Replace with your actual page ID
insert_carbon_block_into_page($page_id_to_modify);
?>


<div class="modules">
    <?php
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $disable_module = $module['disable_module'];
        if (!$disable_module) {
            $module_id = $module['module_id'] ? $module['module_id'] : 'module-' . get_the_ID() . '-' . $key;
            $styles = $module['styles'];
            $classes = '';
            $style_attribute = '';
            $classes_row = '';
            $classes_text_color = '';
            $container_width_class = '';
            $container_width_style_attribute = '';
            $background_image_class = '';
            $baground_image = '';
            $background_overlay_image = '';
            $inner_class = '';
            $inner_style_attribute = '';
            if ($styles) {
                foreach ($styles as $style) {
                    $style_type = $style['_type'];
                    switch ($style_type) {
                        case 'background_color':
                            if ($style['background_color'] != 'background-custom') {
                                $classes .= ' ' . $style['background_color'];
                            } else {
                                $style_attribute = 'background-color: ' . $style['background_color_custom'] . ';';
                            }
                            break;
                        case 'padding':
                            $classes .= ' ' . $style['padding_top'] . ' ' . $style['padding_bottom'] . ' ' . $style['padding_left'] . ' ' . $style['padding_right'];
                            break;
                        case 'margin':
                            $classes .= ' ' . $style['margin_top'] . ' ' . $style['margin_bottom'] . ' ' . $style['margin_left'] . ' ' . $style['margin_right'];
                            break;
                        case 'border_radius':
                            $classes .= ' rounded-corner';
                            if ($style['border_radius']) {
                                $style_attribute .= 'border-radius: ' . $style['border_radius'] . ';';
                            }
                            break;
                        case 'custom_class':
                            $classes .= ' ' . $style['custom_class'];
                            break;
                        case 'alignment':
                            $classes .= ' ' . $style['text_align'];
                            $classes_row .= ' ' . $style['align_items'] . ' ' . $style['justify_content'];
                            break;
                        case 'background_image':
                            $baground_image .= $style['background_image'];
                            $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                            break;
                        case 'background_overlay':
                            $background_overlay_type = $style['background_overlay_type'];
                            if ($background_overlay_type == 'image') {
                                $background_overlay_image .= $style['background_overlay_image'];
                                if ($style['background_overlay_image_opacity'] || $style['background_overlay_image_opacity'] == 0) {
                                    $style_attribute .= '--background-image-opacity: ' . $style['background_overlay_image_opacity'] . ';';
                                }
                                $background_image_class .= 'no-overlay';
                            } else if ($background_overlay_type == 'custom') {
                                $style_attribute .= '--background-overlay-custom: ' . $style['background_overlay_custom'] . ';';
                                $background_image_class .= 'custom-overlay';
                            }
                            $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                            break;
                        case 'text_color':
                            $classes_text_color .= ' ' . $style['text_color'];
                            if ($style['text_color_custom']) {
                                $style_attribute .= 'color: ' . $style['text_color_custom'] . ';';
                            }
                            break;
                        case 'container_width':
                            $container_width_class .= ' ' . $style['container_width'];
                            if ($style['custom_container_width']) {
                                $container_width_style_attribute .= 'max-width: ' . $style['custom_container_width'] . ';';
                            }
                            break;
                        case 'max_width':
                            if ($style['max_width']) {
                                $inner_class = ' max-width';
                                $inner_style_attribute .= 'max-width: ' . $style['max_width'] . '; ';
                                if ($style['centred']) {
                                    $inner_class .= ' me-auto ms-auto';
                                }
                            }
                            break;
                    }
                }
            }
            include locate_template('template-parts/modules/' . $type . '.php');
        }
    }
    ?>
</div>

<?php get_footer(); ?>