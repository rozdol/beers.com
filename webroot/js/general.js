;(function ($) {
    'use strict';

    /**
     * Store and retrieve active tab, for all nav-tabs, using web browser's local storage.
     *
     * @link https://www.tutorialrepublic.com/faq/how-to-keep-the-current-tab-active-on-page-reload-in-bootstrap.php
     */

    var storage = new QoboStorage({
        engine: 'local'
    });

    var prefix = 'activeTab_';
    // store active tab for each navtab
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var navId = $(e.target).parents('.nav-tabs').attr('id');

        if (! navId) {
            return;
        }

        storage.write(prefix + navId, $(e.target).closest('li').index());
    });

    // load active tab for each navtab
    $('.nav-tabs').each(function (key, value) {
        var navId = $(value).attr('id');

        if (! navId) {
            return;
        }

        if (! storage.read(prefix + navId)) {
            return;
        }

        $('#' + navId + ' li:eq(' + storage.read(prefix + navId) + ') a').tab('show');
    });

})(jQuery);


/**
 * Prevent multiple form submition.
 */
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


