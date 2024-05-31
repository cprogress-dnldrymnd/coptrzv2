<?php
$GetData = new GetData;
?>
<?php if ($video['video_id']) { ?>
	<div class="cta-background-box background-box <?= $GetData->get_class($class) ?>">
		<video decoding="async" class="jetpack-lazy-image" autoplay muted loop preload="metadata" playsinline>
			<source src="<?= wp_get_attachment_url($video['video_id']) ?>">
		</video>
	</div>
<?php } ?>