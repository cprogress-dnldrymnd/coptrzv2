<?php
$SVG = new SVG;
?>
<div class="col-auto d-flex align-items-center">
    <div class="row g-4 header-right">
   
        <div class="col-auto d-flex align-items-center d-lg-none ">
            <button class="menu-burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasMenu" aria-controls="offCanvasMenu">
                <div class="icon">
                    <div class="menu"></div>
                </div>
            </button>
        </div>
        <div class="col-auto">
            <?= do_shortcode('[wpml_language_selector_widget]') ?>
        </div>

        <?php

        $button_type = get__theme_option('header_button_type');
        $button_text = get__theme_option('header_button_text');
        $button_url = get__theme_option('header_button_url');
        $button_url_custom = get__theme_option('header_button_url_custom');
        $button_style = get__theme_option('header_button_style');
        $button_target = get__theme_option('header_button_target');
        echo __button(array(
            'button_type' => $button_type,
            'button_text' => $button_text,
            'button_url' => $button_url,
            'button_url_custom' => $button_url_custom,
            'button_style' => $button_style . ' col-auto button-accent button-small d-none d-lg-block',
            'button_target' => $button_target,
        ));
        ?>


    </div>
</div>