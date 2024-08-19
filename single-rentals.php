<?php get_header() ?>
<?php
$attachment_ids = get__post_meta('gallery');
$images_ids_per_slides = array_chunk($attachment_ids, 6);
echo ___hero_modules('text-start', 'small-hero');
echo do_shortcode(___sections('sections', get_the_ID()));
?>
<section class="product-main md-padding">
  <div class="container">
    <div class="images">
      <div class="woocommerce-product-gallery__wrapper">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="product-main-image-holder">
              <div class="swiper product-main-image border-default rounded-corner overflow-hidden">
                <div class="swiper-wrapper">
                  <?php
                  foreach ($attachment_ids as $attachment_id) {
                    ?>
                    <div class="swiper-slide">
                      <?php
                      echo __image(array(
                        'image_id' => $attachment_id,
                        'class'    => _attribute('class', array('product-image')),
                        'size'     => 'large'
                      ));
                      ?>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="product-thumb-holder">
              <div class="swiper product-thumb">
                <div class="swiper-wrapper">
                  <?php
                  if ($attachment_ids) {
                    $key = 1;
                    foreach ($images_ids_per_slides as $images_ids_per_slide) {
                      if (count($attachment_ids) > 6) {
                        echo '<div class="swiper-slide">';
                      }
                      echo '<div class="row g-4 w-100">';

                      foreach ($images_ids_per_slide as $image) {
                        $key = $image['key'];
                        echo "<div class='col-6 thumb-nav' target='$key'>";
                        echo apply_filters('woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html($image['id']), $post_thumbnail_id);
                        echo '</div>';
                      }


                      echo '</div>';
                      if (count($attachment_ids) > 6) {
                        echo '</div>';
                      }
                    }
                  }
                  ?>

                </div>
                <?php if (count($attachment_ids) > 6) { ?>
                  <div class="swiper-nav d-inline-flex">
                    <div class="swiper-button-prev swiper-button-prev-thumb"></div>
                    <div class="swiper-button-next swiper-button-next-thumb"></div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>