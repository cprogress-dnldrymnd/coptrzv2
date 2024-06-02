<?php
$terms = get_terms(
  array(
    'taxonomy'   => 'pa_brands',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
  )
);

$featured_vendor_arr = array();
$a_f_array = array();
$f_p_array = array();
$q_z_array = array();
foreach ($terms as $term) {
  $image = get__term_meta($term->term_id, 'image');
  $hide_vendor = get__term_meta($term->term_id, 'hide_vendor');
  $featured_vendor = get__term_meta($term->term_id, 'featured_vendor');

  if ($featured_vendor) {
    $featured_vendor_arr[$term->term_id] = array(
      'image' => $image,
      'name'  => $term->name
    );
  }

  if (!$hide_vendor) {
    if (str_starts_with($term->name, 'A') || str_starts_with($term->name, 'B') || str_starts_with($term->name, 'C') || str_starts_with($term->name, 'D') || str_starts_with($term->name, 'E')) {
      $a_f_array[$term->term_id] = array(
        'image' => $image,
        'name'  => $term->name
      );
    }


    if (str_starts_with($term->name, 'F') || str_starts_with($term->name, 'G') || str_starts_with($term->name, 'H') || str_starts_with($term->name, 'I') || str_starts_with($term->name, 'J') || str_starts_with($term->name, 'K') || str_starts_with($term->name, 'L') || str_starts_with($term->name, 'M') || str_starts_with($term->name, 'N') || str_starts_with($term->name, 'O') || str_starts_with($term->name, 'P')) {
      $f_p_array[$term->term_id] = array(
        'image' => $image,
        'name'  => $term->name
      );
    }

    if (str_starts_with($term->name, 'Q') || str_starts_with($term->name, 'R') || str_starts_with($term->name, 'S') || str_starts_with($term->name, 'T') || str_starts_with($term->name, 'U') || str_starts_with($term->name, 'V') || str_starts_with($term->name, 'W') || str_starts_with($term->name, 'X') || str_starts_with($term->name, 'Y') || str_starts_with($term->name, 'Z')) {
      $q_z_array[$term->term_id] = array(
        'image' => $image,
        'name'  => $term->name
      );
    }
  }
}
?>

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
            <a class="inner content-margin h-100 background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . $vendor['image'] . '" size="medium"]'); ?>

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
            <a class="inner content-margin background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . $vendor['image'] . '" size="medium"]'); ?>
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
            <a class="inner content-margin background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . $vendor['image'] . '" size="medium"]'); ?>

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
            <a class="inner content-margin background-white d-block" href="<?= get_term_link($key) ?>">
              <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . $vendor['image'] . '" size="medium"]'); ?>
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