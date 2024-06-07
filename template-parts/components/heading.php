<?php
$GetData = new GetData;
?>
<?php
if (isset($heading) && $heading != '') {
	$tag = isset($tag) && $tag ? $tag : 'h2';
?>
	<div class="heading-box <?= $class ?>" <?= $style  ? 'style="' . $style . '"' : '' ?>?><?= $GetData->get_data_aos($data_aos) ?>>
		<?php if ($heading_prefix) { ?>
			<span class="prefix"><?= $heading_prefix ?></span>
		<?php } ?>
		<<?= $tag ?>>
			<?= $heading ?>
		</<?= $tag ?>>
		<?php if ($heading_suffix) { ?>
			<span class="suffix"><?= $heading_suffix ?></span>
		<?php } ?>
	</div>
<?php } ?>