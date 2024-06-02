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
                            <div class="submenu-holder py-3">
                                <div class="container small-container">
                                    <div class="row">
                                        <?php foreach ($submenu as $menu) { ?>
                                            <?php
                                            $type = $menu['_type'];
                                            $width = $menu['width'];
                                            ?>
                                            <div class="<?= $width ?>">
                                                <?php
                                                echo '<div class="submenu-item-title fw-semibold">' . $menu['menu_text'] . '</div>';
                                                if ($type == 'menu_items') {
                                                    echo do_shortcode('[menu id=' . $menu['menu'] . ']');
                                                }
                                                if ($type == 'images') {
                                                    $images = $menu['menu_images'];
                                                ?>
                                                    <div class="row">
                                                        <?php
                                                        foreach ($images as $image) {

                                                        ?>
                                                            <div class="col-auto">

                                                                <?= do_shortcode('[_image link="' . $image['url'] . '" id=' . $image['image'] . ' size="medium"]') ?>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}
