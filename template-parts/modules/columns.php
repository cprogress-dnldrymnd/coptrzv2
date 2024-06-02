<section class="columns <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container">
        <div class="section-heading-description">
            <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading"]') ?>
            <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
        </div>

        <div class="column-items">
            <?php if ($module['columns']) { ?>
                <div class="row">
                    <?php foreach ($module['columns'] as $column) { ?>
                        <div class="col">
                            <?= _elements($column['items']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php
$pages = get__posts('page');
$select_page = '<select name="select-page-selector">';
foreach ($pages as $key => $page) {
    $select_page .= '<option value="' . $key . '"> ' . $page . ' </option>';
}
$select_page .= '</select>';

$posts = get__posts('post');
$select_post = '<select name="select-page-selector">';
foreach ($posts as $key => $post) {
    $select_post .= '<option value="' . $key . '"> ' . $post . ' </option>';
}
$select_post .= '</select>';

$solutions = get__posts('solutions');
$select_solution = '<select name="select-page-selector">';
foreach ($solutions as $key => $solution) {
    $select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
}
$select_solution .= '</select>';

$popups = get__posts('popups');
$select_popup = '<select name="select-page-selector">';
foreach ($popups as $key => $popup) {
    $select_popup .= '<option value="' . $key . '"> ' . $popup . ' </option>';
}
$select_popup .= '</select>';?>