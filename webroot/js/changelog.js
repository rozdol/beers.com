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
                var tableBody = that._prepareTable(data);
                $(id).find('.body').html(tableBody);
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

    /**
     * Generates changelog html table
     *
     * @param  {object} data result set
     * @return {string}
     */
    Changelog.prototype._prepareTable = function(data) {
        that = this;
        result = '';
        $.each(data, function(date, record) {
            $.each(record, function(user, details) {
                result += '<table class="table table-condensed">';
                    result += that._prepareTableHead(date, user);
                    result += that._prepareTableBody(details);
                result += '</table>';
            });
        });

        return result;
    };

    /**
     * Generates html table head
     *
     * @param  {string} date audit log timestamp
     * @param  {string} user user id
     * @return {string}
     */
    Changelog.prototype._prepareTableHead = function(date, user) {
        ts = new Date(date);
        date = ts.getFullYear() + '/' + (ts.getMonth() + 1) + '/' + ts.getDate() + ' ';
        date += ts.getHours() + ':' + (ts.getMinutes() < 10 ? '0' : '') + ts.getMinutes();
        result = '<thead>';
            result += '<tr>';
                result += '<th colspan="3">Changed by ' + this._getUsername(user) + ' on ' + date + '</th>';
            result += '</tr>';
            result += '<tr>';
                result += '<th width="14%">Field</th>';
                result += '<th width="43%">Old Value</th>';
                result += '<th width="43%">New Value</th>';
            result += '</tr>';
        result += '</head>';

        return result;
    };

    /**
     * Generates html table body
     * @param  {object} details audit log details
     * @return {string}
     */
    Changelog.prototype._prepareTableBody = function(details) {
        result = '<tbody>';
            result += this._prepareTableRows(details);
        result += '</tbody>';

        return result;
    };

    /**
     * Generates html table body rows
     *
     * @param  {object} details audit log details
     * @return {string}
     */
    Changelog.prototype._prepareTableRows = function(details) {
        result = '';
        changed = JSON.parse(details.changed);
        original = JSON.parse(details.original);
        $.each(changed, function(k, v) {
            old = '';
            if (original !== null && original.hasOwnProperty(k)) {
                if (original[k] !== v) {
                    old = original[k];
                }
            }
            result += '<tr>';
                result += '<td>' + k + '</td>';
                result += '<td>' + old + '</td>';
                result += '<td>' + v + '</td>';
            result += '</tr>';
        });

        return result;
    };

    /**
     * Fetches and returns username, based on user id
     * @param  {string} id user id
     * @return {string}
     */
    Changelog.prototype._getUsername = function(id) {
        that = this;

        if (!that.usernames.hasOwnProperty(id)) {
            // ajax
            $.ajax({
                url: '/api/users/' + id + '.json',
                method: 'get',
                async: false,
                success: function(data) {
                    that.usernames[id] = data.data.username;
                }
            });
        }

        return that.usernames[id];
    };

    changelog = new Changelog({id: '#changelogBtn'});

    changelog.init();

})(jQuery);
