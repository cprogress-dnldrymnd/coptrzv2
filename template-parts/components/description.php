<?php
$GetData = new GetData;
?>
<?php if ($descriptions) { ?>
	<div class="description-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= do_shortcode(wpautop($description)) ?>
	</div>
<?php } ?>