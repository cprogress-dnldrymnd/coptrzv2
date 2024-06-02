<section class="vendors-nav sm-padding-bottom overflow-visible">
  <div class="container">
    <ul class="list-inline d-flex">
      <li><a href="#featured-vendors">Featured Brands</a></li>
      <li><a href="#a-e">A-E</a></li>
      <li><a href="#f-p">F-P</a></li>
      <li><a href="#q-z">Q-Z</a></li>
    </ul>
  </div>
</section>
<section class="vendors md-padding-bottom overflow-visible" id="featured-vendors">
  <div class="container">
    <div class="heading-box mb-3">
      <h3>Featured Brands</h3>
    </div>
    <div class="vendor-slider-box">
      <div class="row g-3 text-center">
        <?php foreach ($featured_vendor_arr as $key => $vendor) { ?>
          <?php
          ?>
          <div class="col-xl-3 col-lg-4 col-sm-6 col-6 vendor-box">
            <a class="inner h-100 background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image id="' . $vendor['image'] . '" size="medium"]') ?>
              <div class="vendor-title">
                <h4 class="mb-0">
                  <?= $vendor['name'] ?>
                </h4>
              </div>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>


<section class="vendors md-padding-bottom overflow-visible" id="a-e">
  <div class="container">
    <div class="heading-box mb-3">
      <h3>A-E</h3>
    </div>
    <div class="vendor-slider-box">
      <div class="row g-3 text-center">
        <?php foreach ($a_f_array as $key => $vendor) { ?>
          <?php
          ?>
          <div class="col-xl-3 col-lg-4 col-sm-6 col-6 vendor-box">
            <a class="inner h-100 background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image id="' . $vendor['image'] . '" size="medium"]') ?>
              <div class="vendor-title">
                <h4 class="mb-0">
                  <?= $vendor['name'] ?>
                </h4>
              </div>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<section class="vendors md-padding-bottom overflow-visible" id="f-p">
  <div class="container">
    <div class="heading-box mb-3">
      <h3>F-P</h3>
    </div>
    <div class="vendor-slider-box">
      <div class="row g-3 text-center">
        <?php foreach ($f_p_array as $key => $vendor) { ?>
          <?php
          ?>
          <div class="col-xl-3 col-lg-4 col-sm-6 col-6 vendor-box">
            <a class="inner h-100 background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image id="' . $vendor['image'] . '" size="medium"]') ?>
              <div class="vendor-title">
                <h4 class="mb-0">
                  <?= $vendor['name'] ?>
                </h4>
              </div>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<section class="vendors md-padding-bottom overflow-visible" id="q-z">
  <div class="container">
    <div class="heading-box mb-3">
      <h3>Q-Z</h3>
    </div>
    <div class="vendor-slider-box">
      <div class="row g-3 text-center">
        <?php foreach ($q_z_array as $key => $vendor) { ?>
          <?php
          ?>
          <div class="col-xl-3 col-lg-4 col-sm-6 col-6 vendor-box">
            <a class="inner h-100 background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image id="' . $vendor['image'] . '" size="medium"]') ?>
              <div class="vendor-title">
                <h4 class="mb-0">
                  <?= $vendor['name'] ?>
                </h4>
              </div>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>