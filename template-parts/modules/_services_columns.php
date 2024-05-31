<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$SVG = new SVG;
$services = $module['services'];
$offset_top = $module['offset_top'];
$disable_module = $module['disable_module'];
if (!$disable_module) { ?>
    <section <?= $GetData->get_attributes('icon-box-section ' . $offset_top ? ' offset-top' : '', $module_id, $template_class) ?>>
        <?php if ($template_class) { ?>
            <?= $Helpers->get_edit_url('post.php?post=' . $postid . '&action=edit', 'Edit Template') ?>
        <?php } ?>
        <div class="container">
            <div class="row row-g-small">
                <?php foreach ($services as $key => $service) {  ?>
                    <?php
                    $icon = get__post_meta_by_id($service['id'], 'icon_svg');
                    $short_description = get__post_meta_by_id($service['id'], 'short_description');
                    ?>
                    <div class="col-lg-4">
                        <div class="column-holder h-100" data-aos="fade-left" data-aos-offset="300" <?= $Helpers->data_aos_delay(3, $key) ?>>
                            <a class="content-margin d-block h-100 icon-box-holder background-primary" href="<?= get_permalink($service['id']) ?>">
                                <?php
                                $DisplayData->svg(array(
                                    'svg' => $icon,
                                ));
                                $DisplayData->heading(array(
                                    'heading' => get_the_title($service['id'])
                                ));
                                $DisplayData->description(array(
                                    'description' => $short_description
                                ), 'small-text');
                                ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

<?php } ?>