<?php
$url = wp_get_original_image_path($id);
?>
<div class="icon-box <?= $class ?>">
    <?= output_svg_from_url($url) ?>
</div>