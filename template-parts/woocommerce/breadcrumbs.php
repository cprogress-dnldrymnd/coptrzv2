<?php
$term = get_queried_object();
?>


<ul class="breadcrumbs list-unstyled d-flex">
    <li>
        <a href="<?= get_permalink( get_option('woocommerce_shop_page_id') ) ?>">Shop</a>
    </li>
    <li>
        <span> <?=  $term->name ?> </span>
    </li>
</ul>