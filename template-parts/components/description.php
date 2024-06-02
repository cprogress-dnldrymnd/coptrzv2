<?php 
$GetData = new GetData;
?>
<?php if(isset($description)) { ?>
	<div class="description-box sss<?= $GetData->get_class($class) ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= do_shortcode( wpautop($description) ) ?>
	</div>
	<?php } ?>