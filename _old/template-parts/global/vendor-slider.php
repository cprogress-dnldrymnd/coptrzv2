<?php
$DisplayData = new DisplayData;
?>
<section class="vendors-slider has-edit">
  <?php
  if (current_user_can('administrator')) {
    $Helpers = new Helpers;
    echo '<div class="edit-holder-box">';
    echo $Helpers->get_edit_url('edit.php?post_type=product&page=crb_carbon_fields_container_vendor_settings.php', 'Edit Vendor Description');
    echo $Helpers->get_edit_url('edit-tags.php?taxonomy=pa_brands&post_type=product', 'Manage Vendors');
    echo '</div>';
  }
  ?>
  <div class="container ">
    <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">
      <div class="col d-flex align-items-center pt-3">
        <span class="text">
          BRANDS
        </span>
        <span class="line"></span>
      </div>
      <div class="col text-content">
        <?= wpautop(get__theme_option('vendor_description')) ?>
      </div>
    </div>
  </div>
  <div class="container extend-right">
    <div class="vendor-slider-box md-padding">

      <?php
      $terms = get_terms(
        array(
          'taxonomy'   => 'pa_brands',
          'hide_empty' => false,
        )
      );
      ?>
      <div class="swiper mySwiper-vendorSwiper">
        <div class="swiper-wrapper text-center align-items-center">
          <?php foreach ($terms as $term) { ?>
            <?php
            $image = get__term_meta($term->term_id, 'image');
            $hide_vendor = get__term_meta($term->term_id, 'hide_vendor');

            if (!$hide_vendor) {
              ?>
              <div class="swiper-slide">
                <a class="inner background-white d-block" href="<?= get_term_link($term->term_id) ?>">
                  <?php
                  $DisplayData->image(
                    array(
                      'image_id' => $image,
                      'size'     => 'medium'
                    ),
                    'position-relative image-contain-transform mb-3'
                  );
                  ?>
                  <div class="vendor-title">
                    <h4 class="mb-0">
                      <?= $term->name ?>
                    </h4>
                  </div>
                </a>
              </div>
            <?php } ?>
          <?php } ?>
        </div>
        <div class="swiper-button-next d-none d-sm-flex"></div>

        <div class="swiper-button-prev d-none d-sm-flex"></div>


        <div class="swiper-pagination-holder d-flex d-sm-none">
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </div>
  </div>
</section>