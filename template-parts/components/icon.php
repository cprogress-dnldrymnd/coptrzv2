<?php
$url = wp_get_attachment_url($id);
?>
<div class="icon-box <?= $class ?>">
    <?= output_svg_from_url($url) ?>
</div>