<?php get_header() ?>
<section class="not-found text-center xl-padding background-black d-flex align-items-center">
	<div class="container position-relative">
		<div class="heading-box big-heading">
			<h1><strong>404</strong></h1>
		</div>
		<div class="description-box mb-5">
			<p>Oops page not found</p>
		</div>
		<div class="button-box button-accent">
			<a href="<?= get_site_url() ?>" class="" role="button">
				<span class="text"> RETURN HOME </span>
			</a>
		</div>
	</div>
</section>
<?php get_footer() ?>