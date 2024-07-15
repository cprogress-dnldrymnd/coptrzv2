<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Calculator 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules">
    <?php
    echo ___hero_modules();
    ?>
    <section class="calculator medium-container lg-padding-top lg-padding-bottom">
        <div class="container">
            <div class="inner px-5">
                <h1>Project Information</h1>
                <div class="description-box">
                    <p>
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum
                    </p>
                </div>
            </div>
            <hr>
            <div class="inner px-5">
                <div class="row form-groups">
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Initial Investment</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>