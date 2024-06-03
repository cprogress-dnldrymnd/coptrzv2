<?php
$GetData = new GetData;
$SVG = new SVG;

if ($button_type != 'popups') {
	$tag = 'a';
	$link = 'href="' . get_permalink($id) . '"';
} else if ($button_type == 'custom') {
	$tag = 'a';
	$link = 'href="' . $button_url_custom . '"';
} else {
	$tag = 'button';
	$link = 'data-bs-toggle="modal" data-bs-target="#modal-' . $id . '"';
}
echo $link;
?>
<div class="button-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?> <?= $button_attribute ? $button_attribute : '' ?>>
	<<?= $tag ?> <?= $link ?> <?= $button_action ?> <?= $button_type == 'popup'  ? 'data-bs-toggle="modal"' : '' ?>>
		<?php if ($button_icon) { ?>
			<span class="icon"><?= $SVG->{$button_icon} ?></span>
		<?php } ?>
		<span class="text"><?= $button_text ? do_shortcode($button_text) : get_the_title($button_type) ?></span>
	</<?= $tag ?>>
</div>