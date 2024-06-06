<?php
$url = wp_get_original_image_path($id);
echo $url;
?>
<div class="icon-box <?= $class ?>">
    <?= output_svg_from_url($url) ?>
</div>