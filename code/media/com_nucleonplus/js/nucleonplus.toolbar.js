/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

var Nucleonplus = Nucleonplus || {};

(function(document, window) {

/**
 * Edit Bar Configuration
 *
 * @example new Nucleonplus.ToolBar({isAuthentic: true, canAdd: true, canEdit: true, url: {home: 'joomla.box'}});
 * @extends Koowa.Class
 */
Nucleonplus.ToolBar = {

    authentic: null,

    url: {
        home: null,
        dashboard: null,
        login: null,
        logout: null,
    },

    init: function(options) {
        this.authentic     = options.isAuthentic;
        this.url.home      = options.url.homeUrl;
        this.url.dashboard = options.url.dashboardUrl;
        this.url.login     = options.url.loginUrl;
        this.url.logout    = options.url.logoutUrl;
    },

    isAuthentic: function(){
        return this.authentic;
    },

    getParameterByName: function(name, url) {
        if (!url) url = window.location.href;

        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);

        if (!results) return null;
        if (!results[2]) return '';

        return window.decodeURIComponent(results[2].replace(/\+/g, " "));
    }

};

})(document, window);