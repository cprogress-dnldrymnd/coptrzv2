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

        jQuery(document).ready(function($) {
            // Ensure TinyMCE is loaded (assuming you've enqueued it in WordPress)
            if (typeof tinymce !== 'undefined') {
                
                // Find the textarea element
                var textarea = $('textarea[name="custom_textarea"]');
                
                // Replace textarea with TinyMCE editor
                tinymce.init({
                    selector: 'textarea[name="custom_textarea"]',
                    // Additional TinyMCE settings (customize as needed)
                    plugins: 'lists link image table code',
                    toolbar: 'formatselect | bold italic | bullist numlist | link image | table | code'
                });
            
                // Handle form submission (update textarea with TinyMCE content)
                textarea.closest('form').submit(function(e) {
                    // Update the textarea's value before submitting the form
                    textarea.val(tinymce.get('custom_textarea').getContent());
                });
            } else {
                console.error('TinyMCE is not loaded.');
            }
        });


    }, 500);
}