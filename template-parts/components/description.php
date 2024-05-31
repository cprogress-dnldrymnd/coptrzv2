<?php 
$GetData = new GetData;
?>
<?php if(isset($data['description'])) { ?>
	<div class="description-box<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= do_shortcode( wpautop($data['description']) ) ?>
	</div>
	<?php } ?>