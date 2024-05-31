<?php
$GetData = new GetData;
$size = isset($data['size']) ? $data['size'] : 'full';
$placeholder = isset($data['placeholder']) ? true : false;
$image_url = wp_get_attachment_image_url($data['image_id'], $size);
$link = isset($data['link']) ? $data['link'] : false;

$ext = wp_check_filetype(wp_get_attachment_url($data['image_id']))['ext'];
$img_class = '';
if ($image_url) {
	$image_alt = $GetData->get_image_alt($data['image_id']);
}
else {
	if ($placeholder) {
		$logo = get__theme_option('alt_logo');
		$image_url = wp_get_attachment_image_url($logo);
		$image_alt = $GetData->get_image_alt($logo);
		$img_class = 'placeholder-image image-contain-transform';

	}
}

?>
<?php if ($image_url || $placeholder) { ?>

	<div class="image-box<?= $GetData->get_class($class) ?> <?= $img_class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?php
		if ($link) {
			echo '<a href="' . $link . '" class="d-block">';
		}
		?>
		<?php if ($ext != 'svg') { ?>
			<img decoding="async" class="jetpack-lazy-image" src="<?= $image_url ?>" alt="<?= $image_alt ?>">
		<?php }
		else { ?>
			<span class="svg-image" src="<?= $image_url ?>" alt="<?= $image_alt ?>"></span>
		<?php } ?>
	</div>
	<?php
	if ($link) {
		echo '</a>';
	}
?>
<?php } ?>