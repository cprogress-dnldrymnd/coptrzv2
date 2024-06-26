<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$SVG = new SVG;
$Theme_Options = new Theme_Options;
$heading = $module['heading'];
$contact_form = $module['contact_form'];
$disable_module = $module['disable_module'];

?>
<?php if (!$disable_module) { ?>
    <section <?= $GetData->get_attributes('contact-form', $module_id, $template_class) ?>>
        <?php if ($template_class) { ?>
            <?= $Helpers->get_edit_url('post.php?post=' . $postid . '&action=edit', 'Edit Template') ?>
        <?php } ?>
        <div class="container-fluid p-0">
            <div class="row align-items-center m-0">
                <div class="col-lg-6">
                    <div class="column-holder md-padding" data-aos="fade-left">
                        <div class="contact-details-box">
                            <?php if (get__theme_option('office_number') || get__theme_option('mobile_number')) { ?>
                                <div class="contact-details">
                                    <?php
                                    $DisplayData->heading(array(
                                        'heading' => 'Call Us',
                                    ), 'small-heading', false);
                                    ?>
                                    <?php
                                    if (get__theme_option('office_number')) {
                                        echo do_shortcode('[office_number_with_icon]');
                                    }

                                    if (get__theme_option('mobile_number')) {
                                        echo do_shortcode('[mobile_number_with_icon]');
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if (get__theme_option('email_address')) { ?>
                                <div class="contact-details">
                                    <?php
                                    $DisplayData->heading(array(
                                        'heading' => 'Email us',
                                    ), 'small-heading', false);
                                    ?>
                                    <?= do_shortcode('[email_address_with_icon]') ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 background-primary">
                    <div class="column-holder md-padding" data-aos="fade-right">
                        <?php
                        $DisplayData->heading(array(
                            'heading' => $heading,
                        ), 'mb-5', false);
                        ?>
                        <div class="contact-form-box light-color">
                            <?= do_shortcode($contact_form) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>