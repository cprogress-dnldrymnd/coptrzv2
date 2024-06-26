<?php
$image = $module['image'];
?>


<div class="container">
    <div class="image-box text-center">
        <img decoding="async" class="jetpack-lazy-image" src="<?= wp_get_attachment_image_url($image, 'large') ?>" alt="" data-type="background" data-speed="3">
    </div>
</div>