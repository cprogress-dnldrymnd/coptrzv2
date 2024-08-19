<?php get_header() ?>
<?php 
$attachment_ids = get__post_meta('gallery');
?>

<?php
echo ___hero_modules('text-start', 'small-hero');
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
        </div>
      </div>
    </div>
  </div>
</section>
<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>