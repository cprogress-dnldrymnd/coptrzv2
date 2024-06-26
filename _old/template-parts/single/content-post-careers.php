<?php
$salary_and_location = get__post_meta('salary_and_location');
$duration = get__post_meta('duration');
$Theme_Options = new Theme_Options;
?>

<article id="post-<?php the_ID() ?>" role="article">
  <section class="careers sm-padding">
    <div class="container">
      <div class="inner content-margin">
        <div class="heading-box text-center">
          <h2><?php the_title(); ?></h2>
        </div>
        <div class="details-box text-center">
          <span><?= $salary_and_location . ', ' . $duration ?></span>
        </div>
        <div class="image-box">
          <?php the_post_thumbnail('full'); ?>
        </div>
        <?php
        the_content();
        ?>
      </div>
  </section>
  <section class="cta cta-slim lg-padding-bottom">
    <div class="container">
      <div class="cta-inner position-relative background-black">
        <div class="row align-items-end gy-4 position-relative">
          <div class="col-12 col-lg">
            <div class="column-holder content-holder medium-width">
              <div class="heading-box text-uppercase">
                <h2>Interested in this role?</h2>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-auto">
            <div class="column-holder">
              <div class="button-group-box">
                <div class="button-box button-white">
                  <a href="<?= Theme_Options::email_address_url() ?>">EMAIL US</a>
                </div>
                <div class="button-box button-accent">
                  <a href="<?= Theme_Options::email_address_url() ?>">CALL US</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  </div>
</article>