<?php
$DisplayData = new DisplayData;
$GetData = new GetData;
$services = $GetData->get_posts_ids('services');
?>
<section class="the-content md-padding">
    <div class="container">
        <div class="row sticky-parent">
            <div class="col-lg-8 pe-lg-5">
                <div class="column-holder content-margin medium-text">
                    <?php the_content() ?>
                </div>
            </div>
            <div class="col-lg-4 position-relative">
                <div class="column-holder sticky">
                    <?php get_template_part('template-parts/single/single-' . get_post_type() . '/service', 'list-box'); ?>
                </div>
            </div>
        </div>
    </div>
</section>