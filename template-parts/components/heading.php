<?php
$GetData = new GetData;
$heading_small = isset($heading_small) ? $heading_small : false;
?>
<?php
if (isset($heading) && $heading != '') {
	$tag = isset($tag) ? $tag : 'h2';
	?>
	<div class="heading-box<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?php if ($heading_small) { ?>
			<span class="prefix"><?= $heading_small ?></span>
		<?php } ?>
		<<?= $tag ?>>
			<?= $heading ?>
		</<?= $tag ?>>
	</div>
<?php } ?>