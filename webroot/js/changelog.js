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
                data = that._groupByDateAndUser(data);
            }
        });
    };

    /**
     * Groups results by date and user
     *
     * @param  {object} data result set
     * @return {object}
     */
    Changelog.prototype._groupByDateAndUser = function(data) {
        that = this;

        result = {};
        $.each(data.changelog, function(k, v) {
            meta = JSON.parse(v.meta);
            if (!result.hasOwnProperty(meta.user)) {
                result[v.timestamp] = {};
            }
            result[v.timestamp][meta.user] = v;
        });

        return result;
    };

    changelog = new Changelog({id: '#changelogBtn'});

    changelog.init();

})(jQuery);
