<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
function add_carbon_fields_block_to_page($page_id)
{
    $modules = get__post_meta('modules');

    // Ensure the page exists
    if (get_post_status($page_id) === false) {
        return;
    }

    // Get the page's content
    $page_content = get_post_field('post_content', $page_id);

    // Create your Carbon Fields block data as an array
    $block_data = array(
        'blockName' => 'carbon-fields/modules-1', // Replace with your actual block name
        'attrs' => array(
            // Your Carbon Fields data here. Example:
            'modules' => $modules,
        ),
    );

    // Serialize the block data for insertion
    $block_content = serialize_block($block_data);

    // Insert the block at the beginning of the page content
    $updated_content = $block_content . $page_content;

    // Update the page with the new content
    wp_update_post(array(
        'ID' => $page_id,
        'post_content' => $updated_content,
    ));
}

// Example usage: add the block to the page with ID 123
add_carbon_fields_block_to_page(get_the_ID());

var_dump($modules);
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