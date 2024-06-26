<?php
$alt_title = get__post_meta('alt_title');
?>

<article id="post-<?php the_ID() ?>" role="article">
  <?php
  the_content();
  ?>

  <section class="webinar-video lg-padding">
    <div class="container">
      <div class="inner">
        <div class="heading-box heading-elementor text-center mb-4">
          <h2><?= $alt_title ? $alt_title : get_the_title() ?></h2>
        </div>
        <?= do_shortcode('[webinar_box]') ?>
      </div>
    </div>
  </section>
</article>