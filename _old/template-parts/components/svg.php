<?php 
$GetData = new GetData;
$SVG = new SVG;
?>
<?php if(isset($data['svg'])) { ?>
	<div class="svg-box<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= $SVG->{$data['svg']}?>
	</div>
	<?php } ?>