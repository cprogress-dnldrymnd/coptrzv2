<?php get_header(); ?>
<section class="not-found text-center md-padding-top md-padding-bottom text-white bg-primary">
    <div class="container content-margin d-flex align-items-center justify-content-center">
        <h1>404 - Page Not Found</h1>
        <div class="description-box">
            <p>
                Oops! Looks like we’ve flown off course.

            </p>
            <p>
                Why not head back to the home page?
            </p>
        </div>
        <div class="button-box button-bordered">
            <a href="<?= get_site_url() ?>">Return To Home</a>
        </div>
    </div>
</section>
<?php get_footer(); ?>