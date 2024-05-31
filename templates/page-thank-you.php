<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Thank You 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$SVG = new SVG;
?>
<main id="main" class="page-components">
    <section class="thank-you md-padding">
        <div class="container">
            <div class="inner text-center">
                <?php SVG::check() ?>
                <div class="heading-box mb-4 mt-4">
                    <h2> <?= get__post_meta('alt_title') ? get__post_meta('alt_title') : get_the_title() ?> </h2>
                </div>
                <div class="description-box">
                    <?php the_content() ?>
                </div>
                <div class="button-box  button-accent">
                    <a href="<?= get_site_url() ?>">
                        <span class="text">
                            Return to home
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>