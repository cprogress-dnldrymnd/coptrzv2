<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Calculator 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules" id="calculator">
    <?php
    echo ___hero_modules();
    ?>
    <section class="calculator medium-container lg-padding-top md-padding-bottom">
        <div class="container content-margin">
            <div class="inner px-5 content-margin">
                <h1>Project Information</h1>
                <div class="description-box fw-light small-text">
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
                            <input type="number" name="Initial_Investment" value="100000">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Annual Revenue from Drone Survey Work</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="number" name="Annual_Revenue_from_Drone_Survey_Work" value="70000">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Annual Operating Costs</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="number" name="Annual_Operating_Costs" value="30000">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Average Savings per Day Using a Drone</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <div class="icon d-flex align-items-center justify-content-center fw-medium">$</div>
                            <input type="number" name="Average_Savings_per_Day_Using_a_Drone" value="500">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="mb-2 fw-medium">Number of Days Drone Will Be Used Per Year</label>
                        <div class="input-box d-flex rounded-corner border-default">
                            <input type="number" name="Number_of_Days_Drone_Will_Be_Used_Per_Year" value="150">
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
                <div class="description-box fw-light small-text">
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
                            <button class="w-100" id="generate_report">Generate my report</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="inner your-result">
                <div id="results-position" class="md-padding-top"></div>
                <div class="results-holder sm-padding bg-primary rounded-corner text-white" id="results">
                    <div id="results-animation">
                        <div class="loader"></div>
                    </div>
                    <div id="results-box">
                        <h2 class="mb-4">Your Results</h2>
                        <hr class="mb-4">
                        <div class="description-box mb-5 fw-light small-text">
                            <p>
                                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
                            </p>
                        </div>
                        <div class="result-box mb-5">
                            <h3>Your payback period</h3>
                            <div class="result rounded-corner xs-padding text-center big-text text-primary" id="Payback_Period">
                                Approximately X months, X days
                            </div>
                        </div>
                        <div class="result-box">
                            <h3>ROI over 3 years</h3>
                            <div class="result rounded-corner xs-padding text-center big-text text-primary" id="ROI">
                                245%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php get_footer(); ?>
    <script>
        jQuery(document).ready(function() {

            jQuery('#generate_report').click(function(e) {
                jQuery('html, body').animate({
                    scrollTop: jQuery("#results-position").offset().top
                }, 100);

                $Initial_Investment = __get_val('Initial_Investment');
                $Annual_Revenue_from_Drone_Survey_Work = __get_val('Annual_Revenue_from_Drone_Survey_Work');
                $Annual_Operating_Costs = __get_val('Annual_Operating_Costs');
                $Average_Savings_per_Day_Using_a_Drone = __get_val('Average_Savings_per_Day_Using_a_Drone');
                $Number_of_Days_Drone_Will_Be_Used_Per_Year = __get_val('Number_of_Days_Drone_Will_Be_Used_Per_Year');

                $Net_Annual_Cash_Inflow = ($Annual_Revenue_from_Drone_Survey_Work + ($Average_Savings_per_Day_Using_a_Drone * $Number_of_Days_Drone_Will_Be_Used_Per_Year)) - $Annual_Operating_Costs;
                $Payback_Period = $Initial_Investment / $Net_Annual_Cash_Inflow;

                $Total_Net_Profit_Over_3_Years = (($Net_Annual_Cash_Inflow * 3) - $Initial_Investment);
                $ROI = ($Total_Net_Profit_Over_3_Years / $Initial_Investment) * 100;




                jQuery('#Payback_Period').text(yearsToYearsMonthsDays($Payback_Period));
                jQuery('#ROI').text(parseInt($ROI) + '%');

                jQuery('#calculator').addClass('calculating');
                jQuery('#calculator').removeClass('calculated');

                setTimeout(function() {
                    jQuery('#calculator').addClass('calculated');
                    jQuery('#calculator').removeClass('calculating');
                    jQuery('#generate_report').hide();

                }, 3000);

                e.preventDefault();
            });

            function __get_val($name) {
                $val = jQuery('input[name="' + $name + '"]').val();
                return parseFloat($val);
            }

            function yearsToYearsMonthsDays(value) {
                var totalDays = value * 365;
                var years = Math.floor(totalDays / 365);
                var months = Math.floor((totalDays - (years * 365)) / 30);
                var days = Math.floor(totalDays - (years * 365) - (months * 30));
                $years = '';
                if (years != 0) {
                    if (years > 1) {
                        $years = years + " years, ";
                    } else {
                        $years = years + " year, ";
                    }
                }
                if (months > 1) {
                    $months = months + " months, ";
                } else {
                    $months = months + " month, ";
                }
                if (days > 1) {
                    $days = days + " days, ";
                } else {
                    $days = days + " day, ";
                }

                return $years + $months + $days;
            }
        });;
    </script>