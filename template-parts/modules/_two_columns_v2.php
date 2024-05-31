<?php

$DisplayData = new DisplayData;

$columns_width = $module['columns_width'];

$column_1 = $module['column_1'];

$column_2 = $module['column_2'];

$heading = $module['heading'];

$left_title = $module['left_title'];

?>
<?php if ($left_title || $heading) { ?>

    <div class="container mb-7">
        <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">

            <div class="col d-flex align-items-center pt-3">

                <span class="text">

                    <?= $left_title ?>

                </span>

                <span class="line"></span>

            </div>

            <div class="col text-content">

                <p>

                    <?= $heading ?>

                </p>

            </div>

        </div>

    </div>
<?php } ?>

<div class="container">

    <div class="row g-5">

        <div class="col-lg-<?= $columns_width ?>">

            <div class="column-holder">

                <?php

                $DisplayData->description(

                    array(

                        'description' => $column_1

                    ),

                    'content-margin'

                );

                ?>

            </div>

        </div>

        <div class="col-lg-<?= 12 - $columns_width ?>">

            <div class="column-holder">

                <?php

                $DisplayData->description(

                    array(

                        'description' => $column_2

                    ),

                    'content-margin'

                );

                ?>

            </div>

        </div>

    </div>

</div>