<?php
$url = wp_get_original_image_path($id);
$content = file_get_contents($url);
?>
<div class="icon-box <?= $class ?>">
    <?= $content ?>
</div>