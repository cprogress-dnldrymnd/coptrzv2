<?php get_header() ?>
<?php
$attachment_ids = get__post_meta('gallery');

foreach ($attachment_ids as $attachment_id) {
  $image_ids[] = array(
    'key' => $key,
    'id'  => $attachment_id
  );
  $key++;
}
$images_ids_per_slides = array_chunk($image_ids, 6);

echo ___hero_modules('text-start', 'small-hero');
echo do_shortcode(get__post_meta('shortcode'));
echo do_shortcode(___sections('sections', get_the_ID()));
?>

<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>