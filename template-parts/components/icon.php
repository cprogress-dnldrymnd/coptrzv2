<?php
$url = wp_get_original_image_path($id);
?>
<div class="icon-box <?= $class ?>" style="--width: <?= $icon_width ?>; --height: <?= $icon_height ?>; <?= $icon_color_custom ? 'color: ' . $icon_color_custom : '' ?>">
    <?= output_svg_from_url($url) ?>
</div>