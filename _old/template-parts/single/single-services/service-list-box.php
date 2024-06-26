<?php
$DisplayData = new DisplayData;
$GetData = new GetData;
$services = $GetData->get_posts_ids('services');
?>
<div class="services-list-box">
    <h4>Our services</h4>
    <ul class="services-list list-inline fw-bold medium-text">
        <?php foreach ($services as $key => $service) { ?>
            <li class="<?= $key == get_the_ID() ? 'active' : '' ?>">
                <a href="<?= get_permalink($key) ?>"> <?= $service ?> </a>
            </li>
        <?php } ?>
    </ul>
</div>