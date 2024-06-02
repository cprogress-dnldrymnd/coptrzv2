<?php
$GetData = new GetData;
$SVG = new SVG;
if ($button_type != 'popup') {
	$tag = 'a';
	$link = 'href="' . $button_link . '"';
} else {
	$tag = 'button';
	$link = '';
}

?>
<div class="button-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?> <?= $button_attribute ? $button_attribute : '' ?>>
	<<?= $tag ?> <?= $link ?> <?= $button_action ?> <?= $button_type == 'popup'  ? 'data-bs-toggle="modal"' : '' ?>>
		<?php if ($button_icon) { ?>
			<span class="icon"><?= $SVG->{$button_icon} ?></span>
		<?php } ?>
		<span class="text"><?= $button_type ?></span>
	</<?= $tag ?>>
</div>