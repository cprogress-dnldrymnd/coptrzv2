<?php
$url = wp_get_attachment_url($id);
$content = file_get_contents($url);
echo $url;
?>
<div class="icon-box <?= $class ?>">
    <?= output_svg_from_url($url) ?>
</div>