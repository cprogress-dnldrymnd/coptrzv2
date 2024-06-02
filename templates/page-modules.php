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
        if ($styles) {
            foreach ($styles as $style) {
                $style_type = $style['_type'];
                switch ($style_type) {
                    case 'background_color':
                        if ($style['background_color'] != 'background-custom') {
                            $classes .= ' ' . $style['background_color'];
                        } else {
                            $style = 'background-color: ' . $style['background_color_custom'];
                        }
                        break;
                    case 'padding':
                        $classes .= ' ' . $style['padding_top'] . ' ' . $style['padding_bottom'];
                        break;
                }
            }
        }
        include locate_template('template-parts/modules/' . $type . '.php');
    }
    ?>
</div>

<?php get_footer(); ?>