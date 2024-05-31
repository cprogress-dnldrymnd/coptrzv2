<?php
$DisplayData = new DisplayData;
$heading = $module['heading'];
$description = $module['description'];
$columns_width = $module['columns_width'];
$image = $module['image'];
$box_background_color = $module['box_background_color'];
?>


<div class="container">
  <div class="row-holder">
    <div class="row gx-7 <?= $box_background_color ?>">
      <div class="col-lg-<?= $columns_width ?> col-image">
        <div class="column-holder h-100">
          <?php
          $DisplayData->image(
            array(
              'image_id' => $image,
              'size'     => 'medium'
            ),
            'h-100'
          );
          ?>
        </div>
      </div>
      <div class="col-lg-<?= 12 - $columns_width ?> d-flex align-items-center">
        <div class="column-holder column-text content-margin pt-3 pb-3">
          <?php

          $DisplayData->heading(
            array(

              'heading' => $heading,
              'tag'     => 'h3'

            ),
          );
          $DisplayData->description(
            array(
              'description' => $description
            ),
          );
          ?>
        </div>
      </div>
    </div>
  </div>
</div>