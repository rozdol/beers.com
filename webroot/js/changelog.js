var changelog = changelog || {};

(function($) {
    /**
     * Changelog Logic.
     * @param {object} options configuration options
     */
    function Changelog(options) {
        this.id = options.hasOwnProperty('id') ? options.id : null;
        this.previous = {};
        this.usernames = {};
    }

    /**
     * Initialize method.
     *
     * @return {void}
     */
    Changelog.prototype.init = function() {
        that = this;
        $($(that.id).attr('href')).on('show.bs.collapse', function() {
            that._trigger(this);
        });
    };

    /**
     * Triggers changelog functionality
     *
     * @param  {object} id changelog id
     * @return {void}
     */
    Changelog.prototype._trigger = function(id) {
        that = this;
        // ajax
        $.ajax({
            url: $(that.id).data('url'),
            method: 'get',
            data: {
                query: $(that.id).data('id')
            },
            success: function(data) {
            }
        });
    };

    changelog = new Changelog({id: '#changelogBtn'});

    changelog.init();

})(jQuery);
