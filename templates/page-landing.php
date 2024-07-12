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
$form = get__post_meta('wp_form');
$form_heading = get__post_meta('form_heading');
$form_description = get__post_meta('form_description');
$form_image = get__post_meta('form_image');
$form_id = $form[0]['id'];
$image_args['image_id'] = $form_image;
$image_args['size'] = 'medium';
$image_args['class'] = _attribute('class', array('image-box'));

$description_args['description'] =  $form_description;
$description_args['class'] =  _attribute('class', array('description-box'));

?>

<section class="landing-page bg-primary header-padding rounded-corner mx-20px d-flex align-items-center justify-content-center">
    <?php
    if ($background_youtube && $background_type == 'youtube') {
        echo __background($background_youtube, true);
    } else if ($background) {
        echo  __background($background);
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
                    
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('landing'); ?>