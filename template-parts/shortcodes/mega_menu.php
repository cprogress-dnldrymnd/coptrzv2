<?php
$menu_items = get__post_meta_by_id($id, 'menu_items');
echo $id;
if ($menu_items) {
?>
    <div class="mega-menu">
        <div class="row">
            <?php foreach ($menu_items as $menu_item) { ?>
                <?php
                $menu_text = $menu_item['menu_text'];
                $menu_type = $menu_item['menu_type'];
                $menu_item_page = $menu_item['menu_item_page'];
                $menu_custom_url = $menu_item['menu_custom_url'];

                if ($menu_type == 'custom') {
                    $link = $menu_custom_url;
                } else {
                    $link = get_permalink($menu_item_page['id']);
                }

                ?>
                <div class="col-auto">
                    <a href="<?= $link ?>">
                        <?= $menu_text ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}
