jQuery(document).ready(function ($) {
    codemirror();
    button_selector();
});
function codemirror() {
  

    setTimeout(function () {
  

        if (jQuery('textarea[name="carbon_fields_compact_input[_header_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_header_scripts]"'), cm_settings.ce_html);
        }

        if (jQuery('textarea[name="carbon_fields_compact_input[_footer_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_footer_scripts]"'), cm_settings.ce_html);
        }



        if (jQuery('textarea[name="carbon_fields_compact_input[_body_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_body_scripts]"'), cm_settings.ce_html);
        }

    }, 500);

}

function button_selector() {
    jQuery(document).on("change", '.trigger-selector select', function (event) {
        $value = jQuery(this).val();
        $selector = jQuery(this).parent().parent().parent().find('.page-selector');
        active_link_type($selector, $value)
    });


    jQuery(document).on("change", '.select-page-selector', function (event) {
        $value = jQuery(this).val();
        $input = jQuery(this).parent().parent().parent().parent().parent().find('.field-url-cb input');
        $input.val($value);
    });


    function active_link_type($selector, $value, $input = '') {
        if ($value == 'page') {
            $selector.html(selector.page);
        } else if ($value == 'post') {
            $selector.html(selector.post);
        } else if ($value == 'product') {
            $selector.html(selector.product);
        } else if ($value == 'guides') {
            $selector.html(selector.guides);
        } else if ($value == 'casestudies') {
            $selector.html(selector.casestudies);
        } else if ($value == 'industries') {
            $selector.html(selector.industries);
        } else if ($value == 'popups') {
            $selector.html(selector.popups);
        } else {
            $selector.html('');
        }

        $selector.find('.select-page-selector').val($input);


    }

    setTimeout(function () {
        jQuery('.trigger-selector select').each(function (index, element) {
            $value = jQuery(this).val();
            $selector = jQuery(this).parent().parent().parent().find('.page-selector');
            $input = jQuery(this).parent().parent().parent().find('.field-url-cb input').val();
            active_link_type($selector, $value, $input)
        });
    }, 2000);
}