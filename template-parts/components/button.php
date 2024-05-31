<?php
$GetData = new GetData;
$SVG = new SVG;
$button_type = $button_type ? $button_type : '';
$button_link = get_permalink($button_type) ? get_permalink($button_type) : do_shortcode($button_type);
?>
<div class="button-box <?= $button_class ?>" <?= $GetData->get_data_aos($data_aos) ?> <?= $button_attribute ? $button_attribute : '' ?>>
	<a href="<?= $button_link ?>" <?= $button_action ?>>
		<?php if ($button_icon && $button_icon != 'sixty-seconds') { ?>
			<span class="icon"><?= $SVG->{$button_icon} ?></span>
		<?php } ?>

		<span class="text"><?= $button_text ? do_shortcode($button_text) : get_the_title($button_type) ?></span>

		<?php if ($button_icon == 'sixty-seconds') { ?>
			<span class="sixty-seconds"> <?= $SVG->sixty_seconds ?> </span>
		<?php } ?>

	</a>
</div>