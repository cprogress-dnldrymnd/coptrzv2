jQuery(document).ready(function ($) {
    codemirror();
});

function codemirror() {
    setTimeout(function () {
        if (jQuery('textarea[name="carbon_fields_compact_input[_page_custom_css]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_page_custom_css]"'), cm_settings.ce_css);
            wp.data.subscribe(function () {
                // Obtain the CodeMirror instance
                var cm = jQuery('textarea[name="carbon_fields_compact_input[_page_custom_css]"').next('.CodeMirror').get(0).CodeMirror;
                cm.save(); // copy the content of the editor into the textarea.
            });
        }
        if (jQuery('textarea[name="carbon_fields_compact_input[_page_header_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_page_header_scripts]"'), cm_settings.ce_html);
            wp.data.subscribe(function () {
                // Obtain the CodeMirror instance
                var cm = jQuery('textarea[name="carbon_fields_compact_input[_page_header_scripts]"').next('.CodeMirror').get(0).CodeMirror;
                cm.save(); // copy the content of the editor into the textarea.
            });
        }

        if (jQuery('textarea[name="carbon_fields_compact_input[_page_footer_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_page_footer_scripts]"'), cm_settings.ce_html);
            wp.data.subscribe(function () {
                // Obtain the CodeMirror instance
                var cm = jQuery('textarea[name="carbon_fields_compact_input[_page_footer_scripts]"').next('.CodeMirror').get(0).CodeMirror;
                cm.save(); // copy the content of the editor into the textarea.
            });
        }

        if (jQuery('textarea[name="carbon_fields_compact_input[_page_body_scripts]"').length > 0) {
            wp.codeEditor.initialize(jQuery('textarea[name="carbon_fields_compact_input[_page_body_scripts]"'), cm_settings.ce_html);
            wp.data.subscribe(function () {
                // Obtain the CodeMirror instance
                var cm = jQuery('textarea[name="carbon_fields_compact_input[_page_body_scripts]"').next('.CodeMirror').get(0).CodeMirror;
                cm.save(); // copy the content of the editor into the textarea.
            });
        }

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