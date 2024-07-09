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
?>

<section class="landing-page xl-padding-top md-padding-bottom rounded-corner mx-20px">
    <?php
    if ($background_youtube && $background_type == 'youtube') {
        echo _background($background_youtube, true);
    } else if ($background) {
        echo  _background($background);
    }
    ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 text-white">
                <?php the_title() ?>
                <?php the_content() ?>
            </div>
            <div class="col-lg-6">
                <div class="form-box">
                    <?= do_shortcode($form) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('landing'); ?>