<?php 
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$Theme_Options = new Theme_Options;
$description = $module['description'];
$disable_module = $module['disable_module'];
if(!$disable_module) { ?>
	<section <?= $GetData->get_attributes('wysiwyg text-section background-gray medium-padding-bottom medium-padding-top', $module_id, $template_class) ?>>
		<?php if($template_class) { ?>
			<?= $Helpers->get_edit_url('post.php?post='.$postid.'&action=edit', 'Edit Template') ?>
		<?php } ?>
		<div class="container extra-small-container" data-aos="fade-in">
			<?php 
			$DisplayData->description(array(
				'description' => $description
			), '', false);
			?>
		</div>
	</section>
	<?php } ?>