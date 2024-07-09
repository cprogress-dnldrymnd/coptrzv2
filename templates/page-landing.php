<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Landing Page 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header('landing'); ?>

<?php
$background_type = get__post_meta('background_type');
$background = get__post_meta('background');
$background_youtube = get__post_meta('background_youtube');
$form = get__post_meta('form');
$form_heading = get__post_meta('form_heading');
$form_description = get__post_meta('form_description');
$form_image = get__post_meta('form_image');

$image_args['image_id'] = $form_image;
$image_args['size'] = 'medium';
$image_args['class'] = _attribute('class', array('image-box'));

$description_args['description'] =  $form_description;
$description_args['class'] =  _attribute('class', array('description-box'));

?>

<section class="landing-page header-padding rounded-corner mx-20px d-flex align-items-center justify-content-center">
    <?php
    if ($background_youtube && $background_type == 'youtube') {
        echo _background($background_youtube, true);
    } else if ($background) {
        echo  _background($background);
    }
    ?>
    <div class="inner sm-padding-bottom sm-padding-top">
        <div class="container">
            <div class="row g-6 align-items-center">
                <div class="col-lg-6 text-white">
                    <div class="text-holder">
                        <h2><?php the_title() ?></h2>
                        <?php the_content() ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-holder bg-white rounded-corner">
                        <div class="form-header bg-accent text-white">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-3">
                                    <?php
                                    echo __image($image_args);
                                    ?>
                                </div>
                                <div class="col-lg-9">
                                    <div class="column-holder p-20px">
                                        <?php
                                        echo __heading(array(
                                            'tag' => 'h3',
                                            'heading' => $form_heading,
                                        ));
                                        echo __description($description_args);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-box p-20px small-text fw-light">
                            <div class="inner mt-20px">
                                <?= do_shortcode($form) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('landing'); ?>