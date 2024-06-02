<?php
$GetData = new GetData;
?>
<?php if ($description) { ?>
	<div class="description-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= wpautop($description) ?>
	</div>
<?php } ?>