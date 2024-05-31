<?php
$heading = $module['heading'];
$heading_small = $module['heading_small'];
$description = $module['description'];
$columns_width = $module['columns_width'];
$image = $module['image'];
?>

<div class="container">
    <div class="row g-5">
        <div class="col-lg-<?= $columns_width ?> d-flex align-items-end justify-content-center">
            <div class="column-holder">
                <?php
                $DisplayData->image(
                    array(
                        'image_id' => $image,
                        'size'     => 'large'
                    ), 'text-center'
                );
                ?>
            </div>
        </div>
        <div class="col-lg-<?=12 - $columns_width ?>">
            <div class="column-holder content-margin lg-padding small-width">
                <?php
                $DisplayData->heading(
                    array(
                        'heading'       => $heading,
                        'heading_small' => $heading_small
                    )
                );
                $DisplayData->description(
                    array(
                        'description' => $description
                    ),
                );

                if ($module['two_col_1_button_type']) {
                    $DisplayData->button(
                        $module['two_col_1_button_text'],
                        $module['two_col_1_' . $module['two_col_1_button_type']],
                        $module['two_col_1_button_action'],
                        $module['two_col_1_button_icon'],
                        'button-accent', false, $module['two_col_1_button_attribute']
                    );
                }
                ?>
            </div>
        </div>
    </div>
</div>