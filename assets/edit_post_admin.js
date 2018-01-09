(function($) {
    var $addAuthorButton = $('#add_author');
    var $authorSelect = $('#ap_authors');
    var $newAuthorToggle = $('#add_author_toggle');
    var $newAuthorContainer = $('#new_author');
    var $newAuthorInputs = $(':input', $newAuthorContainer);
    var $resetInputs = $('.clear-on-success :input', $newAuthorContainer);

    // Initial Widget Setup
    // ====================================================
    $newAuthorContainer.hide();
    $authorSelect.chosen({ width: "100%" });

    // Hide/Show Add Author Inputs
    // ====================================================
    $newAuthorToggle.on('click', function(event) {
        event.preventDefault();
        $newAuthorContainer.slideToggle();
    });

    // AJAX to handle new author
    // ====================================================
    $addAuthorButton.on('click', function(event) {
        event.preventDefault();

        var formData = $newAuthorInputs.serializeArray();

        formData.push({name: "action", value: "handleAddAuthorAjax"});

        $.post(ajaxurl, formData)
            .done(handleAuthorCreateSuccess)
            .fail(handleAuthorCreateError);
    });

    function handleAuthorCreateSuccess(res) {
        // Append Value
        $authorSelect.append($("<option>", {
            text: res.post_title,
            value: res.ID
        })).val(res.ID).trigger("chosen:updated");

        // Reset Inputs
        $resetInputs.val("");
    }

    function handleAuthorCreateError(res) {
        alert(objectL10n.ajaxError);
        console.error(res);
    }
})(jQuery);
