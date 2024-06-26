<?php

$DisplayData = new DisplayData;

$SVG = new SVG;

?>


<section class="product-slider blog-section careers-section xl-padding-bottom archive-section">

  <div class="container">

    <div class="product-slider-box">

      <div class="mySwiper-productSwiperPost ">

        <div id="results">

          <div class="results-holder">

            <div class="product-holder post-box-PostSlider row gy-3">
              <?php
              if (have_posts()) {
                while (have_posts()) {
                  the_post();
                  $salary_and_location = get__post_meta('salary_and_location');
                  $duration = get__post_meta('duration');
                  ?>
                  <div class="product-box col-lg-4 col-sm-6 post-item">
                    <div class="swiper-slide">
                      <div class="inner background-white d-block ">
                        <a href="<?= get_permalink() ?>" class="box-link"></a>
                        <div class="image-holder position-relative">
                          <?php
                          $DisplayData->image(
                            array(
                              'image_id'    => get_post_thumbnail_id(),
                              'size'        => 'large',
                              'placeholder' => true
                            ),
                            'position-relative image-cover-transform'
                          );
                          ?>
                        </div>

                        <div class="content-box ">
                          <?php
                          $DisplayData->heading(
                            array(
                              'heading' => get_the_title(),
                              'tag'     => 'h4'
                            )
                          );
                          ?>
                          <div class="details-box fw-medium mb-3">
                            <p><?= $salary_and_location . ', ' . $duration ?></p>
                          </div>
                          <?php
                          $DisplayData->description(
                            array(
                              'description' => get_the_excerpt()
                            )
                          );
                          ?>
                        </div>
                        <div class="bottom-box">

                          <div class="link-box">
                            <a href="<?= get_permalink() ?>" class="link-underline fw-medium">
                              Read more
                            </a>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                <?php }
              }
              else {
                ?>
                <h2>No Results Found</h2>
                <?php
              }
              ?>
            </div>

          </div>

        </div>

      </div>

    </div>



  </div>

</section>