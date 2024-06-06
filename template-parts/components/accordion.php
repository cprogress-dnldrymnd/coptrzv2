<?php
if ($accordion_source == 'faqs') {
    $accordion = array();
    foreach ($faqs as $faq) {
        $accordion[$faq['id']] = array(
            'heading' => get_the_title($faq['id']),
            'description' => get_the_content(null, false, $faq['id']),
        );
    }
} else if ($accordion_source == 'faqs_category') {
    $faqs_cat_id = array();
    foreach ($faqs_category as $faqs_cat) {
        $faqs_cat_id[] = $faqs_cat['id'];
    }
    $args = array(
        'post_type' => 'faq',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'faqs_category',
                'field'    => 'term_id',
                'terms'    => $faqs_cat_id
            )
        )
    );
    $faqs_lists = get_posts($args);
    $accordion = array();
    foreach ($faqs_lists as $faq) {
        $accordion[$faq->ID] = array(
            'heading' => $faq->post_title,
            'description' => $faq->post_content
        );
    }
} else {
    $accordion = $accordion;
}
var_dump($faqs_category);
?>


<div class="accordion accordion-v2 accordion-flush" id="accordion-<?= $module_id ?>">
    <?php foreach ($accordion as $key => $accordion_item) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-heading<?= $key ?>">
                <button class="accordion-button justify-content-between p-0 <?= $key == 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $key ?>" aria-expanded="<?= $key == 0 ? 'true' : 'false' ?>" aria-controls="flush-collapse<?= $key ?>">
                    <span>
                        <?= do_shortcode('[_heading heading="' . $accordion_item['heading'] . '" tag="h4"]') ?>
                    </span>
                    <span class="plus-minus"></span>
                </button>
            </h2>
            <div id="flush-collapse<?= $key ?>" class="accordion-collapse collapse <?= $key == 0 ? 'show' : '' ?>" aria-labelledby="flush-heading<?= $key ?>" data-bs-parent="#accordion-<?= $module_id ?>">
                <?= do_shortcode('[_description description="' . _format_text($accordion_item['description']) . '" ]') ?>
            </div>
        </div>
    <?php } ?>
</div>