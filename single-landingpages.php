<?php get_header('landingpages'); ?>
<section class="landing-page bg-black xl-padding-top md-padding-bottom rounded-corner mx-20px">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 text-white">
                <h2> <?php the_title() ?></h2>
                <div class="description-box">
                    <?php the_content() ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-holder">

                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer('landingpages'); ?>