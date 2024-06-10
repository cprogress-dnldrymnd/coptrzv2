<?php
$number_of_columns = $module['number_of_columns'];
$post_type = $module['post_type'][0]['_type'];
$source = $module['post_type'][0]['source'];
?>
<section class="post-grid position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute  ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container <?= $container_width_class ?> <?= $classes_text_color ?>" style="<?= $container_width_style_attribute ?>">
        <?php
        _section_heading_description(array(
            'heading' => $module['heading'],
            'description' => $module['description'],
            'text_align' => $module['text_align'],
            'heading_prefix' => $module['heading_prefix'],
            'heading_suffix' => $module['heading_suffix'],
            'tag' => $module['tag'],
            'size' => $module['size'],
            'heading_with_line' => $module['heading_with_line'],
        ));
        ?>
        <?php
        // Build the args
        $args['post_type'] = $post_type;
        $args['posts_per_page'] = -1;


        if ($source == 'category') {
            $category_ids = array();
            $categories = $module['post_type'][0]['category'];
            $taxonomy_key = $module['post_type'][0]['taxonomy_key'];
            foreach ($categories as $category) {
                $category_ids[] = $category['id'];
            }
            $args['tax_query'] =  array(
                array(
                    'taxonomy' => $taxonomy_key,
                    'field' => 'id',
                    'terms' => $term->term_id,
                )
            );
        } else if ($source == 'manually') {
            $posts = $module['post_type'][0]['post'];
            $posts_ids = array();
            foreach ($posts as $post) {
                $posts_ids[] = $post['id'];
            }
            $args['post__in'] = $posts_ids;
        }
        // Get the posts
        $posts_lists = get_posts($args);
        $post_elements = $module['post_elements'];
        ?>

        <?php if ($posts_lists) { ?>
            <?php
            $styles = $module['post_box_styles'];
            $classes = '';
            $style_attribute_post = '';
            $style_attribute_inner = '';
            if ($styles) {
                foreach ($styles as $style) {
                    $style_type = $style['_type'];
                    switch ($style_type) {
                        case 'background_color':
                            if ($style['background_color'] != 'background-custom') {
                                $classes .= ' ' . $style['background_color'];
                            } else {
                                $style_attribute_post .= 'background-color: ' . $style['background_color_custom'] . ';';
                            }
                            break;
                        case 'padding':
                            $remove_image_padding = $style['remove_image_padding'] ? 'remove-image-padding' : '';
                            $classes .= ' ' . $style['padding_top'] . ' ' . $style['padding_bottom'] . ' ' . $style['padding_left'] . ' ' . $style['padding_right'] . ' ' . $remove_image_padding;
                            break;
                        case 'margin':
                            $classes .= ' ' . $style['margin_top'] . ' ' . $style['margin_bottom'] . ' ' . $style['margin_left'] . ' ' . $style['margin_right'];
                            break;
                        case 'border_radius':
                            $style_attribute_post .= 'border-radius: ' . $style['border_radius'] . ';';
                            break;
                        case 'alignment':
                            $classes .= ' ' . $style['align_items'] . ' ' . $style['justify_content'] . ' ' . $style['text_align'];
                            break;
                        case 'custom_class':
                            $classes .= ' ' .  $style['custom_class'];
                            break;
                        case 'max_width':
                            $style_attribute_inner .= 'max-width: ' . $style['max_width'] . ';';
                            break;
                        case 'column_width':
                            $column_class =  ($style['column_width'] ? $style['column_width'] : 'col-lg-3') . ' ' . $style['column_width_tablet'] . ' ' . ($style['column_width_mobile'] ? $style['column_width_mobile'] : 'col-sm-12');
                            break;
                    }
                }
            }
            ?>

            <div class="row g-4">
                <?php foreach ($posts_lists as $post) { ?>

                    <div class="<?= $column_class ?>">
                        <?php
                        $id = $post->ID;
                        include locate_template('template-parts/components/post_grid.php');
                        ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>