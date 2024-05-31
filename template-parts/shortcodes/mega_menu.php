<?php
$menu_items = carbon_get_post_meta($id, 'menu_items');
if ($menu_items) {
?>
    <div class="mega-menu">
        <div class="row justify-content-between">
            <?php foreach ($menu_items as $menu_item) { ?>
                <?php
                $menu_text = $menu_item['menu_text'];
                $menu_type = $menu_item['menu_type'];
                $menu_item_page = $menu_item['menu_item_page'];
                $menu_custom_url = $menu_item['menu_custom_url'];
                $submenu = $menu_item['submenu'];

                if ($menu_type == 'custom') {
                    $link = $menu_custom_url;
                } else {
                    $link = get_the_permalink($menu_item_page[0]['id']);
                }

                ?>
                <div class="col-auto">
                    <div class="menu-holder">
                        <a class="mega-menu-link text-uppercase fw-semibold d-inline-block <?= $submenu ? 'has-submenu' : '' ?>" href="<?= $link ?>">
                            <?= $menu_text ?>
                        </a>
                        <?php if ($submenu) { ?>
                            <div class="submenu-holder">
                                <div class="container">
                                    <?php foreach ($submenu as $menu) { ?>
                                        <?php
                                        $type = $menu['_type'];
                                        ?>

                                        <?php
                                        if ($type == 'menu_items') {
                                            //echo '<h4>' . $menu['menu_text'] . '</h4>';
                                            //echo do_shortcode('[menu id=' . $menu['menu'] . ']');
                                        }
                                        ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}
