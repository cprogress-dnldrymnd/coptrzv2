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
    <section class="calculator medium-container lg-padding-top md-padding-bottom">
        <div class="container content-margin">
            <div class="inner px-5 content-margin">
                <h1>Project Information</h1>
                <div class="description-box">
                    <p>
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum
                    </p>
                </div>
            </div>
            <hr class="my-5">
            <div class="inner px-5">
                <div class="row g-4 form-groups">
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Initial Investment</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Annual Revenue from Drone Survey Work</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Annual Operating Costs</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Average Savings per Day Using a Drone</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Number of Days Drone Will Be Used Per Year</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="background-gradient-default medium-container md-padding-top md-padding-bottom">
        <div class="container">
            <div class="inner px-5 content-margin">
                <h3>Who should we send your report to?</h3>
                <div class="description-box">
                    <p>
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum
                    </p>
                </div>
                <div class="row g-4 form-groups">
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Full Name</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Company</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Email Address</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Telephone</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="text">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="button-box button-accent">
                            <button class="w-100">Generate my report</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="inner your-result sm-padding bg-primary rounded-corner">
                <h2 class="text-white mb-4">Your Results</h2>
                <hr class="text-white mb-4">
                <div class="description-box mb-4">
                    <p>
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
                    </p>
                </div>
                <div class="result-box">
                    <h3>Your payback period</h3>
                    <div class="result rounded-corner xs-padding text-center big-text">
                        Approximately X months, X days
                    </div>
                </div>
                <div class="result-box">
                    <h3>ROI over 3 years</h3>
                    <div class="result rounded-corner xs-padding text-center big-text">
                        245%
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php get_footer(); ?>