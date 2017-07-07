// Prevent multiple form submition.
jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit',function (e) {
        var $form = $(this);

        if ($form.data('submitted') === true) {
            // Previously submitted - don't submit again.
            e.preventDefault();
        } else {
            // Mark it so that the next submit can be ignored.
            $form.data('submitted', true);
        }
    });

    return this;
};
$(document).ready(function () {
    $('form').preventDoubleSubmission();
});


