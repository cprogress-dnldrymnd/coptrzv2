<?php
$GetData = new GetData;
$heading_small = isset($data['heading_small']) ? $data['heading_small'] : false;
?>
<?php
if (isset($data['heading']) && $data['heading'] != '') {
	$tag = isset($data['tag']) ? $data['tag'] : 'h2';
	?>
	<div class="heading-box<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?php if ($heading_small) { ?>
			<span class="prefix"><?= $heading_small ?></span>
		<?php } ?>
		<<?= $tag ?>>
			<?= $data['heading'] ?>
		</<?= $tag ?>>
	</div>
<?php } ?>