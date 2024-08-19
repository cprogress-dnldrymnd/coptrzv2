<?php get_header() ?>
<?php 
$attachment_ids = get__post_meta('gallery');
?>
<section class="product-main">
  <div class="container">
    <div class="images">
      <div class="woocommerce-product-gallery__wrapper">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="product-main-image-holder">
              <div class="swiper product-main-image border-default rounded-corner overflow-hidden">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <?php

                    if ($post_thumbnail_id) {
                      echo __image(array(
                        'image_id' => $post_thumbnail_id,
                        'class'    => _attribute('class', array('product-image')),
                        'size'     => 'large'
                      ));
                    }
                    else {
                      $html = '<div class="product-image woocommerce-product-gallery__image--placeholder">';
                      $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce'));
                      $html .= '</div>';
                      echo $html;
                    }

                    ?>
                  </div>
                  <?php
                  foreach ($attachment_ids as $attachment_id) {
                    ?>
                    <div class="swiper-slide">
                      <?php
                      echo __image(array(
                        'image_id' => $attachment_id['id'],
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
<?php
echo ___hero_modules('text-start', 'small-hero');
?>
<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>