jQuery(document).ready(function ($) {
    codemirror();
});
function codemirror() {
    setTimeout(function () {
        if (typeof tinymce !== 'undefined') {
            // Loop through each textarea
            var textareaId = 'wysiwyg-editor-field';
            // TinyMCE settings to mimic classic editor
            tinymce.init({
                selector: '#' + textareaId,  // Use the unique ID
                plugins: 'lists link charmap paste textcolor',
                toolbar: 'formatselect | bold italic | bullist numlist | link | forecolor | charmap | pastetext | removeformat',
                block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
                toolbar_location: 'top',
                menubar: false,
                statusbar: false,
                branding: false,
                height: 500
            });



        } else {
            console.error('TinyMCE is not loaded.');
        }
        var $target;

        jQuery(document).on("click", '.wysiwyg-editor-trigger', function (event) {
            jQuery('#wysiwyg-editor').addClass('active');
            $target = jQuery(this).parent().parent().parent().prev().find('textarea');
            tinymce.get(textareaId).setContent($target.val());
        });

        jQuery(document).on("click", '.close-wysiwyg-trigger', function (event) {
            jQuery('#wysiwyg-editor').removeClass('active');
        });


        jQuery(document).on("click", '.submit-wysiwyg-trigger', function (event) {
            jQuery('#wysiwyg-editor').removeClass('active');
            $target.val(tinymce.get(textareaId).getContent());
        });




    }, 1000);

}