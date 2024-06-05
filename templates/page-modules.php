<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
?>
<div class="modules">
    <?php
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $module_id = 'module-' . get_the_ID() . '-' . $key;
        $styles = $module['styles'];
        $classes = '';
        $style_attribute = '';
        $classes_row = '';
        $container_width_class = '';
        $container_width_style_attribute = '';
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
                        if ($style['border_radius']) {
                            $style_attribute .= 'border-radius: ' . $style['border_radius'] . ';';
                        }
                        break;
                    case 'custom_class':
                        $classes .= ' ' . $style['custom_class'];
                        break;
                    case 'alignment':
                        $classes_row .= ' ' . $style['align_items'] . ' ' . $style['justify_content'] . ' ' . $style['text_align'];
                        break;
                    case 'background_image':
                        $baground_image = $style['background_image'];
                        $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                        break;
                    case 'background_overlay':
                        $background_overlay_type = $style['background_overlay_type'];
                        if($background_overlay_type == 'image') {
                            $background_overlay_image = $style['background_overlay_image'];
                        }
                        
                        
                        $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                        break;
                    case 'text_color':
                        $classes .= ' ' . $style['text_color'];
                        if ($style['text_color_custom']) {
                            $style_attribute .= 'color: ' . $style['text_color_custom'] . ';';
                        }
                        break;
                    case 'container_width':
                        $container_width_class .= ' ' . $style['container_width'];
                        if ($style['custom_container_width']) {
                            $container_width_style_attribute .= 'max-width: ' . $style['custom_container_width'] . '; margin-left: auto; margin-right: auto';
                        }
                        break;
                }
            }
        }
        include locate_template('template-parts/modules/' . $type . '.php');
    }
    ?>
</div>

<?php get_footer(); ?>