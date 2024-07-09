<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Landing Page 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header('landing'); ?>

<?php 

<section class="landing-page xl-padding-top md-padding-bottom rounded-corner mx-20px">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <?php the_content() ?>
            </div>
            <div class="col-lg-6">
                <div class="form-box">
                    
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer('landing'); ?>