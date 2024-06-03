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

        /*
        // Check if TinyMCE is loaded
        if (typeof tinymce !== 'undefined') {

            // Select all textareas with the specified class
            var textareas = jQuery('.activate-tinymce textarea');

            // Loop through each textarea
            textareas.each(function (index) {
                var textareaId =  jQuery(this).attr('id'); 
                console.log(textareaId);
                // TinyMCE settings to mimic classic editor
                tinymce.init({
                    selector: '#' + textareaId,  // Use the unique ID
                    plugins: 'lists link charmap paste textcolor',
                    toolbar: 'formatselect | bold italic | bullist numlist | link | forecolor | charmap | pastetext | removeformat',
                    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
                    toolbar_location: 'top',
                    menubar: false,
                    statusbar: false,
                    branding: false
                });
            });

            // Save content on form submission (adjust if your form has a different ID)
            jQuery('form').submit(function (e) {
                textareas.each(function () {
                    var textareaId = jQuery(this).attr('id');
                    jQuery(this).val(tinymce.get(textareaId).getContent());
                });
            });
        } else {
            console.error('TinyMCE is not loaded.');
        }*/

        jQuery(document).on("focus", '.activate-tinymce textarea', function (event) {
            // Check if TinyMCE is loaded
            if (typeof tinymce !== 'undefined') {
                // Loop through each textarea
                var textareaId = jQuery(this).attr('id');
                console.log(textareaId);
                // TinyMCE settings to mimic classic editor
                tinymce.init({
                    selector: '#' + textareaId,  // Use the unique ID
                    plugins: 'lists link charmap paste textcolor',
                    toolbar: 'formatselect | bold italic | bullist numlist | link | forecolor | charmap | pastetext | removeformat',
                    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
                    toolbar_location: 'top',
                    menubar: false,
                    statusbar: false,
                    branding: false
                });

                // Save content on form submission (adjust if your form has a different ID)
                jQuery('form').submit(function (e) {
                    var textareaId = jQuery(this).attr('id');
                    jQuery(this).val(tinymce.get(textareaId).getContent());
                });
            } else {
                console.error('TinyMCE is not loaded.');
            }
        });


    }, 1000);
}