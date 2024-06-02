<?php 
$GetData = new GetData;
?>
<?php if(isset($description)) { ?>
	<div class="description-box<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= wpautop($description) ?>
	</div>
	<?php } ?>