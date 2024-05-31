<?php
$Theme_Options = new Theme_Options;
$shop_mega_menu = $Theme_Options->shop_mega_menu;
if ($shop_mega_menu) {
?>


<div class="menu-modal transition-500" id="coptrzmenu-modal" tabindex="-1" aria-labelledby="coptrzmenu-modalLabel"
    aria-hidden="true">
    <div class="menu-modal-dialog">
        <div class="menu-modal-content">
            <div class="menu-modal-backdrop">
            </div>
            <div class="container large-container position-relative">
                <div class="menu-modal-body p-0">
                    <div class="coptrz-mega-menu">
                        <div class="row">
                            <?php foreach ($shop_mega_menu as $shop_menu) { ?>
                            <?php
        $term = get_term_by('id', $shop_menu['id'], 'product_cat');
        $image = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            ?>

                            <div class="col-lg-4">
                                <div class="column-holder h-100 background-secondary text-center position-relative">
                                    <a href="<?= get_term_link($term->term_id) ?>" class="d-block h-100 white-color">
                                        <div class="menu-content">
                                            <div class="menu-title">
                                                <?= $term->name ?>
                                            </div>
                                            <div class="menu-image">
                                                <img src="<?= wp_get_attachment_image_url($image, 'large') ?>"
                                                    alt="<?= $term->name ?>">
                                            </div>
                                            <div class="button-box button-accent button-small transition-300">
                                                <span>EXPLORE</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<?php } ?>