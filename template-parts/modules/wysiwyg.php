<section class="wysiwyg <?= $classes ?>" id="<?= $module_id ?>">
  <div class="container">
    <?= do_shortcode(wpautop($module['wysiwyg'])) ?>
  </div>
</section>