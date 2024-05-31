<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;

if ($post_is_global) {
    $heading = get__theme_option('product_archive_cta_heading');
    $description = get__theme_option('product_archive_cta_description');
    $background = get__theme_option('product_archive_cta_background');
    $style = get__theme_option('product_archive_cta_style');
    $background_type = wp_attachment_is('image', $background) ? 'image' : 'video';
    $background_color = 'background-black';
} else if ($post_is_training) {
    $heading = get__theme_option('training_cta_heading');
    $description = get__theme_option('training_cta_description');
    $background = get__theme_option('training_cta_background');
    $style = get__theme_option('training_cta_style');
    $background_type = wp_attachment_is('image', $background) ? 'image' : 'video';
    $background_color = 'background-black';
} else {
    $heading = $module['heading'];
    $description = $module['description'];
    $background = $module['background'];
    $style = $module['style'];
    $background_type = wp_attachment_is('image', $background) ? 'image' : 'video';
    $background_color = 'background-black';
}
?>
<?php
if ($post_is_global) {
    echo '<section class="cta cta-slim sm-padding has-edit">';
    if (current_user_can('administrator')) {
        echo $Helpers->get_edit_url('/edit.php?post_type=product&page=crb_carbon_fields_container_settings2.php', 'Edit Product Archive CTA');
    }
} 
?>
<div class="container <?= $style ?>">
    <div class="cta-inner position-relative <?= $background_color ?>">
        <div class="cta-background-box background-box overlay">
            <?php if ($background_type == 'video') { ?>
                <video autoplay muted loop preload="metadata" playsinline>
                    <source src="<?= wp_get_attachment_url($background) ?>">
                </video>
            <?php } else { ?>
                <img decoding="async" class="jetpack-lazy-image" src="<?= wp_get_attachment_image_url($background, 'full') ?>" />
            <?php } ?>
        </div>
        <div class="row align-items-end gy-4 position-relative">
            <div class="col-12 col-lg">
                <div class="column-holder content-holder medium-width">
                    <?php
                    $DisplayData->heading(
                        array(
                            'heading' => $heading
                        )
                    );
                    $DisplayData->description(
                        array(
                            'description' => $description
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="column-holder">
                    <?php
                    if ($post_is_global) {
                        if (get__theme_option('product_archive_cta_button_type')) {
                            $DisplayData->button(
                                get__theme_option('product_archive_cta_button_text'),
                                get__theme_option('product_archive_cta_' . get__theme_option('product_archive_cta_button_type')),
                                get__theme_option('product_archive_cta_button_action'),
                                get__theme_option('product_archive_cta_button_icon'),
                                'button-white',
                            );
                        }
                    } else if ($post_is_training) {
                        if (get__theme_option('training_cta_cta_button_type')) {
                            $DisplayData->button(
                                get__theme_option('training_cta_cta_button_text'),
                                get__theme_option('training_cta_cta_' . get__theme_option('training_cta_cta_button_type')),
                                get__theme_option('training_cta_cta_button_action'),
                                get__theme_option('training_cta_cta_button_icon'),
                                'button-accent',
                            );
                        }
                    } else {
                        if ($module['cta_button_type']) {
                            $DisplayData->button(
                                $module['cta_button_text'],
                                $module['cta_' . $module['cta_button_type']],
                                $module['cta_button_action'],
                                $module['cta_button_icon'],
                                ($style == 'style-1') ? 'button-accent' : 'button-secondary',
                                false,
                                $module['cta_button_attribute']
                            );
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($post_is_global) {
    echo '</section>';
}
?>