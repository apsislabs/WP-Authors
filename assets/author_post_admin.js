(function($) {
    /*
     * Select/Upload image(s) event
     */

    // $('#post').validate();

    var imageUploader = wp.media({
        title: 'Insert image',
        library : {
            uploadedTo : wp.media.view.settings.post.id,
            type : 'image'
        },
        button: {
            text: 'Use this image' // button label text
        },
        multiple: false
    });

    $('body').on('click', '.upload_image', function(event) {
        event.preventDefault();
        imageUploader.on('select', handleImageSelect).open();
    });

    $('body').on('click', '#remove_avatar', function(event) {
        event.preventDefault();

        $('#avatar_preview').empty();
        $('#avatar').val("");
        $('#avatar_id').val("");
        $(this).hide();
    });

    function handleImageSelect()
    {
        var attachment = imageUploader.state().get('selection').first().toJSON();

        if ( attachment ) {
            $('#avatar_preview').empty().append($('<img>', {src: attachment.sizes.medium.url}));
            $('#avatar').val(attachment.url);
            $('#avatar_id').val(attachment.id);
            $('#remove_avatar').show();
        }
    }
})(jQuery);
