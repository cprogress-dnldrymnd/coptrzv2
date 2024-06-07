<?php
$GetData = new GetData;

$text = str_replace("'", "&#39;", $text);
$text = str_replace('"', "&quot;", $text);
?>
<?php if ($description) { ?>
	<div class="description-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?= do_shortcode(wpautop(html_entity_decode($description))) ?>
	</div>
<?php } ?>