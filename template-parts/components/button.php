<?php
$GetData = new GetData;
$SVG = new SVG;
$button_type = $button_type ? $button_type : '';
?>
<div class="button-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?> <?= $button_attribute ? $button_attribute : '' ?>>
	<a href="<?= $button_link ?>" <?= $button_action ?>>
		<?php if ($button_icon) { ?>
			<span class="icon"><?= $SVG->{$button_icon} ?></span>
		<?php } ?>
		<span class="text"><?= $button_text ? do_shortcode($button_text) : get_the_title($button_type) ?></span>
	</a>
</div>