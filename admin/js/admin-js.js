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

        // Ensure TinyMCE is loaded (assuming you've enqueued it in WordPress)
        if (typeof tinymce !== 'undefined') {
            console.log('TinyMCE is  loaded.');

            jQuery('.activate-tinymce textarea').each(function (index, element) {
                var textarea = jQuery(this);
                $name = jQuery(this).attr('name');
                // Replace textarea with TinyMCE editor


                tinymce.init({
                    selector: 'textarea[name="' + $name + '"]',
                    plugins: 'lists link charmap paste textcolor',
                    toolbar: 'formatselect | bold italic | bullist numlist | link | forecolor | charmap | pastetext | removeformat',
                    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre', // Like classic editor
                    toolbar_location: 'top',
                    menubar: false,
                    statusbar: false,
                    branding: false // Hide TinyMCE logo
                });


                // Handle form submission (update textarea with TinyMCE content)
                textarea.closest('form').submit(function (e) {
                    // Update the textarea's value before submitting the form
                    textarea.val(tinymce.get('carbon_fields_compact_input[_modules][0][_description]').getContent());
                });
            });
            // Find the textarea element

        } else {
            console.log('TinyMCE is not loaded.');
        }


    }, 500);
}